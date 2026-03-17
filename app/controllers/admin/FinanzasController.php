<?php
namespace Controllers\Admin;

use Exception;

class FinanzasController {

    public function __construct() {
        requireRole(1);
    }

    public function index() {
        requireAuth();
        global $pdo;

        // Insumos (usualmente son pocos, los cargamos una vez)
        $stmt_in = $pdo->query("SELECT * FROM insumos ORDER BY id_insumo DESC");
        $lista_insumos = $stmt_in->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/admin/finanzas.view.php';
    }

    // API: Obtener rango de fechas con datos
    public function getrangofinanzas() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        
        try {
            $stmt = $pdo->query("(SELECT DATE_FORMAT(MIN(fecha_gasto), '%Y-%m') as min_p, DATE_FORMAT(MAX(fecha_gasto), '%Y-%m') as max_p FROM gastos)
                                 UNION
                                 (SELECT DATE_FORMAT(MIN(fecha_cierre), '%Y-%m') as min_p, DATE_FORMAT(MAX(fecha_cierre), '%Y-%m') as max_p FROM ordenes WHERE estado='FINALIZADO')");
            $res = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Procesar para obtener el min y max global
            $dates = [];
            foreach($res as $r) {
                if($r['min_p']) $dates[] = $r['min_p'];
                if($r['max_p']) $dates[] = $r['max_p'];
            }
            
            if (empty($dates)) {
                echo json_encode(['success' => true, 'min' => date('Y-m'), 'max' => date('Y-m')]);
                return;
            }

            sort($dates);
            echo json_encode([
                'success' => true,
                'min' => $dates[0],
                'max' => $dates[count($dates)-1]
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
    }

    // API: Obtener resumen financiero
    public function getresumen() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        try {
            // Obtener mes y año de la solicitud o usar el actual
            $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

            $data = [];

            // Ingresos por órdenes finalizadas en el periodo seleccionado
            $stmt = $pdo->prepare("SELECT 
                COALESCE(SUM(CASE WHEN DATE(fecha_cierre) = CURDATE() THEN total_final ELSE 0 END), 0) as ingresos_hoy,
                COALESCE(SUM(total_final), 0) as ingresos_mes,
                COUNT(*) as ordenes_finalizadas
                FROM ordenes 
                WHERE estado = 'FINALIZADO' 
                AND MONTH(fecha_cierre) = ? AND YEAR(fecha_cierre) = ?");
            $stmt->execute([$month, $year]);
            $data['ingresos'] = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Gastos en el periodo seleccionado
            $stmt = $pdo->prepare("SELECT 
                COALESCE(SUM(CASE WHEN tipo_gasto = 'FIJO' THEN monto ELSE 0 END), 0) as gastos_fijos,
                COALESCE(SUM(CASE WHEN tipo_gasto = 'VARIABLE' THEN monto ELSE 0 END), 0) as gastos_variables,
                COALESCE(SUM(monto), 0) as gastos_total
                FROM gastos 
                WHERE MONTH(fecha_gasto) = ? AND YEAR(fecha_gasto) = ?");
            $stmt->execute([$month, $year]);
            $data['gastos'] = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Pagos a empleados en el periodo seleccionado
            $stmt = $pdo->prepare("SELECT COALESCE(SUM(monto), 0) as total_planilla 
                FROM pagos_empleados 
                WHERE estado = 'PAGADO' AND MONTH(fecha_programada) = ? AND YEAR(fecha_programada) = ?");
            $stmt->execute([$month, $year]);
            $planilla = $stmt->fetch();
            $data['planilla'] = $planilla['total_planilla'] ?? 0;

            // Punto de equilibrio proyectado: (Gastos Fijos + Planilla) / (1 - (Gastos Variables / Ingresos))
            $ingMes = $data['ingresos']['ingresos_mes'] > 0 ? $data['ingresos']['ingresos_mes'] : 1;
            $gFijos = ($data['gastos']['gastos_fijos'] ?? 0) + $data['planilla'];
            $gVariables = $data['gastos']['gastos_variables'] ?? 0;
            
            $margenContribucion = 1 - ($gVariables / $ingMes);
            // Si el margen es negativo o ridículo, el punto de equilibrio es al menos cubrir todos los gastos actuales
            if ($margenContribucion <= 0.1) {
                $data['punto_equilibrio'] = round($gFijos + $gVariables, 2);
            } else {
                $data['punto_equilibrio'] = round($gFijos / $margenContribucion, 2);
            }

            // Ingresos últimos 6 meses (para tendencia)
            $stmt = $pdo->query("SELECT DATE_FORMAT(fecha_cierre, '%Y-%m') as mes, SUM(total_final) as total 
                FROM ordenes WHERE estado = 'FINALIZADO' AND fecha_cierre >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes ORDER BY mes");
            $data['ingresos_meses'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Gastos últimos 6 meses (para tendencia)
            $stmt = $pdo->query("SELECT DATE_FORMAT(fecha_gasto, '%Y-%m') as mes, tipo_gasto as tipo, SUM(monto) as total 
                FROM gastos WHERE fecha_gasto >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                GROUP BY mes, tipo_gasto ORDER BY mes");
            $data['gastos_meses'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Ingresos por método de pago en el periodo seleccionado
            $stmt = $pdo->prepare("SELECT p.metodo_pago, COUNT(*) as cantidad, SUM(p.monto) as total 
                FROM pagos_orden p
                JOIN ordenes o ON p.id_orden = o.id_orden
                WHERE MONTH(o.fecha_cierre) = ? AND YEAR(o.fecha_cierre) = ?
                AND o.estado = 'FINALIZADO'
                GROUP BY p.metodo_pago");
            $stmt->execute([$month, $year]);
            $data['metodos_pago'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function registrargasto() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        try {
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo_gasto = $_POST['tipo'] ?? 'VARIABLE';
            $monto = floatval($_POST['monto'] ?? 0);
            $fecha_gasto = $_POST['fecha'] ?? date('Y-m-d');
            $id_insumo_origen = !empty($_POST['id_insumo']) ? intval($_POST['id_insumo']) : null;
            $id_usuario = $_SESSION['user']['id'];

            if (empty($descripcion) || $monto <= 0) {
                echo json_encode(['success' => false, 'message' => 'Completa los campos correctamente.']);
                return;
            }

            if (!empty($_POST['id_gasto'])) {
                $stmt = $pdo->prepare("UPDATE gastos SET descripcion=?, tipo_gasto=?, monto=?, fecha_gasto=?, id_insumo_origen=? WHERE id_gasto=?");
                $success = $stmt->execute([$descripcion, $tipo_gasto, $monto, $fecha_gasto, $id_insumo_origen, $_POST['id_gasto']]);
                echo json_encode(['success' => $success, 'message' => $success ? 'Gasto actualizado.' : 'Error al actualizar.']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO gastos (descripcion, tipo_gasto, monto, fecha_gasto, id_insumo_origen, id_usuario_registrador) VALUES (?, ?, ?, ?, ?, ?)");
                $success = $stmt->execute([$descripcion, $tipo_gasto, $monto, $fecha_gasto, $id_insumo_origen, $id_usuario]);
                echo json_encode(['success' => $success, 'message' => $success ? 'Gasto registrado correctamente.' : 'No se pudo guardar.']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Hubo un error del servidor.']);
        }
    }

    public function registrarinsumo() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
            return;
        }

        try {
            $nombre = trim($_POST['nombre'] ?? '');
            $unidad_medida = trim($_POST['unidad_medida'] ?? 'Unidad');
            $costo_unitario = floatval($_POST['costo_unitario'] ?? 0);
            $stock_actual = intval($_POST['stock_actual'] ?? 0);

            if (empty($nombre) || $costo_unitario < 0) {
                echo json_encode(['success' => false, 'message' => 'Completa el nombre y un costo válido.']);
                return;
            }

            if (!empty($_POST['id_insumo'])) {
                $stmt = $pdo->prepare("UPDATE insumos SET nombre=?, unidad_medida=?, costo_unitario=?, stock_actual=? WHERE id_insumo=?");
                $success = $stmt->execute([$nombre, $unidad_medida, $costo_unitario, $stock_actual, $_POST['id_insumo']]);
                echo json_encode(['success' => $success, 'message' => $success ? 'Insumo actualizado.' : 'Error al actualizar.']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO insumos (nombre, unidad_medida, costo_unitario, stock_actual) VALUES (?, ?, ?, ?)");
                $success = $stmt->execute([$nombre, $unidad_medida, $costo_unitario, $stock_actual]);
                echo json_encode(['success' => $success, 'message' => $success ? 'Insumo registrado.' : 'Error al guardar.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error, verifica los datos.']);
        }
    }

    // API: Obtener lista de gastos por mes (para tabla fluida)
    public function getgastosperiodo() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        try {
            $stmt = $pdo->prepare("SELECT g.*, u.nombres as registrador 
                                 FROM gastos g 
                                 LEFT JOIN usuarios u ON g.id_usuario_registrador = u.id_usuario 
                                 WHERE MONTH(g.fecha_gasto) = ? AND YEAR(g.fecha_gasto) = ?
                                 ORDER BY g.fecha_gasto DESC, g.id_gasto DESC");
            $stmt->execute([$month, $year]);
            $gastos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $gastos]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // API: Obtener un gasto específico
    public function obtenergasto() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM gastos WHERE id_gasto = ?");
        $stmt->execute([$id]);
        $gasto = $stmt->fetch(\PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$gasto, 'data' => $gasto]);
    }

    // API: Eliminar gasto
    public function eliminargasto() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'] ?? 0;
        try {
            $stmt = $pdo->prepare("DELETE FROM gastos WHERE id_gasto = ?");
            $success = $stmt->execute([$id]);
            echo json_encode(['success' => $success, 'message' => $success ? 'Gasto eliminado.' : 'No se pudo eliminar.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
        }
    }

    public function exportargastos() {
        requireAuth();
        global $pdo;

        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        $stmt = $pdo->prepare("SELECT fecha_gasto, descripcion, tipo_gasto, monto FROM gastos WHERE MONTH(fecha_gasto) = ? AND YEAR(fecha_gasto) = ? ORDER BY fecha_gasto ASC");
        $stmt->execute([$month, $year]);
        $gastos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=gastos_'.$year.'_'.$month.'.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, [mb_convert_encoding('FECHA', 'UTF-16LE', 'UTF-8'), mb_convert_encoding('DESCRIPCION', 'UTF-16LE', 'UTF-8'), mb_convert_encoding('TIPO', 'UTF-16LE', 'UTF-8'), mb_convert_encoding('MONTO (S/)', 'UTF-16LE', 'UTF-8')]);
        
        // Fix for Excel CSV encoding
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, ['FECHA', 'DESCRIPCION', 'TIPO', 'MONTO (S/)']);

        $total = 0;
        foreach ($gastos as $g) {
            fputcsv($output, [$g['fecha_gasto'], $g['descripcion'], $g['tipo_gasto'], number_format($g['monto'], 2)]);
            $total += $g['monto'];
        }
        fputcsv($output, ['', '', 'TOTAL', number_format($total, 2)]);
        fclose($output);
        exit;
    }

    // API: Obtener un insumo específico
    public function obtenerinsumo() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM insumos WHERE id_insumo = ?");
        $stmt->execute([$id]);
        $insumo = $stmt->fetch(\PDO::FETCH_ASSOC);
        echo json_encode(['success' => !!$insumo, 'data' => $insumo]);
    }

    // API: Eliminar insumo
    public function eliminarinsumo() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $id = $_POST['id'] ?? 0;
        try {
            // Verificar si el insumo está siendo usado en gastos (opcional, pero buena práctica)
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM gastos WHERE id_insumo_origen = ?");
            $stmt_check->execute([$id]);
            if ($stmt_check->fetchColumn() > 0) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar: el insumo tiene gastos asociados.']);
                return;
            }

            $stmt = $pdo->prepare("DELETE FROM insumos WHERE id_insumo = ?");
            $success = $stmt->execute([$id]);
            echo json_encode(['success' => $success, 'message' => $success ? 'Insumo eliminado.' : 'No se pudo eliminar.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
        }
    }
}

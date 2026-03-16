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

        // Historial de gastos para la tabla principal
        $stmt = $pdo->query("SELECT g.*, u.nombres as registrador 
                             FROM gastos g 
                             LEFT JOIN usuarios u ON g.id_usuario_registrador = u.id_usuario 
                             ORDER BY g.fecha_gasto DESC, g.id_gasto DESC");
        $historial_gastos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Insumos
        $stmt_in = $pdo->query("SELECT * FROM insumos ORDER BY id_insumo DESC");
        $lista_insumos = $stmt_in->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/admin/finanzas.view.php';
    }

    // API: Obtener resumen financiero
    public function getresumen() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        try {
            $data = [];

            // Ingresos por órdenes finalizadas
            $stmt = $pdo->query("SELECT 
                COALESCE(SUM(CASE WHEN DATE(fecha_cierre) = CURDATE() THEN total_final ELSE 0 END), 0) as ingresos_hoy,
                COALESCE(SUM(CASE WHEN YEARWEEK(fecha_cierre) = YEARWEEK(CURDATE()) THEN total_final ELSE 0 END), 0) as ingresos_semana,
                COALESCE(SUM(CASE WHEN MONTH(fecha_cierre) = MONTH(CURDATE()) AND YEAR(fecha_cierre) = YEAR(CURDATE()) THEN total_final ELSE 0 END), 0) as ingresos_mes,
                COALESCE(SUM(total_final), 0) as ingresos_total,
                COUNT(*) as ordenes_finalizadas
                FROM ordenes WHERE estado = 'FINALIZADO'");
            $data['ingresos'] = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Gastos
            $stmt = $pdo->query("SELECT 
                COALESCE(SUM(CASE WHEN tipo_gasto = 'FIJO' THEN monto ELSE 0 END), 0) as gastos_fijos,
                COALESCE(SUM(CASE WHEN tipo_gasto = 'VARIABLE' THEN monto ELSE 0 END), 0) as gastos_variables,
                COALESCE(SUM(monto), 0) as gastos_total
                FROM gastos 
                WHERE MONTH(fecha_gasto) = MONTH(CURDATE()) AND YEAR(fecha_gasto) = YEAR(CURDATE())");
            $data['gastos'] = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Pagos a empleados este mes
            $stmt = $pdo->query("SELECT COALESCE(SUM(monto), 0) as total_planilla 
                FROM pagos_empleados 
                WHERE estado = 'PAGADO' AND MONTH(fecha_programada) = MONTH(CURDATE()) AND YEAR(fecha_programada) = YEAR(CURDATE())");
            $planilla = $stmt->fetch();
            $data['planilla'] = $planilla['total_planilla'] ?? 0;

            // Punto de equilibrio: Gastos Fijos / (1 - (Gastos Variables / Ingresos))
            $ingMes = $data['ingresos']['ingresos_mes'] > 0 ? $data['ingresos']['ingresos_mes'] : 1;
            $gFijos = $data['gastos']['gastos_fijos'] ?? 0;
            $gVariables = $data['gastos']['gastos_variables'] ?? 0;
            $margenContribucion = 1 - ($gVariables / $ingMes);
            $data['punto_equilibrio'] = $margenContribucion > 0 ? round($gFijos / $margenContribucion, 2) : 0;

            // Ingresos últimos 6 meses
            $stmt = $pdo->query("SELECT DATE_FORMAT(fecha_cierre, '%Y-%m') as mes, SUM(total_final) as total 
                FROM ordenes WHERE estado = 'FINALIZADO' AND fecha_cierre >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY mes ORDER BY mes");
            $data['ingresos_meses'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Gastos últimos 6 meses
            $stmt = $pdo->query("SELECT DATE_FORMAT(fecha_gasto, '%Y-%m') as mes, tipo_gasto as tipo, SUM(monto) as total 
                FROM gastos WHERE fecha_gasto >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
                GROUP BY mes, tipo_gasto ORDER BY mes");
            $data['gastos_meses'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Ingresos por método de pago
            $stmt = $pdo->query("SELECT p.metodo_pago, COUNT(*) as cantidad, SUM(p.monto) as total 
                FROM pagos_orden p
                JOIN ordenes o ON p.id_orden = o.id_orden
                WHERE MONTH(o.fecha_cierre) = MONTH(CURDATE()) AND YEAR(o.fecha_cierre) = YEAR(CURDATE()) 
                AND o.estado = 'FINALIZADO'
                GROUP BY p.metodo_pago");
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

            $stmt = $pdo->prepare("INSERT INTO gastos (descripcion, tipo_gasto, monto, fecha_gasto, id_insumo_origen, id_usuario_registrador) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$descripcion, $tipo_gasto, $monto, $fecha_gasto, $id_insumo_origen, $id_usuario])) {
                echo json_encode(['success' => true, 'message' => 'Gasto registrado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar el registro.']);
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
}

<?php
namespace Controllers\Admin;

use Orden;
use PDO;

class OrdenController {

    public function __construct() {
        requireRole(1); // Admin
    }

    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/orden.view.php';
        include VIEW_PATH . '/partials/orden/modal_reporte.php';
    }

    public function getlista() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        try {
            $modelo = new Orden($pdo);
            $ordenes = $modelo->getAll();
            
            // Format labels for easy frontend use
            $data = array_map(function($o) {
                $cliente = trim($o['cliente_nombres'] . ' ' . $o['cliente_apellidos']);
                $vehiculo = $o['placa'] ? $o['placa'] . ' (' . $o['categoria_vehiculo'] . ')' : 'Sin Vehículo';
                
                return [
                    'id_orden' => $o['id_orden'],
                    'fecha' => date('d/m/Y h:i A', strtotime($o['fecha_creacion'])),
                    'fecha_raw' => $o['fecha_creacion'],
                    'cliente' => $cliente ?: 'Consumidor Final',
                    'vehiculo' => $vehiculo,
                    'estado' => $o['estado'],
                    'total_final' => (float)$o['total_final'],
                    'creador' => $o['creador_nombre']
                ];
            }, $ordenes);

            echo json_encode(['data' => $data]);
        } catch (\Exception $e) {
            echo json_encode(['data' => []]);
        }
    }

    public function getgrafico() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        try {
            $result = [];
            for ($i = 5; $i >= 0; $i--) {
                $mesNum = date('m', strtotime("-$i months"));
                $anio   = date('Y', strtotime("-$i months"));
                
                $mesesStr = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                $mesNombre = $mesesStr[(int)$mesNum - 1] . ' ' . substr($anio, 2);

                // Obtener Ingresos (Órdenes finalizadas)
                $stmt = $pdo->prepare(
                    "SELECT SUM(total_final) as total FROM ordenes 
                     WHERE estado = 'FINALIZADO' AND MONTH(fecha_cierre) = :mes AND YEAR(fecha_cierre) = :anio"
                );
                $stmt->execute([':mes' => $mesNum, ':anio' => $anio]);
                $ingresos = $stmt->fetch()['total'] ?? 0;

                // Obtener Gastos (Para calcular "Ganancias")
                $stmtG = $pdo->prepare(
                    "SELECT SUM(monto) as total FROM gastos 
                     WHERE MONTH(fecha) = :mes AND YEAR(fecha) = :anio"
                );
                $stmtG->execute([':mes' => $mesNum, ':anio' => $anio]);
                $gastos = $stmtG->fetch()['total'] ?? 0;

                // Obtener Planilla
                $stmtP = $pdo->prepare(
                    "SELECT SUM(monto) as total FROM pagos_empleados 
                     WHERE MONTH(fecha_programada) = :mes AND YEAR(fecha_programada) = :anio"
                );
                $stmtP->execute([':mes' => $mesNum, ':anio' => $anio]);
                $planilla = $stmtP->fetch()['total'] ?? 0;

                $ganancia = $ingresos - $gastos - $planilla;

                $result['categorias'][] = $mesNombre;
                $result['ingresos'][] = (float)$ingresos;
                $result['ganancias'][] = (float)$ganancia;
                
                // Totales globales (todo el tiempo, no solo 6 meses) 
                // para las cards, lo podemos calcular también
            }

            // Totales historicos
            $stmtTot = $pdo->query("SELECT SUM(total_final) as tot FROM ordenes WHERE estado='FINALIZADO'");
            $totalIngresosHist = $stmtTot->fetch()['tot'] ?? 0;
            
            $stmtG = $pdo->query("SELECT SUM(monto) as tot FROM gastos");
            $totalG = $stmtG->fetch()['tot'] ?? 0;
            
            $stmtP = $pdo->query("SELECT SUM(monto) as tot FROM pagos_empleados");
            $totalP = $stmtP->fetch()['tot'] ?? 0;
            
            $totalGananciasHist = $totalIngresosHist - $totalG - $totalP;

            echo json_encode([
                'success' => true,
                'grafico' => $result,
                'totales' => [
                    'ingresos' => (float)$totalIngresosHist,
                    'ganancias' => (float)$totalGananciasHist
                ]
            ]);

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } // <-- Llave faltante que cierra getgrafico

    // API: Obtener detalle completo de una orden
    public function getdetalle() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        if (empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID de orden no proporcionado']);
            return;
        }

        try {
            $modelo = new Orden($pdo);
            $orden = $modelo->getById($_GET['id']);
            
            if (!$orden) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Orden no encontrada']);
                return;
            }

            $detalles = $modelo->getDetalles($_GET['id']);
            
            echo json_encode([
                'success' => true,
                'orden' => $orden,
                'detalles' => $detalles
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Generar Reporte PDF/CSV con filtros BI
     */
    public function exportar() {
        requireAuth();
        global $pdo;

        // Sincronizar con la hora local (UTC-5)
        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'consolidado';
        $formato = $_GET['formato'] ?? 'pdf';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['f_fin'] ?? date('Y-m-d');
        $idUsuario = $_GET['usuario'] ?? 'TODOS';
        $estado = $_GET['estado'] ?? 'TODOS';

        // Títulos profesionales diferenciados
        $titulos_base = [
            'consolidado' => "REPORTE CONSOLIDADO DE SOCIOS",
            'detallado'   => "ANÁLISIS DETALLADO DE ÍTEMS",
            'pagos'       => "CONCILIACIÓN DE PAGOS"
        ];
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE ÓRDENES";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // Etiquetas para el reporte
        $usuario_label = "TODOS";
        if ($idUsuario !== 'TODOS') {
            $st = $pdo->prepare("SELECT nombres FROM usuarios WHERE id_usuario = ?");
            $st->execute([$idUsuario]);
            $usuario_label = $st->fetchColumn() ?: "N/A";
        }
        $estado_label = ($estado === 'TODOS') ? "TODOS LOS ESTADOS" : $estado;

        // Cargar Datos según el tipo
        $data = [];
        require_once BASE_PATH . '/app/models/Orden.php';
        $ordenModel = new \Orden($pdo);

        if ($tipo === 'consolidado') {
            $query = "SELECT o.*, 
                    CONCAT(c.nombres, ' ', COALESCE(c.apellidos, '')) as cliente,
                    CONCAT(v.placa, ' (', cv.nombre, ')') as vehiculo,
                    cr.nombres as creador,
                    (SELECT GROUP_CONCAT(CONCAT(s.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') 
                     FROM detalle_orden d 
                     INNER JOIN servicios s ON d.id_servicio = s.id_servicio 
                     WHERE d.id_orden = o.id_orden) as servicios_resumen
                    FROM ordenes o 
                    LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
                    LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                    LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id_categoria
                    LEFT JOIN usuarios cr ON o.id_usuario_creador = cr.id_usuario
                    WHERE DATE(o.fecha_creacion) BETWEEN ? AND ?";
            
            $params = [$f_inicio, $f_fin];
            if ($idUsuario !== 'TODOS') { $query .= " AND o.id_usuario_creador = ?"; $params[] = $idUsuario; }
            if ($estado !== 'TODOS') { $query .= " AND o.estado = ?"; $params[] = $estado; }
            $query .= " ORDER BY o.id_orden DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($tipo === 'detallado') {
            $query = "SELECT do.*, o.fecha_creacion as fecha_registro,
                      COALESCE(s.nombre, p.nombre) as item_nombre,
                      CASE 
                        WHEN do.id_servicio IS NOT NULL AND do.id_servicio > 0 THEN 'SERVICIO' 
                        ELSE 'PRODUCTO' 
                      END as tipo_item
                      FROM detalle_orden do
                      JOIN ordenes o ON do.id_orden = o.id_orden
                      LEFT JOIN servicios s ON do.id_servicio = s.id_servicio
                      LEFT JOIN productos p ON do.id_producto = p.id_producto
                      WHERE DATE(o.fecha_creacion) BETWEEN ? AND ?";
            
            $params = [$f_inicio, $f_fin];
            if ($idUsuario !== 'TODOS') { $query .= " AND o.id_usuario_creador = ?"; $params[] = $idUsuario; }
            if ($estado !== 'TODOS') { $query .= " AND o.estado = ?"; $params[] = $estado; }
            $query .= " ORDER BY o.id_orden DESC, tipo_item ASC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } elseif ($tipo === 'pagos') {
            $query = "SELECT po.*, o.estado as orden_estado, o.fecha_creacion as fecha_movimiento
                      FROM pagos_orden po
                      JOIN ordenes o ON po.id_orden = o.id_orden
                      WHERE DATE(o.fecha_creacion) BETWEEN ? AND ?";
            
            $params = [$f_inicio, $f_fin];
            if ($idUsuario !== 'TODOS') { $query .= " AND o.id_usuario_creador = ?"; $params[] = $idUsuario; }
            $query .= " AND o.estado = 'FINALIZADO' ORDER BY o.id_orden DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // --- EXPORTACIÓN ---
        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Ordenes_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM para Excel
            
            if($tipo === 'consolidado') {
                fputcsv($output, ['ID ORDEN', 'FECHA', 'CLIENTE', 'VEHICULO', 'SERVICIOS', 'ESTADO', 'TOTAL (S/)'], ';');
                foreach($ordenes as $o) fputcsv($output, [$o['id_orden'], $o['fecha_creacion'], $o['cliente'], $o['vehiculo'], $o['servicios_resumen'], $o['estado'], $o['total_final']], ';');
            } elseif($tipo === 'detallado') {
                fputcsv($output, ['ORDEN', 'FECHA', 'TIPO', 'NOMBRE ITEM', 'CANTIDAD', 'P. UNIT', 'TOTAL'], ';');
                foreach($detalles as $d) fputcsv($output, [$d['id_orden'], $d['fecha_registro'], $d['tipo_item'], $d['item_nombre'], $d['cantidad'], $d['precio_unitario'], $d['subtotal']], ';');
            } elseif($tipo === 'pagos') {
                fputcsv($output, ['ORDEN', 'FECHA PAGO', 'METODO', 'MONTO (S/)'], ';');
                foreach($pagos as $p) fputcsv($output, [$p['id_orden'], $p['fecha_movimiento'], $p['metodo_pago'], $p['monto']], ';');
            }
            fclose($output);
            exit;
        } else {
            // PDF con mPDF
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                
                // Si es Consolidado, usamos orientación Horizontal (A4-L)
                $orientacion = ($tipo === 'consolidado') ? 'A4-L' : 'A4';

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => $orientacion,
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                    'margin_left' => 10,
                    'margin_right' => 10
                ]);

                // Capturar el HTML de la vista
                ob_start();
                include VIEW_PATH . "/admin/reportes/orden/{$tipo}.view.php";
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte); // Título profesional en pestaña
                $mpdf->Output('Reporte_Ordenes_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (Exception $e) {
                die("Error al generar PDF: " . $e->getMessage());
            }
        }
    }
}

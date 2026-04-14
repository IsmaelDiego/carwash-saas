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
    }

    // API: Obtener lista de órdenes
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

    // API: Obtener datos para gráfico (Ingresos de últimos 6 meses)
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
}

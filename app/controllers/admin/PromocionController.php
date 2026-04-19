<?php
namespace Controllers\Admin;

// 1. CARGA MANUAL DEL MODELO (Para asegurar que lo encuentre sí o sí)
// Ajusta la ruta si tu carpeta models está en otro nivel, pero esto suele ser estándar:
require_once __DIR__ . '/../../models/Promocion.php'; 

// 2. CARGA MANUAL DEL SERVICIO WHATSAPP
require_once __DIR__ . '/../WhapiService.php'; 

// Usamos las clases globales (ya que las cargamos manualmente arriba)
use Promocion; 
use Services\WhapiService;

class PromocionController {

    public function __construct() {
        requireRole(1);
    }

    public function index() {
        requireAuth();
        global $pdo;
        
        $model = new Promocion($pdo);
        $recientes = $model->getRecientes();
        $activas = array_filter($recientes, function($p) { return $p['estado'] == 1; });

        require VIEW_PATH . '/admin/lista_promociones.view.php';
    }

    // =======================================================
    // API PARA LA TABLA
    // =======================================================
    public function getall() {
        requireAuth();
        global $pdo;
        // Limpiamos cualquier salida previa para evitar JSON inválido
        ob_clean(); 
        header('Content-Type: application/json');
        
        try {
            $model = new Promocion($pdo);
            $data = $model->getAll();
            echo json_encode(['data' => $data]);
        } catch (\Exception $e) {
            // Si falla, enviamos array vacío para que la tabla no se rompa
            echo json_encode(['data' => []]); 
        }
    }

    // =======================================================
    // API DASHBOARD (Cards)
    // =======================================================
    public function getdashboarddata() {
        requireAuth();
        global $pdo;
        ob_clean();
        header('Content-Type: application/json');
        
        $model = new Promocion($pdo);
        $recientes = $model->getRecientes();
        $activas = array_filter($recientes, function($p) { return $p['estado'] == 1; });
        
        echo json_encode([
            'todas'     => $model->getAll(),
            'recientes' => array_values($recientes),
            'activas'   => array_values($activas)
        ]);
    }

    // =======================================================
    // CRUD
    // =======================================================
    public function registrarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Promocion($pdo);
            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Campaña creada exitosamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
        }
    }

    public function editarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Promocion($pdo);
            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Promoción actualizada.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
            }
        }
    }

    public function eliminarpromocion() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Promocion($pdo);
            if ($model->eliminar($input['id_promocion'])) {
                echo json_encode(['success' => true, 'message' => 'Promoción eliminada.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar.']);
            }
        }
    }

    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Promocion($pdo);
            $estado = $input['estado'] == 1 ? 1 : 0;
            
            if ($model->cambiarEstado($input['id_promocion'], $estado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        }
    }

    // =======================================================
    // WHATSAPP
    // =======================================================
    public function enviarwhatsapp() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            set_time_limit(300);

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['id_promocion']) || empty($input['mensaje'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos.']); return;
            }

            $model = new Promocion($pdo);
            $clientes = $model->getClientesWhatsApp();

            if (empty($clientes)) {
                echo json_encode(['success' => false, 'message' => 'No hay clientes activos.']); return;
            }

            $whapi = new WhapiService();
            $enviados = 0;
            $errores = 0;

            foreach ($clientes as $c) {
                $nombre = explode(' ', trim($c['nombres']))[0];
                $mensaje = str_replace('{{nombre}}', $nombre, $input['mensaje']);
                $res = $whapi->enviarMensaje($c['telefono'], $mensaje);
                if ($res['success']) $enviados++; else $errores++;
                usleep(200000);
            }

            $tipoRespuesta = ($errores > 0) ? 'warning' : 'success';
            echo json_encode([
                'success' => true,
                'type'    => $tipoRespuesta,
                'message' => "Reporte de Envío: ✅ $enviados Enviados | ⚠️ $errores Fallidos"
            ]);
        }
    }

    // =======================================================
    // EXPORTAR REPORTES BI
    // =======================================================
    public function exportar()
    {
        requireAuth();
        global $pdo;
        
        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['f_fin'] ?? date('Y-m-d');

        $titulos_base = [
            'general'     => "LISTADO MAESTRO DE CAMPAÑAS",
            'rendimiento' => "ANÁLISIS DE EFECTIVIDAD Y CANJES",
            'impacto'     => "REPORTE DE IMPACTO FINANCIERO"
        ];
        
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE PROMOCIONES";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        if ($tipo === 'general') {
            $query = "SELECT * FROM promociones ORDER BY id_promocion DESC";
            $stmt = $pdo->query($query);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } elseif ($tipo === 'rendimiento') {
            // EFECTIVIDAD: Resumen por Campaña
            $query = "SELECT p.nombre, p.valor, p.tipo_descuento, COUNT(h.id_historial) as total_usos
                      FROM promociones p
                      LEFT JOIN historial_uso_promociones h ON p.id_promocion = h.id_promocion
                      WHERE h.fecha_uso IS NULL OR DATE(h.fecha_uso) BETWEEN ? AND ?
                      GROUP BY p.id_promocion
                      ORDER BY total_usos DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$f_inicio, $f_fin]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            // FINANCIERO / AUDITORÍA: Detalle Cliente por Cliente
            $query = "SELECT p.nombre, p.valor, p.tipo_descuento, h.fecha_uso, c.nombres, c.apellidos
                      FROM historial_uso_promociones h
                      JOIN promociones p ON h.id_promocion = p.id_promocion
                      JOIN clientes c ON h.id_cliente = c.id_cliente
                      WHERE DATE(h.fecha_uso) BETWEEN ? AND ?
                      ORDER BY h.fecha_uso DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$f_inicio, $f_fin]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Promociones_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            if ($tipo === 'general') {
                fputcsv($output, ['CAMPAÑA', 'VALOR', 'TIPO', 'VIGENCIA', 'ESTADO'], ';');
                foreach ($data as $r) {
                    fputcsv($output, [
                        mb_strtoupper($r['nombre'], 'UTF-8'), 
                        $r['valor'], 
                        $r['tipo_descuento'], 
                        $r['fecha_inicio'].' al '.$r['fecha_fin'],
                        $r['estado'] ? 'ACTIVA' : 'INACTIVA'
                    ], ';');
                }
            } else {
                fputcsv($output, ['CAMPAÑA', 'VALOR DESC.', 'TIPO', 'TOTAL CANJES'], ';');
                foreach ($data as $r) {
                    fputcsv($output, [
                        mb_strtoupper($r['nombre'], 'UTF-8'), 
                        $r['valor'],
                        $r['tipo_descuento'],
                        $r['total_usos']
                    ], ';');
                }
            }
            exit;
        } else {
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15]);
                ob_start();
                $lista = $data;
                require VIEW_PATH . '/admin/reportes/promocion/listado.view.php';
                $html = ob_get_clean();
                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Promociones_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) {
                die("Error al generar PDF: " . $e->getMessage());
            }
        }
    }
}
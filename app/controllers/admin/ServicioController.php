<?php
namespace Controllers\Admin;

use Servicio;

class ServicioController {

    public function __construct() {
        requireRole(1); // Solo Admin
    }

    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/lista_servicios.view.php';
    }

    // API: GET ALL
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Servicio($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // API: REGISTRAR
    public function registrarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['nombre']) || empty($input['precio_base'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']); return;
            }

            $model = new Servicio($pdo);
            
            // Validar nombre único
            if ($model->existeNombre($input['nombre'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre ya existe.']); return;
            }

            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Servicio creado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
        }
    }

    // API: EDITAR (Recibe id_servicio)
    public function editarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_servicio'])) {
                echo json_encode(['success' => false, 'message' => 'ID no identificado.']); return;
            }

            $model = new Servicio($pdo);
            // Validar nombre único excluyendo el actual
            if ($model->existeNombre($input['nombre'], $input['id_servicio'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre ya existe.']); return;
            }

            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Servicio actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Sin cambios o error.']);
            }
        }
    }

    // API: ELIMINAR (Recibe id_servicio)
    public function eliminarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Servicio($pdo);
            if ($model->eliminar($input['id_servicio'])) {
                echo json_encode(['success' => true, 'message' => 'Servicio eliminado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar.']);
            }
        }
    }

    // API: CAMBIAR ESTADO
    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Servicio($pdo);
            // Validar booleano
            $nuevoEstado = $input['estado'] == 1 ? 1 : 0;
            
            if ($model->cambiarEstado($input['id_servicio'], $nuevoEstado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        }
    }

    // EXPORTAR REPORTES BI
    public function exportar()
    {
        requireAuth();
        global $pdo;
        
        // Sincronizar con la hora local (UTC-5)
        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'tarifario';
        $formato = $_GET['formato'] ?? 'pdf';
        $f_inicio = $_GET['f_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['f_fin'] ?? date('Y-m-d');

        // Títulos profesionales diferenciados (Patrón Clientes)
        $titulos_base = [
            'tarifario' => "TARIFARIO MAESTRO DE SERVICIOS",
            'rendimiento' => "ANÁLISIS DE RENDIMIENTO Y PRODUCTIVIDAD",
            'fidelidad' => "CONFIGURACIÓN ESTRATÉGICA DE PUNTOS"
        ];
        
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE SERVICIOS";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        require_once BASE_PATH . '/app/models/Servicio.php';
        $model = new \Servicio($pdo);

        if ($tipo === 'tarifario' || $tipo === 'fidelidad') {
            $data = $model->getAll();
        } else {
            // Lógica Rendimiento (Ranking)
            $query = "SELECT s.nombre, COUNT(do.id_detalle) as total_usos, SUM(do.subtotal) as total_recaudado
                      FROM servicios s
                      JOIN detalle_orden do ON s.id_servicio = do.id_servicio
                      JOIN ordenes o ON do.id_orden = o.id_orden
                      WHERE DATE(o.fecha_creacion) BETWEEN ? AND ?
                      AND o.estado != 'CANCELADO'
                      GROUP BY s.id_servicio
                      ORDER BY total_usos DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$f_inicio, $f_fin]);
            $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Servicios_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            if ($tipo === 'tarifario') {
                fputcsv($output, ['NOMBRE DEL SERVICIO', 'PRECIO BASE (S/)', 'ESTADO'], ';');
                foreach ($data as $r) {
                    fputcsv($output, [
                        mb_strtoupper($r['nombre'], 'UTF-8'), 
                        number_format($r['precio_base'], 2, '.', ''), 
                        $r['estado'] ? 'ACTIVO' : 'INACTIVO'
                    ], ';');
                }
            } elseif ($tipo === 'fidelidad') {
                fputcsv($output, ['NOMBRE DEL SERVICIO', 'ACUMULA PUNTOS', 'PERMITE CANJE'], ';');
                foreach ($data as $r) {
                    fputcsv($output, [
                        mb_strtoupper($r['nombre'], 'UTF-8'), 
                        $r['acumula_puntos'] == 1 ? 'SI' : 'NO', 
                        $r['permite_canje'] == 1 ? 'SI' : 'NO'
                    ], ';');
                }
            } else {
                fputcsv($output, ['NOMBRE DEL SERVICIO', 'FRECUENCIA DE USO', 'RECAUDACIÓN TOTAL (S/)'], ';');
                foreach ($data as $r) {
                    fputcsv($output, [
                        mb_strtoupper($r['nombre'], 'UTF-8'), 
                        $r['total_usos'], 
                        number_format($r['total_recaudado'], 2, '.', '')
                    ], ';');
                }
            }
            exit;
        } else {
            // PDF con mPDF (Configuración Clientes)
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8', 'format' => 'A4',
                    'margin_top' => 15, 'margin_bottom' => 15
                ]);
                
                ob_start();
                $lista = $data;
                require VIEW_PATH . '/admin/reportes/servicio/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Servicios_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) {
                die("Error al generar PDF: " . $e->getMessage());
            }
        }
    }
}
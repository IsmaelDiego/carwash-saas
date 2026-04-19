<?php
namespace Controllers\Admin;

use Temporada;

class TemporadaController {

    public function __construct() {
        // En V3.2, id_rol 1 = Admin.
        requireRole(1);
    }

    public function index() {
        requireAuth();
        global $pdo;
        
        $model = new Temporada($pdo);
        $dash = $model->getDashboardData();

        // Variables para la vista
        $tActual = $dash['actual'];
        $tAnt    = $dash['anterior'];
        $sAct    = $dash['stats_act'];
        $sAnt    = $dash['stats_ant'];

        // Cálculo simple de variación para las flechitas
        $calcVar = function($act, $ant) {
            if($ant == 0) return $act > 0 ? 100 : 0;
            return round((($act - $ant) / $ant) * 100, 1);
        };
        $varGen = $calcVar($sAct['gen'], $sAnt['gen']);
        $varRed = $calcVar($sAct['red'], $sAnt['red']);

        require VIEW_PATH . '/admin/lista_temporadas.view.php';
    }

    // API GET ALL
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Temporada($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // REGISTRAR
    public function registrartemporada() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $model = new Temporada($pdo);

            // Validar si ya hay activa
            if ($model->hayTemporadaActiva()) {
                echo json_encode(['success' => false, 'message' => '¡Ya existe una temporada activa! Ciérrala antes de crear otra.']);
                return;
            }

            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Temporada iniciada correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar en BD.']);
            }
        }
    }

    // EDITAR
    public function editartemporada() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Temporada($pdo);
            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Datos actualizados.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar.']);
            }
        }
    }

    // =========================================================
    // ESTA ES LA FUNCIÓN QUE FALTABA Y EL JS ESTABA BUSCANDO
    // =========================================================
    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            
            // Decodificar JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Temporada($pdo);
            
            // Si el estado es 0, usamos la lógica de cerrar (pone fecha fin hoy)
            if (isset($input['estado']) && $input['estado'] == 0) {
                if ($model->cerrarTemporada($input['id_temporada'])) {
                    echo json_encode(['success' => true, 'message' => 'Temporada finalizada correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se pudo cerrar la temporada.']);
                }
            } else {
                // Si fuera otro cambio de estado genérico
                if ($model->cambiarEstado($input['id_temporada'], $input['estado'])) {
                    echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
                }
            }
        }
    }
    // =========================================================
    // EXPORTAR REPORTES BI
    // =========================================================
    public function exportar()
    {
        requireAuth();
        global $pdo;

        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $periodo = $_GET['periodo'] ?? 'all';
        $estado = $_GET['estado'] ?? 'todos';

        // Lógica de traducción de Periodo Inteligente
        $f_fin = date('Y-m-d');
        $f_inicio = '1900-01-01'; // Default: Todo el historial

        if ($periodo === '3m') {
            $f_inicio = date('Y-m-d', strtotime('-3 months'));
        } elseif ($periodo === '6m') {
            $f_inicio = date('Y-m-d', strtotime('-6 months'));
        } elseif (strpos($periodo, 'year_') === 0) {
            $anioSelect = str_replace('year_', '', $periodo);
            $f_inicio = "$anioSelect-01-01";
            $f_fin = "$anioSelect-12-31";
        }

        $titulos_base = [
            'general'     => "LISTADO MAESTRO DE TEMPORADAS",
            'rendimiento' => "MÉTRICAS DE FIDELIZACIÓN POR PERIODO",
            'impacto'     => "REPORTE SITUACIONAL DE TEMPORADAS"
        ];
        
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE TEMPORADAS";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // Lógica de Filtro Dinámico por Estado
        $where_estado = "";
        $params = [$f_inicio, $f_fin];

        if ($estado !== 'todos') {
            $where_estado = " AND t.estado = ? ";
            $params[] = $estado;
        }

        // Consulta Quirúrgica con Filtros de Fecha y Estado
        $sql = "SELECT t.*, 
                (SELECT COALESCE(SUM(d.cantidad),0) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio INNER JOIN ordenes o ON d.id_orden = o.id_orden WHERE o.id_temporada = t.id_temporada AND o.estado = 'FINALIZADO' AND s.acumula_puntos = 1 AND o.id_cliente != 1) as puntos_gen,
                (SELECT COALESCE(COUNT(o.id_orden),0) FROM ordenes o WHERE o.id_temporada = t.id_temporada AND o.estado = 'FINALIZADO' AND o.descuento_puntos > 0 AND o.id_cliente != 1) as puntos_red
                FROM temporadas t 
                WHERE t.fecha_inicio BETWEEN ? AND ? $where_estado
                ORDER BY t.fecha_inicio DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($formato === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Temporadas_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($output, ['TEMPORADA', 'INICIO', 'FIN', 'PUNTOS GEN.', 'REDENCIONES', 'ESTADO'], ';');
            foreach ($data as $r) {
                fputcsv($output, [
                    mb_strtoupper($r['nombre'], 'UTF-8'),
                    $r['fecha_inicio'],
                    $r['fecha_fin'] ?? '---',
                    $r['puntos_gen'],
                    $r['puntos_red'],
                    $r['estado'] ? 'ACTIVA' : 'FINALIZADA'
                ], ';');
            }
            exit;
        } else {
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15]);
                
                ob_start();
                $lista = $data;
                require VIEW_PATH . '/admin/reportes/temporada/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Temporadas_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) {
                die("Error BI Temporadas: " . $e->getMessage());
            }
        }
    }
}
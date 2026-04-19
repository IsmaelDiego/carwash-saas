<?php
namespace Controllers\Admin;

use PermisoEmpleado;
use Empleado;
use PDO;

class PermisoController{
    private $permisoModel;
    private $empleadoModel;

    public function __construct() {
        requireAuth();
        requireRole(1); // Solo admin
        
        require_once APP_PATH . '/models/PermisoEmpleado.php';
        require_once APP_PATH . '/models/Empleado.php';
        global $pdo;
        $this->permisoModel = new \PermisoEmpleado($pdo);
        $this->empleadoModel = new \Empleado($pdo);
    }

    public function index() {
        $empleados = $this->empleadoModel->getAll();
        require VIEW_PATH . '/admin/rrhh_permisos.view.php';
    }

    public function getall() {
        header('Content-Type: application/json');
        $permisos = $this->permisoModel->getAll(null); // Traer todos para que Datatables filtre localmente
        echo json_encode(['data' => $permisos]);
    }

    public function registrar() {
        header('Content-Type: application/json');
        
        $data = [
            'id_usuario' => $_POST['id_usuario'],
            'tipo' => $_POST['tipo'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'motivo' => $_POST['motivo'],
            'estado' => $_POST['estado'],
            'id_admin_registrador' => $_SESSION['user']['id']
        ];

        if ($this->permisoModel->registrar($data)) {
            echo json_encode(['success' => true, 'message' => 'Permiso registrado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el permiso']);
        }
    }

    public function cambiarestado() {
        header('Content-Type: application/json');
        
        $id_permiso = $_POST['id_permiso'];
        $estado = $_POST['estado'];
        $id_admin_registrador = $_SESSION['user']['id'];

        if ($this->permisoModel->cambiarEstado($id_permiso, $estado, $id_admin_registrador)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    public function getstats() {
        header('Content-Type: application/json');
        $stats = $this->permisoModel->getEstadisticas();
        echo json_encode($stats);
        exit;
    }

    // ==========================================
    // API: CENTRAL DE REPORTES BI
    // ==========================================
    public function exportar()
    {
        requireAuth();
        global $pdo;

        date_default_timezone_set('America/Lima');
        $fecha_actual = date('d/m/Y');
        $tipo_rep = $_GET['tipo_rep'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $id_usuario = $_GET['id_usuario'] ?? 'todos';
        $estado_filtro = $_GET['estado'] ?? 'todos';

        $titulos_base = [
            'general'    => "BITÁCORA GENERAL DE PERMISOS Y AUSENCIAS",
            'consolidado' => "CONSOLIDADO DE AUSENTISMO POR EMPLEADO",
            'analisis'   => "ANÁLISIS DE MOTIVOS Y JUSTIFICACIONES"
        ];
        $titulo_pdf = $titulos_base[$tipo_rep] ?? "REPORTE DE PERMISOS";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // Construcción de Query
        $where = " WHERE 1=1 ";
        $params = [];

        if ($id_usuario !== 'todos') {
            $where .= " AND p.id_usuario = ? ";
            $params[] = $id_usuario;
        }
        if ($estado_filtro !== 'todos') {
            $where .= " AND p.estado = ? ";
            $params[] = $estado_filtro;
        }

        $sql = "SELECT p.*, u.nombres as empleado, a.nombres as admin_registrador
                FROM permisos_empleados p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                LEFT JOIN usuarios a ON p.id_admin_registrador = a.id_usuario
                $where
                ORDER BY p.fecha_inicio DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($formato === 'pdf') {
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15]);
                
                ob_start();
                require VIEW_PATH . '/admin/reportes/permiso/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Permisos_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) { die("Error PDF: " . $e->getMessage()); }
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Permisos_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            if ($tipo_rep === 'consolidado') {
                fputcsv($output, ['Empleado', 'Cantidad Permisos', 'Dias Totales Ausencia'], ';');
                $conso = [];
                foreach ($lista as $r) {
                    $emp = $r['empleado'];
                    if (!isset($conso[$emp])) $conso[$emp] = ['cant' => 0, 'dias' => 0];
                    $d1 = new \DateTime($r['fecha_inicio']);
                    $d2 = new \DateTime($r['fecha_fin']);
                    $diff = $d1->diff($d2)->days + 1;
                    $conso[$emp]['cant']++;
                    $conso[$emp]['dias'] += $diff;
                }
                foreach ($conso as $nom => $v) {
                    fputcsv($output, [mb_strtoupper($nom, 'UTF-8'), $v['cant'], $v['dias']], ';');
                }
            } elseif ($tipo_rep === 'analisis') {
                // EXCEL DE ANÁLISIS: FOCO EN MOTIVOS
                fputcsv($output, ['Empleado', 'Tipo Permiso', 'Fecha Inicio', 'Fecha Fin', 'Dias', 'Estado', 'JUSTIFICACION / MOTIVO'], ';');
                foreach ($lista as $r) {
                    $d1 = new \DateTime($r['fecha_inicio']);
                    $d2 = new \DateTime($r['fecha_fin']);
                    $dias = $d1->diff($d2)->days + 1;
                    fputcsv($output, [
                        mb_strtoupper($r['empleado'], 'UTF-8'), $r['tipo'], $r['fecha_inicio'], 
                        $r['fecha_fin'], $dias, $r['estado'], $r['motivo']
                    ], ';');
                }
            } else {
                // BITÁCORA GENERAL
                fputcsv($output, ['ID', 'Empleado', 'Tipo', 'F. Inicio', 'F. Fin', 'Dias', 'Estado', 'Registrado Por'], ';');
                foreach ($lista as $r) {
                    $d1 = new \DateTime($r['fecha_inicio']);
                    $d2 = new \DateTime($r['fecha_fin']);
                    $dias = $d1->diff($d2)->days + 1;
                    fputcsv($output, [
                        $r['id_permiso'], mb_strtoupper($r['empleado'], 'UTF-8'), $r['tipo'], 
                        $r['fecha_inicio'], $r['fecha_fin'], $dias, $r['estado'], $r['admin_registrador']
                    ], ';');
                }
            }
            fclose($output);
            exit;
        }
    }
}

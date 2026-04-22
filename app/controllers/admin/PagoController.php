<?php
namespace Controllers\Admin;

use PagoEmpleado;
use Empleado;

class PagoController {
    private $pagoModel;
    private $empleadoModel;

    public function __construct() {
        requireAuth();
        requireRole(1); // Solo admin
        
        require_once APP_PATH . '/models/PagoEmpleado.php';
        require_once APP_PATH . '/models/Empleado.php';
        global $pdo;
        $this->pagoModel = new \PagoEmpleado($pdo);
        $this->empleadoModel = new \Empleado($pdo);
    }

    public function index() {
        $empleados = $this->empleadoModel->getAll();
        require VIEW_PATH . '/admin/rrhh_pagos.view.php';
    }

    public function getall() {
        header('Content-Type: application/json');
        $pagos = $this->pagoModel->getAll();
        echo json_encode(['data' => $pagos]);
    }

    public function registrar() {
        header('Content-Type: application/json');
        
        $data = [
            'id_usuario' => $_POST['id_usuario'],
            'tipo' => $_POST['tipo'],
            'monto' => $_POST['monto'],
            'periodo' => $_POST['periodo'],
            'estado' => $_POST['estado'],
            'fecha_programada' => $_POST['fecha_programada'],
            'observaciones' => $_POST['observaciones'],
            'id_admin_registrador' => $_SESSION['user']['id'],
            'fecha_pago' => date('Y-m-d H:i:s')
        ];

        if ($this->pagoModel->registrar($data)) {
            echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago']);
        }
    }

    public function cambiarestado() {
        header('Content-Type: application/json');
        
        $id_pago = $_POST['id_pago'];
        $estado = $_POST['estado'];

        if ($this->pagoModel->cambiarEstado($id_pago, $estado)) {
            markSystemChange();
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    public function getstats() {
        header('Content-Type: application/json');
        $stats = $this->pagoModel->getEstadisticas();
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
        $tipo_rep = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $id_usuario = $_GET['id_usuario'] ?? 'todos';
        $periodo_sel = $_GET['periodo'] ?? 'all';

        // Lógica de Periodo Inteligente
        $f_fin = date('Y-m-d');
        $f_inicio = '1900-01-01';
        if ($periodo_sel === '3m') $f_inicio = date('Y-m-d', strtotime('-3 months'));
        elseif ($periodo_sel === '6m') $f_inicio = date('Y-m-d', strtotime('-6 months'));
        elseif (strpos($periodo_sel, 'year_') === 0) {
            $anio = str_replace('year_', '', $periodo_sel);
            $f_inicio = "$anio-01-01";
            $f_fin = "$anio-12-31";
        }

        $titulos_base = [
            'general'    => "BITÁCORA GENERAL DE PAGOS A PERSONAL",
            'pendientes' => "REPORTE DE DEUDAS Y PAGOS PENDIENTES",
            'consolidado' => "CONSOLIDADO FINANCIERO DE PLANILLA"
        ];
        $titulo_pdf = $titulos_base[$tipo_rep] ?? "REPORTE DE PAGOS";
        $titulo_reporte = "$titulo_pdf ($fecha_actual)";

        // Construcción de Query
        $where = " WHERE p.fecha_creacion BETWEEN ? AND ? ";
        $params = [$f_inicio . ' 00:00:00', $f_fin . ' 23:59:59'];

        if ($id_usuario !== 'todos') {
            $where .= " AND p.id_usuario = ? ";
            $params[] = $id_usuario;
        }
        if ($tipo_rep === 'pendientes') {
            $where .= " AND p.estado != 'PAGADO' ";
        }

        $sql = "SELECT p.*, u.nombres as empleado, u.dni as empleado_dni, a.nombres as admin_registrador
                FROM pagos_empleados p
                JOIN usuarios u ON p.id_usuario = u.id_usuario
                JOIN usuarios a ON p.id_admin_registrador = a.id_usuario
                $where
                ORDER BY p.fecha_creacion DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $lista = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($formato === 'pdf') {
            try {
                require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
                $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15]);
                
                ob_start();
                require VIEW_PATH . '/admin/reportes/pago/listado.view.php';
                $html = ob_get_clean();

                $mpdf->WriteHTML($html);
                $mpdf->SetTitle($titulo_reporte);
                $mpdf->Output('Reporte_Pagos_' . date('Ymd_His') . '.pdf', 'I');
                exit;
            } catch (\Exception $e) { die("Error PDF: " . $e->getMessage()); }
        } else {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=Reporte_Pagos_' . date('Ymd_His') . '.csv');
            $output = fopen('php://output', 'w');
            fputs($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            if ($tipo_rep === 'consolidado') {
                // EXCEL CONSOLIDADO: AGRUPADO POR EMPLEADO
                fputcsv($output, ['Empleado', 'Total Pagado (S/)', 'Total Pendiente (S/)', 'Monto Total (S/)'], ';');
                
                $consolidado = [];
                foreach ($lista as $r) {
                    $emp = $r['empleado'];
                    if (!isset($consolidado[$emp])) $consolidado[$emp] = ['pagado' => 0, 'pendiente' => 0];
                    if ($r['estado'] === 'PAGADO') $consolidado[$emp]['pagado'] += $r['monto'];
                    else $consolidado[$emp]['pendiente'] += $r['monto'];
                }

                foreach ($consolidado as $nombre => $d) {
                    fputcsv($output, [
                        mb_strtoupper($nombre, 'UTF-8'),
                        number_format($d['pagado'], 2, '.', ''),
                        number_format($d['pendiente'], 2, '.', ''),
                        number_format($d['pagado'] + $d['pendiente'], 2, '.', '')
                    ], ';');
                }
            } else {
                // EXCEL DETALLADO: BITÁCORA / PENDIENTES
                fputcsv($output, ['ID Pago', 'Empleado', 'Tipo', 'Monto (S/)', 'Periodo', 'F. Programada', 'F. Pago', 'Estado', 'Registrado Por'], ';');
                foreach ($lista as $r) {
                    fputcsv($output, [
                        $r['id_pago'], mb_strtoupper($r['empleado'], 'UTF-8'), $r['tipo'], $r['monto'], 
                        $r['periodo'], $r['fecha_programada'], $r['fecha_pago'] ?: '---',
                        $r['estado'], $r['admin_registrador']
                    ], ';');
                }
            }
            fclose($output);
            exit;
        }
    }
}

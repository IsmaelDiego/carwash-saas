<?php

namespace Controllers\Admin;

use Exception;
use PDO;

class CajaController
{

    public function __construct()
    {
        requireRole(1); // Solo admin
    }

    public function index()
    {
        requireAuth();
        require VIEW_PATH . '/admin/caja.view.php';
    }

    /**
     * API: Listar arqueos (sesiones de caja)
     */
    public function getarqueos()
    {
        requireAuth();
        require_once BASE_PATH . '/app/models/CajaSesion.php';
        header('Content-Type: application/json');

        try {
            $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

            $cajaModel = new \CajaSesion();
            $res = $cajaModel->getArqueosPorMes($month, $year);

            echo json_encode(['success' => true, 'data' => $res]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Ver detalle de una sesión (desglose por métodos de pago)
     */
    public function detallesesion()
    {
        requireAuth();
        require_once BASE_PATH . '/app/models/CajaSesion.php';
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
            return;
        }

        try {
            $cajaModel = new \CajaSesion();
            
            // 1. Datos básicos
            $sesion = $cajaModel->getSesionInfo($id);
            if (!$sesion) throw new Exception("Sesión no encontrada");

            // 2. Desglose por método de pago
            $metodos = $cajaModel->getResumenCaja($id);

            // 3. Resumen de productos vendidos en esta sesión
            $productos = $cajaModel->getProductosVendidos($id);

            echo json_encode([
                'success' => true,
                'sesion' => $sesion,
                'metodos' => $metodos,
                'productos' => $productos
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Exportar arqueos a CSV
     */
    public function exportararqueos()
    {
        requireAuth();
        require_once BASE_PATH . '/app/models/CajaSesion.php';

        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

        $cajaModel = new \CajaSesion();
        $arqueos = $cajaModel->getArqueosPorMes($month, $year);

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $nombreMes = $meses[$month - 1];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=arqueos_'.$nombreMes.'_'.$year.'.csv');
        $output = fopen('php://output', 'w');

        // BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Cabecera plana en CSV separada por punto y coma (;)
        fputcsv($output, ['ID MES', 'CAJERO', 'FECHA APERTURA', 'FECHA CIERRE', 'MONTO INICIAL (S/)', 'MONTO ESPERADO (S/)', 'RECAUDADO REAL (S/)', 'DIFERENCIA (S/)', 'ESTADO'], ';');

        $correlativo = 1;
        foreach ($arqueos as $cs) {
            $montoEsperado = floatval($cs['monto_esperado']);
            if ($cs['estado'] === 'ABIERTA') {
                $montoEsperado = floatval($cs['monto_apertura']) + floatval($cs['recaudado_acumulado']);
            }
            $montoReal = $cs['monto_cierre_real'] !== null ? number_format((float)$cs['monto_cierre_real'], 2, '.', '') : '-';
            $diff = $cs['diferencia'] !== null ? number_format((float)$cs['diferencia'], 2, '.', '') : '-';

            fputcsv($output, [
                $correlativo,
                $cs['cajero_nombre'],
                $cs['fecha_apertura'],
                $cs['fecha_cierre'] ?: '-',
                number_format((float)$cs['monto_apertura'], 2, '.', ''),
                number_format($montoEsperado, 2, '.', ''),
                $montoReal,
                $diff,
                $cs['estado']
            ], ';');
            
            $correlativo++;
        }

        fclose($output);
        exit;
    }
}

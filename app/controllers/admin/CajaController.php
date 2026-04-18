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
        global $pdo;

        // Obtener solicitudes pendientes
        $solicitudes = $pdo->query("SELECT s.*, u.nombres 
            FROM solicitudes_caja s 
            JOIN usuarios u ON s.id_usuario = u.id_usuario 
            WHERE s.estado = 'PENDIENTE' AND DATE(s.fecha_solicitud) = CURDATE()
            ORDER BY s.fecha_solicitud DESC")->fetchAll(\PDO::FETCH_ASSOC);

        // 1. Obtener empleados con su estado de caja (para saber quién ya tiene una abierta)
        $empleados = $pdo->query("SELECT u.id_usuario, u.nombres, u.id_rol, 
                (SELECT id_sesion FROM caja_sesiones WHERE id_usuario = u.id_usuario AND estado = 'ABIERTA' LIMIT 1) as id_sesion_abierta
            FROM usuarios u 
            WHERE u.estado = 1 AND u.id_rol IN (2, 3)
            ORDER BY u.nombres ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Obtener configuración global (para el modo libre y operador responsable)
        require_once APP_PATH . '/models/ConfiguracionSistema.php';
        $configModel = new \ConfiguracionSistema($pdo);
        $globalConfig = $configModel->get();

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
        global $pdo;
        
        // Sincronizar con la hora local de Lima
        date_default_timezone_set('America/Lima');
        $fecha_reporte = date('d/m/Y');

        $tipo = $_GET['tipo'] ?? 'general';
        $formato = $_GET['formato'] ?? 'pdf';
        $idUsuario = $_GET['id_usuario'] ?? 'TODOS';
        $rango = $_GET['rango'] ?? 'MES_ACTUAL';
        $estado = $_GET['estado'] ?? 'TODOS';

        // Títulos profesionales diferenciados
        $titulos_base = [
            'general'   => "RESUMEN CONSOLIDADO DE ARQUEOS",
            'detallado' => "DETALLE DE OPERACIONES Y VENTAS",
            'pagos'     => "ANÁLISIS ESTRATÉGICO DE PAGOS"
        ];
        
        $titulo_pdf = $titulos_base[$tipo] ?? "REPORTE DE CAJA";
        $titulo_pestaña = "$titulo_pdf ($fecha_reporte)";
        $titulo_reporte = $titulo_pestaña; // Para compatibilidad
        
        $f_inicio = $_GET['fecha_inicio'] ?? date('Y-m-d');
        $f_fin = $_GET['fecha_fin'] ?? date('Y-m-d');

        require_once BASE_PATH . '/app/models/CajaSesion.php';
        $cajaModel = new \CajaSesion();

        if ($rango === 'HOY') {
            $f_inicio = date('Y-m-d'); $f_fin = date('Y-m-d');
        } elseif ($rango === 'MES_ACTUAL') {
            $f_inicio = date('Y-m-01'); $f_fin = date('Y-m-t');
        }

        // --- OBTENER DATOS SEGÚN TIPO ---
        $data_reporte = [];
        $view_to_load = '/admin/reportes/caja/consolidado.view.php';

        if ($tipo === 'general') {
            $data_reporte = $cajaModel->getArqueosPorRango($f_inicio, $f_fin);
            $view_to_load = '/admin/reportes/caja/consolidado.view.php';
        } elseif ($tipo === 'detallado') {
            $data_reporte = $cajaModel->getOperacionesPorRango($f_inicio, $f_fin, $idUsuario);
            $view_to_load = '/admin/reportes/caja/servicios.view.php';
        } elseif ($tipo === 'pagos') {
            $view_to_load = '/admin/reportes/caja/pagos.view.php';
            $queryPagos = "SELECT po.*, o.id_caja_sesion, o.fecha_cierre as fecha_pago 
                           FROM pagos_orden po 
                           JOIN ordenes o ON po.id_orden = o.id_orden 
                           JOIN caja_sesiones cs ON o.id_caja_sesion = cs.id_sesion 
                           WHERE DATE(cs.fecha_apertura) BETWEEN ? AND ? 
                           AND o.estado = 'FINALIZADO'";
            if ($idUsuario !== 'TODOS') $queryPagos .= " AND cs.id_usuario = " . (int)$idUsuario;
            $stmt = $pdo->prepare($queryPagos);
            $stmt->execute([$f_inicio, $f_fin]);
            $data_reporte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // --- GENERACIÓN PDF ---
        if ($formato === 'pdf') {
            require_once BASE_PATH . '/vendor/MPDF/vendor/autoload.php';
            $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_top' => 10, 'margin_bottom' => 12]);
            $mpdf->SetTitle($titulo_reporte);
            
            ob_start();
            $lista = $data_reporte;            // Alias para las vistas
            $arqueos = $data_reporte;          // Alias para consolidado
            $operaciones = $data_reporte;      // Alias para detallado
            $pagos = $data_reporte;            // Alias para pagos
            $fecha_generacion = $fecha_reporte;
            $titulo = $titulo_reporte;         // Título dinámico
            
            require VIEW_PATH . $view_to_load;
            $html = ob_get_clean();

            $mpdf->WriteHTML($html);
            $mpdf->Output('Reporte_Caja_' . date('Ymd_His') . '.pdf', 'I');
            exit;
        }

        // --- GENERACIÓN CSV ---
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Reporte_Caja_' . strtoupper($tipo) . '_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        if ($tipo === 'general') {
            fputcsv($output, [
                'ID SESION', 'CAJERO', 'ROL APERTURA', 'ESTADO',
                'FECHA APERTURA', 'FECHA CIERRE', 'MONTO INICIAL (S/)', 
                'VENTAS (S/)', 'MONTO ESPERADO (S/)', 'RECAUDADO REAL (S/)', 
                'DIFERENCIA (S/)', 'EFECTIVO (S/)', 'TRANSFERENCIA (S/)',
                'TARJETA (S/)', 'TOTAL ATENCIONES'
            ], ';');

            foreach ($data_reporte as $cs) {
                if ($idUsuario !== 'TODOS' && $cs['id_usuario'] != $idUsuario) continue;
                if ($estado !== 'TODOS' && $cs['estado'] !== $estado) continue;

                $montoEsperado = floatval($cs['monto_apertura']) + floatval($cs['recaudado_acumulado']);
                $montoReal = $cs['monto_cierre_real'] !== null ? number_format((float)$cs['monto_cierre_real'], 2, '.', '') : '-';
                $diff = $cs['diferencia'] !== null ? number_format((float)$cs['diferencia'], 2, '.', '') : '-';

                fputcsv($output, [
                    $cs['id_sesion'], $cs['cajero_nombre'], $cs['rol_apertura_nombre'] ?: 'N/A', $cs['estado'],
                    $cs['fecha_apertura'], $cs['fecha_cierre'] ?: 'ABIERTA',
                    number_format((float)$cs['monto_apertura'], 2, '.', ''),
                    number_format((float)$cs['recaudado_acumulado'], 2, '.', ''),
                    number_format($montoEsperado, 2, '.', ''), $montoReal, $diff,
                    number_format((float)$cs['total_efectivo'], 2, '.', ''),
                    number_format((float)$cs['total_transferencia'], 2, '.', ''),
                    number_format((float)$cs['total_tarjeta'], 2, '.', ''),
                    $cs['total_atenciones']
                ], ';');
            }
        } elseif ($tipo === 'detallado') {
            fputcsv($output, ['ID ORDEN', 'SESION', 'FECHA', 'RESPONSABLE', 'TIPO ITEM', 'NOMBRE ITEM', 'CANTIDAD', 'P. UNITARIO', 'TOTAL (S/)'], ';');
            $data = $cajaModel->getOperacionesPorRango($f_inicio, $f_fin, $idUsuario);
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['id_orden'], $row['id_caja_sesion'], $row['fecha_registro'],
                    $row['cajero_nombre'] ?: 'N/A', $row['tipo_item'], $row['item_nombre'],
                    $row['cantidad'], number_format($row['precio_unitario'], 2, '.', ''),
                    number_format($row['total'], 2, '.', '')
                ], ';');
            }
        } elseif ($tipo === 'pagos') {
            fputcsv($output, ['ID ORDEN', 'SESION', 'FECHA PAGO', 'METODO PAGO', 'MONTO (S/)', 'ESTADO ORDEN'], ';');
            // Consulta directa de pagos en el rango
            $queryPagos = "SELECT po.*, o.id_caja_sesion, o.fecha_creacion as fecha_pago, o.estado as orden_estado 
                           FROM pagos_orden po 
                           JOIN ordenes o ON po.id_orden = o.id_orden 
                           JOIN caja_sesiones cs ON o.id_caja_sesion = cs.id_sesion
                           WHERE DATE(cs.fecha_apertura) BETWEEN ? AND ?
                           AND o.estado = 'FINALIZADO'";
            if ($idUsuario !== 'TODOS') $queryPagos .= " AND cs.id_usuario = " . (int)$idUsuario;
            $queryPagos .= " ORDER BY o.id_orden DESC";
            
            $stmt = $pdo->prepare($queryPagos);
            $stmt->execute([$f_inicio, $f_fin]);
            $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pagos as $p) {
                fputcsv($output, [
                    $p['id_orden'], $p['id_caja_sesion'], $p['fecha_pago'],
                    $p['metodo_pago'], number_format($p['monto'], 2, '.', ''),
                    $p['orden_estado']
                ], ';');
            }
        }
        fclose($output);
        exit;
    }

    /**
     * API: Aprobar solicitud de caja
     */
    public function aprobar_solicitud()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $idReq = $input['id_solicitud'] ?? 0;
        $idCajero = $input['id_cajero'] ?? 0;
        $montoApertura = $input['monto_apertura'] ?? 0;

        if (!$idCajero) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos de la solicitud.']);
            return;
        }

        try {
            $pdo->beginTransaction();

            // Verificar que el cajero no tenga una abierta
            require_once BASE_PATH . '/app/models/CajaSesion.php';
            $cajaModel = new \CajaSesion($pdo);
            if ($cajaModel->getCajaAbierta($idCajero)) {
                throw new \Exception("Este empleado ya tiene una caja abierta.");
            }

            // Marcar la solicitud como aprobada
            if ($idReq) {
                $pdo->prepare("UPDATE solicitudes_caja SET estado = 'APROBADA', fecha_respuesta = NOW() WHERE id_solicitud = ?")->execute([$idReq]);
            }

            // Registrar que la apertura fue realizada por el Administrador (Rol de la sesión actual: 1)
            $idRolApertura = $_SESSION['user']['role'];

            // Abrir caja para ese cajero automagicamente ("El admin la abre por él")
            $pdo->prepare("INSERT INTO caja_sesiones (id_usuario, fecha_apertura, monto_apertura, monto_esperado, estado, id_rol_apertura) VALUES (?, NOW(), ?, ?, 'ABIERTA', ?)")
                ->execute([$idCajero, $montoApertura, $montoApertura, $idRolApertura]);

            $pdo->commit();
            
            // Forzar recarga de notificaciones en la sesión del admin
            if (isset($_SESSION['_notif_cache'])) {
                unset($_SESSION['_notif_cache']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Caja aperturada remotamente con éxito.']);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Sincronizar Modo Operación Libre desde el modal de Arqueo
     */
    public function sync_modo_libre()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $isActive = (int)($input['modo_sin_cajero'] ?? 0);

        try {
            $pdo->prepare("UPDATE configuracion SET modo_sin_cajero = ? WHERE id_config = 1")->execute([$isActive]);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Obtener solicitudes pendientes en tiempo real
     */
    public function getpendingsolicitudes() {
        requireAuth();
        header('Content-Type: application/json');
        global $pdo;

        try {
            $solicitudes = $pdo->query("SELECT s.*, u.nombres 
                FROM solicitudes_caja s 
                JOIN usuarios u ON s.id_usuario = u.id_usuario 
                WHERE s.estado = 'PENDIENTE'
                ORDER BY s.fecha_solicitud DESC")->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $solicitudes]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * API: Obtener notificaciones globales del sistema (Helper)
     */
    public function getglobalnotifications() {
        requireAuth();
        header('Content-Type: application/json');
        
        // El helper ya está incluido globalmente por el Router/Index
        $notificaciones = getAdminNotifications();
        
        echo json_encode(['success' => true, 'data' => $notificaciones]);
        exit;
    }
}

<?php
// app/helpers/system_helper.php

function getSystemConfig() {
    global $pdo;
    static $config = null;
    if ($config === null) {
        if ($pdo) {
            $stmtConfig = $pdo->query("SELECT * FROM configuracion_sistema WHERE id_configuracion = 1");
            $config = $stmtConfig->fetch(PDO::FETCH_ASSOC);
            if (!empty($config['logo'])) {
                $base = defined('BASE_PATH') ? BASE_PATH : realpath(__DIR__ . '/../../');
                $local_logo_path = $base . '/' . $config['logo'];
                $config['logo_version'] = file_exists($local_logo_path) ? filemtime($local_logo_path) : time();
            } else {
                $config['logo_version'] = '1';
            }
        } else {
            return [];
        }
    }
    return $config;
}

function getAdminNotifications() {
    global $pdo;
    $notificaciones_admin = [];
    $total_notificaciones = 0;
    
    if (isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1 && $pdo) {
        $cacheKey = '_notif_cache';
        $cacheTTL = 60; // segundos
        $now = time();

        if (!isset($_SESSION[$cacheKey]) || ($now - ($_SESSION[$cacheKey]['ts'] ?? 0)) > $cacheTTL) {
            $stmtPagos = $pdo->query("SELECT COUNT(*) as total FROM pagos_empleados WHERE estado = 'PENDIENTE'");
            $countPagos = (int)($stmtPagos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmtPermisos = $pdo->query("SELECT COUNT(*) as total FROM permisos_empleados WHERE estado = 'PENDIENTE'");
            $countPermisos = (int)($stmtPermisos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmtStockBajo = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock_actual <= stock_minimo AND stock_minimo > 0");
            $countStockBajo = (int)($stmtStockBajo->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmtVencer = $pdo->query("SELECT COUNT(*) as total FROM productos WHERE fecha_caducidad IS NOT NULL AND fecha_caducidad <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
            $countVencer = (int)($stmtVencer->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

            $_SESSION[$cacheKey] = [
                'pagos' => $countPagos, 
                'permisos' => $countPermisos, 
                'stock_bajo' => $countStockBajo,
                'vencer' => $countVencer,
                'ts' => $now
            ];
        }

        $countPagos = $_SESSION[$cacheKey]['pagos'];
        $countPermisos = $_SESSION[$cacheKey]['permisos'];
        $countStockBajo = $_SESSION[$cacheKey]['stock_bajo'] ?? 0;
        $countVencer = $_SESSION[$cacheKey]['vencer'] ?? 0;

        if ($countPagos > 0) {
            $notificaciones_admin[] = [
                'icono' => 'bx-money',
                'color' => 'success',
                'titulo' => 'Pagos Pendientes',
                'descripcion' => "Tienes $countPagos pago(s) esperando ser revisado(s).",
                'url' => BASE_URL . '/admin/pago'
            ];
            $total_notificaciones += $countPagos;
        }
        if ($countPermisos > 0) {
            $notificaciones_admin[] = [
                'icono' => 'bx-calendar',
                'color' => 'warning',
                'titulo' => 'Permisos Pendientes',
                'descripcion' => "Tienes $countPermisos permiso(s) por aprobar u observar.",
                'url' => BASE_URL . '/admin/permiso'
            ];
            $total_notificaciones += $countPermisos;
        }
        if ($countStockBajo > 0) {
            $notificaciones_admin[] = [
                'icono' => 'bx-package',
                'color' => 'danger',
                'titulo' => 'Stock Bajo',
                'descripcion' => "Tienes $countStockBajo producto(s) por debajo del stock mínimo.",
                'url' => BASE_URL . '/admin/producto'
            ];
            $total_notificaciones += $countStockBajo;
        }
        if ($countVencer > 0) {
            $notificaciones_admin[] = [
                'icono' => 'bx-timer',
                'color' => 'danger',
                'titulo' => 'Productos por Vencer',
                'descripcion' => "Hay $countVencer producto(s) por caducar en menos de 30 días o ya vencidos.",
                'url' => BASE_URL . '/admin/producto'
            ];
            $total_notificaciones += $countVencer;
        }
    }
    
    return [
        'lista' => $notificaciones_admin,
        'total' => $total_notificaciones
    ];
}

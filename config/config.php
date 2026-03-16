<?php
// config/config.php — Configuración central del sistema

// ════════════════════════════════════════════
// 1. ENTORNO (development / production)
// ════════════════════════════════════════════
// Cambia a 'production' al subir a InfinityFree
if (!defined('APP_ENV')) {
    define('APP_ENV', 'development'); // 'development' | 'production'
}

// ════════════════════════════════════════════
// 2. MANEJO DE ERRORES según entorno
// ════════════════════════════════════════════
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ini_set('log_errors', 1);
    // Log a archivo local (crear carpeta logs/ manualmente en el servidor)
    $logDir = BASE_PATH . '/logs';
    if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
    ini_set('error_log', $logDir . '/app.log');
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// ════════════════════════════════════════════
// 3. ERROR HANDLER CENTRALIZADO
// ════════════════════════════════════════════
set_exception_handler(function (Throwable $e) {
    $msg = "[" . date('Y-m-d H:i:s') . "] " . $e->getMessage() . " en " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    if (defined('BASE_PATH')) {
        @file_put_contents(BASE_PATH . '/logs/app.log', $msg, FILE_APPEND);
    }
    if (APP_ENV === 'development') {
        echo "<pre>Error: " . htmlspecialchars($e->getMessage()) . "\n" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        echo "Error interno del servidor. Inténtalo más tarde.";
    }
    exit;
});

// ════════════════════════════════════════════
// 4. URL BASE (Auto-detecta local vs producción)
// ════════════════════════════════════════════
// OPCIÓN 1: Override manual (descomenta y ajusta si la auto-detección falla)
// define('BASE_URL', '');          // Para InfinityFree (raíz)
// define('BASE_URL', '/carwash-saas'); // Para XAMPP local

if (!defined('BASE_URL')) {
    $docRoot = @realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $appRoot = @realpath(BASE_PATH);

    if ($docRoot && $appRoot && $docRoot === $appRoot) {
        // El dominio apunta directamente a la carpeta del proyecto (InfinityFree, VPS)
        $baseUrl = '';
    } else {
        // Estamos en una subcarpeta (XAMPP: localhost/carwash-saas)
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $scriptDir = str_replace('\\', '/', $scriptDir);
        $baseUrl = ($scriptDir === '/' || $scriptDir === '.') ? '' : $scriptDir;
    }
    define('BASE_URL', $baseUrl);
}

// ════════════════════════════════════════════
// 5. RUTAS DEL SISTEMA
// ════════════════════════════════════════════
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', BASE_PATH . '/views');
define('PUBLIC_URL', BASE_URL . '/public');
define('RESOURCES_URL', PUBLIC_URL . '/resources');
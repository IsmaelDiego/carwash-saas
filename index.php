<?php
// index.php — Punto de entrada principal (optimizado para producción)

// 1. DEFINIR RAÍZ DEL PROYECTO
define('BASE_PATH', __DIR__);

// 2. CONFIGURACIONES BASE (incluye ENV, error handler, rutas)
require_once BASE_PATH . '/config/config.php';

// 3. CONEXIÓN DB (Singleton — se carga 1 sola vez)
require_once BASE_PATH . '/config/database.php';

// 4. AUTOLOADER
require_once BASE_PATH . '/app/core/autoload.php';

// 5. HELPERS
require_once BASE_PATH . '/app/helpers/auth_helper.php';

// 6. SESIONES SEGURAS
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    if (APP_ENV === 'production') {
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
    }
    session_start();
}

// 7. ARRANCAR ROUTER
$router = new Router();
$router->run();

<?php
// index.php (VERSIÓN SI TUS CARPETAS ESTÁN EN LA RAÍZ)

define('BASE_PATH', __DIR__);

// Ajuste de rutas (quitamos '/app')
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/core/Router.php';

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

spl_autoload_register(function ($class) {
    // Rutas ajustadas a la raíz
    $paths = [
        BASE_PATH . '/controllers/',
        BASE_PATH . '/models/',
        BASE_PATH . '/core/',
        BASE_PATH . '/helpers/', // Agregué helpers aquí también
        BASE_PATH . '/app/controllers/', // Dejo estas por si acaso tienes mezcla
        BASE_PATH . '/app/models/',
    ];

    foreach ($paths as $path) {
        if (file_exists($path . $class . '.php')) {
            require_once $path . $class . '.php';
            return;
        }
    }
});

// Ajuste ruta helpers (quitamos '/app' si está en raíz)
if (file_exists(BASE_PATH . '/helpers/auth_helper.php')) {
    require_once BASE_PATH . '/helpers/auth_helper.php';
} else {
    require_once BASE_PATH . '/app/helpers/auth_helper.php';
}

$router = new Router();
$router->run();
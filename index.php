<?php
// Ubicación: index.php (En la raíz de tu proyecto)

// 1. DEFINIR RAÍZ DEL PROYECTO
define('BASE_PATH', __DIR__);

// 2. CONFIGURACIONES BASE
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/database.php';

// 3. CARGA AUTOMÁTICA DE CLASES (Aquí llamas al archivo que acabamos de crear)
require_once BASE_PATH . '/app/core/autoload.php';

// 4. CARGA DE HELPERS (Funciones globales)
// Nota: Los helpers no se cargan con el autoloader porque son funciones, no Clases.
if (file_exists(BASE_PATH . '/helpers/auth_helper.php')) {
    require_once BASE_PATH . '/helpers/auth_helper.php';
} else {
    require_once BASE_PATH . '/app/helpers/auth_helper.php';
}

// 5. SEGURIDAD Y SESIONES (Tu código de seguridad estaba excelente)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1); // Evita robo de sesión por JS
    ini_set('session.use_strict_mode', 1); // Evita fijación de sesión
    session_start();
}

// 6. ARRANCAR EL SISTEMA (Router)
// Gracias al autoloader, ya no necesitas hacer require del Router.php
$router = new Router();
$router->run();
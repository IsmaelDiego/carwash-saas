<?php
// app/core/autoload.php — Autoloader con rutas absolutas

spl_autoload_register(function ($class) {
    // Rutas donde buscar clases (usando __DIR__ para compatibilidad Linux)
    $basePath = dirname(__DIR__, 2); // Sube a la raíz del proyecto

    $paths = [
        $basePath . '/app/controllers/',
        $basePath . '/app/models/',
        $basePath . '/app/core/',
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
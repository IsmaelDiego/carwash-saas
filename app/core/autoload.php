<?php
// Ubicación: app/core/autoload.php

spl_autoload_register(function ($class) {
    // Lista de carpetas donde PHP buscará tus clases (Modelos, Controladores, etc.)
    $paths = [
        BASE_PATH . '/app/controllers/',
        BASE_PATH . '/app/models/',
        BASE_PATH . '/app/core/',
        BASE_PATH . '/controllers/', // Por si tienes carpetas en la raíz
        BASE_PATH . '/models/',
        BASE_PATH . '/core/',
    ];

    foreach ($paths as $path) {
        if (file_exists($path . $class . '.php')) {
            require_once $path . $class . '.php';
            return;
        }
    }
});
<?php
// app/config/config.php

// 1. Detección de URL base (Tu código actual, está perfecto)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = ($scriptName === '/') ? '' : $scriptName;
define('BASE_URL', $baseUrl);

// 2. Rutas del Sistema (Físicas)
define('APP_PATH', BASE_PATH . '/app'); // Asegúrate que BASE_PATH esté definido en index.php
define('VIEW_PATH', BASE_PATH . '/views');

// 3. Rutas para el HTML (Web)
define('PUBLIC_URL', BASE_URL . '/public');

// --- AGREGAMOS ESTA LÍNEA CLAVE ---
// Apunta a: http://localhost/tu_proyecto/public/resources
define('RESOURCES_URL', PUBLIC_URL . '/resources');
<?php
// app/config/config.php

// BASE_URL dinámica (detecta si estás en localhost/proyecto o en dominio.com)
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = ($scriptName === '/') ? '' : $scriptName;
define('BASE_URL', $baseUrl);

define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', BASE_PATH . '/views');
define('PUBLIC_PATH', BASE_URL . '/public'); // Para usar en HTML: <link href="<?= PUBLIC_PATH ?>
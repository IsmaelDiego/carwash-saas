<?php
require_once __DIR__ . '/../../../config/config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Agregar prioridad_adelanto a ordenes
    $pdo->exec("ALTER TABLE ordenes ADD COLUMN prioridad_adelanto TINYINT(1) DEFAULT 0 AFTER id_rampa");
    echo "Columna prioridad_adelanto añadida a ordenes.\n";

    // 2. Agregar proximo_estado a rampas
    $pdo->exec("ALTER TABLE rampas ADD COLUMN proximo_estado ENUM('ACTIVA','INACTIVA','DESCANSO') DEFAULT NULL AFTER estado");
    echo "Columna proximo_estado añadida a rampas.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

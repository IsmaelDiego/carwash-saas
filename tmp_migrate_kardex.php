<?php
/**
 * Migración Fase 2.1: Kardex de Movimientos + Estado MERMA en lotes
 */
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';

try {
    $pdo = Database::getInstance();

    // 1. Crear tabla kardex_movimientos
    $pdo->exec("CREATE TABLE IF NOT EXISTS `kardex_movimientos` (
        `id_movimiento` INT(11) NOT NULL AUTO_INCREMENT,
        `id_producto` INT(11) NOT NULL,
        `id_lote` INT(11) DEFAULT NULL,
        `tipo` ENUM('ENTRADA','VENTA','MERMA','AJUSTE_SALIDA') NOT NULL,
        `cantidad` INT(11) NOT NULL,
        `referencia` VARCHAR(255) DEFAULT NULL,
        `id_orden` INT(11) DEFAULT NULL,
        `id_usuario` INT(11) DEFAULT NULL,
        `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_movimiento`),
        KEY `idx_kardex_producto` (`id_producto`),
        KEY `idx_kardex_lote` (`id_lote`),
        FOREIGN KEY (`id_producto`) REFERENCES `productos`(`id_producto`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    echo "1. Tabla kardex_movimientos creada.\n";

    // 2. Agregar estado MERMA a producto_lotes si no existe
    $col = $pdo->query("SHOW COLUMNS FROM producto_lotes LIKE 'estado'")->fetch(PDO::FETCH_ASSOC);
    if (strpos($col['Type'], 'MERMA') === false) {
        $pdo->exec("ALTER TABLE producto_lotes MODIFY COLUMN estado ENUM('ACTIVO','AGOTADO','MERMA') NOT NULL DEFAULT 'ACTIVO'");
        echo "2. Estado MERMA agregado a producto_lotes.\n";
    } else {
        echo "2. Estado MERMA ya existe en producto_lotes.\n";
    }

    // 3. Retroalimentar kardex con lotes existentes (entradas legacy)
    $check = $pdo->query("SELECT COUNT(*) as c FROM kardex_movimientos")->fetch();
    if ($check['c'] == 0) {
        $lotes = $pdo->query("SELECT * FROM producto_lotes ORDER BY id_lote")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($lotes as $l) {
            $ins = $pdo->prepare("INSERT INTO kardex_movimientos (id_producto, id_lote, tipo, cantidad, referencia, fecha) VALUES (:p, :l, 'ENTRADA', :c, :ref, :f)");
            $ins->execute([
                ':p' => $l['id_producto'],
                ':l' => $l['id_lote'],
                ':c' => $l['cantidad_inicial'],
                ':ref' => 'Entrada Lote #' . $l['id_lote'] . ' (migración)',
                ':f' => $l['fecha_ingreso']
            ]);
        }
        echo "3. Kardex retroalimentado con " . count($lotes) . " entradas.\n";
    } else {
        echo "3. Kardex ya tiene datos, no se retroalimentó.\n";
    }

    echo "\n=== Migración Fase 2.1 completada ===\n";

} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

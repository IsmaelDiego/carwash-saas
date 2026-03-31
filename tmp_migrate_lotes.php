<?php
/**
 * Migración Fase 2: Inventario por Lotes (FIFO)
 * Ejecutar una sola vez para crear la tabla producto_lotes
 */
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';

try {
    $pdo = Database::getInstance();

    // 1. Crear tabla producto_lotes
    $pdo->exec("CREATE TABLE IF NOT EXISTS `producto_lotes` (
        `id_lote` INT(11) NOT NULL AUTO_INCREMENT,
        `id_producto` INT(11) NOT NULL,
        `cantidad_inicial` INT(11) NOT NULL,
        `cantidad_actual` INT(11) NOT NULL,
        `precio_compra` DECIMAL(10,2) NOT NULL,
        `precio_venta` DECIMAL(10,2) NOT NULL,
        `fecha_vencimiento` DATE DEFAULT NULL,
        `estado` ENUM('ACTIVO','AGOTADO') NOT NULL DEFAULT 'ACTIVO',
        `fecha_ingreso` DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_lote`),
        KEY `idx_producto_lote` (`id_producto`, `estado`, `fecha_vencimiento`),
        FOREIGN KEY (`id_producto`) REFERENCES `productos`(`id_producto`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
    echo "1. Tabla producto_lotes creada.\n";

    // 2. Migrar stock existente como Lote 0 (lote inicial legacy)
    $productos = $pdo->query("SELECT id_producto, stock_actual, precio_compra, precio_venta, fecha_caducidad FROM productos WHERE stock_actual > 0")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($productos as $p) {
        // Verificar que no exista ya un lote para este producto
        $check = $pdo->prepare("SELECT COUNT(*) as c FROM producto_lotes WHERE id_producto = :id");
        $check->execute([':id' => $p['id_producto']]);
        if ($check->fetch()['c'] == 0) {
            $ins = $pdo->prepare("INSERT INTO producto_lotes (id_producto, cantidad_inicial, cantidad_actual, precio_compra, precio_venta, fecha_vencimiento) VALUES (:id, :ci, :ca, :pc, :pv, :fv)");
            $ins->execute([
                ':id' => $p['id_producto'],
                ':ci' => $p['stock_actual'],
                ':ca' => $p['stock_actual'],
                ':pc' => $p['precio_compra'],
                ':pv' => $p['precio_venta'],
                ':fv' => $p['fecha_caducidad']
            ]);
        }
    }
    echo "2. Stock existente migrado como lotes iniciales (" . count($productos) . " productos).\n";

    // 3. Agregar columna id_lote a detalle_orden si no existe
    $check2 = $pdo->query("SHOW COLUMNS FROM `detalle_orden` LIKE 'id_lote'");
    if ($check2->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `detalle_orden` ADD COLUMN `id_lote` INT(11) NULL DEFAULT NULL");
        echo "3. Columna id_lote agregada a detalle_orden.\n";
    } else {
        echo "3. Columna id_lote ya existe en detalle_orden.\n";
    }

    echo "\n=== Migracion FIFO completada exitosamente ===\n";

} catch(PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

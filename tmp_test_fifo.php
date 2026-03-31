<?php
/**
 * Test de verificación Fase 2: FIFO Lotes
 */
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';

try {
    $pdo = Database::getInstance();

    echo "=== VERIFICACIÓN FASE 2: INVENTARIO FIFO ===\n\n";

    // 1. Verificar tabla producto_lotes existe
    $t = $pdo->query("SELECT COUNT(*) as c FROM producto_lotes")->fetch();
    echo "1. Tabla producto_lotes: OK ({$t['c']} lotes registrados)\n";

    // 2. Verificar columna id_lote en detalle_orden
    $col = $pdo->query("SHOW COLUMNS FROM detalle_orden LIKE 'id_lote'")->rowCount();
    echo "2. Columna id_lote en detalle_orden: " . ($col ? "OK" : "FALTA") . "\n";

    // 3. Verificar columna id_caja_sesion en ordenes
    $col2 = $pdo->query("SHOW COLUMNS FROM ordenes LIKE 'id_caja_sesion'")->rowCount();
    echo "3. Columna id_caja_sesion en ordenes: " . ($col2 ? "OK" : "FALTA") . "\n";

    // 4. Listar lotes activos
    echo "\n=== LOTES ACTIVOS ===\n";
    $lotes = $pdo->query("
        SELECT pl.id_lote, p.nombre, pl.cantidad_actual, pl.precio_compra, pl.precio_venta, 
               pl.fecha_vencimiento, pl.estado, DATEDIFF(pl.fecha_vencimiento, CURDATE()) as dias
        FROM producto_lotes pl
        INNER JOIN productos p ON pl.id_producto = p.id_producto
        WHERE pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0
        ORDER BY pl.id_lote
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lotes as $l) {
        $vc = $l['fecha_vencimiento'] ? " | Vence: {$l['fecha_vencimiento']} ({$l['dias']}d)" : "";
        echo "   Lote #{$l['id_lote']} | {$l['nombre']} | Stock: {$l['cantidad_actual']} | PC: {$l['precio_compra']} | PV: {$l['precio_venta']}{$vc}\n";
    }

    // 5. Verificar sincronización stock
    echo "\n=== SINCRONIZACIÓN STOCK ===\n";
    $prods = $pdo->query("
        SELECT p.id_producto, p.nombre, p.stock_actual as stock_tabla_producto,
               IFNULL((SELECT SUM(pl.cantidad_actual) FROM producto_lotes pl WHERE pl.id_producto = p.id_producto AND pl.estado = 'ACTIVO'), 0) as stock_lotes
        FROM productos p ORDER BY p.id_producto
    ")->fetchAll(PDO::FETCH_ASSOC);

    $syncOK = true;
    foreach ($prods as $pr) {
        $match = ($pr['stock_tabla_producto'] == $pr['stock_lotes']) ? "✓" : "✗ DESINCRONIZADO";
        if ($pr['stock_tabla_producto'] != $pr['stock_lotes']) $syncOK = false;
        echo "   {$pr['nombre']}: productos.stock={$pr['stock_tabla_producto']} vs lotes.sum={$pr['stock_lotes']} $match\n";
    }

    echo "\n=== RESULTADO: " . ($syncOK ? "TODO SINCRONIZADO CORRECTAMENTE" : "HAY DESINCRONIZACIONES") . " ===\n";

} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

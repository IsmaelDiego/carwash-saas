<?php

class Producto {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ════════════════════════════════════════
    // LISTAR TODOS
    // ════════════════════════════════════════
    public function getAll() {
        $sql = "SELECT p.*, 
                    DATE_FORMAT(p.fecha_registro, '%Y-%m-%d %H:%i:%s') as fecha_raw, 
                    DATE_FORMAT(p.fecha_registro, '%d/%m/%Y') as fecha,
                    (SELECT MIN(pl.fecha_vencimiento) FROM producto_lotes pl 
                        WHERE pl.id_producto = p.id_producto AND pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0 AND pl.fecha_vencimiento IS NOT NULL
                    ) as prox_vencimiento,
                    (SELECT MAX(pl.precio_venta) FROM producto_lotes pl 
                        WHERE pl.id_producto = p.id_producto AND pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0
                    ) as precio_sugerido,
                    (SELECT COUNT(*) FROM producto_lotes pl 
                        WHERE pl.id_producto = p.id_producto AND pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0
                    ) as lotes_activos
                FROM productos p ORDER BY p.nombre ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // OBTENER POR ID
    // ════════════════════════════════════════
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM productos WHERE id_producto = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // STOCK TOTAL DESDE LOTES (no desde campo estático)
    // ════════════════════════════════════════
    public function getStockTotal($id_producto) {
        $stmt = $this->pdo->prepare(
            "SELECT IFNULL(SUM(cantidad_actual), 0) as total 
             FROM producto_lotes 
             WHERE id_producto = :id AND estado = 'ACTIVO'"
        );
        $stmt->execute([':id' => $id_producto]);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // ════════════════════════════════════════
    // OBTENER PRECIO DE VENTA MÁXIMO (Sugerido)
    // ════════════════════════════════════════
    public function getPrecioVentaSugerido($id_producto) {
        $stmt = $this->pdo->prepare(
            "SELECT IFNULL(MAX(precio_venta), 0) as precio_max 
             FROM producto_lotes 
             WHERE id_producto = :id AND estado = 'ACTIVO' AND cantidad_actual > 0"
        );
        $stmt->execute([':id' => $id_producto]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? (float)$res['precio_max'] : 0.0;
    }

    // ════════════════════════════════════════
    // OBTENER LOTE PRÓXIMO A VENCER (FIFO)
    // ════════════════════════════════════════
    public function getLoteProximoAVencer($id_producto) {
        $stmt = $this->pdo->prepare(
            "SELECT id_lote, fecha_vencimiento, 
                    IF(fecha_vencimiento < CURDATE(), 1, 0) as vencido 
             FROM producto_lotes 
             WHERE id_producto = :id AND estado = 'ACTIVO' AND cantidad_actual > 0 
             ORDER BY fecha_vencimiento ASC, id_lote ASC LIMIT 1"
        );
        $stmt->execute([':id' => $id_producto]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$lote) return null;
        
        return [
            'id_lote' => $lote['id_lote'],
            'fecha'   => $lote['fecha_vencimiento'],
            'vencido' => (int)$lote['vencido']
        ];
    }

    // ════════════════════════════════════════
    // REGISTRAR PRODUCTO
    // ════════════════════════════════════════
    public function registrar($data) {
        $sql = "INSERT INTO productos (nombre, precio_compra, precio_venta, stock_actual, stock_minimo, fecha_caducidad)
                VALUES (:nombre, :pc, :pv, 0, :smin, :fcad)";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':pc'     => $data['precio_compra'],
            ':pv'     => $data['precio_venta'],
            ':smin'   => (int)($data['stock_minimo'] ?? 5),
            ':fcad'   => !empty($data['fecha_caducidad']) ? $data['fecha_caducidad'] : null
        ]);

        if ($ok) {
            $id_producto = $this->pdo->lastInsertId();
            $stock_inicial = (int)($data['stock_actual'] ?? 0);
            if ($stock_inicial > 0) {
                $this->agregarLote([
                    'id_producto'      => $id_producto,
                    'cantidad'         => $stock_inicial,
                    'precio_compra'    => $data['precio_compra'],
                    'precio_venta'     => $data['precio_venta'],
                    'fecha_vencimiento'=> !empty($data['fecha_caducidad']) ? $data['fecha_caducidad'] : null
                ]);
            }
        }
        return $ok;
    }

    // ════════════════════════════════════════
    // EDITAR PRODUCTO (ficha, NO stock directo)
    // ════════════════════════════════════════
    public function editar($data) {
        $sql = "UPDATE productos SET nombre = :nombre, precio_compra = :pc, precio_venta = :pv,
                    stock_minimo = :smin, fecha_caducidad = :fcad
                WHERE id_producto = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':pc'     => $data['precio_compra'],
            ':pv'     => $data['precio_venta'],
            ':smin'   => (int)($data['stock_minimo'] ?? 5),
            ':fcad'   => !empty($data['fecha_caducidad']) ? $data['fecha_caducidad'] : null,
            ':id'     => $data['id_producto']
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    //  LOTES — AGREGAR LOTE (Abastecimiento por Admin)
    // ════════════════════════════════════════════════════════════════
    public function agregarLote($data, $id_usuario = null) {
        $sql = "INSERT INTO producto_lotes (id_producto, cantidad_inicial, cantidad_actual, precio_compra, precio_venta, fecha_vencimiento)
                VALUES (:id, :ci, :ca, :pc, :pv, :fv)";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            ':id'  => $data['id_producto'],
            ':ci'  => (int)$data['cantidad'],
            ':ca'  => (int)$data['cantidad'],
            ':pc'  => $data['precio_compra'],
            ':pv'  => $data['precio_venta'],
            ':fv'  => !empty($data['fecha_vencimiento']) ? $data['fecha_vencimiento'] : null
        ]);

        if ($ok) {
            $id_lote = $this->pdo->lastInsertId();
            $this->sincronizarStock($data['id_producto']);
            // Registrar en Kardex
            $this->registrarKardex([
                'id_producto' => $data['id_producto'],
                'id_lote'     => $id_lote,
                'tipo'        => 'ENTRADA',
                'cantidad'    => (int)$data['cantidad'],
                'referencia'  => 'Entrada Lote #' . $id_lote,
                'id_usuario'  => $id_usuario
            ]);
        }
        return $ok;
    }

    // ════════════════════════════════════════════════════════════════
    //  LOTES — LISTAR LOTES DE UN PRODUCTO (ACTIVOS con stock > 0)
    // ════════════════════════════════════════════════════════════════
    public function getLotes($id_producto) {
        $sql = "SELECT *, DATEDIFF(fecha_vencimiento, CURDATE()) as dias_para_vencer
                FROM producto_lotes 
                WHERE id_producto = :id AND estado = 'ACTIVO' AND cantidad_actual > 0
                ORDER BY CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END, fecha_vencimiento ASC, fecha_ingreso ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════════════════════════════
    //  LOTES — TODOS LOS LOTES (Para vista Admin)
    // ════════════════════════════════════════════════════════════════
    public function getAllLotes() {
        $sql = "SELECT pl.*, p.nombre as producto_nombre, p.stock_minimo,
                    DATEDIFF(pl.fecha_vencimiento, CURDATE()) as dias_para_vencer
                FROM producto_lotes pl
                INNER JOIN productos p ON pl.id_producto = p.id_producto
                WHERE pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0
                ORDER BY CASE WHEN pl.fecha_vencimiento IS NULL THEN 1 ELSE 0 END, pl.fecha_vencimiento ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════════════════════════════
    //  LOTES VENDIBLES — Excluye lotes vencidos del FIFO
    // ════════════════════════════════════════════════════════════════
    public function getLotesVendibles($id_producto) {
        $sql = "SELECT *, DATEDIFF(fecha_vencimiento, CURDATE()) as dias_para_vencer
                FROM producto_lotes 
                WHERE id_producto = :id AND estado = 'ACTIVO' AND cantidad_actual > 0
                  AND (fecha_vencimiento IS NULL OR fecha_vencimiento >= CURDATE())
                ORDER BY CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END, fecha_vencimiento ASC, fecha_ingreso ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════════════════════════════
    //  VERIFICAR si un producto tiene lotes vencidos bloqueantes
    // ════════════════════════════════════════════════════════════════
    public function tieneVencidosBloqueantes($id_producto) {
        $lotes = $this->getLotes($id_producto); // todos los activos
        foreach ($lotes as $lote) {
            if ($lote['fecha_vencimiento'] && $lote['dias_para_vencer'] !== null && (int)$lote['dias_para_vencer'] < 0) {
                return [
                    'bloqueado' => true,
                    'id_lote'   => $lote['id_lote'],
                    'fecha'     => $lote['fecha_vencimiento'],
                    'cantidad'  => $lote['cantidad_actual']
                ];
            }
        }
        return ['bloqueado' => false];
    }

    // ════════════════════════════════════════════════════════════════
    //  FIFO — DESCONTAR STOCK POR LOTES
    //  ★ Ajuste: salta lotes vencidos y usa precio_venta del lote
    //  ★ Si TODOS los lotes son vencidos, lanza excepción con bloqueo
    // ════════════════════════════════════════════════════════════════
    public function descontarStockFIFO($id_producto, $cantidad_solicitada, $id_orden = null, $id_usuario = null) {
        // Solo lotes NO vencidos (vendibles)
        $lotes = $this->getLotesVendibles($id_producto);
        
        if (empty($lotes)) {
            // Verificar si es porque están vencidos
            $vencidos = $this->tieneVencidosBloqueantes($id_producto);
            if ($vencidos['bloqueado']) {
                throw new \Exception("PRODUCTO_VENCIDO|Producto con Lote #" . $vencidos['id_lote'] . " vencido (". $vencidos['fecha'] ."). Requiere retiro de almacén antes de vender.");
            }
            throw new \Exception("Stock insuficiente (sin lotes activos) para producto #$id_producto.");
        }

        $pendiente = (int)$cantidad_solicitada;
        $lotesConsumidos = [];

        foreach ($lotes as $lote) {
            if ($pendiente <= 0) break;

            $disponible = (int)$lote['cantidad_actual'];
            $consumir = min($pendiente, $disponible);

            $nuevoStock = $disponible - $consumir;
            $nuevoEstado = ($nuevoStock <= 0) ? 'AGOTADO' : 'ACTIVO';

            $upd = $this->pdo->prepare(
                "UPDATE producto_lotes SET cantidad_actual = :nueva, estado = :est WHERE id_lote = :id"
            );
            $upd->execute([
                ':nueva' => $nuevoStock,
                ':est'   => $nuevoEstado,
                ':id'    => $lote['id_lote']
            ]);

            // ★ Precio dinámico: usa precio_venta del LOTE, no del producto
            $lotesConsumidos[] = [
                'id_lote'        => $lote['id_lote'],
                'cantidad'       => $consumir,
                'precio_venta'   => $lote['precio_venta'],
                'precio_compra'  => $lote['precio_compra']
            ];

            // Registrar en Kardex
            $this->registrarKardex([
                'id_producto' => $id_producto,
                'id_lote'     => $lote['id_lote'],
                'tipo'        => 'VENTA',
                'cantidad'    => $consumir,
                'referencia'  => $id_orden ? 'Venta Orden #' . $id_orden : 'Venta directa',
                'id_orden'    => $id_orden,
                'id_usuario'  => $id_usuario
            ]);

            $pendiente -= $consumir;
        }

        if ($pendiente > 0) {
            throw new \Exception("Stock insuficiente en lotes vendibles para producto #$id_producto. Faltan $pendiente unidades.");
        }

        $this->sincronizarStock($id_producto);
        return $lotesConsumidos;
    }

    // ════════════════════════════════════════════════════════════════
    //  MERMA — Retirar unidades de un lote específico
    //  Registra gasto automático con precio_compra × cantidad
    // ════════════════════════════════════════════════════════════════
    public function registrarMerma($id_lote, $cantidad, $motivo, $id_usuario) {
        // Obtener información del lote
        $stmt = $this->pdo->prepare("SELECT * FROM producto_lotes WHERE id_lote = :id AND estado = 'ACTIVO'");
        $stmt->execute([':id' => $id_lote]);
        $lote = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$lote) {
            throw new \Exception("Lote #$id_lote no encontrado o no está activo.");
        }

        $cantidad = min((int)$cantidad, (int)$lote['cantidad_actual']);
        if ($cantidad <= 0) {
            throw new \Exception("La cantidad de merma debe ser mayor a 0.");
        }

        $this->pdo->beginTransaction();
        try {
            // 1. Descontar del lote
            $nuevoStock = (int)$lote['cantidad_actual'] - $cantidad;
            $nuevoEstado = ($nuevoStock <= 0) ? 'MERMA' : 'ACTIVO';
            // Si quedan 0, marcar como MERMA; si queda stock, seguir ACTIVO
            if ($nuevoStock <= 0 && $cantidad == (int)$lote['cantidad_actual']) {
                $nuevoEstado = 'MERMA';
            }

            $this->pdo->prepare(
                "UPDATE producto_lotes SET cantidad_actual = :ca, estado = :est WHERE id_lote = :id"
            )->execute([
                ':ca'  => $nuevoStock,
                ':est' => $nuevoEstado,
                ':id'  => $id_lote
            ]);

            // 2. Registrar gasto automático (precio_compra × cantidad = pérdida real)
            $montoGasto = $lote['precio_compra'] * $cantidad;
            $descripcion = "Merma: $motivo (Lote #$id_lote, $cantidad u. de " . $lote['id_producto'] . ")";

            $this->pdo->prepare(
                "INSERT INTO gastos (descripcion, monto, tipo_gasto, fecha_gasto, id_usuario_registrador) 
                 VALUES (:desc, :monto, 'VARIABLE', CURDATE(), :uid)"
            )->execute([
                ':desc'  => $descripcion,
                ':monto' => $montoGasto,
                ':uid'   => $id_usuario
            ]);

            // 3. Registrar en Kardex
            $this->registrarKardex([
                'id_producto' => $lote['id_producto'],
                'id_lote'     => $id_lote,
                'tipo'        => 'MERMA',
                'cantidad'    => $cantidad,
                'referencia'  => "Merma: $motivo",
                'id_usuario'  => $id_usuario
            ]);

            // 4. Sincronizar stock
            $this->sincronizarStock($lote['id_producto']);

            $this->pdo->commit();
            return [
                'success'   => true,
                'cantidad'  => $cantidad,
                'monto'     => $montoGasto,
                'producto'  => $lote['id_producto']
            ];
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // ════════════════════════════════════════════════════════════════
    //  KARDEX — Registrar movimiento
    // ════════════════════════════════════════════════════════════════
    public function registrarKardex($data) {
        $sql = "INSERT INTO kardex_movimientos (id_producto, id_lote, tipo, cantidad, referencia, id_orden, id_usuario) 
                VALUES (:p, :l, :t, :c, :r, :o, :u)";
        $this->pdo->prepare($sql)->execute([
            ':p' => $data['id_producto'],
            ':l' => $data['id_lote'] ?? null,
            ':t' => $data['tipo'],
            ':c' => $data['cantidad'],
            ':r' => $data['referencia'] ?? null,
            ':o' => $data['id_orden'] ?? null,
            ':u' => $data['id_usuario'] ?? null
        ]);
    }

    // ════════════════════════════════════════════════════════════════
    //  KARDEX — Historial de un producto
    // ════════════════════════════════════════════════════════════════
    public function getKardex($id_producto) {
        $sql = "SELECT km.*, u.nombres as usuario_nombre,
                    DATE_FORMAT(km.fecha, '%d/%m/%Y %H:%i') as fecha_fmt
                FROM kardex_movimientos km
                LEFT JOIN usuarios u ON km.id_usuario = u.id_usuario
                WHERE km.id_producto = :id
                ORDER BY km.fecha DESC, km.id_movimiento DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════════════════════════════
    //  KARDEX — Historial global (todos los productos)
    // ════════════════════════════════════════════════════════════════
    public function getKardexGlobal($limit = 100) {
        $sql = "SELECT km.*, p.nombre as producto_nombre, u.nombres as usuario_nombre,
                    DATE_FORMAT(km.fecha, '%d/%m/%Y %H:%i') as fecha_fmt
                FROM kardex_movimientos km
                INNER JOIN productos p ON km.id_producto = p.id_producto
                LEFT JOIN usuarios u ON km.id_usuario = u.id_usuario
                ORDER BY km.fecha DESC, km.id_movimiento DESC
                LIMIT $limit";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════════════════════════════
    //  SINCRONIZAR — Actualiza productos.stock_actual desde lotes
    // ════════════════════════════════════════════════════════════════
    public function sincronizarStock($id_producto) {
        $sql = "UPDATE productos SET stock_actual = (
                    SELECT IFNULL(SUM(cantidad_actual), 0) 
                    FROM producto_lotes 
                    WHERE id_producto = :id AND estado = 'ACTIVO'
                ) WHERE id_producto = :id2";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_producto, ':id2' => $id_producto]);
    }

    // ════════════════════════════════════════
    // AJUSTAR STOCK LEGACY (redirigir a lotes)
    // ════════════════════════════════════════
    public function ajustarStock($id, $cantidad, $tipo = 'ENTRADA', $id_usuario = null) {
        if ($tipo === 'ENTRADA') {
            $prod = $this->getById($id);
            return $this->agregarLote([
                'id_producto'       => $id,
                'cantidad'          => abs((int)$cantidad),
                'precio_compra'     => $prod['precio_compra'],
                'precio_venta'      => $prod['precio_venta'],
                'fecha_vencimiento' => $prod['fecha_caducidad']
            ], $id_usuario);
        } else {
            $this->descontarStockFIFO($id, abs((int)$cantidad), null, $id_usuario);
            return true;
        }
    }

    // ════════════════════════════════════════
    // ELIMINAR
    // ════════════════════════════════════════
    public function eliminar($id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM detalle_orden WHERE id_producto = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetch()['total'] > 0) {
            return false;
        }
        $this->pdo->prepare("DELETE FROM kardex_movimientos WHERE id_producto = :id")->execute([':id' => $id]);
        $this->pdo->prepare("DELETE FROM producto_lotes WHERE id_producto = :id")->execute([':id' => $id]);
        $stmt = $this->pdo->prepare("DELETE FROM productos WHERE id_producto = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ════════════════════════════════════════
    // VERIFICAR NOMBRE DUPLICADO
    // ════════════════════════════════════════
    public function existeNombre($nombre, $excluirId = null) {
        $sql = "SELECT COUNT(*) as total FROM productos WHERE LOWER(nombre) = LOWER(:nombre)";
        $params = [':nombre' => trim($nombre)];
        if ($excluirId) {
            $sql .= " AND id_producto != :id";
            $params[':id'] = $excluirId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'] > 0;
    }

    // ════════════════════════════════════════
    // ESTADÍSTICAS
    // ════════════════════════════════════════
    public function getEstadisticas() {
        $stats = [];
        $stats['total'] = $this->pdo->query("SELECT COUNT(*) as total FROM productos")->fetch()['total'];
        $stats['con_stock'] = $this->pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock_actual > 0")->fetch()['total'];
        $stats['bajo_stock'] = $this->pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock_actual <= stock_minimo AND stock_actual > 0")->fetch()['total'];
        $stats['sin_stock'] = $this->pdo->query("SELECT COUNT(*) as total FROM productos WHERE stock_actual = 0")->fetch()['total'];
        $stats['lotes_por_vencer'] = $this->pdo->query(
            "SELECT COUNT(*) as total FROM producto_lotes 
             WHERE estado = 'ACTIVO' AND cantidad_actual > 0 
             AND fecha_vencimiento IS NOT NULL 
             AND fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)"
        )->fetch()['total'];
        $stats['por_vencer'] = $stats['lotes_por_vencer'];
        $stats['valor_inventario'] = $this->pdo->query(
            "SELECT COALESCE(SUM(precio_compra * cantidad_actual), 0) as total FROM producto_lotes WHERE estado = 'ACTIVO'"
        )->fetch()['total'];
        $stats['valor_venta'] = $this->pdo->query(
            "SELECT COALESCE(SUM(precio_venta * cantidad_actual), 0) as total FROM producto_lotes WHERE estado = 'ACTIVO'"
        )->fetch()['total'];
        $stats['total_lotes_activos'] = $this->pdo->query(
            "SELECT COUNT(*) as total FROM producto_lotes WHERE estado = 'ACTIVO' AND cantidad_actual > 0"
        )->fetch()['total'];
        return $stats;
    }
}

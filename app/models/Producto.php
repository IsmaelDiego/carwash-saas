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
        $sql = "SELECT * FROM productos ORDER BY nombre ASC";
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
    // REGISTRAR
    // ════════════════════════════════════════
    public function registrar($data) {
        $sql = "INSERT INTO productos (nombre, precio_compra, precio_venta, stock_actual, stock_minimo)
                VALUES (:nombre, :pc, :pv, :stock, :smin)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':pc'     => $data['precio_compra'],
            ':pv'     => $data['precio_venta'],
            ':stock'  => (int)($data['stock_actual'] ?? 0),
            ':smin'   => (int)($data['stock_minimo'] ?? 5)
        ]);
    }

    // ════════════════════════════════════════
    // EDITAR
    // ════════════════════════════════════════
    public function editar($data) {
        $sql = "UPDATE productos SET nombre = :nombre, precio_compra = :pc, precio_venta = :pv,
                    stock_actual = :stock, stock_minimo = :smin
                WHERE id_producto = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':pc'     => $data['precio_compra'],
            ':pv'     => $data['precio_venta'],
            ':stock'  => (int)($data['stock_actual'] ?? 0),
            ':smin'   => (int)($data['stock_minimo'] ?? 5),
            ':id'     => $data['id_producto']
        ]);
    }

    // ════════════════════════════════════════
    // AJUSTAR STOCK (Entrada o Salida)
    // ════════════════════════════════════════
    public function ajustarStock($id, $cantidad, $tipo = 'ENTRADA') {
        if ($tipo === 'ENTRADA') {
            $sql = "UPDATE productos SET stock_actual = stock_actual + :cant WHERE id_producto = :id";
        } else {
            $sql = "UPDATE productos SET stock_actual = GREATEST(stock_actual - :cant, 0) WHERE id_producto = :id";
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':cant' => abs((int)$cantidad), ':id' => $id]);
    }

    // ════════════════════════════════════════
    // ELIMINAR
    // ════════════════════════════════════════
    public function eliminar($id) {
        // Verificar si tiene detalles de orden
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM detalle_orden WHERE id_producto = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetch()['total'] > 0) {
            return false; // No se puede eliminar — tiene órdenes asociadas
        }
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
        $stats['valor_inventario'] = $this->pdo->query("SELECT COALESCE(SUM(precio_compra * stock_actual), 0) as total FROM productos")->fetch()['total'];
        $stats['valor_venta'] = $this->pdo->query("SELECT COALESCE(SUM(precio_venta * stock_actual), 0) as total FROM productos")->fetch()['total'];
        return $stats;
    }
}

<?php

class CategoriaVehiculo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categorias_vehiculos ORDER BY id_categoria DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($data) {
        $sql = "INSERT INTO categorias_vehiculos (nombre, factor_precio, factor_tiempo) 
                VALUES (:nombre, :factor_precio, :factor_tiempo)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':factor_precio' => $data['factor_precio'] ?? 1.00,
            ':factor_tiempo' => $data['factor_tiempo'] ?? 1.00
        ]);
    }

    public function editar($data) {
        $sql = "UPDATE categorias_vehiculos SET 
                    nombre = :nombre,
                    factor_precio = :factor_precio,
                    factor_tiempo = :factor_tiempo
                WHERE id_categoria = :id_categoria";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($data['nombre']),
            ':factor_precio' => $data['factor_precio'],
            ':factor_tiempo' => $data['factor_tiempo'],
            ':id_categoria' => $data['id_categoria']
        ]);
    }

    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM categorias_vehiculos WHERE id_categoria = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            return false; // Posible constraint error (foreign key)
        }
    }
}

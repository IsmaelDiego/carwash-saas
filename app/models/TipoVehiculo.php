<?php
// Ubicación: app/models/TipoVehiculo.php

use PDO;

class TipoVehiculo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los tipos de vehículo
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM tipo_vehiculo ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener solo tipos activos
     */
    public function getActivos(): array
    {
        $sql = "SELECT * FROM tipo_vehiculo WHERE estado = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar tipo por ID
     */
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tipo_vehiculo WHERE id_tipo_vehiculo = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si el nombre ya existe
     */
    public function existeNombre(string $nombre, ?int $exceptoId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM tipo_vehiculo WHERE nombre = :nombre";
        $params = [':nombre' => $nombre];
        
        if ($exceptoId) {
            $sql .= " AND id_tipo_vehiculo != :id";
            $params[':id'] = $exceptoId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nuevo tipo de vehículo
     */
    public function crear(array $data): bool
    {
        $sql = "INSERT INTO tipo_vehiculo (nombre, factor_precio, estado) 
                VALUES (:nombre, :factor, :estado)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':factor' => $data['factor_precio'] ?? 1.00,
            ':estado' => $data['estado'] ?? 1
        ]);
    }

    /**
     * Actualizar tipo de vehículo
     */
    public function actualizar(array $data): bool
    {
        $sql = "UPDATE tipo_vehiculo SET 
                    nombre = :nombre, 
                    factor_precio = :factor, 
                    estado = :estado 
                WHERE id_tipo_vehiculo = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':factor' => $data['factor_precio'],
            ':estado' => $data['estado'],
            ':id' => $data['id_tipo_vehiculo']
        ]);
    }

    /**
     * Eliminar tipo de vehículo
     */
    public function eliminar(int $id): bool
    {
        $sql = "DELETE FROM tipo_vehiculo WHERE id_tipo_vehiculo = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Verificar si tiene vehículos vinculados
     */
    public function tieneVehiculosVinculados(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM vehiculos WHERE id_tipo_vehiculo = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}

<?php
// Ubicación: app/models/Servicio.php

use PDO;

class Servicio
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los servicios con sus precios por tipo de vehículo
     */
    public function getAll(): array
    {
        $sql = "SELECT * FROM servicios ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar precios por tipo de vehículo a cada servicio
        foreach ($servicios as &$servicio) {
            $servicio['precios'] = $this->getPreciosByServicio($servicio['id_servicio']);
        }

        return $servicios;
    }

    /**
     * Obtener solo servicios activos
     */
    public function getActivos(): array
    {
        $sql = "SELECT * FROM servicios WHERE estado = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($servicios as &$servicio) {
            $servicio['precios'] = $this->getPreciosByServicio($servicio['id_servicio']);
        }

        return $servicios;
    }

    /**
     * Obtener un servicio por ID
     */
    public function findById(int $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM servicios WHERE id_servicio = :id");
        $stmt->execute([':id' => $id]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($servicio) {
            $servicio['precios'] = $this->getPreciosByServicio($id);
        }
        
        return $servicio;
    }

    /**
     * Obtener precios por tipo de vehículo de un servicio
     */
    public function getPreciosByServicio(int $id_servicio): array
    {
        $sql = "SELECT sp.*, tv.nombre AS tipo_vehiculo 
                FROM servicio_precios sp
                INNER JOIN tipo_vehiculo tv ON sp.id_tipo_vehiculo = tv.id_tipo_vehiculo
                WHERE sp.id_servicio = :id
                ORDER BY tv.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id_servicio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si el nombre ya existe
     */
    public function existeNombre(string $nombre, ?int $exceptoId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM servicios WHERE nombre = :nombre";
        $params = [':nombre' => $nombre];
        
        if ($exceptoId) {
            $sql .= " AND id_servicio != :id";
            $params[':id'] = $exceptoId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nuevo servicio
     */
    public function crear(array $data): int
    {
        $sql = "INSERT INTO servicios (nombre, descripcion, duracion_minutos, estado) 
                VALUES (:nombre, :descripcion, :duracion, :estado)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? '',
            ':duracion' => $data['duracion_minutos'] ?? 30,
            ':estado' => $data['estado'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualizar servicio
     */
    public function actualizar(array $data): bool
    {
        $sql = "UPDATE servicios SET 
                    nombre = :nombre, 
                    descripcion = :descripcion, 
                    duracion_minutos = :duracion, 
                    estado = :estado 
                WHERE id_servicio = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'],
            ':duracion' => $data['duracion_minutos'],
            ':estado' => $data['estado'],
            ':id' => $data['id_servicio']
        ]);
    }

    /**
     * Eliminar servicio y sus precios
     */
    public function eliminar(int $id): bool
    {
        // Primero eliminar precios asociados
        $this->eliminarPrecios($id);
        
        $sql = "DELETE FROM servicios WHERE id_servicio = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Guardar/Actualizar precio de servicio por tipo de vehículo
     */
    public function guardarPrecio(int $id_servicio, int $id_tipo_vehiculo, float $precio): bool
    {
        // Verificar si ya existe
        $sql = "SELECT id FROM servicio_precios WHERE id_servicio = :id_servicio AND id_tipo_vehiculo = :id_tipo";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_servicio' => $id_servicio, ':id_tipo' => $id_tipo_vehiculo]);
        
        if ($stmt->fetch()) {
            // Actualizar
            $sql = "UPDATE servicio_precios SET precio = :precio 
                    WHERE id_servicio = :id_servicio AND id_tipo_vehiculo = :id_tipo";
        } else {
            // Insertar
            $sql = "INSERT INTO servicio_precios (id_servicio, id_tipo_vehiculo, precio) 
                    VALUES (:id_servicio, :id_tipo, :precio)";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_servicio' => $id_servicio,
            ':id_tipo' => $id_tipo_vehiculo,
            ':precio' => $precio
        ]);
    }

    /**
     * Guardar múltiples precios para un servicio
     */
    public function guardarPrecios(int $id_servicio, array $precios): bool
    {
        foreach ($precios as $id_tipo => $precio) {
            if ($precio > 0) {
                $this->guardarPrecio($id_servicio, $id_tipo, $precio);
            }
        }
        return true;
    }

    /**
     * Eliminar todos los precios de un servicio
     */
    public function eliminarPrecios(int $id_servicio): bool
    {
        $sql = "DELETE FROM servicio_precios WHERE id_servicio = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id_servicio]);
    }

    /**
     * Verificar si el servicio está siendo usado en órdenes
     */
    public function tieneOrdenesVinculadas(int $id): bool
    {
        $sql = "SELECT COUNT(*) FROM orden_servicios WHERE id_servicio = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}
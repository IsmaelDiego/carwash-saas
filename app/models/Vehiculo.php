<?php

class Vehiculo
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $sql = "SELECT 
                    v.id_vehiculo,
                    v.id_cliente,
                    v.placa,
                    v.tipo_vehiculo_id,
                    v.marca,
                    v.modelo,
                    v.color,
                    v.observaciones,
                    v.fecha_registro,
                    v.estado,
                    COALESCE(c.nombres, 'Desconocido') AS cliente_nombres,
                    COALESCE(c.apellidos, '') AS cliente_apellidos,
                    CONCAT(COALESCE(c.nombres, ''), ' ', COALESCE(c.apellidos, '')) AS propietario,
                    COALESCE(tv.nombre, 'No asignado') AS nombre_tipo
                FROM vehiculos v
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN tipo_vehiculo tv ON v.tipo_vehiculo_id = tv.id_tipo_vehiculo
                ORDER BY v.id_vehiculo DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * OBTENER TIPOS (Para el Select)
     */
    public function obtenerTiposVehiculo(): array
    {
        $sql = "SELECT id_tipo_vehiculo, nombre FROM tipo_vehiculo ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * CREAR
     */
    public function crearVehiculo(array $data): bool
    {
        $sql = "INSERT INTO vehiculos (
                    id_cliente, placa, tipo_vehiculo_id, marca, modelo, 
                    color, observaciones, fecha_registro, estado
                ) VALUES (
                    :id_cliente, :placa, :tipo_vehiculo_id, :marca, :modelo, 
                    :color, :obs, NOW(), 1
                )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_cliente'       => $data['id_cliente'],
            ':placa'            => strtoupper(trim($data['placa'])),
            ':tipo_vehiculo_id' => $data['tipo_vehiculo_id'],
            ':marca'            => strtoupper(trim($data['marca'])),
            ':modelo'           => trim($data['modelo']),
            ':color'            => trim($data['color']),
            ':obs'              => trim($data['observaciones'])
        ]);
    }

    /**
     * ACTUALIZAR
     */
    public function actualizarVehiculo(array $data): bool
    {
        $sql = "UPDATE vehiculos SET 
                    placa = :placa, 
                    tipo_vehiculo_id = :tipo_vehiculo_id, 
                    marca = :marca, 
                    modelo = :modelo, 
                    color = :color, 
                    observaciones = :observaciones 
                WHERE id_vehiculo = :id_vehiculo";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':placa'            => strtoupper(trim($data['placa'])),
            ':tipo_vehiculo_id' => $data['tipo_vehiculo_id'],
            ':marca'            => strtoupper(trim($data['marca'])),
            ':modelo'           => trim($data['modelo']),
            ':color'            => trim($data['color']),
            ':observaciones'    => trim($data['observaciones']),
            ':id_vehiculo'      => $data['id_vehiculo']
        ]);
    }

    /**
     * ELIMINAR (Lógico)
     */
    public function eliminarVehiculo($id_vehiculo): bool
    {
        $sql = "UPDATE vehiculos SET estado = 0 WHERE id_vehiculo = :id_vehiculo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_vehiculo' => $id_vehiculo]);
    }

    // Auxiliares
    public function findByPlaca(string $placa)
    {
        $stmt = $this->db->prepare("SELECT * FROM vehiculos WHERE placa = :placa AND estado = 1");
        $stmt->execute([':placa' => strtoupper($placa)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
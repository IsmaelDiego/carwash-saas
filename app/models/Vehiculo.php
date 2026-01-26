<?php

class Vehiculo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

   public function getAll(): array
    {
        // Cruzamos la tabla vehiculos (v) con clientes (c) usando el id_cliente
        $sql = "SELECT 
                    v.id_vehiculo,
                    v.id_cliente,
                    v.placa,
                    v.tipo,
                    v.marca,
                    v.modelo,
                    v.color,
                    v.observaciones,
                    v.fecha_registro,
                    c.nombres AS cliente_nombres,
                    c.apellidos AS cliente_apellidos,
                    CONCAT(c.nombres, ' ', c.apellidos) AS propietario
                FROM vehiculos v
                INNER JOIN clientes c ON v.id_cliente = c.id_cliente
                ORDER BY v.id_vehiculo DESC 
                LIMIT 1000";

        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findByPlaca(string $placa)
    {
        $stmt = $this->db->prepare("SELECT * FROM vehiculos WHERE placa = :placa");
        $stmt->execute([':placa' => $placa]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearVehiculo(array $data): bool
    {
        // Preparamos la consulta SQL para la tabla CLIENTES
        $sql = "INSERT INTO vehiculos (
                    id_cliente, placa, tipo, marca, modelo, 
                    color, observaciones
                ) VALUES (
                    :id_cliente, :placa, :tipo, :marca, :modelo, 
                    :color, :obs
                )";

        $stmt = $this->db->prepare($sql);


        // Ejecutamos pasando los datos exactos del formulario
        return $stmt->execute([
            ':id_cliente'=> $data['id_cliente'],
            ':placa'=> $data['placa'],
            ':tipo'    => $data['tipo'], // Puede ser null
            ':marca' => $data['marca'],
            ':modelo'  => $data['modelo'],
            ':color'   => $data['color'], // Viene 1 o 0 del Switch
            ':obs'      => $data['observaciones'] 
        ]);
    }

    public function eliminarVehiculo($id_vehiculo): bool
    {
        $sql = "DELETE FROM vehiculos WHERE id_vehiculo = :id_vehiculo";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_vehiculo' => $id_vehiculo]);
    }

     public function actualizarVehiculo(array $data): bool
    {
        // Solo actualizamos los campos que definimos como "Editables"
        $sql = "UPDATE vehiculos SET 
                    placa = :placa, 
                    tipo = :tipo, 
                    marca = :marca, 
                    modelo = :modelo, 
                    color = :color, 
                    observaciones = :observaciones 
                WHERE id_vehiculo = :id_vehiculo";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':placa' => $data['placa'],
            ':tipo' => $data['tipo'],
            ':marca' => $data['marca'],
            ':modelo' => $data['modelo'],
            ':color' => $data['color'],
            ':observaciones' => $data['observaciones'],
            ':id_vehiculo' => $data['id_vehiculo']
        ]);
    }

}
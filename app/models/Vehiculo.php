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
    

    
}
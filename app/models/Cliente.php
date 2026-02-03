<?php

class Cliente
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findByDni(string $dni)
    {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE dni = :dni");
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CAMBIO: Agregado campo 'apellidos'
    public function crearcliente(array $data): bool
    {
        $sql = "INSERT INTO clientes (
                    dni, nombres, apellidos, sexo, telefono, estado_whatsapp, puntos_acumulados, observaciones
                ) VALUES (
                    :dni, :nombres, :apellidos, :sexo, :telefono, :estado_whatsapp, :puntos, :observaciones
                )";

        $stmt = $this->db->prepare($sql);

        $puntos = empty($data['puntos']) ? 0 : $data['puntos'];
        $estadoWssp = isset($data['estado_whatsapp']) ? $data['estado_whatsapp'] : 1;

        return $stmt->execute([
            ':dni'             => $data['dni'],
            ':nombres'         => $data['nombres'],
            ':apellidos'       => $data['apellidos'], // Nuevo campo
            ':sexo'            => $data['sexo'] ?? null,
            ':telefono'        => $data['telefono'] ?? null,
            ':estado_whatsapp' => $estadoWssp,
            ':puntos'          => $puntos,
            ':observaciones'   => $data['observaciones'] ?? null
        ]);
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM clientes ORDER BY id_cliente DESC LIMIT 1000";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // CAMBIO: Agregado campo 'apellidos'
    public function actualizarCliente(array $data): bool
    {
        $sql = "UPDATE clientes SET 
                    nombres = :nombres,
                    apellidos = :apellidos,
                    sexo = :sexo,
                    telefono = :telefono,
                    estado_whatsapp = :estado_whatsapp,
                    observaciones = :observaciones
                WHERE id_cliente = :id_cliente";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombres'         => $data['nombres'],
            ':apellidos'       => $data['apellidos'], // Nuevo campo
            ':sexo'            => $data['sexo'] ?? null,
            ':telefono'        => $data['telefono'],
            ':estado_whatsapp' => $data['estado_whatsapp'],
            ':observaciones'   => $data['observaciones'] ?? null,
            ':id_cliente'      => $data['id_cliente']
        ]);
    }

    public function eliminarCliente($id_cliente): bool
    {
        try {
            $sql = "DELETE FROM clientes WHERE id_cliente = :id_cliente";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id_cliente' => $id_cliente]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>
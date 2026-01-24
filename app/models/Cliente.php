<?php

class Cliente
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Busca si el DNI ya está registrado para evitar duplicados
    public function findByDni(string $dni)
    {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE dni_ruc = :dni");
        $stmt->execute([':dni' => $dni]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crearcliente(array $data): bool
    {
        // Preparamos la consulta SQL para la tabla CLIENTES
        $sql = "INSERT INTO clientes (
                    dni_ruc, sexo, nombres, apellidos, email, 
                    telefono_principal, telefono_alternativo_w, 
                    estado_whatsapp, puntos, observaciones
                ) VALUES (
                    :dni, :sexo, :nombres, :apellidos, :email, 
                    :tel_prin, :tel_alt, 
                    :estado, :puntos, :obs
                )";

        $stmt = $this->db->prepare($sql);

        // Si los puntos vienen vacíos, ponemos 0
        $puntos = empty($data['puntos']) ? 0 : $data['puntos'];

        // Ejecutamos pasando los datos exactos del formulario
        return $stmt->execute([
            ':dni'      => $data['dni'],
            ':nombres'  => $data['nombres'],
            ':apellidos'=> $data['apellidos'],
            ':sexo'=> $data['sexo'],
            ':email'    => $data['email'] ?? null, // Puede ser null
            ':tel_prin' => $data['telefono_principal'],
            ':tel_alt'  => $data['telefono_alternativo_w'] ?? null,
            ':estado'   => $data['estado_whatsapp'], // Viene 1 o 0 del Switch
            ':puntos'   => $puntos,
            ':obs'      => $data['observaciones'] ?? null
        ]);
    }
   
    // ==========================================
    // TRAER TODOS LOS CLIENTES (Para la tabla)
    // ==========================================
    public function getAll(): array
    {
        // Traemos todos los clientes ordenados por el más reciente (los últimos registrados arriba)
        $sql = "SELECT * FROM clientes ORDER BY id_cliente";
        $stmt = $this->db->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // MÉTODO: EDITAR CLIENTE (Update)
    // ==========================================
    public function actualizarCliente(array $data): bool
    {
        // Solo actualizamos los campos que definimos como "Editables"
        $sql = "UPDATE clientes SET 
                    email = :email, 
                    telefono_principal = :telefono_principal, 
                    telefono_alternativo_w = :telefono_alternativo_w, 
                    estado_whatsapp = :estado_whatsapp, 
                    observaciones = :observaciones 
                WHERE id_cliente = :id_cliente";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':email' => $data['email'],
            ':telefono_principal' => $data['telefono_principal'],
            ':telefono_alternativo_w' => $data['telefono_alternativo_w'],
            ':estado_whatsapp' => $data['estado_whatsapp'],
            ':observaciones' => $data['observaciones'],
            ':id_cliente' => $data['id_cliente']
        ]);
    }

    // ==========================================
    // MÉTODO: ELIMINAR CLIENTE (Delete)
    // ==========================================
    public function eliminarCliente($id_cliente): bool
    {
        $sql = "DELETE FROM clientes WHERE id_cliente = :id_cliente";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id_cliente' => $id_cliente]);
    }
}
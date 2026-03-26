<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar usuario por EMAIL (Para Admins o recuperación)
     */
    public function findByEmail(string $email): ?array
    {
        // ADAPTACIÓN V3.2:
        // 1. Usamos 'password_hash' en lugar de 'password'.
        // 2. Usamos 'nombres' en lugar de 'nombre'.
        // 3. IDs explícitos (id_usuario, id_rol).
        $sql = "
            SELECT 
                u.id_usuario,
                u.nombres,
                u.dni,
                u.email,
                u.password_hash,
                u.avatar_url,
                u.id_rol,
                u.estado,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.email = :email 
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * NUEVO: Buscar usuario por DNI (Para Login de Empleados)
     * En Carwash XP, es más probable que el cajero use su DNI.
     */
    public function findByDni(string $dni): ?array
    {
        $sql = "
            SELECT 
                u.id_usuario,
                u.nombres,
                u.dni,
                u.email,
                u.password_hash,
                u.id_rol,
                u.estado,
                r.nombre AS rol_nombre
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.dni = :dni 
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dni' => $dni]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Crear nuevo usuario (Adaptado a V3.2)
     * Requiere array con: ['id_rol', 'dni', 'nombres', 'email', 'telefono', 'password']
     */
    public function create(array $data): bool
    {
        // 1. Encriptar contraseña
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // 2. Validar Roles (1: Admin, 2: Cajero, 3: Operario)
        $validRoles = [1, 2, 3];
        // Si el rol no es válido, por seguridad lo forzamos a 3 (Operario - menor privilegio)
        $id_rol = in_array((int)$data['id_rol'], $validRoles) ? $data['id_rol'] : 3;

        // 3. Insertar usando las columnas de la V3.2
        // Nota: 'estado' es 1 por defecto en la BD, 'avatar_url' es default.png
        $sql = "INSERT INTO usuarios (id_rol, dni, nombres, email, telefono, password_hash) 
                VALUES (:id_rol, :dni, :nombres, :email, :telefono, :password_hash)";

        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute([
                ':id_rol'        => $id_rol,
                ':dni'           => $data['dni'],      // OBLIGATORIO EN V3.2
                ':nombres'       => $data['nombres'],  // Ojo: es 'nombres', no 'name'
                ':email'         => $data['email'] ?? null, // Puede ser null para operarios
                ':telefono'      => $data['telefono'] ?? null,
                ':password_hash' => $hash
            ]);
        } catch (PDOException $e) {
            // Manejo básico de error (ej: DNI duplicado)
            // En producción podrías loguear el error: error_log($e->getMessage());
            return false;
        }
    }
}

<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Buscar usuario por email (Solo usuarios activos)
     * Retorna array con datos del usuario o null si no existe/está inactivo.
     */
    public function findByEmail(string $email): ?array
    {
        // IMPORTANTE: Agregamos 'AND estado = 1'
        // Así, los usuarios desactivados no pasan el login.
        $sql = "
            SELECT 
                u.id_usuario,
                u.nombre,
                u.email,
                u.password,
                u.id_rol,
                r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r ON u.id_rol = r.id_rol
            WHERE u.email = :email 
            AND u.estado = 1
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Crear nuevo usuario en la base de datos
     */
    public function create(array $data): bool
    {
        // 1. Encriptar la contraseña de forma segura
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        // 2. Validar el Rol para evitar errores de ENUM en MySQL
        // Tu base de datos solo acepta 'admin' o 'operador'.
        $validRoles = ['1', '2'];
        
        // Si el rol que llega no es válido, lo forzamos a 'operador' por seguridad
        $role = in_array($data['role'] ?? '', $validRoles) ? $data['role'] : '2';

        // 3. Preparar la consulta
        // Insertamos active = 1 por defecto explícitamente
        $sql = "INSERT INTO usuarios (nombre, email, password, id_rol ) 
                VALUES (:name, :email, :password, :role)";

        $stmt = $this->db->prepare($sql);

        // 4. Ejecutar
        return $stmt->execute([
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => $hash,
            ':role'     => $role
        ]);
    }
}
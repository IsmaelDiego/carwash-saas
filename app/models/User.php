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
        // IMPORTANTE: Agregamos 'AND active = 1'
        // Así, los usuarios desactivados no pasan el login.
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND active = 1 LIMIT 1");
        $stmt->execute([$email]);

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
        $validRoles = ['admin', 'operador'];
        
        // Si el rol que llega no es válido, lo forzamos a 'operador' por seguridad
        $role = in_array($data['role'] ?? '', $validRoles) ? $data['role'] : 'operador';

        // 3. Preparar la consulta
        // Insertamos active = 1 por defecto explícitamente
        $sql = "INSERT INTO users (name, email, password, role, active) 
                VALUES (:name, :email, :password, :role, 1)";

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
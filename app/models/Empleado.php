<?php

class Empleado {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // =========================================================
    // . GET BY ID
    // =========================================================
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 1. LISTAR TODOS (Con nombre del Rol)
    // =========================================================
    public function getAll() {
        try {
            $sql = "SELECT 
                        u.id_usuario, u.id_rol, u.dni, u.nombres, u.email, 
                        u.telefono, u.avatar_url, u.estado, u.fecha_creacion,
                        r.nombre AS rol_nombre
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    ORDER BY u.estado DESC, u.id_usuario DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // =========================================================
    // 2. OBTENER ROLES (Para el Select del formulario)
    // =========================================================
    public function getRoles() {
        try {
            return $this->pdo->query("SELECT * FROM roles ORDER BY id_rol ASC")->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // =========================================================
    // 3. VALIDAR DNI DUPLICADO
    // =========================================================
    public function existeDni($dni, $id_usuario = null) {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE dni = :dni";
        $params = [':dni' => $dni];

        if ($id_usuario) {
            $sql .= " AND id_usuario != :id";
            $params[':id'] = $id_usuario;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // =========================================================
    // 4. VALIDAR EMAIL DUPLICADO
    // =========================================================
    public function existeEmail($email, $id_usuario = null) {
        if (empty($email)) return false;
        
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
        $params = [':email' => $email];

        if ($id_usuario) {
            $sql .= " AND id_usuario != :id";
            $params[':id'] = $id_usuario;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // =========================================================
    // 5. REGISTRAR EMPLEADO
    // =========================================================
    public function registrar($data) {
        try {
            $hash = password_hash($data['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (id_rol, dni, nombres, email, telefono, password_hash, estado) 
                    VALUES (:id_rol, :dni, :nombres, :email, :telefono, :password_hash, 1)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_rol'        => $data['id_rol'],
                ':dni'           => trim($data['dni']),
                ':nombres'       => trim($data['nombres']),
                ':email'         => !empty($data['email']) ? trim($data['email']) : null,
                ':telefono'      => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':password_hash' => $hash
            ]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 6. EDITAR EMPLEADO (Sin cambiar contraseña)
    // =========================================================
    public function editar($data) {
        try {
            $sql = "UPDATE usuarios SET 
                        id_rol = :id_rol,
                        nombres = :nombres,
                        email = :email,
                        telefono = :telefono
                    WHERE id_usuario = :id_usuario";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_rol'     => $data['id_rol'],
                ':nombres'    => trim($data['nombres']),
                ':email'      => !empty($data['email']) ? trim($data['email']) : null,
                ':telefono'   => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':id_usuario' => $data['id_usuario']
            ]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 7. CAMBIAR CONTRASEÑA
    // =========================================================
    public function cambiarPassword($id_usuario, $nueva_password) {
        try {
            $hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET password_hash = :hash WHERE id_usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':hash' => $hash, ':id' => $id_usuario]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 8. CAMBIAR ESTADO (Activar/Desactivar)
    // =========================================================
    public function cambiarEstado($id, $estado) {
        try {
            $sql = "UPDATE usuarios SET estado = :estado WHERE id_usuario = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 9. ELIMINAR EMPLEADO
    // =========================================================
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 10. CONTADORES PARA ESTADÍSTICAS
    // =========================================================
    public function getEstadisticas() {
        try {
            $stats = [];
            
            // Total empleados activos
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 1");
            $stats['activos'] = $stmt->fetch()['total'] ?? 0;

            // Total empleados inactivos
            $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE estado = 0");
            $stats['inactivos'] = $stmt->fetch()['total'] ?? 0;

            // Conteo por rol
            $sql = "SELECT r.nombre, COUNT(u.id_usuario) as cantidad 
                    FROM roles r 
                    LEFT JOIN usuarios u ON r.id_rol = u.id_rol AND u.estado = 1
                    GROUP BY r.id_rol, r.nombre 
                    ORDER BY r.id_rol ASC";
            $stats['por_rol'] = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch (Exception $e) { return []; }
    }
}

<?php

class PermisoEmpleado {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        try {
            $sql = "SELECT p.*, u.nombres as empleado, a.nombres as admin_registrador
                    FROM permisos_empleados p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    LEFT JOIN usuarios a ON p.id_admin_registrador = a.id_usuario
                    ORDER BY p.fecha_creacion DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getByUserId($id_usuario) {
        try {
            $sql = "SELECT * FROM permisos_empleados WHERE id_usuario = :id_usuario ORDER BY fecha_inicio DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function registrar($data) {
        try {
            $sql = "INSERT INTO permisos_empleados (id_usuario, tipo, fecha_inicio, fecha_fin, motivo, estado, id_admin_registrador) 
                    VALUES (:id_usuario, :tipo, :fecha_inicio, :fecha_fin, :motivo, :estado, :id_admin_registrador)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_usuario' => $data['id_usuario'],
                ':tipo' => $data['tipo'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin' => $data['fecha_fin'],
                ':motivo' => $data['motivo'] ?? null,
                ':estado' => $data['estado'] ?? 'PENDIENTE',
                ':id_admin_registrador' => $data['id_admin_registrador'] ?? 0 // Si el empleado lo pide, será 0 el admin (luego se aprueba)
            ]);
        } catch (Exception $e) { return false; }
    }

    public function cambiarEstado($id_permiso, $estado, $id_admin_registrador) {
        try {
            $sql = "UPDATE permisos_empleados SET estado = :estado, id_admin_registrador = :id_admin WHERE id_permiso = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':id_admin' => $id_admin_registrador, ':id' => $id_permiso]);
        } catch (Exception $e) { return false; }
    }
}

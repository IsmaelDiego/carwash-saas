<?php

class PermisoEmpleado {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($mes_anio = null) {
        try {
            $sql = "SELECT p.*, u.nombres as empleado, a.nombres as admin_registrador
                    FROM permisos_empleados p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    LEFT JOIN usuarios a ON p.id_admin_registrador = a.id_usuario";
            
            $params = [];
            if ($mes_anio) {
                // $mes_anio expects 'YYYY-MM'
                $sql .= " WHERE DATE_FORMAT(p.fecha_inicio, '%Y-%m') = :mes_anio ";
                $params[':mes_anio'] = $mes_anio;
            }
            
            $sql .= " ORDER BY p.fecha_creacion DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getEstadisticas() {
        try {
            $stats = [];
            $stats['total'] = $this->pdo->query("SELECT COUNT(*) as total FROM permisos_empleados")->fetch()['total'];
            $stats['aprobados'] = $this->pdo->query("SELECT COUNT(*) as total FROM permisos_empleados WHERE estado = 'APROBADO'")->fetch()['total'];
            $stats['pendientes'] = $this->pdo->query("SELECT COUNT(*) as total FROM permisos_empleados WHERE estado = 'PENDIENTE'")->fetch()['total'];
            $stats['rechazados'] = $this->pdo->query("SELECT COUNT(*) as total FROM permisos_empleados WHERE estado = 'RECHAZADO'")->fetch()['total'];
            return $stats;
        } catch (Exception $e) { return []; }
    }
}

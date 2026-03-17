<?php

class PagoEmpleado {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        try {
            $sql = "SELECT p.*, u.nombres as empleado, a.nombres as admin_registrador
                    FROM pagos_empleados p
                    JOIN usuarios u ON p.id_usuario = u.id_usuario
                    JOIN usuarios a ON p.id_admin_registrador = a.id_usuario
                    ORDER BY p.fecha_creacion DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function getByUserId($id_usuario) {
        try {
            $sql = "SELECT * FROM pagos_empleados WHERE id_usuario = :id_usuario ORDER BY fecha_programada DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_usuario' => $id_usuario]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    public function registrar($data) {
        try {
            $sql = "INSERT INTO pagos_empleados (id_usuario, tipo, monto, periodo, estado, fecha_programada, fecha_pago, observaciones, id_admin_registrador) 
                    VALUES (:id_usuario, :tipo, :monto, :periodo, :estado, :fecha_programada, :fecha_pago, :observaciones, :id_admin_registrador)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':id_usuario' => $data['id_usuario'],
                ':tipo' => $data['tipo'],
                ':monto' => $data['monto'],
                ':periodo' => $data['periodo'] ?? null,
                ':estado' => $data['estado'],
                ':fecha_programada' => $data['fecha_programada'],
                ':fecha_pago' => (!empty($data['fecha_pago']) && $data['estado'] == 'PAGADO') ? $data['fecha_pago'] : null,
                ':observaciones' => $data['observaciones'] ?? null,
                ':id_admin_registrador' => $data['id_admin_registrador']
            ]);
        } catch (Exception $e) { return false; }
    }

    public function cambiarEstado($id_pago, $estado) {
        try {
            $fecha_pago = ($estado == 'PAGADO') ? date('Y-m-d H:i:s') : null;
            $sql = "UPDATE pagos_empleados SET estado = :estado, fecha_pago = :fecha_pago WHERE id_pago = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':fecha_pago' => $fecha_pago, ':id' => $id_pago]);
        } catch (Exception $e) { return false; }
    }

    public function getEstadisticas() {
        try {
            $stats = [];
            $stats['total'] = $this->pdo->query("SELECT COUNT(*) as total FROM pagos_empleados")->fetch()['total'];
            $stats['pagados'] = $this->pdo->query("SELECT COUNT(*) as total FROM pagos_empleados WHERE estado = 'PAGADO'")->fetch()['total'];
            $stats['pendientes'] = $this->pdo->query("SELECT COUNT(*) as total FROM pagos_empleados WHERE estado = 'PENDIENTE'")->fetch()['total'];
            $stats['retrasados'] = $this->pdo->query("SELECT COUNT(*) as total FROM pagos_empleados WHERE estado = 'RETRASADO'")->fetch()['total'];
            $stats['monto_total'] = $this->pdo->query("SELECT COALESCE(SUM(monto), 0) as total FROM pagos_empleados WHERE estado = 'PAGADO'")->fetch()['total'];
            return $stats;
        } catch (Exception $e) { return []; }
    }
}

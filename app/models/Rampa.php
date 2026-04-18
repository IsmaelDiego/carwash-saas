<?php

class Rampa {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ════════════════════════════════════════
    // OBTENER TODAS LAS RAMPAS CON OPERADOR
    // ════════════════════════════════════════
    public function getAll() {
        $sql = "SELECT r.*,
                    u.nombres AS operador_nombre, u.dni AS operador_dni,
                    (SELECT COUNT(*) FROM ordenes o WHERE o.id_rampa = r.id_rampa AND o.estado = 'EN_PROCESO') AS orden_activa
                FROM rampas r
                LEFT JOIN usuarios u ON r.id_operador = u.id_usuario
                ORDER BY r.numero ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // OBTENER RAMPAS ACTIVAS (para contar cuántas aceptan órdenes)
    // ════════════════════════════════════════
    public function getRampasActivas() {
        $stmt = $this->pdo->query("SELECT * FROM rampas WHERE estado = 'ACTIVA' ORDER BY numero ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar rampas habilitadas
    public function contarActivas() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM rampas WHERE estado = 'ACTIVA'");
        return (int)$stmt->fetch()['total'];
    }

    // ════════════════════════════════════════
    // OBTENER RAMPA POR ID
    // ════════════════════════════════════════
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM rampas WHERE id_rampa = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // ACTUALIZAR OPERADOR Y ESTADO DE RAMPA
    // ════════════════════════════════════════
    public function actualizarEstado($id_rampa, $estado, $id_operador = null, $motivo = null) {
        $sql = "UPDATE rampas SET estado = :estado, id_operador = :operador, motivo_estado = :motivo WHERE id_rampa = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':estado'   => $estado,
            ':operador' => $id_operador,
            ':motivo'   => $motivo,
            ':id'       => $id_rampa
        ]);
    }

    // ════════════════════════════════════════
    // CREAR RAMPAS EN LOTE (cuando admin cambia num_rampas)
    // ════════════════════════════════════════
    public function sincronizarRampas($num_rampas) {
        // Obtener cuántas rampas hay
        $actual = (int)$this->pdo->query("SELECT COUNT(*) as t FROM rampas")->fetch()['t'];

        if ($num_rampas > $actual) {
            // Agregar rampas
            for ($i = $actual + 1; $i <= $num_rampas; $i++) {
                $this->pdo->prepare("INSERT INTO rampas (numero, nombre, estado) VALUES (?, ?, 'INACTIVA')")
                    ->execute([$i, "Rampa $i"]);
            }
        } elseif ($num_rampas < $actual) {
            // Eliminar las últimas (solo si no tienen orden activa ni operador)
            for ($i = $actual; $i > $num_rampas; $i--) {
                $this->pdo->prepare(
                    "DELETE FROM rampas WHERE numero = ? AND (id_operador IS NULL) 
                     AND NOT EXISTS (SELECT 1 FROM ordenes WHERE id_rampa = rampas.id_rampa AND estado = 'EN_PROCESO')"
                )->execute([$i]);
            }
        }
        return true;
    }

    // ════════════════════════════════════════
    // OBTENER PRIMERA RAMPA ACTIVA LIBRE
    // ════════════════════════════════════════
    public function getPrimeraRampaLibre() {
        $sql = "SELECT r.id_rampa FROM rampas r
                WHERE r.estado = 'ACTIVA'
                AND NOT EXISTS (
                    SELECT 1 FROM ordenes o 
                    WHERE o.id_rampa = r.id_rampa AND o.estado = 'EN_PROCESO'
                )
                ORDER BY r.numero ASC
                LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id_rampa'] : null;
    }

    // ════════════════════════════════════════
    // CUÁNTAS RAMPAS ACTIVAS CON ORDEN EN PROCESO
    // ════════════════════════════════════════
    public function contarRampasOcupadas() {
        $stmt = $this->pdo->query(
            "SELECT COUNT(DISTINCT o.id_rampa) as total FROM ordenes o 
             INNER JOIN rampas r ON o.id_rampa = r.id_rampa
             WHERE o.estado = 'EN_PROCESO'"
        );
        return (int)$stmt->fetch()['total'];
    }
}

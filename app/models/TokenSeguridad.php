<?php

class TokenSeguridad {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Generar un nuevo token de seguridad
     */
    public function generar($id_admin, $motivo, $minutos_validez = 60) {
        try {
            $codigo = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $expiracion = date('Y-m-d H:i:s', strtotime("+{$minutos_validez} minutes"));

            $sql = "INSERT INTO tokens_seguridad (codigo, id_usuario_generador, fecha_expiracion, motivo_generacion) 
                    VALUES (:codigo, :id_usuario, :fecha_exp, :motivo)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':codigo'     => $codigo,
                ':id_usuario' => $id_admin,
                ':fecha_exp'  => $expiracion,
                ':motivo'     => trim($motivo)
            ]);

            return [
                'id_token'  => $this->pdo->lastInsertId(),
                'codigo'    => $codigo,
                'expira'    => $expiracion,
                'motivo'    => $motivo
            ];
        } catch (Exception $e) { return false; }
    }

    /**
     * Validar un token: Existe, no expiró, no fue usado
     */
    public function validar($codigo) {
        $sql = "SELECT * FROM tokens_seguridad 
                WHERE codigo = :codigo 
                AND usado = 0 
                AND fecha_expiracion > NOW()
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codigo' => strtoupper(trim($codigo))]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Marcar un token como usado
     */
    public function marcarUsado($id_token) {
        $sql = "UPDATE tokens_seguridad SET usado = 1 WHERE id_token = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id_token]);
    }

    /**
     * Listar todos los tokens (Admin)
     */
    public function getAll() {
        $sql = "SELECT t.*, u.nombres AS generado_por 
                FROM tokens_seguridad t 
                INNER JOIN usuarios u ON t.id_usuario_generador = u.id_usuario 
                ORDER BY t.id_token DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar tokens activos (no usados, no expirados)
     */
    public function contarActivos() {
        $sql = "SELECT COUNT(*) as total FROM tokens_seguridad WHERE usado = 0 AND fecha_expiracion > NOW()";
        return $this->pdo->query($sql)->fetch()['total'] ?? 0;
    }
}

<?php


class Promocion {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // LISTAR TODAS (Para la tabla inferior)
    public function getAll() {
        try {
            // Auto desactivar caducadas
            $this->pdo->query("UPDATE promociones SET estado = 0 WHERE fecha_fin < CURDATE() AND estado = 1");
            $stmt = $this->pdo->query("SELECT p.*, (fecha_fin < CURDATE()) AS es_caducada FROM promociones p ORDER BY p.id_promocion DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // OBTENER RECIENTES (Para las Cards Superiores - Máx 4)
    public function getRecientes() {
        try {
            // Auto desactivar caducadas
            $this->pdo->query("UPDATE promociones SET estado = 0 WHERE fecha_fin < CURDATE() AND estado = 1");
            // Priorizamos las Activas (estado 1) y luego por fecha inicio reciente
            $sql = "SELECT p.*, (fecha_fin < CURDATE()) AS es_caducada FROM promociones p ORDER BY p.estado DESC, p.fecha_inicio DESC LIMIT 4";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // OBTENER CLIENTES CON WHATSAPP (Para el envío masivo)
    // OBTENER CLIENTES PARA DIFUSIÓN (Solo con WhatsApp Activo)
    public function getClientesWhatsApp() {
        try {
            // FILTRO: telefono no vacío Y estado_whatsapp = 1
            $sql = "SELECT id_cliente, nombres, telefono 
                    FROM clientes 
                    WHERE telefono IS NOT NULL 
                    AND telefono != '' 
                    AND estado_whatsapp = 1"; 
            
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // CRUD BÁSICO (Igual que antes)
    public function registrar($data) {
        try {
            $sql = "INSERT INTO promociones (nombre, tipo_descuento, valor, fecha_inicio, fecha_fin, solo_una_vez_por_cliente, mensaje_whatsapp, estado) 
                    VALUES (:nombre, :tipo, :valor, :inicio, :fin, :solo_una_vez, :mensaje, 1)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre' => trim($data['nombre']),
                ':tipo'   => $data['tipo_descuento'],
                ':valor'  => $data['valor'],
                ':inicio' => $data['fecha_inicio'],
                ':fin'    => $data['fecha_fin'],
                ':solo_una_vez' => isset($data['solo_una_vez_por_cliente']) && $data['solo_una_vez_por_cliente'] == 1 ? 1 : 0,
                ':mensaje'=> $data['mensaje_whatsapp'] ?? ''
            ]);
        } catch (Exception $e) { return false; }
    }

    public function editar($data) {
        try {
            $sql = "UPDATE promociones SET 
                        nombre = :nombre, 
                        tipo_descuento = :tipo, 
                        valor = :valor, 
                        fecha_inicio = :inicio, 
                        fecha_fin = :fin, 
                        solo_una_vez_por_cliente = :solo_una_vez, 
                        mensaje_whatsapp = :mensaje
                    WHERE id_promocion = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre' => trim($data['nombre']),
                ':tipo'   => $data['tipo_descuento'],
                ':valor'  => $data['valor'],
                ':inicio' => $data['fecha_inicio'],
                ':fin'    => $data['fecha_fin'],
                ':solo_una_vez' => isset($data['solo_una_vez_por_cliente']) && $data['solo_una_vez_por_cliente'] == 1 ? 1 : 0,
                ':mensaje'=> $data['mensaje_whatsapp'],
                ':id'     => $data['id_promocion']
            ]);
        } catch (Exception $e) { return false; }
    }

    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM promociones WHERE id_promocion = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    public function cambiarEstado($id, $estado) {
        try {
            if ($estado == 1) {
                $sql = "UPDATE promociones SET estado = :estado WHERE id_promocion = :id AND fecha_fin >= CURDATE()";
            } else {
                $sql = "UPDATE promociones SET estado = :estado WHERE id_promocion = :id";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':estado' => $estado, ':id' => $id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) { return false; }
    }
}
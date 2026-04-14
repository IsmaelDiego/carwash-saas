<?php

class Servicio {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // LISTAR TODOS
    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM servicios ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // VALIDAR NOMBRE DUPLICADO
    public function existeNombre($nombre, $id_servicio = null) {
        $sql = "SELECT COUNT(*) FROM servicios WHERE nombre = :nombre";
        $params = [':nombre' => $nombre];
        
        if ($id_servicio) {
            $sql .= " AND id_servicio != :id";
            $params[':id'] = $id_servicio;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // REGISTRAR
    public function registrar($data) {
        try {
            $sql = "INSERT INTO servicios (nombre, precio_base, tiempo_estimado, acumula_puntos, permite_canje, estado) 
                    VALUES (:nombre, :precio_base, :tiempo, :acumula, :canje, 1)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre'      => trim($data['nombre']),
                ':precio_base' => $data['precio_base'],
                ':tiempo'      => isset($data['tiempo_estimado']) && $data['tiempo_estimado'] !== '' ? $data['tiempo_estimado'] : 0,
                // CORRECCIÓN: Verificamos si VALE 1, no solo si existe
                ':acumula'     => (isset($data['acumula_puntos']) && $data['acumula_puntos'] == 1) ? 1 : 0,
                ':canje'       => (isset($data['permite_canje']) && $data['permite_canje'] == 1) ? 1 : 0
            ]);
        } catch (Exception $e) { return false; }
    }

    // EDITAR (Usando id_servicio)
    public function editar($data) {
        try {
            $sql = "UPDATE servicios SET 
                        nombre = :nombre, 
                        precio_base = :precio_base, 
                        tiempo_estimado = :tiempo,
                        acumula_puntos = :acumula, 
                        permite_canje = :canje 
                    WHERE id_servicio = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':nombre'      => trim($data['nombre']),
                ':precio_base' => $data['precio_base'],
                ':tiempo'      => isset($data['tiempo_estimado']) && $data['tiempo_estimado'] !== '' ? $data['tiempo_estimado'] : 0,
                // CORRECCIÓN: Verificamos si VALE 1
                ':acumula'     => (isset($data['acumula_puntos']) && $data['acumula_puntos'] == 1) ? 1 : 0,
                ':canje'       => (isset($data['permite_canje']) && $data['permite_canje'] == 1) ? 1 : 0,
                ':id'          => $data['id_servicio']
            ]);
        } catch (Exception $e) { return false; }
    }

    // ELIMINAR (Usando id_servicio)
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM servicios WHERE id_servicio = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    // CAMBIAR ESTADO
    public function cambiarEstado($id, $estado) {
        try {
            $sql = "UPDATE servicios SET estado = :estado WHERE id_servicio = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':id' => $id]);
        } catch (Exception $e) { return false; }
    }
}
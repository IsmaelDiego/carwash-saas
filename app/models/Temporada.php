<?php


class Temporada {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // =========================================================
    // 1. DASHBOARD: DATOS PARA LAS TARJETAS (Arriba)
    // =========================================================
    public function getDashboardData() {
        try {
            // A. Temporada ACTUAL (Solo puede haber una con estado 1)
            $stmt = $this->pdo->query("SELECT * FROM temporadas WHERE estado = 1 LIMIT 1");
            $actual = $stmt->fetch(PDO::FETCH_ASSOC);

            // B. Temporada ANTERIOR (La última cerrada, estado 0)
            // Buscamos la que tenga la fecha_fin más reciente
            $sqlAnt = "SELECT * FROM temporadas WHERE estado = 0 ORDER BY fecha_fin DESC LIMIT 1";
            $anterior = $this->pdo->query($sqlAnt)->fetch(PDO::FETCH_ASSOC);

            // C. Helper para calcular estadísticas
            $getStats = function($id) {
                if(!$id) return ['gen' => 0, 'red' => 0];
                
                // Gen: Suma de la cantidad de servicios que acumulan puntos en órdenes finalizadas de la temporada
                // Red: Cantidad de órdenes finalizadas en la temporada que tuvieron un canje (descuento_puntos > 0)
                $sql = "SELECT 
                            (SELECT COALESCE(SUM(d.cantidad), 0) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio INNER JOIN ordenes o ON d.id_orden = o.id_orden WHERE o.id_temporada = :id_gen AND o.estado = 'FINALIZADO' AND s.acumula_puntos = 1 AND o.id_cliente != 1) as gen, 
                            (SELECT COALESCE(COUNT(id_orden), 0) FROM ordenes WHERE id_temporada = :id_red AND estado = 'FINALIZADO' AND descuento_puntos > 0 AND id_cliente != 1) as red";
                        
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':id_gen' => $id, ':id_red' => $id]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            };

            return [
                'actual' => $actual,
                'anterior' => $anterior,
                'stats_act' => $getStats($actual['id_temporada'] ?? null),
                'stats_ant' => $getStats($anterior['id_temporada'] ?? null)
            ];
        } catch (Exception $e) { return null; }
    }

    // =========================================================
    // 2. LISTAR HISTORIAL (Para la tabla de abajo)
    // =========================================================
    public function getAll() {
        try {
            $sql = "SELECT t.*, 
                    (SELECT COALESCE(SUM(d.cantidad),0) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio INNER JOIN ordenes o ON d.id_orden = o.id_orden WHERE o.id_temporada = t.id_temporada AND o.estado = 'FINALIZADO' AND s.acumula_puntos = 1 AND o.id_cliente != 1) as puntos_gen,
                    (SELECT COALESCE(COUNT(o.id_orden),0) FROM ordenes o WHERE o.id_temporada = t.id_temporada AND o.estado = 'FINALIZADO' AND o.descuento_puntos > 0 AND o.id_cliente != 1) as puntos_red
                    FROM temporadas t 
                    ORDER BY t.fecha_inicio DESC";
            return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) { return []; }
    }

    // =========================================================
    // 3. VALIDACIÓN: ¿HAY TEMPORADA ACTIVA?
    // =========================================================
    public function hayTemporadaActiva() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM temporadas WHERE estado = 1");
        return $stmt->fetchColumn() > 0;
    }

    // =========================================================
    // 4. CRUD BÁSICO
    // =========================================================
    
    // REGISTRAR
    public function registrar($data) {
        try {
            // Validación de seguridad backend
            if($this->hayTemporadaActiva()) return false;

            $sql = "INSERT INTO temporadas (nombre, fecha_inicio, fecha_fin, estado) 
                    VALUES (:n, :i, NULL, 1)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':n' => $data['nombre'], 
                ':i' => $data['fecha_inicio']
            ]);
        } catch (Exception $e) { return false; }
    }

    // EDITAR
    public function editar($data) {
        try {
            $sql = "UPDATE temporadas SET nombre = :n, fecha_inicio = :i WHERE id_temporada = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':n' => $data['nombre'], 
                ':i' => $data['fecha_inicio'], 
                ':id' => $data['id_temporada']
            ]);
        } catch (Exception $e) { return false; }
    }

    // ELIMINAR
    public function eliminar($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM temporadas WHERE id_temporada = :id");
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    // =========================================================
    // 5. FUNCIONES DE ESTADO (LAS QUE FALTABAN)
    // =========================================================

    // CERRAR TEMPORADA (Finalizar)
    // Pone estado 0 y fecha_fin = HOY
    public function cerrarTemporada($id) {
        try {
            $sql = "UPDATE temporadas SET estado = 0, fecha_fin = CURDATE() WHERE id_temporada = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (Exception $e) { return false; }
    }

    // CAMBIAR ESTADO (Genérico)
    public function cambiarEstado($id, $estado) {
        try {
            // Si intentamos activar una (estado 1), desactivamos todas las demás primero
            // para evitar tener dos temporadas activas al mismo tiempo.
            if($estado == 1) {
                $this->pdo->query("UPDATE temporadas SET estado = 0");
            }
            
            $sql = "UPDATE temporadas SET estado = :estado WHERE id_temporada = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':estado' => $estado, ':id' => $id]);
        } catch (Exception $e) { return false; }
    }
}
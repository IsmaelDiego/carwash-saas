<?php

require_once 'config/database.php';

class CajaSesion {
    private $conn;

    public function __construct($pdo = null) {
        if ($pdo) {
            $this->conn = $pdo;
        } else {
            global $pdo;
            $this->conn = $pdo;
        }
    }

    /**
     * Verifica si un usuario (cajero u operador) tiene una caja abierta
     * @param int $id_usuario
     * @return array|false Retorna la sesión actual si está abierta, falso si no.
     */
    public function getCajaAbierta($id_usuario) {
        $query = "SELECT * FROM caja_sesiones 
                  WHERE id_usuario = :id_usuario AND estado = 'ABIERTA' 
                  ORDER BY fecha_apertura DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Abre una nueva sesión de caja
     * @param int $id_usuario
     * @param float $monto_apertura
     * @param string|null $motivo
     * @param int|null $id_rol
     * @return int|false Retorna el ID de la sesión creada o false en caso de error
     */
    public function abrirCaja($id_usuario, $monto_apertura, $motivo = null, $id_rol = null) {
        // Validación de doble caja de seguridad
        if ($this->getCajaAbierta($id_usuario)) {
            return false; // Ya tiene una caja abierta
        }

        $query = "INSERT INTO caja_sesiones (id_usuario, monto_apertura, monto_esperado, estado, motivo_apertura, id_rol_apertura) 
                  VALUES (:id_usuario, :monto_apertura, :monto_esperado, 'ABIERTA', :motivo, :rol)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindParam(':monto_apertura', $monto_apertura);
        $stmt->bindParam(':monto_esperado', $monto_apertura);
        $stmt->bindParam(':motivo', $motivo);
        $stmt->bindParam(':rol', $id_rol, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cierra la caja verificando las diferencias
     * @param int $id_sesion
     * @param float $monto_cierre_real
     * @return bool
     */
    public function cerrarCaja($id_sesion, $monto_cierre_real) {
        // Primero obtener el monto esperado actualizado sumando las ventas de esta sesión
        $total_ventas = $this->calcularTotalVentasCaja($id_sesion);
        
        $query_sesion = "SELECT monto_apertura FROM caja_sesiones WHERE id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($query_sesion);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        $sesion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $monto_esperado = $sesion['monto_apertura'] + $total_ventas;
        $diferencia = $monto_cierre_real - $monto_esperado;

        $update = "UPDATE caja_sesiones SET 
                   monto_cierre_real = :monto_real,
                   monto_esperado = :monto_esperado,
                   diferencia = :diferencia,
                   estado = 'CERRADA',
                   fecha_cierre = CURRENT_TIMESTAMP
                   WHERE id_sesion = :id_sesion AND estado = 'ABIERTA'";
        
        $stmtUpd = $this->conn->prepare($update);
        $stmtUpd->bindParam(':monto_real', $monto_cierre_real);
        $stmtUpd->bindParam(':monto_esperado', $monto_esperado);
        $stmtUpd->bindParam(':diferencia', $diferencia);
        $stmtUpd->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        
        return $stmtUpd->execute();
    }

    /**
     * Calcula total vendido en base a los pagos de las ordenes atadas a esta sesión
     * @param int $id_caja_sesion
     */
    public function calcularTotalVentasCaja($id_caja_sesion) {
        $query = "SELECT IFNULL(SUM(po.monto), 0) as total_ventas 
                  FROM pagos_orden po
                  INNER JOIN ordenes o ON po.id_orden = o.id_orden
                  WHERE o.id_caja_sesion = :id_caja_sesion AND o.estado = 'FINALIZADO'";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_caja_sesion', $id_caja_sesion, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (float) $result['total_ventas'];
    }

    /**
     * Resumen de la caja activa para mostrar en el Dashboard
     */
    public function getResumenCaja($id_sesion) {
        // Métodos de pago desglose
        $query = "SELECT po.metodo_pago, SUM(po.monto) as total
                  FROM pagos_orden po
                  INNER JOIN ordenes o ON po.id_orden = o.id_orden
                  WHERE o.id_caja_sesion = :id_sesion AND o.estado = 'FINALIZADO'
                  GROUP BY po.metodo_pago";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el listado de arqueos/sesiones por mes y año
     */
    public function getArqueosPorMes($month, $year) {
        $query = "SELECT cs.*, u.nombres as cajero_nombre,
                    (SELECT COALESCE(SUM(po.monto), 0) 
                     FROM pagos_orden po 
                     JOIN ordenes o ON po.id_orden = o.id_orden 
                     WHERE o.id_caja_sesion = cs.id_sesion AND o.estado = 'FINALIZADO') as recaudado_acumulado
                FROM caja_sesiones cs
                LEFT JOIN usuarios u ON cs.id_usuario = u.id_usuario
                WHERE MONTH(cs.fecha_apertura) = :m AND YEAR(cs.fecha_apertura) = :y
                ORDER BY cs.fecha_apertura ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':m', $month, PDO::PARAM_INT);
        $stmt->bindParam(':y', $year, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene datos básicos de una sesión por ID
     */
    public function getSesionInfo($id_sesion) {
        $query = "SELECT cs.*, u.nombres as cajero 
                  FROM caja_sesiones cs 
                  JOIN usuarios u ON cs.id_usuario = u.id_usuario 
                  WHERE cs.id_sesion = :id_sesion";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen de productos de tienda vendidos en una sesión específica
     */
    public function getProductosVendidos($id_sesion) {
        $query = "SELECT p.nombre, SUM(do.cantidad) as total_cant, SUM(do.subtotal) as total_monto
                  FROM detalle_orden do
                  INNER JOIN ordenes o ON do.id_orden = o.id_orden
                  INNER JOIN productos p ON do.id_producto = p.id_producto
                  WHERE o.id_caja_sesion = :id_sesion AND o.estado = 'FINALIZADO'
                  GROUP BY p.id_producto";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_sesion', $id_sesion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

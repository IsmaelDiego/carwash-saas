<?php

class Orden {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ════════════════════════════════════════
    // LISTAR ÓRDENES CON DETALLES
    // ════════════════════════════════════════
    public function getAll($estado = null) {
        $sql = "SELECT o.*, 
                    c.nombres AS cliente_nombres, c.apellidos AS cliente_apellidos,
                    v.placa, cat.nombre AS categoria_vehiculo,
                    u.nombres AS creador_nombre,
                    t.nombre AS temporada_nombre
                FROM ordenes o
                LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
                LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
                LEFT JOIN usuarios u ON o.id_usuario_creador = u.id_usuario
                LEFT JOIN temporadas t ON o.id_temporada = t.id_temporada";
        
        if ($estado) {
            $sql .= " WHERE o.estado = :estado";
        }
        $sql .= " ORDER BY o.id_orden DESC";

        if ($estado) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':estado' => $estado]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // LISTAR ÓRDENES ACTIVAS CAJERO (Con Detalle de Productos/Servicios)
    // ════════════════════════════════════════
    public function getActivasCajero($excluirEnCola = false) {
        $sql = "SELECT o.*, c.nombres AS cli_nombres, c.apellidos AS cli_apellidos, c.puntos_acumulados, v.placa, pr.nombre AS nombre_promocion,
                    (SELECT SUM(d.cantidad) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden AND s.acumula_puntos = 1) AS puntos_ganados,
                    (SELECT GROUP_CONCAT(CONCAT(s.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden) AS servicios_vendidos,
                    (SELECT GROUP_CONCAT(CONCAT(p.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_orden = o.id_orden) AS productos_vendidos
             FROM ordenes o
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             LEFT JOIN promociones pr ON o.id_promocion = pr.id_promocion";
             
        if ($excluirEnCola) {
            $sql .= " WHERE o.estado IN ('EN_PROCESO', 'POR_COBRAR') ORDER BY o.fecha_creacion DESC";
        } else {
            $sql .= " WHERE o.estado IN ('EN_COLA', 'EN_PROCESO', 'POR_COBRAR')
                      ORDER BY CASE o.estado WHEN 'POR_COBRAR' THEN 1 WHEN 'EN_PROCESO' THEN 2 WHEN 'EN_COLA' THEN 3 END ASC, o.id_orden DESC";
        }
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // LISTAR HISTORIAL HOY CAJERO
    // ════════════════════════════════════════
    public function getHistorialHoyCajero() {
        $sql = "SELECT o.*, c.nombres AS cli_nombres, c.apellidos AS cli_apellidos, c.puntos_acumulados, v.placa, pr.nombre AS nombre_promocion,
                    (SELECT GROUP_CONCAT(CONCAT(p.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_orden = o.id_orden) AS productos_vendidos,
                    (SELECT GROUP_CONCAT(CONCAT(s.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden) AS servicios_vendidos,
                    (SELECT SUM(d.cantidad) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden AND s.acumula_puntos = 1) AS puntos_ganados,
                    (SELECT GROUP_CONCAT(metodo_pago SEPARATOR ', ') FROM pagos_orden po WHERE po.id_orden = o.id_orden) AS metodo_pago
             FROM ordenes o
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             LEFT JOIN promociones pr ON o.id_promocion = pr.id_promocion
             WHERE o.estado = 'FINALIZADO' AND DATE(o.fecha_cierre) = CURDATE()
             ORDER BY o.fecha_cierre DESC LIMIT 50";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // OBTENER ORDEN POR ID
    // ════════════════════════════════════════
    public function getById($id) {
        $sql = "SELECT o.*, 
                    c.nombres AS cliente_nombres, c.apellidos AS cliente_apellidos, c.dni AS cliente_dni,
                    v.placa, v.color AS vehiculo_color, cat.nombre AS categoria_vehiculo,
                    u.nombres AS creador_nombre, t.nombre AS temporada_nombre
                FROM ordenes o
                LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
                LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
                LEFT JOIN usuarios u ON o.id_usuario_creador = u.id_usuario
                LEFT JOIN temporadas t ON o.id_temporada = t.id_temporada
                WHERE o.id_orden = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // CREAR ORDEN
    // ════════════════════════════════════════
    public function crear($data) {
        try {
            $this->pdo->beginTransaction();

            // Obtener temporada activa
            $stmt = $this->pdo->query("SELECT id_temporada FROM temporadas WHERE estado = 1 LIMIT 1");
            $temp = $stmt->fetch();
            if (!$temp) throw new \Exception("No hay temporada activa.");

            $sql = "INSERT INTO ordenes (id_temporada, id_cliente, id_vehiculo, id_usuario_creador, estado, ubicacion_en_local) 
                    VALUES (:temp, :cliente, :vehiculo, :creador, 'EN_COLA', :ubicacion)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':temp'      => $temp['id_temporada'],
                ':cliente'   => $data['id_cliente'],
                ':vehiculo'  => !empty($data['id_vehiculo']) ? $data['id_vehiculo'] : null,
                ':creador'   => $data['id_usuario_creador'],
                ':ubicacion' => $data['ubicacion_en_local'] ?? null
            ]);
            $id_orden = $this->pdo->lastInsertId();

            // Insertar detalles (servicios)
            if (!empty($data['servicios']) && is_array($data['servicios'])) {
                $totalServ = 0;
                foreach ($data['servicios'] as $serv) {
                    $sub = $serv['precio_unitario'] * ($serv['cantidad'] ?? 1);
                    $totalServ += $sub;
                    $stmt = $this->pdo->prepare(
                        "INSERT INTO detalle_orden (id_orden, id_servicio, cantidad, precio_unitario, subtotal) 
                         VALUES (:orden, :serv, :cant, :precio, :sub)"
                    );
                    $stmt->execute([
                        ':orden'  => $id_orden,
                        ':serv'   => $serv['id_servicio'],
                        ':cant'   => $serv['cantidad'] ?? 1,
                        ':precio' => $serv['precio_unitario'],
                        ':sub'    => $sub
                    ]);
                }
                // Actualizar totales
                $this->pdo->prepare("UPDATE ordenes SET total_servicios = :ts, total_final = :tf WHERE id_orden = :id")
                    ->execute([':ts' => $totalServ, ':tf' => $totalServ, ':id' => $id_orden]);
            }

            $this->pdo->commit();
            return $id_orden;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    // ════════════════════════════════════════
    // CAMBIAR ESTADO DE ORDEN
    // ════════════════════════════════════════
    public function cambiarEstado($id_orden, $nuevo_estado, $data_extra = []) {
        try {
            $campos = "estado = :estado";
            $params = [':estado' => $nuevo_estado, ':id' => $id_orden];

            if ($nuevo_estado === 'FINALIZADO') {
                $campos .= ", fecha_cierre = NOW()";
                if (!empty($data_extra['id_usuario_cajero'])) {
                    $campos .= ", id_usuario_cajero = :cajero";
                    $params[':cajero'] = $data_extra['id_usuario_cajero'];
                }
            }

            if ($nuevo_estado === 'ANULADO' && !empty($data_extra['motivo_anulacion'])) {
                $campos .= ", motivo_anulacion = :motivo";
                $params[':motivo'] = $data_extra['motivo_anulacion'];
            }

            if (!empty($data_extra['id_token_autorizacion'])) {
                $campos .= ", id_token_autorizacion = :token";
                $params[':token'] = $data_extra['id_token_autorizacion'];
            }

            $sql = "UPDATE ordenes SET $campos WHERE id_orden = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (\Exception $e) { return false; }
    }

    // ════════════════════════════════════════
    // REGISTRAR PAGO
    // ════════════════════════════════════════
    public function registrarPago($id_orden, $metodo_pago, $monto) {
        $sql = "INSERT INTO pagos_orden (id_orden, metodo_pago, monto) VALUES (:orden, :metodo, :monto)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':orden' => $id_orden, ':metodo' => $metodo_pago, ':monto' => $monto]);
    }

    // ════════════════════════════════════════
    // OBTENER DETALLES DE UNA ORDEN
    // ════════════════════════════════════════
    public function getDetalles($id_orden) {
        $sql = "SELECT d.*, s.nombre AS servicio_nombre, p.nombre AS producto_nombre
                FROM detalle_orden d
                LEFT JOIN servicios s ON d.id_servicio = s.id_servicio
                LEFT JOIN productos p ON d.id_producto = p.id_producto
                WHERE d.id_orden = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_orden]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // OBTENER PAGOS DE UNA ORDEN
    // ════════════════════════════════════════
    public function getPagos($id_orden) {
        $sql = "SELECT * FROM pagos_orden WHERE id_orden = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_orden]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ════════════════════════════════════════
    // ESTADÍSTICAS
    // ════════════════════════════════════════
    public function getEstadisticas() {
        $stats = [];
        $estados = ['EN_COLA','EN_PROCESO','POR_COBRAR','FINALIZADO','ANULADO'];
        foreach ($estados as $e) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM ordenes WHERE estado = :e");
            $stmt->execute([':e' => $e]);
            $stats[$e] = $stmt->fetch()['total'] ?? 0;
        }
        // Total ingresos finalizados
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_final), 0) as total FROM ordenes WHERE estado = 'FINALIZADO'");
        $stats['ingresos_total'] = $stmt->fetch()['total'] ?? 0;

        // Ingresos hoy
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_final), 0) as total FROM ordenes WHERE estado = 'FINALIZADO' AND DATE(fecha_cierre) = CURDATE()");
        $stats['ingresos_hoy'] = $stmt->fetch()['total'] ?? 0;

        return $stats;
    }
}

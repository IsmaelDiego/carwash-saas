<?php

namespace Controllers\Caja;

use Orden;
use TokenSeguridad;

class DashboardController
{

    public function __construct()
    {
        requireAnyRole([2, 3]); // Cajero y Operador (cuando actúa como cajero)
    }

    public function index()
    {
        $this->dashboard();
    }

    // ═══ VISTA PRINCIPAL — POS ═══
    public function dashboard()
    {
        requireAuth();
        global $pdo;

        // --- ESTADÍSTICAS DEL DÍA ---
        $stats = [];
        $resStats = $pdo->query("SELECT estado, COUNT(*) as total FROM ordenes WHERE DATE(fecha_creacion) = CURDATE() GROUP BY estado")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($resStats as $rs) $stats[$rs['estado']] = $rs['total'];

        // Ingresos Hoy (Finalizadas)
        $resIngresos = $pdo->query("SELECT SUM(total_final) as total FROM ordenes WHERE estado = 'FINALIZADO' AND DATE(fecha_cierre) = CURDATE()")->fetch();
        $stats['ingresos_hoy'] = $resIngresos['total'] ?? 0;

        // --- CAJA SESIÓN ---
        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);

        // --- ÓRDENES ACTIVAS (TÚNEL) ---
        $ordenModel = new Orden($pdo);
        $todasOrdenes = $ordenModel->getActivasCajero(false);

        // --- TIENDA (PRODUCTOS) ---
        $productos = $pdo->query("
            SELECT p.*, DATEDIFF(p.fecha_caducidad, CURDATE()) as dias_vencimiento,
                   (SELECT IFNULL(MAX(pl.precio_venta), p.precio_venta) 
                    FROM producto_lotes pl 
                    WHERE pl.id_producto = p.id_producto AND pl.estado = 'ACTIVO' AND pl.cantidad_actual > 0
                   ) as precio_venta_pos
            FROM productos p
            WHERE p.stock_actual > 0 
            ORDER BY nombre
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // --- HISTORIAL DE HOY ---
        $historialHoy = $ordenModel->getHistorialHoyCajero();

        // --- DATOS PARA CREAR NUEVA ORDEN DE SERVICIO ---
        $servicios = $pdo->query("SELECT * FROM servicios WHERE estado = 1 ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);
        $clientes = $pdo->query("SELECT id_cliente, dni, nombres, apellidos, puntos_acumulados, ya_canjeo_temporada_actual FROM clientes ORDER BY nombres")->fetchAll(\PDO::FETCH_ASSOC);
        $categoriasVH = $pdo->query("SELECT * FROM categorias_vehiculos ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);
        $promociones = $pdo->query("SELECT * FROM promociones WHERE estado = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE() ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);
        
        // Meta puntos para canje
        $config = $pdo->query("SELECT meta_puntos_canje FROM configuracion_sistema WHERE id_configuracion = 1")->fetch();
        $metaPuntos = $config['meta_puntos_canje'] ?? 10;

        // Órdenes activas para anexo en punto de venta (omnicanalidad)
        $ordenesActivas = $pdo->query(
            "SELECT o.id_orden, o.estado, v.placa, c.nombres AS cli_nombres, c.apellidos
             FROM ordenes o
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             WHERE o.estado NOT IN ('FINALIZADO', 'ANULADO') 
             ORDER BY o.id_orden DESC"
        )->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/caja/dashboard.view.php';
    }

    // ═══ VISTA PERFIL ═══
    public function perfil()
    {
        requireAuth();
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT u.*, r.nombre AS rol_nombre FROM usuarios u
             INNER JOIN roles r ON u.id_rol = r.id_rol
             WHERE u.id_usuario = :id"
        );
        $stmt->execute([':id' => $_SESSION['user']['id']]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmtP = $pdo->prepare("SELECT * FROM permisos_empleados WHERE id_usuario = :id ORDER BY fecha_inicio DESC LIMIT 10");
        $stmtP->execute([':id' => $_SESSION['user']['id']]);
        $permisos = $stmtP->fetchAll(\PDO::FETCH_ASSOC);

        $stmtPg = $pdo->prepare("SELECT * FROM pagos_empleados WHERE id_usuario = :id ORDER BY fecha_programada DESC LIMIT 10");
        $stmtPg->execute([':id' => $_SESSION['user']['id']]);
        $pagos = $stmtPg->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/caja/perfil.view.php';
    }

    // ════════════════════════════════════════
    // API ENDPOINTS
    // ════════════════════════════════════════

    // API: OBTENER ÓRDENES
    public function getordenes()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Orden($pdo);
        $activas = $model->getActivasCajero(true);
        $historial = $model->getHistorialHoyCajero();

        echo json_encode([
            'data' => $activas,
            'historial' => $historial
        ]);
    }

    // API: OBTENER DETALLE DE ORDEN
    public function getdetalle()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $model = new Orden($pdo);
        $orden = $model->getById($id);
        $detalles = $model->getDetalles($id);
        echo json_encode(['orden' => $orden, 'detalles' => $detalles]);
    }

    // API: OBTENER VEHÍCULOS DE UN CLIENTE
    public function getvehiculos()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id_cliente'] ?? 0;
        $stmt = $pdo->prepare("SELECT v.*, cat.nombre as categoria, cat.factor_precio FROM vehiculos v LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria WHERE v.id_cliente = :id");
        $stmt->execute([':id' => $id]);
        echo json_encode(['data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    // API: VERIFICAR PROMO
    public function verificarpromo()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $idC = $_GET['id_cliente'] ?? 0;
        $idP = $_GET['id_promocion'] ?? 0;
        $stmt = $pdo->prepare("SELECT COUNT(*) as usado FROM historial_uso_promociones WHERE id_cliente = :c AND id_promocion = :p");
        $stmt->execute([':c' => $idC, ':p' => $idP]);
        echo json_encode(['usado' => $stmt->fetch()['usado'] > 0]);
    }

    // API: REGISTRAR VEHÍCULO
    public function registrarvehiculo()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id_cliente']) || empty($input['id_categoria'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO vehiculos (id_cliente, id_categoria, placa, color, observaciones)
                 VALUES (:cli, :cat, :p, :c, :o)"
            );
            $stmt->execute([
                ':cli' => $input['id_cliente'],
                ':cat' => $input['id_categoria'],
                ':p'   => strtoupper(trim($input['placa'] ?? '')),
                ':c'   => trim($input['color'] ?? ''),
                ':o'   => trim($input['observaciones'] ?? '')
            ]);
            echo json_encode(['success' => true, 'message' => '¡Vehículo registrado!', 'id_vehiculo' => $pdo->lastInsertId()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // API: Pasar orden a EN_PROCESO (Iniciar)
    public function pasara_proceso()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_orden'] ?? 0;
        
        $stmt = $pdo->prepare("UPDATE ordenes SET estado = 'EN_PROCESO' WHERE id_orden = ? AND estado = 'EN_COLA'");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true, 'message' => 'Servicio iniciado.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo iniciar el servicio.']);
        }
    }

    // API: Pasar orden a POR_COBRAR (Finalizar lavado)
    public function pasara_por_cobrar()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id_orden'] ?? 0;

        $stmt = $pdo->prepare("UPDATE ordenes SET estado = 'POR_COBRAR' WHERE id_orden = ? AND estado = 'EN_PROCESO'");
        if ($stmt->execute([$id])) {
            echo json_encode(['success' => true, 'message' => 'Servicio finalizado. Enviado a cobro.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo finalizar el servicio.']);
        }
    }

    // API: CREAR ORDEN (Cola)
    public function crearorden()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_cliente']) || empty($input['servicios'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        try {
            $pdo->beginTransaction();
            $temp = $pdo->query("SELECT id_temporada FROM temporadas WHERE estado = 1 LIMIT 1")->fetch();
            if (!$temp) throw new \Exception('No hay temporada activa.');

            $stmt = $pdo->prepare("INSERT INTO ordenes (id_temporada, id_cliente, id_vehiculo, id_promocion, id_usuario_creador, estado, ubicacion_en_local) VALUES (:t, :c, :v, :prm, :u, 'EN_PROCESO', :ub)");
            $stmt->execute([
                ':t' => $temp['id_temporada'],
                ':c' => $input['id_cliente'],
                ':v' => !empty($input['id_vehiculo']) ? $input['id_vehiculo'] : null,
                ':prm' => !empty($input['id_promocion']) ? $input['id_promocion'] : null,
                ':u' => $_SESSION['user']['id'],
                ':ub' => $input['ubicacion_en_local'] ?? null
            ]);
            $idOrden = $pdo->lastInsertId();

            $totalServ = 0;
            foreach ($input['servicios'] as $s) {
                $sub = $s['precio_unitario'] * ($s['cantidad'] ?? 1);
                $totalServ += $sub;
                $pdo->prepare("INSERT INTO detalle_orden (id_orden, id_servicio, cantidad, precio_unitario, subtotal) VALUES (:o, :s, :c, :pu, :st)")
                    ->execute([':o' => $idOrden, ':s' => $s['id_servicio'], ':c' => $s['cantidad'] ?? 1, ':pu' => $s['precio_unitario'], ':st' => $sub]);
            }

            // Descuentos (Copy from Operaciones logic)
            $descPromo = 0;
            if (!empty($input['id_promocion'])) {
                $p = $pdo->prepare("SELECT * FROM promociones WHERE id_promocion = ? AND estado = 1");
                $p->execute([$input['id_promocion']]);
                $promo = $p->fetch();
                if ($promo) {
                    $descPromo = ($promo['tipo_descuento'] === 'PORCENTAJE') ? round($totalServ * ($promo['valor'] / 100), 2) : min($promo['valor'], $totalServ);
                    $pdo->prepare("INSERT INTO historial_uso_promociones (id_promocion, id_cliente) VALUES (?, ?)")
                        ->execute([$input['id_promocion'], $input['id_cliente']]);
                }
            }
            $descPuntos = 0;
            if (!empty($input['canjear_puntos'])) {
                // ... logic canje points ...
                $descPuntos = $totalServ - $descPromo; // Simplificado: servicio gratis
            }

            $totalFinal = max($totalServ - $descPromo - $descPuntos, 0);
            $pdo->prepare("UPDATE ordenes SET total_servicios = :ts, descuento_promo = :dp, descuento_puntos = :dpt, total_final = :tf, estado = 'EN_PROCESO' WHERE id_orden = :id")
                ->execute([':ts' => $totalServ, ':dp' => $descPromo, ':dpt' => $descPuntos, ':tf' => $totalFinal, ':id' => $idOrden]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => "Orden #$idOrden creada en proceso.", 'id_orden' => $idOrden]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // API: FINALIZAR ORDEN (Cobrar)
    public function finalizarorden()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);
        if (!$cajaActiva) {
            echo json_encode(['success' => false, 'message' => 'BLOQUEADO: No hay caja abierta. Abre caja para cobrar.']); 
            return;
        }

        $model = new Orden($pdo);
        $orden = $model->getById($input['id_orden']);
        if (!$orden) {
            echo json_encode(['success' => false, 'message' => 'Orden no encontrada.']);
            return;
        }

        $model->registrarPago($input['id_orden'], $input['metodo_pago'], $orden['total_final']);
        $model->cambiarEstado($input['id_orden'], 'FINALIZADO', ['id_usuario_cajero' => $_SESSION['user']['id']]);

        // Ligar la transacción a la sesión de caja
        $stmtV = $pdo->prepare("UPDATE ordenes SET id_caja_sesion = ? WHERE id_orden = ?");
        $stmtV->execute([$cajaActiva['id_sesion'], $input['id_orden']]);

        echo json_encode(['success' => true, 'message' => '¡Orden #' . $input['id_orden'] . ' cobrada!']);
    }

    // API: VENTA DIRECTA (productos de tienda, sin orden de servicio)
    public function ventadirecta()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['productos']) || empty($input['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);
        if (!$cajaActiva) {
            echo json_encode(['success' => false, 'message' => 'BLOQUEADO: Secuencia de caja bloqueada. Abre tu caja.']); 
            return;
        }

        try {
            $pdo->beginTransaction();

            // Crear orden de tipo venta directa (con cliente genérico)
            $temp = $pdo->query("SELECT id_temporada FROM temporadas WHERE estado = 1 LIMIT 1")->fetch();
            if (!$temp) throw new \Exception('No hay temporada activa.');

            $stmt = $pdo->prepare(
                "INSERT INTO ordenes (id_temporada, id_cliente, id_usuario_creador, id_usuario_cajero, estado, ubicacion_en_local, id_caja_sesion)
                 VALUES (:temp, 1, :cajero, :cajero2, 'FINALIZADO', 'Venta Directa', :id_caja)"
            );
            $stmt->execute([
                ':temp' => $temp['id_temporada'], 
                ':cajero' => $_SESSION['user']['id'], 
                ':cajero2' => $_SESSION['user']['id'],
                ':id_caja' => $cajaActiva['id_sesion']
            ]);
            $idOrden = $pdo->lastInsertId();

            $totalProd = 0;
            require_once __DIR__ . '/../../models/Producto.php';
            $productoModel = new \Producto($pdo);

            foreach ($input['productos'] as $item) {
                $p = $productoModel->getById($item['id']);
                if (!$p) {
                    throw new \Exception("Producto no encontrado: {$item['id']}");
                }

                // ★ Verificar bloqueo por lotes vencidos
                $vencido = $productoModel->tieneVencidosBloqueantes($item['id']);
                if ($vencido['bloqueado']) {
                    throw new \Exception("Producto Vencido: '{$p['nombre']}' tiene Lote #{$vencido['id_lote']} vencido ({$vencido['fecha']}). Requiere retiro de almacén.");
                }

                // ★ Precio Sugerido: Se cobra el precio MÁXIMO de entre los lotes activos
                $precioSugerido = $productoModel->getPrecioVentaSugerido($item['id']);

                // ★ Descontar por FIFO — con trazabilidad (manteniendo lote físico)
                $lotesUsados = $productoModel->descontarStockFIFO(
                    $item['id'], 
                    $item['cantidad'], 
                    $idOrden,
                    $_SESSION['user']['id'] ?? null
                );

                // Insertar un detalle_orden por cada lote consumido
                // El precio_unitario será el sugerido (máximo), NO el del lote individual
                foreach ($lotesUsados as $loteUsado) {
                    $subLote = $precioSugerido * $loteUsado['cantidad'];
                    $totalProd += $subLote;

                    $pdo->prepare("INSERT INTO detalle_orden (id_orden, id_producto, cantidad, precio_unitario, subtotal, id_lote) VALUES (:o, :p, :c, :pu, :s, :l)")
                        ->execute([
                            ':o'  => $idOrden, 
                            ':p'  => $item['id'], 
                            ':c'  => $loteUsado['cantidad'], 
                            ':pu' => $precioSugerido, 
                            ':s'  => $subLote,
                            ':l'  => $loteUsado['id_lote']
                        ]);
                }
            }

            $pdo->prepare("UPDATE ordenes SET total_productos = :tp, total_final = :tf, fecha_cierre = NOW() WHERE id_orden = :id")
                ->execute([':tp' => $totalProd, ':tf' => $totalProd, ':id' => $idOrden]);

            // Registrar pago
            $pdo->prepare("INSERT INTO pagos_orden (id_orden, metodo_pago, monto) VALUES (:o, :m, :mo)")
                ->execute([':o' => $idOrden, ':m' => $input['metodo_pago'], ':mo' => $totalProd]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => "¡Venta directa #$idOrden por S/ " . number_format($totalProd, 2) . " procesada!"]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: ANEXAR PRODUCTOS A ORDEN (Omnicanalidad Cajero)
    public function anexarproductos()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['productos'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);
        if (!$cajaActiva) {
            echo json_encode(['success' => false, 'message' => 'BLOQUEADO: Secuencia de caja bloqueada. Abre tu caja.']); 
            return;
        }

        try {
            $pdo->beginTransaction();
            $idOrden = $input['id_orden'];
            
            // Validate order exists and is not finalized
            $ordenStmt = $pdo->prepare("SELECT * FROM ordenes WHERE id_orden = ? FOR UPDATE");
            $ordenStmt->execute([$idOrden]);
            $orden = $ordenStmt->fetch();
            
            if (!$orden || in_array($orden['estado'], ['FINALIZADO', 'ANULADO'])) {
                throw new \Exception('La orden no es válida o ya está cerrada.');
            }

            $totalProdNuevo = 0;
            require_once __DIR__ . '/../../models/Producto.php';
            $productoModel = new \Producto($pdo);

            foreach ($input['productos'] as $item) {
                $p = $productoModel->getById($item['id']);
                if (!$p) throw new \Exception("Producto no encontrado: {$item['id']}");

                $vencido = $productoModel->tieneVencidosBloqueantes($item['id']);
                if ($vencido['bloqueado']) {
                    throw new \Exception("Producto Vencido: '{$p['nombre']}' tiene Lote #{$vencido['id_lote']} vencido.");
                }

                $precioSugerido = $productoModel->getPrecioVentaSugerido($item['id']);
                
                $lotesUsados = $productoModel->descontarStockFIFO(
                    $item['id'], 
                    $item['cantidad'], 
                    $idOrden,
                    $_SESSION['user']['id'] ?? null
                );

                foreach ($lotesUsados as $loteUsado) {
                    $subLote = $precioSugerido * $loteUsado['cantidad'];
                    $totalProdNuevo += $subLote;

                    $pdo->prepare("INSERT INTO detalle_orden (id_orden, id_producto, cantidad, precio_unitario, subtotal, id_lote) VALUES (:o, :p, :c, :pu, :s, :l)")
                        ->execute([
                            ':o'  => $idOrden, 
                            ':p'  => $item['id'], 
                            ':c'  => $loteUsado['cantidad'], 
                            ':pu' => $precioSugerido, 
                            ':s'  => $subLote,
                            ':l'  => $loteUsado['id_lote']
                        ]);
                }
            }

            // Update order totals
            $nuevoTotalProd = ($orden['total_productos'] ?? 0) + $totalProdNuevo;
            $nuevoTotalFinal = ($orden['total_servicios'] ?? 0) + $nuevoTotalProd - ($orden['descuento_promo'] ?? 0) - ($orden['descuento_puntos'] ?? 0);
            
            $pdo->prepare("UPDATE ordenes SET total_productos = :tp, total_final = :tf WHERE id_orden = :id")
                ->execute([':tp' => $nuevoTotalProd, ':tf' => $nuevoTotalFinal, ':id' => $idOrden]);

            $pdo->commit();
            echo json_encode(['success' => true, 'message' => "¡Tienda anexada a la Orden #$idOrden correctamente!"]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: ANULAR REGISTRO (Requiere token)
    public function anularregistro()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['codigo_token']) || empty($input['motivo'])) {
            echo json_encode(['success' => false, 'message' => 'Token y motivo obligatorios.']);
            return;
        }

        $tokenModel = new TokenSeguridad($pdo);
        $token = $tokenModel->validar($input['codigo_token']);
        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']);
            return;
        }
        $tokenModel->marcarUsado($token['id_token']);

        $model = new Orden($pdo);
        $extra = ['motivo_anulacion' => $input['motivo'], 'id_token_autorizacion' => $token['id_token']];
        if ($model->cambiarEstado($input['id_orden'], 'ANULADO', $extra)) {
            echo json_encode(['success' => true, 'message' => 'Orden anulada con autorización.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al anular.']);
        }
    }

    // API: Registrar nuevo cliente
    public function registrarcliente()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['dni']) || empty($input['nombres'])) {
            echo json_encode(['success' => false, 'message' => 'DNI y Nombres son obligatorios.']);
            return;
        }

        $check = $pdo->prepare("SELECT id_cliente FROM clientes WHERE dni = :dni");
        $check->execute([':dni' => $input['dni']]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado.']);
            return;
        }

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO clientes (dni, nombres, apellidos, sexo, telefono, estado_whatsapp)
                 VALUES (:dni, :nombres, :apellidos, :sexo, :tel, :ws)"
            );
            $stmt->execute([
                ':dni'       => $input['dni'],
                ':nombres'   => strtoupper(trim($input['nombres'])),
                ':apellidos' => strtoupper(trim($input['apellidos'] ?? '')),
                ':sexo'      => $input['sexo'] ?? null,
                ':tel'       => $input['telefono'] ?? null,
                ':ws'        => $input['estado_whatsapp'] ?? 1
            ]);
            echo json_encode(['success' => true, 'message' => '¡Cliente registrado!', 'id_cliente' => $pdo->lastInsertId()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: STATS
    public function getstats()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Orden($pdo);
        echo json_encode($model->getEstadisticas());
    }

    // ════════════════════════════════════════
    // API CAJA SESIÓN (Apertura y Cierre)
    // ════════════════════════════════════════

    public function abrir_sesion_caja() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $input = json_decode(file_get_contents('php://input'), true);
        $monto_apertura = (float)($input['monto_apertura'] ?? 0);
        $motivo = $input['motivo_apertura'] ?? null;
        $id_rol = (int)$_SESSION['user']['role'];

        global $pdo;
        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        
        if ($cajaModel->abrirCaja($_SESSION['user']['id'], $monto_apertura, $motivo, $id_rol)) {
            echo json_encode(['success' => true, 'message' => 'Caja iniciada exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ya tienes una caja abierta actualmente.']);
        }
    }

    public function resumen_sesion_caja() {
        requireAuth();
        global $pdo;
        require_once __DIR__ . '/../../models/CajaSesion.php';
        
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);
        
        if (!$cajaActiva) {
            echo json_encode(['success' => false, 'message' => 'Sin caja activa.']);
            return;
        }

        $resumenModosDePago = $cajaModel->getResumenCaja($cajaActiva['id_sesion']);
        $totalVentas = $cajaModel->calcularTotalVentasCaja($cajaActiva['id_sesion']);
        $saldoTeorico = $cajaActiva['monto_apertura'] + $totalVentas;

        echo json_encode([
            'success' => true, 
            'apertura' => $cajaActiva['monto_apertura'],
            'ingresos' => $totalVentas,
            'esperado_en_caja' => $saldoTeorico,
            'resumen_pagos' => $resumenModosDePago,
            'id_sesion' => $cajaActiva['id_sesion']
        ]);
    }

    public function cerrar_sesion_caja() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        
        $input = json_decode(file_get_contents('php://input'), true);
        $id_sesion = (int)($input['id_sesion'] ?? 0);
        $monto_real = (float)($input['monto_declarado'] ?? 0);
        
        global $pdo;
        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        
        if ($cajaModel->cerrarCaja($id_sesion, $monto_real)) {
            echo json_encode(['success' => true, 'message' => 'Caja cerrada y arqueada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Hubo un problema cerrando tu caja.']);
        }
    }
}

<?php
namespace Controllers\Operaciones;

use Orden;
use TokenSeguridad;

class DashboardController {

    public function __construct() {
        requireRole(3); // Solo Operario
    }

    // ═══ VISTA PRINCIPAL — MODO TÚNEL ═══
    public function index() { $this->dashboard(); }

    public function dashboard() {
        requireAuth();
        global $pdo;

        // Verificar si hay una orden en proceso del operario actual
        $stmt = $pdo->prepare(
            "SELECT o.*, c.nombres AS cli_nombres, c.apellidos AS cli_apellidos,
                    c.puntos_acumulados AS cli_puntos, c.ya_canjeo_temporada_actual AS cli_ya_canjeo,
                    c.dni AS cli_dni,
                    v.placa, cat.nombre AS cat_nombre
             FROM ordenes o
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
             WHERE o.id_usuario_creador = :uid AND o.estado IN ('EN_COLA','EN_PROCESO','POR_COBRAR')
             ORDER BY o.id_orden DESC LIMIT 1"
        );
        $stmt->execute([':uid' => $_SESSION['user']['id']]);
        $ordenActiva = $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;

        // Detalles de la orden activa
        $detallesOrden = [];
        if ($ordenActiva) {
            $stmtD = $pdo->prepare(
                "SELECT d.*, s.nombre AS servicio_nombre, s.acumula_puntos, p.nombre AS producto_nombre
                 FROM detalle_orden d
                 LEFT JOIN servicios s ON d.id_servicio = s.id_servicio
                 LEFT JOIN productos p ON d.id_producto = p.id_producto
                 WHERE d.id_orden = :id"
            );
            $stmtD->execute([':id' => $ordenActiva['id_orden']]);
            $detallesOrden = $stmtD->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Calcular puntos que se acumularán con esta orden
        $puntosAcumulables = 0;
        foreach ($detallesOrden as $det) {
            if (!empty($det['id_servicio']) && $det['acumula_puntos']) {
                $puntosAcumulables += (int)$det['cantidad'];
            }
        }

        // Datos para crear nueva orden
        $servicios = $pdo->query("SELECT * FROM servicios WHERE estado = 1 ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);
        $clientes = $pdo->query(
            "SELECT id_cliente, dni, nombres, apellidos, puntos_acumulados, ya_canjeo_temporada_actual
             FROM clientes ORDER BY nombres"
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Categorías de vehículos (para nuevo vehículo)
        $categoriasVH = $pdo->query("SELECT * FROM categorias_vehiculos ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);

        // Productos (solo si tiene token desbloqueado)
        $productos = $pdo->query("SELECT * FROM productos WHERE stock_actual > 0 ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);

        // Promociones activas
        $promociones = $pdo->query(
            "SELECT * FROM promociones WHERE estado = 1 AND fecha_inicio <= CURDATE() AND fecha_fin >= CURDATE() ORDER BY nombre"
        )->fetchAll(\PDO::FETCH_ASSOC);

        // Token desbloqueado en sesión?
        $tokenDesbloqueado = !empty($_SESSION['operario_token_desbloqueado']);

        // Configuración sistema
        $config = $pdo->query("SELECT * FROM configuracion_sistema WHERE id_configuracion = 1")->fetch(\PDO::FETCH_ASSOC);
        $modoSinCajero = $config['modo_sin_cajero'] ?? 0;
        $metaPuntos = $config['meta_puntos_canje'] ?? 10;

        require VIEW_PATH . '/operaciones/dashboard.view.php';
    }

    // ═══ VISTA PERFIL ═══
    public function perfil() {
        requireAuth();
        global $pdo;

        $stmt = $pdo->prepare(
            "SELECT u.*, r.nombre AS rol_nombre FROM usuarios u
             INNER JOIN roles r ON u.id_rol = r.id_rol WHERE u.id_usuario = :id"
        );
        $stmt->execute([':id' => $_SESSION['user']['id']]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stmtP = $pdo->prepare("SELECT * FROM permisos_empleados WHERE id_usuario = :id ORDER BY fecha_inicio DESC LIMIT 10");
        $stmtP->execute([':id' => $_SESSION['user']['id']]);
        $permisos = $stmtP->fetchAll(\PDO::FETCH_ASSOC);

        $stmtPg = $pdo->prepare("SELECT * FROM pagos_empleados WHERE id_usuario = :id ORDER BY fecha_programada DESC LIMIT 10");
        $stmtPg->execute([':id' => $_SESSION['user']['id']]);
        $pagos = $stmtPg->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/operaciones/perfil.view.php';
    }

    // ════════════════════════════════════════
    // API ENDPOINTS
    // ════════════════════════════════════════

    // API: Obtener vehículos de un cliente
    public function getvehiculos() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $idCliente = $_GET['id_cliente'] ?? 0;
        $stmt = $pdo->prepare(
            "SELECT v.*, cat.nombre AS categoria, cat.factor_precio
             FROM vehiculos v
             LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria
             WHERE v.id_cliente = :id ORDER BY v.placa"
        );
        $stmt->execute([':id' => $idCliente]);
        echo json_encode(['data' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
    }

    // API: Verificar si cliente ya usó una promoción
    public function verificarpromo() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $idCliente = $_GET['id_cliente'] ?? 0;
        $idPromo = $_GET['id_promocion'] ?? 0;
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) as usado FROM historial_uso_promociones WHERE id_cliente = :c AND id_promocion = :p"
        );
        $stmt->execute([':c' => $idCliente, ':p' => $idPromo]);
        echo json_encode(['usado' => $stmt->fetch()['usado'] > 0]);
    }

    // API: Registrar nuevo cliente
    public function registrarcliente() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['dni']) || empty($input['nombres'])) {
            echo json_encode(['success' => false, 'message' => 'DNI y Nombres son obligatorios.']); return;
        }

        // Verificar DNI duplicado
        $check = $pdo->prepare("SELECT id_cliente FROM clientes WHERE dni = :dni");
        $check->execute([':dni' => $input['dni']]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado.']); return;
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
            $idCliente = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => '¡Cliente registrado!',
                'id_cliente' => $idCliente,
                'nombre_completo' => strtoupper(trim($input['nombres'])) . ' ' . strtoupper(trim($input['apellidos'] ?? ''))
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Registrar nuevo vehículo
    public function registrarvehiculo() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_cliente']) || empty($input['id_categoria'])) {
            echo json_encode(['success' => false, 'message' => 'Cliente y categoría son obligatorios.']); return;
        }

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO vehiculos (id_cliente, id_categoria, placa, color, observaciones)
                 VALUES (:cli, :cat, :placa, :color, :obs)"
            );
            $stmt->execute([
                ':cli'   => $input['id_cliente'],
                ':cat'   => $input['id_categoria'],
                ':placa' => strtoupper(trim($input['placa'] ?? '')),
                ':color' => trim($input['color'] ?? ''),
                ':obs'   => trim($input['observaciones'] ?? '')
            ]);
            echo json_encode(['success' => true, 'message' => '¡Vehículo registrado!', 'id_vehiculo' => $pdo->lastInsertId()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Consultar RENIEC por DNI
    public function consultarreniec() {
        requireAuth();
        header('Content-Type: application/json');
        $dni = $_GET['dni'] ?? '';
        if (strlen($dni) !== 8) {
            echo json_encode(['success' => false, 'message' => 'DNI debe tener 8 dígitos.']); return;
        }

        // API RENIEC pública
        $url = "https://api.apis.net.pe/v2/reniec/dni?numero={$dni}";
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Accept: application/json']
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (!empty($data['nombres'])) {
                echo json_encode([
                    'success'   => true,
                    'nombres'   => $data['nombres'] ?? '',
                    'apellidos' => trim(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? ''))
                ]);
                return;
            }
        }

        // Fallback: API alternativa
        $url2 = "https://dniruc.apisperu.com/api/v1/dni/{$dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImV4YW1wbGVAZXhhbXBsZS5jb20ifQ.demo";
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL => $url2,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $res2 = curl_exec($ch2);
        curl_close($ch2);

        if ($res2) {
            $d2 = json_decode($res2, true);
            if (!empty($d2['nombres'])) {
                echo json_encode([
                    'success'   => true,
                    'nombres'   => $d2['nombres'] ?? '',
                    'apellidos' => trim(($d2['apellidoPaterno'] ?? '') . ' ' . ($d2['apellidoMaterno'] ?? ''))
                ]);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'No se encontró información para este DNI.']);
    }

    // API: Crear orden (ACTUALIZADO con promoción y puntos)
    public function crearorden() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_cliente']) || empty($input['servicios'])) {
            echo json_encode(['success' => false, 'message' => 'Cliente y servicios son obligatorios.']); return;
        }

        $input['id_usuario_creador'] = $_SESSION['user']['id'];

        try {
            $pdo->beginTransaction();

            // Obtener temporada activa
            $temp = $pdo->query("SELECT id_temporada FROM temporadas WHERE estado = 1 LIMIT 1")->fetch();
            if (!$temp) throw new \Exception("No hay temporada activa.");

            // Crear la orden
            $stmt = $pdo->prepare(
                "INSERT INTO ordenes (id_temporada, id_cliente, id_vehiculo, id_usuario_creador, estado, ubicacion_en_local)
                 VALUES (:temp, :cli, :veh, :crea, 'EN_COLA', :ubic)"
            );
            $stmt->execute([
                ':temp' => $temp['id_temporada'],
                ':cli'  => $input['id_cliente'],
                ':veh'  => !empty($input['id_vehiculo']) ? $input['id_vehiculo'] : null,
                ':crea' => $input['id_usuario_creador'],
                ':ubic' => $input['ubicacion_en_local'] ?? null
            ]);
            $idOrden = $pdo->lastInsertId();

            // Insertar servicios
            $totalServ = 0;
            foreach ($input['servicios'] as $serv) {
                $sub = $serv['precio_unitario'] * ($serv['cantidad'] ?? 1);
                $totalServ += $sub;
                $pdo->prepare(
                    "INSERT INTO detalle_orden (id_orden, id_servicio, cantidad, precio_unitario, subtotal)
                     VALUES (:o, :s, :c, :p, :st)"
                )->execute([':o' => $idOrden, ':s' => $serv['id_servicio'], ':c' => $serv['cantidad'] ?? 1,
                            ':p' => $serv['precio_unitario'], ':st' => $sub]);
            }

            // Calcular descuento por promoción
            $descPromo = 0;
            if (!empty($input['id_promocion'])) {
                $promo = $pdo->prepare("SELECT * FROM promociones WHERE id_promocion = :id AND estado = 1");
                $promo->execute([':id' => $input['id_promocion']]);
                $p = $promo->fetch(\PDO::FETCH_ASSOC);
                if ($p) {
                    // Verificar si es solo_una_vez y ya la usó
                    $yaUso = false;
                    if ($p['solo_una_vez_por_cliente']) {
                        $ch = $pdo->prepare("SELECT COUNT(*) as c FROM historial_uso_promociones WHERE id_cliente = :c AND id_promocion = :p");
                        $ch->execute([':c' => $input['id_cliente'], ':p' => $p['id_promocion']]);
                        $yaUso = $ch->fetch()['c'] > 0;
                    }
                    if (!$yaUso) {
                        $descPromo = $p['tipo_descuento'] === 'PORCENTAJE'
                            ? round($totalServ * ($p['valor'] / 100), 2)
                            : min($p['valor'], $totalServ);

                        // Registrar uso
                        $pdo->prepare("INSERT INTO historial_uso_promociones (id_promocion, id_cliente) VALUES (:p, :c)")
                            ->execute([':p' => $p['id_promocion'], ':c' => $input['id_cliente']]);
                    }
                }
            }

            // Calcular descuento por canje de puntos
            $descPuntos = 0;
            if (!empty($input['canjear_puntos'])) {
                $cli = $pdo->prepare("SELECT puntos_acumulados, ya_canjeo_temporada_actual FROM clientes WHERE id_cliente = :id");
                $cli->execute([':id' => $input['id_cliente']]);
                $cliente = $cli->fetch(\PDO::FETCH_ASSOC);
                $meta = $pdo->query("SELECT meta_puntos_canje FROM configuracion_sistema WHERE id_configuracion = 1")->fetch()['meta_puntos_canje'] ?? 10;

                if ($cliente && $cliente['puntos_acumulados'] >= $meta && !$cliente['ya_canjeo_temporada_actual']) {
                    $descPuntos = $totalServ - $descPromo; // Servicio gratis
                    $pdo->prepare("UPDATE clientes SET puntos_acumulados = puntos_acumulados - :meta, ya_canjeo_temporada_actual = 1 WHERE id_cliente = :id")
                        ->execute([':meta' => $meta, ':id' => $input['id_cliente']]);
                }
            }

            // Actualizar totales
            $totalFinal = max($totalServ - $descPromo - $descPuntos, 0);
            $pdo->prepare(
                "UPDATE ordenes SET total_servicios = :ts, descuento_promo = :dp, descuento_puntos = :dpt, total_final = :tf WHERE id_orden = :id"
            )->execute([':ts' => $totalServ, ':dp' => $descPromo, ':dpt' => $descPuntos, ':tf' => $totalFinal, ':id' => $idOrden]);

            // Pasar a EN_PROCESO
            $pdo->prepare("UPDATE ordenes SET estado = 'EN_PROCESO' WHERE id_orden = :id")->execute([':id' => $idOrden]);

            $pdo->commit();

            $msg = "¡Orden #{$idOrden} creada!";
            if ($descPromo > 0) $msg .= " Promo: -S/ " . number_format($descPromo, 2);
            if ($descPuntos > 0) $msg .= " | ¡CANJE PUNTOS: GRATIS!";

            echo json_encode(['success' => true, 'message' => $msg, 'id_orden' => $idOrden]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Agregar producto (consumo) a la orden
    public function agregarproducto() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['id_producto'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']); return;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE id_producto = :id AND stock_actual > 0");
            $stmt->execute([':id' => $input['id_producto']]);
            $prod = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$prod) { echo json_encode(['success' => false, 'message' => 'Producto sin stock.']); return; }

            $cantidad = (int)($input['cantidad'] ?? 1);
            $subtotal = $prod['precio_venta'] * $cantidad;

            $pdo->prepare(
                "INSERT INTO detalle_orden (id_orden, id_producto, cantidad, precio_unitario, subtotal) VALUES (:o, :p, :c, :pu, :s)"
            )->execute([':o' => $input['id_orden'], ':p' => $input['id_producto'], ':c' => $cantidad, ':pu' => $prod['precio_venta'], ':s' => $subtotal]);

            $pdo->prepare(
                "UPDATE ordenes SET total_productos = total_productos + :sub, total_final = total_servicios + total_productos + :sub2 - descuento_promo - descuento_puntos WHERE id_orden = :id"
            )->execute([':sub' => $subtotal, ':sub2' => $subtotal, ':id' => $input['id_orden']]);

            $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - :c WHERE id_producto = :id")
                ->execute([':c' => $cantidad, ':id' => $input['id_producto']]);

            echo json_encode(['success' => true, 'message' => "{$prod['nombre']} agregado."]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Finalizar orden — AHORA ACUMULA PUNTOS
    public function finalizarorden() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $pdo->beginTransaction();
            $model = new Orden($pdo);
            $orden = $model->getById($input['id_orden']);
            if (!$orden) throw new \Exception('Orden no encontrada.');

            // Cambiar estado a POR_COBRAR
            $model->cambiarEstado($input['id_orden'], 'POR_COBRAR');

            // Acumular puntos al cliente (1 punto por cada servicio que acumula_puntos)
            $stmtS = $pdo->prepare(
                "SELECT SUM(d.cantidad) AS total_puntos FROM detalle_orden d
                 INNER JOIN servicios s ON d.id_servicio = s.id_servicio
                 WHERE d.id_orden = :id AND s.acumula_puntos = 1"
            );
            $stmtS->execute([':id' => $input['id_orden']]);
            $puntosGanados = (int)($stmtS->fetch()['total_puntos'] ?? 0);

            if ($puntosGanados > 0 && $orden['id_cliente']) {
                $pdo->prepare("UPDATE clientes SET puntos_acumulados = puntos_acumulados + :pts WHERE id_cliente = :id")
                    ->execute([':pts' => $puntosGanados, ':id' => $orden['id_cliente']]);
            }

            $pdo->commit();

            $msg = 'Orden lista para cobro.';
            if ($puntosGanados > 0) $msg .= " +{$puntosGanados} punto(s) para el cliente.";
            echo json_encode(['success' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Cobrar orden (con token)
    public function cobrarorden() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']); return;
        }

        $model = new Orden($pdo);
        $orden = $model->getById($input['id_orden']);
        if (!$orden) { echo json_encode(['success' => false, 'message' => 'Orden no encontrada.']); return; }

        $model->registrarPago($input['id_orden'], $input['metodo_pago'], $orden['total_final']);
        $extra = ['id_usuario_cajero' => $_SESSION['user']['id'], 'id_token_autorizacion' => $_SESSION['operario_token_id'] ?? null];
        $model->cambiarEstado($input['id_orden'], 'FINALIZADO', $extra);

        unset($_SESSION['operario_token_desbloqueado'], $_SESSION['operario_token_id']);
        echo json_encode(['success' => true, 'message' => '¡Orden cobrada y finalizada!']);
    }

    // API: Validar token
    public function validartoken() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new TokenSeguridad($pdo);
        $token = $model->validar($input['codigo'] ?? '');
        if ($token) {
            $model->marcarUsado($token['id_token']);
            $_SESSION['operario_token_desbloqueado'] = true;
            $_SESSION['operario_token_id'] = $token['id_token'];
            echo json_encode(['success' => true, 'message' => '¡Token válido!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Token inválido, expirado o usado.']);
        }
    }

    // API: Obtener detalles
    public function getdetalleorden() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $model = new Orden($pdo);
        echo json_encode(['orden' => $model->getById($id), 'detalles' => $model->getDetalles($id)]);
    }

    // API: Anular orden
    public function anularorden() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['id_orden']) || empty($input['codigo_token'])) {
            echo json_encode(['success' => false, 'message' => 'Token obligatorio.']); return;
        }
        $tokenModel = new TokenSeguridad($pdo);
        $token = $tokenModel->validar($input['codigo_token']);
        if (!$token) { echo json_encode(['success' => false, 'message' => 'Token inválido.']); return; }
        $tokenModel->marcarUsado($token['id_token']);
        $model = new Orden($pdo);
        $model->cambiarEstado($input['id_orden'], 'ANULADO', [
            'motivo_anulacion' => $input['motivo'] ?? 'Anulado por operario',
            'id_token_autorizacion' => $token['id_token']
        ]);
        echo json_encode(['success' => true, 'message' => 'Orden anulada.']);
    }
}

<?php
namespace Controllers\Caja;

use Orden;
use TokenSeguridad;

class DashboardController {

    public function __construct() {
        requireRole(2); // Solo Cajero
    }

    public function index() { $this->dashboard(); }

    // ═══ VISTA PRINCIPAL — POS ═══
    public function dashboard() {
        requireAuth();
        global $pdo;

        $ordenModel = new Orden($pdo);
        $stats = $ordenModel->getEstadisticas();

        // Órdenes por cobrar (prioridad)
        $ordenesPorCobrar = $ordenModel->getAll('POR_COBRAR');
        $ordenesEnProceso = $ordenModel->getAll('EN_PROCESO');

        // Productos para venta
        $productos = $pdo->query("SELECT * FROM productos WHERE stock_actual > 0 ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);

        // Historial de hoy
        $stmtHoy = $pdo->query(
            "SELECT o.*, c.nombres AS cli_nombres, c.apellidos AS cli_apellidos, v.placa,
                    (SELECT GROUP_CONCAT(CONCAT(p.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_orden = o.id_orden) AS productos_vendidos,
                    (SELECT GROUP_CONCAT(CONCAT(s.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden) AS servicios_vendidos,
                    (SELECT SUM(d.cantidad) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden AND s.acumula_puntos = 1) AS puntos_ganados,
                    (SELECT GROUP_CONCAT(metodo_pago SEPARATOR ', ') FROM pagos_orden po WHERE po.id_orden = o.id_orden) AS metodo_pago
             FROM ordenes o
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             WHERE o.estado = 'FINALIZADO' AND DATE(o.fecha_cierre) = CURDATE()
             ORDER BY o.fecha_cierre DESC LIMIT 50"
        );
        $historialHoy = $stmtHoy->fetchAll(\PDO::FETCH_ASSOC);

        require VIEW_PATH . '/caja/dashboard.view.php';
    }

    // ═══ VISTA PERFIL ═══
    public function perfil() {
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
    public function getordenes() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $model = new Orden($pdo);
        
        $pendientes = $model->getAll('POR_COBRAR');
        $proceso = $model->getAll('EN_PROCESO');
        
        $stmtHoy = $pdo->query(
            "SELECT o.*, c.nombres AS cli_nombres, c.apellidos AS cli_apellidos, v.placa,
                    (SELECT GROUP_CONCAT(CONCAT(p.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_orden = o.id_orden) AS productos_vendidos,
                    (SELECT GROUP_CONCAT(CONCAT(s.nombre, ' (x', d.cantidad, ')') SEPARATOR ', ') FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden) AS servicios_vendidos,
                    (SELECT SUM(d.cantidad) FROM detalle_orden d INNER JOIN servicios s ON d.id_servicio = s.id_servicio WHERE d.id_orden = o.id_orden AND s.acumula_puntos = 1) AS puntos_ganados,
                    (SELECT GROUP_CONCAT(metodo_pago SEPARATOR ', ') FROM pagos_orden po WHERE po.id_orden = o.id_orden) AS metodo_pago
             FROM ordenes o
             LEFT JOIN clientes c ON o.id_cliente = c.id_cliente
             LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
             WHERE o.estado = 'FINALIZADO' AND DATE(o.fecha_cierre) = CURDATE()
             ORDER BY o.fecha_cierre DESC LIMIT 50"
        );
        $historial = $stmtHoy->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            'data' => array_merge($pendientes, $proceso),
            'historial' => $historial
        ]);
    }

    // API: OBTENER DETALLE DE ORDEN
    public function getdetalle() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $model = new Orden($pdo);
        $orden = $model->getById($id);
        $detalles = $model->getDetalles($id);
        echo json_encode(['orden' => $orden, 'detalles' => $detalles]);
    }

    // API: FINALIZAR ORDEN (Cobrar)
    public function finalizarorden() {
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
        $model->cambiarEstado($input['id_orden'], 'FINALIZADO', ['id_usuario_cajero' => $_SESSION['user']['id']]);

        echo json_encode(['success' => true, 'message' => '¡Orden #' . $input['id_orden'] . ' cobrada!']);
    }

    // API: VENTA DIRECTA (productos de tienda, sin orden de servicio)
    public function ventadirecta() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['productos']) || empty($input['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']); return;
        }

        try {
            $pdo->beginTransaction();

            // Crear orden de tipo venta directa (con cliente genérico)
            $temp = $pdo->query("SELECT id_temporada FROM temporadas WHERE estado = 1 LIMIT 1")->fetch();
            if (!$temp) throw new \Exception('No hay temporada activa.');

            $stmt = $pdo->prepare(
                "INSERT INTO ordenes (id_temporada, id_cliente, id_usuario_creador, id_usuario_cajero, estado, ubicacion_en_local)
                 VALUES (:temp, 1, :cajero, :cajero2, 'FINALIZADO', 'Venta Directa')"
            );
            $stmt->execute([':temp' => $temp['id_temporada'], ':cajero' => $_SESSION['user']['id'], ':cajero2' => $_SESSION['user']['id']]);
            $idOrden = $pdo->lastInsertId();

            $totalProd = 0;
            foreach ($input['productos'] as $item) {
                $prod = $pdo->prepare("SELECT * FROM productos WHERE id_producto = :id AND stock_actual >= :cant");
                $prod->execute([':id' => $item['id'], ':cant' => $item['cantidad']]);
                $p = $prod->fetch(\PDO::FETCH_ASSOC);
                if (!$p) throw new \Exception("Producto sin stock suficiente: {$item['id']}");

                $sub = $p['precio_venta'] * $item['cantidad'];
                $totalProd += $sub;

                $pdo->prepare("INSERT INTO detalle_orden (id_orden, id_producto, cantidad, precio_unitario, subtotal) VALUES (:o, :p, :c, :pu, :s)")
                    ->execute([':o' => $idOrden, ':p' => $item['id'], ':c' => $item['cantidad'], ':pu' => $p['precio_venta'], ':s' => $sub]);

                $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - :c WHERE id_producto = :id")
                    ->execute([':c' => $item['cantidad'], ':id' => $item['id']]);
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

    // API: ANULAR REGISTRO (Requiere token)
    public function anularregistro() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_orden']) || empty($input['codigo_token']) || empty($input['motivo'])) {
            echo json_encode(['success' => false, 'message' => 'Token y motivo obligatorios.']); return;
        }

        $tokenModel = new TokenSeguridad($pdo);
        $token = $tokenModel->validar($input['codigo_token']);
        if (!$token) { echo json_encode(['success' => false, 'message' => 'Token inválido o expirado.']); return; }
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
    public function registrarcliente() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['dni']) || empty($input['nombres'])) {
            echo json_encode(['success' => false, 'message' => 'DNI y Nombres son obligatorios.']); return;
        }

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
            echo json_encode(['success' => true, 'message' => '¡Cliente registrado!', 'id_cliente' => $pdo->lastInsertId()]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: Consultar RENIEC
    public function consultarreniec() {
        requireAuth();
        header('Content-Type: application/json');
        $dni = $_GET['dni'] ?? '';
        if (strlen($dni) !== 8) { echo json_encode(['success' => false, 'message' => 'DNI de 8 dígitos.']); return; }

        $url = "https://api.apis.net.pe/v2/reniec/dni?numero={$dni}";
        $ch = curl_init();
        curl_setopt_array($ch, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_HTTPHEADER => ['Accept: application/json']]);
        $response = curl_exec($ch); $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (!empty($data['nombres'])) {
                echo json_encode(['success' => true, 'nombres' => $data['nombres'] ?? '', 'apellidos' => trim(($data['apellidoPaterno'] ?? '') . ' ' . ($data['apellidoMaterno'] ?? ''))]);
                return;
            }
        }

        $url2 = "https://dniruc.apisperu.com/api/v1/dni/{$dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImV4YW1wbGVAZXhhbXBsZS5jb20ifQ.demo";
        $ch2 = curl_init(); curl_setopt_array($ch2, [CURLOPT_URL => $url2, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 8, CURLOPT_SSL_VERIFYPEER => false]);
        $res2 = curl_exec($ch2); curl_close($ch2);

        if ($res2) {
            $d2 = json_decode($res2, true);
            if (!empty($d2['nombres'])) {
                echo json_encode(['success' => true, 'nombres' => $d2['nombres'] ?? '', 'apellidos' => trim(($d2['apellidoPaterno'] ?? '') . ' ' . ($d2['apellidoMaterno'] ?? ''))]);
                return;
            }
        }
        echo json_encode(['success' => false, 'message' => 'No encontrado.']);
    }

    // API: STATS
    public function getstats() {
        requireAuth(); global $pdo;
        header('Content-Type: application/json');
        $model = new Orden($pdo);
        echo json_encode($model->getEstadisticas());
    }
}

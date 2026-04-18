<?php

namespace Controllers\Caja;

use Orden;
use TokenSeguridad;

class DashboardController
{

    public function __construct()
    {
        requireAnyRole([2, 3]); // Cajero y Operador (cuando actúa como cajero en modo libre)

        if ($_SESSION['user']['role'] == 3) {
            global $pdo;
            $cfg = $pdo->query("SELECT modo_sin_cajero, id_operador_responsable FROM configuracion_sistema LIMIT 1")->fetch(\PDO::FETCH_ASSOC);
            if ($cfg['modo_sin_cajero'] != 1 || $cfg['id_operador_responsable'] != $_SESSION['user']['id']) {
                // Operario sin permiso de usar caja intentando colarse
                header('Location: ' . BASE_URL . '/operaciones/dashboard/perfil');
                exit;
            }
        }
    }

    public function generar_reporte()
    {
        requireAuth();
        global $pdo;

        $id_sesion = $_GET['id_sesion'] ?? null;
        
        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);

        if (!$id_sesion) {
            $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);
            if (!$cajaActiva) {
                die("No hay una sesión de caja ABIERTA actual para reportar.");
            }
            $id_sesion = $cajaActiva['id_sesion'];
        } else {
            // Buscar sesión histórica
            $stmtS = $pdo->prepare("SELECT * FROM caja_sesiones WHERE id_sesion = ?");
            $stmtS->execute([$id_sesion]);
            $cajaActiva = $stmtS->fetch(\PDO::FETCH_ASSOC);
            if (!$cajaActiva) {
                die("Sesión de caja #$id_sesion no encontrada.");
            }
        }

        $fecha_reporte = date('d-m-Y', strtotime($cajaActiva['fecha_apertura']));
        $cajero_nombre = $_SESSION['user']['name'];
        $monto_apertura = $cajaActiva['monto_apertura'];

        $ventas = $cajaModel->calcularTotalVentasCaja($id_sesion);
        $metodos = $cajaModel->getResumenCaja($id_sesion);
        $productos = $cajaModel->getProductosVendidos($id_sesion);

        $servicios = $pdo->query("SELECT s.nombre, SUM(do.cantidad) as cant, SUM(do.subtotal) as total
                                  FROM detalle_orden do
                                  JOIN ordenes o ON do.id_orden = o.id_orden
                                  JOIN servicios s ON do.id_servicio = s.id_servicio
                                  WHERE o.id_caja_sesion = $id_sesion AND o.estado = 'FINALIZADO'
                                  GROUP BY s.id_servicio")->fetchAll(\PDO::FETCH_ASSOC);

        $descuentos = $pdo->query("SELECT IFNULL(SUM(descuento_promo),0) as promo, 
                                          IFNULL(SUM(descuento_puntos),0) as puntos 
                                   FROM ordenes 
                                   WHERE id_caja_sesion = $id_sesion AND estado = 'FINALIZADO'")->fetch(\PDO::FETCH_ASSOC);

        $formato = $_GET['formato'] ?? 'print';

        if ($formato === 'excel') {
            header("Content-Type: application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=REPORTE_CAJA_{$fecha_reporte}_ID{$id_sesion}.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo "<meta charset=\"utf-8\">";
            require VIEW_PATH . '/caja/reporte_excel.view.php';
            exit;
        }

        // Para previsualización PDF (mPDF)
        if ($formato === 'pdf') {
            ob_start();
            $is_mpdf = true;
            require VIEW_PATH . '/caja/reporte_print.view.php';
            $html = ob_get_clean();

            $autoloadPath = __DIR__ . '/../../../vendor/MPDF/vendor/autoload.php';
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                ]);
                $mpdf->SetTitle("Reporte Caja $fecha_reporte #$id_sesion");
                $mpdf->WriteHTML($html);
                $mpdf->Output("REPORTE_CAJA_" . $fecha_reporte . ".pdf", \Mpdf\Output\Destination::INLINE);
                exit;
            }
        }

        // Para IMPRESIÓN DIRECTA o Fallback (HTML puro)
        $is_mpdf = false;
        require VIEW_PATH . '/caja/reporte_print.view.php';
        exit;
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

        // --- SISTEMA CONFIG ---
        require_once __DIR__ . '/../../models/ConfiguracionSistema.php';
        $configModel = new \ConfiguracionSistema($pdo);
        $config = $configModel->get();

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
        $metaPuntos = $config['meta_puntos_canje'] ?? 10;
        $temporadaActiva = $pdo->query("SELECT nombre FROM temporadas WHERE estado = 1 LIMIT 1")->fetchColumn() ?: 'Sin Temporada Activa';

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

        $stmtP = $pdo->prepare("SELECT * FROM permisos_empleados WHERE id_usuario = :id ORDER BY fecha_inicio DESC");
        $stmtP->execute([':id' => $_SESSION['user']['id']]);
        $permisos = $stmtP->fetchAll(\PDO::FETCH_ASSOC);

        $stmtPg = $pdo->prepare("SELECT * FROM pagos_empleados WHERE id_usuario = :id ORDER BY fecha_programada DESC");
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
        $activas = $model->getActivasCajero(false);
        $historial = $model->getHistorialHoyCajero();

        echo json_encode([
            'data' => $activas,
            'historial' => $historial
        ]);
    }

    // API: VER DETALLE DE ORDEN (Historial)
    public function getdetalle()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id_orden'] ?? $_GET['id'] ?? 0;

        $stmt = $pdo->prepare("SELECT o.*, c.nombres, c.apellidos, v.placa, v.color, cv.nombre as categoria 
                               FROM ordenes o
                               JOIN clientes c ON o.id_cliente = c.id_cliente
                               LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                               LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id_categoria
                               WHERE o.id_orden = ?");
        $stmt->execute([$id]);
        $orden = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$orden) {
            echo json_encode(['success' => false, 'message' => 'Orden no encontrada.']);
            return;
        }

        $stmtD = $pdo->prepare("SELECT do.*, COALESCE(s.nombre, p.nombre) as nombre_item 
                                FROM detalle_orden do 
                                LEFT JOIN servicios s ON do.id_servicio = s.id_servicio 
                                LEFT JOIN productos p ON do.id_producto = p.id_producto
                                WHERE do.id_orden = ?");
        $stmtD->execute([$id]);
        $detalles = $stmtD->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'orden' => $orden,
            'detalles' => $detalles
        ]);
    }

    // API: OBTENER VEHÍCULOS DE UN CLIENTE (Y DATA DE FIDELIZACIÓN)
    public function getvehiculos()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id_cliente'] ?? 0;

        // Vehículos
        $stmtV = $pdo->prepare("SELECT v.*, cat.nombre as categoria, cat.factor_precio FROM vehiculos v LEFT JOIN categorias_vehiculos cat ON v.id_categoria = cat.id_categoria WHERE v.id_cliente = :id");
        $stmtV->execute([':id' => $id]);
        $vehiculos = $stmtV->fetchAll(\PDO::FETCH_ASSOC);

        // Fidelización
        $stmtC = $pdo->prepare("SELECT puntos_acumulados, ya_canjeo_temporada_actual FROM clientes WHERE id_cliente = :id");
        $stmtC->execute([':id' => $id]);
        $cliente = $stmtC->fetch(\PDO::FETCH_ASSOC);

        // Promos usadas
        $stmtP = $pdo->prepare("SELECT id_promocion FROM historial_uso_promociones WHERE id_cliente = :id");
        $stmtP->execute([':id' => $id]);
        $usadas = $stmtP->fetchAll(\PDO::FETCH_COLUMN);

        echo json_encode([
            'data' => $vehiculos,
            'fidelizacion' => $cliente,
            'promos_usadas' => $usadas
        ]);
    }

    // API: OBTENER HISTORIAL DE HOY (Cajero)
    public function gethistorial()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        require_once __DIR__ . '/../../models/Orden.php';
        $ordenModel = new \Orden($pdo);
        $historial = $ordenModel->getHistorialHoyCajero();

        echo json_encode(['success' => true, 'data' => $historial]);
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
        $placa = strtoupper(trim($input['placa'] ?? ''));
        if (!preg_match('/^[A-Z0-9]{3}-[0-9]{3}$/', $placa)) {
            echo json_encode(['success' => false, 'message' => 'Formato de placa inválido (Debe ser XXX-123).']);
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
                ':p'   => $placa,
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

        // --- VALIDACIÓN DE CAPACIDAD DE RAMPAS ---
        require_once __DIR__ . '/../../models/Rampa.php';
        $rampaModel = new \Rampa($pdo);
        $rampasActivas = $rampaModel->getRampasActivas();
        $limite_bahias = count($rampasActivas);

        // Contar cuántas están en proceso actualmente
        $activos = (int)$pdo->query("SELECT COUNT(*) as t FROM ordenes WHERE estado = 'EN_PROCESO'")->fetch()['t'];

        if ($activos >= $limite_bahias) {
            echo json_encode(['success' => false, 'message' => "CAPACIDAD AL LÍMITE: Todas las rampas activas ({$limite_bahias}) están ocupadas. Finaliza un lavado para liberar espacio."]);
            return;
        }

        // Buscar una rampa que no esté siendo usada por una orden EN_PROCESO
        // (Asumiendo que id_rampa en ordenes mapea a la rampa usada)
        $id_rampa_libre = $rampaModel->getPrimeraRampaLibre();

        $stmt = $pdo->prepare("UPDATE ordenes SET estado = 'EN_PROCESO', fecha_inicio_proceso = NOW(), id_rampa = ? WHERE id_orden = ? AND estado IN ('EN_COLA', 'EN_ESPERA')");
        if ($stmt->execute([$id_rampa_libre, $id])) {
            // --- NUEVO: Limpiar prioridad al iniciar ---
            $this->limpiarYReajustarPrioridad($id, $pdo);
            echo json_encode(['success' => true, 'message' => 'Servicio iniciado en rampa libre.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo iniciar el servicio.']);
        }
    }

    // Helper para limpiar prioridad y reajustar ranking
    private function limpiarYReajustarPrioridad($id_orden_finalizada, $pdo)
    {
        $act = $pdo->prepare("SELECT prioridad_adelanto FROM ordenes WHERE id_orden = ?");
        $act->execute([$id_orden_finalizada]);
        $prioActual = (int)($act->fetchColumn() ?: 0);

        if ($prioActual > 0) {
            $pdo->prepare("UPDATE ordenes SET prioridad_adelanto = 0 WHERE id_orden = ?")
                ->execute([$id_orden_finalizada]);

            $pdo->prepare("UPDATE ordenes SET prioridad_adelanto = prioridad_adelanto - 1 WHERE prioridad_adelanto > ? AND DATE(fecha_creacion) = CURDATE()")
                ->execute([$prioActual]);
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

        $stmt = $pdo->prepare("UPDATE ordenes SET estado = 'POR_COBRAR', fecha_fin_proceso = NOW() WHERE id_orden = ? AND estado = 'EN_PROCESO'");
        if ($stmt->execute([$id])) {
            // --- NUEVO: Manejo de cierre diferido de rampas ---
            // Obtener la rampa que usaba esta orden
            $ord = $pdo->prepare("SELECT id_rampa FROM ordenes WHERE id_orden = ?");
            $ord->execute([$id]);
            $r_data = $ord->fetch();
            if ($r_data && $r_data['id_rampa']) {
                $id_r = $r_data['id_rampa'];
                // Verificar si el cajero programó un cierre (INACTIVA o DESCANSO) para esta rampa
                $rmp = $pdo->prepare("SELECT proximo_estado FROM rampas WHERE id_rampa = ?");
                $rmp->execute([$id_r]);
                $rampa = $rmp->fetch();
                if ($rampa && $rampa['proximo_estado']) {
                    $pdo->prepare("UPDATE rampas SET estado = proximo_estado, proximo_estado = NULL WHERE id_rampa = ?")
                        ->execute([$id_r]);
                }
            }

            $this->checkAutoAvanzarCola($pdo);
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
            // Obtener factores de categoría del vehículo
            $factorT = 1.0;
            $factorP = 1.0;
            if (!empty($input['id_vehiculo'])) {
                $v = $pdo->prepare("SELECT cv.factor_tiempo, cv.factor_precio FROM vehiculos v JOIN categorias_vehiculos cv ON v.id_categoria = cv.id_categoria WHERE v.id_vehiculo = ?");
                $v->execute([$input['id_vehiculo']]);
                $cat = $v->fetch();
                if ($cat) {
                    $factorT = (float)($cat['factor_tiempo'] ?: 1.0);
                    $factorP = (float)($cat['factor_precio'] ?: 1.0);
                }
            }

            $stmt = $pdo->prepare("INSERT INTO ordenes (id_temporada, id_cliente, id_vehiculo, id_promocion, id_usuario_creador, estado, ubicacion_en_local) VALUES (:t, :c, :v, :prm, :u, 'EN_COLA', :ub)");
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
            $tiempo_estimado_total = 0;
            foreach ($input['servicios'] as $s) {
                // Obtener datos base del servicio desde DB para mayor seguridad
                $sx = $pdo->prepare("SELECT precio_base, tiempo_estimado FROM servicios WHERE id_servicio = ?");
                $sx->execute([$s['id_servicio']]);
                $srv = $sx->fetch();

                if ($srv) {
                    $precioFactorizado = round($srv['precio_base'] * $factorP, 2);
                    $tiempoFactorizado = ceil($srv['tiempo_estimado'] * $factorT);

                    $sub = $precioFactorizado * ($s['cantidad'] ?? 1);
                    $totalServ += $sub;
                    $tiempo_estimado_total += $tiempoFactorizado;

                    $pdo->prepare("INSERT INTO detalle_orden (id_orden, id_servicio, cantidad, precio_unitario, subtotal) VALUES (:o, :s, :c, :pu, :st)")
                        ->execute([
                            ':o' => $idOrden,
                            ':s' => $s['id_servicio'],
                            ':c' => $s['cantidad'] ?? 1,
                            ':pu' => $precioFactorizado,
                            ':st' => $sub
                        ]);
                }
            }

            // Descuentos (Copy from Operaciones logic)
            $descPromo = 0;
            if (!empty($input['id_promocion'])) {
                $p = $pdo->prepare("SELECT * FROM promociones WHERE id_promocion = ? AND estado = 1");
                $p->execute([$input['id_promocion']]);
                $promo = $p->fetch();
                if ($promo) {
                    // Validar si es de un solo uso
                    if ($promo['solo_una_vez_por_cliente'] == 1) {
                        $p_uso = $pdo->prepare("SELECT 1 FROM historial_uso_promociones WHERE id_promocion = ? AND id_cliente = ?");
                        $p_uso->execute([$input['id_promocion'], $input['id_cliente']]);
                        if ($p_uso->fetch()) {
                            throw new \Exception('El cliente ya utilizó esta promoción anteriormente.');
                        }
                    }

                    $descPromo = ($promo['tipo_descuento'] === 'PORCENTAJE') ? round($totalServ * ($promo['valor'] / 100), 2) : min($promo['valor'], $totalServ);
                    $pdo->prepare("INSERT IGNORE INTO historial_uso_promociones (id_promocion, id_cliente) VALUES (?, ?)")
                        ->execute([$input['id_promocion'], $input['id_cliente']]);
                }
            }
            $descPuntos = 0;
            if (!empty($input['canjear_puntos']) && $input['id_cliente'] != 1) {
                $meta = (int)($config['meta_puntos_canje'] ?? 10);
                $stmtC = $pdo->prepare("SELECT puntos_acumulados FROM clientes WHERE id_cliente = ?");
                $stmtC->execute([$input['id_cliente']]);
                $ptsActuales = (int)$stmtC->fetchColumn();

                if ($ptsActuales >= $meta) {
                    $descPuntos = $totalServ - $descPromo; // Descuento Total del servicio
                    $pdo->prepare("UPDATE clientes SET puntos_acumulados = puntos_acumulados - ?, ya_canjeo_temporada_actual = 1 WHERE id_cliente = ?")
                        ->execute([$meta, $input['id_cliente']]);
                }
            }

            // Acumular puntos por servicios válidos (SOLO CÁLCULO PARA EL FRONTEND)
            // Ya no sumamos puntos al cliente aquí para evitar confusión en el dashboard
            // Se sumarán en finalizarorden()

            $totalFinal = max($totalServ - $descPromo - $descPuntos, 0);
            $tiempo_estimado_total = max((int)$tiempo_estimado_total, 0);

            $estado_pago = 'PENDIENTE';
            if (!empty($input['pago_anticipado']) && $input['pago_anticipado']) {
                $estado_pago = 'PAGADO';
                require_once __DIR__ . '/../../models/CajaSesion.php';
                $cajaModel = new \CajaSesion($pdo);
                $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);

                if ($cajaActiva) {
                    $pdo->prepare("INSERT INTO pagos_orden (id_orden, metodo_pago, monto) VALUES (:o, :m, :mo)")
                        ->execute([':o' => $idOrden, ':m' => $input['metodo_pago'], ':mo' => $totalFinal]);
                    $pdo->prepare("UPDATE ordenes SET estado_pago = 'PAGADO', id_caja_sesion = :caja WHERE id_orden = :id")
                        ->execute([':caja' => $cajaActiva['id_sesion'], ':id' => $idOrden]);
                }
            }

            $pdo->prepare("UPDATE ordenes SET total_servicios = :ts, descuento_promo = :dp, descuento_puntos = :dpt, total_final = :tf, estado_pago = :ep, estado = 'EN_COLA', tiempo_total_estimado = :tte WHERE id_orden = :id")
                ->execute([':ts' => $totalServ, ':dp' => $descPromo, ':dpt' => $descPuntos, ':tf' => $totalFinal, ':ep' => $estado_pago, ':tte' => $tiempo_estimado_total, ':id' => $idOrden]);

            $this->checkAutoAvanzarCola($pdo);

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

        $pagadoStmt = $pdo->prepare("SELECT SUM(monto) as pagado FROM pagos_orden WHERE id_orden = ?");
        $pagadoStmt->execute([$input['id_orden']]);
        $totalPagado = (float)($pagadoStmt->fetch()['pagado'] ?? 0);
        $saldoPendiente = (float)$orden['total_final'] - $totalPagado;

        if ($saldoPendiente > 0 && empty($input['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Selecciona un método de pago para cubrir el saldo restante de S/ ' . number_format($saldoPendiente, 2)]);
            return;
        }

        if ($saldoPendiente > 0) {
            $model->registrarPago($input['id_orden'], $input['metodo_pago'], $saldoPendiente);
        }

        $model->cambiarEstado($input['id_orden'], 'FINALIZADO', ['id_usuario_cajero' => $_SESSION['user']['id'], 'estado_pago' => 'PAGADO']);

        // Ligar la transacción a la sesión de caja
        $stmtV = $pdo->prepare("UPDATE ordenes SET id_caja_sesion = ?, estado_pago = 'PAGADO' WHERE id_orden = ?");
        $stmtV->execute([$cajaActiva['id_sesion'], $input['id_orden']]);

        // --- SUMAR PUNTOS AL FINALIZAR ---
        if ($orden['id_cliente'] != 1) { // 1 es Publico General
            $stmtPts = $pdo->prepare("
                SELECT SUM(d.cantidad) 
                FROM detalle_orden d 
                JOIN servicios s ON d.id_servicio = s.id_servicio 
                WHERE d.id_orden = ? AND s.acumula_puntos = 1
            ");
            $stmtPts->execute([$input['id_orden']]);
            $ganados = (int)$stmtPts->fetchColumn();
            if ($ganados > 0) {
                $pdo->prepare("UPDATE clientes SET puntos_acumulados = puntos_acumulados + ? WHERE id_cliente = ?")
                    ->execute([$ganados, $orden['id_cliente']]);
            }
        }

        // Decidir si imprime (Si hubo un pago nuevo, o es totalmente nueva, imprimir)
        $imprimir = ($saldoPendiente > 0 || $totalPagado == 0);

        echo json_encode(['success' => true, 'message' => '¡Orden #' . $input['id_orden'] . ' finalizada y puntos acreditados!', 'imprimir_ticket' => $imprimir]);
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
            echo json_encode(['success' => true, 'message' => "¡Venta directa #$idOrden por S/ " . number_format($totalProd, 2) . " procesada!", 'id_orden' => $idOrden]);
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
            $this->checkAutoAvanzarCola($pdo);
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

    public function abrir_sesion_caja()
    {
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

    public function resumen_sesion_caja()
    {
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

    public function cerrar_sesion_caja()
    {
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
    // ════════════════════════════════════════
    // HELPER: AVANZAR COLA (basado en rampas ACTIVAS)
    // ════════════════════════════════════════
    private function checkAutoAvanzarCola($pdo)
    {
        // Obtener rampas ACTIVAS (no INACTIVAS ni DESCANSO)
        require_once __DIR__ . '/../../models/Rampa.php';
        $rampaModel = new \Rampa($pdo);
        $rampasActivas = $rampaModel->getRampasActivas();
        $limite_bahias = count($rampasActivas);

        if ($limite_bahias === 0) return; // No hay rampas activas, nadie procesa

        // Contar órdenes EN_PROCESO
        $activos = (int)$pdo->query("SELECT COUNT(*) as t FROM ordenes WHERE estado = 'EN_PROCESO'")->fetch()['t'];

        if ($activos < $limite_bahias) {
            $espacios_libres = $limite_bahias - $activos;

            // Buscar órdenes en cola respetando PRIORIDAD primero, luego FIFO
            $stmt_cola = $pdo->prepare(
                "SELECT id_orden FROM ordenes 
                 WHERE estado IN ('EN_COLA', 'EN_ESPERA') 
                 ORDER BY CASE WHEN prioridad_adelanto > 0 THEN prioridad_adelanto ELSE 999999 END ASC, 
                          fecha_creacion ASC 
                 LIMIT ?"
            );
            $stmt_cola->bindValue(1, $espacios_libres, \PDO::PARAM_INT);
            $stmt_cola->execute();
            $en_cola = $stmt_cola->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($en_cola)) {
                foreach ($en_cola as $row) {
                    // Asignar rampa libre si existe
                    $id_rampa_libre = $rampaModel->getPrimeraRampaLibre();
                    if (!$id_rampa_libre) break;

                    $pdo->prepare(
                        "UPDATE ordenes SET estado = 'EN_PROCESO', fecha_inicio_proceso = NOW(), id_rampa = ? WHERE id_orden = ?"
                    )->execute([$id_rampa_libre, $row['id_orden']]);

                    // Limpiar prioridad y reajustar ranking para los que quedan
                    $this->limpiarYReajustarPrioridad($row['id_orden'], $pdo);
                }
            }
        }
    }

    // ════════════════════════════════════════
    // API: PANEL DE RAMPAS (CAJERO)
    // ════════════════════════════════════════

    // API: OBTENER RAMPAS CON ESTADO PARA EL CAJERO
    public function getrampas()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        require_once __DIR__ . '/../../models/Rampa.php';
        $rampaModel = new \Rampa($pdo);

        // Operarios para asignación
        $operarios = $pdo->query(
            "SELECT id_usuario, nombres, dni FROM usuarios WHERE id_rol IN (2,3) AND estado = 1 ORDER BY nombres"
        )->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            'success'  => true,
            'rampas'   => $rampaModel->getAll(),
            'operarios' => $operarios
        ]);
    }

    // API: CAJERO ACTUALIZA ESTADO/OPERADOR DE RAMPA
    public function actualizarrampa()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_rampa']) || empty($input['estado'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $estados_validos = ['ACTIVA', 'INACTIVA', 'DESCANSO'];
        if (!in_array($input['estado'], $estados_validos)) {
            echo json_encode(['success' => false, 'message' => 'Estado inválido.']);
            return;
        }

        require_once __DIR__ . '/../../models/Rampa.php';
        $model = new \Rampa($pdo);

        // --- NUEVO: Validación de rampa ocupada para cierre diferido ---
        $ocupada = (int)$pdo->prepare("SELECT COUNT(*) FROM ordenes WHERE id_rampa = ? AND estado = 'EN_PROCESO'")->execute([$input['id_rampa']]) ? $pdo->prepare("SELECT COUNT(*) FROM ordenes WHERE id_rampa = ? AND estado = 'EN_PROCESO'")->execute([$input['id_rampa']]) : 0; // Simplified count check
        // Repitiendo la lógica de conteo que ya tenemos en el modelo para ser precisos
        $sqlOcup = "SELECT COUNT(*) FROM ordenes WHERE id_rampa = ? AND estado = 'EN_PROCESO'";
        $stOcup = $pdo->prepare($sqlOcup);
        $stOcup->execute([$input['id_rampa']]);
        $estaOcupada = (int)$stOcup->fetchColumn() > 0;

        if ($estaOcupada && $input['estado'] !== 'ACTIVA') {
            // La rampa está trabajando, no podemos apagarla de golpe. 
            // La guardamos como "próximo estado"
            $pdo->prepare("UPDATE rampas SET proximo_estado = ? WHERE id_rampa = ?")
                ->execute([$input['estado'], $input['id_rampa']]);

            echo json_encode([
                'success' => true,
                'message' => 'AVISO: La rampa está ocupada. Se marcará como ' . $input['estado'] . ' automáticamente al terminar el lavado actual.'
            ]);
            return;
        }

        // Si no está ocupada, o si la estamos volviendo a activar, limpiamos proximo_estado por si acaso
        $pdo->prepare("UPDATE rampas SET proximo_estado = NULL WHERE id_rampa = ?")->execute([$input['id_rampa']]);

        // --- NUEVO: Validación de operario único ---
        if (!empty($input['id_operador'])) {
            $stmtOp = $pdo->prepare("SELECT numero FROM rampas WHERE id_operador = ? AND id_rampa != ?");
            $stmtOp->execute([$input['id_operador'], $input['id_rampa']]);
            $yaAsignado = $stmtOp->fetch();
            if ($yaAsignado) {
                echo json_encode(['success' => false, 'message' => "El operario ya está asignado a la Rampa {$yaAsignado['numero']}."]);
                return;
            }
        }

        $ok = $model->actualizarEstado(
            (int)$input['id_rampa'],
            $input['estado'],
            !empty($input['id_operador']) ? (int)$input['id_operador'] : null,
            $input['motivo'] ?? null
        );

        // Si una rampa quedó activa, intentar avanzar la cola
        if ($ok && $input['estado'] === 'ACTIVA') {
            $this->checkAutoAvanzarCola($pdo);
        }

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Rampa actualizada.' : 'Error al actualizar rampa.'
        ]);
    }

    // ════════════════════════════════════════
    // API: ADELANTAR ORDEN EN COLA
    // ════════════════════════════════════════
    public function adelantarorden()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        $id_orden = (int)($input['id_orden'] ?? 0);
        $posicion  = $input['posicion'] ?? 'inicio'; // 'inicio' o un número de posición

        if (!$id_orden) {
            echo json_encode(['success' => false, 'message' => 'ID de orden inválido.']);
            return;
        }

        // Verificar que la orden esté EN_COLA
        $stmt = $pdo->prepare("SELECT id_orden, estado, fecha_creacion FROM ordenes WHERE id_orden = ? AND estado = 'EN_COLA'");
        $stmt->execute([$id_orden]);
        $orden = $stmt->fetch();

        if (!$orden) {
            echo json_encode(['success' => false, 'message' => 'La orden no está en cola o no existe.']);
            return;
        }

        try {
            $pdo->beginTransaction();

            // 1. Desplazar prioridades existentes (la #1 pasa a ser #2, la #2 a #3, etc.)
            // Solo afectamos a las órdenes que siguen en cola o espera
            $pdo->exec("UPDATE ordenes SET prioridad_adelanto = prioridad_adelanto + 1 WHERE prioridad_adelanto > 0 AND estado IN ('EN_COLA', 'EN_ESPERA')");

            // 2. Definir fecha_creacion para mantener el orden FIFO interno si fuera necesario
            $firstInQueue = $pdo->query(
                "SELECT MIN(fecha_creacion) as min_fecha FROM ordenes WHERE estado IN ('EN_COLA','EN_ESPERA') AND id_orden != $id_orden"
            )->fetch();

            if ($firstInQueue && $firstInQueue['min_fecha']) {
                $nuevaFecha = date('Y-m-d H:i:s', strtotime($firstInQueue['min_fecha']) - 60);
            } else {
                $nuevaFecha = $orden['fecha_creacion'];
            }

            // 3. Asignar prioridad #1 a la orden que estamos adelantando
            $stmtUpd = $pdo->prepare("UPDATE ordenes SET fecha_creacion = ?, prioridad_adelanto = 1 WHERE id_orden = ?");
            $stmtUpd->execute([$nuevaFecha, $id_orden]);

            $pdo->commit();

            // Intentar avanzarla inmediatamente si hay rampa libre
            $this->checkAutoAvanzarCola($pdo);

            // Leer estado actual después del avance
            $nueva = $pdo->prepare("SELECT estado, prioridad_adelanto FROM ordenes WHERE id_orden = ?");
            $nueva->execute([$id_orden]);
            $rowNueva = $nueva->fetch();
            $nuevoEstado = $rowNueva['estado'];

            $msg = $nuevoEstado === 'EN_PROCESO'
                ? "¡Orden #$id_orden adelantada y pasada a proceso inmediatamente!"
                : "¡Orden #$id_orden puesta en PRIORIDAD #1! (Las anteriores bajaron un puesto)";

            echo json_encode(['success' => true, 'message' => $msg, 'nuevo_estado' => $nuevoEstado]);
        } catch (\Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Error al adelantar orden: ' . $e->getMessage()]);
        }
    }

    // API: QUITAR PRIORIDAD DE UNA ORDEN
    public function quitar_prioridad()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $id_orden = (int)($input['id_orden'] ?? 0);

        if (!$id_orden) {
            echo json_encode(['success' => false, 'message' => 'ID de orden inválido.']);
            return;
        }

        // Obtener la prioridad actual antes de borrarla
        $act = $pdo->prepare("SELECT prioridad_adelanto FROM ordenes WHERE id_orden = ?");
        $act->execute([$id_orden]);
        $prioActual = (int)($act->fetchColumn() ?: 0);

        // Resetear prioridad de la orden actual
        $pdo->prepare("UPDATE ordenes SET prioridad_adelanto = 0 WHERE id_orden = ?")
            ->execute([$id_orden]);

        // Cerrar el hueco: Las prioridades que eran mayores a la que quitamos, ahora bajan 1 nivel
        if ($prioActual > 0) {
            $pdo->prepare("UPDATE ordenes SET prioridad_adelanto = prioridad_adelanto - 1 WHERE prioridad_adelanto > ? AND DATE(fecha_creacion) = CURDATE()")
                ->execute([$prioActual]);
        }

        echo json_encode(['success' => true, 'message' => 'Prioridad removida. El ranking se ha reajustado automáticamente.']);
    }

    // API: SOLICITAR APERTURA DE CAJA AL ADMIN
    public function solicitar_apertura()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');

        $idUsuario = $_SESSION['user']['id'];

        try {
            // Verificar si ya hay una petición pendiente de este usuario hoy
            $check = $pdo->prepare("SELECT id_solicitud FROM solicitudes_caja WHERE id_usuario = ? AND estado = 'PENDIENTE' AND DATE(fecha_solicitud) = CURDATE()");
            $check->execute([$idUsuario]);
            if ($check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Ya tienes una solicitud pendiente enviada hoy.']);
                return;
            }

            $pdo->prepare("INSERT INTO solicitudes_caja (id_usuario, estado, fecha_solicitud) VALUES (?, 'PENDIENTE', NOW())")
                ->execute([$idUsuario]);

            echo json_encode(['success' => true, 'message' => 'Solicitud enviada correctamete.']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // API: CHECK CAJA ABIERTA (USADO POR EL MODAL BLOQUEANTE)
    public function check_caja_abierta()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        require_once __DIR__ . '/../../models/CajaSesion.php';
        $cajaModel = new \CajaSesion($pdo);
        $cajaActiva = $cajaModel->getCajaAbierta($_SESSION['user']['id']);

        echo json_encode(['abierta' => $cajaActiva ? true : false]);
    }

    // ════════════════════════════════════════
    // API: SOLICITUDES DE EMPLEADO (Perfil)
    // ════════════════════════════════════════

    public function solicitar_permiso()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $stmt = $pdo->prepare("INSERT INTO permisos_empleados (id_usuario, id_admin_registrador, tipo, fecha_inicio, fecha_fin, motivo, estado) VALUES (:id_usr, :id_adm, :tipo, :ini, :fin, :motivo, 'PENDIENTE')");
            $stmt->execute([
                ':id_usr' => $_SESSION['user']['id'],
                ':id_adm' => $_SESSION['user']['id'],
                ':tipo' => $input['tipo'] ?? 'PERMISO',
                ':ini' => $input['desde'],
                ':fin' => $input['hasta'],
                ':motivo' => $input['motivo']
            ]);
            echo json_encode(['success' => true, 'message' => 'Solicitud enviada correctamente. El administrador la revisará.']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al solicitar: ' . $e->getMessage()]);
        }
    }

    public function solicitar_adelanto()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $stmt = $pdo->prepare("INSERT INTO pagos_empleados (id_usuario, id_admin_registrador, tipo, monto, periodo, estado, fecha_programada, observaciones) VALUES (:id_usr, :id_adm, 'ADELANTO', :monto, :periodo, 'PENDIENTE', CURDATE(), :obs)");
            $stmt->execute([
                ':id_usr' => $_SESSION['user']['id'],
                ':id_adm' => $_SESSION['user']['id'],
                ':monto' => $input['monto'],
                ':periodo' => date('Y-m'),
                ':obs' => $input['motivo']
            ]);
            echo json_encode(['success' => true, 'message' => 'Solicitud de adelanto enviada correctamente.']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al solicitar: ' . $e->getMessage()]);
        }
    }
}

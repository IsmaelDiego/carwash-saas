<?php

namespace Controllers\Admin;

class DashboardController
{
    private $pdo;

    public function __construct()
    {
        requireRole(1);
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $dashData = $this->getDashboardData();
        require VIEW_PATH . '/admin/dashboard.view.php';
    }

    private function getDashboardData(): array
    {
        $data = [];

        // ═══ CONTADORES PRINCIPALES (1 sola query en vez de 6) ═══
        $stmt = $this->pdo->query("
            SELECT 
                (SELECT COUNT(*) FROM clientes) as total_clientes,
                (SELECT COUNT(*) FROM vehiculos) as total_vehiculos,
                (SELECT COUNT(*) FROM servicios WHERE estado = 1) as total_servicios,
                (SELECT COUNT(*) FROM promociones WHERE estado = 1) as total_promociones,
                (SELECT COUNT(*) FROM productos WHERE stock_actual > 0) as total_productos,
                (SELECT COALESCE(SUM(puntos_acumulados), 0) FROM clientes) as total_puntos_clientes,
                (SELECT COUNT(*) FROM usuarios WHERE estado = 1) as total_empleados_activos,
                (SELECT COUNT(*) FROM pagos_empleados WHERE estado = 'PENDIENTE') as pagos_pendientes,
                (SELECT COUNT(*) FROM permisos_empleados WHERE estado = 'PENDIENTE') as permisos_pendientes,
                (SELECT COUNT(*) FROM tokens_seguridad WHERE usado = 0 AND fecha_expiracion > NOW()) as tokens_activos,
                (SELECT COUNT(*) FROM clientes WHERE estado_whatsapp = 1 AND telefono IS NOT NULL AND telefono != '') as clientes_whatsapp,
                (SELECT COUNT(*) FROM temporadas) as total_temporadas,
                (SELECT COUNT(*) FROM caja_sesiones WHERE estado = 'ABIERTA') as cajas_abiertas
        ");
        $counters = $stmt->fetch(\PDO::FETCH_ASSOC);
        $data = array_merge($data, $counters);

        // ═══ ÓRDENES HOY (1 query) ═══
        $stmt = $this->pdo->query("SELECT
            COUNT(*) as total_hoy,
            COALESCE(SUM(CASE WHEN estado = 'EN_COLA' THEN 1 ELSE 0 END), 0) as en_cola,
            COALESCE(SUM(CASE WHEN estado = 'EN_PROCESO' THEN 1 ELSE 0 END), 0) as en_proceso,
            COALESCE(SUM(CASE WHEN estado = 'POR_COBRAR' THEN 1 ELSE 0 END), 0) as por_cobrar,
            COALESCE(SUM(CASE WHEN estado = 'FINALIZADO' THEN 1 ELSE 0 END), 0) as finalizadas,
            COALESCE(SUM(CASE WHEN estado = 'FINALIZADO' THEN total_final ELSE 0 END), 0) as ingresos_hoy,
            COALESCE(SUM(CASE WHEN estado = 'FINALIZADO' THEN descuento_promo ELSE 0 END), 0) as descuentospromo_hoy,
            COALESCE(SUM(CASE WHEN estado = 'FINALIZADO' THEN descuento_puntos ELSE 0 END), 0) as descuentospuntos_hoy
            FROM ordenes WHERE DATE(fecha_creacion) = CURDATE()");
        $data['ordenes_hoy'] = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        // ═══ INGRESOS MES E HISTÓRICO (2 queries) ═══
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(total_final), 0) as total 
            FROM ordenes WHERE estado = 'FINALIZADO' 
            AND MONTH(fecha_cierre) = MONTH(CURDATE()) AND YEAR(fecha_cierre) = YEAR(CURDATE())");
        $data['ingresos_mes'] = $stmt->fetch()['total'] ?? 0;

        $stmtTot = $this->pdo->query("SELECT COALESCE(SUM(total_final), 0) as total FROM ordenes WHERE estado = 'FINALIZADO'");
        $data['ingresos_totales'] = $stmtTot->fetch()['total'] ?? 0;

        // ═══ TEMPORADA ACTIVA (1 query) ═══
        $stmt = $this->pdo->query("SELECT * FROM temporadas WHERE estado = 1 LIMIT 1");
        $data['temporada_activa'] = $stmt->fetch() ?: null;

        // ═══ DATOS DE LISTAS (1 query combinada) ═══
        $stmt = $this->pdo->query("SELECT id_cliente, nombres, apellidos, telefono, puntos_acumulados, fecha_registro 
                                    FROM clientes ORDER BY id_cliente DESC LIMIT 5");
        $data['ultimos_clientes'] = $stmt->fetchAll() ?: [];

        $stmt = $this->pdo->query("SELECT id_servicio, nombre, precio_base, estado 
                                    FROM servicios ORDER BY estado DESC, nombre ASC LIMIT 5");
        $data['servicios_populares'] = $stmt->fetchAll() ?: [];

        $stmt = $this->pdo->query("SELECT id_promocion, nombre, tipo_descuento, valor, fecha_inicio, fecha_fin, estado 
                                    FROM promociones ORDER BY estado DESC, fecha_inicio DESC LIMIT 4");
        $data['promociones_recientes'] = $stmt->fetchAll() ?: [];

        // ═══ ÚLTIMAS ÓRDENES (1 query) ═══
        $stmt = $this->pdo->query("SELECT o.id_orden, o.estado, o.total_final, o.fecha_creacion,
                c.nombres AS cliente, v.placa
                FROM ordenes o 
                LEFT JOIN clientes c ON o.id_cliente = c.id_cliente 
                LEFT JOIN vehiculos v ON o.id_vehiculo = v.id_vehiculo
                ORDER BY o.id_orden DESC LIMIT 5");
        $data['ultimas_ordenes'] = $stmt->fetchAll() ?: [];

        // ═══ VEHÍCULOS POR CATEGORÍA (1 query) ═══
        $stmt = $this->pdo->query("SELECT cat.nombre, COUNT(v.id_vehiculo) as cantidad 
                                    FROM categorias_vehiculos cat 
                                    LEFT JOIN vehiculos v ON cat.id_categoria = v.id_categoria 
                                    GROUP BY cat.id_categoria, cat.nombre 
                                    ORDER BY cantidad DESC");
        $data['vehiculos_por_categoria'] = $stmt->fetchAll() ?: [];

        // ═══ INGRESOS POR MES - 6 meses (1 sola query en vez de 6) ═══
        $data['ingresos_por_mes'] = $this->getIngresosPorMes();

        return $data;
    }

    private function getIngresosPorMes($meses_rango = 6): array
    {
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE_FORMAT(fecha_cierre, '%Y-%m') as mes_key,
                MONTH(fecha_cierre) as mes_num,
                COALESCE(SUM(total_final), 0) as total
            FROM ordenes 
            WHERE estado = 'FINALIZADO' 
                AND fecha_cierre >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL :rango MONTH), '%Y-%m-01')
            GROUP BY mes_key, mes_num
            ORDER BY mes_key ASC
        ");
        $stmt->execute([':rango' => $meses_rango - 1]); // -1 porque cuenta el actual
        $dbResults = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $meses = [1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'];
        $indexed = [];
        foreach ($dbResults as $r) {
            $indexed[$r['mes_key']] = (float)$r['total'];
        }

        $result = [];
        for ($i = $meses_rango - 1; $i >= 0; $i--) {
            $key = date('Y-m', strtotime("-$i months"));
            $mesNum = (int)date('m', strtotime("-$i months"));
            $result[] = [
                'mes'   => $meses[$mesNum] . ' ' . date('y', strtotime("-$i months")),
                'total' => $indexed[$key] ?? 0
            ];
        }
        return $result;
    }

    public function apifinanzas()
    {
        requireAuth();
        header('Content-Type: application/json');
        
        $rango = isset($_GET['rango']) ? (int)$_GET['rango'] : 6;
        if($rango < 1) $rango = 6;

        // Calcular ingreso del periodo seleccionado (para el KPI)
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(total_final), 0) as total 
            FROM ordenes WHERE estado = 'FINALIZADO' 
            AND fecha_cierre >= DATE_SUB(CURDATE(), INTERVAL :rango MONTH)");
        $stmt->execute([':rango' => $rango - 1]);
        $ingresos_periodo = $stmt->fetch()['total'] ?? 0;

        echo json_encode([
            'success' => true,
            'grafico' => $this->getIngresosPorMes($rango),
            'ingresos_periodo' => $ingresos_periodo
        ]);
        exit;
    }

    public function apinotifications()
    {
        requireAuth();
        header('Content-Type: application/json');

        // Force refresh cache
        if (isset($_SESSION['_notif_cache'])) {
            unset($_SESSION['_notif_cache']);
        }

        require_once APP_PATH . '/helpers/system_helper.php';
        $notifs = getAdminNotifications();

        echo json_encode(['success' => true, 'data' => $notifs]);
        exit;
    }
}

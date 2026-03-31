<?php
namespace Controllers\Admin;

use Exception;
use PDO;

class CajaController {

    public function __construct() {
        requireRole(1); // Solo admin
    }

    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/caja.view.php';
    }

    /**
     * API: Listar arqueos (sesiones de caja)
     */
    public function getarqueos() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        try {
            $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');
            $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

            $sql = "SELECT cs.*, u.nombres as cajero_nombre,
                        (SELECT COALESCE(SUM(po.monto), 0) 
                         FROM pagos_orden po 
                         JOIN ordenes o ON po.id_orden = o.id_orden 
                         WHERE o.id_caja_sesion = cs.id_sesion AND o.estado = 'FINALIZADO') as recaudado_acumulado
                    FROM caja_sesiones cs
                    LEFT JOIN usuarios u ON cs.id_usuario = u.id_usuario
                    WHERE MONTH(cs.fecha_apertura) = :m AND YEAR(cs.fecha_apertura) = :y
                    ORDER BY cs.fecha_apertura DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':m' => $month, ':y' => $year]);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $res]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * API: Ver detalle de una sesión (desglose por métodos de pago)
     */
    public function detallesesion() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID no proporcionado']); return;
        }

        try {
            // 1. Datos básicos
            $stmt = $pdo->prepare("SELECT cs.*, u.nombres as cajero FROM caja_sesiones cs JOIN usuarios u ON cs.id_usuario = u.id_usuario WHERE id_sesion = ?");
            $stmt->execute([$id]);
            $sesion = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sesion) throw new Exception("Sesión no encontrada");

            // 2. Desglose por método de pago
            $sqlMetodos = "SELECT po.metodo_pago, SUM(po.monto) as total
                           FROM pagos_orden po
                           INNER JOIN ordenes o ON po.id_orden = o.id_orden
                           WHERE o.id_caja_sesion = ? AND o.estado = 'FINALIZADO'
                           GROUP BY po.metodo_pago";
            $stmtM = $pdo->prepare($sqlMetodos);
            $stmtM->execute([$id]);
            $metodos = $stmtM->fetchAll(PDO::FETCH_ASSOC);

            // 3. Resumen de productos vendidos en esta sesión
            $sqlProds = "SELECT p.nombre, SUM(do.cantidad) as total_cant, SUM(do.subtotal) as total_monto
                         FROM detalle_orden do
                         INNER JOIN ordenes o ON do.id_orden = o.id_orden
                         INNER JOIN productos p ON do.id_producto = p.id_producto
                         WHERE o.id_caja_sesion = ? AND o.estado = 'FINALIZADO'
                         GROUP BY p.id_producto";
            $stmtP = $pdo->prepare($sqlProds);
            $stmtP->execute([$id]);
            $productos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'sesion' => $sesion,
                'metodos' => $metodos,
                'productos' => $productos
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

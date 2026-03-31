<?php
namespace Controllers\Admin;

use Producto;
use Exception;

class ProductoController {

    public function __construct() {
        requireRole(1); // Solo Admin
    }

    // ==========================================
    // VISTA PRINCIPAL
    // URL: /admin/producto
    // ==========================================
    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/lista_productos.view.php';
    }

    public function lista() { $this->index(); }

    // ==========================================
    // API: OBTENER TODOS (JSON para DataTables)
    // ==========================================
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Producto($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // ==========================================
    // API: ESTADÍSTICAS
    // ==========================================
    public function getstats() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Producto($pdo);
        echo json_encode($model->getEstadisticas());
    }

    // ==========================================
    // API: REGISTRAR PRODUCTO
    // ==========================================
    public function registrar() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        // Validaciones
        if (empty($input['nombre']) || !isset($input['precio_compra']) || !isset($input['precio_venta'])) {
            echo json_encode(['success' => false, 'message' => 'Nombre, precio compra y precio venta son obligatorios.']);
            return;
        }

        if ($input['precio_compra'] < 0 || $input['precio_venta'] < 0) {
            echo json_encode(['success' => false, 'message' => 'Los precios no pueden ser negativos.']);
            return;
        }

        $model = new Producto($pdo);

        if ($model->existeNombre($input['nombre'])) {
            echo json_encode(['success' => false, 'message' => 'Ya existe un producto con ese nombre.']);
            return;
        }

        try {
            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => '¡Producto registrado correctamente!']);
            } else {
                throw new Exception('Error al insertar.');
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // API: EDITAR PRODUCTO
    // ==========================================
    public function editar() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_producto']) || empty($input['nombre'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        $model = new Producto($pdo);

        if ($model->existeNombre($input['nombre'], $input['id_producto'])) {
            echo json_encode(['success' => false, 'message' => 'Otro producto ya tiene ese nombre.']);
            return;
        }

        try {
            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => '¡Producto actualizado!']);
            } else {
                echo json_encode(['success' => true, 'message' => 'Sin cambios detectados.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // API: AJUSTAR STOCK
    // ==========================================
    public function ajustarstock() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_producto']) || !isset($input['cantidad']) || empty($input['tipo'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
            return;
        }

        if ((int)$input['cantidad'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0.']);
            return;
        }

        $model = new Producto($pdo);
        $tipo = strtoupper($input['tipo']);

        if ($tipo === 'SALIDA') {
            $prod = $model->getById($input['id_producto']);
            if ($prod && $prod['stock_actual'] < (int)$input['cantidad']) {
                echo json_encode(['success' => false, 'message' => 'Stock insuficiente. Stock actual: ' . $prod['stock_actual']]);
                return;
            }
        }

        if ($model->ajustarStock($input['id_producto'], $input['cantidad'], $tipo)) {
            $label = $tipo === 'ENTRADA' ? 'ingresadas' : 'retiradas';
            echo json_encode(['success' => true, 'message' => "{$input['cantidad']} unidades $label correctamente."]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ajustar stock.']);
        }
    }

    // ==========================================
    // API: AGREGAR LOTE (Abastecimiento)
    // ==========================================
    public function agregarlote() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_producto']) || empty($input['cantidad']) || !isset($input['precio_compra']) || !isset($input['precio_venta'])) {
            echo json_encode(['success' => false, 'message' => 'Producto, cantidad y precios son requeridos.']);
            return;
        }

        if ((int)$input['cantidad'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0.']);
            return;
        }

        $model = new Producto($pdo);
        try {
            if ($model->agregarLote($input)) {
                echo json_encode(['success' => true, 'message' => "Lote de {$input['cantidad']} unidades registrado exitosamente."]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar el lote.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // API: LISTAR LOTES DE UN PRODUCTO
    // ==========================================
    public function getlotes() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $model = new Producto($pdo);
        echo json_encode(['data' => $model->getLotes($id)]);
    }

    // ==========================================
    // API: TODOS LOS LOTES (vista general)
    // ==========================================
    public function getalllotes() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Producto($pdo);
        echo json_encode(['data' => $model->getAllLotes()]);
    }

    // ==========================================
    // API: ALERTAS DE VENCIMIENTO (para panel Admin)
    // ==========================================
    public function alertasvencimiento() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Producto($pdo);
        $lotes = $model->getAllLotes();

        $alertas = [];
        foreach ($lotes as $l) {
            if ($l['fecha_vencimiento'] && $l['dias_para_vencer'] !== null) {
                if ($l['dias_para_vencer'] <= 0) {
                    $alertas[] = [
                        'tipo' => 'VENCIDO',
                        'producto' => $l['producto_nombre'],
                        'lote' => $l['id_lote'],
                        'cantidad' => $l['cantidad_actual'],
                        'dias' => $l['dias_para_vencer'],
                        'fecha' => $l['fecha_vencimiento']
                    ];
                } elseif ($l['dias_para_vencer'] <= 30) {
                    $alertas[] = [
                        'tipo' => 'POR_VENCER',
                        'producto' => $l['producto_nombre'],
                        'lote' => $l['id_lote'],
                        'cantidad' => $l['cantidad_actual'],
                        'dias' => $l['dias_para_vencer'],
                        'fecha' => $l['fecha_vencimiento']
                    ];
                }
            }
        }
        echo json_encode(['alertas' => $alertas, 'total' => count($alertas)]);
    }

    // ==========================================
    // API: ELIMINAR PRODUCTO
    // ==========================================
    public function eliminar() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_producto'])) {
            echo json_encode(['success' => false, 'message' => 'ID inválido.']);
            return;
        }

        $model = new Producto($pdo);

        try {
            if ($model->eliminar($input['id_producto'])) {
                echo json_encode(['success' => true, 'message' => 'Producto eliminado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar: tiene ventas asociadas.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: el producto tiene registros asociados.']);
        }
    }

    // ==========================================
    // API: REGISTRAR MERMA (Baja de lote)
    // ==========================================
    public function registrarmerma() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        global $pdo;
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['id_lote']) || empty($input['cantidad']) || empty($input['motivo'])) {
            echo json_encode(['success' => false, 'message' => 'Lote, cantidad y motivo son requeridos.']);
            return;
        }

        if ((int)$input['cantidad'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'La cantidad debe ser mayor a 0.']);
            return;
        }

        $model = new Producto($pdo);
        try {
            $resultado = $model->registrarMerma(
                $input['id_lote'],
                (int)$input['cantidad'],
                $input['motivo'],
                $_SESSION['id_usuario'] ?? 1
            );
            echo json_encode([
                'success' => true, 
                'message' => "Merma registrada: {$resultado['cantidad']} unidades dadas de baja. Gasto S/ " . number_format($resultado['monto'], 2) . " registrado automáticamente."
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // API: KARDEX DE UN PRODUCTO
    // ==========================================
    public function getkardex() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        $model = new Producto($pdo);
        echo json_encode(['data' => $model->getKardex($id)]);
    }

    // ==========================================
    // API: KARDEX GLOBAL (últimos movimientos)
    // ==========================================
    public function getkardexglobal() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Producto($pdo);
        echo json_encode(['data' => $model->getKardexGlobal(200)]);
    }
}


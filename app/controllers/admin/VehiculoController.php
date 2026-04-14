<?php
namespace Controllers\Admin;

use Exception;
use Vehiculo;
use Cliente; 
use CategoriaVehiculo;

class VehiculoController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD
    // ==========================================
    public function __construct()
    {
        requireRole(1);
    }

    public function index()
    {
        $this->lista();
    }

    // ==========================================
    // 1. VISTA HTML
    // ==========================================
    public function lista()
    {
        requireAuth();
        global $pdo;

        $vehiculoModel = new Vehiculo($pdo);
        $clienteModel = new Cliente($pdo);

        // Obtenemos datos para llenar los SELECTS en los Modales
        $clientes = $clienteModel->getAll(); 
        $categorias = $vehiculoModel->getCategorias();

        require VIEW_PATH . '/admin/lista_vehiculos.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (API JSON)
    // ==========================================
    public function getall()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $vehiculoModel = new Vehiculo($pdo);
        $data = $vehiculoModel->getAll();

        echo json_encode(['data' => $data]);
    }

    // ==========================================
    // 3. REGISTRAR VEHÍCULO
    // ==========================================
    public function registrarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            // Validaciones
            if (empty($input['id_cliente']) || empty($input['placa']) || empty($input['id_categoria'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Propietario, Placa y Categoría son obligatorios.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Validar Duplicado
            if ($vehiculoModel->existePlaca($input['placa'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'La placa ya está registrada.']);
                return;
            }

            try {
                if ($vehiculoModel->registrar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo registrado correctamente.']);
                } else {
                    throw new Exception("Error al insertar en BD.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 4. EDITAR VEHÍCULO
    // ==========================================
    public function editarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID no identificado.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            try {
                if ($vehiculoModel->editar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo actualizado correctamente.']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Datos guardados (Sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 5. ELIMINAR VEHÍCULO
    // ==========================================
    public function eliminarvehiculo()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID inválido.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            try {
                if ($vehiculoModel->eliminar($input['id_vehiculo'])) {
                    echo json_encode(['success' => true, 'message' => 'Vehículo eliminado exitosamente.']);
                } else {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()]);
            }
            return;
        }
    }

    // ==========================================
    // 6. CATEGORÍAS VISTA
    // ==========================================
    public function categorias()
    {
        requireAuth();
        require VIEW_PATH . '/admin/lista_categorias.view.php';
    }

    // ==========================================
    // 7. CATEGORÍAS API (JSON)
    // ==========================================
    public function getallcategorias()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $categoriaModel = new CategoriaVehiculo($pdo);
        $data = $categoriaModel->getAll();

        echo json_encode(['data' => $data]);
    }

    public function registrarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio.']);
                return;
            }

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->registrar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Categoría registrada correctamente.']);
                } else {
                    throw new Exception("Error al insertar.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }

    public function editarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_categoria']) || empty($input['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
                return;
            }

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->editar($input)) {
                    echo json_encode(['success' => true, 'message' => 'Categoría actualizada.']);
                } else {
                    echo json_encode(['success' => true, 'message' => 'Sin cambios.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }

    public function eliminarcategoria()
    {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            $categoriaModel = new CategoriaVehiculo($pdo);
            try {
                if ($categoriaModel->eliminar($input['id_categoria'])) {
                    echo json_encode(['success' => true, 'message' => 'Categoría eliminada.']);
                } else {
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar, posiblemente tiene vehículos asignados.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        }
    }
}
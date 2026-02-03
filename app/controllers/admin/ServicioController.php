<?php
namespace Controllers\Admin;

use Servicio;
use TipoVehiculo;
use Exception;

class ServicioController
{
    public function __construct() {
        requireAuth();
    }

    public function index() {
        $this->lista();
    }

    /**
     * Mostrar vista principal de servicios
     */
    public function lista() {
        global $pdo;
        
        // Obtener tipos de vehículo para los precios
        $tipoVehiculoModel = new TipoVehiculo($pdo);
        $tiposVehiculo = $tipoVehiculoModel->getActivos();
        
        // Obtener servicios con sus precios
        $servicioModel = new Servicio($pdo);
        $servicios = $servicioModel->getAll();
        
        require VIEW_PATH . '/admin/lista_servicios.view.php';
    }

    /**
     * API: Obtener todos los servicios (para AJAX)
     */
    public function getall() {
        global $pdo;
        header('Content-Type: application/json');

        $servicioModel = new Servicio($pdo);
        $servicios = $servicioModel->getAll();

        echo json_encode(['data' => $servicios]);
    }

    /**
     * API: Obtener un servicio por ID
     */
    public function getone() {
        global $pdo;
        header('Content-Type: application/json');

        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID requerido']);
            return;
        }

        $servicioModel = new Servicio($pdo);
        $servicio = $servicioModel->findById($id);

        if ($servicio) {
            echo json_encode(['success' => true, 'data' => $servicio]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Servicio no encontrado']);
        }
    }

    /**
     * API: Crear nuevo servicio
     */
    public function registrar() 
    {
        requireRole(1); // Solo admin
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre del servicio es obligatorio.']);
                return;
            }

            $servicioModel = new Servicio($pdo);

            if ($servicioModel->existeNombre($data['nombre'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Ya existe un servicio con ese nombre.']);
                return;
            }

            try {
                $id_servicio = $servicioModel->crear($data);
                
                if ($id_servicio) {
                    // Guardar precios por tipo de vehículo
                    if (!empty($data['precios']) && is_array($data['precios'])) {
                        $servicioModel->guardarPrecios($id_servicio, $data['precios']);
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Servicio creado exitosamente!', 'id' => $id_servicio]);
                } else {
                    throw new Exception("No se pudo crear el servicio.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/servicio/lista');
    }

    /**
     * API: Editar servicio
     */
    public function editar() 
    {
        requireRole(1);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_servicio']) || empty($data['nombre'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
                return;
            }

            $servicioModel = new Servicio($pdo);

            // Verificar nombre duplicado
            if ($servicioModel->existeNombre($data['nombre'], $data['id_servicio'])) {
                http_response_code(409);
                echo json_encode(['success' => false, 'message' => 'Ya existe otro servicio con ese nombre.']);
                return;
            }

            try {
                if ($servicioModel->actualizar($data)) {
                    // Actualizar precios
                    if (!empty($data['precios']) && is_array($data['precios'])) {
                        $servicioModel->guardarPrecios($data['id_servicio'], $data['precios']);
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Servicio actualizado correctamente!']);
                } else {
                    throw new Exception("No se detectaron cambios.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/servicio/lista');
    }

    /**
     * API: Eliminar servicio
     */
    public function eliminar() 
    {
        requireRole(1);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_servicio'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID no válido.']);
                return;
            }

            $servicioModel = new Servicio($pdo);

            // Verificar si tiene órdenes vinculadas
            if ($servicioModel->tieneOrdenesVinculadas($data['id_servicio'])) {
                http_response_code(409);
                echo json_encode([
                    'success' => false, 
                    'message' => 'No se puede eliminar: Este servicio tiene órdenes asociadas.'
                ]);
                return;
            }

            try {
                if ($servicioModel->eliminar($data['id_servicio'])) {
                    echo json_encode(['success' => true, 'message' => 'Servicio eliminado correctamente.']);
                } else {
                    throw new Exception("No se pudo eliminar.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/servicio/lista');
    }
}
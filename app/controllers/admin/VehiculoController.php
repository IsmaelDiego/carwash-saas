<?php
// ==========================================================
// 1. DECLARAMOS EL NAMESPACE Y CLASES EXTERNAS
// ==========================================================
namespace Controllers\Admin;

use Vehiculo;
use Exception; // Importamos la clase de Errores nativa de PHP

class VehiculoController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD
    // ==========================================
    public function index() {
        $this->lista();
    }

    // ==========================================
    // 1. LA VISTA (Carga tu HTML y el menú)
    // URL: /admin/cliente/lista
    // ==========================================
    public function lista() {
        requireAuth();
        // Solo cargamos la pantalla. El JS pedirá los datos después.
        require VIEW_PATH . '/admin/lista_vehiculos.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (AJAX para DataTables)
    // URL: /admin/cliente/getall
    // ==========================================

    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $vehiculoModel = new Vehiculo($pdo);
        
        $vehiculos = $vehiculoModel->getAll(); // ¡Toda la magia del JOIN ocurre aquí!

        echo json_encode(['data' => $vehiculos]);
    }

     public function registrarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validaciones básicas
            if (empty($data['placa']) || empty($data['tipo'])  || empty($data['marca']) || empty($data['modelo']) || empty($data['color'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Placa de rodaje, Tipo de vehiculo, Marca, Modelo y Color son obligatorios.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Verificar si la PLaca ya existe
            if ($vehiculoModel->findByPlaca($data['placa'])) {
                http_response_code(409); // Conflicto
                echo json_encode(['success' => false, 'message' => 'Esta Placa ya está registrado como Vehiculo.']);
                return;
            }

            // Intentar guardar
            try {
                if ($vehiculoModel->crearVehiculo($data)) {
                    // ÉXITO: El JS mostrará el Toast Verde
                    echo json_encode(['success' => true, 'message' => 'Vehiculo registrado exitosamente!']);
                } else {
                    throw new Exception("No se pudo insertar en la base de datos.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }

        // Si intentan entrar por la URL directo, los devolvemos a la lista
        header('Location: ' . BASE_URL . '/admin/Vehiculo/lista');
    }

    public function eliminarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar que tengamos un ID
            if (empty($data['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de Vehiculo no válido.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Intentar eliminar
            try {
                if ($vehiculoModel->eliminarVehiculo($data['id_vehiculo'])) {
                    // ÉXITO: Mensaje que aparecerá en el Toast
                    echo json_encode(['success' => true, 'message' => 'El vehiculo ha sido eliminado del sistema exitosamente.']);
                } else {
                    throw new Exception("El cliente no pudo ser eliminado.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                // Control de Error por Clave Foránea (El cliente tiene ventas)
                if ($e->getCode() == 23000) {
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar: Este vehiculo ya tiene registros asociados.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
                }
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/vehiculo/lista');
    }

       public function editarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar campos obligatorios
            if (empty($data['id_vehiculo']) || empty($data['placa']) || empty($data['tipo']) || empty($data['modelo']) || empty($data['marca'] ) || empty($data['color'] )) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'La Placa , Tipo, Modelo, Marca y Color es obligatorio para actualizar.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Intentar actualizar
            try {
                if ($vehiculoModel->actualizarVehiculo($data)) {
                    // ÉXITO: Mensaje que aparecerá en el Toast
                    echo json_encode(['success' => true, 'message' => '¡Los datos del vehiculo se actualizaron correctamente!']);
                } else {
                    throw new Exception("No se detectaron cambios o el registro no existe.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/vehiculo/lista');
    }

}
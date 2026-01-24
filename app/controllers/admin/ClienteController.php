<?php
// ==========================================================
// 1. DECLARAMOS EL NAMESPACE Y CLASES EXTERNAS
// ==========================================================
namespace Controllers\Admin;

use Cliente;   // Importamos tu modelo Cliente
use Exception; // Importamos la clase de Errores nativa de PHP

class ClienteController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD
    // ==========================================
    public function __construct() {
        // Validación de seguridad: Solo el Rol 1 (Admin) puede acceder a este controlador
        requireRole(1); 
    }

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
        require VIEW_PATH . '/admin/lista_clientes.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (AJAX para DataTables)
    // URL: /admin/cliente/getall
    // ==========================================
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $clienteModel = new Cliente($pdo);
        
        // LIMIT 1000 para evitar errores de memoria RAM
        $sql = "SELECT * FROM clientes ORDER BY id_cliente DESC LIMIT 1000"; 
        $stmt = $pdo->query($sql);
        $clientes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Enviamos los datos al Javascript de tu tabla
        echo json_encode(['data' => $clientes]);
    }

    // ==========================================
    // 3. REGISTRAR CLIENTE (AJAX)
    // URL: /admin/cliente/registrarcliente
    // ==========================================
    public function registrarcliente() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // Validaciones básicas
            if (empty($data['dni']) || empty($data['nombres']) || empty($data['telefono_principal'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'DNI, Nombres y Teléfono Principal son obligatorios.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            // Verificar si el DNI ya existe
            if ($clienteModel->findByDni($data['dni'])) {
                http_response_code(409); // Conflicto
                echo json_encode(['success' => false, 'message' => 'Este DNI/RUC ya está registrado como cliente.']);
                return;
            }

            // Intentar guardar
            try {
                if ($clienteModel->crearcliente($data)) {
                    // ÉXITO: El JS mostrará el Toast Verde
                    echo json_encode(['success' => true, 'message' => '¡Cliente registrado exitosamente!']);
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
        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 4. EDITAR CLIENTE (AJAX)
    // URL: /admin/cliente/editarcliente
    // ==========================================
    public function editarcliente() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar campos obligatorios
            if (empty($data['id_cliente']) || empty($data['telefono_principal'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El teléfono principal es obligatorio para actualizar.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            // Intentar actualizar
            try {
                if ($clienteModel->actualizarCliente($data)) {
                    // ÉXITO: Mensaje que aparecerá en el Toast
                    echo json_encode(['success' => true, 'message' => '¡Los datos del cliente se actualizaron correctamente!']);
                } else {
                    throw new Exception("No se detectaron cambios o el registro no existe.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 5. ELIMINAR CLIENTE (AJAX)
    // URL: /admin/cliente/eliminarcliente
    // ==========================================
    public function eliminarcliente() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar que tengamos un ID
            if (empty($data['id_cliente'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de cliente no válido.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            // Intentar eliminar
            try {
                if ($clienteModel->eliminarCliente($data['id_cliente'])) {
                    // ÉXITO: Mensaje que aparecerá en el Toast
                    echo json_encode(['success' => true, 'message' => 'El cliente ha sido eliminado del sistema exitosamente.']);
                } else {
                    throw new Exception("El cliente no pudo ser eliminado.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                // Control de Error por Clave Foránea (El cliente tiene ventas)
                if ($e->getCode() == 23000) {
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar: Este cliente ya tiene ventas o registros asociados.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
                }
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }
}
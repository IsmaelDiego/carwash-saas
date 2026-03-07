<?php
// ==========================================================
// 1. NAMESPACE Y USO DE CLASES
// ==========================================================
namespace Controllers\Admin;

use Exception; // Importamos Excepciones PHP
use Cliente;

class ClienteController
{
    // ==========================================
    // CONSTRUCTOR: SEGURIDAD (Solo Admin)
    // ==========================================
    public function __construct()
    {
        // En V3.2, id_rol 1 = Admin.
        requireRole(1);
    }

    // Método por defecto (Redirige a lista)
    public function index()
    {
        $this->lista();
    }

    // ==========================================
    // 1. VISTA HTML (Renderiza la página)
    // URL: /admin/cliente/lista
    // ==========================================
    public function lista()
    {
        requireAuth();
        // Carga la vista que contiene la tabla HTML y los Scripts JS
        require VIEW_PATH . '/admin/lista_clientes.view.php';
    }

    // ==========================================
    // 2. OBTENER DATOS (API JSON para DataTables)
    // URL: /admin/cliente/getall
    // ==========================================
    public function getall()
    {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');

        $clienteModel = new Cliente($pdo);

        // Obtenemos array de clientes desde el modelo
        $clientes = $clienteModel->getAll();

        // DataTables espera un objeto con la propiedad "data"
        echo json_encode(['data' => $clientes]);
    }

    // ==========================================
    // 3. REGISTRAR CLIENTE (POST JSON)
    // URL: /admin/cliente/registrarcliente
    // ==========================================
    public function registrarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer cuerpo JSON
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // VALIDACIÓN V3.2: DNI, Nombres, Apellidos y Teléfono son requeridos
            // Nota: 'telefono_principal' ya no existe, ahora es 'telefono'
            if (empty($data['dni']) || empty($data['nombres']) || empty($data['apellidos']) || empty($data['telefono'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'DNI, Nombres, Apellidos y Teléfono son obligatorios.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            // Verificar duplicados por DNI
            if ($clienteModel->findByDni($data['dni'])) {
                http_response_code(409); // Conflict
                echo json_encode(['success' => false, 'message' => 'Este DNI ya está registrado.']);
                return;
            }

            try {
                // Llamar al método crear del modelo
                if ($clienteModel->crearcliente($data)) {
                    echo json_encode(['success' => true, 'message' => '¡Cliente registrado correctamente!']);
                } else {
                    throw new Exception("Error al insertar en BD.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 4. EDITAR CLIENTE (POST JSON)
    // URL: /admin/cliente/editarcliente
    // ==========================================
    public function editarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            // Validación V3.2: ID y Teléfono obligatorios
            if (empty($data['id_cliente']) || empty($data['telefono'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El teléfono es obligatorio para actualizar.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            try {
                if ($clienteModel->actualizarCliente($data)) {
                    echo json_encode(['success' => true, 'message' => '¡Cliente actualizado correctamente!']);
                } else {
                    // Si retorna false, puede ser que no hubo cambios reales en los datos
                    // pero para el usuario es un "Éxito" o un "Sin cambios".
                    echo json_encode(['success' => true, 'message' => 'Datos guardados (Sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }

    // ==========================================
    // 5. ELIMINAR CLIENTE (POST JSON)
    // URL: /admin/cliente/eliminarcliente
    // ==========================================
    public function eliminarcliente()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_cliente'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de cliente inválido.']);
                return;
            }

            $clienteModel = new Cliente($pdo);

            try {
                // Intentar eliminar (El modelo retorna false si falla por FK)
                if ($clienteModel->eliminarCliente($data['id_cliente'])) {
                    echo json_encode(['success' => true, 'message' => 'Cliente eliminado exitosamente.']);
                } else {
                    // Mensaje amigable cuando falla por FK
                    http_response_code(409);
                    echo json_encode(['success' => false, 'message' => 'No se puede eliminar: El cliente tiene historial de ventas o puntos.']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error crítico: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/cliente/lista');
    }
    public function cambiarestadowhatsapp()
    {
        requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_cliente'])) {
                echo json_encode(['success' => false, 'message' => 'ID faltante']);
                return;
            }

            // Validar estado (asegurar que sea 1 o 0)
            $nuevoEstado = $data['estado'] == 1 ? 1 : 0;

            try {
                // Actualización directa pequeña
                $sql = "UPDATE clientes SET estado_whatsapp = :estado WHERE id_cliente = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':estado' => $nuevoEstado, ':id' => $data['id_cliente']]);

                echo json_encode(['success' => true, 'message' => 'Estado de WhatsApp actualizado.']);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error BD']);
            }
            return;
        }
    }
}

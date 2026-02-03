<?php
// ==========================================================
// 1. DECLARAMOS EL NAMESPACE Y CLASES EXTERNAS
// ==========================================================
namespace Controllers\Admin;

use Exception; // Importamos la clase de Errores nativa de PHP
use Vehiculo;

class VehiculoController
{
    // ==========================================
    // CONSTRUCTOR / INDEX
    // ==========================================
    public function index() {
        $this->lista();
    }

    // ==========================================
    // 1. LA VISTA (Carga tu HTML)
    // URL: /admin/vehiculo/lista
    // ==========================================
    public function lista() {
        requireAuth();
        // Solo cargamos la pantalla. El JS pedirá los datos después.
        require VIEW_PATH . '/admin/lista_vehiculos.view.php';
    }
public function getall() {
    requireAuth();
    global $pdo;

    header('Content-Type: application/json');

    $vehiculoModel = new Vehiculo($pdo);
    $vehiculos = $vehiculoModel->getAll();

    echo json_encode([
        'data' => $vehiculos
    ]);
    exit;
}



    // ==========================================
    // 3. NUEVO: OBTENER TIPOS (Para el Select)
    // URL: /admin/vehiculo/gettipos
    // ==========================================
// En controllers/admin/VehiculoController.php

public function gettipos() {
    requireAuth(); // Tu seguridad
    global $pdo;
    
    // Limpiamos cualquier salida previa para evitar errores de JSON
    ob_clean(); 
    header('Content-Type: application/json');

    try {
        // Asegúrate de usar el Namespace correcto
        $vehiculoModel = new Vehiculo($pdo);
        $tipos = $vehiculoModel->obtenerTiposVehiculo();
        
        echo json_encode(['data' => $tipos]);
        
    } catch (\Exception $e) {
        // Si falla, enviamos el error al JS para verlo en consola
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

    // ==========================================
    // 4. REGISTRAR VEHÍCULO
    // URL: /admin/vehiculo/registrarvehiculo
    // ==========================================
    public function registrarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            // Leer JSON enviado por JS
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            // --- CAMBIO IMPORTANTE: Validamos 'tipo_vehiculo_id' en vez de 'tipo' ---
            if (empty($data['placa']) || empty($data['tipo_vehiculo_id']) || empty($data['marca']) || empty($data['modelo']) || empty($data['color'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios (Placa, Tipo, Marca, Modelo, Color).']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Verificar si la Placa ya existe
            if ($vehiculoModel->findByPlaca($data['placa'])) {
                http_response_code(409); // Conflicto
                echo json_encode(['success' => false, 'message' => 'Esta Placa ya está registrada en el sistema.']);
                return;
            }

            // Intentar guardar
            try {
                if ($vehiculoModel->crearVehiculo($data)) {
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

        header('Location: ' . BASE_URL . '/admin/vehiculo/lista');
    }

    // ==========================================
    // 5. EDITAR VEHÍCULO
    // URL: /admin/vehiculo/editarvehiculo
    // ==========================================
    public function editarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            // --- CAMBIO IMPORTANTE: Validamos 'tipo_vehiculo_id' ---
            if (empty($data['id_vehiculo']) || empty($data['placa']) || empty($data['tipo_vehiculo_id']) || empty($data['modelo']) || empty($data['marca'] ) || empty($data['color'] )) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios para la actualización.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            // Validar duplicidad de placa (Opcional, pero recomendado si cambian la placa)
            // Aquí podrías agregar la lógica "existePlaca" excluyendo el ID actual si quisieras.

            try {
                if ($vehiculoModel->actualizarVehiculo($data)) {
                    echo json_encode(['success' => true, 'message' => '¡Los datos del vehiculo se actualizaron correctamente!']);
                } else {
                    // Si devuelve false, puede ser que no hubo cambios o falló SQL, 
                    // pero para la UX solemos decir que fue exitoso o "Sin cambios".
                    echo json_encode(['success' => true, 'message' => 'Datos actualizados (o sin cambios detectados).']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error en base de datos: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/vehiculo/lista');
    }

    // ==========================================
    // 6. ELIMINAR VEHÍCULO
    // URL: /admin/vehiculo/eliminarvehiculo
    // ==========================================
    public function eliminarvehiculo() 
    {
        requireAuth(); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');

            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['id_vehiculo'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID de Vehiculo no válido.']);
                return;
            }

            $vehiculoModel = new Vehiculo($pdo);

            try {
                if ($vehiculoModel->eliminarVehiculo($data['id_vehiculo'])) {
                    echo json_encode(['success' => true, 'message' => 'El vehiculo ha sido eliminado correctamente.']);
                } else {
                    throw new Exception("El vehículo no pudo ser eliminado.");
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
            }
            return;
        }

        header('Location: ' . BASE_URL . '/admin/vehiculo/lista');
    }
}
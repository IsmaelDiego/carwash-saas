<?php
namespace Controllers\Admin;

use Servicio;

class ServicioController {

    public function __construct() {
        requireRole(1); // Solo Admin
    }

    public function index() {
        requireAuth();
        require VIEW_PATH . '/admin/lista_servicios.view.php';
    }

    // API: GET ALL
    public function getall() {
        requireAuth();
        global $pdo;
        header('Content-Type: application/json');
        $model = new Servicio($pdo);
        echo json_encode(['data' => $model->getAll()]);
    }

    // API: REGISTRAR
    public function registrarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['nombre']) || empty($input['precio_base'])) {
                echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']); return;
            }

            $model = new Servicio($pdo);
            
            // Validar nombre único
            if ($model->existeNombre($input['nombre'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre ya existe.']); return;
            }

            if ($model->registrar($input)) {
                echo json_encode(['success' => true, 'message' => 'Servicio creado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al guardar.']);
            }
        }
    }

    // API: EDITAR (Recibe id_servicio)
    public function editarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id_servicio'])) {
                echo json_encode(['success' => false, 'message' => 'ID no identificado.']); return;
            }

            $model = new Servicio($pdo);
            // Validar nombre único excluyendo el actual
            if ($model->existeNombre($input['nombre'], $input['id_servicio'])) {
                echo json_encode(['success' => false, 'message' => 'El nombre ya existe.']); return;
            }

            if ($model->editar($input)) {
                echo json_encode(['success' => true, 'message' => 'Servicio actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Sin cambios o error.']);
            }
        }
    }

    // API: ELIMINAR (Recibe id_servicio)
    public function eliminarservicio() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Servicio($pdo);
            if ($model->eliminar($input['id_servicio'])) {
                echo json_encode(['success' => true, 'message' => 'Servicio eliminado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar.']);
            }
        }
    }

    // API: CAMBIAR ESTADO
    public function cambiarestado() {
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            global $pdo;
            header('Content-Type: application/json');
            $input = json_decode(file_get_contents('php://input'), true);
            
            $model = new Servicio($pdo);
            // Validar booleano
            $nuevoEstado = $input['estado'] == 1 ? 1 : 0;
            
            if ($model->cambiarEstado($input['id_servicio'], $nuevoEstado)) {
                echo json_encode(['success' => true, 'message' => 'Estado actualizado.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al cambiar estado.']);
            }
        }
    }
}
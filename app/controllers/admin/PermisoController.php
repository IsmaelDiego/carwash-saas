<?php
namespace Controllers\Admin;

use PermisoEmpleado;
use Empleado;

class PermisoController{
    private $permisoModel;
    private $empleadoModel;

    public function __construct() {
        requireAuth();
        requireRole(1); // Solo admin
        
        require_once APP_PATH . '/models/PermisoEmpleado.php';
        require_once APP_PATH . '/models/Empleado.php';
        global $pdo;
        $this->permisoModel = new \PermisoEmpleado($pdo);
        $this->empleadoModel = new \Empleado($pdo);
    }

    public function index() {
        $permisos = $this->permisoModel->getAll();
        $empleados = $this->empleadoModel->getAll();
        require VIEW_PATH . '/admin/rrhh_permisos.view.php';
    }

    public function registrar() {
        header('Content-Type: application/json');
        
        $data = [
            'id_usuario' => $_POST['id_usuario'],
            'tipo' => $_POST['tipo'],
            'fecha_inicio' => $_POST['fecha_inicio'],
            'fecha_fin' => $_POST['fecha_fin'],
            'motivo' => $_POST['motivo'],
            'estado' => $_POST['estado'],
            'id_admin_registrador' => $_SESSION['user']['id']
        ];

        if ($this->permisoModel->registrar($data)) {
            echo json_encode(['success' => true, 'message' => 'Permiso registrado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el permiso']);
        }
    }

    public function cambiarestado() {
        header('Content-Type: application/json');
        
        $id_permiso = $_POST['id_permiso'];
        $estado = $_POST['estado'];
        $id_admin_registrador = $_SESSION['user']['id'];

        if ($this->permisoModel->cambiarEstado($id_permiso, $estado, $id_admin_registrador)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }
}

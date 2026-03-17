<?php
namespace Controllers\Admin;

use PagoEmpleado;
use Empleado;

class PagoController {
    private $pagoModel;
    private $empleadoModel;

    public function __construct() {
        requireAuth();
        requireRole(1); // Solo admin
        
        require_once APP_PATH . '/models/PagoEmpleado.php';
        require_once APP_PATH . '/models/Empleado.php';
        global $pdo;
        $this->pagoModel = new \PagoEmpleado($pdo);
        $this->empleadoModel = new \Empleado($pdo);
    }

    public function index() {
        $empleados = $this->empleadoModel->getAll();
        require VIEW_PATH . '/admin/rrhh_pagos.view.php';
    }

    public function getall() {
        header('Content-Type: application/json');
        $pagos = $this->pagoModel->getAll();
        echo json_encode(['data' => $pagos]);
    }

    public function registrar() {
        header('Content-Type: application/json');
        
        $data = [
            'id_usuario' => $_POST['id_usuario'],
            'tipo' => $_POST['tipo'],
            'monto' => $_POST['monto'],
            'periodo' => $_POST['periodo'],
            'estado' => $_POST['estado'],
            'fecha_programada' => $_POST['fecha_programada'],
            'observaciones' => $_POST['observaciones'],
            'id_admin_registrador' => $_SESSION['user']['id'],
            'fecha_pago' => date('Y-m-d H:i:s')
        ];

        if ($this->pagoModel->registrar($data)) {
            echo json_encode(['success' => true, 'message' => 'Pago registrado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar el pago']);
        }
    }

    public function cambiarestado() {
        header('Content-Type: application/json');
        
        $id_pago = $_POST['id_pago'];
        $estado = $_POST['estado'];

        if ($this->pagoModel->cambiarEstado($id_pago, $estado)) {
            echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        }
    }

    public function getstats() {
        header('Content-Type: application/json');
        $stats = $this->pagoModel->getEstadisticas();
        echo json_encode($stats);
        exit;
    }
}

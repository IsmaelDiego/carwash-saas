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
}
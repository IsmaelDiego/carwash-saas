<?php
namespace Controllers\Employee;
class DashboardController
{
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        // 1. Verificar que sea Admin
        requireRole(2); // Usamos la función del helper que creamos antes

        // 2. Cargar vista
        require VIEW_PATH . '/employee/dashboard.view.php';
    }
}
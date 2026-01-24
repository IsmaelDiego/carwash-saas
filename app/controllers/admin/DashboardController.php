<?php
class DashboardController
{
    public function __construct()
    {
        // Al ponerlo aquí, proteges TODOS los métodos de abajo automáticamente.
        requireRole(1); 
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        require VIEW_PATH . '/admin/dashboard.view.php';
    }

    
}


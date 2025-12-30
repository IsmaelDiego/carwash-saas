<?php

class AdminController
{
    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        // 1. Verificar que sea Admin
        requireRole('admin'); // Usamos la función del helper que creamos antes

        // 2. Cargar vista
        require VIEW_PATH . '/admin/dashboard.view.php';
    }
}
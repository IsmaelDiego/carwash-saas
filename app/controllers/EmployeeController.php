<?php

class EmployeeController
{
    public function index()
    {
        // Por defecto, si llaman a /employee, mostramos el dashboard
        $this->dashboard();
    }

    public function dashboard()
    {
        // 1. Verificar autenticación (Helper)
        requireAuth();

        // 2. Verificar que sea realmente un empleado/operador
        // (Si un Admin intenta entrar aquí, ¿lo dejamos? De momento sí, o puedes usar requireRole('operador'))
        if ($_SESSION['user']['role'] !== 'operador' && $_SESSION['user']['role'] !== 'admin') {
             // Si no es ni admin ni operador, fuera.
             header('Location: ' . BASE_URL . '/login');
             exit;
        }

        // 3. Cargar la vista
        require VIEW_PATH . '/employee/dashboard.view.php';
    }
}
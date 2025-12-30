<?php
class HomeController
{
    public function index()
    {
        requireAuth(); // Obligatorio estar logueado

        $role = $_SESSION['user']['role'];

        if ($role === 'admin') {
            header('Location: ' . BASE_URL . '/admin/dashboard');
        } elseif ($role === 'operador') {
            header('Location: ' . BASE_URL . '/employee/dashboard'); // Ojo: tu controlador se llama Employee
        } else {
            // Rol desconocido
            echo "Rol no reconocido";
        }
        exit;
    }
}
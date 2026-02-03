<?php

class HomeController
{
    public function index()
    {
        // Verificar si hay sesión iniciada
        requireAuth();

        // Obtener el ID del rol asegurando que sea un entero
        $role = (int) $_SESSION['user']['role'];

        switch ($role) {
            case 1: // Administrador
                header('Location: ' . BASE_URL . '/admin/dashboard');
                break;

            case 2: // Cajero
                header('Location: ' . BASE_URL . '/caja/dashboard'); // O '/employee/dashboard'
                break;
            
            case 3: // Operario (ESTE FALTABA)
                header('Location: ' . BASE_URL . '/operaciones/dashboard');
                break;

            default:
                // Si el rol es 4, 5, 99 (desconocido), cerramos sesión por seguridad
                // y lo mandamos al login para que no se quede trabado.
                session_destroy();
                header('Location: ' . BASE_URL . '/login?error=rol_invalido');
                break;
        }

        exit;
    }
}
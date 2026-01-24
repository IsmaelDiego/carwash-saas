<?php

class HomeController
{
    public function index()
    {
        requireAuth();

        $role = (int) $_SESSION['user']['role'];

        switch ($role) {
            case 1:
                header('Location: ' . BASE_URL . '/admin/dashboard');
                break;

            case 2:
                header('Location: ' . BASE_URL . '/employee/dashboard');
                break;

            default:
                echo "Rol no reconocido";
                break;
        }

        exit;
    }
}

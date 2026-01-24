<?php
// app/helpers/auth_helper.php

// Verificar si está logueado
function requireAuth(): void
{
    if (!isset($_SESSION['user'])) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

// Verificar rol específico (Admin, Operador, etc.)
function requireRole(int $requiredRole): void
{
    requireAuth(); // Primero valida que esté logueado

    if ($_SESSION['user']['role'] !== $requiredRole) {
        http_response_code(403);
        // Carga una vista de "Sin Permiso" en lugar de redirigir
        require VIEW_PATH . '/403.view.php'; 
        exit;
    }
}
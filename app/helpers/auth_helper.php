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

// Verificar rol específico (Admin=1, Cajero=2, Operario=3)
function requireRole(int $requiredRole): void
{
    requireAuth();

    if ((int)$_SESSION['user']['role'] !== $requiredRole) {
        http_response_code(403);
        if (defined('VIEW_PATH') && file_exists(VIEW_PATH . '/403.view.php')) {
            require VIEW_PATH . '/403.view.php';
        } else {
            echo "<div style='font-family:sans-serif;text-align:center;padding:50px;'>";
            echo "<h1>403 — Sin Permiso</h1>";
            echo "<p>No tienes autorización para acceder a esta sección.</p>";
            echo "<a href='" . BASE_URL . "/home'>Volver</a>";
            echo "</div>";
        }
        exit;
    }
}

// Verificar uno de varios roles (Ej: Admin O Cajero)
function requireAnyRole(array $roles): void
{
    requireAuth();

    if (!in_array((int)$_SESSION['user']['role'], $roles)) {
        http_response_code(403);
        if (defined('VIEW_PATH') && file_exists(VIEW_PATH . '/403.view.php')) {
            require VIEW_PATH . '/403.view.php';
        } else {
            echo "<div style='font-family:sans-serif;text-align:center;padding:50px;'>";
            echo "<h1>403 — Sin Permiso</h1>";
            echo "<p>No tienes autorización para acceder a esta sección.</p>";
            echo "<a href='" . BASE_URL . "/home'>Volver</a>";
            echo "</div>";
        }
        exit;
    }
}

// Helper: Obtener el nombre del rol actual
function getRoleName(): string
{
    $nombres = [1 => 'Administrador', 2 => 'Cajero', 3 => 'Operario'];
    return $nombres[$_SESSION['user']['role'] ?? 0] ?? 'Desconocido';
}

// Helper: Es admin?
function isAdmin(): bool
{
    return isset($_SESSION['user']['role']) && (int)$_SESSION['user']['role'] === 1;
}
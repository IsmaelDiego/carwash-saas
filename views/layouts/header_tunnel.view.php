<!doctype html>
<?php
    // Detectar rol para mostrar datos correctos
    $userRole = (int)($_SESSION['user']['role'] ?? 0);
    $roleName = $_SESSION['user']['rolename'] ?? 'Empleado';
    $roleBadge = $userRole === 2 ? 'bg-label-info' : 'bg-label-warning';
    $perfilUrl = $userRole === 2
        ? BASE_URL . '/caja/dashboard/perfil'
        : BASE_URL . '/operaciones/dashboard/perfil';
    $pageTitle = $userRole === 2 ? 'Cajero' : 'Operario';
?>
<html
    lang="es"
    class="layout-menu-fixed layout-compact"
    data-assets-path="<?= BASE_URL ?>/template/assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $pageTitle ?> — Carwash XP</title>
    <meta name="description" content="Panel <?= $pageTitle ?> Carwash XP" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/template/assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/fonts/iconify-icons.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- Helpers -->
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/helpers.js"></script>
    <script src="<?= BASE_URL ?>/template/assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper — SIN MENÚ LATERAL (modo túnel) -->
    <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <!-- Layout page -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-fluid navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                     id="layout-navbar">
                    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
                        <!-- Brand -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center gap-2">
                                <span class="text-primary fw-bold"><i class="icon-base bx bx-droplet icon-md me-1"></i>Carwash XP</span>
                                <span class="badge <?= $roleBadge ?>"><?= $pageTitle ?></span>
                            </div>
                        </div>

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0"
                                    href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= BASE_URL ?>/public/uploads/user.png" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="d-flex px-3 py-2">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="<?= BASE_URL ?>/public/uploads/user.png" alt class="w-px-40 h-auto rounded-circle" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?= htmlspecialchars($_SESSION['user']['name']) ?></h6>
                                                <small class="text-body-secondary"><?= htmlspecialchars($roleName) ?></small>
                                            </div>
                                        </div>
                                    </li>
                                    <li><div class="dropdown-divider my-1"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= $perfilUrl ?>">
                                            <i class="icon-base bx bx-user icon-md me-3"></i><span>Mi Perfil</span>
                                        </a>
                                    </li>
                                    <li><div class="dropdown-divider my-1"></div></li>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                                            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Cerrar Sesión</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->

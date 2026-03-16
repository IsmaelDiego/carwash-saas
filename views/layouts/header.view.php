<?php
require_once APP_PATH . '/helpers/auth_helper.php';

// Notificaciones de Admin (con caché en sesión — se refresca cada 60s)
$notificaciones_admin = [];
$total_notificaciones = 0;
if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1) {
    $cacheKey = '_notif_cache';
    $cacheTTL = 60; // segundos
    $now = time();

    if (!isset($_SESSION[$cacheKey]) || ($now - ($_SESSION[$cacheKey]['ts'] ?? 0)) > $cacheTTL) {
        global $pdo;
        $stmtPagos = $pdo->query("SELECT COUNT(*) as total FROM pagos_empleados WHERE estado = 'PENDIENTE'");
        $countPagos = (int)($stmtPagos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $stmtPermisos = $pdo->query("SELECT COUNT(*) as total FROM permisos_empleados WHERE estado = 'PENDIENTE'");
        $countPermisos = (int)($stmtPermisos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $_SESSION[$cacheKey] = ['pagos' => $countPagos, 'permisos' => $countPermisos, 'ts' => $now];
    }

    $countPagos = $_SESSION[$cacheKey]['pagos'];
    $countPermisos = $_SESSION[$cacheKey]['permisos'];

    if ($countPagos > 0) {
        $notificaciones_admin[] = [
            'icono' => 'bx-money',
            'color' => 'success',
            'titulo' => 'Pagos Pendientes',
            'descripcion' => "Tienes $countPagos pago(s) esperando ser revisado(s).",
            'url' => BASE_URL . '/admin/pago'
        ];
        $total_notificaciones += $countPagos;
    }
    if ($countPermisos > 0) {
        $notificaciones_admin[] = [
            'icono' => 'bx-calendar',
            'color' => 'warning',
            'titulo' => 'Permisos Pendientes',
            'descripcion' => "Tienes $countPermisos permiso(s) por aprobar u observar.",
            'url' => BASE_URL . '/admin/permiso'
        ];
        $total_notificaciones += $countPermisos;
    }
}
?>
<!doctype html>

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
    <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1) : ?>

        <title>Admin C-SAAS</title>

    <?php elseif (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'employee') : ?>

        <title>Employee C-SAAS</title>

    <?php endif; ?>


    <meta name="description" content="" />

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
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/css/core.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <!-- endbuild -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/template/assets/vendor/libs/apex-charts/apex-charts.css" />
    <!-- Page CSS -->
    <!-- DATABLES -->
    <!-- Helpers -->
    <script src="<?= BASE_URL ?>/template/assets/vendor/js/helpers.js"></script>
    <!--! template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="<?= BASE_URL ?>/template/assets/js/config.js"></script>
    <style>
        .hdr-shortcut-btn:hover {
            background: #f5f5f9;
            transform: translateY(-2px);
        }
        .hdr-shortcut-btn {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include VIEW_PATH . '/layouts/sidebar.view.php'; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav
                    class="layout-navbar container-fluid navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base bx bx-menu icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

                            <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 1): ?>
                                <!-- Quick Shortcuts -->
                                <li class="nav-item dropdown me-3 me-xl-2">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" title="Accesos Rápidos">
                                        <i class="icon-base bx bx-grid-alt icon-md"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end py-0 shadow-lg" style="width: 320px; border-radius: 14px; overflow: hidden; border: 1px solid #e8e8e8;">
                                        <div class="px-3 py-3" style="background: linear-gradient(135deg, #2b2c40 0%, #495071 100%);">
                                            <h6 class="mb-0 text-white fw-bold"><i class="bx bx-bolt-circle me-1"></i> Accesos Rápidos</h6>
                                            <small class="text-white" style="opacity:0.7;">Navega a cualquier módulo</small>
                                        </div>
                                        <div class="p-3">
                                            <div class="row g-2">
                                                <?php
                                                $nav_shortcuts = [
                                                    ['url' => '/admin/cliente/lista', 'icon' => 'bx-user',          'color' => 'primary', 'label' => 'Clientes'],
                                                    ['url' => '/admin/vehiculo/lista','icon' => 'bx-car',           'color' => 'info',    'label' => 'Vehículos'],
                                                    ['url' => '/admin/servicio',      'icon' => 'bx-badge-check',   'color' => 'success', 'label' => 'Servicios'],
                                                    ['url' => '/admin/producto',      'icon' => 'bx-package',       'color' => 'primary', 'label' => 'Tienda'],
                                                    ['url' => '/admin/promocion',     'icon' => 'bx-gift',          'color' => 'warning', 'label' => 'Promos'],
                                                    ['url' => '/admin/temporada',     'icon' => 'bx-calendar-star', 'color' => 'danger',  'label' => 'Temporadas'],
                                                    ['url' => '/admin/empleado',      'icon' => 'bx-group',         'color' => 'dark',    'label' => 'Personal'],
                                                    ['url' => '/admin/configuracion', 'icon' => 'bx-cog',           'color' => 'secondary','label' => 'Ajustes'],
                                                ];
                                                foreach ($nav_shortcuts as $ns): ?>
                                                    <div class="col-3">
                                                        <a href="<?= BASE_URL . $ns['url'] ?>" class="hdr-shortcut-btn d-flex flex-column align-items-center text-decoration-none p-2 rounded-3" style="transition: all 0.25s;">
                                                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-<?= $ns['color'] ?> mb-1" style="width:38px; height:38px;">
                                                                <i class="bx <?= $ns['icon'] ?> text-<?= $ns['color'] ?>" style="font-size:1.15rem;"></i>
                                                            </div>
                                                            <span style="font-size:0.65rem; font-weight:600; color:#566a7f;"><?= $ns['label'] ?></span>
                                                        </a>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <div class="border-top px-3 py-2 text-center">
                                            <a href="<?= BASE_URL ?>/admin/dashboard" class="text-primary small fw-bold text-decoration-none">
                                                <i class="bx bx-home me-1"></i> Ir al Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <!--/ Quick Shortcuts -->

                                <!-- Notifications -->
                                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                        <i class="icon-base bx bx-bell icon-md"></i>
                                        <?php if ($total_notificaciones > 0): ?>
                                            <span class="badge bg-danger rounded-pill badge-notifications"><?= $total_notificaciones ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end py-0 shadow-lg" style="width: 320px;">
                                        <li class="dropdown-menu-header border-bottom">
                                            <div class="dropdown-header d-flex align-items-center py-3">
                                                <h6 class="text-body mb-0 me-auto">Notificaciones RRHH</h6>
                                                <?php if ($total_notificaciones > 0): ?>
                                                    <span class="badge bg-primary rounded-pill"><?= $total_notificaciones ?> Nuevas</span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                        <li class="dropdown-notifications-list scrollable-container">
                                            <ul class="list-group list-group-flush">
                                                <?php if (empty($notificaciones_admin)): ?>
                                                    <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                        <div class="d-flex justify-content-center py-4">
                                                            <span class="text-muted"><i class="bx bx-check-circle text-success me-1"></i> Sin tareas pendientes</span>
                                                        </div>
                                                    </li>
                                                <?php else: ?>
                                                    <?php foreach ($notificaciones_admin as $notif): ?>
                                                        <li class="list-group-item list-group-item-action dropdown-notifications-item cursor-pointer" onclick="window.location.href='<?= $notif['url'] ?>'">
                                                            <div class="d-flex" style="align-items: center;">
                                                                <div class="flex-shrink-0 me-3">
                                                                    <div class="avatar shadow-sm d-flex align-items-center justify-content-center bg-label-<?= $notif['color'] ?>" style="width: 40px; height: 40px; border-radius: 50%;">
                                                                        <i class="bx <?= $notif['icono'] ?> fs-4"></i>
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1" style="font-size: 0.85rem; font-weight: 700; color: #566a7f;"><?= $notif['titulo'] ?></h6>
                                                                    <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;"><?= $notif['descripcion'] ?></p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <!--/ Notifications -->
                            <?php endif; ?>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a
                                    class="nav-link dropdown-toggle hide-arrow p-0"
                                    href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= BASE_URL ?>/public/uploads/user.png" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar avatar-online">
                                                    <img src="<?= BASE_URL ?>/public/uploads/user.png" alt class="w-px-40 h-auto rounded-circle" />
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0"><?= htmlspecialchars($_SESSION['user']['name']) ?></h6>
                                                <small class="text-body-secondary"><?= htmlspecialchars($_SESSION['user']['rolename']) ?></small>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= BASE_URL ?>/logout">
                                            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Cerrar Sesión</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->
<?php
// Obtenemos la URL actual en la que está el usuario
$current_url = $_SERVER['REQUEST_URI'];
$config_sys = getSystemConfig();
$logo_path = !empty($config_sys['logo']) ? BASE_URL . '/' . $config_sys['logo'] : '';
$abreviatura = !empty($config_sys['abreviatura']) ? $config_sys['abreviatura'] : 'C-SAAS';
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="<?= BASE_URL ?>/admin/dashboard" class="app-brand-link hide-url d-flex align-items-center">
            <span class="app-brand-logo demo">
                <?php if($logo_path): ?>
                    <img id="sidebar-logo" src="<?= $logo_path ?>?v=<?= $config_sys['logo_version'] ?? '1' ?>" alt="Logo" style="height: 32px; width: auto; max-width: 40px; border-radius: 4px; object-fit: contain;">
                <?php else: ?>
                    <span class="text-primary" id="sidebar-logo-container">
                        <i class="bx bxs-car" style="font-size: 32px;"></i>
                    </span>
                <?php endif; ?>
            </span>
            <span id="sidebar-abrev" class="app-brand-text demo menu-text fw-bold ms-2" style="font-size: 1.1rem;"><?= htmlspecialchars($abreviatura) ?></span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link hide-url text-large ms-auto">
            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
        </a>
    </div>


    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item <?= strpos($current_url, '/admin/dashboard') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/dashboard" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-home"></i>
                <div class="text-truncate">Dashboard</div>
            </a>
        </li>

        <!-- Finanzas -->
        <li class="menu-item <?= strpos($current_url, '/admin/finanzas') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/finanzas" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-line-chart"></i>
                <div class="text-truncate">Finanzas</div>
            </a>
        </li>

        <!-- Arqueo de Caja -->
        <li class="menu-item <?= strpos($current_url, '/admin/caja') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/caja" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-wallet"></i>
                <div class="text-truncate">Arqueo de Caja</div>
            </a>
        </li>

        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Operaciones</span>
        </li>

        <!-- Ordenes de Servicio -->
        <li class="menu-item <?= strpos($current_url, '/admin/orden') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/orden" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-customize"></i>
                <div class="text-truncate">Órdenes de Servicio</div>
            </a>
        </li>

        <!-- Clientes -->
        <li class="menu-item <?= strpos($current_url, '/admin/cliente') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/cliente/lista" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div class="text-truncate">Clientes</div>
            </a>
        </li>

        <!-- Vehículos -->
        <li class="menu-item <?= strpos($current_url, '/admin/vehiculo') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/vehiculo/lista" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-car"></i>
                <div class="text-truncate">Vehículos</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Gestión Estratégica</span>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/servicio') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/servicio" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-badge-check"></i>
                <div class="text-truncate">Servicios</div>
            </a>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/promocion') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/promocion" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-gift"></i>
                <div class="text-truncate">Promociones</div>
            </a>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/temporada') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/temporada" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-calendar-star"></i>
                <div class="text-truncate">Temporadas</div>
            </a>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/producto') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/producto" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                <div class="text-truncate">Productos Tienda</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Personal y RRHH</span>
        </li>

        <!-- Personal -->
        <li class="menu-item <?= strpos($current_url, '/admin/empleado') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/empleado" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div class="text-truncate">Personal</div>
            </a>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/pago') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/pago" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div class="text-truncate">Control de Pagos</div>
            </a>
        </li>

        <li class="menu-item <?= strpos($current_url, '/admin/permiso') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/permiso" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div class="text-truncate">Control de Permisos</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Sistema</span>
        </li>


        <!-- Configuración -->
        <li class="menu-item <?= strpos($current_url, '/admin/configuracion') !== false ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/admin/configuracion" class="menu-link hide-url">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div class="text-truncate">Configuración del Sistema</div>
            </a>
        </li>
    </ul>
</aside>
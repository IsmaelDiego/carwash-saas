<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- Header -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h3 class="card-header text-dark"><strong>CATÁLOGO DE SERVICIOS Y PRECIOS</strong></h3>
                <div class="box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-custom-icon mb-0">
                            <li class="breadcrumb-item text-primary">
                                <a href="#" class="text-primary">Servicios</a>
                                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
                            </li>
                            <li class="breadcrumb-item active text-primary">Gestión de Servicios</li>
                        </ol>
                    </nav>
                    
                    <div class="btns d-flex flex-wrap gap-2">
                        <button type="button" class="btn rounded-pill btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                            <i class="icon-base bx bx-plus-circle"></i> &nbsp NUEVO SERVICIO
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtro rápido -->
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="flex-grow-1" style="max-width: 300px;">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" id="buscadorServicios" placeholder="Buscar servicio..." />
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary btn-filtro-estado active" data-estado="todos">Todos</button>
                        <button type="button" class="btn btn-sm btn-outline-success btn-filtro-estado" data-estado="1">Activos</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-filtro-estado" data-estado="0">Inactivos</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Servicios -->
        <div class="row" id="contenedorServicios">
            <?php if (empty($servicios)): ?>
                <!-- Sin registros -->
                <div class="col-12" id="sinRegistros">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="120" class="mb-3 opacity-75" alt="Sin servicios">
                            <h5 class="fw-bold text-primary mb-1">No hay servicios registrados</h5>
                            <p class="text-muted">Crea tu primer servicio para comenzar.</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($servicios as $servicio): ?>
                    <div class="col-md-6 col-lg-4 mb-4 card-servicio" 
                         data-nombre="<?= strtolower($servicio['nombre']) ?>" 
                         data-estado="<?= $servicio['estado'] ?>">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header d-flex align-items-center justify-content-between py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                        <i class="bx bx-car-wash fs-4 text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0 fw-bold"><?= htmlspecialchars($servicio['nombre']) ?></h5>
                                        <small class="text-muted">
                                            <i class="bx bx-time"></i> <?= $servicio['duracion_minutos'] ?? 30 ?> min
                                        </small>
                                    </div>
                                </div>
                                <!-- Dropdown Acciones -->
                                <div class="dropdown">
                                    <button class="btn btn-sm text-body-secondary p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded fs-4"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item btn-editar" href="javascript:void(0);" 
                                           data-id="<?= $servicio['id_servicio'] ?>">
                                            <i class="bx bx-edit me-2"></i> Editar
                                        </a>
                                        <?php if ($_SESSION['user']['role'] == 1): ?>
                                        <a class="dropdown-item text-danger btn-eliminar" href="javascript:void(0);" 
                                           data-id="<?= $servicio['id_servicio'] ?>"
                                           data-nombre="<?= htmlspecialchars($servicio['nombre']) ?>">
                                            <i class="bx bx-trash me-2"></i> Eliminar
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body pt-3">
                                <!-- Descripción -->
                                <p class="text-muted small mb-3" style="min-height: 40px;">
                                    <?= htmlspecialchars($servicio['descripcion'] ?: 'Sin descripción') ?>
                                </p>

                                <!-- Estado -->
                                <div class="mb-3">
                                    <?php if ($servicio['estado'] == 1): ?>
                                        <span class="badge bg-success"><i class="bx bx-check-circle me-1"></i> Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="bx bx-x-circle me-1"></i> Inactivo</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Precios por tipo de vehículo -->
                                <h6 class="text-primary fw-bold mb-2">
                                    <i class="bx bx-dollar-circle me-1"></i> Precios por Vehículo
                                </h6>
                                <ul class="list-group list-group-flush">
                                    <?php if (!empty($servicio['precios'])): ?>
                                        <?php foreach ($servicio['precios'] as $precio): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                <span class="text-dark">
                                                    <i class="bx bx-car me-1 text-muted"></i>
                                                    <?= htmlspecialchars($precio['tipo_vehiculo']) ?>
                                                </span>
                                                <span class="fw-bold text-success">S/ <?= number_format($precio['precio'], 2) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="list-group-item text-muted text-center px-0 py-2">
                                            <small>Sin precios configurados</small>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
    <!-- / Content -->
</div>
<!-- / Content wrapper -->

<script>
    const BASE_URL = "<?= BASE_URL ?>";
    const TIPOS_VEHICULO = <?= json_encode($tiposVehiculo) ?>;
</script>

<?php require VIEW_PATH . '/partials/servicio/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<!-- footer -->
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/servicio.js"></script>

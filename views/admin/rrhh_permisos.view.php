<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .stat-prod-card {
        border: none;
        border-radius: 14px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .stat-prod-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
</style>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4 g-3">
            <div class="col-sm-6 col-md-3 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-primary shadow-sm" style="border-radius:12px;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;"><i class="bx bx-calendar text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Permisos</small>
                            <div class="fw-bold text-primary" id="stat_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-success shadow-sm" style="border-radius:12px;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;"><i class="bx bx-check-double text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Aprobados</small>
                            <div class="fw-bold text-success" id="stat_aprobados" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-warning shadow-sm" style="border-radius:12px;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;"><i class="bx bx-time text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Pendientes</small>
                            <div class="fw-bold text-warning" id="stat_pendientes" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-danger shadow-sm" style="border-radius:12px;width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;"><i class="bx bx-x-circle text-danger"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Rechazados</small>
                            <div class="fw-bold text-danger" id="stat_rechazados" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-calendar text-primary me-1"></i> CONTROL DE PERMISOS
                </h5>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/empleado">Personal</a></li>
                            <li class="breadcrumb-item active text-primary">Permisos</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar permiso..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        </div>

                        <button type="button" class="btn btn-primary shadow-sm" id="btnNuevoPermiso">
                            <i class="bx bx-plus me-1"></i> Nuevo Permiso
                        </button>
                        
                        <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
                            <i class="bx bx-filter-alt me-1"></i> Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tbPermisos" >
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none">ID</th>
                            <th style="color: #f0f0f0;">Empleado</th>
                            <th style="color: #f0f0f0;">Tipo</th>
                            <th style="color: #f0f0f0;">Desde</th>
                            <th style="color: #f0f0f0;">Hasta</th>
                            <th style="color: #f0f0f0;">Motivo</th>
                            <th style="color: #f0f0f0;">Estado</th>
                            <th class="text-center" style="color: #f0f0f0;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<?php require VIEW_PATH . '/partials/rrhh_permiso/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php require VIEW_PATH . '/partials/rrhh_permiso/filtros.php'; ?>
<script src="<?= BASE_URL ?>/public/js/admin/rrhh_permiso.js"></script>

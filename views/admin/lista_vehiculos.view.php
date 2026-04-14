<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }
    .detalle-card { background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
</style>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">

    <!-- ═══ STATS ═══ -->
    <div class="row mb-4" id="statsVehiculos">
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-info shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-car text-info"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Vehículos</small>
                        <div class="fw-bold text-info" id="stat_veh_total" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-category text-primary"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Categorías</small>
                        <div class="fw-bold text-primary" id="stat_veh_categorias" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-success shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-user text-success"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Propietarios</small>
                        <div class="fw-bold text-success" id="stat_veh_propietarios" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-calendar-plus text-warning"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Este Mes</small>
                        <div class="fw-bold text-warning" id="stat_veh_mes" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header border-bottom mb-3">
          <i class="bx bx-car text-primary me-1"></i> GESTIÓN DE VEHÍCULOS
        </h5>
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <nav aria-label="breadcrumb" class="me-auto">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                  <li class="breadcrumb-item active text-primary">Vehículos</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="input-group" style="width: 240px;">
                    <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar vehículo..." autocomplete="off">
                    <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                </div>
                
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                  <i class="bx bx-plus me-1"></i> Nuevo Vehículo
                </button>
                <a href="<?= BASE_URL ?>/admin/vehiculo/categorias" class="btn btn-label-primary shadow-sm">
                  <i class="bx bx-category me-1"></i> Categorías
                </a>
                <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
                  <i class="bx bx-filter-alt me-1"></i> Filtros
                </button>
                <button class="btn btn-outline-success" type="button" id="btnExportar">
                  <i class="bx bxs-file-export p-2"></i>
                </button>
            </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="table-responsive text-nowrap px-3">
        <table class="table table-hover w-100" id="tablaVehiculos">
          <thead class="bg-primary">
            <tr>
              <th class="d-none">ID V</th>
              <th class="d-none">ID C</th>
              <th class="d-none">ID Cat</th>

              <th style="color: #f0f0f0;">Placa</th>          <th style="color: #f0f0f0;">Categoría</th>      <th style="color: #f0f0f0;">Color</th>          <th style="color: #f0f0f0;">Propietario</th>    <th class="d-none">Obs</th>   <th class="d-none">Fecha</th> <th class="text-center" style="color: #f0f0f0;">Acciones</th> </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="content-backdrop fade"></div>
</div>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>

<?php require VIEW_PATH . '/partials/vehiculo/modals.php'; ?>
<?php require VIEW_PATH . '/partials/vehiculo/filtros.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/vehiculo.js"></script>
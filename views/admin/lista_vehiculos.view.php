<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }
    .detalle-card { background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; height: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
</style>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">
    
    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header border-bottom mb-3">GESTIÓN DE VEHÍCULOS</h5>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                  <li class="breadcrumb-item active text-primary">Vehículos</li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn rounded-pill btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                  <i class="bx bx-plus me-1"></i> NUEVO VEHÍCULO
                </button>
                <button class="btn rounded-pill btn-dark shadow-sm" type="button" id="btnAbrirFiltro">
                  <i class="bx bx-search-alt me-1"></i> BUSCAR
                </button>
                <button class="btn rounded-pill btn-outline-success" type="button" id="btnExportar">
                  <i class="bx bxs-file-export me-1"></i> EXPORTAR
                </button>
            </div>
        </div>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="table-responsive text-nowrap px-3">
        <table class="table table-hover w-100" id="tablaVehiculos">
          <thead class="bg-light">
            <tr>
              <th class="d-none">ID V</th>
              <th class="d-none">ID C</th>
              <th class="d-none">ID Cat</th>

              <th>Placa</th>          <th>Categoría</th>      <th>Color</th>          <th>Propietario</th>    <th class="d-none">Obs</th>   <th class="d-none">Fecha</th> <th class="text-center">Acciones</th> </tr>
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
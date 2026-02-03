<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">
    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header">LISTA DE VEHÍCULOS</h5>
        <div class="box d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="btns d-flex flex-wrap gap-2 ms-auto">
                <button type="button" class="btn rounded-pill btn-primary" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="bx bx-plus"></i> NUEVO
                </button>
                <button class="btn rounded-pill btn-outline-secondary" type="button" id="btnExportar">
                    <i class="bx bx-export"></i> EXPORTAR
                </button>
            </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-hover w-100" id="tablaVehiculos">
          <thead>
            <tr>
              <th>Placa</th>
              <th>Propietario</th> <th>Tipo</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Color</th>
              <th>Estado</th> <th>Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="content-backdrop fade"></div>
</div>

<script>const BASE_URL = "<?= BASE_URL ?>";</script>

<?php 
require VIEW_PATH . '/partials/vehiculo/modals.php'; 
require VIEW_PATH . '/partials/global/toasts.php';
// require VIEW_PATH . '/partials/vehiculo/filtros.php'; // Descomenta si usas filtros
require VIEW_PATH . '/layouts/footer.view.php'; 
?>

<script src="<?= BASE_URL ?>/public/js/admin/vehiculo.js"></script>
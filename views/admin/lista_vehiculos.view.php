<?php require VIEW_PATH . '/layouts/header.view.php'; ?>


<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->

  <div class="container-fluid flex-grow-1 container-p-y">

    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header">LISTA DE VEHICULOS</h5>

        <div class="box d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

          <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-custom-icon mb-0">
              <li class="breadcrumb-item text-primary">
                <a href="#" class="text-primary">Vehiculos</a>
                <i class="breadcrumb-icon icon-base bx bx-chevron-right align-middle"></i>
              </li>
              <li class="breadcrumb-item active text-primary">Lista de Vehiculos</li>
            </ol>
          </nav>
          <div class="btns d-flex flex-wrap gap-2">
            <button
              type="button"
              class="btn rounded-pill btn-primary"
              data-bs-toggle="modal"
              data-bs-target="#modalRegistrar">
              <i class="icon-base bx bx-plus"></i> &nbsp NUEVO
            </button>
            <button
              class="btn rounded-pill btn-outline-secondary"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#offcanvasDark"
              aria-controls="offcanvasDark">
              <i class="icon-base bx bx-filter"></i> &nbsp FILTRAR
            </button>
            <button class="btn rounded-pill btn-outline-secondary" type="button" id="btnExportar">
              <i class="icon-base bx bx-export"></i> &nbsp EXPORTAR
            </button>
          </div>
          


        </div>
      </div>
    </div>

    <!-- Hoverable Table rows -->
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-hover w-100" id="tablaVehiculos">
          <thead>
            <tr>
              <th>Placa</th>
              <th>propietario</th>
              <th>Tipo</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Color</th>
              <th>Observaciones</th>
              <th>Fecha Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">

          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- / Content -->



  <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<script>
  // 1. Definimos la variable GLOBALMENTE para que todos los JS la puedan ver
  const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php

require VIEW_PATH . '/partials/vehiculo/modals.php';

require VIEW_PATH . '/partials/global/toasts.php';

require VIEW_PATH . '/partials/vehiculo/filtros.php';
?>



<!-- footer -->
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/vehiculo.js"></script>


<script src="<?= BASE_URL ?>/public/js/select2/dist/js/select2.min.js"></script>



<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>



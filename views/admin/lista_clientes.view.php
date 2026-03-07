<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
  /* Ocultar buscador nativo */
  .dataTables_filter {
    display: none !important;
  }

  /* Paginación izquierda */
  .dataTables_paginate {
    display: flex !important;
    justify-content: flex-start !important;
    margin-top: 1.5rem !important;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
  }

  /* Info derecha */
  .dataTables_info {
    text-align: right !important;
    margin-top: 1.5rem !important;
    padding-top: 1rem;
    color: #b0b0b0 !important;
  }

  /* Switch WhatsApp */
  .switch-whatsapp {
    cursor: pointer;
    width: 3em !important;
    height: 1.5em !important;
  }

  /* Tarjetas Detalle */
  .detalle-card {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  }

  .detalle-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #8592a3;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .detalle-value {
    font-size: 1rem;
    color: #384551;
    font-weight: 500;
  }
</style>

<div class="content-wrapper">
  <div class="container-fluid flex-grow-1 container-p-y">

    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header border-bottom mb-3">GESTIÓN DE CLIENTES</h5>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
              <li class="breadcrumb-item active text-primary">Clientes</li>
            </ol>
          </nav>

          <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn rounded-pill btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
              <i class="bx bx-plus me-1"></i> NUEVO
            </button>

            <button class="btn rounded-pill btn-outline-secondary" type="button" id="btnAbrirFiltro">
              <i class="bx bx-search-alt me-1"></i> BUSCAR / FILTRAR
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
        <table class="table table-hover w-100" id="tablaClientes">
          <thead class="bg-primary">
            <tr style="color: #f0f0f0;">
              <th class="d-none">Nombres</th>
              <th class="d-none">Apellidos</th>
              <th class="d-none">DNI</th>
              <th class="d-none">Teléfono</th>
              <th class="d-none">Obs</th>
              <th class="d-none">FechaRaw</th>

              <th style="color: #f0f0f0;">Cliente</th>
              <th style="color: #f0f0f0;" class="text-center">Sexo</th>
              <th style="color: #f0f0f0;" class="text-center">WhatsApp</th>
              <th style="color: #f0f0f0;" class="text-center">Puntos</th>
              <th style="color: #f0f0f0;" class="text-center">Registro</th>
              <th style="color: #f0f0f0;" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>

  </div>
  <div class="content-backdrop fade"></div>
</div>

<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php require VIEW_PATH . '/partials/cliente/modals.php'; ?>


<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/partials/cliente/filtros.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/cliente.js"></script>
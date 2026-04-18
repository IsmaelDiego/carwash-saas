<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
  /* Ocultar buscador nativo y longitud */
  .dataTables_filter, .dataTables_length {
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

  /* Switch WhatsApp Custom */
  .switch-whatsapp {
    cursor: pointer;
    width: 3em !important;
    height: 1.5em !important;
    background-color: #e0e0e0 !important; /* Gris por defecto */
    border-color: #d1d1d1 !important;
    transition: all 0.3s ease;
  }

  .switch-whatsapp:checked {
    background-color: #25d366 !important; /* Verde WhatsApp */
    border-color: #25d366 !important;
    box-shadow: 0 0 10px rgba(37, 211, 102, 0.4);
  }

  .switch-whatsapp:focus {
    box-shadow: 0 0 0 0.25rem rgba(37, 211, 102, 0.25);
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

    <!-- ═══ STATS ═══ -->
    <div class="row mb-4" id="statsClientes">
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-group text-primary"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Clientes</small>
                        <div class="fw-bold text-primary" id="stat_cli_total" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-success shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bxl-whatsapp text-success"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Con WhatsApp</small>
                        <div class="fw-bold text-success" id="stat_cli_whatsapp" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-star text-warning"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Puntos Totales</small>
                        <div class="fw-bold text-warning" id="stat_cli_puntos" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-info shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-calendar-plus text-info"></i></div>
                    <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Este Mes</small>
                        <div class="fw-bold text-info" id="stat_cli_mes" style="font-size:1.4rem">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mb-4">
      <div class="m-1">
        <h5 class="card-header border-bottom mb-3">
          <i class="bx bx-group text-primary me-1"></i> GESTIÓN DE CLIENTES
        </h5>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
          <nav aria-label="breadcrumb" class="me-auto">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
              <li class="breadcrumb-item active text-primary">Clientes</li>
            </ol>
          </nav>

          <div class="d-flex flex-wrap align-items-center gap-2">
            <div class="input-group" style="width: 240px;">
                <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar cliente..." autocomplete="off">
                <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
            </div>

            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
              <i class="bx bx-plus me-1"></i> Nuevo Cliente
          
              <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
              <i class="bx bx-filter-alt me-1"></i> Filtros
            </button>


             <!-- Botón de Reportes BI Sincronizado -->
            <button type="button" class="btn btn-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalReportesCliente">
                <i class="bx bxs-bar-chart-alt-2 p-1 fs-5"></i> <span class="d-none d-md-inline ms-1">Centro de Reportes BI</span>
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

<?php 
    require VIEW_PATH . '/partials/cliente/modals.php'; 
    require VIEW_PATH . '/partials/cliente/modal_reporte.php'; 
?>


<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/partials/cliente/filtros.php'; ?>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/cliente.js"></script>
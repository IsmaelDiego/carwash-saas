<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* Ocultamos elementos nativos de DataTables que no queremos */
    .dataTables_filter,
    .dataTables_length {
        display: none !important;
    }

    /* Ajuste de paginación */
    .dataTables_paginate {
        display: flex !important;
        justify-content: flex-start !important;
        margin-top: 1rem !important;
    }

    .dataTables_info {
        text-align: right !important;
        margin-top: 1rem !important;
        color: #b0b0b0 !important;
    }

    .detalle-card {
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 15px;
        height: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

  /* Switch Estado Custom */
  .switch-estado {
    cursor: pointer;
    width: 3em !important;
    height: 1.5em !important;
    background-color: #e0e0e0 !important;
    border-color: #d1d1d1 !important;
    transition: all 0.3s ease;
  }

  .switch-estado:checked {
    background-color: #25d366 !important;
    border-color: #25d366 !important;
    box-shadow: 0 0 10px rgba(37, 211, 102, 0.4);
  }

  .switch-estado:focus {
    box-shadow: 0 0 0 0.25rem rgba(37, 211, 102, 0.25);
  }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4" id="statsServicios">
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-badge-check text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Servicios</small>
                            <div class="fw-bold text-primary" id="stat_srv_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-success shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-check-circle text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Activos</small>
                            <div class="fw-bold text-success" id="stat_srv_activos" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-star text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Con Puntos</small>
                            <div class="fw-bold text-warning" id="stat_srv_puntos" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-info shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-gift text-info"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Permite Canje</small>
                            <div class="fw-bold text-info" id="stat_srv_canje" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">CATÁLOGO DE SERVICIOS</h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Servicios</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">

                        <div class="input-group" style="width: 50%;">
                            <input type="text" id="buscadorGlobal" class="form-control " placeholder="Buscar servicio..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>

                        </div>

                        <button type="button" class="btn  btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                            <i class="bx bx-plus me-1"></i> Nuevo Servicio
                        </button>

                        <button class="btn  btn-outline-success" type="button" id="btnExportar">
                            <i class="bx bxs-file-export p-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
    
    <div class="card-body border-bottom p-3 badge bg-label-dark">
        <div class="d-flex flex-wrap align-items-center gap-4">
            <span class="fw-bold text-muted small text-uppercase"><i class="bx bx-info-circle me-1"></i> Leyenda:</span>
            
            <div class="d-flex align-items-center">
                <span class="badge bg-label-primary me-2 p-1"><i class="bx bxs-star fs-6"></i></span>
                <span class="small text-dark">Genera Puntos</span>
            </div>

            <div class="d-flex align-items-center">
                <span class="badge bg-label-warning me-2 p-1"><i class="bx bxs-gift fs-6"></i></span>
                <span class="small text-dark">Permite Canje</span>
            </div>
            
            <div class="d-flex align-items-center ms-auto">
                <small class="text-muted fst-italic" style="font-size: 0.75rem;">* Los servicios inactivos no aparecen en caja.</small>
            </div>
        </div>
    </div>

    <div class="table-responsive text-nowrap px-3">
        <table class="table table-hover w-100 my-3" id="tablaServicios">
            <thead class="bg-primary">
                <tr>
                    <th class="d-none">ID</th>
                    <th class="d-none">Acumula</th>
                    <th class="d-none">Canje</th>

                    <th style="color: #f0f0f0;">Nombre Servicio</th>
                    <th style="color: #f0f0f0;"> Precio Base</th>
                    <th style="color: #f0f0f0;" class="text-center"> Tiempo Est.</th>
                    <th class="text-center" style="color: #f0f0f0;">Reglas</th>
                    <th class="text-center" style="color: #f0f0f0;">Estado</th>
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

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<?php require VIEW_PATH . '/partials/servicio/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/servicio.js"></script>
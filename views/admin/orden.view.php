<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter,
    .dataTables_length {
        display: none !important;
    }
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
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4" id="statsOrdenes">
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-list-ol text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Órdenes</small>
                            <div class="fw-bold text-primary" id="stat_ord_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-success shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-check-circle text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Finalizadas</small>
                            <div class="fw-bold text-success" id="stat_ord_finalizado" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-time text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">En Proceso / Cola</small>
                            <div class="fw-bold text-warning" id="stat_ord_proceso" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-info shadow-sm" style="width:48px;height:48px;font-size:1.4rem;border-radius:12px;"><i class="bx bx-money text-info"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Ingreso Total</small>
                            <div class="fw-bold text-info" id="stat_ord_ingreso" style="font-size:1.4rem">S/ 0.00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HEADER + ACCIONES -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-customize text-primary me-1"></i> REGISTRO DE ÓRDENES
                </h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Órdenes de Servicio</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar orden..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        </div>

                        <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
                            <i class="bx bx-filter-alt me-1"></i> Filtros

                        <button class="btn btn-outline-success" type="button" id="btnExportar">
                            <i class="bx bxs-file-export p-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA CARD -->
        <div class="card shadow-sm">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tbOrdenes">
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none">FechaRaw</th>
                            <th style="color: #f0f0f0;">N° Orden</th>
                            <th style="color: #f0f0f0;">Fecha</th>
                            <th style="color: #f0f0f0;">Cliente</th>
                            <th style="color: #f0f0f0;">Vehículo</th>
                            <th style="color: #f0f0f0;">Creado por</th>
                            <th class="text-center" style="color: #f0f0f0;">Estado</th>
                            <th class="text-end" style="color: #f0f0f0;">Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- Extra Offcanvas Filtro -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFiltroOrdenes">
  <div class="offcanvas-header bg-dark text-white">
    <h5 class="offcanvas-title text-white"><i class="bx bx-filter-alt me-2"></i> Filtros de Fechas</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <div class="mb-3">
        <label for="filtroFechaInicio" class="form-label">Desde:</label>
        <input type="date" id="filtroFechaInicio" class="form-control">
    </div>
    <div class="mb-4">
        <label for="filtroFechaFin" class="form-label">Hasta:</label>
        <input type="date" id="filtroFechaFin" class="form-control">
    </div>
    <div class="d-grid gap-2 mt-auto">
        <button type="button" class="btn btn-primary" id="btnAplicarFiltros">
            <i class="bx bx-check"></i> Aplicar / Cerrar
        </button>
        <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">
            <i class="bx bx-refresh"></i> Limpiar Todo
        </button>
    </div>
  </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/js/admin/orden.js"></script>

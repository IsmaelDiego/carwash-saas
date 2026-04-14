<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }

    /* ─── STAT CARDS ─── */
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
    .stat-prod-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
    /* Switch Estado Custom */
    .switch-estado, .form-check-input {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-input:checked {
        background-color: #25d366 !important;
        border-color: #25d366 !important;
        box-shadow: 0 0 10px rgba(37, 211, 102, 0.4);
    }
</style>

<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4 g-3">
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-primary shadow-sm"><i class="bx bx-package text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Productos</small>
                            <div class="fw-bold text-primary" id="stat_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-success shadow-sm"><i class="bx bx-check-circle text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Con Stock</small>
                            <div class="fw-bold text-success" id="stat_con_stock" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-warning shadow-sm"><i class="bx bx-error text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Bajo Stock</small>
                            <div class="fw-bold text-warning" id="stat_bajo_stock" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-danger shadow-sm"><i class="bx bx-x-circle text-danger"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Sin Stock</small>
                            <div class="fw-bold text-danger" id="stat_sin_stock" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-danger shadow-sm"><i class="bx bx-timer text-danger"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Por Vencer</small>
                            <div class="fw-bold text-danger" id="stat_por_vencer" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-info shadow-sm"><i class="bx bx-wallet text-info"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Valor Inventario</small>
                            <div class="fw-bold text-info" id="stat_valor_inv" style="font-size:1.4rem">S/ 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4 col-xl">
                <div class="card stat-prod-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-prod-icon bg-label-secondary shadow-sm"><i class="bx bx-layer text-secondary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Lotes Activos</small>
                            <div class="fw-bold text-secondary" id="stat_lotes_activos" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ HEADER + ACCIONES ═══ -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-store-alt text-primary me-1"></i> INVENTARIO DE PRODUCTOS
                </h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Productos Tienda</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar producto..." autocomplete="off">
                        </div>

                        <select class="form-select" id="filtroStock" style="width: 160px;">
                            <option value="">Todos</option>
                            <option value="con_stock">Con stock</option>
                            <option value="bajo_stock">Bajo stock</option>
                            <option value="sin_stock">Sin stock</option>
                        </select>
                        <button class="btn btn-outline-secondary" type="button" id="btnAbrirFiltro">
                            <i class="bx bx-filter-alt me-1"></i> Filtros
                        </button>
                        <button class="btn btn-outline-warning position-relative" type="button" id="btnAlertasVencimiento" title="Alertas de Vencimiento">
                            <i class="bx bx-bell bx-tada"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="badgeAlertasVenc" style="display:none;">0</span>
                        </button>
                        <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#modalReportesInventario" title="Central de Reportes">
                            <i class="bx bxs-file-export p-2"></i> Reportes
                        </button>
                        <button class="btn btn-primary shadow-sm" id="btnNuevoProducto">
                            <i class="bx bx-plus me-1"></i> Nuevo Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ TABLA ═══ -->
        <div class="card shadow-sm">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tablaProductos">
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none">FechaRaw</th>
                            <th style="color: #f0f0f0;">ID</th>
                            <th style="color: #f0f0f0;">Producto</th>
                            <th class="text-center" style="color: #f0f0f0;">Costos/Venta</th>
                            <th class="text-center" style="color: #f0f0f0;">Disponibilidad (Stock)</th>
                            <th class="text-center" style="color: #f0f0f0;">Caducidad / Estado</th>
                            <th class="text-center" style="color: #f0f0f0;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProductos"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php require VIEW_PATH . '/partials/producto/filtros.php'; ?>

<!-- ═══ MODALES ═══ -->
<?php require VIEW_PATH . '/partials/producto/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<script> const BASE_URL = "<?= BASE_URL ?>"; </script>
<script src="<?= BASE_URL ?>/public/js/admin/producto.js"></script>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>


<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }

    /* ─── STAT CARDS ─── */
    .stat-arq-card {
        border: none;
        border-radius: 14px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .stat-arq-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }
    .stat-arq-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }
</style>

<div class="content-wrapper" data-base-url="<?= BASE_URL ?>">
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- ═══ STATS ═══ -->
        <div class="row mb-4 g-3" id="statsArqueos">
            <div class="col-sm-6 col-md-3">
                <div class="card stat-arq-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-arq-icon bg-label-primary shadow-sm"><i class="bx bx-receipt text-primary"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Total Sesiones</small>
                            <div class="fw-bold text-primary" id="stat_arq_total" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-arq-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-arq-icon bg-label-success shadow-sm"><i class="bx bx-check-circle text-success"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Cuadrados</small>
                            <div class="fw-bold text-success" id="stat_arq_cuadrados" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-arq-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-arq-icon bg-label-warning shadow-sm"><i class="bx bx-trending-up text-warning"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Sobrantes</small>
                            <div class="fw-bold text-warning" id="stat_arq_sobrantes" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card stat-arq-card shadow-sm h-100" style="border:1px solid #f0f0f0;">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="stat-arq-icon bg-label-danger shadow-sm"><i class="bx bx-trending-down text-danger"></i></div>
                        <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem">Faltantes</small>
                            <div class="fw-bold text-danger" id="stat_arq_faltantes" style="font-size:1.4rem">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ HEADER + ACCIONES ═══ -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-calculator text-primary me-1"></i> ARQUEO DE CAJA
                </h5>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Arqueo de Caja</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Buscador Global -->
                        <div class="input-group input-group-merge shadow-none border rounded bg-white" style="width: 220px;">
                            <span class="input-group-text border-0 bg-transparent px-2 text-muted"><i class="bx bx-search"></i></span>
                            <input type="text" id="buscadorArqueos" class="form-control border-0 px-1 text-sm" placeholder="Buscar fecha, ID...">
                        </div>

                        <!-- Selector Periodo -->
                        <div class="input-group input-group-merge shadow-none border rounded" style="width: 200px;">
                            <select id="filterMonth" class="form-select border-0 bg-white" onchange="cargarArqueos()">
                                <?php
                                $nombres_meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                for ($m = 1; $m <= 12; $m++):
                                    $sel = ($m == date('n')) ? 'selected' : '';
                                    echo "<option value='$m' $sel>{$nombres_meses[$m - 1]}</option>";
                                endfor;
                                ?>
                            </select>
                            <select id="filterYear" class="form-select border-0 border-start px-2 bg-white" style="max-width: 85px;" onchange="cargarArqueos()">
                                <?php
                                for ($y = 2024; $y <= date('Y') + 1; $y++):
                                    $sel = ($y == date('Y')) ? 'selected' : '';
                                    echo "<option value='$y' $sel>$y</option>";
                                endfor;
                                ?>
                            </select>
                        </div>

                        <button class="btn btn-outline-success border" style="padding: 0.4375rem 0.8rem;" type="button" onclick="exportarArqueos()" title="Exportar CSV">
                            <i class="bx bxs-file-export fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ TABLA ═══ -->
        <div class="card shadow-sm">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tbArqueos">
                    <thead class="bg-primary">
                        <tr>
                            <th style="color: #f0f0f0;">ID</th>
                            <th style="color: #f0f0f0;">Apertura</th>
                            <th style="color: #f0f0f0;">Cajero</th>
                            <th style="color: #f0f0f0;" class="text-center">Monto Inicial</th>
                            <th style="color: #f0f0f0;" class="text-center">Esperado (Total)</th>
                            <th style="color: #f0f0f0;" class="text-center">Recaudado</th>
                            <th style="color: #f0f0f0;" class="text-center">Diferencia</th>
                            <th style="color: #f0f0f0;" class="text-center">Estado</th>
                            <th style="color: #f0f0f0;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyArqueos">
                        <!-- JS -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Detalle Arqueo -->
<div class="modal fade" id="modalDetalleSesion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg" >
            <!-- Header Premium -->
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-receipt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">ARQUEO DE CAJA #<span id="detIdSesion"></span></h5>
                        <small class="text-white-50">Detalle completo de la sesión de caja</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 p-md-4">

                <!-- ═══ Información General ═══ -->
                <div class="p-3 rounded-3 border mb-4" style="background-color: rgba(105, 108, 255, 0.04);">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bx bx-info-circle fs-5 me-2 text-primary"></i>
                        <span class="fw-bold text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Información General</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Cajero</small>
                            <span class="fw-bold text-dark" id="detCajero">-</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Apertura</small>
                            <span class="fw-semibold text-dark" id="detFechaApertura">-</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Cierre</small>
                            <span class="fw-semibold text-dark" id="detFechaCierre">-</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.65rem;">Monto Inicial</small>
                            <span class="fw-bold text-primary" id="detMontoApertura">-</span>
                        </div>
                    </div>
                </div>

                <!-- ═══ Resumen Financiero ═══ -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-3 text-center">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-label-info mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bx bx-target-lock text-info fs-5"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.6rem;">Esperado</small>
                                <span class="fw-bold text-dark fs-5" id="detMontoEsperado">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-3 text-center">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-label-primary mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bx bx-money text-primary fs-5"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.6rem;">Recaudado</small>
                                <span class="fw-bold text-primary fs-5" id="detMontoReal">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-3 text-center">
                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-label-warning mx-auto mb-2" style="width: 40px; height: 40px;">
                                    <i class="bx bx-transfer text-warning fs-5"></i>
                                </div>
                                <small class="text-muted fw-bold text-uppercase d-block" style="font-size: 0.6rem;">Diferencia</small>
                                <span class="fw-bold fs-5" id="detDiferencia">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ═══ Desglose ═══ -->
                <div class="row g-3">
                    <!-- Métodos de Pago -->
                    <div class="col-md-5">
                        <div class="card border h-100" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom py-3 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-primary me-2" style="width: 32px; height: 32px;">
                                        <i class="bx bx-wallet text-primary"></i>
                                    </div>
                                    <span class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.3px;">Métodos de Pago</span>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div id="detMetodosCont" class="list-group list-group-flush shadow-none">
                                    <!-- JS -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ventas de Tienda -->
                    <div class="col-md-7">
                        <div class="card border h-100" style="border-radius: 12px;">
                            <div class="card-header bg-transparent border-bottom py-3 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-label-warning me-2" style="width: 32px; height: 32px;">
                                        <i class="bx bx-package text-warning"></i>
                                    </div>
                                    <span class="fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.3px;">Ventas de Tienda</span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="ps-3 fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Producto</th>
                                                <th class="text-center fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Cant.</th>
                                                <th class="text-end pe-3 fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="detProdsCont">
                                            <!-- JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white border-top p-4">
                <button type="button" class="btn btn-white w-100 fw-bold border text-muted shadow-sm" data-bs-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>
<script src="<?= BASE_URL ?>/public/js/admin/caja.js"></script>

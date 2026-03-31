<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<div class="content-wrapper" data-base-url="<?= BASE_URL ?>">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
            <nav aria-label="breadcrumb" class="me-auto">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                    <li class="breadcrumb-item active text-primary">Arqueo de Caja</li>
                </ol>
                <h4 class="fw-bold mb-0">Historial de Arqueos</h4>
            </nav>

            <div class="d-flex align-items-center gap-2">
                <!-- Selector Periodo -->
                <div class="input-group input-group-merge shadow-none border rounded" style="width: 220px;">
                    <select id="filterMonth" class="form-select border-0 bg-white" onchange="cargarArqueos()">
                        <?php
                        $nombres_meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                        for($m=1; $m<=12; $m++):
                            $sel = ($m == date('n')) ? 'selected' : '';
                            echo "<option value='$m' $sel>{$nombres_meses[$m-1]}</option>";
                        endfor;
                        ?>
                    </select>
                    <select id="filterYear" class="form-select border-0 border-start px-2 bg-white" style="max-width: 85px;" onchange="cargarArqueos()">
                        <?php
                        for($y=2024; $y<=date('Y')+1; $y++):
                            $sel = ($y == date('Y')) ? 'selected' : '';
                            echo "<option value='$y' $sel>$y</option>";
                        endfor;
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100" id="tbArqueos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Apertura</th>
                                <th>Cajeros</th>
                                <th>Monto Inicial</th>
                                <th>Esperado (Total)</th>
                                <th>Recaudado</th>
                                <th>Diferencia</th>
                                <th>Estado</th>
                                <th>Acciones</th>
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
</div>

<!-- Modal Detalle Arqueo -->
<div class="modal fade" id="modalDetalleSesion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white p-4" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-receipt me-2"></i> Detalle de Arqueo #<span id="detIdSesion"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Resumen -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Información General</h6>
                        <dl class="row mb-0">
                            <dt class="col-sm-5 fw-semibold text-muted">Cajero:</dt>
                            <dd class="col-sm-7 fw-bold" id="detCajero">-</dd>
                            
                            <dt class="col-sm-5 fw-semibold text-muted">Apertura:</dt>
                            <dd class="col-sm-7" id="detFechaApertura">-</dd>
                            
                            <dt class="col-sm-5 fw-semibold text-muted">Cierre:</dt>
                            <dd class="col-sm-7" id="detFechaCierre">-</dd>
                            
                            <dt class="col-sm-5 fw-semibold text-muted">Monto Inicial:</dt>
                            <dd class="col-sm-7" id="detMontoApertura">-</dd>
                        </dl>
                    </div>

                    <!-- Resultados -->
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Esperado:</span>
                                <span class="fw-bold" id="detMontoEsperado">-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Recaudado:</span>
                                <span class="fw-bold text-primary" id="detMontoReal">-</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="fw-bold">Diferencia:</span>
                                <span class="fw-bold" id="detDiferencia">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <div class="col-md-5">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bx bx-wallet me-1 text-primary"></i> Por Método de Pago</h6>
                        <div id="detMetodosCont" class="list-group list-group-flush shadow-none">
                            <!-- JS -->
                        </div>
                    </div>

                    <!-- Ventas de Insumos / Tienda -->
                    <div class="col-md-7">
                        <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bx bx-package me-1 text-warning"></i> Ventas de Tienda</h6>
                        <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cant.</th>
                                        <th class="text-end">Monto</th>
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
            <div class="modal-footer p-3 border-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>
<script src="<?= BASE_URL ?>/public/js/admin/caja.js"></script>

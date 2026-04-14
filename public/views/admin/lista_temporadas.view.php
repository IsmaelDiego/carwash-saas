<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* --- ESTILOS MODERNOS Y FORMALES --- */

    /* Card Activa: Blanca, limpia, con sombra suave y acento izquierdo */
    .card-active {
        background: #ffffff;
        border: 0;
        border-radius: 1rem;
        border-left: 6px solid #696cff;
        box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .card-active:hover { transform: translateY(-3px); box-shadow: 0 0.8rem 2rem rgba(105, 108, 255, 0.15); }

    .card-header-active {
        background: linear-gradient(90deg, rgba(105, 108, 255, 0.05) 0%, rgba(255, 255, 255, 0) 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #f0f2f4;
    }

    .stat-value { font-size: 2.2rem; font-weight: 800; color: #32475c; line-height: 1; }
    .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #a1acb8; font-weight: 700; }

    .trend-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 20px;
    }

    .card-prev {
        background: #fcfcfd;
        border: 1px dashed #d9dee3;
        border-radius: 1rem;
    }

    /* Tabla Estilo Premium */
   
    #tablaTemporadas tbody td { padding: 1rem; vertical-align: middle; }

    .dataTables_paginate .pagination .page-link { border-radius: 8px; margin: 0 2px; border: none; background: #f0f2f4; color: #566a7f; }
    .dataTables_paginate .pagination .active .page-link { background: #696cff; color: #fff; }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0 text-primary">Gestión de Periodos</h4>
                <small class="text-muted">Administra las temporadas de venta y puntos.</small>
            </div>

            <?php if (!$tActual): ?>
                <button class="btn btn-primary rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                    <i class="bx bx-play me-1"></i> INICIAR TEMPORADA
                </button>
            <?php else: ?>
                <span class="badge bg-label-success fs-6 border border-success px-3">
                    <i class="bx bx-check-circle me-1"></i> Periodo en Curso
                </span>
            <?php endif; ?>
        </div>

        <div class="row g-4 mb-4">

            <div class="col-lg-8">
                <?php if ($tActual): ?>
                    <div class="card card-active h-100">
                        <div class="card-header-active d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-primary fw-bold small text-uppercase mb-1 d-block"><i class='bx bx-pulse'></i> Actividad en tiempo real</span>
                                <h3 class="text-dark fw-bold mb-0"><?= $tActual['nombre'] ?></h3>
                                <small class="text-muted">
                                    Iniciada el <strong><?= date('d/m/Y', strtotime($tActual['fecha_inicio'])) ?></strong>
                                </small>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-sm btn-label-secondary" type="button" data-bs-toggle="dropdown"><i class="bx bx-cog me-1"></i> Opciones</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                   
                                    <li><a class="dropdown-item btn-ver-dash" href="javascript:void(0);"
                                            data-nom="<?= $tActual['nombre'] ?>"
                                            data-ini="<?= $tActual['fecha_inicio'] ?>"
                                            data-gen="<?= $sAct['gen'] ?>"
                                            data-red="<?= $sAct['red'] ?>"
                                            data-est="1">
                                            <i class="bx bx-show me-2"></i> Ver Detalle</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <div class="d-flex flex-column h-100 ps-2">
                                        <span class="stat-label mb-2">Puntos Emitidos</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="stat-value text-dark"><?= number_format($sAct['gen'], 0) ?></span>
                                            <span class="trend-badge <?= $varGen >= 0 ? 'bg-label-success text-success' : 'bg-label-danger text-danger' ?>">
                                                <i class='bx <?= $varGen >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>'></i> <?= abs($varGen) ?>%
                                            </span>
                                        </div>
                                        <small class="text-muted mt-2">Vs. Periodo anterior</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex flex-column h-100 ps-4">
                                        <span class="stat-label mb-2">Canjes Realizados (Tickets)</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="stat-value text-dark"><?= number_format($sAct['red'], 0) ?></span>
                                            <span class="trend-badge <?= $varRed >= 0 ? 'bg-label-success text-success' : 'bg-label-danger text-danger' ?>">
                                                <i class='bx <?= $varRed >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>'></i> <?= abs($varRed) ?>%
                                            </span>
                                        </div>
                                        <small class="text-muted mt-2">Tickets que usaron canje</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-secondary px-4 btn-editar-card"
                                    data-id="<?= $tActual['id_temporada'] ?>"
                                    data-nom="<?= $tActual['nombre'] ?>"
                                    data-ini="<?= $tActual['fecha_inicio'] ?>">
                                    <i class="bx bx-edit me-1"></i> Editar
                                </button>
                                <button class="btn btn-danger px-4" id="btnAbrirModalCerrar" data-id="<?= $tActual['id_temporada'] ?>" data-nombre="<?= $tActual['nombre'] ?>">
                                    <i class="bx bx-stop-circle me-1"></i> CERRAR TEMPORADA
                                </button>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="card h-100 border-2 border-dashed border-secondary bg-transparent d-flex justify-content-center align-items-center text-center p-5">
                        <div class="p-4">
                            <div class="avatar avatar-lg bg-label-primary rounded mb-3 mx-auto">
                                <i class='bx bx-calendar-plus fs-3 mt-3'></i>
                            </div>
                            <h5 class="text-primary fw-bold">No hay temporada activa</h5>
                            <p class="text-muted mb-4">Inicia un nuevo periodo para habilitar el sistema de puntos.</p>
                            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrar">
                                Crear Temporada
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card card-prev h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm bg-label-secondary rounded ">
                                <i class='bx bx-history m-2'></i>
                            </div>
                            <span class="fw-bold text-muted text-uppercase small">&nbsp; Último Cierre</span>
                        </div>

                        <?php if ($tAnt): ?>
                            <h5 class="text-dark fw-bold mb-1"><?= $tAnt['nombre'] ?></h5>
                            <p class="text-muted small mb-4">
                                Finalizada el: <?= date('d/m/Y', strtotime($tAnt['fecha_fin'])) ?>
                            </p>

                            <div class="border-top pt-3 mt-auto">
                                <div class="row text-center">
                                    <div class="col-6 border-end">
                                        <h6 class="mb-0 fw-bold"><?= number_format($sAnt['gen'], 0) ?></h6>
                                        <small class="text-muted" style="font-size:0.7rem">Emitidos</small>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="mb-0 fw-bold text-danger"><?= number_format($sAnt['red'], 0) ?></h6>
                                        <small class="text-muted" style="font-size:0.7rem">Canjeados</small>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 opacity-50">
                                <i class='bx bx-ghost fs-1'></i>
                                <p class="small mt-2">Sin historial.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-list-ul me-1"></i> Historial de Periodos</h5>
                <div class="d-flex gap-2">
                    <div class="input-group" style="width: 240px;">
                        <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar temporada..." autocomplete="off">
                    </div>
                    <button class="btn btn-sm btn-outline-success" id="btnExportar" title="Exportar Excel">
                        <i class="bx bxs-file-export"></i>
                    </button>
                </div>
            </div>
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tablaTemporadas">
                    <thead class="bg-primary">
                        <tr>
                            <th class="d-none" style="color: #f0f0f0;">ID</th>
                            <th style="color: #f0f0f0;">Temporada</th>
                            <th style="color: #f0f0f0;">Periodo de Vigencia</th>
                            <th style="color: #f0f0f0;">Puntos Gen.</th>
                            <th style="color: #f0f0f0;">Canjes</th>
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

<?php require VIEW_PATH . '/partials/temporada/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script src="<?= BASE_URL ?>/public/js/admin/temporada.js"></script>
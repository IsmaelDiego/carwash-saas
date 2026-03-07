<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* --- ESTILOS MODERNOS Y FORMALES --- */

    /* Card Activa: Blanca, limpia, con sombra suave y acento izquierdo */
    .card-active {
        background: #ffffff;
        border: 0;
        border-left: 6px solid #696cff;
        /* Acento de marca */
        box-shadow: 0 0.375rem 1rem 0 rgba(50, 60, 90, 0.08);
        transition: transform 0.2s;
    }

    .card-active:hover {
        transform: translateY(-2px);
    }

    /* Encabezado sutil dentro de la card */
    .card-header-active {
        background: linear-gradient(90deg, rgba(200, 220, 255, 1) 0%, rgba(255, 255, 255, 0) 100%);
        padding: 1.5rem;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Stats limpias */
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #566a7f;
    }

    .stat-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #a1acb8;
        font-weight: 600;
    }

    /* Indicadores de tendencia (Flechas) adaptados a fondo blanco */
    .trend-up {
        color: #28c76f;
        background-color: rgba(40, 199, 111, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    .trend-down {
        color: #ff3e1d;
        background-color: rgba(255, 62, 29, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
    }

    /* Card Anterior (Grisáceo para diferenciar) */
    .card-prev {
        background: #fdfdfd;
        border: 1px solid #eaeaec;
    }

    /* DataTables */
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
                                    <li><a class="dropdown-item btn-editar-card" href="javascript:void(0);"
                                            data-id="<?= $tActual['id_temporada'] ?>"
                                            data-nom="<?= $tActual['nombre'] ?>"
                                            data-ini="<?= $tActual['fecha_inicio'] ?>">
                                            <i class="bx bx-edit me-2"></i> Editar Datos</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6 border-end">
                                    <div class="d-flex flex-column h-100 ps-2">
                                        <span class="stat-label mb-2">Puntos Emitidos</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="stat-value"><?= number_format($sAct['gen'], 0) ?></span>
                                            <span class="small fw-bold <?= $varGen >= 0 ? 'trend-up' : 'trend-down' ?>">
                                                <i class='bx <?= $varGen >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>'></i> <?= abs($varGen) ?>%
                                            </span>
                                        </div>
                                        <small class="text-muted mt-2">Vs. Periodo anterior</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="d-flex flex-column h-100 ps-4">
                                        <span class="stat-label mb-2">Canjes Realizados (Puntos)</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="stat-value text-dark"><?= number_format($sAct['red'], 0) ?></span>
                                            <span class="small fw-bold <?= $varRed >= 0 ? 'trend-up' : 'trend-down' ?>">
                                                <i class='bx <?= $varRed >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>'></i> <?= abs($varRed) ?>%
                                            </span>
                                        </div>
                                        <small class="text-muted mt-2">Puntos redimidos por clientes</small>
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

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-bold text-primary"><i class="bx bx-list-ul me-1"></i> Historial Completo</h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light"><i class="bx bx-search"></i></span>
                        <input type="text" id="buscadorGlobal" class="form-control bg-light" placeholder="Buscar registros...">
                    </div>
                    <button class="btn btn-sm btn-outline-success" id="btnExportar"><i class="bx bxs-file-export"></i></button>
                </div>
            </div>
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tablaTemporadas">
                    <thead class="bg-light">
                        <tr>
                            <th class="d-none">ID</th>
                            <th>Temporada</th>
                            <th>Periodo</th>
                            <th>Puntos Gen.</th>
                            <th>Canjes</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
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
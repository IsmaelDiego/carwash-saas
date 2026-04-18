<?php require VIEW_PATH . '/layouts/header_tunnel.view.php'; 

function formatoMesAno($periodo) {
    if (!$periodo || strlen($periodo) < 7) return htmlspecialchars($periodo ?? '—');
    $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
    $p = explode('-', substr($periodo, 0, 7));
    return ($meses[$p[1]] ?? '') . ' ' . $p[0];
}
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Nav pills (estilo Account Settings de Sneat) -->
        <div class="row">
            <div class="col-md-12">
                <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:void(0);" onclick="showTab('cuenta')">
                                <i class="icon-base bx bx-user icon-sm me-1_5"></i> Mi Cuenta
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="showTab('calendario')">
                                <i class="icon-base bx bx-calendar icon-sm me-1_5"></i> Permisos y Descansos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:void(0);" onclick="showTab('pagos')">
                                <i class="icon-base bx bx-wallet icon-sm me-1_5"></i> Mis Pagos
                            </a>
                        </li>
                        <li class="nav-item ms-md-auto">
                            <a class="nav-link" href="<?= BASE_URL ?>/caja/dashboard">
                                <i class="icon-base bx bx-arrow-back icon-sm me-1_5"></i> Volver al Panel
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ═══ TAB: MI CUENTA ═══ -->
                <div class="tab-panel active" id="tab-cuenta">
                    <div class="card mb-6" style="border:none;border-radius:14px">
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                                <img src="<?= BASE_URL ?>/public/uploads/user.png"
                                     alt="user-avatar"
                                     class="d-block w-px-100 h-px-100 rounded" />
                                <div>
                                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($usuario['nombres']) ?></h4>
                                    <span class="badge bg-label-info mb-2"><?= htmlspecialchars($usuario['rol_nombre']) ?></span>
                                    <p class="text-muted mb-0 small">Miembro desde <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">DNI</label>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($usuario['dni']) ?>" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nombres</label>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($usuario['nombres']) ?>" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($usuario['email'] ?? '—') ?>" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Teléfono</label>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($usuario['telefono'] ?? '—') ?>" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Rol</label>
                                    <input class="form-control" type="text" value="<?= htmlspecialchars($usuario['rol_nombre']) ?>" disabled />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <div>
                                        <?php if ($usuario['estado']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactivo</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="border:none;border-radius:14px;border-top:3px solid #ff3e1d">
                        <div class="card-body">
                            <h5 class="fw-bold mb-1"><i class="bx bx-error-circle text-danger me-1"></i>Cerrar Sesión</h5>
                            <p class="text-muted small mb-3">Al cerrar sesión volverás a la pantalla de inicio de sesión.</p>
                            <a href="<?= BASE_URL ?>/logout" class="btn btn-danger">
                                <i class="bx bx-power-off me-1"></i> Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ═══ TAB: PERMISOS ═══ -->
                <div class="tab-panel" id="tab-calendario">
                    <div class="card" style="border:none;border-radius:14px">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-0"><i class="bx bx-calendar text-primary me-1"></i>Mis Permisos y Descansos</h5>
                            </div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalSolicitarPermiso"><i class="bx bx-plus me-1"></i>Solicitar</button>
                        </div>
                        <div class="card-body pt-0">
                            <?php if (!empty($permisos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaPermisos">
                                    <thead>
                                        <tr>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Tipo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Desde</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Hasta</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Motivo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($permisos as $perm):
                                            $ct = ['DESCANSO'=>'info', 'PERMISO'=>'warning', 'VACACION'=>'success', 'FALTA'=>'danger'];
                                            $ce = ['APROBADO'=>'success', 'PENDIENTE'=>'warning', 'RECHAZADO'=>'danger'];
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-label-<?= $ct[$perm['tipo']] ?? 'secondary' ?>"><?= $perm['tipo'] ?></span></td>
                                            <td><?= date('d/m/Y', strtotime($perm['fecha_inicio'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($perm['fecha_fin'])) ?></td>
                                            <td class="small"><?= htmlspecialchars($perm['motivo'] ?? '—') ?></td>
                                            <td><span class="badge bg-<?= $ce[$perm['estado']] ?? 'secondary' ?>"><?= $perm['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="icon-base bx bx-calendar-check" style="font-size:3rem"></i>
                                <p class="mt-2 mb-0">No tienes registros</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ═══ TAB: PAGOS ═══ -->
                <div class="tab-panel" id="tab-pagos">
                    <div class="card" style="border:none;border-radius:14px">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-0"><i class="icon-base bx bx-wallet text-success me-1"></i>Mis Pagos</h5>
                            </div>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalSolicitarAdelanto"><i class="icon-base bx bx-money-withdraw me-1"></i>Solicitar Adelanto</button>
                        </div>
                        <div class="card-body pt-0">
                            <?php if (!empty($pagos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="tablaPagos">
                                    <thead>
                                        <tr>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Tipo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Monto</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Periodo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Fecha</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago):
                                            $cp = ['PENDIENTE'=>'warning', 'PAGADO'=>'success', 'RETRASADO'=>'danger'];
                                            $ctp = ['SALARIO'=>'primary', 'ADELANTO'=>'info', 'BONO'=>'success', 'DESCUENTO'=>'danger'];
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-label-<?= $ctp[$pago['tipo']] ?? 'secondary' ?>"><?= $pago['tipo'] ?></span></td>
                                            <td class="fw-bold">S/ <?= number_format($pago['monto'], 2) ?></td>
                                            <td class="small text-capitalize"><?= formatoMesAno($pago['periodo']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pago['fecha_programada'])) ?></td>
                                            <td><span class="badge bg-<?= $cp[$pago['estado']] ?? 'secondary' ?>"><?= $pago['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-money-withdraw" style="font-size:3rem"></i>
                                <p class="mt-2 mb-0">No tienes pagos registrados</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Solicitar Permiso -->
<div class="modal fade" id="modalSolicitarPermiso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form class="modal-content shadow-lg border-0 rounded-4" onsubmit="enviarSolicitudPermiso(event)">
            <div class="modal-header px-4 py-3 bg-primary">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-calendar-plus me-1"></i> Solicitar Permiso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Tipo de Solicitud</label>
                    <select class="form-select" id="perm_tipo" required>
                        <option value="PERMISO">Permiso / Ausencia Corta</option>
                        <option value="VACACION">Vacaciones</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Desde</label>
                        <input type="date" class="form-control" id="perm_desde" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Hasta</label>
                        <input type="date" class="form-control" id="perm_hasta" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Motivo / Descripción</label>
                    <textarea class="form-control" id="perm_motivo" rows="2" maxlength="255" required placeholder="Describe brevemente..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnReqPerm">Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Solicitar Adelanto -->
<div class="modal fade" id="modalSolicitarAdelanto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form class="modal-content shadow-lg border-0 rounded-4" onsubmit="enviarSolicitudAdelanto(event)">
            <div class="modal-header px-4 py-3 bg-success">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-money-withdraw me-1"></i> Solicitar Adelanto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Monto Solicitado (S/)</label>
                    <input type="number" class="form-control" id="adelanto_monto" required min="10" step="0.50" placeholder="Ej: 50.00">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Detalle / Sustento</label>
                    <textarea class="form-control" id="adelanto_motivo" rows="2" maxlength="255" required placeholder="Ej: Adelanto de quincena, emergencia..."></textarea>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-bold" id="btnReqAdelanto">Enviar Solicitud</button>
            </div>
        </form>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>

<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
    
    $(document).ready(function() {
        const dtConfig = {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            lengthMenu: [5, 10, 25, 50],
            pageLength: 5,
            order: [[1, 'desc']], // Ordenar por fecha por defecto
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        };

        if ($('#tablaPermisos').length) {
            window.dtPermisos = $('#tablaPermisos').DataTable(dtConfig);
        }
        if ($('#tablaPagos').length) {
            window.dtPagos = $('#tablaPagos').DataTable({
                ...dtConfig,
                order: [[3, 'desc']] // Columna de fecha de pago
            });
        }
    });

    function showTab(tabId) {
        $('.tab-panel').removeClass('active');
        $('.nav-link').removeClass('active');
        $(`#tab-${tabId}`).addClass('active');
        $(`.nav-link[onclick="showTab('${tabId}')"]`).addClass('active');
    }
</script>
<script src="<?= BASE_URL ?>/public/js/caja/perfil.js"></script>

<style>
    .tab-panel { display: none; animation: fadeInTab 0.3s ease; }
    .tab-panel.active { display: block; }
    @keyframes fadeInTab { from { opacity:0; } to { opacity:1; } }
    
    /* Estilos para integrar DataTable con Sneat */
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0; margin-left: 2px; }
    .dataTables_wrapper .dataTables_length select { margin: 0 5px; }
    .dataTables_filter input { margin-left: 10px; border: 1px solid #d9dee3; border-radius: 0.375rem; padding: 0.422rem 0.875rem; }
</style>

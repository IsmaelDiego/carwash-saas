<?php require VIEW_PATH . '/layouts/header_tunnel.view.php'; ?>

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
                            <a class="nav-link" href="<?= BASE_URL ?>/operaciones/dashboard">
                                <i class="icon-base bx bx-arrow-back icon-sm me-1_5"></i> Volver al Panel
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- ═══ TAB: MI CUENTA ═══ -->
                <div class="tab-panel active" id="tab-cuenta">
                    <div class="card mb-6" style="border:none;border-radius:14px">
                        <!-- Avatar & Info -->
                        <div class="card-body">
                            <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                                <img src="<?= BASE_URL ?>/public/uploads/user.png"
                                     alt="user-avatar"
                                     class="d-block w-px-100 h-px-100 rounded"
                                     id="uploadedAvatar" />
                                <div>
                                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($usuario['nombres']) ?></h4>
                                    <span class="badge bg-label-warning mb-2"><?= htmlspecialchars($usuario['rol_nombre']) ?></span>
                                    <p class="text-muted mb-0 small">Miembro desde <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-4">
                            <form id="formPerfil" onsubmit="return false">
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
                            </form>
                        </div>
                    </div>

                    <!-- Danger Zone -->
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

                <!-- ═══ TAB: PERMISOS Y DESCANSOS ═══ -->
                <div class="tab-panel" id="tab-calendario">
                    <div class="card" style="border:none;border-radius:14px">
                        <div class="card-header border-0">
                            <h5 class="fw-bold mb-0"><i class="bx bx-calendar text-primary me-1"></i>Mis Permisos y Descansos</h5>
                            <small class="text-muted">Historial de permisos, descansos y vacaciones registrados por el administrador</small>
                        </div>
                        <div class="card-body pt-0">
                            <?php if (!empty($permisos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
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
                                            $coloresT = ['DESCANSO'=>'info','PERMISO'=>'warning','VACACION'=>'success','FALTA'=>'danger'];
                                            $coloresE = ['APROBADO'=>'success','PENDIENTE'=>'warning','RECHAZADO'=>'danger'];
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-label-<?= $coloresT[$perm['tipo']] ?? 'secondary' ?>"><?= $perm['tipo'] ?></span></td>
                                            <td><?= date('d/m/Y', strtotime($perm['fecha_inicio'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($perm['fecha_fin'])) ?></td>
                                            <td class="small"><?= htmlspecialchars($perm['motivo'] ?? '—') ?></td>
                                            <td><span class="badge bg-<?= $coloresE[$perm['estado']] ?? 'secondary' ?>"><?= $perm['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-calendar-check" style="font-size:3rem"></i>
                                <p class="mt-2 mb-0">No tienes permisos ni descansos registrados</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ═══ TAB: MIS PAGOS ═══ -->
                <div class="tab-panel" id="tab-pagos">
                    <div class="card" style="border:none;border-radius:14px">
                        <div class="card-header border-0">
                            <h5 class="fw-bold mb-0"><i class="bx bx-wallet text-success me-1"></i>Mis Pagos</h5>
                            <small class="text-muted">Historial de salarios, bonos y adelantos</small>
                        </div>
                        <div class="card-body pt-0">
                            <?php if (!empty($pagos)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Tipo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Monto</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Periodo</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Fecha Prog.</th>
                                            <th style="font-size:0.72rem;text-transform:uppercase;color:#8592a3">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pagos as $pago):
                                            $coloresP = ['PENDIENTE'=>'warning','PAGADO'=>'success','RETRASADO'=>'danger'];
                                            $coloresTP = ['SALARIO'=>'primary','ADELANTO'=>'info','BONO'=>'success','DESCUENTO'=>'danger'];
                                        ?>
                                        <tr>
                                            <td><span class="badge bg-label-<?= $coloresTP[$pago['tipo']] ?? 'secondary' ?>"><?= $pago['tipo'] ?></span></td>
                                            <td class="fw-bold">S/ <?= number_format($pago['monto'], 2) ?></td>
                                            <td class="small"><?= htmlspecialchars($pago['periodo'] ?? '—') ?></td>
                                            <td><?= date('d/m/Y', strtotime($pago['fecha_programada'])) ?></td>
                                            <td><span class="badge bg-<?= $coloresP[$pago['estado']] ?? 'secondary' ?>"><?= $pago['estado'] ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bx bx-money-withdraw" style="font-size:3rem"></i>
                                <p class="mt-2 mb-0">No tienes pagos registrados aún</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-pills .nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.closest('.nav-link').classList.add('active');
}
</script>
<style>
    .tab-panel { display: none; animation: fadeInTab 0.3s ease; }
    .tab-panel.active { display: block; }
    @keyframes fadeInTab { from { opacity:0; } to { opacity:1; } }
</style>

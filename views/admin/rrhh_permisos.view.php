<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    .dataTables_filter, .dataTables_length { display: none !important; }
    .dataTables_paginate { display: flex !important; justify-content: flex-start !important; margin-top: 1.5rem !important; padding-top: 1rem; border-top: 1px solid #f0f0f0; }
    .dataTables_info { text-align: right !important; margin-top: 1.5rem !important; padding-top: 1rem; color: #b0b0b0 !important; }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <h5 class="card-header border-bottom mb-3">
                    <i class="bx bx-calendar text-primary me-1"></i> CONTROL DE PERMISOS
                </h5>
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/empleado">Personal</a></li>
                            <li class="breadcrumb-item active text-primary">Permisos</li>
                        </ol>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group" style="width: 240px;">
                            <input type="text" id="buscadorGlobal" class="form-control" placeholder="Buscar permiso..." autocomplete="off">
                            <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                        </div>

                        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarPermiso">
                            <i class="bx bx-plus me-1"></i> Nuevo Permiso
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="table-responsive text-nowrap px-3">
                <table class="table table-hover w-100 my-3" id="tbPermisos" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th class="d-none">ID Permiso</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Empleado</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Tipo</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Desde</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Hasta</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Motivo</th>
                            <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Estado</th>
                            <th class="text-center text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($permisos as $p): ?>
                        <tr>
                            <td class="d-none"><?= $p['id_permiso'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($p['empleado']) ?></td>
                            <td><?= $p['tipo'] ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_inicio'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['fecha_fin'])) ?></td>
                            <td><?= htmlspecialchars($p['motivo'] ?? '') ?></td>
                            <td>
                                <?php if($p['estado'] == 'PENDIENTE'): ?>
                                    <span class="badge bg-label-warning">Pendiente</span>
                                <?php elseif($p['estado'] == 'APROBADO'): ?>
                                    <span class="badge bg-label-success">Aprobado</span>
                                <?php else: ?>
                                    <span class="badge bg-label-danger">Rechazado</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($p['estado'] == 'PENDIENTE'): ?>
                                    <button class="btn btn-sm btn-outline-success btn-estado" data-id="<?= $p['id_permiso'] ?>" data-estado="APROBADO" title="Aprobar"><i class="bx bx-check"></i></button>
                                    <button class="btn btn-sm btn-outline-danger btn-estado" data-id="<?= $p['id_permiso'] ?>" data-estado="RECHAZADO" title="Rechazar"><i class="bx bx-x"></i></button>
                                <?php else: ?>
                                    <span class="text-muted"><i class="bx bx-check-double"></i> <?= ucfirst(strtolower($p['estado'])) ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalRegistrarPermiso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRegistrarPermiso">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Permiso o Descanso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Empleado</label>
                        <select name="id_usuario" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach($empleados as $e): if($e['id_rol'] == 1) continue; ?>
                                <option value="<?= $e['id_usuario'] ?>"><?= htmlspecialchars($e['nombres']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Ausencia</label>
                        <select name="tipo" class="form-select" required>
                            <option value="DESCANSO">Día Libre / Descanso Médico</option>
                            <option value="PERMISO">Permiso por horas/días</option>
                            <option value="VACACION">Vacaciones</option>
                            <option value="FALTA">Falta Injustificada</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Desde</label>
                            <input type="date" name="fecha_inicio" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="fecha_fin" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado Inicial</label>
                        <select name="estado" class="form-select" required>
                            <option value="APROBADO">Aprobado</option>
                            <option value="PENDIENTE">Pendiente de Aprobación</option>
                            <option value="RECHAZADO">Rechazado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo (Opcional)</label>
                        <textarea name="motivo" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const BASE_URL = "<?= BASE_URL ?>";

$(document).ready(function() {
    let tabla = $('#tbPermisos').DataTable({
        language: {
            lengthMenu: " _MENU_ ",
            info: "Mostrando _START_ a _END_ de _TOTAL_ permisos",
            infoEmpty: "0 permisos",
            infoFiltered: "(filtrado)",
            paginate: { next: "Sig", previous: "Ant" },
            zeroRecords: "No hay registros"
        },
        order: [[3, 'desc']]
    });

    $("#buscadorGlobal").on("keyup", function() { tabla.search(this.value).draw(); });

    $('#formRegistrarPermiso').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: BASE_URL + '/admin/permiso/registrar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                if(res.success) {
                    location.reload();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            }
        });
    });

    $(document).on('click', '.btn-estado', function() {
        let id_permiso = $(this).data('id');
        let estado = $(this).data('estado');
        let texto = estado == 'APROBADO' ? 'aprobar' : 'rechazar';

        Swal.fire({
            title: `¿Desea ${texto} el permiso?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, confirmar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(BASE_URL + '/admin/permiso/cambiarestado', { id_permiso: id_permiso, estado: estado }, function(res) {
                    if(res.success) location.reload();
                    else Swal.fire('Error', res.message, 'error');
                });
            }
        });
    });
});
</script>

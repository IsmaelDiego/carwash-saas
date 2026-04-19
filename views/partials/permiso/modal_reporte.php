<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- Modal Central de Reportes BI - PERMISOS LABORALES -->
<div class="modal fade" id="modalReportesPermisos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bx-calendar-event fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">BI & CONTROL DE AUSENCIAS</h5>
                        <small class="text-white-50">Gestión de Permisos y Vacaciones</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesPermisos" action="<?= BASE_URL ?>/admin/permiso/exportar" method="GET" target="_blank">
                <div class="modal-body p-4 bg-light">
                    <!-- Nivel de Análisis -->
                    <div class="row g-2 mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted mb-2">1. Nivel de Análisis</label>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo_rep" id="perm_rep_bitacora" value="general" checked>
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="perm_rep_bitacora" style="border-radius: 12px;">
                                <i class="bx bx-list-ul fs-2 mb-2"></i>
                                <span class="fw-bold small">Bitácora</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo_rep" id="perm_rep_conso" value="consolidado">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="perm_rep_conso" style="border-radius: 12px;">
                                <i class="bx bx-group fs-2 mb-2"></i>
                                <span class="fw-bold small">Consolidado</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo_rep" id="perm_rep_analisis" value="analisis">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="perm_rep_analisis" style="border-radius: 12px;">
                                <i class="bx bx-chart fs-2 mb-2"></i>
                                <span class="fw-bold small">Análisis</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filtros Inteligentes -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Empleado (Opcional)</label>
                                    <select class="form-select" name="id_usuario">
                                        <option value="todos">Todos los Colaboradores</option>
                                        <?php 
                                            global $pdo;
                                            $stmtE = $pdo->query("SELECT id_usuario, nombres FROM usuarios WHERE estado = 1 ORDER BY nombres ASC");
                                            while($e = $stmtE->fetch(PDO::FETCH_ASSOC)):
                                        ?>
                                            <option value="<?= $e['id_usuario'] ?>"><?= $e['nombres'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="todos">Todos</option>
                                        <option value="PENDIENTE">Pendientes</option>
                                        <option value="APROBADO">Aprobados</option>
                                        <option value="RECHAZADO">Rechazados</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">Formato</label>
                                    <select class="form-select fw-bold" name="formato">
                                        <option value="pdf">📄 PDF Formal</option>
                                        <option value="csv">📊 Excel Data</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 bg-white justify-content-between">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm">
                        <i class="bx bx-download me-1"></i> GENERAR REPORTE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formReportesPermisos').addEventListener('submit', function() {
    setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesPermisos'));
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
    }, 1000);
});
</script>

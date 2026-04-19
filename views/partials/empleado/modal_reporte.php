<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- Modal Central de Reportes BI - PERSONAL -->
<div class="modal fade" id="modalReportesPersonal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bx-group fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white-50">Análisis Táctico de Personal y RRHH</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesPersonal" action="<?= BASE_URL ?>/admin/empleado/exportar" method="GET" target="_blank">
                <div class="modal-body p-4 bg-light">
                    <!-- Nivel de Análisis -->
                    <div class="row g-2 mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted mb-2">1. Nivel de Análisis</label>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="emp_rep_maestro" value="general" checked>
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="emp_rep_maestro" style="border-radius: 12px;">
                                <i class="bx bx-user-circle fs-2 mb-2"></i>
                                <span class="fw-bold small">Maestro</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="emp_rep_prod" value="rendimiento">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="emp_rep_prod" style="border-radius: 12px;">
                                <i class="bx bx-bolt-circle fs-2 mb-2"></i>
                                <span class="fw-bold small">Rendimiento</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="emp_rep_sec" value="seguridad">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="emp_rep_sec" style="border-radius: 12px;">
                                <i class="bx bx-shield-quarter fs-2 mb-2"></i>
                                <span class="fw-bold small">Seguridad</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filtros Dinámicos -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Filtrar por Cargo (Rol)</label>
                                    <select class="form-select" name="rol">
                                        <option value="todos">Todos los Cargos</option>
                                        <?php 
                                            global $pdo;
                                            $stmtR = $pdo->query("SELECT * FROM roles ORDER BY nombre ASC");
                                            while($r = $stmtR->fetch(PDO::FETCH_ASSOC)):
                                        ?>
                                            <option value="<?= $r['id_rol'] ?>"><?= $r['nombre'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-muted">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option value="todos">Todos</option>
                                        <option value="1">Activos</option>
                                        <option value="0">Inactivos</option>
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
document.getElementById('formReportesPersonal').addEventListener('submit', function() {
    setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesPersonal'));
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
    }, 1000);
});
</script>

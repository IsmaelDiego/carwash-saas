<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- Modal Central de Reportes BI - CONTROL DE PAGOS (PLANILLA) -->
<div class="modal fade" id="modalReportesPagos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-success">
                            <i class="bx bx-money fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">BI & CONTROL DE DESEMBOLSOS</h5>
                        <small class="text-white-50">Auditoría Financiera de Personal</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesPagos" action="<?= BASE_URL ?>/admin/pago/exportar" method="GET" target="_blank">
                <div class="modal-body p-4 bg-light">
                    <!-- Nivel de Análisis -->
                    <div class="row g-2 mb-4">
                        <label class="form-label fw-bold text-uppercase small text-muted mb-2">1. Nivel de Análisis</label>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="pago_rep_bitacora" value="general" checked>
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="pago_rep_bitacora" style="border-radius: 12px;">
                                <i class="bx bx-list-check fs-2 mb-2"></i>
                                <span class="fw-bold small">Bitácora</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="pago_rep_deuda" value="pendientes">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="pago_rep_deuda" style="border-radius: 12px;">
                                <i class="bx bx-timer fs-2 mb-2"></i>
                                <span class="fw-bold small">Pendientes</span>
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="tipo" id="pago_rep_conso" value="consolidado">
                            <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none h-100" for="pago_rep_conso" style="border-radius: 12px;">
                                <i class="bx bx-pie-chart-alt-2 fs-2 mb-2"></i>
                                <span class="fw-bold small">Consolidado</span>
                            </label>
                        </div>
                    </div>

                    <!-- Filtros Inteligentes -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">Empleado (Opcional)</label>
                                    <select class="form-select select2" name="id_usuario" id="pago_rep_usuario">
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
                                    <label class="form-label fw-bold small text-muted">Periodo</label>
                                    <select class="form-select" name="periodo">
                                        <option value="all">Todo el Historial</option>
                                        <option value="3m">Últimos 3 Meses</option>
                                        <option value="6m">Últimos 6 Meses</option>
                                        <optgroup label="Por Año">
                                            <?php 
                                                $stmtY = $pdo->query("SELECT DISTINCT YEAR(fecha_creacion) as anio FROM pagos_empleados ORDER BY anio DESC");
                                                while($ay = $stmtY->fetch(PDO::FETCH_ASSOC)):
                                            ?>
                                                <option value="year_<?= $ay['anio'] ?>">Todo el Año <?= $ay['anio'] ?></option>
                                            <?php endwhile; ?>
                                        </optgroup>
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
                        <i class="bx bx-download me-1"></i> GENERAR Y DESCARGAR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formReportesPagos').addEventListener('submit', function() {
    setTimeout(() => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesPagos'));
        if (modal) modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
    }, 1000);
});
</script>

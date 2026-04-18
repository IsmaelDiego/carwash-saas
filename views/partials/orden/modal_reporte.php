<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- Modal Central de Reportes BI - Órdenes -->
<div class="modal fade" id="modalReportesOrden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium Idéntico al de Caja -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bx-calculator fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white opacity-75">Gestión y Análisis de Órdenes de Servicio</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesOrden" action="<?= BASE_URL ?>/admin/orden/exportar" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Selección del Tipo de Análisis -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-3 d-block">1. Selecciona el Tipo de Análisis</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="ord_rep_cons" value="consolidado" checked>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="ord_rep_cons" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-list-check fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Consolidado</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="ord_rep_det" value="detallado">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="ord_rep_det" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-search-alt-2 fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Análisis Items</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="ord_rep_pag" value="pagos">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="ord_rep_pag" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-credit-card fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Método Pago</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Filtro de Rango de Fechas -->
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold small text-muted">Fecha Inicio</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                <input type="date" class="form-control" name="f_inicio" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <label class="form-label fw-bold small text-muted">Fecha Fin</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-calendar-check"></i></span>
                                <input type="date" class="form-control" name="f_fin" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-1 opacity-25">
                        </div>

                        <!-- Filtro de Usuario -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Filtrar por Cajero / Usuario</label>
                            <select class="form-select select2" name="usuario">
                                <option value="TODOS">--- TODOS LOS USUARIOS ---</option>
                                <?php 
                                global $pdo;
                                // Obtenemos solo los usuarios que realmente han creado órdenes
                                $stmt = $pdo->query("SELECT DISTINCT u.id_usuario, u.nombres 
                                                    FROM usuarios u 
                                                    INNER JOIN ordenes o ON u.id_usuario = o.id_usuario_creador 
                                                    WHERE u.estado = 1 
                                                    ORDER BY u.nombres ASC");
                                while ($u = $stmt->fetch()): ?>
                                    <option value="<?= $u['id_usuario'] ?>"><?= $u['nombres'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Filtro de Estado -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Estado de la Orden</label>
                            <select class="form-select" name="estado">
                                <option value="TODOS">CUALQUIER ESTADO</option>
                                <option value="FINALIZADO">FINALIZADOS (Ventas)</option>
                                <option value="POR_COBRAR">POR COBRAR</option>
                                <option value="EN_PROCESO">EN PROCESO / COLA</option>
                                <option value="ANULADO">ANULADOS</option>
                            </select>
                        </div>

                        <!-- Formato de Salida -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block">2. Elige el Formato</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="ord_fmt_pdf" value="pdf" checked>
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="ord_fmt_pdf">
                                        <i class="bx bxs-file-pdf me-2 fs-4"></i> PDF
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="ord_fmt_csv" value="csv">
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="ord_fmt_csv">
                                        <i class="bx bx-spreadsheet me-2 fs-4"></i> Excel
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-4 justify-content-between">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="bx bx-download me-1"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formReportesOrden').addEventListener('submit', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesOrden'));
    setTimeout(() => {
        modal.hide();
        // Forzar limpieza de backdrops de Bootstrap
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
        document.body.classList.remove('modal-open');
    }, 1000);
});
</script>

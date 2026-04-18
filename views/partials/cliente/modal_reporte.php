<?php 
    date_default_timezone_set('America/Lima');
?>
<div class="modal fade" id="modalReportesCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium Idéntico al de Órdenes/Caja -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bxs-group fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white opacity-75">Gestión y Análisis de Inteligencia de Clientes</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesCliente" action="<?= BASE_URL ?>/admin/cliente/exportar" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Selección del Tipo de Análisis -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-3 d-block">1. ¿Qué información necesitas?</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="cli_rep_gen" value="general" checked>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="cli_rep_gen" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-book-content fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Listado Base</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="cli_rep_puntos" value="puntos">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="cli_rep_puntos" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bxs-star fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Top Puntos</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="cli_rep_mkt" value="marketing">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="cli_rep_mkt" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bxl-whatsapp fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Marketing</span>
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

                        <!-- Filtro por Puntos Mínimos -->
                        <div class="col-12 sec-points" style="display:none;">
                            <label class="form-label fw-bold small text-muted">Mostrar clientes con más de (puntos):</label>
                            <input type="number" class="form-control" name="min_puntos" value="0" min="0">
                        </div>

                        <!-- Filtro por Estado WhatsApp -->
                        <div class="col-12 sec-whatsapp" style="display:none;">
                            <label class="form-label fw-bold small text-muted">Filtrar por Disponibilidad WhatsApp:</label>
                            <select class="form-select" name="whatsapp_status">
                                <option value="TODOS">TODOS LOS CLIENTES</option>
                                <option value="1">SOLO CON WHATSAPP HABILITADO</option>
                                <option value="0">SOLO SIN WHATSAPP</option>
                            </select>
                        </div>

                        <!-- Formato de Salida -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block">2. Elige el Formato</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="cli_fmt_pdf" value="pdf" checked>
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="cli_fmt_pdf">
                                        <i class="bx bxs-file-pdf me-2 fs-4"></i> PDF
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="cli_fmt_csv" value="csv">
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="cli_fmt_csv">
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
// Lógica para mostrar/ocultar filtros según el reporte seleccionado
document.querySelectorAll('input[name="tipo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelector('.sec-points').style.display = (this.value === 'puntos') ? 'block' : 'none';
        document.querySelector('.sec-whatsapp').style.display = (this.value === 'marketing') ? 'block' : 'none';
    });
});

document.getElementById('formReportesCliente').addEventListener('submit', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesCliente'));
    setTimeout(() => {
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
        document.body.classList.remove('modal-open');
    }, 1000);
});
</script>

<!-- Modal Central de Reportes BI - VEHÍCULOS -->
<?php 
    date_default_timezone_set('America/Lima');
?>
<div class="modal fade" id="modalReportesVehiculo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium Idéntico al de Órdenes/Caja/Clientes -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bxs-car fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white opacity-75">Gestión y Análisis de Flota de Vehículos</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesVehiculo" action="<?= BASE_URL ?>/admin/vehiculo/exportar" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Selección del Tipo de Análisis -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-3 d-block">1. Elige el Tipo de Reporte</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipo" id="veh_rep_gen" value="general" checked>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="veh_rep_gen" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bxs-car-garage fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Flota General</span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="tipo" id="veh_rep_cat" value="por_categoria">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="veh_rep_cat" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-category fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Por Categoría</span>
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

                        <!-- Filtro por Categoría -->
                        <div class="col-12 sec-categoria mt-4" style="display:none;">
                            <label class="form-label fw-bold small text-muted">Selecciona la Categoría:</label>
                            <select class="form-select" name="id_categoria">
                                <option value="TODAS">--- TODAS LAS CATEGORÍAS ---</option>
                                <?php 
                                global $pdo;
                                $stmtCat = $pdo->query("SELECT id_categoria, nombre FROM categorias_vehiculos ORDER BY nombre ASC");
                                while($cat = $stmtCat->fetch()): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Formato de Salida -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block">2. Elige el Formato</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="veh_fmt_pdf" value="pdf" checked>
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="veh_fmt_pdf">
                                        <i class="bx bxs-file-pdf me-2 fs-4"></i> PDF
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="veh_fmt_csv" value="csv">
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="veh_fmt_csv">
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
// Lógica para mostrar/ocultar selección de categoría
document.querySelectorAll('input[name="tipo"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelector('.sec-categoria').style.display = (this.value === 'por_categoria') ? 'block' : 'none';
    });
});

document.getElementById('formReportesVehiculo').addEventListener('submit', function() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportesVehiculo'));
    setTimeout(() => {
        modal.hide();
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.style.overflow = 'auto';
        document.body.classList.remove('modal-open');
    }, 1000);
});
</script>

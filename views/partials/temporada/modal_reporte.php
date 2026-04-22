<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- Modal Central de Reportes BI - TEMPORADAS -->
<div class="modal fade" id="modalReportesTemporada" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Premium -->
            <div class="modal-header bg-dark p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar flex-shrink-0 me-3">
                        <span class="avatar-initial rounded-circle bg-label-dark">
                            <i class="bx bx-calendar-star fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white opacity-75">Periodos y Métricas de Fidelización</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formReportesTemporada" action="<?= BASE_URL ?>/admin/temporada/exportar" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Selección del Tipo de Análisis (3 Columnas) -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-3 d-block">1. Nivel de Análisis</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="temp_rep_gen" value="general" checked>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="temp_rep_gen" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-calendar fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Bitácora</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="temp_rep_rend" value="rendimiento">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="temp_rep_rend" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-line-chart fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Fidelidad</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="tipo" id="temp_rep_audit" value="impacto">
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center justify-content-center shadow-none" for="temp_rep_audit" style="min-height: 90px; border-radius: 12px;">
                                        <i class="bx bx-history fs-2 mb-2"></i>
                                        <span class="fw-bold" style="font-size: 0.75rem;">Situacional</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Rango de Tiempo Dinámico -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold small text-muted">Rango de Tiempo</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-time-five"></i></span>
                                <select class="form-select" name="periodo">
                                    <option value="all">Todo el Historial</option>
                                    <option value="3m">Últimos 3 Meses</option>
                                    <option value="6m">Últimos 6 Meses</option>
                                    
                                    <!-- Años con registros detectados automáticamente -->
                                    <optgroup label="Por Año Específico">
                                        <?php 
                                            global $pdo;
                                            $stmtY = $pdo->query("SELECT DISTINCT YEAR(fecha_inicio) as anio FROM temporadas ORDER BY anio DESC");
                                            $anios = $stmtY->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($anios as $a):
                                        ?>
                                            <option value="year_<?= $a['anio'] ?>">Todo el Año <?= $a['anio'] ?></option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <!-- Filtro de Estado -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold small text-muted">Estado de Temporada</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                                <select class="form-select" name="estado">
                                    <option value="todos">Todos los Estados</option>
                                    <option value="1">Solo Temporadas Activas (En Curso)</option>
                                    <option value="0">Solo Temporadas Finalizadas (Cerradas)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Formato de Salida -->
                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold text-uppercase small text-muted mb-2 d-block">2. Elige el Formato</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="temp_fmt_pdf" value="pdf" checked>
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="temp_fmt_pdf">
                                        <i class="bx bxs-file-pdf me-2 fs-4"></i> PDF
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="formato" id="temp_fmt_csv" value="csv">
                                    <label class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center py-2" for="temp_fmt_csv">
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



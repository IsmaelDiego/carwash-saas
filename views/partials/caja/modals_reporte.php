<?php 
    date_default_timezone_set('America/Lima');
?>
<!-- ═══════════════════════════════════════════════════════════════
     MODAL: CENTRAL DE REPORTES DE CAJA (ARQUEOS) PRO
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalReportesCaja" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-calculator fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CENTRAL DE REPORTES BI</h5>
                        <small class="text-white-50">Inteligencia de Arqueos y Flujo de Efectivo</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReportesCaja" action="<?= BASE_URL ?>/admin/caja/exportararqueos" method="GET" target="_blank">
                <div class="modal-body p-4 bg-light">
                    <!-- TIPO PRINCIPAL Y FORMATO -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-primary small mb-2"><i class="bx bx-category me-1"></i> MATRIZ DE EXTRACCIÓN</label>
                                    <select class="form-select fw-bold" name="tipo" id="rep_tipo_caja" required style="border: 2px solid #696cff;">
                                        <option value="general" selected>📊 1. Resumen de Arqueos (Consolidado)</option>
                                        <option value="detallado">📝 2. Detalle de Operaciones (Servicios y Ventas)</option>
                                        <option value="pagos">💳 3. Análisis de Métodos de Pago</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-dark small mb-2"><i class="bx bxs-file-pdf me-1"></i> FORMATO</label>
                                    <select class="form-select bg-label-secondary fw-bold" name="formato" id="rep_formato">
                                        <option value="csv" selected>Excel / CSV</option>
                                        <option value="pdf">Documento PDF</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PANELES DE FILTRO DINÁMICOS -->
                    <div class="row g-3">
                        
                        <!-- FILTROS: CAJERO / OPERADOR -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-dark small"><i class="bx bx-user me-1"></i> Responsable de Caja</label>
                                    <select class="form-select bg-label-secondary" name="id_usuario" id="rep_cajero">
                                        <option value="TODOS">Todos los Empleados</option>
                                        <!-- Javascript lo llena -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- FILTROS: ESTADO DE SESIÓN -->
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body p-3">
                                    <label class="form-label fw-bold text-dark small"><i class="bx bx-toggle-right me-1"></i> Estado de Sesión</label>
                                    <select class="form-select bg-label-secondary" name="estado">
                                        <option value="TODOS">Cualquier Estado (Histórico)</option>
                                        <option value="CERRADA" selected>Sesiones Cerradas (Arqueadas)</option>
                                        <option value="ABIERTA">Sesiones en Curso (Activas)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- FILTROS: RANGO DE FECHAS -->
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small">Filtro de Tiempo</label>
                                            <select class="form-select bg-label-secondary" name="rango" id="rep_rango_caja">
                                                <option value="MES_ACTUAL" selected>Mes en Curso</option>
                                                <option value="HOY">Operaciones de Hoy</option>
                                                <option value="PERSONALIZADO">Rango Personalizado...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 panel-rango-personalizado" style="display:none;">
                                            <label class="form-label fw-bold text-dark small">Desde</label>
                                            <input type="date" class="form-control" name="fecha_inicio" id="rep_caja_f_inicio" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="col-md-4 panel-rango-personalizado" style="display:none;">
                                            <label class="form-label fw-bold text-dark small">Hasta</label>
                                            <input type="date" class="form-control" name="fecha_fin" id="rep_caja_f_fin" value="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- FOOTER -->
                <div class="modal-footer p-4 border-top bg-white d-flex justify-content-between align-items-center">
                    <button class="btn btn-outline-secondary px-4 fw-bold" type="button" id="btnRepLimpiarCaja"><i class="bx bx-eraser me-2"></i> LIMPIAR</button>
                    <div>
                        <button class="btn btn-white fw-bold text-muted border me-2" data-bs-dismiss="modal" type="button">Cancelar</button>
                        <button class="btn btn-dark fw-bold shadow-sm px-4" type="submit"><i class="bx bxs-file-export me-2"></i> GENERAR REPORTE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formReportesCaja').addEventListener('submit', function() {
    // Cerramos el modal después de un pequeño delay para que la descarga inicie
    setTimeout(() => {
        const modalEl = document.getElementById('modalReportesCaja');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
        
        // Limpieza forzada del backdrop y estilos de Bootstrap
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 800);
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('modalReportesCaja');
        
        // Manejo de Rango de Fechas
        $('#rep_rango_caja').on('change', function() {
            if ($(this).val() === 'PERSONALIZADO') {
                $('.panel-rango-personalizado').fadeIn(200);
            } else {
                $('.panel-rango-personalizado').hide();
            }
        });

        // Limpiar
        $('#btnRepLimpiarCaja').on('click', function() {
            document.getElementById('formReportesCaja').reset();
            $('#rep_rango_caja').trigger('change');
        });

        // Autocompletar Select de Empleados al abrir modal
        modalEl.addEventListener('show.bs.modal', function() {
            // Reutilizamos la lista de empleados que ya tenemos en la vista principal de caja
            const selOriginal = document.getElementById('selCajeroManual');
            const options = Array.from(selOriginal.options)
                .filter(opt => opt.value !== "")
                .map(opt => `<option value="${opt.value}">${opt.textContent.replace('(CON CAJA ABIERTA)', '').trim()}</option>`)
                .join('');
            
            document.getElementById('rep_cajero').innerHTML = '<option value="TODOS">Todos los Empleados</option>' + options;
        });

        // Limpiar y Cerrar al generar
        $('#formReportesCaja').on('submit', function() {
            setTimeout(() => {
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();
                $('#btnRepLimpiarCaja').click();
            }, 800);
        });
    });
</script>

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-play-circle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">INICIAR NUEVA TEMPORADA</h5>
                        <small class="text-white-50">Registra un nuevo periodo para acumular puntos</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarTemporada">
                <div class="modal-body p-4">
                    <div class="alert alert-primary mb-4 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius:12px; background: rgba(105, 108, 255, 0.08);">
                        <i class='bx bx-info-circle fs-2 text-primary'></i>
                        <small class="text-primary fw-medium">Esta temporada iniciará activa. No podrás crear otra hasta que esta se cierre formalmente.</small>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nombre del Periodo <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-calendar-star"></i></span>
                                <input type="text" class="form-control" name="nombre" placeholder="Ej. Campaña de Verano 2026" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Fecha de Inicio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-calendar-event"></i></span>
                                <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bx bx-save me-2"></i>INICIAR AHORA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-edit fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">EDITAR DATOS DEL PERIODO</h5>
                        <small class="text-white-50">Actualiza la información de la campaña promocional</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarTemporada">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id_temporada" name="id_temporada">
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nombre Actualizado <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning"><i class="bx bx-rename"></i></span>
                                <input type="text" id="edit_nombre" name="nombre" class="form-control fw-bold" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nueva Fecha de Inicio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning"><i class="bx bx-calendar"></i></span>
                                <input type="date" id="edit_inicio" name="fecha_inicio" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm"><i class="bx bx-save me-2"></i>GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-list-ul fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">DETALLE DEL PERIODO</h5>
                        <small class="text-white-50">Información de la temporada</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer p-4 border-top bg-white">
                <button type="button" class="btn btn-white fw-bold text-muted border w-100" data-bs-dismiss="modal">ENTENDIDO</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCerrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 70px; height: 70px;">
                    <i class='bx bx-stop-circle text-danger' style='font-size: 3rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-1">¿Cerrar Periodo?</h4>
                <p class="text-muted mb-4 small">
                    Finalizarás la temporada permanentemente:<br>
                    <span id="nombre_cerrar" class="badge bg-white border border-secondary text-secondary fs-6 mt-2 py-2 px-3 w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>
                
                <div class="alert alert-warning mb-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3" style="border-radius:12px; background: rgba(255, 171, 0, 0.08);">
                    <i class="bx bx-error fs-2 text-warning"></i>
                    <small class="text-warning fw-medium text-start">La fecha fin será establecida como <strong>HOY</strong> de forma permanente.</small>
                </div>
                
                <form id="formCerrarTemporada">
                    <input type="hidden" id="id_cerrar" name="id_temporada">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm">SÍ, FINALIZAR AHORA</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
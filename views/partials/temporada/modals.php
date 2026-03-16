<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-primary px-4 py-4">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center"><i class="bx bx-play-circle fs-3 me-2"></i> INICIAR NUEVA TEMPORADA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarTemporada">
                <div class="modal-body p-4">
                    <div class="bg-label-primary p-3 rounded-3 mb-4 border border-primary border-opacity-10">
                        <div class="d-flex">
                            <i class='bx bx-info-circle me-2 fs-4'></i>
                            <div class="small fw-semibold">Esta temporada iniciará activa. No podrás crear otra hasta que esta se cierre formalmente.</div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nombre del Periodo</label>
                        <div class="input-group input-group-merge shadow-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-calendar-star text-primary"></i></span>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej. Campaña de Verano 2026" required>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted small text-uppercase">Fecha de Inicio</label>
                        <div class="input-group input-group-merge shadow-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-calendar-event text-primary"></i></span>
                            <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-label-secondary fw-bold px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">INICIAR AHORA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-4 border-0">
            <div class="modal-header bg-warning px-4 py-4">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center"><i class="bx bx-edit fs-3 me-2"></i> EDITAR DATOS DEL PERIODO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarTemporada">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id_temporada" name="id_temporada">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nombre Actualizado</label>
                        <div class="input-group input-group-merge shadow-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-rename text-warning"></i></span>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control fw-bold" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nueva Fecha de Inicio</label>
                        <div class="input-group input-group-merge shadow-sm">
                            <span class="input-group-text bg-white"><i class="bx bx-calendar text-warning"></i></span>
                            <input type="date" id="edit_inicio" name="fecha_inicio" class="form-control shadow-none" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-label-secondary fw-bold px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning text-white fw-bold px-4 shadow-sm">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary border-bottom">
                <h3 class="modal-title text-dark fw-bold d-flex align-items-center text-white"><i class="bx bx-list-ul fs-3 me-2"></i>Detalle del Periodo</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer p-2"><button type="button" class="btn btn-primary w-100 btn-sm" data-bs-dismiss="modal">ENTENDIDO</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCerrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="modal-body text-center p-5">
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 80px; height: 80px; box-shadow: 0 0 20px rgba(255, 62, 29, 0.2);">
                    <i class='bx bx-stop-circle text-danger' style='font-size: 3.5rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-2">¿Cerrar Periodo?</h4>
                <p class="text-muted mb-4 small">
                    Finalizarás la temporada:<br>
                    <span id="nombre_cerrar" class="badge bg-secondary text-white fs-6 mt-2 py-2 px-3 w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>
                
                <div class="bg-label-warning p-3 rounded-3 mb-4 text-start border border-warning border-opacity-10">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error fs-4 me-2"></i>
                        <span class="small fw-semibold">La fecha fin será establecida como <strong>HOY</strong> de forma permanente.</span>
                    </div>
                </div>
                
                <form id="formCerrarTemporada">
                    <input type="hidden" id="id_cerrar" name="id_temporada">
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold rounded-pill">SÍ, FINALIZAR AHORA</button>
                        <button type="button" class="btn btn-label-secondary fw-bold rounded-pill" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
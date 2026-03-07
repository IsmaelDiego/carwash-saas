<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary">Nueva Temporada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="registrarTemporada">
                <div class="modal-body p-4">
                    <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
                        <i class='bx bx-info-circle me-2 fs-4'></i>
                        <div class="small">Esta temporada iniciará activa. No podrás crear otra hasta que esta se cierre.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej. Verano 2026" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Iniciar Temporada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Editar Temporada</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTemporada">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id_temporada" name="id_temporada">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" id="edit_nombre" name="nombre" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Fecha Inicio</label>
                        <input type="date" id="edit_inicio" name="fecha_inicio" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Detalle del Periodo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer p-2"><button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCerrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-top border-5 border-danger">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger">
                    <i class='bx bx-stop-circle' style='font-size: 4rem;'></i>
                </div>
                <h4 class="mb-2 fw-bold text-danger">¿Cerrar Temporada?</h4>
                <p class="text-muted mb-4">
                    Estás a punto de finalizar: <br>
                    <strong id="nombre_cerrar" class="text-dark fs-5"></strong>
                </p>
                <div class="alert alert-warning text-start small mb-4">
                    <i class="bx bx-error me-1"></i>
                    Esta acción establecerá la fecha fin como <strong>HOY</strong>. Podrás crear una nueva temporada inmediatamente después.
                </div>
                
                <form id="formCerrarTemporada">
                    <input type="hidden" id="id_cerrar" name="id_temporada">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">SÍ, FINALIZAR AHORA</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
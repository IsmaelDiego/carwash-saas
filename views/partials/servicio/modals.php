<!-- Modal: Registrar Servicio -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="bx bx-plus-circle me-2"></i> Nuevo Servicio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRegistrarServicio">
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Nombre del Servicio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" placeholder="Ej: Lavado Completo" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duración (min)</label>
                            <input type="number" class="form-control" name="duracion_minutos" value="30" min="5">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="2" placeholder="Descripción breve..."></textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="estado" value="1" checked>
                            <label class="form-check-label">Servicio Activo</label>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h6 class="text-primary fw-bold mb-3"><i class="bx bx-dollar-circle me-1"></i> Precios por Tipo de Vehículo</h6>
                    <div class="row g-3" id="contenedorPreciosRegistrar"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Editar Servicio -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary"><i class="bx bx-edit me-2"></i> Editar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarServicio">
                <input type="hidden" name="id_servicio" id="edit_id_servicio">
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duración (min)</label>
                            <input type="number" class="form-control" name="duracion_minutos" id="edit_duracion" min="5">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" id="edit_descripcion" rows="2"></textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="estado" id="edit_estado" value="1">
                            <label class="form-check-label">Servicio Activo</label>
                        </div>
                    </div>
                    <hr class="my-4">
                    <h6 class="text-primary fw-bold mb-3"><i class="bx bx-dollar-circle me-1"></i> Precios</h6>
                    <div class="row g-3" id="contenedorPreciosEditar"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Eliminar Servicio -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="bx bx-error-circle text-danger mb-3" style="font-size: 4rem;"></i>
                <h4 class="mb-2">¿Eliminar servicio?</h4>
                <p class="text-muted mb-4">Se eliminará <strong id="nombre_eliminar"></strong>.</p>
                <form id="formEliminarServicio">
                    <input type="hidden" id="delete_id_servicio" name="id_servicio">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
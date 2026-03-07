<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white fw-bold"><i class='bx bxs-star me-1'></i> CREAR NUEVA CAMPAÑA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="registrarPromocion">
                <div class="modal-body p-4">
                    
                    <div class="row g-4">
                        <div class="col-md-7">
                            <h6 class="fw-bold text-muted text-uppercase mb-3">Detalles de la Oferta</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de Campaña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-tag'></i></span>
                                    <input type="text" class="form-control" name="nombre" placeholder="Ej. Descuento Verano" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Tipo</label>
                                    <select class="form-select" name="tipo_descuento">
                                        <option value="PORCENTAJE">Porcentaje (%)</option>
                                        <option value="MONTO_FIJO">Monto Fijo (S/)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Valor</label>
                                    <input type="number" class="form-control" name="valor" step="0.01" placeholder="Ej. 10" required>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Inicio</label>
                                    <input type="date" class="form-control" name="fecha_inicio" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Fin</label>
                                    <input type="date" class="form-control" name="fecha_fin" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 bg-light rounded p-3 border">
                            <h6 class="fw-bold text-muted text-uppercase mb-3">Reglas</h6>
                            
                            <div class="form-check form-switch mb-3 p-3 bg-white rounded border">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="solo_una_vez_por_cliente" id="reg_solo_una" value="1" checked>
                                <label class="form-check-label fw-bold cursor-pointer" for="reg_solo_una">
                                    Solo 1 vez por cliente
                                    <span class="d-block small text-muted fw-normal mt-1">Evita que usen el descuento múltiples veces.</span>
                                </label>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold">Nota Interna</label>
                                <textarea class="form-control" name="mensaje_whatsapp" rows="3" placeholder="Nota opcional..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Lanzar Campaña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-dark">Editar Campaña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarPromocion">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id_promocion" name="id_promocion">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Tipo</label>
                            <select id="edit_tipo" name="tipo_descuento" class="form-select">
                                <option value="PORCENTAJE">%</option>
                                <option value="MONTO_FIJO">S/</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Valor</label>
                            <input type="number" id="edit_valor" name="valor" step="0.01" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Inicio</label>
                            <input type="date" id="edit_inicio" name="fecha_inicio" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Fin</label>
                            <input type="date" id="edit_fin" name="fecha_fin" class="form-control">
                        </div>
                        <div class="col-12 pt-2">
                             <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_solo_una" name="solo_una_vez_por_cliente" value="1">
                                <label class="form-check-label fw-bold" for="edit_solo_una">Solo 1 vez por cliente</label>
                            </div>
                        </div>
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

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-top border-5 border-danger">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger"><i class='bx bx-trash' style='font-size: 4rem;'></i></div>
                <h4 class="mb-2 fw-bold text-danger">¿Eliminar?</h4>
                <p class="text-muted">Campaña: <strong id="nombre_eliminar" class="text-dark"></strong></p>
                <form id="formEliminarPromocion">
                    <input type="hidden" id="delete_id_promocion" name="id_promocion">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">SÍ, ELIMINAR</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
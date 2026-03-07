<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white fw-bold"><i class='bx bx-layer-plus'></i> NUEVO SERVICIO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="registrarServicio">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Servicio</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Ej. Lavado Premium" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Precio Base (S/)</label>
                        <div class="input-group">
                            <span class="input-group-text">S/</span>
                            <input type="number" class="form-control" name="precio_base" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded border">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase">Reglas de Negocio</h6>
                        
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="acumula_puntos" id="reg_acumula" value="1" checked>
                            <label class="form-check-label fw-semibold" for="reg_acumula">Genera Puntos</label>
                        </div>
                        
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="permite_canje" id="reg_canje" value="1">
                            <label class="form-check-label fw-semibold" for="reg_canje">Permite Canje</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary">DETALLE DEL SERVICIO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer border-top p-2"><button type="button" class="btn btn-sm btn-secondary w-100" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary"><i class="bx bx-edit"></i> Editar Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarServicio">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_servicio" name="id_servicio">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" id="edit_nombre" name="nombre" class="form-control fw-bold text-dark">
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Precio Base (S/)</label>
                        <input type="number" id="edit_precio_base" name="precio_base" step="0.01" class="form-control">
                    </div>

                    <div class="bg-light p-3 rounded border">
                        <h6 class="fw-bold text-dark mb-3 small text-uppercase">Configuración</h6>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="edit_acumula" name="acumula_puntos" value="1">
                            <label class="form-check-label" for="edit_acumula">Acumula Puntos</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="edit_canje" name="permite_canje" value="1">
                            <label class="form-check-label" for="edit_canje">Se puede canjear</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-top border-5 border-danger">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger"><i class='bx bx-trash' style='font-size: 4.5rem;'></i></div>
                <h4 class="mb-2 fw-bold text-danger">¿Eliminar?</h4>
                <p class="text-muted">Servicio: <strong id="nombre_eliminar" class="text-dark"></strong></p>
                <form id="formEliminarServicio">
                    <input type="hidden" id="delete_id_servicio" name="id_servicio">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">SÍ, ELIMINAR</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
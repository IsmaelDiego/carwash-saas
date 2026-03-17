<!-- Modal Registrar -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class='bx bxs-megaphone fs-3'></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">LANZAR NUEVA CAMPAÑA</h5>
                        <small class="text-white-50">Configura los detalles de la oferta promocional</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarPromocion">
                <div class="modal-body p-4 p-md-5">
                    
                    <div class="row g-4">
                        <div class="col-md-7 border-end pe-md-4">
                            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Configuración de la Oferta</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nombre Público</label>
                                <div class="input-group input-group-merge shadow-sm">
                                    <span class="input-group-text bg-white"><i class='bx bx-purchase-tag-alt text-primary'></i></span>
                                    <input type="text" class="form-control" name="nombre" placeholder="Ej. Descuento por Apertura" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Tipo de Beneficio</label>
                                    <select class="form-select shadow-sm" name="tipo_descuento" required>
                                        <option value="PORCENTAJE">Porcentaje (%)</option>
                                        <option value="MONTO_FIJO">Monto Fijo (S/)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Valor</label>
                                    <input type="number" class="form-control shadow-sm" name="valor" step="0.01" placeholder="0.00" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Inicio</label>
                                    <input type="date" class="form-control shadow-sm" name="fecha_inicio" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Fin</label>
                                    <input type="date" class="form-control shadow-sm" name="fecha_fin" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Reglas & Comunicación</h6>
                            
                            <div class="bg-label-primary p-4 rounded-4 border border-primary border-opacity-10 mb-4 shadow-sm">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input ms-0 me-3 shadow-none" type="checkbox" name="solo_una_vez_por_cliente" id="reg_solo_una" value="1" checked style="width: 3em; height: 1.5em; cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer mt-1" for="reg_solo_una">
                                        ¿Solo 1 vez?
                                        <span class="d-block small text-muted fw-normal mt-1" style="font-size: 0.7rem;">Evita el uso frecuente por el mismo cliente.</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nota / Descripción Interna</label>
                                <textarea class="form-control shadow-sm" name="mensaje_whatsapp" rows="4" placeholder="Algún detalle extra sobre esta campaña..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white border-top p-4">
                    <button type="button" class="btn btn-white fw-bold text-muted border px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bx bx-send me-2"></i>LANZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg  rounded-4">
            <div class="modal-header bg-warning text-white p-4 position-relative">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class='bx bx-edit fs-3'></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">MODIFICAR CAMPAÑA</h5>
                        <small class="text-white-50">Actualiza los parámetros de la promoción</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarPromocion">
                <div class="modal-body p-4 p-md-5">
                    <input type="hidden" id="edit_id_promocion" name="id_promocion">
                    
                    <div class="row g-4">
                        <div class="col-md-7 border-end pe-md-4">
                            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Actualizar Parámetros</h6>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nombre de la Campaña</label>
                                <input type="text" id="edit_nombre" name="nombre" class="form-control shadow-sm fw-bold border-warning-subtle" required>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Tipo</label>
                                    <select id="edit_tipo" name="tipo_descuento" class="form-select shadow-sm" required>
                                        <option value="PORCENTAJE">Porcentaje (%)</option>
                                        <option value="MONTO_FIJO">Monto Fijo (S/)</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Valor</label>
                                    <input type="number" id="edit_valor" name="valor" step="0.01" class="form-control shadow-sm fw-bold" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Inicio</label>
                                    <input type="date" id="edit_inicio" name="fecha_inicio" class="form-control shadow-sm" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Fin</label>
                                    <input type="date" id="edit_fin" name="fecha_fin" class="form-control shadow-sm" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <h6 class="fw-bold text-dark text-uppercase mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Configuración Avanzada</h6>
                            
                            <div class="bg-label-warning p-4 rounded-4 border border-warning border-opacity-10 mb-4 shadow-sm">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input ms-0 me-3 shadow-none" type="checkbox" id="edit_solo_una" name="solo_una_vez_por_cliente" value="1" style="width: 3em; height: 1.5em; cursor:pointer;">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer mt-1" for="edit_solo_una">Uso Limitado</label>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted small text-uppercase">Notas de Campaña</label>
                                <textarea class="form-control shadow-sm" id="edit_mensaje" name="mensaje_whatsapp" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top p-4">
                    <button type="button" class="btn btn-white fw-bold text-muted border px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning fw-bold text-white px-4 shadow-sm"><i class="bx bx-save me-2"></i>GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Detalles (Nuevo) -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-info text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-info-circle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">DETALLES DE CAMPAÑA</h5>
                        <small class="text-white-50">Información de la promoción</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle">
                <div class="text-center p-5">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-white border-top p-4">
                <button type="button" class="btn btn-white w-100 fw-bold border text-muted" data-bs-dismiss="modal">ENTENDIDO</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 70px; height: 70px;">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3rem;'></i>
                </div>

                <h4 class="fw-bold text-dark mb-1">¿Retirar Oferta?</h4>
                <p class="text-muted mb-4 small px-2">La oferta promocional dejará de estar disponible:<br>
                    <span id="nombre_eliminar" class="badge bg-white border border-danger-subtle text-danger fs-6 mt-3 py-2 px-3 w-100 text-wrap font-monospace fw-bold shadow-sm" style="white-space: normal;"></span>
                </p>

                <form id="formEliminarPromocion">
                    <input type="hidden" id="delete_id_promocion" name="id_promocion">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm">SÍ, RETIRAR</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
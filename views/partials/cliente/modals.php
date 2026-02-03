<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white fw-bold"><i class='bx bx-user-plus'></i> NUEVO CLIENTE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="registrarcliente">
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label text-primary fw-bold">Consulta DNI (RENIEC)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bx bx-id-card"></i></span>
                                <input type="text" class="form-control form-control-lg" id="dni" name="dni" placeholder="8 dígitos + ENTER" maxlength="8" autofocus>
                                <button class="btn btn-primary" type="button" id="btnBuscarDni"><i class="bx bx-search"></i> BUSCAR</button>
                            </div>
                            <div id="dniFeedback" class="form-text mt-1"></div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Nombres</label><input type="text" class="form-control bg-light" id="nombres" name="nombres" readonly></div>
                        <div class="col-md-6"><label class="form-label">Apellidos</label><input type="text" class="form-control bg-light" id="apellidos" name="apellidos" readonly></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="text" id="telefono" name="telefono" class="form-control" placeholder="999888777"></div>
                        <div class="col-md-6"><label class="form-label">Sexo</label><select class="form-select" id="sexo" name="sexo"><option value="Masculino">Masculino</option><option value="Femenino">Femenino</option></select></div>
                    </div>
                    <div class="row g-3 align-items-center mb-3 p-3 bg-lighter rounded mx-0">
                        <div class="col-md-6">
                            <div class="form-check form-switch"><input type="hidden" name="estado_whatsapp" value="0"><input class="form-check-input" type="checkbox" name="estado_whatsapp" value="1" checked style="transform: scale(1.3);"><label class="form-check-label ms-2 fw-bold text-dark">Activar Notificaciones WhatsApp</label></div>
                        </div>
                    </div>
                    <div class="mb-0"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">GUARDAR CLIENTE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary">FICHA DE CLIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer border-top p-2">
                <button type="button" class="btn btn-sm btn-secondary w-100" data-bs-dismiss="modal">Cerrar Ficha</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="bx bx-edit-alt me-2"></i> Actualizar Información
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarCliente">
                <div class="modal-body py-4">
                    <input type="hidden" id="edit_id_cliente" name="id_cliente">
                    
                    <div class="bg-light  p-3 rounded mb-4 border">
                        <div class="d-flex align-items-center mb-3">
                            <i class='bx bx-lock-alt fs-4 me-2 text-secondary'></i>
                            <span class="fw-bold text-secondary text-uppercase small">Datos de Identificación (No editables)</span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">DNI / RUC</label>
                                <input type="text" id="edit_dni" class="form-control-plaintext fw-bold text-dark px-2 border-bottom" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Nombres</label>
                                <input type="text" id="edit_nombres" name="nombres" class="form-control-plaintext text-dark px-2 border-bottom" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Apellidos</label>
                                <input type="text" id="edit_apellidos" name="apellidos" class="form-control-plaintext text-dark px-2 border-bottom" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 ps-1">Datos de Contacto y Preferencias</h6>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                <input type="text" id="edit_tel1" name="telefono" class="form-control" placeholder="999 000 000">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sexo</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-male-female"></i></span>
                                <select id="edit_sexo" name="sexo" class="form-select">
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="border rounded p-3 h-100 d-flex align-items-center bg-white shadow-sm">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="edit_whatsapp">
                                        <i class='bx bxl-whatsapp text-success fs-5 me-1'></i> Alertas WhatsApp
                                    </label>
                                    <input class="form-check-input ms-0" type="checkbox" id="edit_whatsapp" name="estado_whatsapp" value="1" style="width: 3em; height: 1.5em; cursor: pointer;">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Observaciones" id="edit_observaciones" name="observaciones" style="height: 80px"></textarea>
                                <label for="edit_observaciones">Observaciones o Notas</label>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="edit_puntos" name="puntos"> 
                </div>

                <div class="modal-footer  border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4"><i class='bx bx-save me-1'></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content border-top border-5 border-danger">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger"><i class='bx bx-trash' style='font-size: 4.5rem;'></i></div>
                <h4 class="mb-2 fw-bold text-danger">¿Estás seguro?</h4>
                <p class="text-muted mb-4">Vas a eliminar a:<br><strong id="nombre_eliminar" class="text-dark fs-5"></strong><br>Esta acción es irreversible.</p>
                <form id="formEliminarCliente">
                    <input type="hidden" id="delete_id_cliente" name="id_cliente">
                    <div class="d-grid gap-2">
                        <?php if ($_SESSION['user']['role'] == 1): ?>
                            <button type="submit" class="btn btn-danger btn-lg">SÍ, ELIMINAR</button>
                        <?php else: ?>
                             <button type="button" class="btn btn-secondary" disabled>Acceso Restringido</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: REGISTRAR NUEVO EMPLEADO                       -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-user-plus"></i> NUEVO EMPLEADO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRegistrarEmpleado">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- DNI -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="dni" placeholder="Ej. 75692933" 
                                   maxlength="20" required autocomplete="off">
                        </div>
                        <!-- NOMBRES -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombres Completos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombres" placeholder="Ej. Juan Pérez" required>
                        </div>
                        <!-- ROL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_rol" id="reg_rol" required>
                                <option value="">-- Seleccionar Rol --</option>
                            </select>
                        </div>
                        <!-- EMAIL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="correo@ejemplo.com">
                        </div>
                        <!-- TELÉFONO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" class="form-control" name="telefono" placeholder="Ej. 973563350" maxlength="20">
                        </div>
                        <!-- CONTRASEÑA -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Contraseña <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="reg_password" 
                                       placeholder="Mínimo 6 caracteres" minlength="6" required>
                                <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="reg_password">
                                    <i class="bx bx-hide"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3 mb-0 d-flex align-items-center gap-2" style="border-radius:10px">
                        <i class="bx bx-info-circle fs-4"></i>
                        <small>El empleado usará su <strong>DNI</strong> y <strong>contraseña</strong> para acceder al sistema según su rol asignado.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: VER DETALLE EMPLEADO                            -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary"><i class="bx bx-id-card me-1"></i> PERFIL DEL EMPLEADO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer border-top p-2">
                <button type="button" class="btn btn-sm btn-secondary w-100" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: EDITAR EMPLEADO                                 -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary"><i class="bx bx-edit"></i> Editar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarEmpleado">
                <input type="hidden" id="edit_id_usuario" name="id_usuario">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- DNI (Solo lectura) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">DNI</label>
                            <input type="text" id="edit_dni" class="form-control bg-light" readonly disabled>
                        </div>
                        <!-- NOMBRES -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nombres" name="nombres" class="form-control fw-bold text-dark" required>
                        </div>
                        <!-- ROL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Rol <span class="text-danger">*</span></label>
                            <select id="edit_rol" name="id_rol" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                            </select>
                        </div>
                        <!-- EMAIL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" id="edit_email" name="email" class="form-control">
                        </div>
                        <!-- TELÉFONO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Teléfono</label>
                            <input type="text" id="edit_telefono" name="telefono" class="form-control" maxlength="20">
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

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: CAMBIAR CONTRASEÑA                              -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-top border-5 border-warning">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-warning"><i class="bx bx-lock-alt" style="font-size: 3.5rem;"></i></div>
                <h5 class="fw-bold mb-1">Cambiar Contraseña</h5>
                <p class="text-muted small mb-3"><span id="pass_nombre_empleado" class="fw-bold text-dark"></span></p>
                <form id="formCambiarPassword">
                    <input type="hidden" id="pass_id_usuario" name="id_usuario">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" class="form-control" name="nueva_password" id="nueva_password" 
                                   placeholder="Nueva contraseña" minlength="6" required>
                            <button type="button" class="btn btn-outline-secondary btn-toggle-pass" data-target="nueva_password">
                                <i class="bx bx-hide"></i>
                            </button>
                        </div>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning">CAMBIAR CONTRASEÑA</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: ELIMINAR EMPLEADO                               -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-5 text-center">
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 80px; height: 80px; box-shadow: 0 0 20px rgba(255, 62, 29, 0.2);">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3.5rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-2">¿Confirmar Baja?</h4>
                <p class="text-muted mb-4 small">Estás a punto de retirar al empleado:<br>
                   <span id="nombre_eliminar" class="badge bg-light text-danger fs-6 mt-2 py-2 px-3 border border-danger-subtle w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminarEmpleado">
                    <input type="hidden" id="delete_id_usuario" name="id_usuario">
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold">SÍ, RETIRAR PERSONAL</button>
                        <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
            <div class="bg-danger" style="height: 6px; opacity: 0.8;"></div>
        </div>
    </div>
</div>

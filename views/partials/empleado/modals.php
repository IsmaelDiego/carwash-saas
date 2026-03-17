<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: REGISTRAR NUEVO EMPLEADO                       -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-user-plus fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">NUEVO EMPLEADO</h5>
                        <small class="text-white-50">Ingresa los datos del nuevo integrante</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRegistrarEmpleado">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- DNI -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">DNI <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-id-card"></i></span>
                                <input type="text" class="form-control" name="dni" id="reg_dni" placeholder="Ej. 75692933" maxlength="11" required autocomplete="off" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <button class="btn btn-outline-primary" type="button" id="btnBuscarDniEmpleado">
                                    <i class="bx bx-search fs-5"></i>
                                </button>
                            </div>
                            <small id="dniFeedbackEmpleado" class="text-muted" style="font-size: 0.7rem;">Introduce el DNI para autocompletar nombre.</small>
                        </div>
                        <!-- NOMBRES -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nombres Completos <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-user"></i></span>
                                <input type="text" class="form-control bg-white text-muted" name="nombres" id="reg_nombres" placeholder="Autocompletado por RENIEC" required readonly>
                            </div>
                        </div>
                        <!-- ROL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Rol <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-briefcase"></i></span>
                                <select class="form-select" name="id_rol" id="reg_rol" required>
                                    <option value="">-- Seleccionar Rol --</option>
                                </select>
                            </div>
                        </div>
                        <!-- TELÉFONO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Teléfono / Celular</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-phone"></i></span>
                                <input type="text" class="form-control" name="telefono" placeholder="Ej. 973563350" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <!-- EMAIL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Correo Electrónico</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-envelope"></i></span>
                                <input type="email" class="form-control" name="email" placeholder="correo@ejemplo.com" title="Debe incluir el símbolo @" pattern=".*@.*">
                            </div>
                        </div>
                        <!-- CONTRASEÑA -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Contraseña Segura <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-lock-alt"></i></span>
                                <input type="password" class="form-control" name="password" id="reg_password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                                <button type="button" class="btn btn-outline-white border text-muted border-start-0 btn-toggle-pass" data-target="reg_password" style="background: white;">
                                    <i class="bx bx-hide"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary mt-4 mb-0 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius:12px; background: rgba(105, 108, 255, 0.08);">
                        <i class="bx bx-info-circle fs-2 text-primary"></i>
                        <small class="text-primary fw-medium">El empleado accederá a su cuenta usando su <strong>DNI</strong> como usuario y la <strong>contraseña</strong> que le asignes aquí.</small>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4"><i class="bx bx-save me-2"></i> REGISTRAR EMPLEADO</button>
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
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-id-card fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">PERFIL DEL EMPLEADO</h5>
                        <small class="text-white-50">Información detallada de la cuenta</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer bg-white p-4 border-top">
                <button type="button" class="btn btn-white fw-bold text-muted border w-100" data-bs-dismiss="modal">CERRAR</button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════ -->
<!-- MODAL: EDITAR EMPLEADO                                 -->
<!-- ═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-edit-alt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">EDITAR EMPLEADO</h5>
                        <small class="text-white-50">Actualiza los datos de la cuenta</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarEmpleado">
                <input type="hidden" id="edit_id_usuario" name="id_usuario">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- DNI (Solo lectura) -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">DNI</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-muted"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="edit_dni" class="form-control bg-white text-muted font-monospace fw-bold" readonly disabled>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;"><i class="bx bx-info-circle"></i> El DNI (usuario) no se puede modificar.</small>
                        </div>
                        <!-- NOMBRES -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Nombres Completos</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-muted"><i class="bx bx-user"></i></span>
                                <input type="text" id="edit_nombres" name="nombres" class="form-control fw-bold bg-white text-muted" readonly>
                            </div>
                            <small class="text-muted" style="font-size: 0.7rem;"><i class="bx bx-lock-alt"></i> Autocompletado por RENIEC.</small>
                        </div>
                        <!-- ROL -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Rol <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-briefcase"></i></span>
                                <select id="edit_rol" name="id_rol" class="form-select" required>
                                    <option value="">-- Seleccionar --</option>
                                </select>
                            </div>
                        </div>
                        <!-- TELÉFONO -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Teléfono / Celular</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-phone"></i></span>
                                <input type="text" id="edit_telefono" name="telefono" class="form-control" maxlength="20" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <!-- EMAIL -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Correo Electrónico</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-envelope"></i></span>
                                <input type="email" id="edit_email" name="email" class="form-control" title="Debe incluir el símbolo @" pattern=".*@.*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white p-4 border-top">
                    <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold shadow-sm px-4"><i class="bx bx-save me-2"></i> ACTUALIZAR CAMBIOS</button>
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
        <div class="modal-content border-top  shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-warning" style="width: 70px; height: 70px;">
                    <i class="bx bx-lock-alt text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-1">Cambiar Clave</h5>
                <p class="mb-4 text-warning"><span id="pass_nombre_empleado" class="fw-bold text-warning badge bg-white w-100 text-wrap py-2 mt-1 border"></span></p>
                <form id="formCambiarPassword">
                    <input type="hidden" id="pass_id_usuario" name="id_usuario">
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-muted">Nueva Contraseña <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text bg-white text-warning"><i class="bx bx-key"></i></span>
                            <input type="password" class="form-control border-end-0" name="nueva_password" id="nueva_password" 
                                   placeholder="Mínimo 6 caracteres" minlength="6" required>
                            <button type="button" class="btn btn-outline-white border text-muted border-start-0 btn-toggle-pass" data-target="nueva_password" style="background: white;">
                                <i class="bx bx-hide"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning fw-bold shadow-sm">ACTUALIZAR CLAVE</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
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
        <div class="modal-content border-0 shadow-lg" >
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 70px; height: 70px;">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-1">¿Retirar Personal?</h4>
                <p class="text-muted mb-4 small">Esta acción no se puede deshacer. Retirará al empleado: <br>
                   <span id="nombre_eliminar" class="badge bg-white text-danger fs-6 mt-2 py-2 px-3 border border-danger-subtle w-100 text-wrap font-monospace fw-bold shadow-sm"></span>
                </p>

                <form id="formEliminarEmpleado">
                    <input type="hidden" id="delete_id_usuario" name="id_usuario">
                    <input type="hidden" name="password_admin" id="delete_password_admin">
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">SÍ, RETIRAR</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg" >
            <div class="modal-header bg-primary px-4 py-4 position-relative">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center"><i class='bx bx-user-plus fs-3 me-2'></i> REGISTRAR NUEVO CLIENTE</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarcliente" class="">
                <div class="modal-body p-4 p-md-5">
                    
                    <!-- Búsqueda Inteligente -->
                    <div class="p-4 rounded-3 mb-4 border border-primary shadow-sm" style="background-color: rgba(105, 108, 255, 0.05);">
                        <label class="form-label text-primary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bx bx-search-alt-2"></i> Búsqueda Automática (RENIEC / SUNAT)</label>
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-0 text-primary"><i class="bx bx-id-card"></i></span>
                            <input type="text" class="form-control border-0 bg-white" id="dni" name="dni" placeholder="Ingresa los 8 u 11 dígitos y dale a Buscar..." maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '');" autofocus>
                            <button class="btn btn-primary fw-bold px-4 shadow-sm" type="button" id="btnBuscarDni"><i class="bx bx-search fs-5"></i></button>
                        </div>
                        <div id="dniFeedback" class="form-text mt-2 mb-0 fw-medium">Introduce el documento para autocompletar nombre.</div>
                    </div>

                    <!-- Datos Personales -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Nombres</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white border-0"><i class="bx bx-user text-muted"></i></span>
                                <input type="text" class="form-control bg-white border-0 fw-bold" id="nombres" name="nombres" placeholder="Nombres" readonly style="pointer-events: none;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Apellidos</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text  bg-white border-0"><i class="bx bx-user text-muted"></i></span>
                                <input type="text" class="form-control  bg-white border-0 fw-bold" id="apellidos" name="apellidos" placeholder="Apellidos" readonly style="pointer-events: none;">
                            </div>
                        </div>
                    </div>

                    <!-- Contacto -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Celular / Teléfono</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-phone text-muted"></i></span>
                                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ej. 999 888 777" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Género</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-male-female text-muted"></i></span>
                                <select class="form-select " id="sexo" name="sexo" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="-">Sin especificar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Opciones Adicionales -->
                    <div class="row g-4 mb-3">
                        <div class="col-md-5 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Notificaciones Automáticas</label>
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="check_whatsapp">
                                        <i class='bx bxl-whatsapp text-success fs-4 me-2'></i> Alertas WhatsApp
                                    </label>
                                    <input type="hidden" name="estado_whatsapp" value="0">
                                    <input class="form-check-input ms-0 cursor-pointer switch-whatsapp" type="checkbox" id="check_whatsapp" name="estado_whatsapp" value="1" checked style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Observaciones o Notas</label>
                            <div class="input-group input-group-merge shadow-sm h-100">
                                <span class="input-group-text bg-white border-end-0"></span>
                                <textarea name="observaciones" class="form-control border-start-0 ps-0" rows="2" placeholder="Información adicional relevante..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer  px-4 py-3 border-top">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal"> Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">CREAR CLIENTE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content  shadow-lg">
            <div class="modal-header bg-primary border-bottom px-4 pt-4 pb-3">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center"><i class="bx bx-id-card fs-4 me-2"></i> FICHA DE CLIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Contenido dinámico desde JS -->
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer bg-white border-top px-4 py-3">
                <button type="button" class="btn btn-primary w-100 fw-bold shadow-sm" data-bs-dismiss="modal"><i class="bx bx-check me-1"></i> Entendido</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content  shadow-lg" >

            <div class="modal-header bg-warning px-4 py-4 position-relative">
                <h5 class="modal-title text-dark fw-bold d-flex align-items-center text-white"><i class="bx bx-edit fs-3 me-2"></i> ACTUALIZAR INFORMACIÓN AL CLIENTE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarCliente">
                <div class="modal-body p-4 p-md-5">
                    <input type="hidden" id="edit_id_cliente" name="id_cliente">

                    <!-- Identificación -->
                    <div class="bg-label-warning p-4 rounded-3 border mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class='bx bx-lock-alt fs-5 me-2 text-secondary '></i>
                            <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Datos de Identidad (Sólo lectura)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Documento</label>
                                <input type="text" id="edit_dni" class="form-control bg-transparent border-0 fw-bold text-dark px-0 fs-6" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Nombres</label>
                                <input type="text" id="edit_nombres" name="nombres" class="form-control bg-transparent border-0 fw-bold text-dark px-0 fs-6" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Apellidos</label>
                                <input type="text" id="edit_apellidos" name="apellidos" class="form-control bg-transparent border-0 fw-bold text-dark px-0 fs-6" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 ps-1 mt-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Contacto y Ajustes</h6>

                    <!-- Contacto Editable -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Celular / Teléfono</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-phone"></i></span>
                                <input type="text" id="edit_tel1" name="telefono" class="form-control" placeholder="Ej. 999 000 000" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Género</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-male-female"></i></span>
                                <select id="edit_sexo" name="sexo" class="form-select" required>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                    <option value="-">Sin especificar</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Ajustes Extra -->
                    <div class="row g-4 mb-3">
                        <div class="col-md-5 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Canales de Comunicación</label>
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm" >
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="edit_whatsapp">
                                        <i class='bx bxl-whatsapp text-success fs-4 me-2'></i> Whatsapp
                                    </label>
                                    <input class="form-check-input ms-0 cursor-pointer switch-whatsapp" type="checkbox" id="edit_whatsapp" name="estado_whatsapp" value="1" style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Notas de la cuenta</label>
                            <div class="input-group input-group-merge shadow-sm h-100">
                                <span class="input-group-text bg-white border-end-0"></span>
                                <textarea class="form-control border-start-0 ps-0" placeholder="Observaciones guardadas..." id="edit_observaciones" name="observaciones" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="edit_puntos" name="puntos">
                </div>

                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark shadow-sm text-white">GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-5 text-center">
                <!-- Icono de advertencia premium -->
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 80px; height: 80px; box-shadow: 0 0 20px rgba(255, 62, 29, 0.2);">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3.5rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-2">¿Confirmar Baja?</h4>
                <p class="text-muted mb-4 small">Estás a punto de retirar al cliente del sistema:<br>
                   <span id="nombre_eliminar" class="badge bg-secondary text-white fs-6 mt-2 py-2 px-3 w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminarCliente">
                    <input type="hidden" id="delete_id_cliente" name="id_cliente">
                    <div class="d-grid gap-3">
                        <?php if ($_SESSION['user']['role'] == 1): ?>
                            <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold">SÍ, RETIRAR REGISTRO</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg fw-bold" disabled>ACCESO RESTRINGIDO</button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">MANTENER CLIENTE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
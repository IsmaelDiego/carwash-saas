<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg" >
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class='bx bx-layer-plus fs-3'></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">CREAR NUEVO SERVICIO</h5>
                        <small class="text-white-50">Ingresa los datos para registrar un nuevo servicio</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarServicio">
                <div class="modal-body p-4 p-md-5">

                    <div class="divider text-start mb-4"><div class="divider-text text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Detalles del Servicio</div></div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Nombre Comercial</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class='bx bx-briefcase-alt-2 text-muted'></i></span>
                                <input type="text" class="form-control fw-bold text-dark" name="nombre" placeholder="Ej. Lavado de Salón Premium" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Tarifa Base (S/)</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class='bx bx-money text-success'></i></span>
                                <input type="number" class="form-control text-success fw-bold" name="precio_base" step="0.01" placeholder="Ej. 45.00" required>
                            </div>
                        </div>
                    </div>

                    <div class="divider text-start mb-4"><div class="divider-text text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Reglas del Negocio</div></div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm" style="transition: all 0.2s;" onmouseover="this.classList.add('border-primary')" onmouseout="this.classList.remove('border-primary')">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="reg_acumula">
                                        <i class='bx bxs-star text-warning fs-4 me-2'></i> Genera Puntos
                                    </label>
                                    <input type="hidden" name="acumula_puntos" value="0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="reg_acumula" name="acumula_puntos" value="1" checked style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm" style="transition: all 0.2s;" onmouseover="this.classList.add('border-primary')" onmouseout="this.classList.remove('border-primary')">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="reg_canje">
                                        <i class='bx bx-gift text-info fs-4 me-2'></i> Permite Canje
                                    </label>
                                    <input type="hidden" name="permite_canje" value="0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="reg_canje" name="permite_canje" value="1" style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white p-4 border-top">
                    <button type="button" class="btn btn-white fw-bold text-muted border px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bx bx-save me-2"></i>REGISTRAR SERVICIO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" >
            <div class="modal-header bg-info text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-list-ul fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">DETALLES DEL SERVICIO</h5>
                        <small class="text-white-50">Información y reglas del servicio</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Contenido dinámico desde JS -->
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer bg-white border-top p-4">
                <button type="button" class="btn btn-white w-100 fw-bold border text-muted shadow-sm" data-bs-dismiss="modal">ENTENDIDO</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-edit fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">ACTUALIZAR SERVICIO</h5>
                        <small class="text-white-50">Modifica los datos del servicio</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarServicio">
                <div class="modal-body p-4 p-md-5">
                    <input type="hidden" id="edit_id_servicio" name="id_servicio">

                    <div class="divider text-start mb-4"><div class="divider-text text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Detalles del Servicio</div></div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Nombre Comercial</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class='bx bx-briefcase-alt-2 text-muted'></i></span>
                                <input type="text" id="edit_nombre" name="nombre" class="form-control fw-bold text-dark" required>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Tarifa Base (S/)</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class='bx bx-money text-success'></i></span>
                                <input type="number" id="edit_precio_base" name="precio_base" step="0.01" class="form-control text-success fw-bold" required>
                            </div>
                        </div>
                    </div>

                    <div class="divider text-start mb-4"><div class="divider-text text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Reglas del Negocio</div></div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm" style="transition: all 0.2s;" onmouseover="this.classList.add('border-warning')" onmouseout="this.classList.remove('border-warning')">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="edit_acumula">
                                        <i class='bx bxs-star text-warning fs-4 me-2'></i> Genera Puntos
                                    </label>
                                    <input type="hidden" name="acumula_puntos" value="0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="edit_acumula" name="acumula_puntos" value="1" style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 d-flex align-items-center bg-white shadow-sm" style="transition: all 0.2s;" onmouseover="this.classList.add('border-warning')" onmouseout="this.classList.remove('border-warning')">
                                <div class="form-check form-switch w-100 d-flex justify-content-between align-items-center ps-0 mb-0">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer d-flex align-items-center" for="edit_canje">
                                        <i class='bx bx-gift text-info fs-4 me-2'></i> Permite Canje
                                    </label>
                                    <input type="hidden" name="permite_canje" value="0">
                                    <input class="form-check-input ms-0 cursor-pointer" type="checkbox" id="edit_canje" name="permite_canje" value="1" style="width: 3.5em; height: 1.75em;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer bg-white border-top p-4">
                    <button type="button" class="btn btn-white fw-bold text-muted border px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-white shadow-sm"><i class="bx bx-save me-2"></i>GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 70px; height: 70px;">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-1">¿Quitar Servicio?</h4>
                <p class="text-muted mb-4 small">Esta acción removerá el servicio de la lista de opciones:<br>
                   <span id="nombre_eliminar" class="badge bg-white border border-danger-subtle text-danger fs-6 mt-3 py-2 px-3 w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminarServicio">
                    <input type="hidden" id="delete_id_servicio" name="id_servicio">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm">SÍ, RETIRAR</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
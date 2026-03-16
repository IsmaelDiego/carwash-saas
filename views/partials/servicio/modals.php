<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg" >
            <div class="modal-header bg-primary px-4 py-4 position-relative">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center"><i class='bx bx-layer-plus fs-3 me-2'></i> CREAR NUEVO SERVICIO</h5>
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
                <div class="modal-footer bg-white px-4 py-3 border-top">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm">REGISTRAR SERVICIO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg" >
            <div class="modal-header bg-primary border-bottom px-4 pt-4 pb-3">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center"><i class="bx bx-list-ul fs-4 me-2"></i> DETALLE DEL SERVICIO</h5>
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
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning px-4 py-4 position-relative">
                <h5 class="modal-title text-white fw-bold d-flex align-items-center"><i class="bx bx-edit fs-3 me-2"></i> ACTUALIZAR SERVICIO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

                <div class="modal-footer bg-white border-top px-4 py-3">
                    <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal"> Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-white shadow-sm"> GUARDAR CAMBIOS</button>
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
                
                <h4 class="fw-bold text-dark mb-2">¿Quitar Servicio?</h4>
                <p class="text-muted mb-4 small">Esta acción removerá el servicio de la lista de opciones:<br>
                   <span id="nombre_eliminar" class="badge bg-secondary text-white fs-6 mt-2 py-2 px-3  w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminarServicio">
                    <input type="hidden" id="delete_id_servicio" name="id_servicio">
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold">SÍ, RETIRAR SERVICIO</button>
                        <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">MANTENER SERVICIO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
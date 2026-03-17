<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content  shadow-lg" >
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class='bx bxs-car fs-3'></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">REGISTRAR NUEVO VEHÍCULO</h5>
                        <small class="text-white-50">Ingresa placa y detalles del automotor</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="registrarVehiculo">
                <div class="modal-body p-4 p-md-5">
                    
                    <!-- Selección de Propietario -->
                    <div class="bg-label-primary p-4 rounded-3 mb-4 border border-primary position-relative" style="overflow: visible;">
                        <label class="form-label text-primary fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><i class="bx bxs-user-detail"></i> Propietario del Vehículo</label>
                        
                        <!-- Custom Lite Select Search -->
                        <div class="custom-select-search position-relative">
                            <div class="input-group input-group-lg shadow-sm cursor-pointer border rounded-3 bg-white" id="selectTriggerPropietario">
                                <span class="input-group-text bg-white border-0 text-primary"><i class="bx bx-user"></i></span>
                                <div class="form-control border-0 text-dark fw-bold bg-white d-flex align-items-center justify-content-between py-2">
                                    <span id="txtSeleccionComun" class="text-truncate">Seleccione al dueño...</span>
                                    <i class="bx bx-chevron-down text-muted fs-4"></i>
                                </div>
                                <input type="hidden" name="id_cliente" id="val_id_cliente" required>
                            </div>
                            
                            <!-- Dropdown con Buscador Interno -->
                            <div id="dropdownPropietarios" class="dropdown-menu shadow-lg w-100 p-2 border-0 mt-1" style="display:none; border-radius: 12px; z-index: 1060; border: 1px solid #eee !important;">
                                <div class="p-2 border-bottom mb-2">
                                    <div class="input-group input-group-merge input-group-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bx bx-search text-muted"></i></span>
                                        <input type="text" id="inputSearchInterno" class="form-control bg-white border-0" placeholder="Escribe para buscar..." autocomplete="off">
                                    </div>
                                </div>
                                <div id="listaItemsPropietarios" style="max-height: 250px; overflow-y: auto;">
                                    <!-- Top 10 más recientes se cargan aquí -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .cursor-pointer { cursor: pointer; }
                        #dropdownPropietarios {
                            transition: all 0.2s ease;
                            transform: translateY(10px);
                        }
                        #dropdownPropietarios.show {
                            display: block !important;
                            transform: translateY(0);
                        }
                        
                        .resultado-item {
                            padding: 10px 12px;
                            border-radius: 8px;
                            margin-bottom: 2px;
                            transition: all 0.2s;
                            cursor: pointer;
                        }
                        .resultado-item:hover { background-color: #f0f1ff; }
                        .resultado-item.active { background-color: #696cff; color: #fff !important; }
                        .resultado-item.active .text-muted { color: rgba(255,255,255,0.8) !important; }
                        
                        .resultado-item .nombre { font-size: 0.9rem; font-weight: 600; display: block; }
                        .resultado-item .dni { font-size: 0.75rem; display: block; }
                        
                        #listaItemsPropietarios::-webkit-scrollbar { width: 4px; }
                        #listaItemsPropietarios::-webkit-scrollbar-thumb { background: #dcdfe8; border-radius: 10px; }
                    </style>




                    <div class="divider text-start mb-4"><div class="divider-text text-uppercase fw-bold text-muted" style="font-size: 0.75rem;">Especificaciones Técnicas</div></div>

                    <!-- Datos del auto -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Placa (Sin Guiones)</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class='bx bx-hash text-muted'></i></span>
                                <input type="text" class="form-control text-uppercase fw-bold" name="placa" placeholder="Ej. ABC123" required oninput="this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();" maxlength="7">
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Clase / Categoría</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-shape-polygon text-muted"></i></span>
                                <select class="form-select" name="id_categoria" required>
                                    <option value="">Selecciona el tamaño...</option>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-5 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Color Visual</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-palette text-muted"></i></span>
                                <input type="text" class="form-control text-capitalize" name="color" placeholder="Ej. Rojo Metálico">
                            </div>
                        </div>
                        <div class="col-md-7 mb-2">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Observaciones o Detalles</label>
                            <div class="input-group input-group-merge shadow-sm h-100">
                                <span class="input-group-text bg-white border-end-0"></span>
                                <textarea name="observaciones" class="form-control border-start-0 ps-0" rows="2" placeholder="Arañones, llanta baja, etc..."></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-white border-top p-4">
                    <button type="button" class="btn btn-white fw-bold text-muted border px-4" data-bs-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bx bx-save me-2"></i>REGISTRAR AUTO</button>
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
                        <i class="bx bx-car fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">FICHA TÉCNICA DEL AUTO</h5>
                        <small class="text-white-50">Información de tamaño y dueño</small>
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
                        <h5 class="modal-title text-white fw-bold mb-0">ACTUALIZAR FICHA DEL AUTO</h5>
                        <small class="text-white-50">Edita aspectos visuales y de tamaño</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditarVehiculo">
                <div class="modal-body p-4 p-md-5">
                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">

                    <!-- Identificación Básica -->
                    <div class="bg-label-warning p-4 rounded-3 border mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class='bx bx-lock-alt fs-5 me-2 text-secondary'></i>
                            <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Propiedad Intransferible</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Señor Propietario</label>
                                <input type="text" id="edit_nombre_cliente" class="form-control bg-transparent border-0 fw-bold text-dark px-0 fs-6" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted fw-bold text-uppercase" style="font-size: 0.65rem;">Placa Vehicular</label>
                                <input type="text" id="edit_placa" class="form-control bg-transparent border-0 fw-bold text-dark px-0 fs-6 text-uppercase" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 ps-1 mt-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Peritaje y Detalles</h6>

                    <!-- Edición -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Clase / Categoría</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-shape-polygon"></i></span>
                                <select id="edit_id_categoria" name="id_categoria" class="form-select">
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Color Visual</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-palette"></i></span>
                                <input type="text" id="edit_color" name="color" class="form-control text-capitalize" placeholder="Ej. Plata">
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Anotaciones / Daños Previos</label>
                            <div class="input-group input-group-merge shadow-sm h-100">
                                <span class="input-group-text bg-white border-end-0"></span>
                                <textarea class="form-control border-start-0 ps-0" placeholder="Guardar historial visual..." id="edit_observaciones" name="observaciones" rows="2"></textarea>
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
                
                <h4 class="fw-bold text-dark mb-1">¿Confirmar Baja?</h4>
                <p class="text-muted mb-4 small">Estás a punto de retirar el vehículo del sistema:<br>
                   <span id="placa_eliminar" class="badge bg-white border border-danger-subtle text-danger fs-6 mt-3 py-2 px-3 w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminarVehiculo">
                    <input type="hidden" id="delete_id_vehiculo" name="id_vehiculo">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger fw-bold shadow-sm">SÍ, RETIRAR</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
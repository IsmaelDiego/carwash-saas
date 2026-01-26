       <div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
           <div class="modal-dialog modal-lg" role="document">
               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title fw-bold text-primary" id="modalTitle">
                           <i class="bx bx-car me-1"></i> NUEVO VEHÍCULO
                       </h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>

                   <div class="modal-body">
                       <form id="registrarvehiculo">

                           <div class="mb-4">
                               <label class="form-label text-primary fw-bold">Propietario del Vehículo</label>

                               <input type="hidden" id="id_cliente" name="id_cliente" required>

                               <div id="btnAbrirSelect" class="form-select d-flex justify-content-between align-items-center" style="cursor: pointer; background-color: #fff;">
                                   <span id="textoSelect" class="text-muted"><i class='bx bx-user me-1'></i> Seleccione un cliente...</span>
                                   <i id="btnLimpiarSelect" class='bx bx-x text-danger d-none fs-5' style="z-index: 10;"></i>
                               </div>

                               <div id="menuSelect" class="position-absolute w-100 bg-white border rounded shadow-lg d-none mt-1" style="z-index: 2000;">
                                   <div class="p-2 border-bottom bg-light">
                                       <div class="input-group input-group-sm">
                                           <span class="input-group-text"><i class="bx bx-search"></i></span>
                                           <input type="text" id="inputBuscador" class="form-control" placeholder="Escriba nombre o DNI para buscar...">
                                       </div>
                                   </div>
                                   <ul id="listaClientes" class="list-group list-group-flush m-0" style="max-height: 200px; overflow-y: auto;">
                                   </ul>
                               </div>
                           </div>

                           <hr class="my-4">

                           <div class="row g-4 mb-4">
                               <div class="col-md-6">
                                   <label class="form-label" for="placa">Placa de Rodaje</label>
                                   <div class="input-group input-group-merge">
                                       <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                       <input type="text" id="placa" name="placa" class="form-control font-monospace text-uppercase" placeholder="ABC-123" maxlength="7"  />
                                   </div>
                               </div>

                               <div class="col-md-6">

                                   <label class="form-label" for="tipo">Tipo de Vehículo</label>
                                   <div class="mb-4">

                                       <input type="hidden" id="tipo" name="tipo">

                                       <div class="dropdown">
                                           <button class="form-select d-flex justify-content-between align-items-center bg-white" type="button" id="btnTipoVehiculo" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                                               <span id="textoTipoVehiculo" class="text-muted"><i class='bx bx-category me-1'></i> Seleccione el tipo...</span>
                                           </button>

                                           <ul class="dropdown-menu w-100 shadow-lg border-0 mt-1" aria-labelledby="btnTipoVehiculo" style="max-height: 250px; overflow-y: auto; border-radius: 0.5rem;">

                                               <li>
                                                   <h6 class="dropdown-header text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Uso Particular / Ligero</h6>
                                               </li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Auto"><span>🚗</span> Auto / Sedan</a></li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Camioneta"><span>🚙</span> Camioneta / SUV / 4x4</a></li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Moto"><span>🏍️</span> Moto / Scooter</a></li>

                                               <li>
                                                   <hr class="dropdown-divider">
                                               </li>

                                               <li>
                                                   <h6 class="dropdown-header text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Transporte y Servicios</h6>
                                               </li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Taxi"><span>🚕</span> Taxi / Colectivo</a></li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Van"><span>🚐</span> Van / Minivan</a></li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Bus"><span>🚌</span> Bus / Combi / Custer</a></li>

                                               <li>
                                                   <hr class="dropdown-divider">
                                               </li>

                                               <li>
                                                   <h6 class="dropdown-header text-primary fw-bold text-uppercase" style="font-size: 0.75rem;">Carga Pesada</h6>
                                               </li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Camion"><span>🚚</span> Camión / Furgón</a></li>
                                               <li><a class="dropdown-item d-flex align-items-center py-2 item-tipo" href="javascript:void(0);" data-val="Maquinaria"><span>🚜</span> Maquinaria / Tractor</a></li>
                                           </ul>
                                       </div>
                                   </div>
                               </div>
                           </div>

                           <div class="row g-4 mb-4">
                               <div class="col-md-4">
                                   <label class="form-label" for="marca">Marca</label>
                                   <div class="input-group input-group-merge">
                                       <span class="input-group-text"><i class="bx bx-purchase-tag-alt"></i></span>
                                       <input type="text" id="marca" name="marca" class="form-control text-uppercase" placeholder="Ej: TOYOTA" />
                                   </div>
                               </div>

                               <div class="col-md-4">
                                   <label class="form-label" for="modelo">Modelo</label>
                                   <div class="input-group input-group-merge">
                                       <span class="input-group-text"><i class="bx bx-car"></i></span>
                                       <input type="text" id="modelo" name="modelo" class="form-control text-capitalize" placeholder="Ej: Yaris" />
                                   </div>
                               </div>

                               <div class="col-md-4">
                                   <label class="form-label" for="color">Color</label>
                                   <div class="input-group input-group-merge">
                                       <span class="input-group-text"><i class="bx bx-palette"></i></span>
                                       <input type="text" id="color" name="color" class="form-control text-capitalize" placeholder="Ej: Rojo" />
                                   </div>
                               </div>
                           </div>

                           <div class="mb-4">
                               <label class="form-label" for="observaciones">Observaciones / Daños Previos</label>
                               <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Ej: Tiene un raspón en la puerta derecha...">Sin observaciones</textarea>
                           </div>

                           <div class="modal-footer mt-4 px-0 pb-0">
                               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                               <button type="submit" class="btn btn-primary" id="btnGuardarVehiculo"><i class='bx bx-save me-1'></i> REGISTRAR VEHÍCULO</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>

       <div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary d-flex align-items-center">
                    <i class='bx bx-car fs-4 me-2'></i> Detalle del Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle" >
                
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

       <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary fw-bold d-flex align-items-center m-0">
                    <i class="bx bx-edit me-2" style="font-size: 1.5rem;"></i> Editar Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarVehiculo">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">

                    <div class="mb-4">
                        <label class="form-label text-muted">Propietario del Vehículo</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bx bx-user"></i></span>
                            <input type="text" id="edit_propietario" class="form-control bg-light" readonly>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="text-primary mb-3">Datos del Vehículo</h6>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="edit_placa">Placa de Rodaje</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                <input type="text" id="edit_placa" name="placa" class="form-control font-monospace text-uppercase" maxlength="7" required />
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="edit_tipo">Tipo de Vehículo</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-category"></i></span>
                                <select id="edit_tipo" name="tipo" class="form-select">
                                    <optgroup label="Uso Particular / Ligero">
                                        <option value="Auto">🚗 Auto / Sedan</option>
                                        <option value="Camioneta">🚙 Camioneta / SUV / 4x4</option>
                                        <option value="Moto">🏍️ Moto / Scooter</option>
                                    </optgroup>
                                    <optgroup label="Transporte y Servicios">
                                        <option value="Taxi">🚕 Taxi / Colectivo</option>
                                        <option value="Van">🚐 Van / Minivan</option>
                                        <option value="Bus">🚌 Bus / Combi / Custer</option>
                                    </optgroup>
                                    <optgroup label="Carga Pesada">
                                        <option value="Camion">🚚 Camión / Furgón</option>
                                        <option value="Maquinaria">🚜 Maquinaria / Tractor</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="edit_marca">Marca</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-purchase-tag-alt"></i></span>
                                <input type="text" id="edit_marca" name="marca" class="form-control text-uppercase" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="edit_modelo">Modelo</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-car"></i></span>
                                <input type="text" id="edit_modelo" name="modelo" class="form-control text-capitalize" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label" for="edit_color">Color</label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="bx bx-palette"></i></span>
                                <input type="text" id="edit_color" name="color" class="form-control text-capitalize" />
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="edit_observaciones">Observaciones / Daños Previos</label>
                        <textarea id="edit_observaciones" name="observaciones" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save me-1'></i> GUARDAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

       <div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <i class="bx bx-error-circle text-danger mb-3" style="font-size: 4rem;"></i>
                <h4 class="mb-2">¿Estás seguro?</h4>
                <p class="text-muted mb-4">Se eliminará el vehículo con placa <strong class="text-dark fs-5 text-uppercase" id="placa_eliminar"></strong> (<span id="marca_eliminar"></span>).</p>
                <form id="formEliminarVehiculo">
                    <input type="hidden" id="delete_id_vehiculo" name="id_vehiculo">
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                        <?php if ($_SESSION['user']['role'] == 1): ?>
                            <button type="submit" class="btn btn-danger">Sí, eliminar</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
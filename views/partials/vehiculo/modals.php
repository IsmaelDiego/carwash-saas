<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary fw-bold">NUEVO VEHÍCULO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="registrarvehiculo">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Propietario</label>
                        <input type="hidden" id="id_cliente" name="id_cliente">
                        <div id="btnAbrirSelect" class="form-select d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span id="textoSelect" class="text-muted">Seleccione un cliente...</span>
                            <i id="btnLimpiarSelect" class='bx bx-x text-danger d-none fs-5' style="z-index: 10;"></i>
                        </div>
                        <div id="menuSelect" class="position-absolute w-100 bg-white border rounded shadow-lg d-none mt-1" style="z-index: 2000;">
                            <div class="p-2 border-bottom bg-light">
                                <input type="text" id="inputBuscador" class="form-control" placeholder="Buscar...">
                            </div>
                            <ul id="listaClientes" class="list-group list-group-flush m-0" style="max-height: 200px; overflow-y: auto;"></ul>
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Placa</label>
                            <input type="text" id="placa" name="placa" class="form-control text-uppercase font-monospace" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Vehículo</label>
                            <select class="form-select" id="tipo_vehiculo_id" name="tipo_vehiculo_id" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-4"><label class="form-label">Marca</label><input type="text" name="marca" class="form-control text-uppercase" required></div>
                        <div class="col-md-4"><label class="form-label">Modelo</label><input type="text" name="modelo" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Color</label><input type="text" name="color" class="form-control" required></div>
                    </div>
                    
                    <div class="mb-3">
                        <textarea name="observaciones" class="form-control" placeholder="Observaciones..."></textarea>
                    </div>

                    <div class="modal-footer px-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">GUARDAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">EDITAR VEHÍCULO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarVehiculo">
                <div class="modal-body">
                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">
                    
                    <div class="mb-3">
                        <label class="form-label">Propietario</label>
                        <input type="text" id="edit_propietario" class="form-control bg-light" readonly>
                    </div>

                    <div class="row g-4 mb-3">
                        <div class="col-md-6"><label class="form-label">Placa</label><input type="text" id="edit_placa" name="placa" class="form-control text-uppercase" required></div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" id="edit_tipo_vehiculo_id" name="tipo_vehiculo_id" required></select>
                        </div>
                    </div>
                    <div class="row g-4 mb-3">
                        <div class="col-md-4"><label>Marca</label><input type="text" id="edit_marca" name="marca" class="form-control" required></div>
                        <div class="col-md-4"><label>Modelo</label><input type="text" id="edit_modelo" name="modelo" class="form-control" required></div>
                        <div class="col-md-4"><label>Color</label><input type="text" id="edit_color" name="color" class="form-control" required></div>
                    </div>
                    <div class="mb-3"><textarea id="edit_observaciones" name="observaciones" class="form-control"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">ACTUALIZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="mb-2">¿Eliminar?</h4>
            <p id="placa_eliminar" class="fw-bold"></p>
            <form id="formEliminarVehiculo">
                <input type="hidden" id="delete_id_vehiculo" name="id_vehiculo">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="submit" class="btn btn-danger">Sí, eliminar</button>
            </form>
        </div>
    </div>
</div>
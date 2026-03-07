<div class="modal fade" id="modalRegistrar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white fw-bold"><i class='bx bxs-car'></i> NUEVO VEHÍCULO</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="registrarVehiculo">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label text-primary fw-bold">Propietario</label>
                        <select class="form-select form-select-lg" name="id_cliente" required>
                            <option value="">Seleccione un cliente...</option>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?= $c['id_cliente'] ?>">
                                    <?= $c['nombres'] . ' ' . $c['apellidos'] ?> (<?= $c['dni'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="divider text-start"><div class="divider-text">Datos del Vehículo</div></div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Placa</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-hash'></i></span>
                                <input type="text" class="form-control text-uppercase fw-bold" name="placa" placeholder="ABC-123" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Categoría</label>
                            <select class="form-select" name="id_categoria" required>
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Color</label>
                            <input type="text" class="form-control" name="color" placeholder="Ej. Rojo Metálico">
                        </div>
                    </div>
                    <div class="mb-0"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control" rows="2"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetalle" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary">FICHA DEL VEHÍCULO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="contenidoDetalle"></div>
            <div class="modal-footer border-top p-2"><button type="button" class="btn btn-sm btn-secondary w-100" data-bs-dismiss="modal">Cerrar</button></div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold text-primary"><i class="bx bx-edit-alt me-2"></i> Editar Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarVehiculo">
                <div class="modal-body py-4">
                    <input type="hidden" id="edit_id_vehiculo" name="id_vehiculo">
                    
                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="d-flex align-items-center mb-3">
                            <i class='bx bx-lock-alt fs-4 me-2 text-secondary'></i>
                            <span class="fw-bold text-secondary text-uppercase small">Datos Principales (No editables)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-muted small fw-bold">Propietario</label>
                                <input type="text" id="edit_nombre_cliente" class="form-control-plaintext fw-bold text-dark px-2 border-bottom" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted small fw-bold">Placa</label>
                                <input type="text" id="edit_placa" class="form-control-plaintext fw-bold text-dark px-2 border-bottom text-uppercase" readonly>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3 ps-1">Características</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría</label>
                            <select id="edit_id_categoria" name="id_categoria" class="form-select">
                                <?php foreach($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>"><?= $cat['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Color</label>
                            <input type="text" id="edit_color" name="color" class="form-control">
                        </div>
                    </div>
                    <div class="form-floating">
                        <textarea class="form-control" id="edit_observaciones" name="observaciones" style="height: 80px"></textarea>
                        <label>Observaciones</label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary px-4"><i class='bx bx-save me-1'></i> Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-top border-5 border-danger">
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-danger"><i class='bx bx-trash' style='font-size: 4.5rem;'></i></div>
                <h4 class="mb-2 fw-bold text-danger">¿Eliminar Vehículo?</h4>
                <p class="text-muted mb-4">Placa: <strong id="placa_eliminar" class="text-dark fs-5"></strong></p>
                <form id="formEliminarVehiculo">
                    <input type="hidden" id="delete_id_vehiculo" name="id_vehiculo">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg">SÍ, ELIMINAR</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
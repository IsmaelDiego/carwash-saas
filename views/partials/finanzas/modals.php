<!-- Modal Registrar/Editar Gasto -->
<div class="modal fade" id="modalRegistrarGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="formRegistrarGasto">
                <input type="hidden" name="id_gasto" id="gasto_id">
                <div class="modal-header bg-danger text-white border-0 py-3">
                    <h5 class="modal-title text-white"><i class="bx bx-trending-down me-2"></i> <span id="gastoModalTitle">Registrar Nuevo Gasto</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción del Gasto</label>
                        <input type="text" name="descripcion" id="gasto_descripcion" class="form-control" required placeholder="Ej: Pago de Luz Marzo">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tipo de Gasto</label>
                            <select name="tipo" id="gasto_tipo" class="form-select" required>
                                <option value="VARIABLE">Gasto Variable</option>
                                <option value="FIJO">Gasto Fijo</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Insumo (Opcional)</label>
                            <select name="id_insumo" id="gasto_insumo" class="form-select">
                                <option value="">Ninguno</option>
                                <?php if (isset($lista_insumos)): foreach ($lista_insumos as $in): ?>
                                    <option value="<?= $in['id_insumo'] ?>"><?= htmlspecialchars($in['nombre']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Monto (S/)</label>
                            <input type="number" step="0.01" name="monto" id="gasto_monto" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Fecha</label>
                            <input type="date" name="fecha" id="gasto_fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger px-4">Guardar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar/Editar Insumo -->
<div class="modal fade" id="modalRegistrarInsumo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form id="formRegistrarInsumo">
                <input type="hidden" name="id_insumo" id="insumo_id">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title font-weight-bold"><i class="bx bx-box me-2"></i> Gestión de Insumo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Insumo</label>
                        <input type="text" name="nombre" id="insumo_nombre" class="form-control" required placeholder="Ej: Champú con Cera">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Unidad Medida</label>
                            <input type="text" name="unidad_medida" id="insumo_um" class="form-control" value="Unidad">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Costo Ref. (S/)</label>
                            <input type="number" step="0.01" name="costo_unitario" id="insumo_costo" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Stock Actual</label>
                        <input type="number" name="stock_actual" id="insumo_stock" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning px-4">Guardar Insumo</button>
                </div>
            </form>
        </div>
    </div>
</div>

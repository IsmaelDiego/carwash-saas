<!-- ═══ MODAL: REGISTRAR PRODUCTO ═══ -->
<div class="modal fade" id="modalRegistro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#696cff,#9b9dff);color:#fff;border:0">
                <h5 class="modal-title fw-bold"><i class="bx bx-plus-circle me-1"></i>Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRegistro">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="reg_nombre" required placeholder="Ej: Ambientador Vainilla">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Precio Compra <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_compra" id="reg_precio_compra" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_venta" id="reg_precio_venta" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stock Inicial</label>
                            <input type="number" min="0" class="form-control" name="stock_actual" id="reg_stock" value="0" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stock Mínimo</label>
                            <input type="number" min="0" class="form-control" name="stock_minimo" id="reg_stock_min" value="5" placeholder="5">
                        </div>
                    </div>
                    <div class="alert alert-info d-flex align-items-center py-2 small" role="alert">
                        <i class="bx bx-info-circle me-2 fs-5"></i>
                        <div>El <strong>stock mínimo</strong> genera una alerta cuando el stock baja a ese nivel.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-primary fw-bold" type="submit"><i class="bx bx-save me-1"></i>GUARDAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ MODAL: EDITAR PRODUCTO ═══ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#ffab00,#ffc14d);color:#fff;border:0">
                <h5 class="modal-title fw-bold"><i class="bx bx-edit me-1"></i>Editar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id" name="id_producto">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Precio Compra</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_compra" id="edit_precio_compra" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Precio Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_venta" id="edit_precio_venta" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stock Actual</label>
                            <input type="number" min="0" class="form-control" name="stock_actual" id="edit_stock">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stock Mínimo</label>
                            <input type="number" min="0" class="form-control" name="stock_minimo" id="edit_stock_min">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-warning fw-bold" type="submit"><i class="bx bx-save me-1"></i>ACTUALIZAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ MODAL: AJUSTAR STOCK ═══ -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#71dd37,#56c41a);color:#fff;border:0">
                <h5 class="modal-title fw-bold"><i class="bx bx-transfer me-1"></i>Ajustar Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formStock">
                <div class="modal-body p-4">
                    <input type="hidden" id="stock_id" name="id_producto">
                    <div class="text-center mb-3">
                        <h6 class="fw-bold" id="stock_nombre">—</h6>
                        <p class="mb-0 text-muted">Stock actual: <strong id="stock_actual_display">0</strong></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de Movimiento</label>
                        <select class="form-select" id="stock_tipo" name="tipo">
                            <option value="ENTRADA" selected>📥 Entrada (agregar)</option>
                            <option value="SALIDA">📤 Salida (retirar)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad</label>
                        <input type="number" class="form-control text-center fw-bold" id="stock_cantidad" name="cantidad" min="1" value="1" style="font-size:1.3rem">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-success fw-bold" type="submit"><i class="bx bx-check me-1"></i>AJUSTAR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ MODAL: ELIMINAR ═══ -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-body p-5 text-center">
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 80px; height: 80px; box-shadow: 0 0 20px rgba(255, 62, 29, 0.2);">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3.5rem;'></i>
                </div>
                
                <h4 class="fw-bold text-dark mb-2">¿Eliminar Stock?</h4>
                <p class="text-muted mb-4 small">El producto ya no aparecerá en inventario:<br>
                   <span id="eliminar_nombre" class="badge bg-light text-danger fs-6 mt-2 py-2 px-3 border border-danger-subtle w-100 text-wrap text-uppercase shadow-sm"></span>
                </p>

                <form id="formEliminar">
                    <input type="hidden" id="eliminar_id" name="id_producto">
                    <div class="d-grid gap-3">
                        <button type="submit" class="btn btn-danger btn-lg shadow-sm fw-bold">SÍ, RETIRAR PRODUCTO</button>
                        <button type="button" class="btn btn-label-secondary fw-bold" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
            <div class="bg-danger" style="height: 6px; opacity: 0.8;"></div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="bs-toast toast fade bg-success position-fixed top-0 end-0 m-3" id="toastProducto" role="alert" style="z-index:11000">
    <div class="toast-header">
        <strong class="me-auto">Productos</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
    </div>
    <div class="toast-body" id="toastProductoMsg"></div>
</div>

<!-- ═══ MODAL: REGISTRAR PRODUCTO ═══ -->
<div class="modal fade" id="modalRegistro" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-plus-circle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">NUEVO PRODUCTO</h5>
                        <small class="text-white-50">Ingresa los detalles del nuevo producto en inventario</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRegistro">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Nombre del Producto <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-box"></i></span>
                                <input type="text" class="form-control" name="nombre" id="reg_nombre" required placeholder="Ej: Ambientador Vainilla">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Precio Compra <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_compra" id="reg_precio_compra" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Precio Venta <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_venta" id="reg_precio_venta" required placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Stock Inicial (Lote #1)</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-bar-chart"></i></span>
                                <input type="number" min="0" class="form-control" name="stock_actual" id="reg_stock" value="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Stock Mínimo</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-bell"></i></span>
                                <input type="number" min="0" class="form-control" name="stock_minimo" id="reg_stock_min" value="5" placeholder="5">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Fecha de Caducidad <span class="text-muted">(Opcional)</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-primary"><i class="bx bx-calendar-x"></i></span>
                                <input type="date" class="form-control" name="fecha_caducidad" id="reg_fecha_caducidad">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-primary mt-4 mb-0 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius:12px; background: rgba(105, 108, 255, 0.08);">
                        <i class="bx bx-info-circle fs-2 text-primary"></i>
                        <div>
                            <small class="text-primary fw-medium d-block mb-1">Al registrar con stock inicial, se creará automáticamente el <strong>Lote #1</strong> del producto.</small>
                            <small class="text-primary fw-medium d-block">Para reabastecer, usa el botón <strong>"Agregar Lote"</strong> en el menú de acciones.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-primary fw-bold shadow-sm px-4" type="submit"><i class="bx bx-save me-2"></i> REGISTRAR PRODUCTO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ MODAL: EDITAR PRODUCTO (sin stock directo) ═══ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-warning text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-edit-alt fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">EDITAR PRODUCTO</h5>
                        <small class="text-white-50">Actualiza ficha del producto (stock se gestiona por Lotes)</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditar">
                <div class="modal-body p-4">
                    <input type="hidden" id="edit_id" name="id_producto">
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Nombre del Producto <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white"><i class="bx bx-box text-warning"></i></span>
                                <input type="text" class="form-control" name="nombre" id="edit_nombre" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Precio Compra</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_compra" id="edit_precio_compra" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Precio Venta</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_venta" id="edit_precio_venta" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Stock Mínimo</label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning"><i class="bx bx-bell"></i></span>
                                <input type="number" min="0" class="form-control" name="stock_minimo" id="edit_stock_min">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Fecha de Caducidad <span class="text-muted">(Opcional)</span></label>
                            <div class="input-group input-group-merge shadow-sm">
                                <span class="input-group-text bg-white text-warning"><i class="bx bx-calendar-x"></i></span>
                                <input type="date" class="form-control" name="fecha_caducidad" id="edit_fecha_caducidad">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-4 mb-0 border-0 shadow-sm" style="border-radius:12px;">
                        <small><i class="bx bx-info-circle me-1"></i> El stock ahora se gestiona exclusivamente por <strong>Lotes</strong>. Para agregar unidades, usa "Agregar Lote" desde las acciones.</small>
                    </div>
                </div>
                <div class="modal-footer bg-white p-4 border-top">
                    <button class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-warning fw-bold shadow-sm px-4" type="submit"><i class="bx bx-save me-2"></i>ACTUALIZAR CAMBIOS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ MODAL: AJUSTAR STOCK (Legacy) ═══ -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-top shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-success" style="width: 70px; height: 70px;">
                    <i class="bx bx-transfer text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold mb-1">Ajustar Stock</h5>
                <p class="mb-4 text-success"><span id="stock_nombre" class="fw-bold text-success badge bg-white w-100 text-wrap py-2 mt-1 border"></span></p>
                <form id="formStock">
                    <input type="hidden" id="stock_id" name="id_producto">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold small text-muted">Stock actual en sistema:</label>
                        <div class="fs-4 fw-bold text-center text-dark"><span id="stock_actual_display">0</span> u.</div>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold small text-muted">Tipo de Movimiento</label>
                        <select class="form-select shadow-sm" id="stock_tipo" name="tipo">
                            <option value="ENTRADA">📥 Entrada (Añadir)</option>
                            <option value="SALIDA">📤 Salida (Retirar)</option>
                        </select>
                    </div>
                    <div class="mb-4 text-start">
                        <label class="form-label fw-bold small text-muted">Cantidad a mover</label>
                        <div class="input-group input-group-merge shadow-sm">
                            <span class="input-group-text bg-white text-success"><i class="bx bx-plus-circle"></i></span>
                            <input type="number" class="form-control border-end-0 fw-bold fs-5 text-center" id="stock_cantidad" name="cantidad" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success fw-bold shadow-sm">APLICAR AJUSTE</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: ELIMINAR ═══ -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-label-danger" style="width: 70px; height: 70px;">
                    <i class='bx bx-error-circle text-danger' style='font-size: 3rem;'></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">¿Eliminar Producto?</h4>
                <p class="text-muted mb-4 small">El producto, <strong>todos sus lotes</strong> y su stock se eliminarán:<br>
                   <span id="eliminar_nombre" class="badge bg-white text-danger fs-6 mt-2 py-2 px-3 border border-danger-subtle w-100 text-wrap font-monospace fw-bold shadow-sm"></span>
                </p>
                <form id="formEliminar">
                    <input type="hidden" id="eliminar_id" name="id_producto">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold shadow-sm">SÍ, ELIMINAR</button>
                        <button type="button" class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal">CANCELAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL: AGREGAR LOTE (Entrada de Almacén)
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalAgregarLote" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-archive-in fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">ENTRADA DE ALMACÉN</h5>
                        <small class="text-white-50">Registrar nuevo lote para: <span id="lote_producto_nombre" class="fw-bold"></span></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAgregarLote">
                <input type="hidden" id="lote_id_producto" name="id_producto">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Cantidad de Unidades <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text bg-white text-success"><i class="bx bx-package"></i></span>
                                <input type="number" min="1" class="form-control fw-bold text-center" name="cantidad" id="lote_cantidad" required placeholder="Ej: 50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Precio Compra (S/) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-success">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_compra" id="lote_precio_compra" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Precio Venta (S/) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-success">S/</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_venta" id="lote_precio_venta" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Fecha de Vencimiento <span class="text-muted">(Opcional)</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-success"><i class="bx bx-calendar-check"></i></span>
                                <input type="date" class="form-control" name="fecha_vencimiento" id="lote_fecha_vencimiento">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success mt-4 mb-0 border-0 shadow-sm" style="border-radius:12px;">
                        <small><i class="bx bx-check-shield me-1"></i> Este lote se registrará como entrada <strong>independiente</strong>. El stock total se sincroniza automáticamente desde todos los lotes activos.</small>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-success fw-bold shadow-sm px-4" type="submit"><i class="bx bx-archive-in me-2"></i>REGISTRAR LOTE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL: VER LOTES DE UN PRODUCTO
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalVerLotes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-secondary text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-layer fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">LOTES DEL PRODUCTO</h5>
                        <small class="text-white-50" id="verLotes_nombre">—</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#Lote</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">P. Compra</th>
                                <th class="text-center">P. Venta</th>
                                <th class="text-center">Vencimiento</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyLotes">
                            <tr><td colspan="7" class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ═══ OFFCANVAS: ALERTAS DE VENCIMIENTO ═══ -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAlertas" style="width:420px;">
    <div class="offcanvas-header bg-warning text-white py-3">
        <h6 class="offcanvas-title fw-bold"><i class="bx bx-bell me-2"></i>Alertas de Vencimiento</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0" id="listaAlertasVencimiento">
        <div class="text-center text-muted py-5"><i class="bx bx-loader-alt bx-spin fs-1"></i><br>Cargando alertas...</div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL: REGISTRAR MERMA (Baja de Lote)
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalVerLotes" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-trash fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">REGISTRAR MERMA</h5>
                        <small class="text-white-50">Dar de baja unidades del lote seleccionado</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMerma">
                <input type="hidden" id="merma_id_lote" name="id_lote">
                <div class="modal-body p-4">
                    <div class="alert alert-light border mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Producto:</span>
                            <span class="fw-bold" id="merma_producto_nombre">—</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Lote:</span>
                            <span class="fw-bold" id="merma_lote_id">—</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Disponible:</span>
                            <span class="fw-bold text-primary" id="merma_disponible">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Costo unitario:</span>
                            <span class="fw-bold" id="merma_costo">S/ 0.00</span>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">Cantidad a dar de baja <span class="text-danger">*</span></label>
                            <input type="number" min="1" class="form-control fw-bold text-center" name="cantidad" id="merma_cantidad" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Motivo de la merma <span class="text-danger">*</span></label>
                            <select class="form-select mb-2" id="merma_motivo_sel">
                                <option value="">Seleccionar motivo...</option>
                                <option value="Producto vencido">Producto vencido</option>
                                <option value="Producto dañado">Producto dañado</option>
                                <option value="Pérdida/robo">Pérdida/robo</option>
                                <option value="Error de inventario">Error de inventario</option>
                                <option value="otro">Otro (especificar)</option>
                            </select>
                            <input type="text" class="form-control" name="motivo" id="merma_motivo" placeholder="Detalle del motivo..." required>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 mb-0 border-0 shadow-sm" style="border-radius:12px;">
                        <small><i class="bx bx-error-circle me-1"></i> Se registrará un <strong>gasto automático</strong> por el valor de compra (S/ <span id="merma_gasto_estimado">0.00</span>) de las unidades retiradas.</small>
                    </div>
                </div>
                <div class="modal-footer p-4 border-top bg-white">
                    <button class="btn btn-white fw-bold text-muted border" data-bs-dismiss="modal" type="button">Cancelar</button>
                    <button class="btn btn-danger fw-bold shadow-sm px-4" type="submit"><i class="bx bx-trash me-2"></i>REGISTRAR MERMA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL: KARDEX DE MOVIMIENTOS
═══════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalKardex" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bx bx-history fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-0">KARDEX DE MOVIMIENTOS</h5>
                        <small class="text-white-50" id="kardex_nombre">Historial completo</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th class="text-center">Lote</th>
                                <th class="text-center">Cantidad</th>
                                <th>Referencia</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyKardex">
                            <tr><td colspan="6" class="text-center text-muted py-4">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
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

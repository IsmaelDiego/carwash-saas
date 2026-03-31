<?php
// Modals for Cashier Dashboard
// Include all modal HTML previously in the view
?>
<!-- Modal: Cobrar Orden -->
<div class="modal fade" id="modalCobrar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#71dd37,#56c41a);color:#fff;border:0">
                <h5 class="modal-title fw-bold"><i class="bx bx-check-double me-1"></i>Finalizar Orden #<span id="cobrar_id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="fw-bold text-muted small">TOTAL A PAGAR</div>
                    <div style="font-size:2.5rem;font-weight:800;color:#696cff" id="cobrar_total">S/ 0.00</div>
                </div>
                <div class="mb-3" id="cobrar_detalle" style="max-height:120px;overflow-y:auto"></div>
                <label class="form-label fw-bold small">Método de Pago</label>
                <div class="row g-2 mb-3">
                    <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodo(this)"><i class="bx bx-money text-success"></i><small class="fw-bold">Efectivo</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodo(this)"><i class="bx bx-mobile" style="color:#6f2da8"></i><small class="fw-bold">Yape</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodo(this)"><i class="bx bx-mobile-alt text-info"></i><small class="fw-bold">Plin</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodo(this)"><i class="bx bx-credit-card text-warning"></i><small class="fw-bold">Tarjeta</small></div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button class="btn btn-primary bg-gradient btn-lg w-100 fw-bold rounded-pill border-0 shadow-sm" onclick="confirmarCobro()" id="btnConfirmarCobro"><i class="bx bx-check-double me-1"></i>FINALIZAR ORDEN</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Venta Directa -->
<div class="modal fade" id="modalVenta" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden">
            <div class="modal-header" style="background:linear-gradient(135deg,#696cff,#9b9dff);color:#fff;border:0">
                <h5 class="modal-title fw-bold"><i class="bx bx-cart me-1"></i>Procesar Venta Directa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="fw-bold text-muted small">TOTAL</div>
                    <div style="font-size:2.2rem;font-weight:800;color:#696cff" id="venta_total">S/ 0.00</div>
                </div>
                <label class="form-label fw-bold small">Método de Pago</label>
                <div class="row g-2">
                    <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodoVenta(this)"><i class="bx bx-money text-success"></i><small class="fw-bold">Efectivo</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodoVenta(this)"><i class="bx bx-mobile" style="color:#6f2da8"></i><small class="fw-bold">Yape</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodoVenta(this)"><i class="bx bx-mobile-alt text-info"></i><small class="fw-bold">Plin</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodoVenta(this)"><i class="bx bx-credit-card text-warning"></i><small class="fw-bold">Tarjeta</small></div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button class="btn btn-primary btn-lg w-100 fw-bold rounded-pill" onclick="confirmarVenta()"><i class="bx bx-check me-1"></i>CONFIRMAR VENTA</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Anular Orden -->
<div class="modal fade" id="modalAnular" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;border-top:4px solid #ff3e1d">
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="bx bx-error-circle text-danger" style="font-size:3rem"></i>
                    <h5 class="fw-bold mt-2">Anular Orden #<span id="anular_id"></span></h5>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Código de Token</label>
                    <input type="text" class="form-control text-center fw-bold" id="anular_token" placeholder="CÓDIGO" maxlength="6" style="letter-spacing:3px;text-transform:uppercase;font-size:1.2rem;border:2px solid #ff3e1d;border-radius:12px">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Motivo</label>
                    <textarea class="form-control" id="anular_motivo" rows="2" placeholder="¿Por qué se anula?" style="border-radius:12px"></textarea>
                </div>
                <button class="btn btn-danger w-100 fw-bold rounded-pill mb-2" onclick="confirmarAnulacion()">ANULAR CON TOKEN</button>
                <button class="btn btn-outline-secondary w-100 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Apertura Caja -->
<div class="modal fade" id="modalAperturaCaja" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-lock-open-alt me-2"></i>Apertura de Caja</h5>
            </div>
            <form id="formAperturaCaja">
                <div class="modal-body">
                    <p class="small text-muted mb-3">Ingresa el monto de saldo inicial/sencillo con el que inicias tu turno en físico.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Inicial Efectivo (S/)</label>
                        <input type="number" step="0.10" class="form-control form-control-lg text-center fw-bold text-primary" name="monto_apertura" value="0.00" required>
                    </div>
                    <?php if ($_SESSION['user']['role'] == 3): ?>
                    <div class="mb-3 border-top pt-3">
                        <label class="form-label fw-bold small"><i class="bx bx-info-circle text-warning me-1"></i>Motivo de Apertura (Rol Operador)</label>
                        <select class="form-select border-warning" name="motivo_apertura" required>
                            <option value="Cajero Ausente">Cajero Ausente</option>
                            <option value="Cambio de Turno / Apoyo">Cambio de Turno / Apoyo</option>
                            <option value="Emergencia">Emergencia</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <small class="text-muted" style="font-size:0.65rem">Estás accediendo con un rol de operador. Es necesario reportar el motivo.</small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger w-100 fw-bold">CONFIRMAR APERTURA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Arqueo y Cierre de Caja -->
<div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white fw-bold"><i class="bx bx-bar-chart-alt-2 me-2"></i>Arqueo y Cierre de Caja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCierreCaja">
                <div class="modal-body">
                    <div class="alert alert-secondary p-3 mb-4 rounded-3 text-center">
                        <h6 class="fw-bold mb-1 border-bottom pb-2">RESUMEN DEL SISTEMA</h6>
                        <div class="d-flex justify-content-around mt-3">
                            <div><span class="d-block small text-muted">Apertura</span><strong id="arqueoApertura">S/ 0.00</strong></div>
                            <div><span class="d-block small text-muted">Ventas</span><strong id="arqueoVentas" class="text-success">S/ 0.00</strong></div>
                            <div><span class="d-block small text-muted">Total Esperado</span><strong id="arqueoEsperado" class="text-primary fs-5">S/ 0.00</strong></div>
                        </div>
                    </div>
                    <div id="desglosePagos" class="mb-3 small"></div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Monto Físico Declarado (S/)</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text"><i class="bx bx-money"></i></span>
                            <input type="number" step="0.10" class="form-control fw-bold text-center" id="montoRealCierre" name="monto_declarado" required>
                        </div>
                        <div id="mensajeDiferencia" class="form-text mt-2 fw-bold"></div>
                    </div>
                    <input type="hidden" id="cierreIdSesion" name="id_sesion">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info fw-bold" id="btnProcesarCierre">PROCESAR CIERRE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Nueva Orden de Servicio -->
<div class="modal fade" id="modalNuevaOrden" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title fw-bold text-white"><i class="bx bx-plus-circle me-1"></i>Apertura de Orden de Servicio</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-12">
                        <div class="p-4">
                            <!-- Buscar Cliente -->
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Cliente</label>
                                <select class="form-select select2-clientes" id="sel_cliente_orden" style="width:100%" onchange="cargarVehiculosCliente(this.value)">
                                    <option value="">-- Seleccionar o Buscar Cliente --</option>
                                    <?php foreach ($clientes as $c): ?>
                                    <option value="<?php echo $c['id_cliente']; ?>" data-puntos="<?php echo $c['puntos_acumulados']; ?>" data-canjeo="<?php echo $c['ya_canjeo_temporada_actual']; ?>">
                                        <?php echo htmlspecialchars($c['dni']); ?> — <?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Vehículo</label>
                                    <div class="input-group">
                                        <select class="form-select" id="sel_vehiculo_orden" onchange="checkNuevoVeh(this.value)">
                                            <option value="">-- Seleccionar --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Ubicación</label>
                                    <input type="text" class="form-control" id="ubic_orden" placeholder="Zona/Bahía">
                                </div>
                            </div>
                            <div id="camposNuevoVehiculo" class="bg-light p-3 rounded-3 mb-4 border border-info" style="display:none">
                                <h6 class="fw-bold mb-3 small text-info"><i class="bx bx-car me-1"></i>Datos del Nuevo Vehículo</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="form-label small">Placa</label><input type="text" class="form-control text-uppercase" id="nv_placa" placeholder="ABC-123"></div>
                                    <div class="col-6"><label class="form-label small">Color</label><input type="text" class="form-control" id="nv_color" placeholder="Rojo"></div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small">Categoría</label>
                                    <select class="form-select" id="nv_categoria">
                                        <?php foreach ($categoriasVH as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <label class="form-label fw-bold small mb-2">Servicios Disponibles</label>
                            <div class="row g-2 mb-4" id="servicios_grid">
                                <?php foreach ($servicios as $s): ?>
                                <div class="col-md-6">
                                    <div class="service-selectable p-3 border rounded-3 cursor-pointer" onclick="toggleServicioOrden(this)" data-id="<?php echo $s['id_servicio']; ?>" data-precio="<?php echo $s['precio_base']; ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="small fw-bold"><?php echo htmlspecialchars($s['nombre']); ?></div>
                                            <div class="small text-primary fw-bold">S/ <?php echo number_format($s['precio_base'], 2); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (!empty($promociones)): ?>
                            <div class="mb-4 mt-2">
                                <label class="form-label fw-bold small mb-2"><i class="bx bx-gift text-danger me-1"></i>Promoción (opcional)</label>
                                <div class="row g-2" id="listaPromos">
                                    <div class="col-12">
                                        <div class="promo-option selected" data-id="" onclick="selectPromoOrden(this)">
                                            <div class="fw-bold small">Sin promoción</div>
                                        </div>
                                    </div>
                                    <?php foreach ($promociones as $promo): ?>
                                    <div class="col-12">
                                        <div class="promo-option" data-id="<?php echo $promo['id_promocion']; ?>" data-tipo="<?php echo $promo['tipo_descuento']; ?>" data-valor="<?php echo $promo['valor']; ?>" onclick="selectPromoOrden(this)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="fw-bold small"><?php echo htmlspecialchars($promo['nombre']); ?></div>
                                                </div>
                                                <span class="badge bg-danger">
                                                    <?php echo $promo['tipo_descuento'] === 'PORCENTAJE' ? '-' . $promo['valor'] . '%' : '-S/ ' . number_format($promo['valor'], 2); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="mb-4 mt-2">
                                <label class="form-label fw-bold small mb-2"><i class="bx bx-gift text-muted me-1"></i>Promociones</label>
                                <div class="alert alert-secondary text-center small py-2 mb-0" role="alert">
                                    <i class="bx bx-info-circle me-1"></i>No hay promociones disponibles actualmente
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Resumen del Total -->
                            <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center border">
                                <span class="fw-bold text-muted text-uppercase small">A Pagar (Est.)</span>
                                <h4 class="fw-bold text-primary mb-0" id="lbl_total_nueva_orden">S/ 0.00</h4>
                            </div>

                            <button class="btn btn-primary btn-lg w-100 fw-bold rounded-pill mt-2 shadow-sm" onclick="confirmarCreacionOrden()">
                                <i class="bx bx-rocket me-1"></i>CREAR ORDEN E INICIAR SERVICIO
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php // Modals de Cliente (Admin) ?>
<?php require VIEW_PATH . '/partials/cliente/modals.php'; ?>
?>

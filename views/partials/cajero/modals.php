<?php
// Modals for Cashier Dashboard
// Include all modal HTML previously in the view
?>
<!-- Modal: Cobrar Orden -->
<div class="modal fade" id="modalCobrar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title fw-bold"  style="color: white !important;"><i class="bx bx-check-double me-1"></i>Finalizar Orden #<span id="cobrar_id"></span></h5>
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
                    <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodo(this)">
                        <div class="pm-icon-wrapper" style="background:#e8fadf; color:#71dd37;"><i class="bx bx-money"></i></div>
                        <span class="pm-label">EFECTIVO</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodo(this)">
                        <div class="pm-icon-wrapper" style="background:#f4e8fb; color:#8c52ff;"><i class="bx bx-qr"></i></div>
                        <span class="pm-label">YAPE</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodo(this)">
                        <div class="pm-icon-wrapper" style="background:#e1f9fc; color:#00e4ff;"><i class="bx bx-qr-scan"></i></div>
                        <span class="pm-label">PLIN</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodo(this)">
                        <div class="pm-icon-wrapper" style="background:#fff2d6; color:#ffab00;"><i class="bx bx-credit-card-front"></i></div>
                        <span class="pm-label">TARJETA</span>
                    </div></div>
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
        <div class="modal-content" >
            <div class="modal-header bg-primary p-3">
                <h5 class="modal-title fw-bold"  style="color: white !important;"><i class="bx bx-cart me-1"></i>Procesar Venta Directa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="fw-bold text-muted small">TOTAL</div>
                    <div style="font-size:2.2rem;font-weight:800;color:#696cff" id="venta_total">S/ 0.00</div>
                </div>
                <label class="form-label fw-bold small">Método de Pago</label>
                <div class="row g-2">
                    <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodoVenta(this)">
                        <div class="pm-icon-wrapper" style="background:#e8fadf; color:#71dd37;"><i class="bx bx-money"></i></div>
                        <span class="pm-label">EFECTIVO</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodoVenta(this)">
                        <div class="pm-icon-wrapper" style="background:#f4e8fb; color:#8c52ff;"><i class="bx bx-qr"></i></div>
                        <span class="pm-label">YAPE</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodoVenta(this)">
                        <div class="pm-icon-wrapper" style="background:#e1f9fc; color:#00e4ff;"><i class="bx bx-qr-scan"></i></div>
                        <span class="pm-label">PLIN</span>
                    </div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodoVenta(this)">
                        <div class="pm-icon-wrapper" style="background:#fff2d6; color:#ffab00;"><i class="bx bx-credit-card-front"></i></div>
                        <span class="pm-label">TARJETA</span>
                    </div></div>
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

<!-- Modal: Terminar Lavado -->
<div class="modal fade" id="modalTerminar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;border-top:4px solid #ffab00">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bx bx-check-double text-warning" style="font-size:3.5rem"></i>
                    <h5 class="fw-bold mt-2">Terminar Lavado #<span id="terminar_id"></span></h5>
                    <p class="text-muted small">¿Estás seguro de marcar esta orden como terminada y lista para cobrar?</p>
                </div>
                <button class="btn btn-warning w-100 fw-bold rounded-pill mb-2 text-dark" onclick="confirmarTerminarLavado()">SÍ, TERMINAR LAVADO</button>
                <button class="btn btn-outline-secondary w-100 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Iniciar Lavado -->
<div class="modal fade" id="modalIniciar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;border-top:4px solid #696cff">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bx bx-play-circle text-primary" style="font-size:3.5rem"></i>
                    <h5 class="fw-bold mt-2">Iniciar Lavado #<span id="iniciar_id"></span></h5>
                    <p class="text-muted small">¿Confirmas que el vehículo ya está en la zona de lavado?</p>
                </div>
                <button class="btn btn-primary w-100 fw-bold rounded-pill mb-2 text-white" onclick="confirmarIniciarLavado()">SÍ, INICIAR LAVADO</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                        <select class="form-select" id="sel_vehiculo_orden" onchange="checkNuevoVeh(this.value); calcularTotalNuevaOrden();">
                                            <option value="">-- Seleccionar --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small">Ubicación</label>
                                    <input type="text" class="form-control" id="ubic_orden" value="Atención en local" placeholder="Zona/Bahía">
                                </div>
                            </div>
                            <div id="camposNuevoVehiculo" class="bg-white p-3 rounded-3 mb-4 border border-info" style="display:none">
                                <h6 class="fw-bold mb-3 small text-info"><i class="bx bx-car me-1"></i>Datos del Nuevo Vehículo</h6>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="form-label small">Placa</label><input type="text" class="form-control text-uppercase" id="nv_placa" placeholder="ABC-123"></div>
                                    <div class="col-6"><label class="form-label small">Color</label><input type="text" class="form-control" id="nv_color" placeholder="Rojo"></div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Detalles / Observaciones</label>
                                    <input type="text" class="form-control" id="nv_observaciones" placeholder="Ej: Rayón puerta izq, falta tapón, etc.">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small">Categoría</label>
                                    <select class="form-select" id="nv_categoria" onchange="calcularTotalNuevaOrden()">
                                        <?php foreach ($categoriasVH as $cat): ?>
                                        <option value="<?php echo $cat['id_categoria']; ?>" data-factor="<?php echo $cat['factor_precio']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
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
                                        <div class="promo-option" data-id="<?php echo $promo['id_promocion']; ?>" data-tipo="<?php echo $promo['tipo_descuento']; ?>" data-valor="<?php echo $promo['valor']; ?>" data-once="<?php echo $promo['solo_una_vez_por_cliente']; ?>" onclick="selectPromoOrden(this)">
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
                                  <!-- Cobro Anticipado -->
                            <div class="mb-4 bg-label-success p-3 rounded-3 border border-success">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="chk_pago_anticipado" onchange="togglePagoAnticipado(this)">
                                    <label class="form-check-label fw-bold small text-success" for="chk_pago_anticipado">
                                        <i class="bx bx-dollar-circle me-1"></i>PAGO ANTICIPADO (Opcional)
                                    </label>
                                </div>
                                <div id="panel_metodos_pago_anticipado" style="display:none; transition: all 0.3s">
                                    <label class="form-label fw-bold small">Método de Pago</label>
                                    <div class="row g-2">
                                        <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodoAnticipado(this)"><i class="bx bx-money text-success"></i><small class="fw-bold">Efectivo</small></div></div>
                                        <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodoAnticipado(this)"><i class="bx bx-mobile" style="color:#6f2da8"></i><small class="fw-bold">Yape</small></div></div>
                                        <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodoAnticipado(this)"><i class="bx bx-mobile-alt text-info"></i><small class="fw-bold">Plin</small></div></div>
                                        <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodoAnticipado(this)"><i class="bx bx-credit-card text-warning"></i><small class="fw-bold">Tarjeta</small></div></div>
                                    </div>
                                    <input type="hidden" id="metodo_anticipado" value="EFECTIVO">
                                </div>
                            </div>

                            <!-- Fidelización (Puntos) -->
                            <div id="seccion_fidelizacion" class="mb-4 p-3 rounded-3 border bg-light" style="display:none">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="pts-icon-container me-2">
                                            <i class="bx bxs-star fs-3" id="icon_pts_status"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold small" id="titulo_pts_status">FIDELIZACIÓN</div>
                                            <div class="text-muted text-uppercase" style="font-size:0.7rem; letter-spacing: 0.5px;" id="msg_pts_status"><?= htmlspecialchars($temporadaActiva) ?></div>
                                        </div>
                                    </div>
                                    <div id="pts_badge_container">
                                        <span class="badge" id="badge_pts_count" style="font-size:0.9rem">0 pts</span>
                                    </div>
                                </div>
                                <input type="checkbox" id="chk_canjear_puntos" class="d-none" onchange="calcularTotalNuevaOrden()">
                            </div>                    </div>
                            
                            <!-- Resumen del Total -->
                            <div class="bg-light p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center border">
                                <span class="fw-bold text-muted text-uppercase small">A Pagar (Est.)</span>
                                <h4 class="fw-bold text-primary mb-0" id="lbl_total_nueva_orden">S/ 0.00</h4>
                            </div>

                            <button class="btn btn-primary btn-lg w-100 fw-bold mt-2 shadow-sm" onclick="confirmarCreacionOrden()">
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

<!-- Modal: Gestionar Rampa (Cajero) -->
<div class="modal fade" id="modalGestionRampa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-primary p-3">
                <h5 class="modal-title fw-bold" style="color:white!important"><i class="bx bx-car-wash me-1"></i>Rampa <span id="gr_numero"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="gr_id_rampa">
                
                <!-- Aviso de Cierre Diferido -->
                <div id="avisoCierreDiferido" style="display:none"></div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Operario Asignado</label>
                    <select class="form-select" id="gr_operador">
                        <option value="">— Sin asignar —</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Estado de Rampa</label>
                    <div class="d-flex gap-2">
                        <div class="flex-fill text-center p-2 rounded-3 border rampa-estado-btn cursor-pointer" data-estado="ACTIVA"
                             style="cursor:pointer" onclick="selEstadoRampa(this)">
                            <i class="bx bx-check-circle text-success d-block" style="font-size:1.4rem"></i>
                            <small class="fw-bold">ACTIVA</small>
                        </div>
                        <div class="flex-fill text-center p-2 rounded-3 border rampa-estado-btn cursor-pointer" data-estado="DESCANSO"
                             style="cursor:pointer" onclick="selEstadoRampa(this)">
                            <i class="bx bx-coffee text-warning d-block" style="font-size:1.4rem"></i>
                            <small class="fw-bold">DESCANSO</small>
                        </div>
                        <div class="flex-fill text-center p-2 rounded-3 border rampa-estado-btn cursor-pointer" data-estado="INACTIVA"
                             style="cursor:pointer" onclick="selEstadoRampa(this)">
                            <i class="bx bx-x-circle text-danger d-block" style="font-size:1.4rem"></i>
                            <small class="fw-bold">INACTIVA</small>
                        </div>
                    </div>
                    <input type="hidden" id="gr_estado" value="ACTIVA">
                </div>

                <div class="mb-3" id="gr_motivo_container" style="display:none">
                    <label class="form-label fw-bold small">Motivo</label>
                    <select class="form-select" id="gr_motivo">
                        <option value="Falta de personal">Falta de personal</option>
                        <option value="Horario de almuerzo">Horario de almuerzo ☕</option>
                        <option value="Break / Descanso corto">Break / Descanso corto</option>
                        <option value="Mantenimiento de rampa">Mantenimiento de rampa</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <button class="btn btn-primary w-100 fw-bold rounded-pill" onclick="confirmarGestionRampa()">
                    <i class="bx bx-save me-1"></i>GUARDAR CAMBIOS
                </button>
                <button class="btn btn-outline-secondary w-100 mt-2 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Adelantar Orden en Cola -->
<div class="modal fade" id="modalAdelantar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;border-top:4px solid #ffab00">
            <div class="modal-body p-4 text-center">
                <i class="bx bx-up-arrow-circle text-warning" style="font-size:3.5rem"></i>
                <h5 class="fw-bold mt-2">Adelantar Orden #<span id="adelantar_id"></span></h5>
                <p class="text-muted small mb-4">Esta orden pasará al <strong>inicio de la cola</strong>. Si hay una rampa libre, iniciará proceso inmediatamente.</p>
                <button class="btn btn-warning w-100 fw-bold rounded-pill mb-2 text-dark" onclick="confirmarAdelantar()">
                    <i class="bx bx-rocket me-1"></i>SÍ, ADELANTAR
                </button>
                <button class="btn btn-outline-secondary w-100 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Quitar Prioridad -->
<div class="modal fade" id="modalQuitarPrioridad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border:none;border-radius:16px;overflow:hidden;border-top:4px solid #ff3e1d">
            <div class="modal-body p-4 text-center">
                <i class="bx bx-reset text-danger" style="font-size:3.5rem"></i>
                <h5 class="fw-bold mt-2">Quitar Prioridad #<span id="quitar_prio_id"></span></h5>
                <p class="text-muted small mb-4">¿Estás seguro de quitar la prioridad? La orden volverá a su posición natural según su hora de llegada.</p>
                <button class="btn btn-danger w-100 fw-bold rounded-pill mb-2 text-white" onclick="confirmarQuitarPrioridad()">
                    <i class="bx bx-check me-1"></i>SÍ, QUITAR PRIORIDAD
                </button>
                <button class="btn btn-outline-secondary w-100 rounded-pill" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal: Ver Detalle de Orden -->
<div class="modal fade" id="modalDetalleOrden" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary py-3">
                <h5 class="modal-title fw-bold text-white mb-0"><i class="bx bx-receipt me-2"></i> RESUMEN DE ORDEN #<span id="det_id_orden"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Sección Info Cliente/Vehículo -->
                <div class="p-4 bg-light border-bottom">
                    <div class="row g-3">
                        <div class="col-6 border-end">
                            <label class="text-uppercase text-muted fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 1px;">Cliente</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-label-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bx bx-user fs-5"></i>
                                </div>
                                <div class="fw-bold text-dark lh-1" id="det_cliente" style="font-size: 0.85rem;">—</div>
                            </div>
                        </div>
                        <div class="col-6 px-3">
                            <label class="text-uppercase text-muted fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 1px;">Vehículo</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-label-info rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                    <i class="bx bx-car fs-5"></i>
                                </div>
                                <div class="fw-bold text-dark lh-1" id="det_vehiculo" style="font-size: 0.85rem;">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Ítems -->
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 text-primary small">SERVICIOS Y PRODUCTOS</h6>
                        <span class="badge bg-label-secondary rounded-pill" style="font-size: 10px;">POS V1.0</span>
                    </div>
                    
                    <div id="det_lista_servicios" class="mb-4 overflow-auto" style="max-height: 250px;">
                        <!-- Ítems dinámicos -->
                    </div>

                    <!-- Resumen de Totales -->
                    <div class="bg-label-primary p-3 rounded-3 mt-3 shadow-none border border-primary border-opacity-10">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Subtotal Bruto</span>
                            <span class="fw-bold small" id="det_subtotal">S/ 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span class="small">Descuentos</span>
                            <span class="fw-bold small" id="det_descuento">- S/ 0.00</span>
                        </div>
                        <hr class="my-2 opacity-25">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 fw-bold mb-0">TOTAL CANCELADO</span>
                            <span class="h4 fw-bold text-primary mb-0" id="det_total">S/ 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-4 pt-0 d-flex flex-column gap-2">
                <button id="btnReimprimirDetalle" class="btn btn-primary w-100 py-3 fs-6 fw-bold shadow-sm" style="border-radius: 14px;">
                    <i class="bx bx-printer me-2 fs-5"></i> REIMPRIMIR TICKET
                </button>
                <button class="btn btn-label-secondary w-100 py-2 fw-bold" data-bs-dismiss="modal" style="border-radius: 12px;">
                    CERRAR
                </button>
            </div>
        </div>
    </div>
</div>

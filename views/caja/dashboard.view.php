<?php require VIEW_PATH . '/layouts/header_tunnel.view.php'; ?>

<style>
    /* ─── POS LAYOUT ─── */
    .pos-layout { display: grid; grid-template-columns: 1fr 380px; gap: 0; min-height: calc(100vh - 130px); }
    @media(max-width:1199px) { .pos-layout { grid-template-columns: 1fr; } .pos-sidebar { display: none; } }

    /* ─── STATS BAR ─── */
    .stats-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
    .stat-pill { background: #fff; border-radius: 12px; padding: 12px 18px; display: flex; align-items: center; gap: 10px;
        flex: 1; min-width: 140px; box-shadow: 0 2px 6px rgba(0,0,0,0.04); border: 1px solid #f0f0f0; }
    .stat-pill .sp-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center;
        justify-content: center; font-size: 1.2rem; }
    .stat-pill .sp-val { font-size: 1.3rem; font-weight: 700; line-height: 1; }
    .stat-pill .sp-lbl { font-size: 0.68rem; color: #8592a3; font-weight: 600; text-transform: uppercase; }

    /* ─── ORDER TABS ─── */
    .order-tabs { display: flex; background: #fff; border-radius: 12px; overflow: hidden; margin-bottom: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
    .order-tab { flex: 1; padding: 12px; text-align: center; font-weight: 600; font-size: 0.82rem; color: #8592a3;
        cursor: pointer; transition: all 0.25s; border-bottom: 3px solid transparent; position: relative; }
    .order-tab:hover { background: #f8f8ff; }
    .order-tab.active { color: #696cff; border-bottom-color: #696cff; background: #f8f8ff; }
    .order-tab .badge { position: absolute; top: 6px; right: 20%; }

    /* ─── ORDER CARD ─── */
    .order-card { background: #fff; border-radius: 14px; padding: 16px; margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.25s; border-left: 5px solid #eee; }
    .order-card:hover { box-shadow: 0 6px 16px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .order-card.st-POR_COBRAR { border-left-color: #71dd37; }
    .order-card.st-EN_PROCESO { border-left-color: #ffab00; }
    .order-card.st-FINALIZADO { border-left-color: #8592a3; opacity: 0.65; }
    .order-card.st-ANULADO { border-left-color: #ff3e1d; opacity: 0.5; }
    .oc-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .oc-id { font-weight: 800; font-size: 1rem; color: #566a7f; }
    .oc-total { font-weight: 800; font-size: 1.1rem; }

    /* ─── SIDEBAR (TIENDA) ─── */
    .pos-sidebar { background: #fff; border-left: 1px solid #eee; display: flex; flex-direction: column; height: calc(100vh - 130px); }
    .ps-header { padding: 16px; border-bottom: 1px solid #eee; }
    .ps-body { flex: 1; overflow-y: auto; padding: 16px; }
    .ps-footer { padding: 16px; border-top: 1px solid #eee; background: #fafafa; }
    
    .search-box { position: relative; }
    .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #8592a3; }
    .search-box input { padding-left: 36px; border-radius: 10px; border: 1px solid #d9dee3; }

    .prod-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px;
        border: 1px solid #f0f0f0; margin-bottom: 8px; cursor: pointer; transition: all 0.2s; }
    .prod-item:hover { border-color: #696cff; background: #fafaff; }
    .pi-icon { width: 36px; height: 36px; border-radius: 8px; background: #f0f0ff; display: flex;
        align-items: center; justify-content: center; color: #696cff; font-size: 1rem; flex-shrink: 0; }

    /* ─── CARRITO ─── */
    .cart-item { display: flex; align-items: center; gap: 8px; padding: 6px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.85rem; }
    .cart-item:last-child { border: 0; }
    .cart-total-box { background: linear-gradient(135deg, #696cff, #9b9dff); border-radius: 12px; padding: 14px;
        text-align: center; color: #fff; margin: 12px 0; }
    .cart-total-box .ctb-val { font-size: 1.8rem; font-weight: 800; }

    /* ─── PAYMENT MODAL ─── */
    .pay-method-btn { padding: 14px; border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.2s;
        border: 2px solid #eee; }
    .pay-method-btn:hover { border-color: #c5c6ff; }
    .pay-method-btn.selected { border-color: #696cff; background: #f0f0ff; }
    .pay-method-btn i { font-size: 1.5rem; display: block; margin-bottom: 4px; }

    /* ─── DETALLE OFFCANVAS ─── */
    .detalle-line { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.88rem; }
</style>

<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <?php
        $nombre = htmlspecialchars($_SESSION['user']['name']);
        $hora = date('H');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
        ?>

        <!-- ═══ TOP INFO ═══ -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bx bx-calculator text-primary me-1"></i><?= $saludo ?>, <?= $nombre ?> — <span class="text-primary">Caja</span></h5>
                <small class="text-muted"><?= date('l d/m/Y — H:i') ?></small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-success" onclick="abrirModalNuevoCliente()"><i class="bx bx-user-plus me-1"></i>Registrar Cliente</button>
                <button class="btn btn-sm btn-outline-primary" onclick="cargarOrdenes()"><i class="bx bx-refresh me-1"></i>Actualizar</button>
            </div>
        </div>

        <!-- ═══ STATS BAR ═══ -->
        <div class="stats-bar">
            <div class="stat-pill">
                <div class="sp-icon bg-label-warning"><i class="bx bx-loader-circle"></i></div>
                <div><div class="sp-val text-warning" id="sEnProceso"><?= $stats['EN_PROCESO'] ?? 0 ?></div><div class="sp-lbl">En Proceso</div></div>
            </div>
            <div class="stat-pill">
                <div class="sp-icon bg-label-success"><i class="bx bx-dollar-circle"></i></div>
                <div><div class="sp-val text-success" id="sPorCobrar"><?= $stats['POR_COBRAR'] ?? 0 ?></div><div class="sp-lbl">Por Cobrar</div></div>
            </div>
            <div class="stat-pill">
                <div class="sp-icon bg-label-secondary"><i class="bx bx-check-double"></i></div>
                <div><div class="sp-val" id="sFinalizadas"><?= $stats['FINALIZADO'] ?? 0 ?></div><div class="sp-lbl">Finalizadas</div></div>
            </div>
            <div class="stat-pill">
                <div class="sp-icon bg-label-primary"><i class="bx bx-wallet"></i></div>
                <div><div class="sp-val text-primary" id="sIngresosHoy">S/ <?= number_format($stats['ingresos_hoy'] ?? 0, 0) ?></div><div class="sp-lbl">Ingresos Hoy</div></div>
            </div>
        </div>

        <!-- ═══ POS LAYOUT ═══ -->
        <div class="pos-layout">
            <!-- ─── PANEL IZQUIERDO: ÓRDENES ─── -->
            <div style="padding-right:16px">
                <!-- Tabs -->
                <div class="order-tabs">
                    <div class="order-tab active" data-filter="ACTIVAS" onclick="filtrarOrdenes('ACTIVAS', this)">
                        <i class="bx bx-loader-circle me-1"></i>Órdenes Activas
                        <span class="badge bg-primary rounded-pill" id="badgeActivas"><?= count($ordenesPorCobrar) + count($ordenesEnProceso) ?></span>
                    </div>
                    <div class="order-tab" data-filter="HISTORIAL" onclick="filtrarOrdenes('HISTORIAL', this)">
                        <i class="bx bx-history me-1"></i>Historial Hoy
                    </div>
                </div>

                <!-- Lista de órdenes -->
                <div id="listaOrdenes">
                    <!-- Se llena por JS -->
                </div>
            </div>

            <!-- ─── PANEL DERECHO: TIENDA ─── -->
            <div class="pos-sidebar">
                <div class="ps-header pb-2">
                    <h6 class="fw-bold mb-2"><i class="bx bx-store-alt text-primary me-1"></i>Tienda — Venta Directa</h6>
                    <ul class="nav nav-pills nav-fill mb-2" role="tablist" style="font-size: 0.8rem; background: #f4f5f7; border-radius: 8px; padding: 4px;">
                        <li class="nav-item">
                            <button class="nav-link active py-1 px-2 fw-bold" data-bs-toggle="pill" data-bs-target="#tabDisponibles">Disponibles</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-1 px-2 fw-bold text-danger" data-bs-toggle="pill" data-bs-target="#tabVencidos">Vencidos</button>
                        </li>
                    </ul>
                    <div class="search-box">
                        <i class="bx bx-search"></i>
                        <input type="text" class="form-control form-control-sm" id="buscadorTienda" placeholder="Buscar producto..." onkeyup="filtrarTienda()">
                    </div>
                </div>
                <div class="ps-body tab-content p-0" id="productosLista" style="flex:1; overflow-y:auto; padding: 16px!important">
                    <div class="tab-pane fade show active" id="tabDisponibles">
                        <?php 
                        $hayDisponibles = false;
                        if (!empty($productos)): 
                            foreach ($productos as $prod): 
                                $dias = $prod['dias_vencimiento'];
                                $is_vencido = ($dias !== null && $dias < 0);
                                if ($is_vencido) continue;
                                $hayDisponibles = true;
                                $is_warning = ($dias !== null && $dias >= 0 && $dias <= 30);
                                $bg_style = $is_warning ? 'background-color:#fff3cd;border-color:#ffecb5;' : '';
                                $icon_color = $is_warning ? 'color:#ffab00;background:#fff8e1;' : '';
                        ?>
                            <div class="prod-item" style="<?= $bg_style ?>" onclick="agregarAlCarrito(<?= $prod['id_producto'] ?>, '<?= htmlspecialchars(addslashes($prod['nombre'])) ?>', <?= $prod['precio_venta'] ?>, <?= $prod['stock_actual'] ?>)">
                                <div class="pi-icon" style="<?= $icon_color ?>"><i class="bx bx-package"></i></div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small"><?= htmlspecialchars($prod['nombre']) ?></div>
                                    <div style="font-size:0.7rem;color:#8592a3">
                                        Stock: <?= $prod['stock_actual'] ?>
                                        <?= $is_warning ? "<span class='text-warning ms-1 fw-bold'><i class='bx bx-alarm-exclamation'></i> Por vencer</span>" : "" ?>
                                    </div>
                                </div>
                                <span class="fw-bold text-primary">S/ <?= number_format($prod['precio_venta'], 2) ?></span>
                            </div>
                        <?php endforeach; endif; ?>
                        <?php if(!$hayDisponibles): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bx bx-package" style="font-size:2.5rem"></i>
                                <p class="mt-2 mb-0 small">Sin productos disponibles</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="tab-pane fade" id="tabVencidos">
                        <?php 
                        $hayVencidos = false;
                        if (!empty($productos)): 
                            foreach ($productos as $prod): 
                                $dias = $prod['dias_vencimiento'];
                                $is_vencido = ($dias !== null && $dias < 0);
                                if (!$is_vencido) continue;
                                $hayVencidos = true;
                        ?>
                            <div class="prod-item" style="background-color:#ffebe9;border-color:#f5c2c7;opacity:0.8;cursor:not-allowed">
                                <div class="pi-icon" style="color:#ff3e1d;background:#ffe0db;"><i class="bx bx-dizzy"></i></div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small"><?= htmlspecialchars($prod['nombre']) ?></div>
                                    <div style="font-size:0.7rem;color:#ff3e1d" class="fw-bold">VENCIDO</div>
                                </div>
                                <span class="fw-bold text-muted text-decoration-line-through">S/ <?= number_format($prod['precio_venta'], 2) ?></span>
                            </div>
                        <?php endforeach; endif; ?>
                        <?php if(!$hayVencidos): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bx bx-check-circle" style="font-size:2.5rem;color:#71dd37"></i>
                                <p class="mt-2 mb-0 small">No hay productos vencidos</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Carrito -->
                <div class="ps-footer">
                    <h6 class="fw-bold small mb-2"><i class="bx bx-cart me-1"></i>Carrito <span class="badge bg-primary rounded-pill ms-1" id="carritoCount">0</span></h6>
                    <div id="carritoLista" style="max-height:150px;overflow-y:auto"></div>
                    <div class="cart-total-box">
                        <div class="small" style="opacity:0.8">TOTAL VENTA</div>
                        <div class="ctb-val" id="carritoTotal">S/ 0.00</div>
                    </div>
                    <button class="btn btn-primary w-100 fw-bold rounded-pill" onclick="abrirModalVenta()" id="btnVenta" disabled>
                        <i class="bx bx-credit-card me-1"></i>PROCESAR VENTA
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ═══ MODAL: COBRAR ORDEN ═══ -->
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
                <!-- Detalle orden -->
                <div class="mb-3" id="cobrar_detalle" style="max-height:120px;overflow-y:auto"></div>
                <!-- Método de pago -->
                <label class="form-label fw-bold small">Método de Pago</label>
                <div class="row g-2 mb-3">
                    <div class="col-6"><div class="pay-method-btn selected" data-metodo="EFECTIVO" onclick="selMetodo(this)"><i class="bx bx-money text-success"></i><small class="fw-bold">Efectivo</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="YAPE" onclick="selMetodo(this)"><i class="bx bx-mobile text-purple" style="color:#6f2da8"></i><small class="fw-bold">Yape</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="PLIN" onclick="selMetodo(this)"><i class="bx bx-mobile-alt text-info"></i><small class="fw-bold">Plin</small></div></div>
                    <div class="col-6"><div class="pay-method-btn" data-metodo="TARJETA" onclick="selMetodo(this)"><i class="bx bx-credit-card text-warning"></i><small class="fw-bold">Tarjeta</small></div></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button class="btn btn-primary bg-gradient btn-lg w-100 fw-bold rounded-pill border-0 shadow-sm" onclick="confirmarCobro()" id="btnConfirmarCobro">
                    <i class="bx bx-check-double me-1"></i>FINALIZAR ORDEN
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: VENTA DIRECTA ═══ -->
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
                <button class="btn btn-primary btn-lg w-100 fw-bold rounded-pill" onclick="confirmarVenta()">
                    <i class="bx bx-check me-1"></i>CONFIRMAR VENTA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══ MODAL: ANULAR ═══ -->
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

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border-top:4px solid #71dd37">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="bx bx-user-plus text-success me-1"></i>Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-8">
                        <label class="form-label fw-semibold">DNI <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="nc_dni" maxlength="8" placeholder="8 dígitos">
                            <button class="btn btn-outline-primary" type="button" onclick="buscarReniec()"><i class="bx bx-search"></i></button>
                        </div>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-semibold">Sexo</label>
                        <select class="form-select" id="nc_sexo"><option value="M">M</option><option value="F">F</option></select>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nc_nombres" placeholder="Nombres">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Apellidos</label>
                        <input type="text" class="form-control" id="nc_apellidos" placeholder="Apellidos">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" class="form-control" id="nc_telefono" placeholder="973 563 350">
                </div>
                <button class="btn btn-success w-100 fw-bold rounded-pill" onclick="registrarNuevoCliente()">REGISTRAR CLIENTE</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="bs-toast toast fade bg-success position-fixed top-0 end-0 m-3" id="toastSistema" role="alert" style="z-index:11000">
    <div class="toast-header"><strong class="me-auto">Caja — Carwash XP</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
    <div class="toast-body" id="toastMensaje"></div>
</div>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>

<script>
const BASE_URL = '<?= BASE_URL ?>';
let todasOrdenes = [];
let historialHoy = <?= json_encode($historialHoy) ?>;
let filtroActual = 'ACTIVAS';
let carrito = [];
let ordenCobrar = null;
let metodoCobro = 'EFECTIVO';
let metodoVenta = 'EFECTIVO';
let ordenAnulando = null;

// ═══ INIT ═══
document.addEventListener('DOMContentLoaded', () => { cargarOrdenes(); });

// ═══ NUEVO CLIENTE ═══
function abrirModalNuevoCliente() {
    document.getElementById('nc_dni').value = '';
    document.getElementById('nc_nombres').value = '';
    document.getElementById('nc_apellidos').value = '';
    document.getElementById('nc_telefono').value = '';
    new bootstrap.Modal(document.getElementById('modalNuevoCliente')).show();
}

async function buscarReniec() {
    const dni = document.getElementById('nc_dni').value.trim();
    if (dni.length !== 8) return mostrarToast('DNI debe tener 8 dígitos', 'warning');
    const btn = document.querySelector('#modalNuevoCliente .btn-outline-primary');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/consultarreniec?dni=${dni}`);
        const data = await res.json();
        if (data.success) {
            document.getElementById('nc_nombres').value = data.nombres;
            document.getElementById('nc_apellidos').value = data.apellidos;
            mostrarToast('Datos de RENIEC obtenidos', 'success');
        } else { mostrarToast(data.message || 'No en RENIEC', 'warning'); }
    } catch(e) { mostrarToast('Error RENIEC', 'danger'); }
    btn.innerHTML = '<i class="bx bx-search"></i>';
}

async function registrarNuevoCliente() {
    const data = {
        dni: document.getElementById('nc_dni').value.trim(),
        nombres: document.getElementById('nc_nombres').value.trim(),
        apellidos: document.getElementById('nc_apellidos').value.trim(),
        sexo: document.getElementById('nc_sexo').value,
        telefono: document.getElementById('nc_telefono').value.trim()
    };
    if (!data.dni || !data.nombres) return mostrarToast('DNI y nombres requeridos', 'warning');
    
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/registrarcliente`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
        });
        const r = await res.json();
        if (r.success) {
            mostrarToast(r.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente')).hide();
        } else { mostrarToast(r.message, 'danger'); }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
}

// ═══ CARGAR ÓRDENES ═══
async function cargarOrdenes() {
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/getordenes`);
        const json = await res.json();
        todasOrdenes = json.data || [];
        renderOrdenes();
        actualizarBadges();
    } catch(e) { mostrarToast('Error cargando órdenes', 'danger'); }
}

// ═══ FILTRAR ═══
function filtrarOrdenes(filtro, el) {
    filtroActual = filtro;
    document.querySelectorAll('.order-tab').forEach(t => t.classList.remove('active'));
    if (el) el.classList.add('active');
    renderOrdenes();
}

// ═══ RENDER ═══
function renderOrdenes() {
    const container = document.getElementById('listaOrdenes');
    let data = [];

    if (filtroActual === 'HISTORIAL') {
        data = historialHoy;
    } else if (filtroActual === 'ACTIVAS') {
        data = todasOrdenes.filter(o => o.estado === 'POR_COBRAR' || o.estado === 'EN_PROCESO');
    } else {
        data = todasOrdenes.filter(o => o.estado === filtroActual);
    }

    if (!data.length) {
        container.innerHTML = `<div class="text-center py-5 text-muted">
            <i class="bx bx-receipt" style="font-size:3rem"></i>
            <p class="mt-2 mb-0">Sin órdenes ${filtroActual === 'HISTORIAL' ? 'hoy' : 'en esta categoría'}</p>
        </div>`;
        return;
    }

    container.innerHTML = data.map(o => {
        const est = o.estado || 'FINALIZADO';
        const badgeColors = {EN_COLA:'bg-label-primary',EN_PROCESO:'bg-label-warning',POR_COBRAR:'bg-label-success',FINALIZADO:'bg-label-secondary',ANULADO:'bg-label-danger'};
        let actions = '';
        if (est === 'POR_COBRAR') {
            actions = `<div class="d-flex gap-2 mt-2">
                <button class="btn btn-primary bg-gradient btn-sm flex-grow-1 fw-bold border-0 shadow-sm" onclick="abrirCobro(${o.id_orden}, ${o.total_final})">
                    <i class="bx bx-check-double me-1"></i>Finalizar
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="iniciarAnulacion(${o.id_orden})" title="Anular">
                    <i class="bx bx-x-circle"></i>
                </button>
            </div>`;
        } else if (est === 'EN_PROCESO') {
            actions = `<div class="mt-2"><small class="text-muted">⏳ Esperando que el operario finalice...</small></div>`;
        } else if (est === 'FINALIZADO') {
            let infoExtra = '';
            if (o.ubicacion_en_local === 'Venta Directa') infoExtra += `<span class="badge bg-label-info mb-1" style="font-size:0.65rem">VENTA DIRECTA</span>`;
            if (o.productos_vendidos) infoExtra += `<div class="mt-1 small text-dark"><span class="fw-bold text-muted">📦 Productos:</span> ${o.productos_vendidos}</div>`;
            if (o.servicios_vendidos) infoExtra += `<div class="mt-1 small text-dark"><span class="fw-bold text-muted">🚗 Servicios:</span> ${o.servicios_vendidos}</div>`;
            if (o.puntos_ganados > 0) infoExtra += `<div class="mt-1 small text-warning fw-bold"><i class="bx bx-star"></i> Ganó ${o.puntos_ganados} Puntos</div>`;
            
            const descPromo = parseFloat(o.descuento_promo || 0);
            const descPuntos = parseFloat(o.descuento_puntos || 0);
            
            if (descPromo > 0) infoExtra += `<div class="mt-1 small text-danger"><i class="bx bx-tag-alt"></i> Promo Aplicada (-S/ ${descPromo.toFixed(2)})</div>`;
            if (descPuntos > 0) infoExtra += `<div class="mt-1 small text-danger"><i class="bx bx-gift"></i> Canje de Puntos (-S/ ${descPuntos.toFixed(2)})</div>`;
            
            if (o.metodo_pago) infoExtra += `<div class="mt-1 small text-success fw-bold"><i class="bx bx-wallet-alt"></i> Pagado con ${o.metodo_pago}</div>`;
            
            actions = `<div class="mt-2 pt-2 border-top border-light" style="line-height:1.2; font-size: 0.85rem">${infoExtra}</div>`;
        }
        const fecha = o.fecha_creacion ? new Date(o.fecha_creacion).toLocaleTimeString('es-PE', {hour:'2-digit', minute:'2-digit'}) : '';
        return `<div class="order-card st-${est}">
            <div class="oc-header">
                <span class="oc-id">#${o.id_orden}</span>
                <span class="oc-total text-primary">S/ ${parseFloat(o.total_final || 0).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold small">${o.cliente_nombres || o.cli_nombres || ''} ${o.cliente_apellidos || o.cli_apellidos || ''}</div>
                    <div class="text-muted" style="font-size:0.75rem">${o.placa ? '🚗 '+o.placa+' • ' : ''}${fecha}</div>
                </div>
                <span class="badge ${badgeColors[est]}" style="font-size:0.68rem">${est.replace(/_/g,' ')}</span>
            </div>
            ${actions}
        </div>`;
    }).join('');
}

function actualizarBadges() {
    const cobrar = todasOrdenes.filter(o => o.estado === 'POR_COBRAR').length;
    const proceso = todasOrdenes.filter(o => o.estado === 'EN_PROCESO').length;
    if (document.getElementById('badgeActivas')) {
        document.getElementById('badgeActivas').textContent = cobrar + proceso;
    }
    document.getElementById('sPorCobrar').textContent = cobrar;
    document.getElementById('sEnProceso').textContent = proceso;
}

// ═══ COBRAR ORDEN ═══
async function abrirCobro(id, total) {
    ordenCobrar = id;
    metodoCobro = 'EFECTIVO';
    document.getElementById('cobrar_id').textContent = id;
    document.getElementById('cobrar_total').textContent = 'S/ ' + parseFloat(total).toFixed(2);
    // Reset métodos
    document.querySelectorAll('#modalCobrar .pay-method-btn').forEach(b => b.classList.remove('selected'));
    document.querySelector('#modalCobrar .pay-method-btn[data-metodo="EFECTIVO"]').classList.add('selected');
    // Cargar detalle
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/getdetalle?id=${id}`);
        const data = await res.json();
        const det = data.detalles || [];
        document.getElementById('cobrar_detalle').innerHTML = det.map(d =>
            `<div class="detalle-line">
                <span>${d.servicio_nombre ? '✅ '+d.servicio_nombre : '📦 '+d.producto_nombre+' x'+d.cantidad}</span>
                <span class="fw-bold">S/ ${parseFloat(d.subtotal).toFixed(2)}</span>
            </div>`
        ).join('') || '<p class="text-muted small text-center">Sin detalles</p>';
    } catch(e) { document.getElementById('cobrar_detalle').innerHTML = ''; }
    new bootstrap.Modal(document.getElementById('modalCobrar')).show();
}

function selMetodo(el) {
    document.querySelectorAll('#modalCobrar .pay-method-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    metodoCobro = el.dataset.metodo;
}

async function confirmarCobro() {
    if (!ordenCobrar) return;
    const btn = document.getElementById('btnConfirmarCobro');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando...';
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/finalizarorden`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id_orden: ordenCobrar, metodo_pago: metodoCobro })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCobrar')).hide();
            cargarOrdenes();
        }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
    btn.disabled = false; btn.innerHTML = '<i class="bx bx-check-double me-1"></i>CONFIRMAR COBRO';
}

// ═══ CARRITO ═══
function filtrarTienda() {
    const term = document.getElementById('buscadorTienda').value.toLowerCase();
    const items = document.querySelectorAll('#productosLista .prod-item');
    items.forEach(el => {
        const text = el.querySelector('.fw-bold').textContent.toLowerCase();
        el.style.display = text.includes(term) ? 'flex' : 'none';
    });
}

function agregarAlCarrito(id, nombre, precio, stock) {
    const exist = carrito.find(c => c.id == id);
    if (exist) {
        if (exist.cantidad >= stock) return mostrarToast('Sin stock suficiente', 'warning');
        exist.cantidad++;
    } else {
        carrito.push({ id, nombre, precio, cantidad: 1, stock });
    }
    renderCarrito();
}

function quitarDelCarrito(id) {
    const idx = carrito.findIndex(c => c.id == id);
    if (idx > -1) {
        carrito[idx].cantidad--;
        if (carrito[idx].cantidad <= 0) carrito.splice(idx, 1);
    }
    renderCarrito();
}

function renderCarrito() {
    const lista = document.getElementById('carritoLista');
    const total = carrito.reduce((s, c) => s + c.precio * c.cantidad, 0);
    const count = carrito.reduce((s, c) => s + c.cantidad, 0);

    lista.innerHTML = carrito.map(c => `
        <div class="cart-item py-2">
            <div class="flex-grow-1" style="max-width: 45%;">
                <div class="fw-bold text-truncate" title="${c.nombre}">${c.nombre}</div>
                <small class="text-muted">S/ ${c.precio.toFixed(2)}</small>
            </div>
            <div class="d-flex align-items-center bg-light rounded-pill px-1" style="border: 1px solid #e1e4e8;">
                <button class="btn btn-sm btn-icon text-danger" onclick="quitarDelCarrito(${c.id})" style="width: 24px; height: 24px; padding: 0;"><i class="bx bx-minus"></i></button>
                <span class="fw-bold px-2 text-center" style="min-width: 25px; font-size: 0.85rem">${c.cantidad}</span>
                <button class="btn btn-sm btn-icon text-success" onclick="agregarAlCarrito(${c.id}, '${c.nombre.replace(/'/g, "\\'")}', ${c.precio}, ${c.stock})" style="width: 24px; height: 24px; padding: 0;"><i class="bx bx-plus"></i></button>
            </div>
            <div class="fw-bold text-primary ms-auto" style="min-width: 60px; text-align: right;">S/ ${(c.precio * c.cantidad).toFixed(2)}</div>
        </div>
    `).join('');

    document.getElementById('carritoCount').textContent = count;
    document.getElementById('carritoTotal').textContent = `S/ ${total.toFixed(2)}`;
    document.getElementById('btnVenta').disabled = !carrito.length;
}

// ═══ VENTA DIRECTA ═══
function abrirModalVenta() {
    if (!carrito.length) return;
    metodoVenta = 'EFECTIVO';
    const total = carrito.reduce((s, c) => s + c.precio * c.cantidad, 0);
    document.getElementById('venta_total').textContent = `S/ ${total.toFixed(2)}`;
    document.querySelectorAll('#modalVenta .pay-method-btn').forEach(b => b.classList.remove('selected'));
    document.querySelector('#modalVenta .pay-method-btn[data-metodo="EFECTIVO"]').classList.add('selected');
    new bootstrap.Modal(document.getElementById('modalVenta')).show();
}

function selMetodoVenta(el) {
    document.querySelectorAll('#modalVenta .pay-method-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    metodoVenta = el.dataset.metodo;
}

async function confirmarVenta() {
    if (!carrito.length) return;
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/ventadirecta`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({
                productos: carrito.map(c => ({ id: c.id, cantidad: c.cantidad })),
                metodo_pago: metodoVenta
            })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) {
            carrito = [];
            renderCarrito();
            bootstrap.Modal.getInstance(document.getElementById('modalVenta')).hide();
            setTimeout(() => location.reload(), 800);
        }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
}

// ═══ ANULACIÓN ═══
function iniciarAnulacion(id) {
    ordenAnulando = id;
    document.getElementById('anular_id').textContent = id;
    document.getElementById('anular_token').value = '';
    document.getElementById('anular_motivo').value = '';
    new bootstrap.Modal(document.getElementById('modalAnular')).show();
}

async function confirmarAnulacion() {
    const token = document.getElementById('anular_token').value.trim();
    const motivo = document.getElementById('anular_motivo').value.trim();
    if (!token || !motivo) return mostrarToast('Token y motivo obligatorios', 'warning');
    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/anularregistro`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id_orden: ordenAnulando, codigo_token: token, motivo })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) { bootstrap.Modal.getInstance(document.getElementById('modalAnular')).hide(); cargarOrdenes(); }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
}

// ═══ TOAST ═══
function mostrarToast(msg, tipo) {
    const el = document.getElementById('toastSistema');
    el.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = '11000';
    document.getElementById('toastMensaje').textContent = msg;
    new bootstrap.Toast(el).show();
}
</script>

<?php require VIEW_PATH . '/layouts/header_tunnel.view.php'; ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/caja/dashboard.css" />

<!-- Content wrapper -->
<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <?php
        date_default_timezone_set('America/Lima');
        $nombre = htmlspecialchars($_SESSION['user']['name']);
        $hora = (int)date('H');
        if ($hora >= 5 && $hora < 12) {
            $saludo = "Buenos días";
        } elseif ($hora >= 12 && $hora < 18) {
            $saludo = "Buenas tardes";
        } else {
            $saludo = "Buenas noches";
        }
        ?>

        <!-- ═══ TOP INFO ═══ -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bx bx-calculator text-primary me-1"></i><?= $saludo ?>, <?= $nombre ?> — <span class="text-primary">Caja</span></h5>
                <small class="text-muted">
                    <?php
                    $dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
                    echo $dias[date('w')] . " " . date('d/m/Y — h:i A');
                    ?>
                </small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary shadow-none" onclick="abrirModalNuevaOrden()"><i class="bx bx-plus me-1"></i>NUEVA ORDEN</button>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalRegistrarCliente()"><i class="bx bx-user-plus me-1"></i>Registrar Cliente</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="cargarOrdenes()"><i class="bx bx-refresh me-1"></i>Actualizar Ordenes</button>
            </div>
        </div>

        <!-- ═══ ALERTAS DE CAJA SESIÓN ═══ -->
        <?php if (!$cajaActiva): ?>
            <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
                <div>
                    <i class="bx bx-error-circle me-2 fs-4"></i>
                    <strong>CAJA CERRADA:</strong> Debes aperturar la caja para poder registrar cobros o procesar órdenes.
                </div>
                <button class="btn btn-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAperturaCaja"><i class="bx bx-lock-open-alt me-1"></i> ABRIR CAJA</button>
            </div>
        <?php else: ?>
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 py-2 shadow-sm" role="alert">
                <div>
                    <i class="bx bx-check-shield me-2 fs-4"></i>
                    <span>Caja Abierta (Inicial: S/ <?= number_format($cajaActiva['monto_apertura'], 2) ?>)</span>
                </div>
                <button class="btn btn-info btn-sm fw-bold" onclick="abrirModalArqueo()"><i class="bx bx-lock-alt me-1"></i> CERRAR CAJA</button>
            </div>
        <?php endif; ?>

        <!-- ═══ STATS BAR ═══ -->
        <div class="stats-bar">
            <div class="stat-pill border-0 shadow-sm" style="background: linear-gradient(135deg, #696cff, #8e91ff); color:#fff">
                <div class="sp-icon bg-white text-primary"><i class="bx bx-car"></i></div>
                <div>
                    <div class="sp-val" id="badgeActivas"><?= ($stats['EN_PROCESO'] ?? 0) + ($stats['POR_COBRAR'] ?? 0) ?></div>
                    <div class="sp-lbl text-white" style="opacity:0.8">Servicios Activos</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="sp-icon bg-label-warning"><i class="bx bx-loader-circle"></i></div>
                <div>
                    <div class="sp-val text-warning" id="sEnProceso"><?= $stats['EN_PROCESO'] ?? 0 ?></div>
                    <div class="sp-lbl">Progreso</div>
                </div>
            </div>
            <div class="stat-pill">
                <div class="sp-icon bg-label-success"><i class="bx bx-dollar-circle"></i></div>
                <div>
                    <div class="sp-val text-success" id="sPorCobrar"><?= $stats['POR_COBRAR'] ?? 0 ?></div>
                    <div class="sp-lbl">Por Cobrar</div>
                </div>
            </div>

            <div class="stat-pill">
                <div class="sp-icon bg-label-primary"><i class="bx bx-wallet"></i></div>
                <div>
                    <div class="sp-val text-primary" id="sIngresosHoy">S/ <?= number_format($stats['ingresos_hoy'] ?? 0, 0) ?></div>
                    <div class="sp-lbl">Vendido</div>
                </div>
            </div>
        </div>

        <!-- ═══ POS LAYOUT ═══ -->
        <div class="pos-layout">
            <!-- ─── PANEL IZQUIERDO: ÓRDENES ─── -->
            <div style="padding-right:16px">
                <!-- Filtros y Búsqueda -->
                <div class="row g-3 mb-4">
                    <div class="col-md-7">
                        <div class="input-group input-group-merge shadow-sm p-1 bg-white rounded-pill border">
                            <span class="input-group-text bg-transparent border-0"><i class="bx bx-search fs-4 text-primary"></i></span>
                            <input type="text" id="searchOrders" class="form-control border-0 px-2" placeholder="Buscar por Placa o DNI..." onkeyup="actualizarVista()">
                            <button class="btn btn-primary rounded-pill px-4 fw-bold" onclick="actualizarVista()">BUSCAR</button>
                        </div>
                    </div>
                </div>

                <!-- Tabs de Navegación -->
                <div class="order-tabs mb-4 px-2" style="overflow-x: auto; white-space: nowrap; flex-wrap: nowrap;">
                    <div class="order-tab active" data-filter="TODAS" onclick="setFiltro('TODAS', this)">Todos</div>
                    <div class="order-tab" data-filter="EN_COLA" onclick="setFiltro('EN_COLA', this)">En Cola</div>
                    <div class="order-tab" data-filter="EN_PROCESO" onclick="setFiltro('EN_PROCESO', this)">En Proceso</div>
                    <div class="order-tab" data-filter="POR_COBRAR" onclick="setFiltro('POR_COBRAR', this)">Por Cobrar</div>
                    <div class="order-tab" data-filter="HISTORIAL" onclick="setFiltro('HISTORIAL', this)">Historial Hoy</div>
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
                                <div class="prod-item" style="<?= $bg_style ?>" onclick="agregarAlCarrito(<?= $prod['id_producto'] ?>, '<?= htmlspecialchars(addslashes($prod['nombre'])) ?>', <?= $prod['precio_venta_pos'] ?>, <?= $prod['stock_actual'] ?>)">
                                    <div class="pi-icon" style="<?= $icon_color ?>"><i class="bx bx-package"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold small"><?= htmlspecialchars($prod['nombre']) ?></div>
                                        <div style="font-size:0.7rem;color:#8592a3">
                                            Stock: <?= $prod['stock_actual'] ?>
                                            <?= $is_warning ? "<span class='text-warning ms-1 fw-bold'><i class='bx bx-alarm-exclamation'></i> Por vencer</span>" : "" ?>
                                        </div>
                                    </div>
                                    <span class="fw-bold text-primary">S/ <?= number_format($prod['precio_venta_pos'], 2) ?></span>
                                </div>
                        <?php endforeach;
                        endif; ?>
                        <?php if (!$hayDisponibles): ?>
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
                                    <span class="fw-bold text-muted text-decoration-line-through">S/ <?= number_format($prod['precio_venta_pos'], 2) ?></span>
                                </div>
                        <?php endforeach;
                        endif; ?>
                        <?php if (!$hayVencidos): ?>
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

                    <!-- ANEXAR ORDEN (OMNICANALIDAD) -->
                    <div class="mb-3" id="containerAnexar" style="display:none">
                        <select class="form-select select2-ordenes-activas" id="sel_anexar_orden" style="width:100%" data-placeholder="Venta Directa Libre">
                            <option value="">-- VENTA DIRECTA LIBRE --</option>
                            <?php foreach ($ordenesActivas as $oa): ?>
                                <option value="<?= $oa['id_orden'] ?>">
                                    Orden #<?= $oa['id_orden'] ?> - <?= htmlspecialchars($oa['placa'] ?? 'S/P') ?> - <?= htmlspecialchars($oa['cli_nombres']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="carritoEmpty" class="text-center py-4" style="opacity:0.35">
                        <i class="bx bx-cart-alt" style="font-size:2.5rem"></i>
                        <p class="mb-0 small mt-2 fw-bold italic">SIN PRODUCTOS SELECCIONADOS</p>
                    </div>

                    <div id="carritoLista" style="max-height:180px;overflow-y:auto"></div>
                    <div class="cart-total-box" id="containerTotal" style="display:none">
                        <div class="small" style="opacity:0.8">TOTAL VENTA</div>
                        <div class="ctb-val" id="carritoTotal">S/ 0.00</div>
                    </div>
                    <button class="btn btn-primary btn-checkout w-100 fw-bold shadow-sm" onclick="procesarCarrito()" id="btnVenta" style="display:none">
                        <i class="bx bx-credit-card me-1"></i>PROCESAR VENTA
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include VIEW_PATH . '/partials/cajero/modals.php'; ?>

<!-- Modales de Cliente (Admin) -->
<?php require VIEW_PATH . '/partials/cliente/modals.php'; ?>

<!-- Toast -->
<div class="bs-toast toast fade bg-success position-fixed top-0 end-0 m-3" id="toastSistema" role="alert" style="z-index:11000">
    <div class="toast-header"><strong class="me-auto">Caja — Carwash XP</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
    <div class="toast-body" id="toastMensaje"></div>
</div>

</div>
</div>
</div>
</div>
</div>
</div>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    let historialHoy = <?= json_encode($historialHoy) ?>;
    let _cajaActivaId = <?= $cajaActiva ? $cajaActiva['id_sesion'] : 'null' ?>;
</script>

<!-- Custom Script for Caja Dashboard -->
<script src="<?= BASE_URL ?>/public/js/caja/dashboard.js" defer></script>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>
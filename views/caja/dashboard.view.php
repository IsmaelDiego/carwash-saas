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
                <span class="badge bg-label-primary ms-2"><i class="bx bx-calendar-star me-1"></i><?= htmlspecialchars($temporadaActiva) ?></span>
            </div>
            <div class="d-flex gap-2">

                <button class="btn btn-sm btn-primary shadow-none" onclick="abrirModalNuevaOrden()"><i class="bx bx-plus me-1"></i>NUEVA ORDEN</button>
                <button class="btn btn-sm btn-outline-primary" onclick="abrirModalRegistrarCliente()"><i class="bx bx-user-plus me-1"></i>Registrar Cliente</button>
                <button class="btn btn-sm btn-info shadow-none" data-bs-toggle="modal" data-bs-target="#modalReporte"><i class="bx bx-file me-1"></i>Reporte Diario</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="cargarOrdenes(this)"><i class="bx bx-refresh me-1"></i>Actualizar Ordenes</button>
            </div>
        </div>

        <!-- ═══ ALERTAS DE CAJA SESIÓN ═══ -->
        <?php if (!$cajaActiva): ?>
            <?php if (isset($config['cajero_puede_abrir_caja']) && $config['cajero_puede_abrir_caja'] == 0): ?>
                <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
                    <div>
                        <i class="bx bx-error-circle me-2 fs-4"></i>
                        <strong>CAJA CERRADA:</strong> Solo el administrador puede abrir la caja. Se requiere que solicites apertura.
                    </div>
                    <button class="btn btn-warning btn-sm fw-bold text-dark" onclick="solicitarAperturaCaja()"><i class="bx bx-mail-send me-1"></i> SOLICITAR APERTURA</button>
                </div>
            <?php else: ?>
                <div class="alert alert-danger d-flex align-items-center justify-content-between mb-4 shadow-sm" role="alert">
                    <div>
                        <i class="bx bx-error-circle me-2 fs-4"></i>
                        <strong>CAJA CERRADA:</strong> Debes aperturar la caja para poder registrar cobros o procesar órdenes.
                    </div>
                    <button class="btn btn-danger btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAperturaCaja"><i class="bx bx-lock-open-alt me-1"></i> ABRIR CAJA</button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-info d-flex align-items-center justify-content-between mb-4 py-2 shadow-sm" role="alert">
                <div>
                    <i class="bx bx-check-shield me-2 fs-4"></i>
                    <span>Caja Abierta (Inicial: S/ <?= number_format($cajaActiva['monto_apertura'], 2) ?>)</span>
                </div>
                <button class="btn btn-info btn-sm fw-bold" onclick="abrirModalArqueo()"><i class="bx bx-lock-alt me-1"></i> CERRAR CAJA</button>
            </div>
        <?php endif; ?>

        <?php if (!$cajaActiva && isset($config['cajero_puede_abrir_caja']) && $config['cajero_puede_abrir_caja'] == 0): ?>
            <!-- ═══ BLOQUEO DE CAJA EN VISTA PRINCIPAL ═══ -->
            <div class="card border-0 shadow-lg mt-5" style="border-top: 6px solid #ff3e1d !important; max-width: 600px; margin: auto;">
                <div class="card-body text-center p-5">
                    <i class="bx bx-lock-alt text-danger mb-3" style="font-size: 5rem;"></i>
                    <h4 class="fw-bold mb-2">Caja Cerrada por Admin</h4>
                    <p class="text-muted mb-4">No tienes los permisos para aperturar la caja. El contenido del dashboard ha sido bloqueado preventivamente. El administrador debe aperturarla desde su panel. Desde esta pantalla puedes seguir teniendo acceso a tu menú de perfil arriba a la derecha para cerrar sesión.</p>
                    <button class="btn btn-warning w-100 fw-bold rounded-pill text-dark text-uppercase py-2" id="btnSolicitarCajaVirtual" onclick="solicitarAperturaCaja()">
                        <i class="bx bx-mail-send me-1 fs-5 align-middle"></i> Solicitar al Administrador
                    </button>
                    <div class="mt-3 text-muted small" id="msjCajaVirtualEstado"></div>
                </div>
            </div>
            <script>
                // Recargar periódicamente para comprobar si el admin ya abrió la sesión
                setInterval(async () => {
                    try {
                        const res = await fetch(`${BASE_URL}/caja/dashboard/check_caja_abierta`);
                        const json = await res.json();
                        if (json.abierta) {
                            location.reload();
                        }
                    } catch (e) {}
                }, 5000);
            </script>
        <?php else: ?>
            <!-- ═══ CONTENIDO DEL DASHBOARD ═══ -->

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

            <!-- ═══ PANEL RAMPAS ═══ -->
            <div class="mb-4" id="panelRampas">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold mb-0"><i class="bx bx-car-wash text-primary me-1"></i>Estado de Rampas</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="cargarRampas(this)"><i class="bx bx-refresh me-1"></i>Actualizar Rampas</button>
                </div>
                <div class="row g-2" id="contenedorRampas">
                    <div class="col-12 text-center text-muted py-3"><i class="bx bx-loader-alt bx-spin me-1"></i>Cargando rampas...</div>
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

        <?php endif; ?> <!-- /Fin contenido oculto o bloqueado -->

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

<!-- Modal Reporte Diario -->
<div class="modal fade" id="modalReporte" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom pb-3">
                <h5 class="modal-title fw-bold"><i class="bx bx-line-chart me-2 text-info"></i>Reporte del Día</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="small text-muted mb-3">Exporta el consolidado de ventas en lo que va de tu turno actual.</p>

                <!-- Diseño de Conformidad Formal -->
                <div class="mb-4 text-start">
                    <div class="p-3 rounded border border-info-subtle bg-info-subtle bg-opacity-10 d-flex align-items-start gap-3">
                        <div class="pt-1">
                            <input type="checkbox" id="chkConformidadReporte" onchange="toggleBotonesReporte(this.checked)" class="form-check-input border-secondary shadow-none" style="width: 20px; height: 20px; cursor:pointer;">
                        </div>
                        <label for="chkConformidadReporte" class="cursor-pointer">
                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.85rem;">Declaración de Conformidad</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem; line-height: 1.3;">
                                Certifico que los montos y registros detallados en este reporte corresponden fielmente a las operaciones realizadas durante mi turno actual.
                            </p>
                        </label>
                    </div>
                </div>

                <style>
                    #chkConformidadReporte:checked+label h6 {
                        color: #696cff !important;
                    }

                    .bg-info-subtle {
                        background-color: #f0f7ff !important;
                    }

                    .border-info-subtle {
                        border-color: #d1e9ff !important;
                    }
                </style>

                <div class="d-grid gap-2">
                    <button type="button" id="btnRepoPrint" onclick="imprimirReporteDirecto()" class="btn btn-primary fw-bold text-start disabled">
                        <i class="bx bx-printer fs-5 me-2 align-middle"></i>Imprimir Reporte Directo
                    </button>
                    <button type="button" id="btnRepoPdf" onclick="descargarReporte('pdf')" class="btn btn-outline-danger fw-bold text-start disabled">
                        <i class="bx bx-download fs-5 me-2 align-middle"></i>Descargar PDF (Solo Ver)
                    </button>
                    <button type="button" id="btnRepoExcel" onclick="descargarReporte('excel')" class="btn btn-success fw-bold text-start disabled">
                        <i class="bx bx-spreadsheet fs-5 me-2 align-middle"></i>Exportar Excel (.xls)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Iframe Oculto para Impresión -->
<iframe id="iframeImpresion" style="position: absolute; width: 0; height: 0; border: none; visibility: hidden; pointer-events: none;"></iframe>

<script>
    function toggleBotonesReporte(checked) {
        const btns = ['btnRepoPrint', 'btnRepoPdf', 'btnRepoExcel'];

        btns.forEach(id => {
            const btn = document.getElementById(id);
            if (checked) {
                btn.classList.remove('disabled');
                if (btn.tagName === 'BUTTON') btn.disabled = false;
            } else {
                btn.classList.add('disabled');
                if (btn.tagName === 'BUTTON') btn.disabled = true;
            }
        });
    }

    function imprimirReporteDirecto() {
        if (!_cajaActivaId) {
            mostrarToast("No hay una sesión de caja ABIERTA actual para reportar.", "danger");
            return;
        }
        const url = `<?= BASE_URL ?>/caja/dashboard/generar_reporte?formato=print`;
        const iframe = document.getElementById('iframeImpresion');
        iframe.src = url;
    }

    function descargarReporte(formato) {
        if (!_cajaActivaId) {
            mostrarToast("No hay una sesión de caja ABIERTA actual para reportar.", "danger");
            return;
        }
        const url = `<?= BASE_URL ?>/caja/dashboard/generar_reporte?formato=${formato}`;
        window.open(url, '_blank');
    }
</script>

<script>
    const BASE_URL = '<?= BASE_URL ?>';
    let historialHoy = <?= json_encode($historialHoy) ?>;
    let _cajaActivaId = <?= $cajaActiva ? $cajaActiva['id_sesion'] : 'null' ?>;
</script>

<!-- Custom Script for Caja Dashboard -->
<script src="<?= BASE_URL ?>/public/js/caja/dashboard.js" defer></script>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>
<?php require VIEW_PATH . '/layouts/header_tunnel.view.php'; ?>

<style>
    /* ─── STEPPER VISUAL ─── */
    .tunnel-stepper { display: flex; justify-content: center; gap: 0; padding: 16px 20px; background: #fff; border-bottom: 1px solid #eee; }
    .tunnel-step { display: flex; align-items: center; gap: 6px; }
    .tunnel-step .ts-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700; background: #eee; color: #8592a3; transition: all 0.4s; }
    .tunnel-step.active .ts-circle { background: #696cff; color: #fff; box-shadow: 0 3px 10px rgba(105,108,255,0.35); }
    .tunnel-step.done .ts-circle { background: #71dd37; color: #fff; }
    .tunnel-step .ts-label { font-size: 0.72rem; font-weight: 600; color: #8592a3; }
    .tunnel-step.active .ts-label { color: #696cff; }
    .tunnel-step.done .ts-label { color: #71dd37; }
    .tunnel-connector { width: 40px; height: 2px; background: #eee; align-self: center; margin: 0 4px; }
    .tunnel-connector.done { background: #71dd37; }

    .tunnel-panel { display: none; animation: fadeInPanel 0.4s ease; }
    .tunnel-panel.active { display: block; }
    @keyframes fadeInPanel { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: none; } }

    .servicio-check { border: 2px solid #eee; border-radius: 12px; padding: 12px 14px; cursor: pointer; transition: all 0.25s;
        display: flex; align-items: center; gap: 10px; }
    .servicio-check:hover { border-color: #c5c6ff; background: #fafaff; }
    .servicio-check.selected { border-color: #696cff; background: #f0f0ff; }
    .servicio-check .sc-radio { width: 22px; height: 22px; border: 2px solid #ccc; border-radius: 6px; display: flex;
        align-items: center; justify-content: center; transition: all 0.25s; flex-shrink: 0; }
    .servicio-check.selected .sc-radio { border-color: #696cff; background: #696cff; color: #fff; }

    .prod-card { border: 1px solid #eee; border-radius: 12px; padding: 12px; text-align: center; cursor: pointer;
        transition: all 0.25s; height: 100%; }
    .prod-card:hover { border-color: #696cff; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .prod-card .pc-icon { width: 48px; height: 48px; border-radius: 12px; background: #f0f0ff; display: flex;
        align-items: center; justify-content: center; margin: 0 auto 8px; font-size: 1.3rem; color: #696cff; }

    .resumen-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f4f4f4; font-size: 0.88rem; }
    .resumen-item:last-child { border-bottom: none; }
    .resumen-total { background: linear-gradient(135deg, #696cff, #9b9dff); color: #fff; border-radius: 12px;
        padding: 16px; text-align: center; margin-top: 12px; }
    .resumen-total .rt-val { font-size: 2rem; font-weight: 800; }

    .lock-overlay { background: rgba(0,0,0,0.03); border: 2px dashed #ddd; border-radius: 14px; padding: 24px;
        text-align: center; color: #8592a3; }
    .token-input-group { max-width: 280px; margin: 0 auto; }
    .token-input-group input { text-align: center; font-size: 1.3rem; font-weight: 700; letter-spacing: 4px;
        text-transform: uppercase; border: 2px solid #696cff; border-radius: 12px; }

    /* ─── PUNTOS CARD ─── */
    .points-card { background: linear-gradient(135deg, #ffab00, #ffc14d); border-radius: 14px; padding: 16px; color: #fff; }
    .points-card .pc-val { font-size: 2rem; font-weight: 800; }

    /* ─── PROMO CARD ─── */
    .promo-option { border: 2px solid #eee; border-radius: 12px; padding: 12px; cursor: pointer; transition: all 0.2s; }
    .promo-option:hover { border-color: #ff3e1d; background: #fff5f3; }
    .promo-option.selected { border-color: #ff3e1d; background: #fff5f3; }

    /* ─── NUEVO CLIENTE FORM ─── */
    .nuevo-form { background: #f8f9fa; border-radius: 14px; padding: 16px; border: 2px dashed #ddd; }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">

        <?php
        $pasoActual = 1;
        if ($ordenActiva) {
            if ($ordenActiva['estado'] === 'EN_COLA' || $ordenActiva['estado'] === 'EN_PROCESO') $pasoActual = 2;
            elseif ($ordenActiva['estado'] === 'POR_COBRAR') $pasoActual = 3;
        }
        ?>

        <!-- STEPPER -->
        <div class="tunnel-stepper mb-4">
            <div class="tunnel-step <?= $pasoActual >= 1 ? ($pasoActual > 1 ? 'done' : 'active') : '' ?>">
                <div class="ts-circle"><?= $pasoActual > 1 ? '<i class="bx bx-check"></i>' : '1' ?></div>
                <span class="ts-label d-none d-sm-inline">Nueva Orden</span>
            </div>
            <div class="tunnel-connector <?= $pasoActual > 1 ? 'done' : '' ?>"></div>
            <div class="tunnel-step <?= $pasoActual >= 2 ? ($pasoActual > 2 ? 'done' : 'active') : '' ?>">
                <div class="ts-circle"><?= $pasoActual > 2 ? '<i class="bx bx-check"></i>' : '2' ?></div>
                <span class="ts-label d-none d-sm-inline">En Proceso</span>
            </div>
            <div class="tunnel-connector <?= $pasoActual > 2 ? 'done' : '' ?>"></div>
            <div class="tunnel-step <?= $pasoActual >= 3 ? 'active' : '' ?>">
                <div class="ts-circle">3</div>
                <span class="ts-label d-none d-sm-inline">Finalizar</span>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- PASO 1: CREAR NUEVA ORDEN                            -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div class="tunnel-panel <?= $pasoActual === 1 ? 'active' : '' ?>" id="panel-paso1">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <div class="card shadow-sm" style="border:none;border-radius:14px;border-top:4px solid #696cff">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <div style="width:56px;height:56px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center" class="bg-label-primary mb-2">
                                    <i class="bx bx-plus-circle" style="font-size:1.8rem"></i>
                                </div>
                                <h5 class="fw-bold mb-1">Nueva Orden de Servicio</h5>
                                <p class="text-muted small mb-0">Selecciona cliente, vehículo y servicios</p>
                            </div>

                            <form id="formNuevaOrden">
                                <!-- ───── CLIENTE ───── -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold"><i class="bx bx-user text-primary me-1"></i>Cliente <span class="text-danger">*</span></label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select" id="sel_cliente" name="id_cliente" required onchange="onClienteChange()">
                                            <option value="">-- Seleccionar cliente --</option>
                                            <?php foreach ($clientes as $cli): ?>
                                                <option value="<?= $cli['id_cliente'] ?>" data-puntos="<?= $cli['puntos_acumulados'] ?>" data-canjeo="<?= $cli['ya_canjeo_temporada_actual'] ?>">
                                                    <?= htmlspecialchars($cli['dni'] . ' — ' . $cli['nombres'] . ' ' . $cli['apellidos']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" onclick="toggleNuevoCliente()" title="Nuevo Cliente">
                                            <i class="bx bx-user-plus"></i>
                                        </button>
                                    </div>
                                    <!-- Info puntos del cliente -->
                                    <div id="infoPuntosCliente" class="mt-2" style="display:none">
                                        <small class="d-flex align-items-center gap-2">
                                            <span class="badge bg-label-warning"><i class="bx bx-star me-1"></i><span id="puntosClienteActual">0</span> pts</span>
                                            <span class="text-muted">Meta: <?= $metaPuntos ?> pts para servicio gratis</span>
                                        </small>
                                    </div>
                                </div>

                                <!-- ───── NUEVO CLIENTE (oculto) ───── -->
                                <div id="formNuevoCliente" class="nuevo-form mb-3" style="display:none">
                                    <h6 class="fw-bold mb-3"><i class="bx bx-user-plus text-success me-1"></i>Registrar Nuevo Cliente</h6>
                                    <div class="row g-2 mb-2">
                                        <div class="col-8">
                                            <label class="form-label small fw-semibold">DNI <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="nc_dni" maxlength="8" placeholder="8 dígitos">
                                                <button class="btn btn-outline-info" type="button" onclick="buscarReniec()"><i class="bx bx-search"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label small fw-semibold">Sexo</label>
                                            <select class="form-select" id="nc_sexo"><option value="M">M</option><option value="F">F</option></select>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">Nombres <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="nc_nombres" placeholder="Nombres">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-semibold">Apellidos</label>
                                            <input type="text" class="form-control" id="nc_apellidos" placeholder="Apellidos">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Teléfono</label>
                                        <input type="text" class="form-control" id="nc_telefono" placeholder="973 563 350">
                                    </div>
                                    <button type="button" class="btn btn-success btn-sm w-100 fw-bold" onclick="registrarNuevoCliente()">
                                        <i class="bx bx-check me-1"></i>Registrar Cliente
                                    </button>
                                </div>

                                <!-- ───── VEHÍCULO (dinámico) ───── -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold"><i class="bx bx-car text-info me-1"></i>Vehículo</label>
                                    <div class="d-flex gap-2">
                                        <select class="form-select" id="sel_vehiculo" name="id_vehiculo" onchange="calcularTotal()">
                                            <option value="">-- Sin vehículo / Selecciona cliente --</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-info" onclick="toggleNuevoVehiculo()" title="Nuevo Vehículo" id="btnNuevoVeh" style="display:none">
                                            <i class="bx bx-car"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- ───── NUEVO VEHÍCULO (oculto) ───── -->
                                <div id="formNuevoVehiculo" class="nuevo-form mb-3" style="display:none">
                                    <h6 class="fw-bold mb-3"><i class="bx bx-car text-info me-1"></i>Nuevo Vehículo</h6>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><label class="form-label small fw-semibold">Placa</label><input type="text" class="form-control text-uppercase" id="nv_placa" placeholder="ABC-123"></div>
                                        <div class="col-6"><label class="form-label small fw-semibold">Color</label><input type="text" class="form-control" id="nv_color" placeholder="Rojo"></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Categoría <span class="text-danger">*</span></label>
                                        <select class="form-select" id="nv_categoria">
                                            <?php foreach ($categoriasVH as $cat): ?>
                                                <option value="<?= $cat['id_categoria'] ?>" data-factor="<?= $cat['factor_precio'] ?>"><?= htmlspecialchars($cat['nombre']) ?> (x<?= $cat['factor_precio'] ?>)</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-info btn-sm w-100 fw-bold" onclick="registrarNuevoVehiculo()">
                                        <i class="bx bx-check me-1"></i>Registrar Vehículo
                                    </button>
                                </div>

                                <!-- ───── UBICACIÓN ───── -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold"><i class="bx bx-map text-warning me-1"></i>Ubicación en Local</label>
                                    <input type="text" class="form-control" name="ubicacion_en_local" placeholder="Ej: Zona A, Bahía 3">
                                </div>

                                <!-- ───── SERVICIOS ───── -->
                                <label class="form-label fw-semibold mb-3"><i class="bx bx-badge-check text-success me-1"></i>Servicios <span class="text-danger">*</span></label>
                                <div class="row g-2 mb-3" id="listaServicios">
                                    <?php foreach ($servicios as $serv): ?>
                                    <div class="col-12">
                                        <div class="servicio-check" onclick="toggleServicio(this)" data-id="<?= $serv['id_servicio'] ?>" data-precio="<?= $serv['precio_base'] ?>" data-puntos="<?= $serv['acumula_puntos'] ?>">
                                            <div class="sc-radio"><i class="bx bx-check" style="font-size:0.9rem"></i></div>
                                            <div class="flex-grow-1">
                                                <div class="fw-bold"><?= htmlspecialchars($serv['nombre']) ?></div>
                                                <?php if ($serv['acumula_puntos']): ?>
                                                    <small class="text-warning"><i class="bx bx-star"></i> Acumula puntos</small>
                                                <?php endif; ?>
                                            </div>
                                            <span class="badge bg-label-success fw-bold px-3 py-2">S/ <?= number_format($serv['precio_base'], 2) ?></span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- ───── PROMOCIONES ───── -->
                                <?php if (!empty($promociones)): ?>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold"><i class="bx bx-gift text-danger me-1"></i>Promoción (opcional)</label>
                                    <div class="row g-2" id="listaPromos">
                                        <div class="col-12">
                                            <div class="promo-option selected" data-id="" onclick="selectPromo(this)">
                                                <div class="fw-bold small">Sin promoción</div>
                                            </div>
                                        </div>
                                        <?php foreach ($promociones as $promo): ?>
                                        <div class="col-12">
                                            <div class="promo-option" data-id="<?= $promo['id_promocion'] ?>" data-tipo="<?= $promo['tipo_descuento'] ?>" data-valor="<?= $promo['valor'] ?>" data-unica="<?= $promo['solo_una_vez_por_cliente'] ?>" onclick="selectPromo(this)">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold small"><?= htmlspecialchars($promo['nombre']) ?></div>
                                                        <small class="text-muted"><?= $promo['fecha_inicio'] ?> al <?= $promo['fecha_fin'] ?></small>
                                                    </div>
                                                    <span class="badge bg-danger">
                                                        <?= $promo['tipo_descuento'] === 'PORCENTAJE' ? '-' . $promo['valor'] . '%' : '-S/ ' . number_format($promo['valor'], 2) ?>
                                                    </span>
                                                </div>
                                                <small class="text-warning promo-advertencia" style="display:none"><i class="bx bx-error"></i> Ya usada por este cliente</small>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- ───── CANJEAR PUNTOS ───── -->
                                <div id="canjeoPuntosBox" class="mb-4" style="display:none">
                                    <div class="alert alert-warning d-flex align-items-center gap-2 py-2">
                                        <i class="bx bx-star bx-tada" style="font-size:1.5rem"></i>
                                        <div>
                                            <div class="fw-bold small">¡Este cliente puede canjear puntos!</div>
                                            <small>Tiene <strong id="canjeoPuntosVal">0</strong> puntos. Meta: <?= $metaPuntos ?>. <strong>Servicio GRATIS</strong>.</small>
                                        </div>
                                        <div class="form-check form-switch ms-auto">
                                            <input class="form-check-input" type="checkbox" id="chkCanjePuntos" onchange="calcularTotal()">
                                        </div>
                                    </div>
                                </div>

                                <!-- ───── RESUMEN ───── -->
                                <div id="resumenDescuentos" style="display:none">
                                    <div class="resumen-item"><span>Subtotal Servicios</span><span class="fw-bold" id="rSubtotal">S/ 0.00</span></div>
                                    <div class="resumen-item text-danger" id="rPromoRow" style="display:none"><span>Descuento Promo</span><span class="fw-bold" id="rPromo">- S/ 0.00</span></div>
                                    <div class="resumen-item text-success" id="rCanjeRow" style="display:none"><span>🌟 Canje Puntos</span><span class="fw-bold" id="rCanje">- S/ 0.00</span></div>
                                </div>

                                <div class="resumen-total mb-4">
                                    <div class="small" style="opacity:0.8">TOTAL ESTIMADO</div>
                                    <div class="rt-val" id="totalEstimado">S/ 0.00</div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold" id="btnCrearOrden">
                                    <i class="bx bx-right-arrow-alt me-1"></i> CREAR ORDEN Y CONTINUAR
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- PASO 2: ORDEN EN PROCESO                             -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div class="tunnel-panel <?= $pasoActual === 2 ? 'active' : '' ?>" id="panel-paso2">
            <?php if ($ordenActiva && ($ordenActiva['estado'] === 'EN_PROCESO' || $ordenActiva['estado'] === 'EN_COLA')): ?>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Info orden -->
                    <div class="card shadow-sm mb-4" style="border:none;border-radius:14px;border-top:4px solid #ffab00">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h5 class="fw-bold mb-1"><i class="bx bx-loader-circle text-warning me-1"></i>Orden #<?= $ordenActiva['id_orden'] ?> — En Proceso</h5>
                                    <p class="text-muted mb-0">
                                        <i class="bx bx-user me-1"></i><?= htmlspecialchars($ordenActiva['cli_nombres'] . ' ' . $ordenActiva['cli_apellidos']) ?>
                                        <?php if ($ordenActiva['placa']): ?>
                                            <span class="ms-2"><i class="bx bx-car me-1"></i><?= $ordenActiva['placa'] ?> (<?= $ordenActiva['cat_nombre'] ?>)</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <span class="badge bg-warning fs-6 px-3">EN PROCESO</span>
                            </div>

                            <!-- Detalle -->
                            <div class="mt-3 pt-3 border-top">
                                <h6 class="fw-bold small text-uppercase mb-2"><i class="bx bx-list-ul text-primary me-1"></i>Detalle</h6>
                                <?php foreach ($detallesOrden as $det): ?>
                                <div class="resumen-item">
                                    <span>
                                        <?php if ($det['servicio_nombre']): ?>
                                            <i class="bx bx-badge-check text-success me-1"></i><?= htmlspecialchars($det['servicio_nombre']) ?>
                                        <?php else: ?>
                                            <i class="bx bx-package text-info me-1"></i><?= htmlspecialchars($det['producto_nombre']) ?> x<?= $det['cantidad'] ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="fw-bold">S/ <?= number_format($det['subtotal'], 2) ?></span>
                                </div>
                                <?php endforeach; ?>

                                <?php if ($ordenActiva['descuento_promo'] > 0): ?>
                                <div class="resumen-item text-danger"><span><i class="bx bx-gift me-1"></i>Desc. Promoción</span><span class="fw-bold">- S/ <?= number_format($ordenActiva['descuento_promo'], 2) ?></span></div>
                                <?php endif; ?>
                                <?php if ($ordenActiva['descuento_puntos'] > 0): ?>
                                <div class="resumen-item text-success"><span><i class="bx bx-star me-1"></i>Canje Puntos (GRATIS)</span><span class="fw-bold">- S/ <?= number_format($ordenActiva['descuento_puntos'], 2) ?></span></div>
                                <?php endif; ?>

                                <div class="resumen-total mt-3">
                                    <div class="small" style="opacity:0.8">TOTAL ACTUAL</div>
                                    <div class="rt-val">S/ <?= number_format($ordenActiva['total_final'], 2) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ───── PUNTOS DEL CLIENTE ───── -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="points-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small" style="opacity:0.8">Puntos Acumulados del Cliente</div>
                                        <div class="pc-val"><?= $ordenActiva['cli_puntos'] ?? 0 ?></div>
                                    </div>
                                    <i class="bx bx-star" style="font-size:2.5rem;opacity:0.3"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="points-card" style="background:linear-gradient(135deg,#71dd37,#56c41a)">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small" style="opacity:0.8">Puntos que ganará</div>
                                        <div class="pc-val">+<?= $puntosAcumulables ?></div>
                                        <small style="opacity:0.8">Total: <?= ($ordenActiva['cli_puntos'] ?? 0) + $puntosAcumulables ?> / <?= $metaPuntos ?></small>
                                    </div>
                                    <i class="bx bx-trending-up" style="font-size:2.5rem;opacity:0.3"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Productos tienda -->
                    <?php if ($tokenDesbloqueado || $modoSinCajero): ?>
                    <div class="card shadow-sm mb-4" style="border:none;border-radius:14px">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success"><i class="bx bx-lock-open-alt me-1"></i>Desbloqueado</span>
                                <h6 class="fw-bold mb-0"><i class="bx bx-store-alt text-info me-1"></i>Agregar Productos</h6>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row g-3">
                                <?php foreach ($productos as $prod): ?>
                                <div class="col-6 col-sm-4 col-md-3">
                                    <div class="prod-card" onclick="agregarProducto(<?= $prod['id_producto'] ?>, '<?= htmlspecialchars(addslashes($prod['nombre'])) ?>')">
                                        <div class="pc-icon"><i class="bx bx-package"></i></div>
                                        <div class="fw-bold small"><?= htmlspecialchars($prod['nombre']) ?></div>
                                         <div class="text-primary fw-bold">S/ <?= number_format($prod['precio_venta_pos'], 2) ?></div>
                                         <small class="text-muted">Stock: <?= $prod['stock_actual'] ?></small>
                                         <?php if($prod['dias_vencimiento'] !== null && $prod['dias_vencimiento'] <= 30): ?>
                                            <div class="text-warning fw-bold" style="font-size:0.65rem"><i class="bx bx-alarm-exclamation"></i> Por vencer</div>
                                         <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($productos)): ?>
                                <div class="col-12 text-center py-3 text-muted"><i class="bx bx-package" style="font-size:2rem"></i><p class="mb-0 mt-2">Sin productos</p></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="lock-overlay mb-4">
                        <i class="bx bx-lock-alt" style="font-size:2.5rem"></i>
                        <h6 class="fw-bold mt-2">Productos Bloqueados</h6>
                        <p class="small mb-3">Necesitas un token para agregar productos.</p>
                        <div class="token-input-group">
                            <div class="input-group">
                                <input type="text" class="form-control" id="tokenInputPaso2" placeholder="CÓDIGO" maxlength="6">
                                <button class="btn btn-primary" onclick="desbloquearToken()"><i class="bx bx-key me-1"></i>Usar</button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Botón Finalizar -->
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <button class="btn btn-success btn-lg w-100 rounded-pill fw-bold" onclick="finalizarOrden(<?= $ordenActiva['id_orden'] ?>)">
                                <i class="bx bx-check-circle me-1"></i> FINALIZAR — ENVIAR A COBRO
                            </button>
                            <p class="text-center text-muted small mt-2">La orden pasará a "Por Cobrar".</p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ══════════════════════════════════════════════════════ -->
        <!-- PASO 3: POR COBRAR                                   -->
        <!-- ══════════════════════════════════════════════════════ -->
        <div class="tunnel-panel <?= $pasoActual === 3 ? 'active' : '' ?>" id="panel-paso3">
            <?php if ($ordenActiva && $ordenActiva['estado'] === 'POR_COBRAR'): ?>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-sm" style="border:none;border-radius:14px;border-top:4px solid #71dd37">
                        <div class="card-body text-center p-4">
                            <div style="width:64px;height:64px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center" class="bg-label-success mb-3">
                                <i class="bx bx-hourglass" style="font-size:2rem"></i>
                            </div>
                            <h4 class="fw-bold">Orden #<?= $ordenActiva['id_orden'] ?></h4>
                            <span class="badge bg-success fs-6 px-3 py-2 mb-3">POR COBRAR</span>
                            <p class="text-muted">Esperando cajero.</p>

                            <div class="resumen-total mb-4">
                                <div class="small" style="opacity:0.8">TOTAL</div>
                                <div class="rt-val">S/ <?= number_format($ordenActiva['total_final'], 2) ?></div>
                            </div>

                            <?php if ($tokenDesbloqueado || $modoSinCajero): ?>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Método de Pago</label>
                                <div class="row g-2">
                                    <div class="col-6"><button type="button" class="btn btn-outline-success w-100 btn-metodo active" data-metodo="EFECTIVO"><i class="bx bx-money me-1"></i>Efectivo</button></div>
                                    <div class="col-6"><button type="button" class="btn btn-outline-primary w-100 btn-metodo" data-metodo="YAPE"><i class="bx bx-mobile me-1"></i>Yape</button></div>
                                    <div class="col-6"><button type="button" class="btn btn-outline-info w-100 btn-metodo" data-metodo="PLIN"><i class="bx bx-mobile-alt me-1"></i>Plin</button></div>
                                    <div class="col-6"><button type="button" class="btn btn-outline-warning w-100 btn-metodo" data-metodo="TARJETA"><i class="bx bx-credit-card me-1"></i>Tarjeta</button></div>
                                </div>
                            </div>
                            <button class="btn btn-success btn-lg w-100 rounded-pill fw-bold" onclick="cobrarOrden(<?= $ordenActiva['id_orden'] ?>)">
                                <i class="bx bx-check-double me-1"></i> COBRAR Y FINALIZAR
                            </button>
                            <?php else: ?>
                            <div class="lock-overlay mb-3">
                                <i class="bx bx-lock-alt" style="font-size:2rem"></i>
                                <p class="small mt-2 mb-3">Ingresa un token para cobrar:</p>
                                <div class="token-input-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="tokenInputPaso3" placeholder="CÓDIGO" maxlength="6">
                                        <button class="btn btn-primary" onclick="desbloquearToken()"><i class="bx bx-key me-1"></i>Usar</button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <hr class="my-4">
                            <button class="btn btn-outline-secondary w-100" onclick="location.reload()"><i class="bx bx-refresh me-1"></i>Actualizar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Toast -->
<div class="bs-toast toast fade bg-success position-fixed top-0 end-0 m-3" id="toastSistema" role="alert" style="z-index:11000">
    <div class="toast-header"><strong class="me-auto">Carwash XP</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
    <div class="toast-body" id="toastMensaje"></div>
</div>

<?php require VIEW_PATH . '/layouts/footer_tunnel.view.php'; ?>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const META_PUNTOS = <?= $metaPuntos ?>;
let metodoPago = 'EFECTIVO';
let clienteSeleccionado = null;
let promoSeleccionada = null;

// ═══ CLIENTE CHANGE → Cargar vehículos ═══
async function onClienteChange() {
    const sel = document.getElementById('sel_cliente');
    clienteSeleccionado = sel.value;
    const vehSel = document.getElementById('sel_vehiculo');
    vehSel.innerHTML = '<option value="">-- Sin vehículo --</option>';

    // Info puntos
    const opt = sel.selectedOptions[0];
    const puntosDiv = document.getElementById('infoPuntosCliente');
    const canjeBox = document.getElementById('canjeoPuntosBox');
    if (sel.value && opt) {
        const puntos = parseInt(opt.dataset.puntos) || 0;
        const yaCanjo = parseInt(opt.dataset.canjeo) || 0;
        document.getElementById('puntosClienteActual').textContent = puntos;
        puntosDiv.style.display = 'block';
        // Canje
        if (puntos >= META_PUNTOS && !yaCanjo) {
            document.getElementById('canjeoPuntosVal').textContent = puntos;
            canjeBox.style.display = 'block';
        } else {
            canjeBox.style.display = 'none';
            document.getElementById('chkCanjePuntos').checked = false;
        }
    } else {
        puntosDiv.style.display = 'none';
        canjeBox.style.display = 'none';
    }

    // Botón nuevo vehículo
    document.getElementById('btnNuevoVeh').style.display = sel.value ? 'block' : 'none';
    document.getElementById('formNuevoVehiculo').style.display = 'none';

    if (!sel.value) { calcularTotal(); return; }

    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/getvehiculos?id_cliente=${sel.value}`);
        const json = await res.json();
        (json.data || []).forEach(v => {
            const o = document.createElement('option');
            o.value = v.id_vehiculo;
            o.textContent = `${v.placa || 'Sin placa'} — ${v.categoria} (x${v.factor_precio})`;
            o.dataset.factor = v.factor_precio;
            vehSel.appendChild(o);
        });
        if (json.data && json.data.length === 1) vehSel.selectedIndex = 1;
    } catch(e) { console.error(e); }

    // Verificar promos
    document.querySelectorAll('.promo-advertencia').forEach(a => a.style.display = 'none');
    document.querySelectorAll('.promo-option[data-id]').forEach(async po => {
        if (!po.dataset.id || !po.dataset.unica || po.dataset.unica === '0') return;
        try {
            const r = await fetch(`${BASE_URL}/operaciones/dashboard/verificarpromo?id_cliente=${sel.value}&id_promocion=${po.dataset.id}`);
            const d = await r.json();
            if (d.usado) po.querySelector('.promo-advertencia').style.display = 'block';
        } catch(e) {}
    });

    calcularTotal();
}

// ═══ TOGGLE FORMS ═══
function toggleNuevoCliente() {
    const f = document.getElementById('formNuevoCliente');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}
function toggleNuevoVehiculo() {
    const f = document.getElementById('formNuevoVehiculo');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

// ═══ RENIEC ═══
async function buscarReniec() {
    const dni = document.getElementById('nc_dni').value.trim();
    if (dni.length !== 8) return mostrarToast('DNI debe tener 8 dígitos', 'warning');
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/consultarreniec?dni=${dni}`);
        const data = await res.json();
        if (data.success) {
            document.getElementById('nc_nombres').value = data.nombres;
            document.getElementById('nc_apellidos').value = data.apellidos;
            mostrarToast('Datos RENIEC cargados', 'success');
        } else {
            mostrarToast(data.message || 'No se encontró en RENIEC', 'warning');
        }
    } catch(e) { mostrarToast('Error conectando con RENIEC', 'danger'); }
}

// ═══ REGISTRAR CLIENTE ═══
async function registrarNuevoCliente() {
    const data = {
        dni: document.getElementById('nc_dni').value.trim(),
        nombres: document.getElementById('nc_nombres').value.trim(),
        apellidos: document.getElementById('nc_apellidos').value.trim(),
        sexo: document.getElementById('nc_sexo').value,
        telefono: document.getElementById('nc_telefono').value.trim()
    };
    if (!data.dni || !data.nombres) return mostrarToast('DNI y Nombres obligatorios', 'warning');
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/registrarcliente`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
        });
        const r = await res.json();
        if (r.success) {
            mostrarToast(r.message, 'success');
            // Agregar al select
            const sel = document.getElementById('sel_cliente');
            const opt = document.createElement('option');
            opt.value = r.id_cliente;
            opt.textContent = `${data.dni} — ${r.nombre_completo}`;
            opt.dataset.puntos = 0;
            opt.dataset.canjeo = 0;
            sel.appendChild(opt);
            sel.value = r.id_cliente;
            onClienteChange();
            document.getElementById('formNuevoCliente').style.display = 'none';
        } else { mostrarToast(r.message, 'danger'); }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
}

// ═══ REGISTRAR VEHÍCULO ═══
async function registrarNuevoVehiculo() {
    const sel = document.getElementById('sel_cliente');
    if (!sel.value) return mostrarToast('Selecciona un cliente primero', 'warning');
    const data = {
        id_cliente: sel.value,
        id_categoria: document.getElementById('nv_categoria').value,
        placa: document.getElementById('nv_placa').value.trim(),
        color: document.getElementById('nv_color').value.trim()
    };
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/registrarvehiculo`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(data)
        });
        const r = await res.json();
        if (r.success) {
            mostrarToast(r.message, 'success');
            onClienteChange(); // Reload vehículos
            document.getElementById('formNuevoVehiculo').style.display = 'none';
        } else { mostrarToast(r.message, 'danger'); }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); }
}

// ═══ SERVICIOS ═══
function toggleServicio(el) {
    el.classList.toggle('selected');
    calcularTotal();
}

// ═══ PROMOCIÓN ═══
function selectPromo(el) {
    document.querySelectorAll('.promo-option').forEach(p => p.classList.remove('selected'));
    el.classList.add('selected');
    promoSeleccionada = el.dataset.id || null;
    calcularTotal();
}

// ═══ CALCULAR TOTAL ═══
function calcularTotal() {
    let subtotal = 0;
    document.querySelectorAll('.servicio-check.selected').forEach(sc => subtotal += parseFloat(sc.dataset.precio));
    const sel = document.getElementById('sel_vehiculo');
    const factor = (sel && sel.value) ? (parseFloat(sel.selectedOptions[0]?.dataset.factor) || 1) : 1;
    subtotal *= factor;

    // Descuento promo
    let descPromo = 0;
    const promoEl = document.querySelector('.promo-option.selected[data-id]');
    if (promoEl && promoEl.dataset.id) {
        const adv = promoEl.querySelector('.promo-advertencia');
        if (!adv || adv.style.display === 'none') {
            descPromo = promoEl.dataset.tipo === 'PORCENTAJE'
                ? subtotal * (parseFloat(promoEl.dataset.valor) / 100)
                : Math.min(parseFloat(promoEl.dataset.valor), subtotal);
        }
    }

    // Canje puntos
    let descPuntos = 0;
    const chk = document.getElementById('chkCanjePuntos');
    if (chk && chk.checked) { descPuntos = subtotal - descPromo; }

    const total = Math.max(subtotal - descPromo - descPuntos, 0);

    // Mostrar resumen
    const hasDescuento = descPromo > 0 || descPuntos > 0;
    document.getElementById('resumenDescuentos').style.display = hasDescuento ? 'block' : 'none';
    document.getElementById('rSubtotal').textContent = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('rPromoRow').style.display = descPromo > 0 ? 'flex' : 'none';
    document.getElementById('rPromo').textContent = '- S/ ' + descPromo.toFixed(2);
    document.getElementById('rCanjeRow').style.display = descPuntos > 0 ? 'flex' : 'none';
    document.getElementById('rCanje').textContent = '- S/ ' + descPuntos.toFixed(2);

    document.getElementById('totalEstimado').textContent = 'S/ ' + total.toFixed(2);
}

// ═══ CREAR ORDEN ═══
document.getElementById('formNuevaOrden')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const selCliente = document.getElementById('sel_cliente');
    const selVehiculo = document.getElementById('sel_vehiculo');
    const serviciosSelected = document.querySelectorAll('.servicio-check.selected');

    if (!selCliente.value) return mostrarToast('Selecciona un cliente', 'danger');
    if (!serviciosSelected.length) return mostrarToast('Selecciona al menos un servicio', 'danger');

    const factor = selVehiculo.value ? (parseFloat(selVehiculo.selectedOptions[0]?.dataset.factor) || 1) : 1;
    const servicios = [];
    serviciosSelected.forEach(sc => {
        servicios.push({ id_servicio: sc.dataset.id, cantidad: 1, precio_unitario: (parseFloat(sc.dataset.precio) * factor).toFixed(2) });
    });

    const btn = document.getElementById('btnCrearOrden');
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Creando...';

    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/crearorden`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({
                id_cliente: selCliente.value,
                id_vehiculo: selVehiculo.value || null,
                ubicacion_en_local: this.querySelector('[name="ubicacion_en_local"]').value,
                servicios: servicios,
                id_promocion: promoSeleccionada || null,
                canjear_puntos: document.getElementById('chkCanjePuntos')?.checked || false
            })
        });
        const data = await res.json();
        if (data.success) { mostrarToast(data.message, 'success'); setTimeout(() => location.reload(), 800); }
        else { mostrarToast(data.message, 'danger'); btn.disabled = false; btn.innerHTML = '<i class="bx bx-right-arrow-alt me-1"></i> CREAR ORDEN Y CONTINUAR'; }
    } catch(e) { mostrarToast('Error de conexión', 'danger'); btn.disabled = false; btn.innerHTML = '<i class="bx bx-right-arrow-alt me-1"></i> CREAR ORDEN Y CONTINUAR'; }
});

// ═══ AGREGAR PRODUCTO ═══
async function agregarProducto(id, nombre) {
    const idOrden = <?= $ordenActiva['id_orden'] ?? 0 ?>;
    if (!idOrden || !confirm(`¿Agregar "${nombre}"?`)) return;
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/agregarproducto`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id_orden: idOrden, id_producto: id, cantidad: 1 })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) setTimeout(() => location.reload(), 600);
    } catch(e) { mostrarToast('Error', 'danger'); }
}

// ═══ FINALIZAR / COBRAR ═══
async function finalizarOrden(id) {
    if (!confirm('¿Finalizar esta orden?')) return;
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/finalizarorden`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ id_orden: id })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) setTimeout(() => location.reload(), 800);
    } catch(e) { mostrarToast('Error', 'danger'); }
}

document.querySelectorAll('.btn-metodo').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.btn-metodo').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        metodoPago = this.dataset.metodo;
    });
});

async function cobrarOrden(id) {
    if (!confirm(`¿Cobrar con ${metodoPago}?`)) return;
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/cobrarorden`, {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ id_orden: id, metodo_pago: metodoPago })
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
        if (data.success) setTimeout(() => location.reload(), 800);
    } catch(e) { mostrarToast('Error', 'danger'); }
}

// ═══ TOKEN ═══
async function desbloquearToken() {
    const input = document.getElementById('tokenInputPaso2') || document.getElementById('tokenInputPaso3');
    const codigo = input?.value.trim();
    if (!codigo) return mostrarToast('Ingresa el código', 'warning');
    try {
        const res = await fetch(`${BASE_URL}/operaciones/dashboard/validartoken`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ codigo })
        });
        const data = await res.json();
        if (data.success) { mostrarToast(data.message, 'success'); setTimeout(() => location.reload(), 600); }
        else { mostrarToast(data.message, 'danger'); }
    } catch(e) { mostrarToast('Error', 'danger'); }
}

function mostrarToast(msg, tipo) {
    const el = document.getElementById('toastSistema');
    if (!el) { alert(msg); return; }
    el.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = '11000';
    document.getElementById('toastMensaje').textContent = msg;
    new bootstrap.Toast(el).show();
}
</script>
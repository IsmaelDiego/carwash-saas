<?php require VIEW_PATH . '/layouts/header.view.php'; ?>

<style>
    /* ─── ANIMACIONES ─── */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .dash-animate {
        animation: fadeInUp 0.5s ease-out forwards;
        opacity: 0;
    }

    .dash-animate:nth-child(1) {
        animation-delay: 0.05s;
    }

    .dash-animate:nth-child(2) {
        animation-delay: 0.1s;
    }

    .dash-animate:nth-child(3) {
        animation-delay: 0.15s;
    }

    .dash-animate:nth-child(4) {
        animation-delay: 0.2s;
    }

    /* ─── WELCOME BANNER ─── */
    .welcome-banner {
        background: linear-gradient(135deg, #696cff 0%, #3b3dec 100%);
        border-radius: 20px;
        padding: 35px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(105, 108, 255, 0.25);
    }

    .welcome-banner::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle fill="rgba(255,255,255,0.05)" cx="80" cy="20" r="40"/><circle fill="rgba(255,255,255,0.08)" cx="90" cy="90" r="30"/></svg>') no-repeat right center;
        background-size: cover;
        opacity: 0.6;
        pointer-events: none;
    }

    .welcome-banner::before {
        content: '\e9d0';
        font-family: 'boxicons'!important;
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 10rem;
        opacity: 0.1;
        color: #fff;
    }

    .welcome-banner h4 {
        font-weight: 800;
        margin-bottom: 8px;
        color: #fff;
        font-size: 1.8rem;
    }

    .welcome-banner p {
        opacity: 0.9;
        font-size: 1rem;
        font-weight: 500;
    }

    /* ─── KPI CARDS ─── */
    .kpi-card {
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 20px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: #fff;
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border-color: rgba(105, 108, 255, 0.2);
    }

    .kpi-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .kpi-value {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    /* ─── ORDENES LIVE ─── */
    .order-live {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        z-index: 1;
        position: relative;
    }

    .order-pill {
        border-radius: 16px;
        padding: 12px 18px;
        text-align: center;
        flex: 1;
        min-width: 90px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }

    .order-pill:hover {
        transform: translateY(-3px);
        background: rgba(255,255,255,0.25);
    }

    .order-pill .opv {
        font-size: 1.6rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 2px;
    }

    .order-pill .opl {
        font-size: 0.75rem;
        font-weight: 600;
        color: rgba(255,255,255,0.9);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ─── TABLES ─── */
    .dash-table {
        margin-bottom: 0;
    }
    .dash-table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #a1acb8;
        font-weight: 700;
        border-bottom: 2px solid #f4f4f4;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .dash-table td {
        vertical-align: middle;
        border-color: #f8f9fa;
        font-size: 0.9rem;
        padding: 1rem 0.85rem;
    }
    
    .dash-table tbody tr {
        transition: background 0.2s;
    }
    .dash-table tbody tr:hover {
        background: rgba(105, 108, 255, 0.03);
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">
        <?php
        date_default_timezone_set('America/Lima');
        $hora = date('H');
        $saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');
        $nombre = $_SESSION['user']['name'] ?? '1';
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $fecha = $dias[date('w')] . ' ' . date('d') . ' de ' . $meses[date('n') - 1] . ', ' . date('Y');
        $oh = $dashData['ordenes_hoy'] ?? [];
        ?>

        <!-- ═══ WELCOME BANNER ═══ -->
        <div class="welcome-banner mb-4 dash-animate">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h4 class="mb-2 fw-bolder"><?= $saludo ?>, <?= htmlspecialchars($nombre) ?> 👋</h4>
                    <p class="mb-0"><i class="bx bx-calendar me-1"></i><?= ucfirst($fecha) ?></p>
                </div>
                <div class="col-md-5">
                    <div class="order-live mt-4 mt-md-0">
                        <div class="order-pill position-relative overflow-hidden">
                            <div class="opv"><?= $oh['en_cola'] ?? 0 ?></div>
                            <div class="opl">En Cola Hoy <i class='bx bx-time text-white-50 ms-1'></i></div>
                        </div>
                        <div class="order-pill position-relative overflow-hidden">
                            <div class="opv"><?= $oh['en_proceso'] ?? 0 ?></div>
                            <div class="opl">Proceso Hoy <i class='bx bx-loader-circle text-white-50 ms-1 bx-spin'></i></div>
                        </div>
                        <div class="order-pill position-relative overflow-hidden">
                            <div class="opv"><?= $oh['por_cobrar'] ?? 0 ?></div>
                            <div class="opl">A Cobrar Hoy <i class='bx bx-dollar-circle text-white-50 ms-1'></i></div>
                        </div>
                        <div class="order-pill position-relative overflow-hidden">
                            <div class="opv"><?= $oh['finalizadas'] ?? 0 ?></div>
                            <div class="opl">Listos Hoy <i class='bx bx-check-double text-white-50 ms-1'></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- ═══ KPI CARDS ═══ -->
        <div class="row mb-4">
            <?php
            $kpis = [
                ['val' => 'S/ ' . number_format($oh['ingresos_hoy'] ?? 0, 2), 'lbl' => 'Ingresos Hoy', 'icon' => 'bx-dollar-circle', 'color' => 'success'],
                ['val' => 'S/ ' . number_format($dashData['ingresos_mes'] ?? 0, 2), 'lbl' => 'Ingresos del Mes', 'icon' => 'bx-wallet', 'color' => 'primary'],
                ['val' => $dashData['total_clientes'], 'lbl' => 'Clientes', 'icon' => 'bx-user', 'color' => 'info'],
                ['val' => $dashData['total_puntos_clientes'], 'lbl' => 'Puntos Globales', 'icon' => 'bx-star', 'color' => 'warning'],
                ['val' => $dashData['total_servicios'], 'lbl' => 'Servicios Activos', 'icon' => 'bx-badge-check', 'color' => 'success'],
                ['val' => $dashData['total_productos'], 'lbl' => 'Productos Tienda', 'icon' => 'bx-package', 'color' => 'dark'],
                ['val' => $dashData['total_promociones'], 'lbl' => 'Promos Activas', 'icon' => 'bx-gift', 'color' => 'danger', 'url' => '#'],
                ['val' => $dashData['pagos_pendientes'], 'lbl' => 'Pagos Pendientes', 'icon' => 'bx-money', 'color' => 'success', 'url' => BASE_URL . '/admin/pago'],
                ['val' => $dashData['permisos_pendientes'], 'lbl' => 'Permisos Pendientes', 'icon' => 'bx-calendar', 'color' => 'warning', 'url' => BASE_URL . '/admin/permiso'],
                ['val' => $dashData['tokens_activos'], 'lbl' => 'Tokens de Seguridad', 'icon' => 'bx-key', 'color' => 'secondary', 'url' => '#'],
            ];
            foreach ($kpis as $i => $kpi): ?>
                <div class="col-sm-6 col-md-4 col-xl-5th mb-3 dash-animate" style="flex: 1 0 20%;">
                    <?php if(!empty($kpi['url']) && $kpi['url'] !== '#'): ?><a href="<?= $kpi['url'] ?>" style="text-decoration:none; color:inherit; display:block; height:100%;"><?php endif; ?>
                    <div class="card kpi-card h-100" <?= (!empty($kpi['url']) && $kpi['url'] !== '#') ? 'style="cursor:pointer;"' : '' ?>>
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <div class="kpi-icon bg-label-<?= $kpi['color'] ?> shadow-sm flex-shrink-0">
                                    <i class="bx <?= $kpi['icon'] ?> text-<?= $kpi['color'] ?>"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-muted fw-bold text-uppercase d-block mb-1 text-truncate" style="font-size:0.7rem; letter-spacing: 0.5px;"><?= $kpi['lbl'] ?></small>
                                    <div class="kpi-value text-<?= $kpi['color'] ?> text-truncate" title="<?= strip_tags($kpi['val']) ?>"><?= $kpi['val'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($kpi['url']) && $kpi['url'] !== '#'): ?></a><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <!-- ═══ CHART: INGRESOS POR MES ═══ -->
            <div class="col-lg-8 mb-4 dash-animate">
                <div class="card h-100" style="border: 1px solid rgba(0,0,0,0.04); border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-header border-0 pb-0 pt-4 px-4">
                        <h6 class="mb-0 fw-bold fs-5"><i class="bx bx-dollar-circle text-success me-2 fs-4 align-middle"></i>Ingresos por Mes <span class="text-muted fw-normal fs-6 ms-1">(6 meses)</span></h6>
                    </div>
                    <div class="card-body px-3 pb-3 pt-0 mt-2">
                        <div id="chartIngresosMes"></div>
                    </div>
                </div>
            </div>
            <!-- ═══ CHART: VEHÍCULOS ═══ -->
            <div class="col-lg-4 mb-4 dash-animate">
                <div class="card h-100" style="border: 1px solid rgba(0,0,0,0.04); border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-header border-0 pb-0 pt-4 px-4">
                        <h6 class="mb-0 fw-bold fs-5"><i class="bx bx-car text-info me-2 fs-4 align-middle"></i>Flota por Categoría</h6>
                    </div>
                    <div class="card-body px-3 pb-3 pt-0 mt-3 d-flex align-items-center justify-content-center">
                        <div id="chartVehiculos" class="w-100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ═══ ÚLTIMAS ÓRDENES ═══ -->
            <div class="col-lg-7 mb-4 dash-animate">
                <div class="card h-100" style="border: 1px solid rgba(0,0,0,0.04); border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-header border-0 d-flex justify-content-between align-items-center pt-4 px-4 pb-3">
                        <h6 class="mb-0 fw-bold fs-5"><i class="bx bx-receipt text-primary me-2 fs-4 align-middle"></i>Últimas Órdenes</h6>
                        <a href="<?= BASE_URL ?>/admin/orden" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-bold">Ver Todas</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4"># Ticket</th>
                                        <th>Cliente</th>
                                        <th>Vehículo</th>
                                        <th>Estado Actual</th>
                                        <th class="text-end pe-4">Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dashData['ultimas_ordenes'])): ?>
                                        <?php foreach ($dashData['ultimas_ordenes'] as $ord):
                                            $est_colors = ['EN_COLA' => 'primary', 'EN_PROCESO' => 'warning', 'POR_COBRAR' => 'success', 'FINALIZADO' => 'secondary', 'ANULADO' => 'danger'];
                                            $badge = $est_colors[$ord['estado']] ?? 'secondary';
                                        ?>
                                            <tr>
                                                <td class="fw-bold ps-4 text-primary">#<?= str_pad($ord['id_orden'], 5, '0', STR_PAD_LEFT) ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bxs-user"></i></span>
                                                        </div>
                                                        <span class="fw-semibold text-truncate" style="max-width: 150px;"><?= htmlspecialchars($ord['cliente'] ?? '—') ?></span>
                                                    </div>
                                                </td>
                                                <td><span class="badge bg-label-dark fw-bold border" style="letter-spacing: 1px;"><?= $ord['placa'] ?? '—' ?></span></td>
                                                <td><span class="badge bg-<?= $badge ?> bg-glow rounded-pill px-3 shadow-sm"><?= str_replace('_', ' ', $ord['estado']) ?></span></td>
                                                <td class="text-end fw-bold text-dark pe-4 fs-6">S/ <?= number_format($ord['total_final'] ?? 0, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5"><i class="bx bx-receipt opacity-25" style="font-size:3rem"></i><br>Aún no hay tickets hoy</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ RESUMEN RÁPIDO ─── -->
            <div class="col-lg-5 mb-4 dash-animate">
                <div class="card h-100" style="border: 1px solid rgba(0,0,0,0.04); border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-header border-0 pt-4 px-4 pb-2">
                        <h6 class="mb-0 fw-bold fs-5"><i class="bx bx-info-circle text-primary me-2 fs-4 align-middle"></i>Métricas Rápidas</h6>
                    </div>
                    <div class="card-body px-4 pt-2">
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom dashed">
                            <span class="text-muted d-flex align-items-center"><i class="bx bx-phone text-success fs-5 me-2"></i>Contactos de WhatsApp</span>
                            <span class="fw-bold fs-5"><?= $dashData['clientes_whatsapp'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom dashed">
                            <span class="text-muted d-flex align-items-center"><i class="bx bx-sun text-warning fs-5 me-2"></i>Temporada Activa</span>
                            <span class="badge bg-label-primary px-3 rounded-pill fw-bold"><?= $dashData['temporada_activa']['nombre'] ?? 'Ninguna' ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom dashed">
                            <span class="text-muted d-flex align-items-center"><i class="bx bx-receipt text-info fs-5 me-2"></i>Tickets de Hoy</span>
                            <span class="fw-bold fs-5"><?= $oh['total_hoy'] ?? 0 ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom dashed">
                            <span class="text-muted d-flex align-items-center"><i class="bx bxs-tag-x text-danger fs-5 me-2"></i>Desc. por Promociones</span>
                            <span class="fw-bold text-danger fs-6">- S/ <?= number_format($oh['descuentospromo_hoy'] ?? 0, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted d-flex align-items-center"><i class="bx bxs-gift text-warning fs-5 me-2"></i>Puntos Canjeados</span>
                            <span class="fw-bold text-warning fs-6">- S/ <?= number_format($oh['descuentospuntos_hoy'] ?? 0, 2) ?></span>
                        </div>

                        <div class="mt-4 pt-4 border-top">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted"><i class="bx bx-badge-check text-success me-2"></i>Top Servicios Activos</h6>
                            <?php foreach ($dashData['servicios_populares'] as $serv): ?>
                                <div class="d-flex align-items-center justify-content-between mb-3 p-2 rounded hover-bg-light transition-all">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-<?= $serv['estado'] ? 'success' : 'secondary' ?>"><i class="bx bx-car"></i></span>
                                        </div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($serv['nombre']) ?></span>
                                    </div>
                                    <span class="fw-bold text-primary">S/ <?= number_format($serv['precio_base'], 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ RECENT CLIENTS ═══ -->
        <div class="row dash-animate">
            <div class="col-12 mb-4">
                <div class="card" style="border: 1px solid rgba(0,0,0,0.04); border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-header border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold fs-5"><i class="bx bx-user-plus text-success me-2 fs-4 align-middle"></i>Nuevos Clientes en la Red</h6>
                        <a href="<?= BASE_URL ?>/admin/cliente" class="btn btn-sm btn-label-success rounded-pill px-3 fw-bold">Ver Directorio</a>
                    </div>
                    <div class="card-body p-0 mt-3">
                        <div class="table-responsive">
                            <table class="table dash-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Perfil</th>
                                        <th>Contacto Directo</th>
                                        <th>Nivel de Lealtad</th>
                                        <th class="text-end pe-4">Fecha de Alta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dashData['ultimos_clientes'] as $cli): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded-circle bg-primary text-white shadow-sm"><?= strtoupper(substr($cli['nombres'], 0, 1) . substr($cli['apellidos'], 0, 1)) ?></span>
                                                    </div>
                                                    <span class="fw-bold text-dark text-uppercase"><?= htmlspecialchars($cli['nombres'] . ' ' . $cli['apellidos']) ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if($cli['telefono']): ?>
                                                    <span class="badge bg-label-secondary fw-medium px-3"><i class="bx bx-phone me-1"></i><?= $cli['telefono'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted small fst-italic">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning bg-glow text-dark px-3 rounded-pill fw-bold shadow-sm"><i class="bx bxs-star me-1"></i><?= $cli['puntos_acumulados'] ?> pt(s)</span>
                                            </td>
                                            <td class="text-muted small fw-medium text-end pe-4">
                                                <i class="bx bx-calendar-event me-1"></i><?= date('d/m/Y', strtotime($cli['fecha_registro'])) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof ApexCharts === 'undefined') return;

        // Chart: Ingresos por Mes
        const ingresosMeses = <?= json_encode(array_column($dashData['ingresos_por_mes'], 'mes')) ?>;
        const ingresosData = <?= json_encode(array_column($dashData['ingresos_por_mes'], 'total')) ?>;

        new ApexCharts(document.querySelector('#chartIngresosMes'), {
            series: [{
                name: 'Ingresos',
                data: ingresosData
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'Public Sans'
            },
            colors: ['#71dd37'],
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.35, opacityTo: 0.05 }
            },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: ingresosMeses },
            yaxis: {
                labels: { formatter: (value) => 'S/ ' + value.toFixed(0) }
            },
            tooltip: {
                y: { formatter: (value) => 'S/ ' + value.toFixed(2) }
            },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f0f0f0' }
        }).render();

        // Chart: Vehículos por Categoría
        const vehLabels = <?= json_encode(array_column($dashData['vehiculos_por_categoria'], 'nombre')) ?>;
        const vehData = <?= json_encode(array_map('intval', array_column($dashData['vehiculos_por_categoria'], 'cantidad'))) ?>;

        new ApexCharts(document.querySelector('#chartVehiculos'), {
            series: vehData,
            chart: {
                type: 'donut',
                height: 260,
                fontFamily: 'Public Sans'
            },
            labels: vehLabels,
            colors: ['#696cff', '#03c3ec', '#71dd37', '#ffab00', '#ff3e1d'],
            legend: {
                position: 'bottom',
                fontSize: '12px'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '55%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: () => '<?= $dashData['total_vehiculos'] ?>'
                            }
                        }
                    }
                }
            }
        }).render();
    });
</script>
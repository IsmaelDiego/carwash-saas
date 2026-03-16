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
        background: linear-gradient(135deg, #1a237e 0%, #0d47a1 40%, #0288d1 100%);
        border-radius: 16px;
        padding: 28px 30px;
        color: #fff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 24px rgba(13, 71, 161, 0.25);
    }

    .welcome-banner::before {
        content: '\e9d0';
        font-family: 'boxicons'!important;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 8rem;
        opacity: 0.08;
        color: #fff;
    }

    .welcome-banner h4 {
        font-weight: 700;
        margin-bottom: 4px;
        color: #fff;
    }

    .welcome-banner p {
        opacity: 0.85;
        font-size: 0.9rem;
    }



    /* ─── KPI CARDS ─── */
    .kpi-card {
        border: none;
        border-radius: 14px;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .kpi-value {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }

    /* ─── ORDENES LIVE ─── */
    .order-live {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .order-pill {
        border-radius: 10px;
        padding: 8px 14px;
        text-align: center;
        flex: 1;
        min-width: 80px;
    }

    .order-pill .opv {
        font-size: 1.3rem;
        font-weight: 700;
    }

    .order-pill .opl {
        font-size: 0.68rem;
        font-weight: 600;
    }

    /* ─── TABLES ─── */
    .dash-table th {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #8592a3;
        font-weight: 600;
        border: 0;
    }

    .dash-table td {
        vertical-align: middle;
        border-color: #f4f4f4;
        font-size: 0.85rem;
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
                    <div class="order-live mt-3 mt-md-0">
                        <div class="order-pill bg-label-primary">
                            <div class="opv"><?= $oh['en_cola'] ?? 0 ?></div>
                            <div class="opl">En Cola</div>
                        </div>
                        <div class="order-pill bg-label-warning">
                            <div class="opv"><?= $oh['en_proceso'] ?? 0 ?></div>
                            <div class="opl">Proceso</div>
                        </div>
                        <div class="order-pill bg-label-success">
                            <div class="opv"><?= $oh['por_cobrar'] ?? 0 ?></div>
                            <div class="opl">Cobrar</div>
                        </div>
                        <div class="order-pill bg-label-info">
                            <div class="opv"><?= $oh['finalizadas'] ?? 0 ?></div>
                            <div class="opl">Listos</div>
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
                    <div class="card kpi-card shadow-sm h-100" style="border:1px solid #f0f0f0; <?= (!empty($kpi['url']) && $kpi['url'] !== '#') ? 'cursor:pointer;' : '' ?>">
                        <div class="card-body d-flex align-items-center justify-content-between p-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="kpi-icon bg-label-<?= $kpi['color'] ?> shadow-sm"><i class="bx <?= $kpi['icon'] ?> text-<?= $kpi['color'] ?>"></i></div>
                                <div><small class="text-muted fw-bold text-uppercase" style="font-size:0.65rem"><?= $kpi['lbl'] ?></small>
                                    <div class="kpi-value text-<?= $kpi['color'] ?>" style="font-size:1.4rem"><?= $kpi['val'] ?></div>
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
                <div class="card shadow-sm" style="border:none;border-radius:14px">
                    <div class="card-header border-0">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-dollar-circle text-success me-1"></i>Ingresos por Mes (6 meses)</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div id="chartIngresosMes"></div>
                    </div>
                </div>
            </div>
            <!-- ═══ CHART: VEHÍCULOS ═══ -->
            <div class="col-lg-4 mb-4 dash-animate">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px">
                    <div class="card-header border-0">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-car text-info me-1"></i>Vehículos por Categoría</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div id="chartVehiculos"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ═══ ÚLTIMAS ÓRDENES ═══ -->
            <div class="col-lg-7 mb-4 dash-animate">
                <div class="card shadow-sm" style="border:none;border-radius:14px">
                    <div class="card-header border-0 d-flex justify-content-between">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-receipt text-primary me-1"></i>Últimas Órdenes</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table dash-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Placa</th>
                                        <th>Estado</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($dashData['ultimas_ordenes'])): ?>
                                        <?php foreach ($dashData['ultimas_ordenes'] as $ord):
                                            $est_colors = ['EN_COLA' => 'primary', 'EN_PROCESO' => 'warning', 'POR_COBRAR' => 'success', 'FINALIZADO' => 'secondary', 'ANULADO' => 'danger'];
                                            $badge = $est_colors[$ord['estado']] ?? 'secondary';
                                        ?>
                                            <tr>
                                                <td class="fw-bold">#<?= $ord['id_orden'] ?></td>
                                                <td><i class="bx bxs-user-circle text-muted me-1"></i><?= htmlspecialchars($ord['cliente'] ?? '—') ?></td>
                                                <td><span class="badge bg-label-info"><?= $ord['placa'] ?? '—' ?></span></td>
                                                <td><span class="badge bg-label-<?= $badge ?>"><?= str_replace('_', ' ', $ord['estado']) ?></span></td>
                                                <td class="text-end fw-bold">S/ <?= number_format($ord['total_final'] ?? 0, 2) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4"><i class="bx bx-receipt" style="font-size:2rem"></i><br>Sin órdenes aún</td>
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
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px">
                    <div class="card-header border-0">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-info-circle text-primary me-1"></i>Indicadores Clave</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bx-phone text-success me-1"></i>WhatsApp Activos</span>
                            <span class="fw-bold"><?= $dashData['clientes_whatsapp'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bx-calendar-star text-warning me-1"></i>Total Temporadas</span>
                            <span class="fw-bold"><?= $dashData['total_temporadas'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bx-sun text-danger me-1"></i>Temporada Activa</span>
                            <span class="fw-bold text-primary"><?= $dashData['temporada_activa']['nombre'] ?? 'Ninguna' ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bx-key text-warning me-1"></i>Tokens Activos</span>
                            <span class="fw-bold text-warning"><?= $dashData['tokens_activos'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bx-receipt text-info me-1"></i>Órdenes Hoy</span>
                            <span class="fw-bold"><?= $oh['total_hoy'] ?? 0 ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted"><i class="bx bxs-tag-x text-danger me-1"></i>Desc. x Promociones</span>
                            <span class="fw-bold text-danger">- S/ <?= number_format($oh['descuentospromo_hoy'] ?? 0, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted"><i class="bx bxs-gift text-warning me-1"></i>Canjes Reclamados</span>
                            <span class="fw-bold text-warning">- S/ <?= number_format($oh['descuentospuntos_hoy'] ?? 0, 2) ?></span>
                        </div>

                        <hr>
                        <h6 class="fw-bold mb-3 small text-uppercase"><i class="bx bx-badge-check text-success me-1"></i>Servicios</h6>
                        <?php foreach ($dashData['servicios_populares'] as $serv): ?>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <span class="badge bg-<?= $serv['estado'] ? 'success' : 'secondary' ?> me-1" style="width:8px;height:8px;border-radius:50%;padding:0;display:inline-block"></span>
                                    <span class="small fw-semibold"><?= htmlspecialchars($serv['nombre']) ?></span>
                                </div>
                                <span class="small fw-bold text-primary">S/ <?= number_format($serv['precio_base'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ RECENT CLIENTS ═══ -->
        <div class="row dash-animate">
            <div class="col-12 mb-4">
                <div class="card shadow-sm" style="border:none;border-radius:14px">
                    <div class="card-header border-0">
                        <h6 class="mb-0 fw-bold"><i class="bx bx-user-plus text-success me-1"></i>Últimos Clientes Registrados</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table dash-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Puntos</th>
                                        <th>Registrado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dashData['ultimos_clientes'] as $cli): ?>
                                        <tr>
                                            <td class="fw-semibold"><i class="bx bxs-user text-primary me-1"></i><?= htmlspecialchars($cli['nombres'] . ' ' . $cli['apellidos']) ?></td>
                                            <td><?= $cli['telefono'] ?: '—' ?></td>
                                            <td><span class="badge bg-label-primary"><?= $cli['puntos_acumulados'] ?> pts</span></td>
                                            <td class="text-muted small"><?= date('d/m/Y', strtotime($cli['fecha_registro'])) ?></td>
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
<?php require VIEW_PATH . '/layouts/header.view.php'; ?>
<style>
    .fin-card {
        border: none;
        border-radius: 14px;
        transition: all 0.3s;
        overflow: hidden;
    }

    .fin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .fin-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .fin-val {
        font-size: 1.6rem;
        font-weight: 700;
        line-height: 1;
    }

    .fin-lbl {
        font-size: 0.78rem;
        color: #8592a3;
        margin-top: 3px;
    }

    .pe-card {
        background: linear-gradient(135deg, #696cff, #9b9dff);
        color: #fff;
        border-radius: 14px;
        padding: 24px;
        text-align: center;
    }

    .pe-val {
        font-size: 2.5rem;
        font-weight: 800;
    }

    .pe-lbl {
        font-size: 0.85rem;
        opacity: 0.85;
    }

    .chart-card {
        border: none;
        border-radius: 14px;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h5 class="fw-bold mb-1"><i class="bx bx-line-chart text-primary me-1"></i> Panel de Finanzas</h5>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                        <li class="breadcrumb-item active text-primary">Finanzas</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-label-primary fs-6 py-2 px-3 d-flex align-items-center"><i class="bx bx-calendar me-2"></i><?= date('F Y') ?></span>
                <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarInsumo" onclick="document.getElementById('formRegistrarInsumo').reset(); document.getElementById('insumo_id').value='';">
                    <i class="bx bx-plus me-1"></i> Nuevo Insumo
                </button>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarGasto">
                    <i class="bx bx-plus me-1"></i> Nuevo Gasto
                </button>
            </div>
        </div>

        <!-- ═══ KPI CARDS ═══ -->
        <div class="row mb-4" id="kpiCards">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card fin-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="fin-icon bg-label-success"><i class="bx bx-dollar-circle text-success"></i></div>
                        <div>
                            <div class="fin-val text-success" id="kIngHoy">S/ 0</div>
                            <div class="fin-lbl">Ingresos Hoy</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card fin-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="fin-icon bg-label-primary"><i class="bx bx-wallet text-primary"></i></div>
                        <div>
                            <div class="fin-val text-primary" id="kIngMes">S/ 0</div>
                            <div class="fin-lbl">Ingresos del Mes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card fin-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="fin-icon bg-label-danger"><i class="bx bx-trending-down text-danger"></i></div>
                        <div>
                            <div class="fin-val text-danger" id="kGastos">S/ 0</div>
                            <div class="fin-lbl">Gastos del Mes</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card fin-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="fin-icon bg-label-info"><i class="bx bx-group text-info"></i></div>
                        <div>
                            <div class="fin-val text-info" id="kPlanilla">S/ 0</div>
                            <div class="fin-lbl">Planilla del Mes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- ═══ PUNTO DE EQUILIBRIO ═══ -->
            <div class="col-lg-4 mb-3">
                <div class="pe-card h-100 d-flex flex-column justify-content-center">
                    <i class="bx bx-target-lock mb-2" style="font-size:2.5rem;opacity:0.8"></i>
                    <div class="pe-lbl">Punto de Equilibrio Mensual</div>
                    <div class="pe-val" id="puntoEq">S/ 0</div>
                    <div class="pe-lbl mt-2">Ventas mínimas necesarias para cubrir costos</div>
                    <div class="mt-2">
                        <div class="progress" style="height:8px;background:rgba(255,255,255,0.2)">
                            <div class="progress-bar bg-warning" id="peProgress" style="width:0%"></div>
                        </div>
                        <small class="mt-1 d-block" id="pePercentText">0% alcanzado</small>
                    </div>
                </div>
            </div>

            <!-- ═══ BALANCE RESUMEN ═══ -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bx bx-bar-chart-alt-2 text-primary me-1"></i>Balance del Mes</h6>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Ingresos Brutos</span>
                            <span class="fw-bold text-success" id="bIngresos">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">(-) Gastos Fijos</span>
                            <span class="fw-bold text-danger" id="bGFijos">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">(-) Gastos Variables</span>
                            <span class="fw-bold text-danger" id="bGVariables">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">(-) Planilla</span>
                            <span class="fw-bold text-danger" id="bPlanilla">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 mt-2">
                            <span class="fw-bold fs-5">Utilidad Neta</span>
                            <span class="fw-bold fs-5" id="bUtilidad">S/ 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══ MÉTODOS DE PAGO ═══ -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm h-100" style="border:none;border-radius:14px">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="bx bx-credit-card text-primary me-1"></i>Distribución de Pagos</h6>
                        <div id="chartMetodos"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- ═══ GRÁFICO INGRESOS VS GASTOS ═══ -->
            <div class="col-lg-8 mb-3">
                <div class="card chart-card shadow-sm">
                    <div class="card-header border-0">
                        <h6 class="fw-bold mb-0"><i class="bx bx-line-chart text-primary me-1"></i>Ingresos vs Gastos (6 Meses)</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div id="chartIngresosGastos" style="min-height:320px"></div>
                    </div>
                </div>
            </div>
            <!-- ═══ DESGLOSE GASTOS ═══ -->
            <div class="col-lg-4 mb-3">
                <div class="card chart-card shadow-sm h-100">
                    <div class="card-header border-0">
                        <h6 class="fw-bold mb-0"><i class="bx bx-pie-chart-alt text-danger me-1"></i>Desglose de Gastos</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div id="chartGastosPie" style="min-height:280px"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ═══ HISTORIAL DE GASTOS ═══ -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"><i class="bx bx-list-ul text-primary me-1"></i> Historial de Gastos Registrados</h6>
                    </div>
                    <div class="table-responsive text-nowrap px-3">
                        <table class="table table-hover w-100 my-3" id="tbGastos" >
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">ID</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Descripción</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Tipo</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Monto</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Fecha</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Registrado Por</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($historial_gastos) && count($historial_gastos) > 0): ?>
                                    <?php foreach ($historial_gastos as $g): ?>
                                        <tr>
                                            <td><?= $g['id_gasto'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($g['descripcion']) ?></td>
                                            <td>
                                                <?php if ($g['tipo_gasto'] == 'FIJO'): ?>
                                                    <span class="badge bg-label-danger">Fijo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-label-warning">Variable</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-danger">S/ <?= number_format($g['monto'], 2) ?></td>
                                            <td><?= date('d/m/Y', strtotime($g['fecha_gasto'])) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($g['registrador'] ?? 'Sistema') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- ═══ INVENTARIO DE INSUMOS ═══ -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0"><i class="bx bx-box text-warning me-1"></i> Control de Insumos</h6>
                    </div>
                    <div class="table-responsive text-nowrap px-3">
                        <table class="table table-hover w-100 my-3" id="tbInsumos">
                            <thead>
                                <tr style="background-color: #f8f9fa;">
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">ID</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Insumo</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">U. / Medida</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Costo Unit.</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Stock</th>
                                    <th class="text-secondary fw-bold text-uppercase" style="font-size: 0.75rem;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($lista_insumos) && count($lista_insumos) > 0): ?>
                                    <?php foreach ($lista_insumos as $i): ?>
                                        <tr>
                                            <td><?= $i['id_insumo'] ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($i['nombre']) ?></td>
                                            <td><span class="badge bg-label-info"><?= htmlspecialchars($i['unidad_medida']) ?></span></td>
                                            <td class="fw-bold text-dark">S/ <?= number_format($i['costo_unitario'], 2) ?></td>
                                            <td>
                                                <?php if ($i['stock_actual'] <= 5): ?>
                                                    <span class="badge bg-danger rounded-pill"><?= $i['stock_actual'] ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill"><?= $i['stock_actual'] ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-icon rounded-pill text-primary" onclick="editarInsumo(<?= $i['id_insumo'] ?>, '<?= addslashes($i['nombre']) ?>', '<?= addslashes($i['unidad_medida']) ?>', <?= $i['costo_unitario'] ?>, <?= $i['stock_actual'] ?>)">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- Modal Registrar Gasto -->
<div class="modal fade" id="modalRegistrarGasto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRegistrarGasto">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-trending-down text-danger me-2"></i> Registrar Nuevo Gasto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Descripción del Gasto (Servicios, Insumos, etc.)</label>
                        <input type="text" name="descripcion" class="form-control" required placeholder="Ej: Pago de Luz Marzo">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Tipo de Gasto</label>
                            <select name="tipo" class="form-select" required>
                                <option value="VARIABLE">Gasto Variable (Insumos, Reparación)</option>
                                <option value="FIJO">Gasto Fijo (Alquiler, Luz, Agua)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Origen (Opcional Insumo)</label>
                            <select name="id_insumo" class="form-select">
                                <option value="">Ninguno / Gastos Generales</option>
                                <?php if (isset($lista_insumos)): foreach ($lista_insumos as $in): ?>
                                        <option value="<?= $in['id_insumo'] ?>"><?= htmlspecialchars($in['nombre']) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Monto (S/)</label>
                            <input type="number" step="0.01" name="monto" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Fecha de Gasto</label>
                            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Guardar Gasto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Insumo -->
<div class="modal fade" id="modalRegistrarInsumo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formRegistrarInsumo">
                <input type="hidden" name="id_insumo" id="insumo_id">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-box text-warning me-2"></i> Registrar / Editar Insumo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Insumo</label>
                        <input type="text" name="nombre" id="insumo_nombre" class="form-control" required placeholder="Ej: Champú Cera">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Unidad de Medida</label>
                            <input type="text" name="unidad_medida" id="insumo_um" class="form-control" placeholder="Galón, Unidad, etc" value="Unidad">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Costo Referencial (S/)</label>
                            <input type="number" step="0.01" name="costo_unitario" id="insumo_costo" class="form-control" required placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stock Actual</label>
                        <input type="number" name="stock_actual" id="insumo_stock" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Guardar Insumo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<style>
    .dataTables_filter,
    .dataTables_length {
        display: none !important;
    }

    .dataTables_paginate {
        display: flex !important;
        justify-content: flex-start !important;
        margin-top: 1.5rem !important;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }

    .dataTables_info {
        text-align: right !important;
        margin-top: 1.5rem !important;
        padding-top: 1rem;
        color: #b0b0b0 !important;
    }
</style>

<script>
    const BASE_URL_FIN = "<?= BASE_URL ?>";

    document.addEventListener('DOMContentLoaded', () => {
        cargarFinanzas();
        initTables();
    });

    async function cargarFinanzas() {
        try {
            const res = await fetch(`${BASE_URL_FIN}/admin/finanzas/getresumen`);
            const json = await res.json();
            if (!json.success) return;
            const d = json.data;

            const ing = d.ingresos || {};
            const gas = d.gastos || {};
            const planilla = parseFloat(d.planilla) || 0;

            // KPIs
            document.getElementById('kIngHoy').textContent = `S/ ${parseFloat(ing.ingresos_hoy || 0).toFixed(2)}`;
            document.getElementById('kIngMes').textContent = `S/ ${parseFloat(ing.ingresos_mes || 0).toFixed(2)}`;
            document.getElementById('kGastos').textContent = `S/ ${parseFloat(gas.gastos_total || 0).toFixed(2)}`;
            document.getElementById('kPlanilla').textContent = `S/ ${planilla.toFixed(2)}`;

            // Punto de equilibrio
            const pe = parseFloat(d.punto_equilibrio) || 0;
            document.getElementById('puntoEq').textContent = `S/ ${pe.toFixed(2)}`;
            const ingMesValue = parseFloat(ing.ingresos_mes) || 0;
            const pePercent = (pe > 0) ? Math.min(100, (ingMesValue / pe * 100)).toFixed(0) : 0;
            document.getElementById('peProgress').style.width = `${pePercent}%`;
            document.getElementById('pePercentText').textContent = `${pePercent}% alcanzado`;

            // Balance
            const gFijos = parseFloat(gas.gastos_fijos) || 0;
            const gVars = parseFloat(gas.gastos_variables) || 0;
            document.getElementById('bIngresos').textContent = `S/ ${ingMesValue.toFixed(2)}`;
            document.getElementById('bGFijos').textContent = `S/ ${gFijos.toFixed(2)}`;
            document.getElementById('bGVariables').textContent = `S/ ${gVars.toFixed(2)}`;
            document.getElementById('bPlanilla').textContent = `S/ ${planilla.toFixed(2)}`;
            const utilidad = ingMesValue - gFijos - gVars - planilla;
            const elUtil = document.getElementById('bUtilidad');
            elUtil.textContent = `S/ ${utilidad.toFixed(2)}`;
            elUtil.classList.remove('text-success', 'text-danger');
            elUtil.classList.add(utilidad >= 0 ? 'text-success' : 'text-danger');

            if (typeof ApexCharts !== 'undefined') {
                // Chart: Ingresos vs Gastos
                const meses = d.ingresos_meses || [];
                const gastosMeses = d.gastos_meses || [];
                const labels = meses.map(m => m.mes);
                const ingData = meses.map(m => parseFloat(m.total));
                const gastosMap = {};
                gastosMeses.forEach(g => {
                    gastosMap[g.mes] = (gastosMap[g.mes] || 0) + parseFloat(g.total);
                });
                const gasData = labels.map(l => gastosMap[l] || 0);

                new ApexCharts(document.getElementById('chartIngresosGastos'), {
                    series: [{
                        name: 'Ingresos',
                        data: ingData
                    }, {
                        name: 'Gastos',
                        data: gasData
                    }],
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Public Sans'
                    },
                    colors: ['#696cff', '#ff3e1d'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            opacityFrom: 0.3,
                            opacityTo: 0.05
                        }
                    },
                    xaxis: {
                        categories: labels
                    },
                    yaxis: {
                        labels: {
                            formatter: v => 'S/ ' + v.toFixed(0)
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: v => 'S/ ' + v.toFixed(2)
                        }
                    }
                }).render();

                // Chart: Gastos Pie
                new ApexCharts(document.getElementById('chartGastosPie'), {
                    series: [gFijos, gVars, planilla],
                    chart: {
                        type: 'donut',
                        height: 280
                    },
                    labels: ['Fijos', 'Variables', 'Planilla'],
                    colors: ['#ff3e1d', '#ffab00', '#03c3ec'],
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Gasto Total',
                                        formatter: () => `S/ ${(gFijos + gVars + planilla).toFixed(0) }`
                                    }
                                }
                            }
                        }
                    }
                }).render();

                // Chart: Métodos de pago
                const metodos = d.metodos_pago || [];
                new ApexCharts(document.getElementById('chartMetodos'), {
                    series: metodos.map(m => parseFloat(m.total)),
                    chart: {
                        type: 'donut',
                        height: 200
                    },
                    labels: metodos.map(m => m.metodo_pago),
                    colors: ['#71dd37', '#696cff', '#03c3ec', '#ffab00'],
                    legend: {
                        position: 'bottom'
                    }
                }).render();
            }
        } catch (e) {
            console.error('Error cargando finanzas:', e);
        }
    }

    function initTables() {
        const lang = {
            lengthMenu: " Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_",
            zeroRecords: "Sin registros found",
            infoEmpty: "Sin registros",
            paginate: {
                next: "Sig",
                previous: "Ant"
            }
        };

        $('#tbGastos').DataTable({
            language: lang,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                targets: [0],
                visible: false
            }]
        });

        $('#tbInsumos').DataTable({
            language: lang,
            order: [
                [0, 'desc']
            ],
            columnDefs: [{
                targets: [0],
                visible: false
            }]
        });
    }

    function mostrarToast(msg, tipo) {
        let toastEl = document.getElementById('toastSistema');
        if (!toastEl) return;
        toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
        document.getElementById('toastMensaje').textContent = msg;
        new bootstrap.Toast(toastEl).show();
    }

    $(document).on('submit', '#formRegistrarGasto', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Guardando...');
        $.post(`${BASE_URL_FIN}/admin/finanzas/registrargasto`, $(this).serialize(), function(res) {
            if (res.success) {
                mostrarToast(res.message, "success");
                setTimeout(() => window.location.reload(), 1500);
            } else {
                mostrarToast(res.message, "danger");
                btn.prop('disabled', false).text('Guardar Gasto');
            }
        }, 'json').fail(() => {
            mostrarToast('Error de comunicación.', 'danger');
            btn.prop('disabled', false).text('Guardar Gasto');
        });
    });

    $(document).on('submit', '#formRegistrarInsumo', function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).text('Guardando...');
        $.post(`${BASE_URL_FIN}/admin/finanzas/registrarinsumo`, $(this).serialize(), function(res) {
            if (res.success) {
                mostrarToast(res.message, "success");
                setTimeout(() => window.location.reload(), 1500);
            } else {
                mostrarToast(res.message, "danger");
                btn.prop('disabled', false).text('Guardar Insumo');
            }
        }, 'json').fail(() => {
            mostrarToast('Error de comunicación.', 'danger');
            btn.prop('disabled', false).text('Guardar Insumo');
        });
    });

    function editarInsumo(id, nombre, um, costo, stock) {
        $('#insumo_id').val(id);
        $('#insumo_nombre').val(nombre);
        $('#insumo_um').val(um);
        $('#insumo_costo').val(costo);
        $('#insumo_stock').val(stock);
        new bootstrap.Modal(document.getElementById('modalRegistrarInsumo')).show();
    }
</script>
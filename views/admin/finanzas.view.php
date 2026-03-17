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

<div class="content-wrapper" data-base-url="<?= BASE_URL ?>">
    <div class="container-fluid flex-grow-1 container-p-y">
        <!-- ═══ HEADER & FILTERS (Personal Style) ═══ -->
        <div class="col-lg-12 mb-4">
            <div class="m-1">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    <nav aria-label="breadcrumb" class="me-auto">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li>
                            <li class="breadcrumb-item active text-primary">Finanzas</li>
                        </ol>
                        <h4 class="fw-bold mb-0">Gestión de Finanzas</h4>
                    </nav>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <div class="input-group input-group-merge shadow-none border rounded" style="width: 260px;">
                            <span class="input-group-text bg-white border-0"><i class="bx bx-search text-muted"></i></span>
                            <input type="text" id="buscadorGlobal" class="form-control border-0 ps-0 bg-white" placeholder="Buscar en tablas...">
                        </div>

                        <!-- Selector Periodo -->
                        <div class="input-group input-group-merge shadow-none border rounded" style="width: 220px;">
                            <select id="filterMonth" class="form-select border-0 bg-white" onchange="cargarFinanzas()">
                                <?php
                                $nombres_meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                for($m=1; $m<=12; $m++):
                                    $sel = ($m == date('n')) ? 'selected' : '';
                                    echo "<option value='$m' $sel>{$nombres_meses[$m-1]}</option>";
                                endfor;
                                ?>
                            </select>
                            <select id="filterYear" class="form-select border-0 border-start px-2 bg-white" style="max-width: 85px;" onchange="cargarFinanzas()">
                                <?php
                                for($y=2024; $y<=date('Y')+1; $y++):
                                    $sel = ($y == date('Y')) ? 'selected' : '';
                                    echo "<option value='$y' $sel>$y</option>";
                                endfor;
                                ?>
                            </select>
                        </div>

                        <div class="d-flex gap-1 border-start ps-2">
                            <button type="button" class="btn btn-primary shadow-sm" onclick="prepararNuevoGasto()" data-bs-toggle="modal" data-bs-target="#modalRegistrarGasto" title="Nuevo Gasto">
                                <i class="bx bx-plus me-1"></i> Gasto
                            </button>
                            <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRegistrarInsumo" title="Nuevo Insumo">
                                <i class="bx bx-box me-1"></i> Insumo
                            </button>
                            <button type="button" class="btn btn-success shadow-sm" onclick="exportarGastos()" title="Exportar CSV">
                                <i class="bx bx-download"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="monthBadgeStatus" class="mt-2 text-end"></div>
            </div>
        </div>
  <!-- ═══ SECCIÓN DE TABLAS (OCULTA POR DEFECTO PARA ENFOQUE) ═══ -->
        <div class="accordion mb-4 border-0 shadow-none" id="accordionTables">
            <div class="accordion-item border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTables">
                        <i class="bx bx-table me-2 text-primary"></i> Ver Detalle de Movimientos e Inventario
                    </button>
                </h2>
                <div id="collapseTables" class="accordion-collapse collapse" data-bs-parent="#accordionTables">
                    <div class="accordion-body p-4" style="background-color: #faf9ffff; border-bottom: 1px solid #1b1fffff;">
                        <div class="row">
                            <!-- ═══ HISTORIAL DE GASTOS ═══ -->
                            <div class="col-xl-7 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bx bx-list-ul text-primary me-1"></i> Historial de Gastos</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover w-100 bg-white shadow-sm rounded-3" id="tbGastos">
                                        <thead class="bg-primary">
                                            <tr>
                                                <th class="d-none">ID</th>
                                                <th class="text-white">Fecha</th>
                                                <th class="text-white">Descripción</th>
                                                <th class="text-white">Tipo</th>
                                                <th class="text-white">Monto</th>
                                                <th class="text-white">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyGastos">
                                            <!-- Cargado por AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- ═══ INVENTARIO DE INSUMOS ═══ -->
                            <div class="col-xl-5 mb-4 border-start">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold mb-0"><i class="bx bx-box text-warning me-1"></i> Control de Insumos</h6>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover w-100 bg-white shadow-sm rounded-3" id="tbInsumos">
                                        <thead class="bg-warning">
                                            <tr>
                                                <th class="d-none">ID</th>
                                                <th class="text-white">Insumo</th>
                                                <th class="text-white">Stock</th>
                                                <th class="text-white">Costo</th>
                                                <th class="text-white">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (isset($lista_insumos)): foreach ($lista_insumos as $ins): ?>
                                                <tr>
                                                    <td class="d-none"><?= $ins['id_insumo'] ?></td>
                                                    <td class="small fw-bold"><?= htmlspecialchars($ins['nombre']) ?></td>
                                                    <td>
                                                        <span class="badge bg-label-<?= $ins['stock_actual'] <= 5 ? 'danger' : 'success' ?>">
                                                            <?= $ins['stock_actual'] ?> <?= $ins['unidad_medida'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="small">S/ <?= number_format($ins['costo_unitario'], 2) ?></td>
                                                    <td class="text-end">
                                                        <div class="d-flex gap-1 justify-content-end">
                                                            <?php 
                                                                $js_name = htmlspecialchars(addslashes($ins['nombre']), ENT_QUOTES, 'UTF-8');
                                                                $js_um = htmlspecialchars(addslashes($ins['unidad_medida']), ENT_QUOTES, 'UTF-8');
                                                            ?>
                                                            <button class="btn btn-sm btn-icon btn-label-warning" onclick='editarInsumo(<?= $ins["id_insumo"] ?>, "<?= $js_name ?>", "<?= $js_um ?>", <?= $ins["costo_unitario"] ?>, <?= $ins["stock_actual"] ?>)' title="Editar"><i class="bx bx-edit"></i></button>
                                                            <button class="btn btn-sm btn-icon btn-label-danger" onclick="eliminarInsumo(<?= $ins['id_insumo'] ?>)" title="Eliminar"><i class="bx bx-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ═══ BLOQUE 1: RESUMEN Y PUNTO DE EQUILIBRIO ═══ -->
        <div class="row mb-4">
            <!-- 1. Balance del Periodo (Explicado) -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm h-100 border-0 overflow-hidden" style="border-radius:14px;">
                    <div class="bg-primary p-3 text-white">
                        <h6 class="fw-bold mb-0 text-white"><i class="bx bx-wallet me-2"></i>1. Balance de Caja</h6>
                        <small class="opacity-75">Resumen de lo que entró y salió.</small>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" title="Total de dinero recibido por servicios">Ingresos Brutos <i class="bx bx-info-circle small"></i></span>
                            <span class="fw-bold text-success" id="bIngresos">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" title="Gastos que no cambian (Alquiler, Luz, Internet)">Gastos Fijos <i class="bx bx-info-circle small"></i></span>
                            <span class="fw-bold text-danger" id="bGFijos">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted" title="Gastos por uso (Insumos, reparaciones)">Gastos Variables <i class="bx bx-info-circle small"></i></span>
                            <span class="fw-bold text-danger" id="bGVariables">S/ 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-2 border-bottom">
                            <span class="text-muted" title="Sueldos pagados a tu personal">Pago Personal <i class="bx bx-info-circle small"></i></span>
                            <span class="fw-bold text-danger" id="bPlanilla">S/ 0</span>
                        </div>
                        
                        <div class="p-3 rounded-3 text-center mb-0" id="balanceUtilityContainer" style="background: rgba(105, 108, 255, 0.05);">
                            <div class="small text-muted mb-1 fw-bold">UTILIDAD (GANANCIA REAL)</div>
                            <div class="fw-bold fs-4" id="bUtilidad">S/ 0.00</div>
                            <p class="small text-muted mb-0 mt-1" style="font-size: 0.7rem;">Esto es lo que queda libre para ti después de pagar todo.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Punto de Equilibrio (Nueva Sección Explicada) -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm h-100 border-0 overflow-hidden" style="border-radius:14px;">
                    <div class="bg-warning p-3 text-dark">
                        <h6 class="fw-bold mb-0"><i class="bx bx-target-lock me-2"></i>2. Punto de Equilibrio</h6>
                        <small class="text-dark opacity-75">¿Cuánto necesito para no perder dinero?</small>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row text-center mb-3">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Meta Mensual</small>
                                <div class="fw-bold fs-4 text-warning" id="puntoEq">S/ 0.00</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Meta Diaria</small>
                                <div class="fw-bold fs-4 text-dark" id="puntoEqDiario">S/ 0.00</div>
                            </div>
                        </div>
                        <p class="small text-muted text-center mb-3">Este es el monto mínimo que debes generar para cubrir costos y planilla.</p>
                        
                        <div class="bg-white p-3 rounded-3 border shadow-none mt-auto">
                            <div class="progress mb-2" style="height: 10px; background: #f0f0f0;">
                                <div id="peProgress" class="progress-bar bg-warning" style="width: 0%"></div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="fw-bold" id="pePercentText">0% Cubierto</span>
                                <span class="text-muted" id="peStatusText">En proceso...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Análisis de Metas (Simulador) -->
            <div class="col-lg-4 mb-3">
                <div class="card shadow-sm h-100 border-0 overflow-hidden" style="border-radius:14px;">
                    <div class="bg-info p-3 text-white">
                        <h6 class="fw-bold mb-0 text-white"><i class="bx bx-bullseye me-2"></i>3. Análisis de Metas</h6>
                        <small class="opacity-75">Simulación y proyección de crecimiento.</small>
                    </div>
                    <div class="card-body">
                        <div id="projectionAlert" class="mb-3">
                            <p class="small mb-2" id="projectionText">Analizando el rendimiento del mes...</p>
                            <div class="fw-bold fs-4 text-info" id="projectionValue">S/ 0.00</div>
                        </div>
                        <div class="progress mt-2" style="height: 6px;">
                            <div id="projectionProgress" class="progress-bar bg-info" style="width: 0%"></div>
                        </div>
                        <div class="mt-3 p-2 bg-label-info rounded small" id="metaHint">
                            <i class="bx bx-bulb me-1"></i> <strong>Tip:</strong> Intenta que tus ingresos sean al menos 50% mayores que tus egresos para un crecimiento saludable.
                        </div>
                        <small class="d-block mt-2 text-muted text-center fw-bold" id="projectionStatus">Estado: Cargando...</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ BLOQUE 2: DISTRIBUCIÓN Y TENDENCIAS ═══ -->
        <div class="row mb-4">
            <div class="col-lg-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius:14px">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-secondary"><i class="bx bx-credit-card me-1"></i>Distribución de Pagos</h6>
                            <i class="bx bx-question-mark border rounded-circle p-1 text-muted" title="Muestra qué métodos de pago prefieren tus clientes." data-bs-toggle="tooltip"></i>
                        </div>
                        <div id="chartMetodos" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-3">
                <div class="card shadow-sm border-0 h-100" style="border-radius:14px">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h6 class="fw-bold mb-0 text-secondary"><i class="bx bx-pie-chart-alt me-1"></i>Desglose de Egresos</h6>
                            <i class="bx bx-question-mark border rounded-circle p-1 text-muted" title="Divide tus gastos en Fijos, Variables y Sueldos para ver dónde gastas más." data-bs-toggle="tooltip"></i>
                        </div>
                        <div id="chartGastosPie" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0" style="border-radius:14px">
                    <div class="card-header border-0 bg-transparent pt-4 px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-line-chart text-primary me-1"></i>Evolución Mensual (Ingresos vs Gastos)</h6>
                            <span class="badge bg-label-secondary small">Últimos 6 meses</span>
                        </div>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div id="chartIngresosGastos" style="min-height:350px"></div>
                    </div>
                </div>
            </div>
        </div>

      

    </div><!-- /container-fluid -->
    <div class="content-backdrop fade"></div>
</div><!-- /content-wrapper -->

<?php require VIEW_PATH . '/partials/finanzas/modals.php'; ?>
<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<style>
    .dataTables_filter { display: block !important; margin-bottom: 1rem; }
    .dataTables_length { display: block !important; padding-bottom: 0.5rem; }
    .dataTables_length select { border-radius: 6px; border: 1px solid #eee; padding: 2px 5px; }
    .dataTables_paginate { display: flex !important; justify-content: flex-end !important; margin-top: 1rem !important; }
    .dataTables_info { color: #b0b0b0 !important; font-size: 0.8rem; margin-top: 1rem !important; }
    .table-responsive { overflow-x: hidden !important; }
    @media (max-width: 768px) { .table-responsive { overflow-x: auto !important; } }
</style>

<script src="<?= BASE_URL ?>/public/js/admin/finanzas.js"></script>
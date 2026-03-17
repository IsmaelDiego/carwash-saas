let BASE_URL_FIN = "";
let chartIG, chartMet, chartGas;
let tableGastos;

document.addEventListener('DOMContentLoaded', () => {
    BASE_URL_FIN = document.querySelector('.content-wrapper').dataset.baseUrl;
    cargarFinanzas();
    initTables();
    restringirRangoFechas();
});

async function cargarFinanzas() {
    const selMonth = parseInt(document.getElementById('filterMonth').value);
    const selYear = parseInt(document.getElementById('filterYear').value);
    
    // 🏷️ Status Badge
    actualizarBadgeStatus(selMonth, selYear);

    try {
        const res = await fetch(`${BASE_URL_FIN}/admin/finanzas/getresumen?month=${selMonth}&year=${selYear}`);
        const json = await res.json();
        if (!json.success) return;
        const d = json.data;

        // --- DASHBOARD CARDS ---
        const ing = d.ingresos || {};
        const gas = d.gastos || {};
        const planilla = parseFloat(d.planilla) || 0;
        const totalIng = parseFloat(ing.ingresos_mes) || 0;
        const gFijos = parseFloat(gas.gastos_fijos) || 0;
        const gVars = parseFloat(gas.gastos_variables) || 0;
        const totalEgresos = gFijos + gVars + planilla;
        const utilidad = totalIng - totalEgresos;

        document.getElementById('bIngresos').textContent = `S/ ${totalIng.toFixed(2)}`;
        document.getElementById('bGFijos').textContent = `S/ ${gFijos.toFixed(2)}`;
        document.getElementById('bGVariables').textContent = `S/ ${gVars.toFixed(2)}`;
        document.getElementById('bPlanilla').textContent = `S/ ${planilla.toFixed(2)}`;
        
        const elUtil = document.getElementById('bUtilidad');
        elUtil.textContent = `S/ ${utilidad.toFixed(2)}`;
        
        const utilBox = document.getElementById('balanceUtilityContainer');
        const utilMsg = utilBox.querySelector('p');
        if (utilidad >= 0) {
            utilBox.style.background = 'rgba(40, 167, 69, 0.08)';
            elUtil.className = 'fw-bold fs-4 text-success';
            utilMsg.innerHTML = '<i class="bx bx-trending-up me-1"></i> ¡Excelente! Estás operando con ganancias.';
        } else {
            utilBox.style.background = 'rgba(220, 53, 69, 0.08)';
            elUtil.className = 'fw-bold fs-4 text-danger';
            utilMsg.innerHTML = '<i class="bx bx-trending-down me-1"></i> Alerta: Los gastos superan los ingresos.';
        }

        // --- PUNTO EQUILIBRIO ---
        const pe = parseFloat(d.punto_equilibrio) || 0;
        const daysInMonth = new Date(selYear, selMonth, 0).getDate();
        document.getElementById('puntoEq').textContent = `S/ ${pe.toFixed(2)}`;
        document.getElementById('puntoEqDiario').textContent = `S/ ${(pe / daysInMonth).toFixed(2)}`;
        
        const pePercent = (pe > 0) ? Math.min(100, (totalIng / pe * 100)).toFixed(0) : 0;
        document.getElementById('peProgress').style.width = `${pePercent}%`;
        document.getElementById('pePercentText').textContent = `${pePercent}% Cubierto`;
        
        const peStatusText = document.getElementById('peStatusText');
        peStatusText.innerHTML = pePercent >= 100 ? 
            '<span class="text-success fw-bold"><i class="bx bxs-check-circle"></i> META CUMPLIDA</span>' : 
            `<span class="text-muted">Faltan S/ ${(pe - totalIng).toFixed(0)}</span>`;

        // --- PROYECCIONES ---
        actualizarProyeccion(totalIng, totalEgresos);

        // --- TABLA DE GASTOS ---
        cargarGastosTabla(selMonth, selYear);

        // --- CHARTS ---
        renderCharts(d, gFijos, gVars, planilla, totalEgresos);

    } catch (e) { console.error('Error:', e); }
}

async function cargarGastosTabla(month, year) {
    try {
        const res = await fetch(`${BASE_URL_FIN}/admin/finanzas/getgastosperiodo?month=${month}&year=${year}`);
        const json = await res.json();
        if (!json.success) return;

        tableGastos.clear();
        json.data.forEach(g => {
            const badgeClass = g.tipo_gasto === 'FIJO' ? 'danger' : 'warning';
            tableGastos.row.add([
                g.id_gasto,
                new Date(g.fecha_gasto).toLocaleDateString('es-ES'),
                g.descripcion,
                `<span class="badge bg-label-${badgeClass}">${g.tipo_gasto}</span>`,
                `<span class="fw-bold text-dark">S/ ${parseFloat(g.monto).toFixed(2)}</span>`,
                `<div class="d-flex gap-1">
                    <button class="btn btn-sm btn-icon btn-label-primary" onclick="editarGasto(${g.id_gasto})" title="Editar"><i class="bx bx-edit"></i></button>
                    <button class="btn btn-sm btn-icon btn-label-danger" onclick="eliminarGasto(${g.id_gasto})" title="Eliminar"><i class="bx bx-trash"></i></button>
                </div>`
            ]);
        });
        tableGastos.draw();
    } catch (e) { console.error(e); }
}

async function restringirRangoFechas() {
    try {
        const res = await fetch(`${BASE_URL_FIN}/admin/finanzas/getrangofinanzas`);
        const json = await res.json();
        if(!json.success) return;

        const [minYear, minMonth] = json.min.split('-').map(Number);
        const [maxYear, maxMonth] = json.max.split('-').map(Number);

        const filterMonth = document.getElementById('filterMonth');
        const filterYear = document.getElementById('filterYear');

        const validarRango = () => {
            const y = parseInt(filterYear.value);
            const m = parseInt(filterMonth.value);
            
            // Si es mayor al máximo registro (proyección futura permitida)
            // Si es menor al mínimo registro, advertir
            if (y < minYear || (y === minYear && m < minMonth)) {
                mostrarToast("Este mes no tiene registros históricos.", "warning");
            }
        };

        filterMonth.addEventListener('change', validarRango);
        filterYear.addEventListener('change', validarRango);

    } catch (e) {}
}

function actualizarBadgeStatus(selMonth, selYear) {
    const ahora = new Date();
    const curMonth = ahora.getMonth() + 1;
    const curYear = ahora.getFullYear();
    let label = "";

    if (selYear > curYear || (selYear === curYear && selMonth > curMonth)) {
        label = '<span class="badge bg-label-info animate__animated animate__fadeIn"><i class="bx bx-calendar-star me-1"></i> PROYECCIÓN FUTURA</span>';
    } else if (selYear === curYear && selMonth === curMonth) {
        label = '<span class="badge bg-label-success animate__animated animate__pulse animate__infinite"><i class="bx bx-calendar-check me-1"></i> MES EN CURSO</span>';
    } else {
        label = '<span class="badge bg-label-secondary animate__animated animate__fadeIn"><i class="bx bx-history me-1"></i> REGISTRO PASADO</span>';
    }
    document.getElementById('monthBadgeStatus').innerHTML = label;
}

function actualizarProyeccion(ingMesValue, totalEgresos) {
    const projectionValue = document.getElementById('projectionValue');
    const projectionText = document.getElementById('projectionText');
    const projectionProgress = document.getElementById('projectionProgress');
    const projectionStatus = document.getElementById('projectionStatus');

    if (ingMesValue < totalEgresos) {
        const faltante = totalEgresos - ingMesValue;
        projectionText.textContent = "Falta facturar para cubrir el total de costos:";
        projectionValue.textContent = `S/ ${faltante.toFixed(2)}`;
        projectionValue.className = "fw-bold fs-4 text-danger";
        const ratio = Math.min(100, (ingMesValue / totalEgresos * 100));
        projectionProgress.style.width = `${ratio}%`;
        projectionProgress.className = "progress-bar bg-danger";
        projectionStatus.innerHTML = `<span class="badge bg-label-danger w-100 p-2">SITUACIÓN: DÉFICIT OPERATIVO</span>`;
    } else {
        const metaGanancia = totalEgresos * 1.5;
        if (ingMesValue >= metaGanancia) {
            projectionText.textContent = "Has superado la meta de rentabilidad ideal.";
            projectionValue.textContent = "ESTADO EXCELENTE";
            projectionValue.className = "fw-bold fs-4 text-success";
            projectionProgress.style.width = "100%";
            projectionProgress.className = "progress-bar bg-success";
            projectionStatus.innerHTML = `<span class="badge bg-label-success w-100 p-2">SITUACIÓN: RENTABLE</span>`;
        } else {
            const paraMeta = metaGanancia - ingMesValue;
            projectionText.textContent = "Cubriste costos. Para la meta de ganancia (50%) falta:";
            projectionValue.textContent = `S/ ${paraMeta.toFixed(2)}`;
            projectionValue.className = "fw-bold fs-4 text-warning";
            const ratio = Math.min(100, (ingMesValue / metaGanancia * 100));
            projectionProgress.style.width = `${ratio}%`;
            projectionProgress.className = "progress-bar bg-warning";
            projectionStatus.innerHTML = `<span class="badge bg-label-warning w-100 p-2">SITUACIÓN: EQUILIBRIO</span>`;
        }
    }
}

function initTables() {
    const lang = {
        lengthMenu: " _MENU_ ", search: "Buscar:",
        info: "Mostrando _START_ a _END_ de _TOTAL_",
        zeroRecords: "Sin registros en este mes",
        infoEmpty: "Sin registros",
        paginate: { next: '<i class="bx bx-chevron-right"></i>', previous: '<i class="bx bx-chevron-left"></i>' }
    };

    tableGastos = $('#tbGastos').DataTable({
        language: lang,
        order: [[0, 'desc']],
        lengthMenu: [10, 20, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mb-2"l>t<"row mx-0 mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        columnDefs: [{ targets: [0], visible: false }]
    });

    tableInsumos = $('#tbInsumos').DataTable({
        language: lang,
        order: [[0, 'desc']],
        lengthMenu: [10, 20, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mb-2"l>t<"row mx-0 mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        columnDefs: [{ targets: [0], visible: false }]
    });

    // Global Search linking
    document.getElementById('buscadorGlobal').addEventListener('keyup', function() {
        tableGastos.search(this.value).draw();
        tableInsumos.search(this.value).draw();
    });
}

function renderCharts(d, gFijos, gVars, planilla, totalEgresos) {
    if (typeof ApexCharts === 'undefined') return;

    // --- CHART TENDENCIA ---
    const meses = d.ingresos_meses || [];
    const gastosMeses = d.gastos_meses || [];
    const labels = meses.map(m => m.mes);
    const ingData = meses.map(m => parseFloat(m.total));
    const gastosMap = {};
    gastosMeses.forEach(g => { gastosMap[g.mes] = (gastosMap[g.mes] || 0) + parseFloat(g.total); });
    const gasData = labels.map(l => gastosMap[l] || 0);

    if (chartIG) chartIG.destroy();
    chartIG = new ApexCharts(document.getElementById('chartIngresosGastos'), {
        series: [{ name: 'Ingresos', data: ingData }, { name: 'Gastos', data: gasData }],
        chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
        colors: ['#696cff', '#ff3e1d'],
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.1 } },
        xaxis: { categories: labels },
        yaxis: { labels: { formatter: v => 'S/ ' + v.toFixed(0) } }
    });
    chartIG.render();

    // --- CHART GASTOS PIE ---
    if (chartGas) chartGas.destroy();
    chartGas = new ApexCharts(document.getElementById('chartGastosPie'), {
        series: [gFijos, gVars, planilla],
        chart: { type: 'donut', height: 250 },
        labels: ['Fijos', 'Variables', 'Planilla'],
        colors: ['#ff3e1d', '#ffab00', '#03c3ec'],
        plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Egresos', formatter: () => `S/ ${totalEgresos.toFixed(0)}` } } } } }
    });
    chartGas.render();

    // --- CHART METODOS ---
    if (chartMet) chartMet.destroy();
    const metodos = d.metodos_pago || [];
    chartMet = new ApexCharts(document.getElementById('chartMetodos'), {
        series: metodos.map(m => parseFloat(m.total)),
        chart: { type: 'donut', height: 250 },
        labels: metodos.map(m => m.metodo_pago),
        colors: ['#71dd37', '#696cff', '#03c3ec', '#ffab00'],
        plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Cobrado' } } } } }
    });
    chartMet.render();
}

function exportarGastos() {
    const selMonth = document.getElementById('filterMonth').value;
    const selYear = document.getElementById('filterYear').value;
    window.location.href = `${BASE_URL_FIN}/admin/finanzas/exportargastos?month=${selMonth}&year=${selYear}`;
}

async function editarGasto(id) {
    try {
        const res = await fetch(`${BASE_URL_FIN}/admin/finanzas/obtenergasto?id=${id}`);
        const json = await res.json();
        if(!json.success) return;

        const g = json.data;
        document.getElementById('gasto_id').value = g.id_gasto;
        document.getElementById('gasto_descripcion').value = g.descripcion;
        document.getElementById('gasto_tipo').value = g.tipo_gasto;
        document.getElementById('gasto_monto').value = g.monto;
        document.getElementById('gasto_fecha').value = g.fecha_gasto;
        document.getElementById('gasto_insumo').value = g.id_insumo_origen || "";
        
        document.getElementById('gastoModalTitle').textContent = "Editar Gasto";
        new bootstrap.Modal(document.getElementById('modalRegistrarGasto')).show();
    } catch (e) {}
}

function eliminarGasto(id) {
    if(!confirm("¿Estás seguro de eliminar este registro de gasto?")) return;
    $.post(`${BASE_URL_FIN}/admin/finanzas/eliminargasto`, { id: id }, function(res) {
        if(res.success) {
            mostrarToast(res.message, "success");
            cargarFinanzas();
        } else {
            mostrarToast(res.message, "danger");
        }
    }, 'json');
}

function eliminarInsumo(id) {
    if(!confirm("¿Estás seguro de eliminar este insumo?")) return;
    $.post(`${BASE_URL_FIN}/admin/finanzas/eliminarinsumo`, { id: id }, function(res) {
        if(res.success) {
            mostrarToast(res.message, "success");
            window.location.reload();
        } else {
            mostrarToast(res.message, "danger");
        }
    }, 'json');
}

function prepararNuevoGasto() {
    document.getElementById('formRegistrarGasto').reset();
    document.getElementById('gasto_id').value = "";
    document.getElementById('gastoModalTitle').textContent = "Registrar Nuevo Gasto";
}

function mostrarToast(msg, tipo) {
    let toastEl = document.getElementById('toastSistema');
    if (!toastEl) return;
    toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    document.getElementById('toastMensaje').textContent = msg;
    new bootstrap.Toast(toastEl).show();
}

// Event Handlers para Forms
$(document).on('submit', '#formRegistrarGasto', function(e) {
    e.preventDefault();
    let btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');
    $.post(`${BASE_URL_FIN}/admin/finanzas/registrargasto`, $(this).serialize(), function(res) {
        if (res.success) {
            mostrarToast(res.message, "success");
            bootstrap.Modal.getInstance(document.getElementById('modalRegistrarGasto')).hide();
            cargarFinanzas();
        } else {
            mostrarToast(res.message, "danger");
        }
        btn.prop('disabled', false).text('Guardar Registro');
    }, 'json');
});

$(document).on('submit', '#formRegistrarInsumo', function(e) {
    e.preventDefault();
    let btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Guardando...');
    $.post(`${BASE_URL_FIN}/admin/finanzas/registrarinsumo`, $(this).serialize(), function(res) {
        if (res.success) {
            mostrarToast(res.message, "success");
            bootstrap.Modal.getInstance(document.getElementById('modalRegistrarInsumo')).hide();
            window.location.reload(); // Insumos cargan por PHP, mejor reload
        } else {
            mostrarToast(res.message, "danger");
        }
        btn.prop('disabled', false).text('Guardar Insumo');
    }, 'json');
});

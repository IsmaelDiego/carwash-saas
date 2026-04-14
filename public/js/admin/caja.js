document.addEventListener('DOMContentLoaded', function() {
    cargarArqueos();
});

const BASE_URL = document.querySelector('.content-wrapper').dataset.baseUrl || '';

async function cargarArqueos() {
    const m = document.getElementById('filterMonth').value;
    const y = document.getElementById('filterYear').value;

    const res = await fetch(`${BASE_URL}/admin/caja/getarqueos?month=${m}&year=${y}`);
    const data = await res.json();

    if (data.success) {
        renderTable(data.data);
        updateStats(data.data);
    } else {
        console.error("Error al cargar arqueos:", data.message);
    }
}

function updateStats(arqueos) {
    const total = arqueos.length;
    let cuadrados = 0, sobrantes = 0, faltantes = 0;

    arqueos.forEach(cs => {
        if (cs.estado === 'CERRADA') {
            const diff = parseFloat(cs.diferencia || 0);
            if (diff === 0) cuadrados++;
            else if (diff > 0) sobrantes++;
            else faltantes++;
        }
    });

    const elTotal = document.getElementById('stat_arq_total');
    const elCuad = document.getElementById('stat_arq_cuadrados');
    const elSob = document.getElementById('stat_arq_sobrantes');
    const elFalt = document.getElementById('stat_arq_faltantes');

    if (elTotal) elTotal.textContent = total;
    if (elCuad) elCuad.textContent = cuadrados;
    if (elSob) elSob.textContent = sobrantes;
    if (elFalt) elFalt.textContent = faltantes;
}

function renderTable(arqueos) {
    if (typeof $ !== 'undefined' && $.fn.DataTable.isDataTable('#tbArqueos')) {
        $('#tbArqueos').DataTable().destroy();
    }

    const tbody = document.getElementById('tbodyArqueos');
    tbody.innerHTML = '';

    if (arqueos.length > 0) {
        arqueos.forEach((cs, index) => {
            const numeroCorrelativo = index + 1;
            let montoEsperado = parseFloat(cs.monto_esperado || 0);
            let montoReal = cs.monto_cierre_real !== null ? parseFloat(cs.monto_cierre_real) : null;
            
            // Si está abierta, el esperado es monto_apertura + lo recaudado hasta ahora
            if (cs.estado === 'ABIERTA') {
                montoEsperado = parseFloat(cs.monto_apertura) + parseFloat(cs.recaudado_acumulado || 0);
            }

            const diff = cs.diferencia !== null ? parseFloat(cs.diferencia) : 0;
            const diffBadge = cs.estado === 'CERRADA' ? getDiffBadge(diff) : '<span class="text-muted" style="font-size: 0.85em;">Cierre pendiente</span>';
            const statusBadge = `<span class="badge bg-label-${cs.estado === 'ABIERTA' ? 'success' : 'secondary'}">${cs.estado}</span>`;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-bold text-center">${numeroCorrelativo}</td>
                <td>${formatFecha(cs.fecha_apertura)}</td>
                <td class="fw-semibold">${cs.cajero_nombre}</td>
                <td>S/ ${parseFloat(cs.monto_apertura).toFixed(2)}</td>
                <td class="fw-bold">S/ ${montoEsperado.toFixed(2)}</td>
                <td class="text-primary fw-bold">S/ ${montoReal !== null ? montoReal.toFixed(2) : '-'}</td>
                <td>${diffBadge}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-label-primary shadow-none" onclick="verDetalleSesion(${cs.id_sesion}, ${numeroCorrelativo})" title="Ver Detalle">
                        <i class="bx bx-show"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Inicializar DataTables
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#tbArqueos').DataTable({
            destroy: true,
            ordering: false,
            responsive: true,
            autoWidth: false,
            dom: '<"row mx-2"<"col-md-12 my-2"l>>t<"row mx-2"<"col-md-6"p><"col-md-6 text-end"i>>',
            language: {
                lengthMenu: " _MENU_ ",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "0 registros",
                infoFiltered: "(filtrado)",
                paginate: { next: "Siguiente", previous: "Anterior" },
                zeroRecords: `<div class="text-center p-5"><img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="80" class="mb-3 opacity-50"><h5 class="fw-bold text-muted">No hay cierres de caja en este periodo</h5></div>`
            }
        });

        // Vincular el buscador externo
        $('#buscadorArqueos').off('keyup').on('keyup', function() {
            $('#tbArqueos').DataTable().search(this.value).draw();
        });
    }
}

function formatFecha(fecha) {
    if (!fecha) return '-';
    // Formato simple: 27/03/26 13:45 
    const d = new Date(fecha.replace(/-/g, "/"));
    return d.toLocaleString('es-ES', { day: '2-digit', month: '2-digit', year: '2-digit', hour: '2-digit', minute: '2-digit' });
}

function getDiffBadge(diff) {
    if (diff === 0) return `<span class="badge bg-label-success">Cuadrado</span>`;
    if (diff > 0) return `<span class="badge bg-label-warning">Sobrante: S/ ${diff.toFixed(2)}</span>`;
    return `<span class="badge bg-label-danger">Faltante: S/ ${Math.abs(diff).toFixed(2)}</span>`;
}

async function verDetalleSesion(id, numeroCorrelativo) {
    const res = await fetch(`${BASE_URL}/admin/caja/detallesesion?id=${id}`);
    const data = await res.json();

    if (data.success) {
        const s = data.sesion;
        let montoEsperadoTotal = parseFloat(s.monto_esperado || 0);
        
        // Sumar métodos de pago para saber cuánto hay realmente recaudado hasta ahora
        let recaudadoRealTime = 0;
        data.metodos.forEach(m => { recaudadoRealTime += parseFloat(m.total || 0); });

        if (s.estado === 'ABIERTA') {
            montoEsperadoTotal = parseFloat(s.monto_apertura) + recaudadoRealTime;
        }

        let selectMes = document.getElementById('filterMonth');
        let nombreMes = selectMes.options[selectMes.selectedIndex].text;
        document.getElementById('detIdSesion').textContent = numeroCorrelativo + ' perteneciente al mes de ' + nombreMes;
        
        document.getElementById('detCajero').textContent = s.cajero;
        document.getElementById('detFechaApertura').textContent = formatFecha(s.fecha_apertura);
        document.getElementById('detFechaCierre').textContent = formatFecha(s.fecha_cierre);
        document.getElementById('detMontoApertura').textContent = 'S/ ' + parseFloat(s.monto_apertura).toFixed(2);
        document.getElementById('detMontoEsperado').textContent = 'S/ ' + montoEsperadoTotal.toFixed(2);
        document.getElementById('detMontoReal').textContent = 'S/ ' + (s.monto_cierre_real !== null ? parseFloat(s.monto_cierre_real).toFixed(2) : '-');
        
        const diff = s.diferencia !== null ? parseFloat(s.diferencia) : 0;
        const detDiff = document.getElementById('detDiferencia');
        
        if (s.estado === 'ABIERTA') {
            detDiff.textContent = 'En curso...';
            detDiff.className = 'fw-bold text-muted';
        } else {
            detDiff.textContent = 'S/ ' + diff.toFixed(2);
            detDiff.className = 'fw-bold ' + (diff === 0 ? 'text-success' : (diff > 0 ? 'text-warning' : 'text-danger'));
        }

        // Métodos de Pago
        const contMetodos = document.getElementById('detMetodosCont');
        contMetodos.innerHTML = '';
        if (data.metodos.length === 0) {
            contMetodos.innerHTML = '<div class="small text-muted py-2">No hubo pagos registrados.</div>';
        } else {
            data.metodos.forEach(m => {
                contMetodos.innerHTML += `
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 px-0">
                        <span class="small"><i class="bx bx-chevron-right me-1"></i> ${m.metodo_pago}</span>
                        <span class="small fw-bold">S/ ${parseFloat(m.total).toFixed(2)}</span>
                    </div>
                `;
            });
        }

        // Productos
        const contProds = document.getElementById('detProdsCont');
        contProds.innerHTML = '';
        if (data.productos.length === 0) {
            contProds.innerHTML = '<tr><td colspan="3" class="text-center small text-muted">Sin ventas de tienda</td></tr>';
        } else {
            data.productos.forEach(p => {
                contProds.innerHTML += `
                    <tr>
                        <td class="small">${p.nombre}</td>
                        <td class="text-center small">${p.total_cant}</td>
                        <td class="text-end small fw-bold">S/ ${parseFloat(p.total_monto).toFixed(2)}</td>
                    </tr>
                `;
            });
        }

        new bootstrap.Modal(document.getElementById('modalDetalleSesion')).show();
    }
}

function exportarArqueos() {
    const selMonth = document.getElementById('filterMonth').value;
    const selYear = document.getElementById('filterYear').value;
    window.location.href = `${BASE_URL}/admin/caja/exportararqueos?month=${selMonth}&year=${selYear}`;
}

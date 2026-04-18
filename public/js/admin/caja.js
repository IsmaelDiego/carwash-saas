document.addEventListener('DOMContentLoaded', function() {
    cargarArqueos();
    checkNewRequests();
    
    // Sincronización Total cada 1 segundo (SPA Mode)
    setInterval(() => {
        cargarArqueos();
        checkNewRequests();
    }, 1000);
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

    let hayCajaAbiertaActualmente = false;

    if (arqueos.length > 0) {
        arqueos.forEach((cs, index) => {
            if (cs.estado === 'ABIERTA') hayCajaAbiertaActualmente = true;

            const numeroCorrelativo = index + 1;
            let montoEsperado = parseFloat(cs.monto_esperado || 0);
            let montoReal = cs.monto_cierre_real !== null ? parseFloat(cs.monto_cierre_real) : null;
            
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

    // Actualizar el estado del botón principal automáticamente
    updateAperturaButtonUI(hayCajaAbiertaActualmente);

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
        document.getElementById('detRolApertura').textContent = s.rol_apertura_nombre || 'N/A';
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

// ═══ APERTURA REMOTA ADMIN ═══
function abrirModalAprobarCaja(id_solicitud, id_usuario, nombre) {
    document.getElementById('lblCajeroApertura').textContent = nombre;
    document.getElementById('aperturaIdSol').value = id_solicitud;
    document.getElementById('aperturaIdCajero').value = id_usuario;
    document.getElementById('montoAperturaAdmin').value = '0.00';
    new bootstrap.Modal(document.getElementById('modalAprobarCaja')).show();
}

async function confirmarApertura() {
    const idSol = document.getElementById('aperturaIdSol').value;
    const idCajero = document.getElementById('aperturaIdCajero').value;
    const monto = document.getElementById('montoAperturaAdmin').value;
    const btn = document.getElementById('btnConfirmarApertura');

    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Abriendo...';

    try {
        const res = await fetch(`${BASE_URL}/admin/caja/aprobar_solicitud`, {
            method: 'POST',
            body: JSON.stringify({
                id_solicitud: idSol,
                id_cajero: idCajero,
                monto_apertura: parseFloat(monto) || 0
            }),
            headers: {'Content-Type': 'application/json'}
        });
        const data = await res.json();
        
        if(data.success) {
            if(typeof mostrarToast !== 'undefined') mostrarToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalAprobarCaja')).hide();
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
            
            // Remover del panel (si la hay)
            if (idSol != 0) {
                const card = document.getElementById('solCard_' + idSol);
                if (card) card.remove();
                
                // Si no quedan solicitudes, ocultar el panel
                const pt = document.getElementById('listaPeticionesCaja');
                if (pt && pt.children.length === 0) {
                    const p = document.getElementById('panelSolicitudesCaja');
                    if (p) p.style.display = 'none';
                }
            }

            // Refrescar tabla visual y actualizar UI sin recargar
            cargarArqueos();
            updateAperturaButtonUI(true);
        } else {
            if(typeof mostrarToast !== 'undefined') mostrarToast(data.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
        }
    } catch(e) {
        if(typeof mostrarToast !== 'undefined') mostrarToast('Error de red al aperturar caja.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
    }
}

function abrirModalAperturaManual() {
    const meta = document.getElementById('configMetadata');
    const modoLibreGlobal = meta.dataset.modoLibre == 1;
    const opResponsable = meta.dataset.opResponsable;
    const select = document.getElementById('selCajeroManual');

    document.getElementById('montoAperturaManual').value = '0.00';
    
    // Filtrar según config global
    filterEmployeesByRole();

    if (modoLibreGlobal && opResponsable) {
        select.value = opResponsable;
        select.disabled = true; // Bloqueado porque ya está asignado en Ajustes
    } else {
        select.value = "";
        select.disabled = false;
    }

    new bootstrap.Modal(document.getElementById('modalAperturaManual')).show();
}

function filterEmployeesByRole() {
    const meta = document.getElementById('configMetadata');
    const isFreeMode = meta.dataset.modoLibre == 1;
    const select = document.getElementById('selCajeroManual');
    const options = select.querySelectorAll('option');

    options.forEach(opt => {
        if (opt.value === "") return; // Placeholder
        const rol = parseInt(opt.dataset.rol);
        
        if (isFreeMode) {
            // En modo libre se muestran todos, pero el select estará bloqueado en el responsable
            opt.hidden = false;
        } else {
            // Mostrar solo Cajeros (Rol 2)
            if (rol === 2) {
                opt.hidden = false;
            } else {
                opt.hidden = true;
                if (select.value === opt.value) select.value = "";
            }
        }
    });
}

// syncModoLibre ya no es necesaria desde aquí, se hace en Ajustes


async function confirmarAperturaManual() {
    const idCajero = document.getElementById('selCajeroManual').value;
    const monto = document.getElementById('montoAperturaManual').value;
    const btn = document.getElementById('btnConfirmarAperturaManual');

    if (!idCajero) {
        if(typeof mostrarToast !== 'undefined') mostrarToast('Seleccione un cajero u operario.', 'warning');
        else alert('Seleccione un cajero u operario.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-1"></i> Abriendo...';

    try {
        const res = await fetch(`${BASE_URL}/admin/caja/aprobar_solicitud`, {
            method: 'POST',
            body: JSON.stringify({
                id_solicitud: 0, // 0 = Sin petición previa (Manual)
                id_cajero: parseInt(idCajero),
                monto_apertura: parseFloat(monto) || 0
            }),
            headers: {'Content-Type': 'application/json'}
        });
        const data = await res.json();
        
        if(data.success) {
            if(typeof mostrarToast !== 'undefined') mostrarToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalAperturaManual')).hide();
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
            
            // Refrescar tabla de arqueos y actualizar UI sin recargar
            cargarArqueos();
            updateAperturaButtonUI(true);
        } else {
            if(typeof mostrarToast !== 'undefined') mostrarToast(data.message, 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
        }
    } catch(e) {
        if(typeof mostrarToast !== 'undefined') mostrarToast('Error de red al aperturar caja manual.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bx bx-check-double me-1"></i> Confirmar Apertura';
    }
}

function updateAperturaButtonUI(hayCajaAbierta) {
    const btn = document.getElementById('btnAperturaCajaPrincipal');
    if (!btn) return;

    if (hayCajaAbierta) {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        btn.disabled = true;
        btn.title = "Ya existe una caja abierta";
        btn.innerHTML = '<i class="bx bx-lock-alt"></i> <span class="d-none d-md-inline ms-1">Caja Aperturada</span>';
        btn.onclick = null;
    } else {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-primary');
        btn.disabled = false;
        btn.title = "Aperturar Caja";
        btn.onclick = abrirModalAperturaManual;
        btn.innerHTML = '<i class="bx bx-lock-open-alt"></i> <span class="d-none d-md-inline ms-1">Aperturar Caja</span>';
    }
}

async function checkNewRequests() {
    try {
        // Añadimos un timestamp para evitar el caché del navegador
        const res = await fetch(`${BASE_URL}/admin/caja/getpendingsolicitudes?t=${new Date().getTime()}`);
        const data = await res.json();

        if (data.success) {
            renderSolicitudes(data.data);
        }
    } catch (e) {
        console.error("Error monitoreando solicitudes:", e);
    }
}

function renderSolicitudes(solicitudes) {
    const panel = document.getElementById('panelSolicitudesCaja');
    const container = document.getElementById('listaPeticionesCaja');
    if (!panel || !container) return;

    if (!solicitudes || solicitudes.length === 0) {
        panel.style.display = 'none';
        container.innerHTML = '';
        return;
    }

    // Mostrar panel
    panel.style.display = 'block';

    // Limpiamos y redibujamos siempre para asegurar frescura total de datos
    container.innerHTML = '';
    solicitudes.forEach(sol => {
        container.innerHTML += `
            <div class="col-md-4 card-solicitud" id="solCard_${sol.id_solicitud}">
                <div class="card shadow-none border bg-white border-warning">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bx bx-user me-2 text-warning fs-4"></i>
                            <span class="fs-6 fw-bold">${sol.nombres}</span>
                        </div>
                        <span class="small text-muted mb-3"><i class="bx bx-time me-1"></i>${sol.fecha_solicitud}</span>
                        <button class="btn btn-sm btn-primary mt-auto" onclick="abrirModalAprobarCaja(${sol.id_solicitud}, ${sol.id_usuario}, '${sol.nombres}')">
                            <i class="bx bx-lock-open-alt me-1"></i> Aperturar Caja
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
}

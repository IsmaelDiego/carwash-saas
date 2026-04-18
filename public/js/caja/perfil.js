function mostrarToast(msg, tipo = 'success') {
    let toastEl = document.getElementById('toastSistema');
    if(!toastEl) { alert(msg); return; }
    toastEl.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    if(document.getElementById('toastMensaje')) document.getElementById('toastMensaje').innerText = msg;
    try { new bootstrap.Toast(toastEl).show(); } catch(e) { console.log(msg); }
}

function showTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-pills .nav-link').forEach(l => l.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.closest('.nav-link').classList.add('active');
}

async function enviarSolicitudPermiso(e) {
    e.preventDefault();
    const btn = document.getElementById('btnReqPerm');
    btn.disabled = true;
    btn.innerText = 'Enviando...';

    const payload = {
        tipo: document.getElementById('perm_tipo').value,
        desde: document.getElementById('perm_desde').value,
        hasta: document.getElementById('perm_hasta').value,
        motivo: document.getElementById('perm_motivo').value
    };

    if(payload.desde > payload.hasta) {
        mostrarToast("La fecha de fin no puede ser anterior a la de inicio.", "warning");
        btn.disabled = false; btn.innerText = 'Enviar Solicitud'; return;
    }

    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/solicitar_permiso`, {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.success) { 
            mostrarToast(data.message, "success");
            const mod = bootstrap.Modal.getInstance(document.getElementById('modalSolicitarPermiso'));
            if(mod) mod.hide();

            // Actualizar tabla sin refrescar
            if (window.dtPermisos) {
                const colors = { 'DESCANSO': 'info', 'PERMISO': 'warning', 'VACACION': 'success', 'FALTA': 'danger' };
                const tBadge = `<span class="badge bg-label-${colors[payload.tipo] || 'secondary'}">${payload.tipo}</span>`;
                const sBadge = `<span class="badge bg-warning">PENDIENTE</span>`;
                const fIni = payload.desde.split('-').reverse().join('/');
                const fFin = payload.hasta.split('-').reverse().join('/');
                
                window.dtPermisos.row.add([
                    tBadge,
                    fIni,
                    fFin,
                    `<span class="small">${payload.motivo}</span>`,
                    sBadge
                ]).draw(false);
            }

            e.target.reset();
        } else { 
            mostrarToast(data.message, "danger"); 
        }
    } catch(err) { mostrarToast('Error de red al procesar solicitud', 'danger'); }
    btn.disabled = false; btn.innerText = 'Enviar Solicitud';
}

async function enviarSolicitudAdelanto(e) {
    e.preventDefault();
    const btn = document.getElementById('btnReqAdelanto');
    btn.disabled = true;
    btn.innerText = 'Enviando...';

    const payload = {
        monto: document.getElementById('adelanto_monto').value,
        motivo: document.getElementById('adelanto_motivo').value
    };

    try {
        const res = await fetch(`${BASE_URL}/caja/dashboard/solicitar_adelanto`, {
            method: 'POST',
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if(data.success) { 
            mostrarToast(data.message, "success");
            const mod = bootstrap.Modal.getInstance(document.getElementById('modalSolicitarAdelanto'));
            if(mod) mod.hide();

            // Actualizar tabla sin refrescar
            if (window.dtPagos) {
                const tBadge = `<span class="badge bg-label-info">ADELANTO</span>`;
                const sBadge = `<span class="badge bg-warning">PENDIENTE</span>`;
                const today = new Date().toLocaleDateString('es-PE');
                
                window.dtPagos.row.add([
                    tBadge,
                    `<span class="fw-bold">S/ ${parseFloat(payload.monto).toFixed(2)}</span>`,
                    `<span class="small text-capitalize">Este Mes</span>`,
                    today,
                    sBadge
                ]).draw(false);
            }

            e.target.reset();
        } else { 
            mostrarToast(data.message, "danger"); 
        }
    } catch(err) { mostrarToast('Error de red al procesar solicitud', 'danger'); }
    btn.disabled = false; btn.innerText = 'Enviar Solicitud';
}

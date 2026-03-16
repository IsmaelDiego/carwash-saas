<?php require VIEW_PATH . '/layouts/header.view.php'; ?>
<style>
    .config-card { border:none; border-radius:14px; overflow:hidden; }
    .config-card .card-header { background: linear-gradient(135deg,#696cff,#7b7eff); color:#fff; }
    .token-card { border:none; border-radius:12px; padding:14px; margin-bottom:10px; box-shadow:0 2px 6px rgba(0,0,0,0.04);
        border-left:4px solid #696cff; transition:all 0.2s; }
    .token-card:hover { box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    .token-card.usado { opacity:0.5; border-left-color:#8592a3; }
    .token-card.expirado { opacity:0.4; border-left-color:#ff3e1d; }
    .token-codigo { font-family:monospace; font-size:1.3rem; font-weight:700; letter-spacing:3px; color:#696cff;
        background:#f0f0ff; padding:6px 14px; border-radius:8px; display:inline-block; }
    .token-card.usado .token-codigo { color:#8592a3; background:#f5f5f5; }
</style>

<div class="content-wrapper">
    <div class="container-fluid flex-grow-1 container-p-y">

        <h5 class="card-header border-bottom mb-4">
            <i class="bx bx-cog text-primary me-1"></i> CONFIGURACIÓN DEL SISTEMA
        </h5>
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/home">Inicio</a></li><li class="breadcrumb-item active text-primary">Configuración</li></ol>
        </nav>

        <div class="row">
            <!-- ═══ CONFIGURACIÓN GENERAL ═══ -->
            <div class="col-lg-5 mb-4">
                <div class="card config-card shadow-sm">
                    <div class="card-header py-3">
                        <h6 class="mb-0 fw-bold text-white"><i class="bx bx-buildings me-1"></i> Datos del Negocio</h6>
                    </div>
                    <div class="card-body p-4">
                        <form id="formConfig" enctype="multipart/form-data">
                            <div class="mb-3 text-center">
                                <img src="<?= BASE_URL ?>/public/img/logo.png" id="logoPreview" style="max-height:80px;border-radius:10px;box-shadow:0 4px 8px rgba(0,0,0,0.1)">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre del Negocio</label>
                                <input type="text" class="form-control" name="nombre_negocio" id="cfg_nombre" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-4">
                                    <label class="form-label fw-bold">Abreviatura</label>
                                    <input type="text" class="form-control" name="abreviatura" id="cfg_abrev" maxlength="10">
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold">Moneda</label>
                                    <select class="form-select" name="moneda" id="cfg_moneda">
                                        <option value="S/">S/ (Soles)</option>
                                        <option value="$">$ (Dólares)</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <label class="form-label fw-bold">Logo</label>
                                    <input type="file" class="form-control" name="logo" id="cfg_logo" accept="image/*" onchange="previewLogo(this)">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Meta de Puntos para Canje</label>
                                <input type="number" class="form-control" name="meta_puntos_canje" id="cfg_meta" min="1">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="cfg_modo_sin_cajero" name="modo_sin_cajero" value="1">
                                    <label class="form-check-label fw-bold" for="cfg_modo_sin_cajero">
                                        Modo Sin Cajero <small class="text-muted">(Operario puede cobrar sin token)</small>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                                <i class="bx bx-save me-1"></i> Guardar Cambios
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ═══ TOKENS DE SEGURIDAD ═══ -->
            <div class="col-lg-7 mb-4">
                <div class="card shadow-sm" style="border:none;border-radius:14px">
                    <div class="card-header d-flex justify-content-between align-items-center border-0">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="bx bx-key text-warning me-1"></i> Tokens de Seguridad</h6>
                            <small class="text-muted">Genera códigos temporales para operarios y cajeros</small>
                        </div>
                        <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalToken">
                            <i class="bx bx-plus me-1"></i>Generar Token
                        </button>
                    </div>
                    <div class="card-body pt-0" id="listaTokens" style="max-height:500px;overflow-y:auto">
                        <div class="text-center py-4 text-muted">Cargando tokens...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>

<!-- MODAL: Generar Token -->
<div class="modal fade" id="modalToken" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-top border-5 border-primary">
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="bx bx-key text-primary" style="font-size:3rem"></i>
                    <h5 class="fw-bold mt-2">Generar Token</h5>
                </div>
                <form id="formToken">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Motivo</label>
                        <select class="form-select" name="motivo" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="Cajero ausente - Operario cobra">Cajero ausente — Operario cobra</option>
                            <option value="Corrección de registro - Cajero">Corrección de registro — Cajero</option>
                            <option value="Anulación autorizada">Anulación autorizada</option>
                            <option value="Otro">Otro motivo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Duración (minutos)</label>
                        <input type="number" class="form-control" name="minutos_validez" value="60" min="5" max="1440">
                        <small class="text-muted">Mín: 5 min — Máx: 24 horas (1440 min)</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">GENERAR</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Token Generado -->
<div class="modal fade" id="modalTokenResult" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-top border-5 border-success">
            <div class="modal-body p-4 text-center">
                <i class="bx bx-check-circle text-success" style="font-size:3rem"></i>
                <h5 class="fw-bold mt-2">¡Token Generado!</h5>
                <p class="text-muted small">Comunica este código al empleado:</p>
                <div class="token-codigo mb-3" id="tokenResultCodigo" style="font-size:2rem;letter-spacing:5px"></div>
                <p class="small text-muted">Expira: <strong id="tokenResultExpira"></strong></p>
                <button class="btn btn-success w-100" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<?php require VIEW_PATH . '/partials/global/toasts.php'; ?>
<?php require VIEW_PATH . '/layouts/footer.view.php'; ?>

<script>
const BASE_URL = "<?= BASE_URL ?>";

document.addEventListener('DOMContentLoaded', () => {
    cargarConfig();
    cargarTokens();
    initForms();
});

async function cargarConfig() {
    const res = await fetch(`${BASE_URL}/admin/configuracion/getconfig`);
    const json = await res.json();
    if (json.success) {
        const d = json.data;
        document.getElementById('cfg_nombre').value = d.nombre_negocio || '';
        document.getElementById('cfg_abrev').value = d.abreviatura || '';
        document.getElementById('cfg_moneda').value = d.moneda || 'S/';
        document.getElementById('cfg_meta').value = d.meta_puntos_canje || 10;
        document.getElementById('cfg_modo_sin_cajero').checked = d.modo_sin_cajero == 1;
        if (d.logo) {
            document.getElementById('logoPreview').src = `${BASE_URL}/${d.logo}`;
        }
    }
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { document.getElementById('logoPreview').src = e.target.result; }
        reader.readAsDataURL(input.files[0]);
    }
}

async function cargarTokens() {
    const res = await fetch(`${BASE_URL}/admin/configuracion/gettokens`);
    const json = await res.json();
    const tokens = json.data || [];
    const container = document.getElementById('listaTokens');

    if (!tokens.length) {
        container.innerHTML = `<div class="text-center py-4 text-muted"><i class="bx bx-key" style="font-size:2.5rem"></i><p class="mt-2 mb-0">No hay tokens generados</p></div>`;
        return;
    }

    container.innerHTML = tokens.map(t => {
        const ahora = new Date();
        const expira = new Date(t.fecha_expiracion);
        const usado = t.usado == 1;
        const expirado = expira < ahora && !usado;
        const activo = !usado && !expirado;
        const cls = usado ? 'usado' : (expirado ? 'expirado' : '');
        const badge = activo ? '<span class="badge bg-success">ACTIVO</span>' : (usado ? '<span class="badge bg-secondary">USADO</span>' : '<span class="badge bg-danger">EXPIRADO</span>');

        return `<div class="token-card ${cls}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="token-codigo">${t.codigo}</span>
                ${badge}
            </div>
            <div class="small text-muted"><i class="bx bx-user me-1"></i>${t.generado_por}</div>
            <div class="small text-muted"><i class="bx bx-message-detail me-1"></i>${t.motivo_generacion || '—'}</div>
            <div class="small text-muted"><i class="bx bx-time me-1"></i>Expira: ${expira.toLocaleString('es-PE')}</div>
        </div>`;
    }).join('');
}

function initForms() {
    // Config
    document.getElementById('formConfig').addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        // Explicitly set checkbox value since unchecked boxes aren't serialized
        const modo = document.getElementById('cfg_modo_sin_cajero').checked ? 1 : 0;
        fd.set('modo_sin_cajero', modo);

        const res = await fetch(`${BASE_URL}/admin/configuracion/guardar`, {
            method: 'POST', body: fd
        });
        const data = await res.json();
        mostrarToast(data.message, data.success ? 'success' : 'danger');
    });

    // Token
    document.getElementById('formToken').addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(this);
        const obj = {};
        fd.forEach((v,k) => obj[k] = v);

        const res = await fetch(`${BASE_URL}/admin/configuracion/generartoken`, {
            method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(obj)
        });
        const data = await res.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalToken')).hide();
            document.getElementById('tokenResultCodigo').textContent = data.token.codigo;
            document.getElementById('tokenResultExpira').textContent = new Date(data.token.expira).toLocaleString('es-PE');
            new bootstrap.Modal(document.getElementById('modalTokenResult')).show();
            this.reset();
            cargarTokens();
        } else {
            mostrarToast(data.message, 'danger');
        }
    });
}

function mostrarToast(msg, tipo) {
    let el = document.getElementById('toastSistema');
    el.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = "11000";
    document.getElementById('toastMensaje').textContent = msg;
    new bootstrap.Toast(el).show();
}
</script>

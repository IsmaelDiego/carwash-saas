let initialConfig = {};

document.addEventListener('DOMContentLoaded', () => {
    cargarConfig();
    cargarTokens();
    initForms();
});

async function cargarConfig() {
    try {
        const res = await fetch(`${BASE_URL}/admin/configuracion/getconfig`);
        const json = await res.json();
        if (json.success) {
            const d = json.data;
            initialConfig = { ...d }; // Guardar estado inicial para detección de cambios

            document.getElementById('cfg_nombre').value = d.nombre_negocio || '';
            document.getElementById('cfg_abrev').value = d.abreviatura || '';
            document.getElementById('cfg_moneda').value = d.moneda || 'S/';
            document.getElementById('cfg_meta').value = d.meta_puntos_canje || 10;
            document.getElementById('cfg_modo_sin_cajero').checked = d.modo_sin_cajero == 1;
            
            // Actualizar Previsualización Inicial
            actualizarPreviewLive();

            if (d.logo) {
                const timestamp = new Date().getTime();
                const logoUrl = `${BASE_URL}/${d.logo}?v=${timestamp}`;
                document.getElementById('logoPreview').src = logoUrl;
                document.getElementById('previewSidebarLogo').innerHTML = `<img src="${logoUrl}" style="width:100%; height:100%; object-fit:contain;">`;
                
                // También actualizar el logo real del sidebar si existe en el layout
                const realSidebarLogo = document.getElementById('sidebar-logo');
                if (realSidebarLogo) realSidebarLogo.src = logoUrl;
            }
        }
    } catch(e) { console.error("Error al cargar configuración", e); }
}

function actualizarPreviewLive() {
    const abrev = document.getElementById('cfg_abrev').value || 'C-SAAS';
    const moneda = document.getElementById('cfg_moneda').value || 'S/';
    
    document.getElementById('previewSidebarAbrev').textContent = abrev;
    document.getElementById('previewMoneda').textContent = moneda;

    detectarCambios();
}

function detectarCambios() {
    const current = {
        nombre_negocio: document.getElementById('cfg_nombre').value,
        abreviatura: document.getElementById('cfg_abrev').value,
        moneda: document.getElementById('cfg_moneda').value,
        meta_puntos_canje: document.getElementById('cfg_meta').value,
        modo_sin_cajero: document.getElementById('cfg_modo_sin_cajero').checked ? 1 : 0
    };

    let hasChanges = false;
    for (let key in current) {
        if (current[key] != initialConfig[key]) {
            hasChanges = true;
            break;
        }
    }

    // Si hay un archivo seleccionado, también hay cambios
    if (document.getElementById('cfg_logo').files.length > 0) hasChanges = true;

    const badge = document.getElementById('badgeUnsaved');
    const btnDiscard = document.getElementById('btnDiscardConfig');
    
    if (hasChanges) {
        badge.style.display = 'block';
        if(btnDiscard) btnDiscard.style.display = 'block';
        document.querySelector('.btn-save-cfg').classList.add('pulse-save');
    } else {
        badge.style.display = 'none';
        if(btnDiscard) btnDiscard.style.display = 'none';
        document.querySelector('.btn-save-cfg').classList.remove('pulse-save');
    }
}

function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) { 
            document.getElementById('logoPreview').src = e.target.result;
            document.getElementById('previewSidebarLogo').innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:contain;">`;
            detectarCambios();
        }
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

    let hayActivo = false;
    container.innerHTML = tokens.map(t => {
        const ahora = new Date();
        const expira = new Date(t.fecha_expiracion);
        const usado = t.usado == 1;
        const expirado = expira < ahora && !usado;
        const activo = !usado && !expirado;
        
        if (activo) hayActivo = true;

        const cls = usado ? 'usado' : (expirado ? 'expirado' : '');
        const badge = activo ? '<span class="badge bg-success">ACTIVO</span>' : (usado ? '<span class="badge bg-secondary">USADO</span>' : '<span class="badge bg-danger">EXPIRADO</span>');

        // Mostrar conteo de usos
        const limiteText = t.limite_usos == 0 ? 'Ilimitado' : t.limite_usos;
        const usosHtml = `<div class="small fw-bold mt-1 text-primary"><i class="bx bx-repost me-1"></i>Usos: ${t.contador_usos} / ${limiteText}</div>`;

        return `<div class="token-card ${cls}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="token-codigo">${t.codigo}</span>
                ${badge}
            </div>
            <div class="small text-muted"><i class="bx bx-user me-1"></i>${t.generado_por}</div>
            <div class="small text-muted"><i class="bx bx-message-detail me-1"></i>${t.motivo_generacion || '—'}</div>
            ${usosHtml}
            <div class="small text-muted mt-1"><i class="bx bx-time me-1"></i>Expira: ${expira.toLocaleString('es-PE')}</div>
        </div>`;
    }).join('');

    // Bloquear botón de generar si ya hay uno activo
    const btnGen = document.getElementById('btnShowModalToken');
    if (hayActivo) {
        btnGen.disabled = true;
        btnGen.innerHTML = '<i class="bx bx-lock-alt me-1"></i> Token Activo';
        btnGen.classList.replace('btn-primary', 'btn-label-secondary');
        btnGen.title = "Ya tienes un token funcionando";
    } else {
        btnGen.disabled = false;
        btnGen.innerHTML = '<i class="bx bx-plus me-1"></i> Generar Token';
        btnGen.classList.replace('btn-label-secondary', 'btn-primary');
        btnGen.title = "";
    }
}

function initForms() {
    // Escuchar cambios en inputs para Live Preview y Cambio detección
    ['cfg_nombre', 'cfg_abrev', 'cfg_moneda', 'cfg_meta', 'cfg_modo_sin_cajero'].forEach(id => {
        const el = document.getElementById(id);
        if(el) {
            el.addEventListener('input', actualizarPreviewLive);
            el.addEventListener('change', actualizarPreviewLive);
        }
    });

    // Abrir Modal Token con verificación
    $('#btnShowModalToken').on('click', async function() {
        const password = await window.confirmByPassword();
        if(password) {
            $('#modalToken').modal('show');
        }
    });

    // Descartar Cambios (Revertir al inicial sin alertas nativas)
    const btnDiscard = document.getElementById('btnDiscardConfig');
    if (btnDiscard) {
        btnDiscard.addEventListener('click', function() {
            cargarConfig(); // Simplemente recargar los valores iniciales guardados
            document.getElementById('formConfig').reset();
            document.getElementById('cfg_logo').value = ""; // Limpiar file input
            mostrarToast("Cambios descartados", "info");
        });
    }

    // Config: Guardar con Verificación de Seguridad
    document.getElementById('formConfig').addEventListener('submit', async function(e) {
        e.preventDefault();

        // 🛡️ VERIFICACIÓN DE SEGURIDAD PRIMERO
        const password = await window.confirmByPassword();
        if(!password) return;

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        const logoInput = document.getElementById('cfg_logo');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i> Optimizando Recursos...';

        const fd = new FormData(this);

        // Si hay un logo nuevo, redimensionarlo antes de subir
        if (logoInput.files && logoInput.files[0]) {
            try {
                const optimizedLogo = await resizeLogo(logoInput.files[0], 500, 500);
                fd.set('logo', optimizedLogo, 'logo_optimized.webp');
            } catch (e) {
                console.warn("No se pudo optimizar el logo, se enviará original", e);
            }
        }
        const modo = document.getElementById('cfg_modo_sin_cajero').checked ? 1 : 0;
        fd.set('modo_sin_cajero', modo);
        // Inyectar password verificado para el backend si es necesario (el controlador lo verificará de nuevo si queremos)
        fd.append('password_admin', password);

        try {
            const res = await fetch(`${BASE_URL}/admin/configuracion/guardar`, {
                method: 'POST', body: fd
            });
            const data = await res.json();
            
            if (data.success) {
                mostrarToast(data.message, 'success');
                // Limpiar el input de archivo para que detectarCambios() se resetee
                logoInput.value = "";
                // Actualizar estado inicial para "limpiar" cambios pendientes
                await cargarConfig(); 
                
                // Actualizar interfaz global
                if (document.getElementById('sidebar-abrev')) {
                    document.getElementById('sidebar-abrev').textContent = document.getElementById('cfg_abrev').value;
                }
                if (document.querySelector('.app-brand-text')) {
                    document.querySelector('.app-brand-text').textContent = document.getElementById('cfg_abrev').value;
                }
                if (document.getElementById('sidebar-logo')) {
                    document.getElementById('sidebar-logo').src = document.getElementById('logoPreview').src;
                }
                
                // Actualizar Favicon (Icono de la Pestaña) dinámicamente
                let faviconLink = document.querySelector("link[rel~='icon']");
                if (faviconLink) {
                    faviconLink.href = document.getElementById('logoPreview').src;
                }
            } else {
                mostrarToast(data.message, 'danger');
            }
        } catch(err) {
            mostrarToast('Error al conectar con el servidor', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Token: Guardar datos (Ya verificado al abrir modal)
    document.getElementById('formToken').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin me-2"></i> Generando...';

        const fd = new FormData(this);
        const obj = {};
        fd.forEach((v,k) => obj[k] = v);

        try {
            const res = await fetch(`${BASE_URL}/admin/configuracion/generartoken`, {
                method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(obj)
            });
            const data = await res.json();

            if (data.success) {
                $('#modalToken').modal('hide');
                document.getElementById('tokenResultCodigo').textContent = data.token.codigo;
                $('#modalTokenResult').modal('show');
                this.reset();
                cargarTokens();
            } else {
                mostrarToast(data.message, 'danger');
            }
        } catch(err) {
            mostrarToast('Error al generar token', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Copiar Token al Portapapeles (Boton o Codigo)
    const copiarToken = function() {
        const text = document.getElementById('tokenResultCodigo').textContent;
        navigator.clipboard.writeText(text).then(() => {
            const feedback = document.getElementById('copyFeedback');
            const btn = document.getElementById('btnCopyToken');

            feedback.style.display = 'block';
            btn.innerHTML = '<i class="bx bx-check me-1"></i> ¡TOKEN COPIADO!';
            btn.classList.replace('btn-outline-primary', 'btn-success');
            
            setTimeout(() => {
                feedback.style.display = 'none';
                btn.innerHTML = '<i class="bx bx-copy me-1"></i> COPIAR TOKEN';
                btn.classList.replace('btn-success', 'btn-outline-primary');
            }, 3000);

            if (window.navigator.vibrate) window.navigator.vibrate(50); // Vibración leve para móviles
        });
    };

    document.getElementById('btnCopyToken').addEventListener('click', copiarToken);
    document.getElementById('tokenResultCodigo').addEventListener('click', copiarToken);
}

async function resizeLogo(file, maxWidth, maxHeight) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                // Calcular nuevas dimensiones manteniendo el aspect ratio
                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Exportar como WebP con calidad 85% para reducir peso drásticamente
                canvas.toBlob((blob) => {
                    if (blob) {
                        resolve(blob);
                    } else {
                        reject(new Error("Error al procesar imagen"));
                    }
                }, 'image/webp', 0.85);
            };
            img.onerror = (err) => reject(err);
        };
        reader.onerror = (err) => reject(err);
    });
}

function mostrarToast(msg, tipo) {
    let el = document.getElementById('toastSistema');
    el.className = `bs-toast toast fade bg-${tipo} position-fixed top-0 end-0 m-3`;
    el.style.zIndex = "11000";
    document.getElementById('toastMensaje').textContent = msg;
    new bootstrap.Toast(el).show();
}

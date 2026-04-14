<!-- Estilos para el overlay de seguridad -->
<style>
#securityOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
}
#securityOverlay .security-card {
    background: white;
    width: 320px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    overflow: hidden;
    animation: slideUp 0.3s ease;
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<!-- Overlay de Seguridad -->
<div id="securityOverlay">
    <div class="security-card">
        <div class="bg-primary p-3 text-center">
            <h5 class="text-white mb-0"><i class="bx bx-shield-quarter me-2"></i>Seguridad</h5>
        </div>
        <form id="formSecurityVerify">
            <div class="p-4 text-center">
                <p class="text-muted small mb-3">Confirma tu contraseña de administrador para continuar.</p>
                <div class="input-group input-group-merge mb-2">
                    <span class="input-group-text"><i class="bx bx-lock-alt text-primary"></i></span>
                    <input type="password" id="securityPassInput" class="form-control" placeholder="Contraseña" required>
                </div>
                <div id="securityError" class="text-danger small mb-0" style="display:none;"></div>
            </div>
            <div class="p-3 border-top d-flex gap-2 justify-content-center">
                <button type="button" id="btnSecurityCancel" class="btn btn-label-secondary btn-sm">Cancelar</button>
                <button type="submit" id="btnSecurityConfirm" class="btn btn-primary btn-sm">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    let currentResolve = null;

    window.confirmByPassword = function() {
        return new Promise((resolve) => {
            // Guardar el resolve actual
            currentResolve = resolve;

            const overlay = document.getElementById('securityOverlay');
            const form = document.getElementById('formSecurityVerify');
            const input = document.getElementById('securityPassInput');
            const error = document.getElementById('securityError');
            const btnConfirm = document.getElementById('btnSecurityConfirm');

            // Limpiar
            form.reset();
            error.style.display = 'none';
            btnConfirm.disabled = false;
            btnConfirm.innerHTML = 'Confirmar';

            // Mostrar Overlay
            overlay.style.display = 'flex';
            setTimeout(() => input.focus(), 100);

            // Handler interno para cerrar
            const close = (val) => {
                overlay.style.display = 'none';
                currentResolve = null;
                resolve(val);
            };

            // Cancelar
            document.getElementById('btnSecurityCancel').onclick = () => close(false);

            // Submit
            form.onsubmit = async (e) => {
                e.preventDefault();
                const pass = input.value;
                if(!pass) return;

                btnConfirm.disabled = true;
                btnConfirm.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>';

                try {
                    const res = await fetch(`${BASE_URL}/auth/verifypassword`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ password: pass })
                    });
                    const data = await res.json();

                    if (data.success) {
                        close(pass);
                    } else {
                        error.textContent = data.message || 'Contraseña incorrecta';
                        error.style.display = 'block';
                        btnConfirm.disabled = false;
                        btnConfirm.innerHTML = 'Confirmar';
                    }
                } catch (err) {
                    error.textContent = 'Error de red';
                    error.style.display = 'block';
                    btnConfirm.disabled = false;
                    btnConfirm.innerHTML = 'Confirmar';
                }
            };
        });
    };
})();
</script>

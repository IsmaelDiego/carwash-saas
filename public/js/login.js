document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const messageBox = document.getElementById("loginMessage");
    const recoveryMessageBox = document.getElementById("recuperarMessage");
    
    function showAlert(msg, type = "danger", target = "login") {
        const box = (target === "login") ? messageBox : recoveryMessageBox;
        if (!box) return;
        box.style.display = "block";
        box.className = `alert alert-${type} text-center small mb-3`;
        box.innerText = msg;
        
        // Auto-scroll to message if in modal
        if (target !== "login") box.scrollIntoView({ behavior: 'smooth' });
    }

    function hideAlerts() {
        if (messageBox) messageBox.style.display = "none";
        if (recoveryMessageBox) recoveryMessageBox.style.display = "none";
    }

    // --- LÓGICA DE TIPO DE LOGIN (DNI/EMAIL) ---
    const radios = document.querySelectorAll('input[name="login_type"]');
    const identInput = document.getElementById('identifier');
    const identLabel = document.getElementById('identLabel');

    if (radios && identInput) {
        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                identInput.value = '';
                if (e.target.value === 'dni') {
                    identLabel.innerText = "DNI";
                    identInput.type = "text";
                    identInput.inputMode = "numeric";
                    identInput.placeholder = "Ej: 45892122";
                    identInput.maxLength = 8;
                } else {
                    identLabel.innerText = "Correo Electrónico";
                    identInput.type = "email";
                    identInput.inputMode = "email";
                    identInput.placeholder = "Ej: admin@carwash.com";
                    identInput.removeAttribute("maxLength");
                }
            });
        });

        identInput.addEventListener('input', function() {
            const isDni = document.querySelector('input[name="login_type"]:checked').value === 'dni';
            if (isDni) this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // --- SUBMIT LOGIN ---
    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideAlerts();
            
            const submitBtn = loginForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Verificando...";

            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch(`${BASE_URL}/login`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        identifier: data.identifier,
                        password: data.password
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    showAlert(result.message, "success");
                    setTimeout(() => window.location.href = result.redirect, 800);
                } else {
                    if (result.is_inactive) {
                        const modalInactivo = new bootstrap.Modal(document.getElementById('modalInactivo'));
                        modalInactivo.show();
                    } else {
                        showAlert(result.message || "Credenciales incorrectas", "danger");
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                }
            } catch (err) {
                showAlert("Error de conexión con el servidor", "warning");
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }

    // --- LÓGICA MULTI-STEP RECUPERACIÓN ---
    window.showStep = (step) => {
        hideAlerts();
        document.getElementById('stepRecuperar1').style.display = step === 1 ? 'block' : 'none';
        document.getElementById('stepRecuperar2').style.display = step === 2 ? 'block' : 'none';
        document.getElementById('stepRecuperar3').style.display = step === 3 ? 'block' : 'none';
    };

    let recoveryData = { identifier: '', pin: '' };

    // STEP 1: Solicitar (Solo Admins avanzan al step 2)
    const formRecuperar = document.getElementById("formRecuperar");
    if (formRecuperar) {
        formRecuperar.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideAlerts();
            const btn = formRecuperar.querySelector("button[type='submit']");
            const originalText = btn.innerText;
            btn.disabled = true;
            btn.innerText = "Procesando...";

            recoveryData.identifier = formRecuperar.querySelector('[name="identifier"]').value;

            try {
                const res = await fetch(`${BASE_URL}/auth/solicitarrecuperacion`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ identifier: recoveryData.identifier })
                });
                const data = await res.json();
                
                if (data.success) {
                    if (data.has_email) {
                        // Es admin y se envió PIN
                        document.getElementById('pinMessage').innerText = data.message;
                        showStep(2);
                    } else {
                        // No es admin o no hay mail, se notificó al admin de verdad
                        showAlert(data.message, "info", "recovery");
                        formRecuperar.reset();
                        // Ocultar botón después de éxito para evitar re-envíos
                        btn.style.display = "none"; 
                    }
                } else {
                    showAlert(data.message, "danger", "recovery");
                }
            } catch (err) { 
                showAlert("Error al conectar con el servicio de recuperación", "warning", "recovery"); 
            } finally { 
                btn.disabled = false; 
                if (btn.innerText === "Procesando...") btn.innerText = originalText;
            }
        });
    }

    // STEP 2: Verificar PIN
    const formVerificarPin = document.getElementById("formVerificarPin");
    if (formVerificarPin) {
        formVerificarPin.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideAlerts();
            const btn = formVerificarPin.querySelector("button[type='submit']");
            btn.disabled = true;

            recoveryData.pin = formVerificarPin.querySelector('[name="pin"]').value;

            try {
                const res = await fetch(`${BASE_URL}/auth/verificarpin`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(recoveryData)
                });
                const data = await res.json();
                
                if (data.success) {
                    showStep(3);
                } else {
                    showAlert(data.message, "danger", "recovery");
                }
            } catch (err) { 
                showAlert("Error de conexión al verificar PIN", "warning", "recovery"); 
            } finally { btn.disabled = false; }
        });
    }

    // STEP 3: Reset Password
    const formResetPassword = document.getElementById("formResetPassword");
    if (formResetPassword) {
        formResetPassword.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideAlerts();
            const btn = formResetPassword.querySelector("button[type='submit']");
            btn.disabled = true;

            const newPass = formResetPassword.querySelector('[name="password"]').value;

            try {
                const res = await fetch(`${BASE_URL}/auth/restablecerconpin`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ ...recoveryData, password: newPass })
                });
                const data = await res.json();
                
                if (data.success) {
                    showAlert(data.message, "success", "recovery");
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert(data.message, "danger", "recovery");
                }
            } catch (err) { 
                showAlert("Error de conexión al restablecer contraseña", "warning", "recovery"); 
            } finally { btn.disabled = false; }
        });
    }
});
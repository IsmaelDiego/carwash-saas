document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const messageBox = document.getElementById("loginMessage");
    function showAlert(message, type) {
        if (!messageBox) return;
        messageBox.style.display = "block";
        messageBox.className = `alert alert-${type} text-center`;
        messageBox.innerText = message;
    }

    const radios = document.querySelectorAll('input[name="login_type"]');
    const identInput = document.getElementById('identifier');
    const identLabel = document.getElementById('identLabel');

    if (radios && identInput) {
        radios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                identInput.value = ''; // Limpiar al cambiar
                if (e.target.value === 'dni') {
                    identLabel.innerText = "DNI";
                    identInput.type = "text";
                    identInput.inputMode = "numeric";
                    identInput.placeholder = "Ej: 45892122";
                    identInput.maxLength = 8;
                    identInput.minLength = 8;
                    identInput.pattern = "\\d{8}";
                } else {
                    identLabel.innerText = "Correo Electrónico";
                    identInput.type = "email";
                    identInput.inputMode = "email";
                    identInput.placeholder = "Ej: admin@carwash.com";
                    identInput.removeAttribute("maxLength");
                    identInput.removeAttribute("minLength");
                    identInput.removeAttribute("pattern");
                }
            });
        });

        // Filtrar números si está en modo DNI
        identInput.addEventListener('input', function() {
            const isDni = document.querySelector('input[name="login_type"]:checked').value === 'dni';
            if (isDni) {
                this.value = this.value.replace(/[^0-9]/g, ''); // Solo números
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            if (messageBox) messageBox.style.display = "none";
            const submitBtn = loginForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Verificando...";

            // 1. Capturar datos
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            // --- ADAPTACIÓN: Asegurar compatibilidad con AuthController ---
            // Tu PHP busca $data['email'] O $data['identifier'].
            // Si en tu HTML el input se llama "usuario", "dni", "user", etc.,
            // lo mapeamos a 'identifier' para que el PHP lo entienda siempre.
            
            // Buscamos el valor del input principal (asumiendo que es el primero o tiene nombre específico)
            const userValue = data.email || data.dni || data.identifier || data.usuario;
            
            const payload = {
                identifier: userValue, // Enviamos como 'identifier' genérico
                password: data.password
            };

            try {
                const response = await fetch(`${BASE_URL}/login`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload) // Enviamos el payload limpio
                });

                let result;
                try {
                    result = await response.json();
                } catch (err) {
                    throw new Error("El servidor no devolvió JSON");
                }

                if (response.ok && result.success) {
                    showAlert("¡Bienvenido! Redirigiendo...", "success");
                    setTimeout(() => {
                        window.location.href = result.redirect; 
                    }, 1500); 
                } else {
                    // El PHP devuelve 401 si falla, entra aquí si response.ok es false
                    // O si response.ok es true pero success false (depende de tu lógica exacta, cubrimos ambos)
                    showAlert(result.message || "Credenciales incorrectas", "danger");
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                }

            } catch (error) {
                console.error("Error:", error);
                showAlert("Error de conexión con el servidor", "warning");
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }
});
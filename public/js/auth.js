document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");
    const messageBox = document.getElementById("message-auth");

    function showAlert(msg, type) {
        if (!messageBox) return; // Seguridad si no existe el div
        messageBox.style.display = "block";
        messageBox.className = `alert alert-${type} text-center`; 
        messageBox.innerText = msg;
    }

    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            // 1. UI: Bloquear botón
            const submitBtn = registerForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Registrando...";
            if (messageBox) messageBox.style.display = "none";

            // 2. Capturar datos
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries()); 

            // --- ADAPTACIÓN V3.2: Validación Frontend básica ---
            // Tu PHP exige 'nombres' y 'dni'. Si el HTML tiene 'name' o 'fullname', fallará.
            if (!data.nombres || !data.dni || !data.password) {
                showAlert("Por favor, completa DNI, Nombres y Contraseña.", "warning");
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/register`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                });

                // Intentar parsear JSON, si falla es error de servidor (HTML error dump)
                let result;
                try {
                    result = await response.json();
                } catch (err) {
                    throw new Error("Respuesta inválida del servidor");
                }

                if (response.ok && result.success) {
                    // 3. ÉXITO
                    showAlert("¡Usuario registrado! Redirigiendo...", "success");
                    setTimeout(() => {
                        window.location.href = result.redirect; 
                    }, 1500); 
                } else {
                    // 4. ERROR LÓGICO (Ej: DNI duplicado)
                    showAlert(result.message || "Error al registrar", "danger");
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                }

            } catch (error) {
                console.error("Error:", error);
                showAlert("Error de conexión o servidor caído", "warning");
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }
});
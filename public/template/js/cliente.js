document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registrarcliente");
    // Asegúrate de que en tu HTML este elemento exista: <div id="message" class="alert" style="display:none;"></div>
    const messageBox = document.getElementById("message-registerCliente");

    // Función para manejar colores y visibilidad
    function showAlert(msg, type) {
        messageBox.style.display = "block";
        messageBox.className = `alert alert-${type} text-center`; // Bootstrap classes: alert-success, alert-danger
        messageBox.innerText = msg;
    }

    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault(); // Evita que el formulario recargue la página

            // 1. UI: Bloquear botón y limpiar mensajes previos
            const submitBtn = registerForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Registrando...";
            messageBox.style.display = "none";

            // 2. Capturar datos
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries()); 

            try {
                const response = await fetch(`${BASE_URL}/registrarcliente`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // 3. ÉXITO (Verde)
                    showAlert("¡Usuario registrado exitosamente! Redirigiendo...", "success");
                    
                    setTimeout(() => {
                        window.location.href = result.redirect; // Redirección controlada por backend
                    }, 1500); 
                } else {
                    // 4. ERROR DEL SERVIDOR (Rojo) - Ej: Email duplicado
                    showAlert(result.message || "Error desconocido al registrar", "danger");
                    
                    // Restaurar botón
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                }

            } catch (error) {
                console.error("Error:", error);
                
                // 5. ERROR DE CONEXIÓN (Amarillo)
                showAlert("Error de conexión con el servidor", "warning");

                // Restaurar botón
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }
});
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    // Este elemento en tu HTML debe ser un <div id="loginMessage" class="alert" role="alert"></div>
    const messageBox = document.getElementById("loginMessage");

    // Función auxiliar para gestionar los colores y mensajes
    function showAlert(message, type) {
        // Aseguramos que sea visible
        messageBox.style.display = "block";
        // Asignamos las clases de Bootstrap: alert-success (verde), alert-danger (rojo), alert-warning (amarillo)
        messageBox.className = `alert alert-${type} text-center`;
        messageBox.innerText = message;
    }

    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault(); // 1. Evita el envío tradicional

            // UI: Ocultar alertas previas y mostrar estado de carga en el botón
            messageBox.style.display = "none";
            const submitBtn = loginForm.querySelector("button[type='submit']");
            const originalBtnText = submitBtn.innerText;
            
            submitBtn.disabled = true;
            submitBtn.innerText = "Verificando...";

            // 2. Capturar datos del formulario
            const formData = new FormData(loginForm);
            const data = Object.fromEntries(formData.entries());

            try {
                // 3. Enviar petición al backend
                const response = await fetch(`${BASE_URL}/login`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // 4. Éxito: Mostrar alerta VERDE
                    showAlert("¡Bienvenido! Redirigiendo...", "success");
                    
                    setTimeout(() => {
                        window.location.href = result.redirect; 
                    }, 1500); // 1.5 segundos para que el usuario vea el mensaje verde
                } else {
                    // 5. Error Lógico (Credenciales mal): Mostrar alerta ROJA
                    showAlert(result.message || "Credenciales incorrectas o usuario inactivo", "danger");
                    
                    // Reactivar botón para intentar de nuevo
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalBtnText;
                }

            } catch (error) {
                console.error("Error:", error);
                
                // 6. Error de Conexión (Fetch falló): Mostrar alerta AMARILLA
                showAlert("Error de conexión con el servidor", "warning");

                // Reactivar botón
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
            }
        });
    }
});
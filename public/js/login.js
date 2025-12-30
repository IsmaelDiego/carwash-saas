document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const messageBox = document.getElementById("loginMessage");

    if (loginForm) {
        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault(); // 1. Evita el envío tradicional

            // Limpiar mensajes anteriores
            messageBox.innerText = "Verificando...";
            
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
                    // 4. Éxito: Redirigir
                    messageBox.style.color = "green";
                    messageBox.innerText = "¡Bienvenido! Redirigiendo...";
                    
                    setTimeout(() => {
                        window.location.href = result.redirect; 
                    }, 1000); // Pequeña pausa opcional para ver el mensaje
                } else {
                    // 5. Error: Mostrar mensaje (contraseña mal, usuario inactivo, etc.)
                    messageBox.style.color = "red";
                    messageBox.innerText = result.message || "Credenciales incorrectas";
                }

            } catch (error) {
                console.error("Error:", error);
                messageBox.innerText = "Error de conexión con el servidor";
            }
        });
    }
});
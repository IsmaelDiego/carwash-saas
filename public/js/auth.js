document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", async (e) => {
            e.preventDefault(); // Evita que el formulario recargue la página

            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData.entries()); // Convierte FormData a Objeto JS

            try {
                const response = await fetch(`${BASE_URL}/register`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert("Registro exitoso via JS. Redirigiendo al login...");
                    window.location.href = result.redirect; // Redirección controlada por backend
                } else {
                    // Mostrar error
                    document.getElementById("message").innerText = result.message || "Error desconocido";
                }

            } catch (error) {
                console.error("Error:", error);
                document.getElementById("message").innerText = "Error de conexión con el servidor";
            }
        });
    }
});
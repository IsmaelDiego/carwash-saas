<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerrando sesión...</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f5f5f9;
            font-family: sans-serif;
        }
        .card {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
        }
        .counter {
            font-size: 4rem;
            font-weight: bold;
            color: #696cff; /* Color primario de tu tema */
            margin: 1rem 0;
        }
        .message {
            color: #697a8d;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="card-body">
            <div style="font-size: 3rem;">👋</div>
            
            <h3 class="mt-3">¡Hasta pronto!</h3>
            <p class="message">Cerrando sesión de forma segura...</p>
            
            <div class="counter" id="countdown">5</div>
            
            <p class="text-muted small">Redireccionando en segundos</p>
        </div>
    </div>

    <script>
        // Definir BASE_URL desde PHP para usarlo en JS
        const BASE_URL = "<?= BASE_URL ?>";

        // Lógica de la cuenta regresiva
        let seconds = 5;
        const countElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            seconds--;
            countElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(interval);
                // Redirigir al login
                window.location.href = BASE_URL + '/login';
            }
        }, 1000); // 1000ms = 1 segundo
    </script>
</body>
</html>
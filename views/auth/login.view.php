<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    </head>
<body>

    <h2>Iniciar Sesión</h2>

    <form id="loginForm">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit">Ingresar</button>
    </form>

    <p id="loginMessage" style="color: red; font-weight: bold;"></p>

    <p>¿No tienes cuenta? <a href="<?= BASE_URL ?>/register">Regístrate aquí</a></p>

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    
    <script src="<?= BASE_URL ?>/public/js/login.js"></script>

</body>
</html>
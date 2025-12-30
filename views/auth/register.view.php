<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
</head>
<body>

    <h2>Registro de Usuario</h2>

    <form id="registerForm">
        <input type="text" name="name" required placeholder="Nombre completo">
        <input type="email" name="email" required placeholder="Correo electrónico">
        <input type="password" name="password" required placeholder="Contraseña">

        <select name="role">
            <option value="operador">Operador (Empleado)</option>
            <option value="admin">Administrador</option>
        </select>

        <button type="submit">Registrar</button>
    </form>
    
    <p id="message" style="color: red;"></p>
    <p>¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/login">Inicia sesión</a></p>

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/public/js/auth.js"></script>
</body>
</html>
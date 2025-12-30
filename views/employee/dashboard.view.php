<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Empleado</title>
</head>
<body>
    <h1>Dashboard Empleado</h1>
    <p>Bienvenido, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></p>
    <p>Rol: <?= $_SESSION['user']['role'] ?></p>

    <hr>

    <a href="<?= BASE_URL ?>/logout">
        <button type="button">Cerrar Sesión</button>
    </a>
</body>
</html>
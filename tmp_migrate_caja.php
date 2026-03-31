<?php
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';

try {
    $pdo = Database::getInstance();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Crear tabla caja_sesiones
    $sql1 = "CREATE TABLE IF NOT EXISTS `caja_sesiones` (
      `id_sesion` int(11) NOT NULL AUTO_INCREMENT,
      `id_usuario` int(11) NOT NULL,
      `monto_apertura` decimal(10,2) NOT NULL,
      `monto_cierre_real` decimal(10,2) DEFAULT NULL,
      `monto_esperado` decimal(10,2) DEFAULT NULL,
      `diferencia` decimal(10,2) DEFAULT NULL,
      `estado` enum('ABIERTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
      `fecha_apertura` datetime DEFAULT CURRENT_TIMESTAMP,
      `fecha_cierre` datetime DEFAULT NULL,
      PRIMARY KEY (`id_sesion`),
      FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id_usuario`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
    $pdo->exec($sql1);
    echo "Tabla caja_sesiones creada exitosamente.\n";

    // 2. Modificar tabla ordenes (agregar foreign key sin duplicar)
    // Verificar si la columna existe primero
    $check = $pdo->query("SHOW COLUMNS FROM `ordenes` LIKE 'id_caja_sesion'");
    if ($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE `ordenes` ADD COLUMN `id_caja_sesion` INT(11) NULL DEFAULT NULL;");
        $pdo->exec("ALTER TABLE `ordenes` ADD CONSTRAINT `fk_orden_caja` FOREIGN KEY (`id_caja_sesion`) REFERENCES `caja_sesiones`(`id_sesion`) ON DELETE SET NULL;");
        echo "Columna y Foranea id_caja_sesion agregadas a ordenes.\n";
    } else {
        echo "La columna id_caja_sesion ya existe en ordenes.\n";
    }

} catch(PDOException $e) {
    echo "Error ejecutando SQL: " . $e->getMessage();
}

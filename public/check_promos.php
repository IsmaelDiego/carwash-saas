<?php
$pdo = new PDO('mysql:host=localhost;dbname=carwash-sys;charset=utf8mb4', 'root', '');
$pdo->exec("UPDATE promociones SET fecha_fin = '2026-05-01', estado = 1 WHERE id_promocion = 8;");
echo "Promocion 8 actualizada";

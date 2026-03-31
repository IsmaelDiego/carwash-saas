<?php
$pdo = new PDO('mysql:host=localhost;dbname=carwash-sys;charset=utf8mb4', 'root', '');
$pdo->exec('ALTER TABLE `ordenes` ADD COLUMN `id_promocion` INT NULL DEFAULT NULL AFTER `id_vehiculo`;');
echo "Column added successfully.\n";

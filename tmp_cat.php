<?php
require 'config/database.php';
global $pdo;
$stmt = $pdo->query("DESCRIBE categorias_vehiculos");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

<?php
require 'app/core/helpers.php';
require 'config/db.php';
global $pdo;
$stmt = $pdo->query('DESCRIBE ordenes');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);

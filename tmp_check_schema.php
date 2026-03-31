<?php
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';
$pdo = Database::getInstance();
$cols = $pdo->query("DESCRIBE usuarios")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) echo $c['Field'] . " | " . $c['Type'] . "\n";

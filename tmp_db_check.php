<?php
require_once 'c:/xampp/htdocs/carwash-saas/config/database.php';
try {
    $pdo = Database::getInstance();
    
    // ver tablas
    $q = $pdo->query("SHOW TABLES");
    $tables = $q->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in DB:\n";
    foreach($tables as $t) {
        echo "- $t\n";
    }
    
    // check columnas de ordenes
    $q2 = $pdo->query("SHOW COLUMNS FROM ordenes");
    $cols = $q2->fetchAll(PDO::FETCH_COLUMN);
    echo "\nColumns in 'ordenes':\n";
    foreach($cols as $c) {
        echo "- $c\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}

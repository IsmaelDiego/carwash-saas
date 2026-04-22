<?php

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            
            $host = 'localhost';
            $db   = 'carwash-sys';
            $user = 'root';
            $pass = '';

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                if (defined('APP_ENV') && APP_ENV === 'development') {
                    die("Error de Conexión DB: " . $e->getMessage());
                }
                http_response_code(503);
                die("Servicio temporalmente no disponible.");
            }
        }
        return self::$instance;
    }

    private function __construct() {}
    private function __clone() {}
    public function __wakeup() { throw new \Exception("Cannot unserialize singleton"); }
}

$pdo = Database::getInstance();

<?php
// config/alamat_db.php - Read-only connection to alamat_db
require_once 'config.php';

class AlamatDB {
    private static $pdo = null;
    
    public static function getConnection() {
        if (self::$pdo === null) {
            try {
                $host = DB_HOST;
                $user = DB_USER;
                $pass = DB_PASS;
                
                self::$pdo = new PDO(
                    "mysql:host=$host;dbname=alamat_db;charset=utf8mb4",
                    $user,
                    $pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                error_log("AlamatDB Connection Error: " . $e->getMessage());
                return null;
            }
        }
        return self::$pdo;
    }
}

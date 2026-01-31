<?php
// app/core/Database.php
class Database {
    private static $conn;

    public static function getConnection() {
        if (!self::$conn) {
            self::$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (self::$conn->connect_error) {
                die("DB Connection Failed: " . self::$conn->connect_error);
            }
        }
        return self::$conn;
    }
}
?>

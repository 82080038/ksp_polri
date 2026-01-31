<?php
// app/core/AuditLogger.php
// Simple audit logger with auto table creation if missing

require_once 'Database.php';

class AuditLogger {
    private static function ensureTable() {
        static $checked = false;
        if ($checked) return;
        $db = Database::getConnection();
        $db->query("CREATE TABLE IF NOT EXISTS audit_trails (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(50) NOT NULL,
            entity VARCHAR(100) NOT NULL,
            entity_id INT NULL,
            payload_before TEXT NULL,
            payload_after TEXT NULL,
            ip_address VARCHAR(64) NULL,
            user_agent VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        $checked = true;
    }

    public static function log($userId, $action, $entity, $entityId = null, $before = null, $after = null) {
        self::ensureTable();
        $db = Database::getConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $stmt = $db->prepare("INSERT INTO audit_trails (user_id, action, entity, entity_id, payload_before, payload_after, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $beforeJson = $before ? json_encode($before) : null;
        $afterJson = $after ? json_encode($after) : null;
        $stmt->bind_param("ississss", $userId, $action, $entity, $entityId, $beforeJson, $afterJson, $ip, $ua);
        $stmt->execute();
    }
}

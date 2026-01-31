<?php
// app/models/User.php
require_once '../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("
            SELECT users.id, roles.name 
            FROM users 
            JOIN roles ON roles.id = users.role_id
            WHERE username = ? AND is_active = 1
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result['password'])) {
            return $result;
        }
        return false;
    }
}
?>

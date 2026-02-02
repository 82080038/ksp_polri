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
            SELECT users.id, users.password, roles.name 
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
    
    /**
     * Cek apakah username sudah ada
     */
    public function exists($username) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    /**
     * Register user baru
     */
    public function register($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (username, password, nama_lengkap, email, telepon, role_id, koperasi_id, Prop_desa, alamat_lengkap, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->bind_param("sssssiiis", 
            $data['username'],
            $data['password'],
            $data['nama_lengkap'],
            $data['email'],
            $data['telepon'],
            $data['role_id'],
            $data['koperasi_id'],
            $data['Prop_desa'],
            $data['alamat_lengkap']
        );
        $stmt->execute();
        return $this->db->insert_id;
    }
}
?>

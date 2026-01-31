<?php
// backend/app/models/Investor.php
require_once '../config/database.php';

class Investor {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM investors WHERE status = 'ACTIVE'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM investors WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO investors (nama, jenis, npwp, alamat, telepon, email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $data['nama'], $data['jenis'], $data['npwp'], $data['alamat'], $data['telepon'], $data['email']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE investors SET nama = ?, jenis = ?, npwp = ?, alamat = ?, telepon = ?, email = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssssssi", $data['nama'], $data['jenis'], $data['npwp'], $data['alamat'], $data['telepon'], $data['email'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("UPDATE investors SET status = 'INACTIVE' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

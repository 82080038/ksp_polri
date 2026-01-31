<?php
// backend/app/models/Supplier.php
require_once '../config/database.php';

class Supplier {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM suppliers WHERE status = 'ACTIVE'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO suppliers (nama_perusahaan, npwp, alamat, telepon, email, kategori) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $data['nama_perusahaan'], $data['npwp'], $data['alamat'], $data['telepon'], $data['email'], $data['kategori']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE suppliers SET nama_perusahaan = ?, npwp = ?, alamat = ?, telepon = ?, email = ?, kategori = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssssssi", $data['nama_perusahaan'], $data['npwp'], $data['alamat'], $data['telepon'], $data['email'], $data['kategori'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("UPDATE suppliers SET status = 'INACTIVE' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

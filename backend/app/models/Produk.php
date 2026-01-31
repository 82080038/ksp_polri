<?php
// backend/app/models/Produk.php
require_once '../config/database.php';

class Produk {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM produk WHERE status = 'ACTIVE'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM produk WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok, foto, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidsss", $data['kode_produk'], $data['nama_produk'], $data['kategori_id'], $data['harga'], $data['stok'], $data['foto'], $data['deskripsi']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE produk SET nama_produk = ?, kategori_id = ?, harga = ?, stok = ?, foto = ?, deskripsi = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sidsssi", $data['nama_produk'], $data['kategori_id'], $data['harga'], $data['stok'], $data['foto'], $data['deskripsi'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("UPDATE produk SET status = 'INACTIVE' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

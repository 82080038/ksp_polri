<?php
// app/models/Anggota.php
require_once '../core/Database.php';

class Anggota {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO anggota (nrp, nama, pangkat, satuan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $data['nrp'], $data['nama'], $data['pangkat'], $data['satuan']);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM anggota");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE anggota SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}
?>

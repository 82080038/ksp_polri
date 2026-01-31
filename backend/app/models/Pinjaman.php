<?php
// app/models/Pinjaman.php
require_once '../core/Database.php';

class Pinjaman {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO pinjaman (anggota_id, jumlah, tenor, bunga, status) VALUES (?, ?, ?, ?, 'DIAJUKAN')");
        $stmt->bind_param("idid", $data['anggota_id'], $data['jumlah'], $data['tenor'], $data['bunga']);
        return $stmt->execute();
    }

    public function approve($id, $status) {
        $stmt = $this->db->prepare("UPDATE pinjaman SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query("SELECT p.*, a.nama FROM pinjaman p JOIN anggota a ON a.id = p.anggota_id ORDER BY p.created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getDetail($id) {
        $stmt = $this->db->prepare("SELECT * FROM pinjaman WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $pinjaman = $stmt->get_result()->fetch_assoc();

        $stmt2 = $this->db->prepare("SELECT SUM(jumlah) AS total_bayar FROM angsuran WHERE pinjaman_id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $bayar = $stmt2->get_result()->fetch_assoc()['total_bayar'] ?? 0;

        $pinjaman['total_bayar'] = $bayar;
        $pinjaman['sisa'] = $pinjaman['jumlah'] - $bayar;
        return $pinjaman;
    }
}
?>

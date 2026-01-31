<?php
// backend/app/models/Agent.php
require_once '../config/database.php';

class Agent {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM agents WHERE status = 'ACTIVE'";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM agents WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO agents (user_id, partner_id, jenis_agen, wilayah_penjualan, komisi_persen, batas_kredit) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdd", $data['user_id'], $data['partner_id'], $data['jenis_agen'], $data['wilayah_penjualan'], $data['komisi_persen'], $data['batas_kredit']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE agents SET jenis_agen = ?, wilayah_penjualan = ?, komisi_persen = ?, batas_kredit = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssddi", $data['jenis_agen'], $data['wilayah_penjualan'], $data['komisi_persen'], $data['batas_kredit'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("UPDATE agents SET status = 'INACTIVE' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>

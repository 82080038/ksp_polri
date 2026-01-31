<?php
// app/models/Simpanan.php
require_once '../core/Database.php';

class Simpanan {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO simpanan (anggota_id, jenis, jumlah, tanggal, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isdsi", $data['anggota_id'], $data['jenis'], $data['jumlah'], $data['tanggal'], $data['created_by']);
        return $stmt->execute();
    }

    public function getByAnggota($anggota_id) {
        $stmt = $this->db->prepare("SELECT * FROM simpanan WHERE anggota_id = ? ORDER BY tanggal DESC");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSaldo($anggota_id) {
        $result = $this->db->query("
            SELECT jenis, SUM(jumlah) AS total
            FROM simpanan
            WHERE anggota_id = $anggota_id
            GROUP BY jenis
        ");
        $saldo = ['POKOK' => 0, 'WAJIB' => 0, 'SUKARELA' => 0, 'TOTAL' => 0];
        while ($row = $result->fetch_assoc()) {
            $saldo[$row['jenis']] = (float)$row['total'];
            $saldo['TOTAL'] += (float)$row['total'];
        }
        return $saldo;
    }
}
?>

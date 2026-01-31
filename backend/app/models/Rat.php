<?php
// app/models/Rat.php
require_once '../core/Database.php';

class Rat {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($tahun) {
        $tanggal = date('Y-m-d');
        $stmt = $this->db->prepare("INSERT INTO rat (tahun, tanggal, status) VALUES (?, ?, 'DRAFT')");
        $stmt->bind_param("is", $tahun, $tanggal);
        return $stmt->execute();
    }

    public function sahkan($id) {
        $stmt = $this->db->prepare("UPDATE rat SET status='DISAHKAN' WHERE id=?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getLaporan($tahun) {
        $shu_total = $this->db->query("SELECT SUM(jumlah) total FROM shu WHERE tahun=$tahun")->fetch_assoc()['total'] ?? 0;
        $simpanan_total = $this->db->query("SELECT SUM(jumlah) total FROM simpanan")->fetch_assoc()['total'] ?? 0;
        $pinjaman_edar = $this->db->query("SELECT SUM(jumlah) total FROM pinjaman WHERE status IN ('DISETUJUI','LUNAS')")->fetch_assoc()['total'] ?? 0;

        return [
            'tahun' => $tahun,
            'total_shu' => $shu_total,
            'total_simpanan' => $simpanan_total,
            'pinjaman_edar' => $pinjaman_edar
        ];
    }
}
?>

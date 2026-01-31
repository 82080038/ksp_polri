<?php
// app/models/Shu.php
require_once '../core/Database.php';

class Shu {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function generate($tahun, $total_shu) {
        // Total simpanan
        $ts = $this->db->query("SELECT SUM(jumlah) total FROM simpanan")->fetch_assoc()['total'] ?? 0;

        // Total pinjaman
        $tp = $this->db->query("SELECT SUM(jumlah) total FROM pinjaman WHERE status IN ('DISETUJUI','LUNAS')")->fetch_assoc()['total'] ?? 0;

        $anggota = $this->db->query("SELECT id FROM anggota WHERE status='AKTIF'");

        while ($a = $anggota->fetch_assoc()) {
            $id = $a['id'];

            $s = $this->db->query("SELECT SUM(jumlah) total FROM simpanan WHERE anggota_id=$id")->fetch_assoc()['total'] ?? 0;

            $p = $this->db->query("SELECT SUM(jumlah) total FROM pinjaman WHERE anggota_id=$id AND status IN ('DISETUJUI','LUNAS')")->fetch_assoc()['total'] ?? 0;

            $shu = 0;
            if ($ts > 0) $shu += ($s / $ts) * ($total_shu * 0.5);
            if ($tp > 0) $shu += ($p / $tp) * ($total_shu * 0.5);

            $stmt = $this->db->prepare("INSERT INTO shu (anggota_id, tahun, jumlah) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $id, $tahun, $shu);
            $stmt->execute();
        }
    }

    public function getByTahun($tahun) {
        $stmt = $this->db->prepare("SELECT a.nama, s.jumlah FROM shu s JOIN anggota a ON a.id=s.anggota_id WHERE s.tahun=?");
        $stmt->bind_param("i", $tahun);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByAnggota($anggota_id, $tahun) {
        $stmt = $this->db->prepare("SELECT jumlah FROM shu WHERE anggota_id=? AND tahun=?");
        $stmt->bind_param("ii", $anggota_id, $tahun);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['jumlah'] ?? 0;
    }
}
?>

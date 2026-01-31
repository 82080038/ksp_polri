<?php
// backend/services/RatService.php
require_once '../config/database.php';

class RatService {

    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getRingkasan($tahun) {
        return [
            'anggota_awal' => $this->getCountAnggotaAwal($tahun),
            'anggota_akhir' => $this->getCountAnggotaAkhir($tahun),
            'total_simpanan' => $this->sum("simpanan", "jumlah", $tahun),
            'pinjaman_edar' => $this->sum("pinjaman", "jumlah", $tahun),
            'shu' => $this->sum("shu", "jumlah", $tahun)
        ];
    }

    private function sum($table, $field, $tahun) {
        $q = $this->db->query("SELECT SUM($field) total FROM $table WHERE tahun = '$tahun'");
        return $q->fetch_assoc()['total'] ?? 0;
    }

    private function getCountAnggotaAwal($tahun) {
        $q = $this->db->query("SELECT COUNT(*) c FROM anggota WHERE YEAR(created_at) < '$tahun'");
        return $q->fetch_assoc()['c'];
    }

    private function getCountAnggotaAkhir($tahun) {
        $q = $this->db->query("SELECT COUNT(*) c FROM anggota WHERE status='AKTIF'");
        return $q->fetch_assoc()['c'];
    }
}
?>

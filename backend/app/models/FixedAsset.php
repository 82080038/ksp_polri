<?php
// app/models/FixedAsset.php
require_once '../core/Database.php';

class FixedAsset {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO fixed_assets (kode_aset, nama_aset, kategori, nilai_perolehan, tanggal_perolehan, metode_depresiasi, umur_ekonomis, nilai_buku, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVE')");
        $stmt->bind_param("sssdsidd", $data['kode_aset'], $data['nama_aset'], $data['kategori'], $data['nilai_perolehan'], $data['tanggal_perolehan'], $data['metode_depresiasi'], $data['umur_ekonomis'], $data['nilai_perolehan']);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM fixed_assets ORDER BY kode_aset ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM fixed_assets WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE fixed_assets SET kode_aset = ?, nama_aset = ?, kategori = ?, nilai_perolehan = ?, tanggal_perolehan = ?, metode_depresiasi = ?, umur_ekonomis = ?, nilai_buku = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssdsisssi", $data['kode_aset'], $data['nama_aset'], $data['kategori'], $data['nilai_perolehan'], $data['tanggal_perolehan'], $data['metode_depresiasi'], $data['umur_ekonomis'], $data['nilai_buku'], $data['status'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM fixed_assets WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function calculateDepreciation($asset_id) {
        $asset = $this->getById($asset_id);
        if (!$asset || $asset['metode_depresiasi'] !== 'STRAIGHT_LINE') {
            return false;
        }

        $depreciation_per_year = $asset['nilai_perolehan'] / $asset['umur_ekonomis'];
        $details = [];
        $current_book_value = $asset['nilai_perolehan'];
        $year = (int)date('Y', strtotime($asset['tanggal_perolehan']));

        for ($i = 1; $i <= $asset['umur_ekonomis']; $i++) {
            $current_book_value -= $depreciation_per_year;
            $details[] = [
                'periode' => $year + $i,
                'nilai_depresiasi' => $depreciation_per_year,
                'nilai_buku_setelah' => max(0, $current_book_value)
            ];
        }

        return $details;
    }

    public function saveDepreciation($asset_id, $periode, $nilai_depresiasi, $nilai_buku_setelah) {
        $stmt = $this->db->prepare("INSERT INTO asset_depreciations (asset_id, periode, nilai_depresiasi, nilai_buku_setelah) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE nilai_depresiasi = ?, nilai_buku_setelah = ?");
        $stmt->bind_param("isdddd", $asset_id, $periode, $nilai_depresiasi, $nilai_buku_setelah, $nilai_depresiasi, $nilai_buku_setelah);
        return $stmt->execute();
    }

    public function getDepreciations($asset_id) {
        $stmt = $this->db->prepare("SELECT * FROM asset_depreciations WHERE asset_id = ? ORDER BY periode ASC");
        $stmt->bind_param("i", $asset_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

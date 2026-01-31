<?php
// app/models/Angsuran.php
require_once '../core/Database.php';

class Angsuran {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function bayar($data) {
        $pinjaman_id = $data['pinjaman_id'];
        $jumlah = $data['jumlah'];

        // Cek sisa
        $stmt = $this->db->prepare("
            SELECT jumlah - IFNULL((SELECT SUM(jumlah) FROM angsuran WHERE pinjaman_id = ?), 0) AS sisa
            FROM pinjaman WHERE id = ?
        ");
        $stmt->bind_param("ii", $pinjaman_id, $pinjaman_id);
        $stmt->execute();
        $sisa = $stmt->get_result()->fetch_assoc()['sisa'];

        if ($jumlah > $sisa) {
            return false;
        }

        // Simpan angsuran
        $stmt2 = $this->db->prepare("INSERT INTO angsuran (pinjaman_id, jumlah, tanggal) VALUES (?, ?, ?)");
        $tanggal = date('Y-m-d');
        $stmt2->bind_param("ids", $pinjaman_id, $jumlah, $tanggal);
        $stmt2->execute();

        // Jika lunas
        if ($jumlah == $sisa) {
            $this->db->query("UPDATE pinjaman SET status = 'LUNAS' WHERE id = $pinjaman_id");
        }

        return true;
    }

    public function getByPinjaman($pinjaman_id) {
        $stmt = $this->db->prepare("SELECT jumlah, tanggal FROM angsuran WHERE pinjaman_id = ?");
        $stmt->bind_param("i", $pinjaman_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

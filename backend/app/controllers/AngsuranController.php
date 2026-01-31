<?php
// app/controllers/AngsuranController.php
require_once '../app/models/Angsuran.php';
require_once '../app/core/AuditLogger.php';
require_once '../app/views/json.php';

class AngsuranController {
    public static function bayar() {
        $data = [
            'pinjaman_id' => $_POST['pinjaman_id'],
            'jumlah' => $_POST['jumlah']
        ];

        $angsuran = new Angsuran();
        if ($angsuran->bayar($data)) {
            AuditLogger::log($_SESSION['user_id'] ?? null, 'CREATE', 'angsuran', null, null, $data);
            jsonResponse(true, 'Angsuran berhasil dibayar');
        } else {
            jsonResponse(false, 'Jumlah melebihi sisa pinjaman');
        }
    }

    public static function list() {
        $pinjaman_id = $_GET['pinjaman_id'];
        $angsuran = new Angsuran();
        $data = $angsuran->getByPinjaman($pinjaman_id);
        jsonResponse(true, 'Riwayat angsuran', $data);
    }
}
?>

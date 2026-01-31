<?php
// app/controllers/PinjamanController.php
require_once '../app/models/Pinjaman.php';
require_once '../app/views/json.php';

class PinjamanController {
    public static function create() {
        $data = [
            'anggota_id' => $_POST['anggota_id'],
            'jumlah' => $_POST['jumlah'],
            'tenor' => $_POST['tenor'],
            'bunga' => $_POST['bunga']
        ];

        $pinjaman = new Pinjaman();
        if ($pinjaman->create($data)) {
            // Audit
            jsonResponse(true, 'Pinjaman diajukan');
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function approve() {
        $id = $_POST['pinjaman_id'];
        $aksi = $_POST['aksi'];
        $status = ($aksi === 'SETUJUI') ? 'DISETUJUI' : 'DITOLAK';

        $pinjaman = new Pinjaman();
        if ($pinjaman->approve($id, $status)) {
            // Audit
            jsonResponse(true, "Pinjaman $status");
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function list() {
        $pinjaman = new Pinjaman();
        $data = $pinjaman->getAll();
        jsonResponse(true, 'Data pinjaman', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $pinjaman = new Pinjaman();
        $data = $pinjaman->getDetail($id);
        jsonResponse(true, 'Detail pinjaman', $data);
    }
}
?>

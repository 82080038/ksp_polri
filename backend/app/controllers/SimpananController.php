<?php
// app/controllers/SimpananController.php
require_once '../app/models/Simpanan.php';
require_once '../app/views/json.php';

class SimpananController {
    public static function create() {
        $data = [
            'anggota_id' => $_POST['anggota_id'],
            'jenis' => $_POST['jenis'],
            'jumlah' => $_POST['jumlah'],
            'tanggal' => date('Y-m-d'),
            'created_by' => $_SESSION['user_id']
        ];

        if (!in_array($data['jenis'], ['POKOK','WAJIB','SUKARELA'])) {
            jsonResponse(false, 'Jenis simpanan tidak valid');
        }

        if ($data['jenis'] === 'POKOK') {
            $conn = Database::getConnection();
            $cek = $conn->query("SELECT id FROM simpanan WHERE anggota_id = {$data['anggota_id']} AND jenis = 'POKOK'");
            if ($cek->num_rows > 0) {
                jsonResponse(false, 'Simpanan pokok sudah ada');
            }
        }

        $simpanan = new Simpanan();
        if ($simpanan->create($data)) {
            // Audit
            jsonResponse(true, 'Simpanan berhasil ditambahkan');
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function list() {
        $anggota_id = $_GET['anggota_id'];
        $simpanan = new Simpanan();
        $data = $simpanan->getByAnggota($anggota_id);
        jsonResponse(true, 'Data simpanan', $data);
    }

    public static function saldo() {
        $anggota_id = $_GET['anggota_id'];
        $simpanan = new Simpanan();
        $data = $simpanan->getSaldo($anggota_id);
        jsonResponse(true, 'Saldo simpanan', $data);
    }
}
?>

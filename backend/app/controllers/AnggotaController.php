<?php
// app/controllers/AnggotaController.php
require_once '../app/models/Anggota.php';
require_once '../app/views/json.php';

class AnggotaController {
    public static function create() {
        $data = [
            'nrp' => $_POST['nrp'],
            'nama' => $_POST['nama'],
            'pangkat' => $_POST['pangkat'],
            'satuan' => $_POST['satuan']
        ];

        $anggota = new Anggota();
        if ($anggota->create($data)) {
            // Audit log
            $conn = Database::getConnection();
            $conn->query("INSERT INTO audit_log (user_id, aksi) VALUES ({$_SESSION['user_id']}, 'CREATE ANGGOTA')");
            jsonResponse(true, 'Anggota ditambahkan');
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function list() {
        $anggota = new Anggota();
        $data = $anggota->getAll();
        jsonResponse(true, 'Data anggota', $data);
    }
}
?>

<?php
// app/controllers/AnggotaController.php
require_once '../app/models/Anggota.php';
require_once '../app/core/AuditLogger.php';
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
            AuditLogger::log($_SESSION['user_id'] ?? null, 'CREATE', 'anggota', null, null, $data);
            jsonResponse(true, 'Anggota ditambahkan');
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function updateStatus() {
        $id = $_POST['id'];
        $status = $_POST['status'];

        $anggota = new Anggota();
        $before = $anggota->getById($id);
        if ($anggota->updateStatus($id, $status)) {
            AuditLogger::log($_SESSION['user_id'] ?? null, 'UPDATE', 'anggota', $id, $before, ['status' => $status]);
            jsonResponse(true, 'Status diperbarui');
        } else {
            jsonResponse(false, 'Gagal memperbarui status');
        }
    }

    public static function list() {
        $anggota = new Anggota();
        $data = $anggota->getAll();
        jsonResponse(true, 'Data anggota', $data);
    }
}
?>

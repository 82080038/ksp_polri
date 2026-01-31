<?php
// backend/app/controllers/AgentController.php
require_once '../app/models/Agent.php';
require_once '../app/views/json.php';

class AgentController {
    public static function list() {
        $agent = new Agent();
        $data = $agent->getAll();
        jsonResponse(true, 'Data agent', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $agent = new Agent();
        $data = $agent->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail agent', $data);
        } else {
            jsonResponse(false, 'Agent tidak ditemukan');
        }
    }

    public static function create() {
        $data = [
            'user_id' => $_POST['user_id'],
            'partner_id' => $_POST['partner_id'] ?? null,
            'jenis_agen' => $_POST['jenis_agen'],
            'wilayah_penjualan' => $_POST['wilayah_penjualan'],
            'komisi_persen' => $_POST['komisi_persen'],
            'batas_kredit' => $_POST['batas_kredit']
        ];

        $agent = new Agent();
        if ($agent->create($data)) {
            // Audit
            jsonResponse(true, 'Agent berhasil ditambahkan');
        } else {
            jsonResponse(false, 'Gagal menambah agent');
        }
    }

    public static function update() {
        $id = $_POST['id'];
        $data = [
            'jenis_agen' => $_POST['jenis_agen'],
            'wilayah_penjualan' => $_POST['wilayah_penjualan'],
            'komisi_persen' => $_POST['komisi_persen'],
            'batas_kredit' => $_POST['batas_kredit']
        ];

        $agent = new Agent();
        if ($agent->update($id, $data)) {
            // Audit
            jsonResponse(true, 'Agent berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update agent');
        }
    }

    public static function delete() {
        $id = $_POST['id'];
        $agent = new Agent();
        if ($agent->delete($id)) {
            // Audit
            jsonResponse(true, 'Agent berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal hapus agent');
        }
    }
}
?>

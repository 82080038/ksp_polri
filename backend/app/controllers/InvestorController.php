<?php
// backend/app/controllers/InvestorController.php
require_once '../app/models/Investor.php';
require_once '../app/views/json.php';

class InvestorController {
    public static function list() {
        $investor = new Investor();
        $data = $investor->getAll();
        jsonResponse(true, 'Data investor', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $investor = new Investor();
        $data = $investor->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail investor', $data);
        } else {
            jsonResponse(false, 'Investor tidak ditemukan');
        }
    }

    public static function create() {
        $data = [
            'nama' => $_POST['nama'],
            'jenis' => $_POST['jenis'],
            'npwp' => $_POST['npwp'],
            'alamat' => $_POST['alamat'],
            'telepon' => $_POST['telepon'],
            'email' => $_POST['email']
        ];

        $investor = new Investor();
        if ($investor->create($data)) {
            // Audit
            jsonResponse(true, 'Investor berhasil ditambahkan');
        } else {
            jsonResponse(false, 'Gagal menambah investor');
        }
    }

    public static function update() {
        $id = $_POST['id'];
        $data = [
            'nama' => $_POST['nama'],
            'jenis' => $_POST['jenis'],
            'npwp' => $_POST['npwp'],
            'alamat' => $_POST['alamat'],
            'telepon' => $_POST['telepon'],
            'email' => $_POST['email']
        ];

        $investor = new Investor();
        if ($investor->update($id, $data)) {
            // Audit
            jsonResponse(true, 'Investor berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update investor');
        }
    }

    public static function delete() {
        $id = $_POST['id'];
        $investor = new Investor();
        if ($investor->delete($id)) {
            // Audit
            jsonResponse(true, 'Investor berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal hapus investor');
        }
    }
}
?>

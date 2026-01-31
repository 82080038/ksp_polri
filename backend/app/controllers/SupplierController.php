<?php
// backend/app/controllers/SupplierController.php
require_once '../app/models/Supplier.php';
require_once '../app/views/json.php';

class SupplierController {
    public static function list() {
        $supplier = new Supplier();
        $data = $supplier->getAll();
        jsonResponse(true, 'Data supplier', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $supplier = new Supplier();
        $data = $supplier->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail supplier', $data);
        } else {
            jsonResponse(false, 'Supplier tidak ditemukan');
        }
    }

    public static function create() {
        $data = [
            'nama_perusahaan' => $_POST['nama_perusahaan'],
            'npwp' => $_POST['npwp'],
            'alamat' => $_POST['alamat'],
            'telepon' => $_POST['telepon'],
            'email' => $_POST['email'],
            'kategori' => $_POST['kategori']
        ];

        $supplier = new Supplier();
        if ($supplier->create($data)) {
            // Audit
            jsonResponse(true, 'Supplier berhasil ditambahkan');
        } else {
            jsonResponse(false, 'Gagal menambah supplier');
        }
    }

    public static function update() {
        $id = $_POST['id'];
        $data = [
            'nama_perusahaan' => $_POST['nama_perusahaan'],
            'npwp' => $_POST['npwp'],
            'alamat' => $_POST['alamat'],
            'telepon' => $_POST['telepon'],
            'email' => $_POST['email'],
            'kategori' => $_POST['kategori']
        ];

        $supplier = new Supplier();
        if ($supplier->update($id, $data)) {
            // Audit
            jsonResponse(true, 'Supplier berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update supplier');
        }
    }

    public static function delete() {
        $id = $_POST['id'];
        $supplier = new Supplier();
        if ($supplier->delete($id)) {
            // Audit
            jsonResponse(true, 'Supplier berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal hapus supplier');
        }
    }
}
?>

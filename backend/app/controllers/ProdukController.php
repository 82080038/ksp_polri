<?php
// backend/app/controllers/ProdukController.php
require_once '../app/models/Produk.php';
require_once '../app/views/json.php';

class ProdukController {
    public static function list() {
        $produk = new Produk();
        $data = $produk->getAll();
        jsonResponse(true, 'Data produk', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $produk = new Produk();
        $data = $produk->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail produk', $data);
        } else {
            jsonResponse(false, 'Produk tidak ditemukan');
        }
    }

    public static function create() {
        $data = [
            'kode_produk' => $_POST['kode_produk'],
            'nama_produk' => $_POST['nama_produk'],
            'kategori_id' => $_POST['kategori_id'],
            'harga' => $_POST['harga'],
            'stok' => $_POST['stok'],
            'foto' => $_POST['foto'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? ''
        ];

        $produk = new Produk();
        if ($produk->create($data)) {
            // Audit
            jsonResponse(true, 'Produk berhasil ditambahkan');
        } else {
            jsonResponse(false, 'Gagal menambah produk');
        }
    }

    public static function update() {
        $id = $_POST['id'];
        $data = [
            'nama_produk' => $_POST['nama_produk'],
            'kategori_id' => $_POST['kategori_id'],
            'harga' => $_POST['harga'],
            'stok' => $_POST['stok'],
            'foto' => $_POST['foto'] ?? '',
            'deskripsi' => $_POST['deskripsi'] ?? ''
        ];

        $produk = new Produk();
        if ($produk->update($id, $data)) {
            // Audit
            jsonResponse(true, 'Produk berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update produk');
        }
    }

    public static function delete() {
        $id = $_POST['id'];
        $produk = new Produk();
        if ($produk->delete($id)) {
            // Audit
            jsonResponse(true, 'Produk berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal hapus produk');
        }
    }
}
?>

<?php
// backend/app/controllers/OrderController.php
require_once '../app/models/Order.php';
require_once '../app/views/json.php';

class OrderController {
    public static function list() {
        $order = new Order();
        $data = $order->getAll();
        jsonResponse(true, 'Data pesanan', $data);
    }

    public static function detail() {
        $id = $_GET['id'];
        $order = new Order();
        $data = $order->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail pesanan', $data);
        } else {
            jsonResponse(false, 'Pesanan tidak ditemukan');
        }
    }

    public static function create() {
        $data = [
            'customer_id' => $_POST['customer_id'] ?? $_SESSION['user_id'],
            'customer_type' => $_POST['customer_type'] ?? 'ANGGOTA',
            'tanggal_order' => date('Y-m-d'),
            'total_harga' => $_POST['total_harga'],
            'total_biaya_operasional' => $_POST['total_biaya_operasional'] ?? 0,
            'total_bayar' => $_POST['total_bayar'],
            'metode_pengambilan' => $_POST['metode_pengambilan'],
            'lokasi_pengambilan_id' => $_POST['lokasi_pengambilan_id'] ?? null,
            'alamat_pengiriman' => $_POST['alamat_pengiriman'] ?? '',
            'metode_pembayaran' => $_POST['metode_pembayaran'],
            'status_pembayaran' => 'PENDING',
            'status_order' => 'PENDING',
            'details' => json_decode($_POST['details'], true)
        ];

        $order = new Order();
        $order_id = $order->create($data);
        if ($order_id) {
            // Audit
            jsonResponse(true, 'Pesanan berhasil dibuat', ['order_id' => $order_id]);
        } else {
            jsonResponse(false, 'Gagal membuat pesanan');
        }
    }

    public static function updateStatus() {
        $id = $_POST['id'];
        $status_order = $_POST['status_order'];
        $status_pembayaran = $_POST['status_pembayaran'];

        $order = new Order();
        if ($order->updateStatus($id, $status_order, $status_pembayaran)) {
            // Audit
            jsonResponse(true, 'Status pesanan berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update status pesanan');
        }
    }
}
?>

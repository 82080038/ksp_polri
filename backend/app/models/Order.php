<?php
// backend/app/models/Order.php
require_once '../config/database.php';

class Order {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM orders ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $this->conn->begin_transaction();
        try {
            // Generate nomor_order
            $nomor_order = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            $stmt = $this->conn->prepare("INSERT INTO orders (nomor_order, customer_id, customer_type, tanggal_order, total_harga, total_biaya_operasional, total_bayar, metode_pengambilan, lokasi_pengambilan_id, alamat_pengiriman, metode_pembayaran, status_pembayaran, status_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisddddddsisss", $nomor_order, $data['customer_id'], $data['customer_type'], $data['tanggal_order'], $data['total_harga'], $data['total_biaya_operasional'], $data['total_bayar'], $data['metode_pengambilan'], $data['lokasi_pengambilan_id'], $data['alamat_pengiriman'], $data['metode_pembayaran'], $data['status_pembayaran'], $data['status_order']);
            $stmt->execute();
            $order_id = $this->conn->insert_id;

            // Insert order details
            foreach ($data['details'] as $detail) {
                $stmt2 = $this->conn->prepare("INSERT INTO order_details (order_id, produk_id, qty, harga_satuan, diskon, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param("iiiddd", $order_id, $detail['produk_id'], $detail['qty'], $detail['harga_satuan'], $detail['diskon'], $detail['subtotal']);
                $stmt2->execute();
            }

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function updateStatus($id, $status_order, $status_pembayaran) {
        $stmt = $this->conn->prepare("UPDATE orders SET status_order = ?, status_pembayaran = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("ssi", $status_order, $status_pembayaran, $id);
        return $stmt->execute();
    }
}
?>

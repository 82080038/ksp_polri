<?php
// backend/app/models/Cart.php
require_once '../config/database.php';

class Cart {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT c.*, p.nama_produk, p.harga FROM cart c JOIN produk p ON c.produk_id = p.id WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function add($user_id, $produk_id, $qty) {
        // Check if already in cart
        $stmt = $this->conn->prepare("SELECT id, qty FROM cart WHERE user_id = ? AND produk_id = ?");
        $stmt->bind_param("ii", $user_id, $produk_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $new_qty = $existing['qty'] + $qty;
            $stmt = $this->conn->prepare("UPDATE cart SET qty = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ii", $new_qty, $existing['id']);
            return $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("INSERT INTO cart (user_id, produk_id, qty) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $produk_id, $qty);
            return $stmt->execute();
        }
    }

    public function updateQty($user_id, $produk_id, $qty) {
        $stmt = $this->conn->prepare("UPDATE cart SET qty = ?, updated_at = NOW() WHERE user_id = ? AND produk_id = ?");
        $stmt->bind_param("iii", $qty, $user_id, $produk_id);
        return $stmt->execute();
    }

    public function remove($user_id, $produk_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ? AND produk_id = ?");
        $stmt->bind_param("ii", $user_id, $produk_id);
        return $stmt->execute();
    }

    public function clear($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>

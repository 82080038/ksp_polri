<?php
// backend/app/controllers/CartController.php
require_once '../app/models/Cart.php';
require_once '../app/views/json.php';

class CartController {
    public static function list() {
        $user_id = $_SESSION['user_id'];
        $cart = new Cart();
        $data = $cart->getByUser($user_id);
        jsonResponse(true, 'Keranjang belanja', $data);
    }

    public static function add() {
        $user_id = $_SESSION['user_id'];
        $produk_id = $_POST['produk_id'];
        $qty = $_POST['qty'];

        $cart = new Cart();
        if ($cart->add($user_id, $produk_id, $qty)) {
            jsonResponse(true, 'Produk ditambahkan ke keranjang');
        } else {
            jsonResponse(false, 'Gagal menambah ke keranjang');
        }
    }

    public static function updateQty() {
        $user_id = $_SESSION['user_id'];
        $produk_id = $_POST['produk_id'];
        $qty = $_POST['qty'];

        $cart = new Cart();
        if ($cart->updateQty($user_id, $produk_id, $qty)) {
            jsonResponse(true, 'Qty berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal update qty');
        }
    }

    public static function remove() {
        $user_id = $_SESSION['user_id'];
        $produk_id = $_POST['produk_id'];

        $cart = new Cart();
        if ($cart->remove($user_id, $produk_id)) {
            jsonResponse(true, 'Produk dihapus dari keranjang');
        } else {
            jsonResponse(false, 'Gagal hapus dari keranjang');
        }
    }

    public static function clear() {
        $user_id = $_SESSION['user_id'];
        $cart = new Cart();
        if ($cart->clear($user_id)) {
            jsonResponse(true, 'Keranjang dikosongkan');
        } else {
            jsonResponse(false, 'Gagal kosongkan keranjang');
        }
    }
}
?>

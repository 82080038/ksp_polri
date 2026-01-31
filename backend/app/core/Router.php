<?php
// app/core/Router.php
class Router {
    public static function route($path) {
        switch ($path) {
            case 'login':
                require '../app/controllers/AuthController.php';
                AuthController::login();
                break;
            case 'anggota/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AnggotaController.php';
                AnggotaController::create();
                break;
            case 'anggota/list':
                Auth::requireLogin();
                require '../app/controllers/AnggotaController.php';
                AnggotaController::list();
                break;
            case 'simpanan/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SimpananController.php';
                SimpananController::create();
                break;
            case 'simpanan/list':
                Auth::requireLogin();
                require '../app/controllers/SimpananController.php';
                SimpananController::list();
                break;
            case 'pinjaman/create':
                Auth::requireLogin();
                require '../app/controllers/PinjamanController.php';
                PinjamanController::create();
                break;
            case 'pinjaman/approve':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/PinjamanController.php';
                PinjamanController::approve();
                break;
            case 'pinjaman/list':
                Auth::requireLogin();
                require '../app/controllers/PinjamanController.php';
                PinjamanController::list();
                break;
            case 'angsuran/bayar':
                Auth::requireLogin();
                require '../app/controllers/AngsuranController.php';
                AngsuranController::bayar();
                break;
            case 'shu/generate':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ShuController.php';
                ShuController::generate();
                break;
            case 'shu/list':
                Auth::requireLogin();
                require '../app/controllers/ShuController.php';
                ShuController::list();
                break;
            case 'rat/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/RatController.php';
                RatController::create();
                break;
            case 'rat/sahkan':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/RatController.php';
                RatController::sahkan();
                break;
            // Produk routes
            case 'produk/list':
                Auth::requireLogin();
                require '../app/controllers/ProdukController.php';
                ProdukController::list();
                break;
            case 'produk/detail':
                Auth::requireLogin();
                require '../app/controllers/ProdukController.php';
                ProdukController::detail();
                break;
            case 'produk/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::create();
                break;
            case 'produk/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::update();
                break;
            case 'produk/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/ProdukController.php';
                ProdukController::delete();
                break;
            // Order routes
            case 'order/list':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::list();
                break;
            case 'order/detail':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::detail();
                break;
            case 'order/create':
                Auth::requireLogin();
                require '../app/controllers/OrderController.php';
                OrderController::create();
                break;
            case 'order/updateStatus':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/OrderController.php';
                OrderController::updateStatus();
                break;
            // Cart routes
            case 'cart/list':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::list();
                break;
            case 'cart/add':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::add();
                break;
            case 'cart/updateQty':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::updateQty();
                break;
            case 'cart/remove':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::remove();
                break;
            case 'cart/clear':
                Auth::requireLogin();
                require '../app/controllers/CartController.php';
                CartController::clear();
                break;
            // Supplier routes
            case 'supplier/list':
                Auth::requireLogin();
                require '../app/controllers/SupplierController.php';
                SupplierController::list();
                break;
            case 'supplier/detail':
                Auth::requireLogin();
                require '../app/controllers/SupplierController.php';
                SupplierController::detail();
                break;
            case 'supplier/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::create();
                break;
            case 'supplier/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::update();
                break;
            case 'supplier/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/SupplierController.php';
                SupplierController::delete();
                break;
            // Investor routes
            case 'investor/list':
                Auth::requireLogin();
                require '../app/controllers/InvestorController.php';
                InvestorController::list();
                break;
            case 'investor/detail':
                Auth::requireLogin();
                require '../app/controllers/InvestorController.php';
                InvestorController::detail();
                break;
            case 'investor/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::create();
                break;
            case 'investor/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::update();
                break;
            case 'investor/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/InvestorController.php';
                InvestorController::delete();
                break;
            // Agent routes
            case 'agent/list':
                Auth::requireLogin();
                require '../app/controllers/AgentController.php';
                AgentController::list();
                break;
            case 'agent/detail':
                Auth::requireLogin();
                require '../app/controllers/AgentController.php';
                AgentController::detail();
                break;
            case 'agent/create':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::create();
                break;
            case 'agent/update':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::update();
                break;
            case 'agent/delete':
                Auth::requireLogin();
                Auth::requireRole('pengurus');
                require '../app/controllers/AgentController.php';
                AgentController::delete();
                break;
            default:
                echo json_encode(['status' => false, 'message' => 'Endpoint not found']);
        }
    }
}
?>

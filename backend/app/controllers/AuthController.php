<?php
// app/controllers/AuthController.php
require_once '../app/models/User.php';
require_once '../app/views/json.php';

class AuthController {
    public static function login() {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = new User();
        $auth = $user->authenticate($username, $password);

        if ($auth) {
            $_SESSION['user_id'] = $auth['id'];
            $_SESSION['role'] = $auth['name'];
            jsonResponse(true, 'Login berhasil');
        } else {
            jsonResponse(false, 'Login gagal');
        }
    }
}
?>

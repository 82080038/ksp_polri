<?php
// app/controllers/AuthController.php
require_once '../app/models/User.php';
require_once '../app/views/json.php';

class AuthController {
    public static function login() {
        // Simple session-based rate limiting: max 5 attempts per 5 minutes
        if (!isset($_SESSION)) {
            session_start();
        }
        $attemptKey = 'login_attempts';
        $now = time();
        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [];
        }
        // Filter attempts within last 5 minutes
        $_SESSION[$attemptKey] = array_filter($_SESSION[$attemptKey], function($ts) use ($now) {
            return ($now - $ts) <= 300;
        });
        if (count($_SESSION[$attemptKey]) >= 5) {
            jsonResponse(false, 'Terlalu banyak percobaan login. Coba lagi setelah 5 menit.');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            jsonResponse(false, 'Username dan password wajib diisi');
            return;
        }

        $user = new User();
        $auth = $user->authenticate($username, $password);

        if ($auth) {
            // reset attempts on success
            $_SESSION[$attemptKey] = [];
            $_SESSION['user_id'] = $auth['id'];
            $_SESSION['role'] = $auth['name'];
            jsonResponse(true, 'Login berhasil', [
                'role' => $auth['name']
            ]);
        } else {
            $_SESSION[$attemptKey][] = $now;
            jsonResponse(false, 'Login gagal');
        }
    }
}
?>

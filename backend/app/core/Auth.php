<?php
// app/core/Auth.php

class Auth {
    public static function requireLogin() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['status' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    public static function requireRole($role) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if ($_SESSION['role'] !== $role) {
            echo json_encode(['status' => false, 'message' => 'Forbidden']);
            exit;
        }
    }

    public static function generateCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRF($token) {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            echo json_encode(['status' => false, 'message' => 'CSRF token invalid']);
            exit;
        }
    }

    public static function rateLimit() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $key = 'rate_limit_' . $_SERVER['REMOTE_ADDR'];
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        $now = time();
        if ($now - $_SESSION[$key]['time'] > 3600) { // 1 hour
            $_SESSION[$key] = ['count' => 0, 'time' => $now];
        }
        $_SESSION[$key]['count']++;
        if ($_SESSION[$key]['count'] > 100) {
            echo json_encode(['status' => false, 'message' => 'Rate limit exceeded']);
            exit;
        }
    }
}
?>

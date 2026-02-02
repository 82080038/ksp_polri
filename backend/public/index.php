<?php
// public/index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/ksp_polri/backend/logs/php_errors.log');

try {
    require_once '../config/config.php';
    require_once '../app/core/Router.php';
    require_once '../app/core/Auth.php';
    require_once '../app/core/Database.php';

    $path = $_GET['path'] ?? '';
    Router::route($path);
} catch (Exception $e) {
    error_log("Fatal error in index.php: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Internal server error',
        'error' => $e->getMessage()
    ]);
}
?>

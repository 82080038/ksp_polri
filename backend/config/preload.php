<?php
// preload.php - OPcache preload file for performance
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Auth.php';
require_once __DIR__ . '/../app/views/json.php';

// Preload commonly used models
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/Koperasi.php';
require_once __DIR__ . '/../app/models/Alamat.php';

// Preload controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
?>

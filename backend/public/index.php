<?php
// public/index.php
require_once '../config/config.php';
require_once '../app/core/Router.php';
require_once '../app/core/Auth.php';
require_once '../app/core/Database.php';

$path = $_GET['path'] ?? '';
Router::route($path);
?>

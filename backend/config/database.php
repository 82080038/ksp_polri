<?php
/**
 * Database Configuration for KSP POLRI Application
 */

return [
    'host' => 'localhost',
    'username' => 'ksp_user',
    'password' => 'ksp_password',
    'database' => 'ksp_polri',
    'charset' => 'utf8mb4',
    'port' => 3306,

    // PDO options
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];

<?php
// debug.php - Simple debugging script for KSP POLRI
echo "=== KSP POLRI DEBUG SCRIPT ===\n\n";

// Test 1: Check if config files exist
echo "1. Checking config files...\n";
$configFiles = [
    'config/config.php',
    'config/database.php',
    'config/email_config.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

echo "\n";

// Test 2: Check if core files exist
echo "2. Checking core files...\n";
$coreFiles = [
    'app/core/Router.php',
    'app/core/Auth.php',
    'app/core/Database.php'
];

foreach ($coreFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

echo "\n";

// Test 3: Test database connection
echo "3. Testing database connection...\n";
try {
    $dbConfig = require 'config/database.php';
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']};port={$dbConfig['port']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    echo "✓ Database connection successful\n";

    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✓ Users table has {$result['count']} records\n";

} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 4: Test Router instantiation
echo "4. Testing Router class...\n";
try {
    require_once 'app/core/Router.php';
    echo "✓ Router class loaded successfully\n";

    // Test if Router class exists
    if (class_exists('Router')) {
        echo "✓ Router class exists\n";
    } else {
        echo "✗ Router class not found\n";
    }

} catch (Exception $e) {
    echo "✗ Router loading failed: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>

<?php
// scripts/backup_database.php
/**
 * Automated Database Backup Script for KSP POLRI
 *
 * Features:
 * - Daily database backup
 * - Compressed SQL dump
 * - Local storage with retention policy
 * - Email notifications for failures
 * - Backup verification
 *
 * Usage:
 * php scripts/backup_database.php
 *
 * Cron job (daily at 2 AM):
 * 0 2 * * * cd /var/www/html/ksp_polri && php scripts/backup_database.php >> logs/backup.log 2>&1
 */

require_once '../app/core/Database.php';
require_once '../services/NotificationService.php';

// Configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'ksp_polri',
    'db_user' => 'root', // Change this to your actual DB user
    'db_pass' => '',     // Change this to your actual DB password
    'backup_dir' => '../backups',
    'retention_days' => 30, // Keep backups for 30 days
    'max_backup_size' => 100 * 1024 * 1024, // 100MB max
    'notification_email' => 'admin@ksp-polri.local', // Email for notifications
    'mysqldump_path' => 'mysqldump' // Path to mysqldump binary
];

// Set timezone
date_default_timezone_set('Asia/Jakarta');

$log_file = '../logs/backup.log';
$timestamp = date('Y-m-d H:i:s');

// Start logging
logMessage("[$timestamp] Starting database backup process...");

try {
    // Create backup directory if not exists
    if (!is_dir($config['backup_dir'])) {
        mkdir($config['backup_dir'], 0755, true);
        logMessage("Created backup directory: {$config['backup_dir']}");
    }

    // Generate backup filename
    $date = date('Y-m-d_H-i-s');
    $backup_file = "{$config['backup_dir']}/ksp_polri_backup_{$date}.sql.gz";

    // Create database backup
    $success = createDatabaseBackup($config, $backup_file);

    if ($success) {
        logMessage("Backup created successfully: $backup_file");

        // Verify backup integrity
        if (verifyBackup($backup_file)) {
            logMessage("Backup verification successful");

            // Clean old backups
            cleanupOldBackups($config);

            // Send success notification (optional)
            sendBackupNotification(true, "Backup berhasil dibuat: " . basename($backup_file));

        } else {
            logMessage("ERROR: Backup verification failed!");
            sendBackupNotification(false, "Backup verification gagal untuk file: " . basename($backup_file));
        }

    } else {
        logMessage("ERROR: Failed to create backup!");
        sendBackupNotification(false, "Gagal membuat backup database");
    }

} catch (Exception $e) {
    logMessage("ERROR: Exception occurred: " . $e->getMessage());
    sendBackupNotification(false, "Exception during backup: " . $e->getMessage());
}

$end_timestamp = date('Y-m-d H:i:s');
logMessage("[$end_timestamp] Backup process completed.");

/**
 * Create compressed database backup using mysqldump
 */
function createDatabaseBackup($config, $backup_file) {
    $command = sprintf(
        '%s --host=%s --user=%s --password=%s %s | gzip > %s',
        escapeshellcmd($config['mysqldump_path']),
        escapeshellarg($config['db_host']),
        escapeshellarg($config['db_user']),
        escapeshellarg($config['db_pass']),
        escapeshellarg($config['db_name']),
        escapeshellarg($backup_file)
    );

    // Execute backup command
    $output = [];
    $return_code = 0;
    exec($command . ' 2>&1', $output, $return_code);

    if ($return_code === 0) {
        // Check file size
        if (file_exists($backup_file) && filesize($backup_file) > 0) {
            // Check if file size is reasonable (not too large)
            if (filesize($backup_file) < $config['max_backup_size']) {
                return true;
            } else {
                logMessage("ERROR: Backup file too large: " . filesize($backup_file) . " bytes");
                unlink($backup_file); // Delete oversized file
                return false;
            }
        }
    }

    logMessage("Backup command failed with return code: $return_code");
    logMessage("Command output: " . implode("\n", $output));

    return false;
}

/**
 * Verify backup integrity
 */
function verifyBackup($backup_file) {
    if (!file_exists($backup_file)) {
        return false;
    }

    // Check file size
    if (filesize($backup_file) < 1000) { // Minimum 1KB for a valid backup
        logMessage("Backup file too small: " . filesize($backup_file) . " bytes");
        return false;
    }

    // Try to decompress and check if it's valid SQL
    $command = "gzip -dc " . escapeshellarg($backup_file) . " | head -n 10";
    $output = [];
    exec($command, $output);

    // Check if output contains SQL-like content
    $sql_found = false;
    foreach ($output as $line) {
        if (stripos($line, 'create table') !== false ||
            stripos($line, 'insert into') !== false ||
            stripos($line, 'dump completed') !== false) {
            $sql_found = true;
            break;
        }
    }

    if (!$sql_found) {
        logMessage("Backup file does not contain valid SQL content");
        return false;
    }

    return true;
}

/**
 * Clean up old backup files based on retention policy
 */
function cleanupOldBackups($config) {
    $backup_dir = $config['backup_dir'];
    $retention_days = $config['retention_days'];

    if (!is_dir($backup_dir)) {
        return;
    }

    $files = glob($backup_dir . '/ksp_polri_backup_*.sql.gz');
    $deleted_count = 0;

    foreach ($files as $file) {
        $file_date = filemtime($file);
        $days_old = (time() - $file_date) / (60 * 60 * 24);

        if ($days_old > $retention_days) {
            if (unlink($file)) {
                $deleted_count++;
                logMessage("Deleted old backup: " . basename($file));
            }
        }
    }

    if ($deleted_count > 0) {
        logMessage("Cleaned up $deleted_count old backup files");
    }
}

/**
 * Send backup notification
 */
function sendBackupNotification($success, $message) {
    global $config;

    // Only send notifications if email is configured
    if (empty($config['notification_email'])) {
        return;
    }

    try {
        // For now, we'll just log the notification
        // In production, you might want to send actual emails
        $status = $success ? 'SUCCESS' : 'FAILED';
        logMessage("NOTIFICATION [$status]: $message");

        // You can integrate with NotificationService here
        // $notification = new NotificationService();
        // $notification->sendEmail($config['notification_email'], "Database Backup $status", $message);

    } catch (Exception $e) {
        logMessage("Failed to send notification: " . $e->getMessage());
    }
}

/**
 * Log message to file
 */
function logMessage($message) {
    global $log_file;

    $log_dir = dirname($log_file);
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }

    file_put_contents($log_file, $message . "\n", FILE_APPEND);
    echo $message . "\n"; // Also output to console for cron jobs
}

/**
 * Get backup statistics
 */
function getBackupStats($config) {
    $backup_dir = $config['backup_dir'];

    if (!is_dir($backup_dir)) {
        return ['total_backups' => 0, 'total_size' => 0, 'oldest_backup' => null, 'newest_backup' => null];
    }

    $files = glob($backup_dir . '/ksp_polri_backup_*.sql.gz');
    $total_size = 0;
    $oldest_time = time();
    $newest_time = 0;

    foreach ($files as $file) {
        $size = filesize($file);
        $total_size += $size;

        $mtime = filemtime($file);
        $oldest_time = min($oldest_time, $mtime);
        $newest_time = max($newest_time, $mtime);
    }

    return [
        'total_backups' => count($files),
        'total_size' => $total_size,
        'total_size_formatted' => formatBytes($total_size),
        'oldest_backup' => $oldest_time < time() ? date('Y-m-d H:i:s', $oldest_time) : null,
        'newest_backup' => $newest_time > 0 ? date('Y-m-d H:i:s', $newest_time) : null
    ];
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $units[$i];
}

// If run with --stats parameter, show backup statistics
if (isset($argv[1]) && $argv[1] === '--stats') {
    $stats = getBackupStats($config);
    echo "\n=== Backup Statistics ===\n";
    echo "Total Backups: " . $stats['total_backups'] . "\n";
    echo "Total Size: " . $stats['total_size_formatted'] . "\n";
    echo "Oldest Backup: " . ($stats['oldest_backup'] ?: 'None') . "\n";
    echo "Newest Backup: " . ($stats['newest_backup'] ?: 'None') . "\n";
}
?>

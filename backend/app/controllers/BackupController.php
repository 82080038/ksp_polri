<?php
// app/controllers/BackupController.php
require_once '../app/views/json.php';

class BackupController {

    /**
     * Get backup statistics
     */
    public static function getBackupStats() {
        Auth::requireRole('pengurus');

        $config = self::getBackupConfig();
        $stats = self::calculateBackupStats($config);

        jsonResponse(true, 'Backup statistics retrieved', $stats);
    }

    /**
     * Run manual backup
     */
    public static function runManualBackup() {
        Auth::requireRole('pengurus');

        $config = self::getBackupConfig();

        // Check if backup is already running
        $lock_file = '../logs/backup_running.lock';
        if (file_exists($lock_file)) {
            $lock_time = filemtime($lock_file);
            if (time() - $lock_time < 300) { // 5 minutes timeout
                jsonResponse(false, 'Backup sedang berjalan. Silakan tunggu.');
                return;
            } else {
                unlink($lock_file); // Remove stale lock
            }
        }

        // Create lock file
        file_put_contents($lock_file, time());

        try {
            // Run backup script
            $command = 'php ../scripts/backup_database.php';
            $output = [];
            $return_code = 0;

            exec($command . ' 2>&1', $output, $return_code);

            // Remove lock file
            if (file_exists($lock_file)) {
                unlink($lock_file);
            }

            if ($return_code === 0) {
                // Refresh stats
                $stats = self::calculateBackupStats($config);
                jsonResponse(true, 'Backup berhasil dibuat', $stats);
            } else {
                jsonResponse(false, 'Backup gagal: ' . implode("\n", $output));
            }

        } catch (Exception $e) {
            if (file_exists($lock_file)) {
                unlink($lock_file);
            }
            jsonResponse(false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * List all backup files
     */
    public static function listBackupFiles() {
        Auth::requireRole('pengurus');

        $config = self::getBackupConfig();
        $backup_dir = $config['backup_dir'];

        if (!is_dir($backup_dir)) {
            jsonResponse(true, 'No backup directory', ['backups' => []]);
            return;
        }

        $files = glob($backup_dir . '/ksp_polri_backup_*.sql.gz');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $mtime = filemtime($file);

            // Parse filename to get date
            if (preg_match('/ksp_polri_backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql\.gz/', $filename, $matches)) {
                $date_str = str_replace('_', ' ', $matches[1]);
                $date = DateTime::createFromFormat('Y-m-d H-i-s', $date_str);
            } else {
                $date = new DateTime('@' . $mtime);
            }

            $backups[] = [
                'filename' => $filename,
                'size' => $size,
                'size_formatted' => self::formatBytes($size),
                'created_at' => $date->format('Y-m-d H:i:s'),
                'age_days' => floor((time() - $mtime) / (60 * 60 * 24)),
                'path' => $file
            ];
        }

        // Sort by creation date (newest first)
        usort($backups, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        jsonResponse(true, 'Backup files retrieved', ['backups' => $backups]);
    }

    /**
     * Download backup file
     */
    public static function downloadBackup() {
        Auth::requireRole('pengurus');

        $filename = $_GET['file'] ?? '';
        if (empty($filename)) {
            jsonResponse(false, 'Filename required');
            return;
        }

        $config = self::getBackupConfig();
        $filepath = $config['backup_dir'] . '/' . basename($filename); // Security: prevent directory traversal

        if (!file_exists($filepath)) {
            jsonResponse(false, 'Backup file not found');
            return;
        }

        // Check if file is in backup directory
        $real_path = realpath($filepath);
        $real_backup_dir = realpath($config['backup_dir']);

        if (!$real_path || !str_starts_with($real_path, $real_backup_dir)) {
            jsonResponse(false, 'Invalid file path');
            return;
        }

        // Send file for download
        header('Content-Type: application/gzip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        readfile($filepath);
        exit;
    }

    /**
     * Delete backup file
     */
    public static function deleteBackup() {
        Auth::requireRole('pengurus');

        $filename = $_POST['file'] ?? '';
        if (empty($filename)) {
            jsonResponse(false, 'Filename required');
            return;
        }

        $config = self::getBackupConfig();
        $filepath = $config['backup_dir'] . '/' . basename($filename); // Security: prevent directory traversal

        if (!file_exists($filepath)) {
            jsonResponse(false, 'Backup file not found');
            return;
        }

        // Check if file is in backup directory
        $real_path = realpath($filepath);
        $real_backup_dir = realpath($config['backup_dir']);

        if (!$real_path || !str_starts_with($real_path, $real_backup_dir)) {
            jsonResponse(false, 'Invalid file path');
            return;
        }

        if (unlink($filepath)) {
            jsonResponse(true, 'Backup file deleted');
        } else {
            jsonResponse(false, 'Failed to delete backup file');
        }
    }

    /**
     * Restore backup file
     */
    public static function restoreBackup() {
        Auth::requireRole('pengurus');

        $filename = $_POST['file'] ?? '';
        if (empty($filename)) {
            jsonResponse(false, 'Filename required');
            return;
        }

        $config = self::getBackupConfig();
        $filepath = $config['backup_dir'] . '/' . basename($filename); // prevent traversal

        if (!file_exists($filepath)) {
            jsonResponse(false, 'Backup file not found');
            return;
        }

        $real_path = realpath($filepath);
        $real_backup_dir = realpath($config['backup_dir']);
        if (!$real_path || !str_starts_with($real_path, $real_backup_dir)) {
            jsonResponse(false, 'Invalid file path');
            return;
        }

        // Use lock to prevent concurrent restore/backup
        $lock_file = '../logs/backup_restore.lock';
        if (file_exists($lock_file) && (time() - filemtime($lock_file) < 600)) {
            jsonResponse(false, 'Proses restore/backup lain sedang berjalan. Coba lagi nanti.');
            return;
        }
        file_put_contents($lock_file, time());

        try {
            // Decompress and import using mysql client
            $dbHost = DB_HOST;
            $dbUser = DB_USER;
            $dbPass = DB_PASS;
            $dbName = DB_NAME;

            $escapedFile = escapeshellarg($real_path);
            $escapedDb = escapeshellarg($dbName);
            $passPart = $dbPass !== '' ? ('-p' . escapeshellarg($dbPass)) : '';

            // gunzip to stdout pipe to mysql
            $command = "gunzip < $escapedFile | mysql -h " . escapeshellarg($dbHost) . " -u " . escapeshellarg($dbUser) . " $passPart $escapedDb";

            $output = [];
            $return_code = 0;
            exec($command . ' 2>&1', $output, $return_code);

            if ($return_code === 0) {
                jsonResponse(true, 'Restore berhasil dijalankan');
            } else {
                jsonResponse(false, 'Restore gagal: ' . implode("\n", $output));
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Error: ' . $e->getMessage());
        } finally {
            if (file_exists($lock_file)) {
                unlink($lock_file);
            }
        }
    }

    /**
     * Get backup configuration
     */
    private static function getBackupConfig() {
        return [
            'backup_dir' => '../backups',
            'retention_days' => 30,
            'max_backup_size' => 100 * 1024 * 1024, // 100MB
        ];
    }

    /**
     * Calculate backup statistics
     */
    private static function calculateBackupStats($config) {
        $backup_dir = $config['backup_dir'];

        if (!is_dir($backup_dir)) {
            return [
                'total_backups' => 0,
                'total_size' => 0,
                'total_size_formatted' => '0 B',
                'oldest_backup' => null,
                'newest_backup' => null,
                'average_size' => '0 B'
            ];
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

        $count = count($files);

        return [
            'total_backups' => $count,
            'total_size' => $total_size,
            'total_size_formatted' => self::formatBytes($total_size),
            'oldest_backup' => $oldest_time < time() ? date('Y-m-d H:i:s', $oldest_time) : null,
            'newest_backup' => $newest_time > 0 ? date('Y-m-d H:i:s', $newest_time) : null,
            'average_size' => $count > 0 ? self::formatBytes($total_size / $count) : '0 B'
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private static function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check backup directory permissions
     */
    public static function checkBackupPermissions() {
        Auth::requireRole('pengurus');

        $config = self::getBackupConfig();
        $backup_dir = $config['backup_dir'];

        $checks = [
            'directory_exists' => is_dir($backup_dir),
            'directory_writable' => is_writable($backup_dir),
            'logs_writable' => is_writable('../logs'),
            'scripts_executable' => is_executable('../scripts/backup_database.php'),
        ];

        $issues = [];
        if (!$checks['directory_exists']) {
            $issues[] = 'Backup directory tidak ada';
        }
        if (!$checks['directory_writable']) {
            $issues[] = 'Backup directory tidak dapat ditulis';
        }
        if (!$checks['logs_writable']) {
            $issues[] = 'Directory logs tidak dapat ditulis';
        }
        if (!$checks['scripts_executable']) {
            $issues[] = 'Script backup tidak dapat dieksekusi';
        }

        jsonResponse(true, 'Permission check completed', [
            'checks' => $checks,
            'issues' => $issues,
            'ready' => empty($issues)
        ]);
    }
}
?>

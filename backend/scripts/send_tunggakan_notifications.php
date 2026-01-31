<?php
// scripts/send_tunggakan_notifications.php
/**
 * Script untuk mengirim notifikasi tunggakan otomatis
 * 
 * Cara menjalankan:
 * php scripts/send_tunggakan_notifications.php
 * 
 * Atau via cron job (setiap hari jam 9 pagi):
 * 0 9 * * * cd /var/www/html/ksp_polri/backend && php scripts/send_tunggakan_notifications.php >> logs/cron.log 2>&1
 */

require_once '../app/core/Database.php';
require_once '../services/NotificationService.php';

// Set timezone
date_default_timezone_set('Asia/Jakarta');

echo "[" . date('Y-m-d H:i:s') . "] Memulai proses notifikasi tunggakan...\n";

$db = Database::getConnection();
$config = require '../config/email_config.php';

if (!$config['notifications']['tunggakan_enabled']) {
    echo "[INFO] Notifikasi tunggakan dinonaktifkan di config.\n";
    exit(0);
}

$threshold = $config['notifications']['tunggakan_days_threshold'];
$service = new NotificationService();

// Cari angsuran yang menunggak
$query = "
    SELECT 
        a.id as anggota_id,
        a.nama,
        a.nrp,
        p.id as pinjaman_id,
        SUM(ang.jumlah) as total_tunggakan,
        DATEDIFF(CURDATE(), MAX(ang.tanggal_jatuh_tempo)) as hari_terlambat,
        COUNT(ang.id) as jumlah_angsuran_tunggak
    FROM angsuran ang
    JOIN pinjaman p ON ang.pinjaman_id = p.id
    JOIN anggota a ON p.anggota_id = a.id
    WHERE ang.status = 'BELUM_LUNAS'
    AND ang.tanggal_jatuh_tempo < CURDATE() - INTERVAL ? DAY
    AND NOT EXISTS (
        SELECT 1 FROM email_logs el 
        WHERE el.recipient = a.email 
        AND el.subject LIKE '%Tunggakan%'
        AND el.sent_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
    )
    GROUP BY a.id, p.id
    HAVING hari_terlambat >= ?
";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $threshold, $threshold);
$stmt->execute();
$result = $stmt->get_result();

$sent = 0;
$failed = 0;
$total = $result->num_rows;

echo "[INFO] Ditemukan {$total} anggota dengan tunggakan >= {$threshold} hari\n";

while ($row = $result->fetch_assoc()) {
    echo "[PROCESSING] {$row['nama']} ({$row['nrp']}) - Rp " . number_format($row['total_tunggakan'], 0, ',', '.') . " - {$row['hari_terlambat']} hari\n";
    
    $notif_result = $service->sendTunggakanNotification(
        $row['anggota_id'],
        $row['pinjaman_id'],
        $row['total_tunggakan'],
        $row['hari_terlambat']
    );
    
    if ($notif_result['success']) {
        echo "[SUCCESS] Email terkirim ke {$row['nama']}\n";
        $sent++;
    } else {
        echo "[FAILED] Gagal kirim ke {$row['nama']}: {$notif_result['message']}\n";
        $failed++;
    }
    
    // Delay 1 detik untuk menghindari rate limit
    sleep(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Selesai. Terkirim: {$sent}, Gagal: {$failed}\n";

// Simpan ringkasan ke log file
$log_file = '../logs/tunggakan_notifications.log';
$log_dir = dirname($log_file);
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

$log_entry = date('Y-m-d H:i:s') . " | Total: {$total} | Terkirim: {$sent} | Gagal: {$failed}\n";
file_put_contents($log_file, $log_entry, FILE_APPEND);
?>

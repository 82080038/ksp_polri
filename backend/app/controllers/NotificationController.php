<?php
// app/controllers/NotificationController.php
require_once '../services/NotificationService.php';
require_once '../app/views/json.php';

class NotificationController {
    private $notificationService;
    
    public function __construct() {
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Kirim notifikasi tunggakan manual untuk satu anggota
     */
    public static function sendTunggakanManual() {
        $anggota_id = $_POST['anggota_id'];
        $pinjaman_id = $_POST['pinjaman_id'];
        $jumlah = $_POST['jumlah'];
        $hari = $_POST['hari'];
        
        $service = new NotificationService();
        $result = $service->sendTunggakanNotification($anggota_id, $pinjaman_id, $jumlah, $hari);
        
        jsonResponse($result['success'], $result['message']);
    }
    
    /**
     * Proses semua tunggakan dan kirim notifikasi
     */
    public static function processAllTunggakan() {
        $db = Database::getConnection();
        $config = require '../config/email_config.php';
        
        if (!$config['notifications']['tunggakan_enabled']) {
            jsonResponse(false, 'Notifikasi tunggakan dinonaktifkan');
            return;
        }
        
        $threshold = $config['notifications']['tunggakan_days_threshold'];
        $service = new NotificationService();
        
        // Cari angsuran yang menunggak
        $query = "
            SELECT 
                a.id as anggota_id,
                a.nama,
                p.id as pinjaman_id,
                SUM(ang.jumlah) as total_tunggakan,
                DATEDIFF(CURDATE(), MAX(ang.tanggal_jatuh_tempo)) as hari_terlambat
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
        
        while ($row = $result->fetch_assoc()) {
            $notif_result = $service->sendTunggakanNotification(
                $row['anggota_id'],
                $row['pinjaman_id'],
                $row['total_tunggakan'],
                $row['hari_terlambat']
            );
            
            if ($notif_result['success']) {
                $sent++;
            } else {
                $failed++;
            }
        }
        
        jsonResponse(true, "Notifikasi terkirim: {$sent}, Gagal: {$failed}");
    }
    
    /**
     * Kirim notifikasi SHU ke semua anggota
     */
    public static function broadcastShu() {
        $tahun = $_POST['tahun'];
        $db = Database::getConnection();
        $service = new NotificationService();
        
        // Ambil semua SHU untuk tahun tersebut
        $stmt = $db->prepare("
            SELECT anggota_id, jumlah 
            FROM shu 
            WHERE tahun = ?
        ");
        $stmt->bind_param("i", $tahun);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sent = 0;
        $failed = 0;
        
        while ($row = $result->fetch_assoc()) {
            $notif_result = $service->sendShuNotification(
                $row['anggota_id'],
                $tahun,
                $row['jumlah']
            );
            
            if ($notif_result['success']) {
                $sent++;
            } else {
                $failed++;
            }
        }
        
        jsonResponse(true, "Notifikasi SHU terkirim: {$sent}, Gagal: {$failed}");
    }
    
    /**
     * Get log email yang telah dikirim
     */
    public static function getEmailLogs() {
        $db = Database::getConnection();
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("
            SELECT * FROM email_logs 
            ORDER BY sent_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        jsonResponse(true, 'Email logs', $logs);
    }
    
    /**
     * Test kirim email
     */
    public static function testEmail() {
        $to = $_POST['email'];
        $service = new NotificationService();
        
        // Kirim email test sederhana
        $db = Database::getConnection();
        $anggota = ['nama' => 'Test User', 'nrp' => 'TEST123', 'email' => $to];
        
        $subject = 'Test Email - KSP Personel POLRI';
        $body = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Test Email Berhasil!</h2>
            <p>Ini adalah email test dari sistem KSP Personel POLRI.</p>
            <p>Jika Anda menerima email ini, konfigurasi email sudah benar.</p>
        </body>
        </html>
        ";
        
        $result = $service->sendEmail($to, $subject, $body);
        jsonResponse($result['success'], $result['message']);
    }
}
?>

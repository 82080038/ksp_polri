<?php
// services/NotificationService.php

require_once '../config/email_config.php';
require_once '../app/models/Notifikasi.php';

class NotificationService {
    private $config;
    private $db;
    
    public function __construct() {
        $this->config = require '../config/email_config.php';
        $this->db = Database::getConnection();
    }
    
    /**
     * Kirim email tunggakan ke anggota + buat notifikasi UI
     */
    public function sendTunggakanNotification($anggota_id, $pinjaman_id, $jumlah_tunggakan, $jumlah_hari) {
        // Get anggota data
        $anggota = $this->getAnggotaData($anggota_id);
        if (!$anggota) {
            return ['success' => false, 'message' => 'Data anggota tidak ditemukan'];
        }
        
        // Buat notifikasi UI
        $this->createInAppNotification(
            $anggota['user_id'],
            $anggota_id,
            'tunggakan',
            'Peringatan Tunggakan Angsuran',
            "Anda memiliki tunggakan Rp " . number_format($jumlah_tunggakan, 0, ',', '.') . " selama {$jumlah_hari} hari",
            ['pinjaman_id' => $pinjaman_id, 'jumlah' => $jumlah_tunggakan, 'hari' => $jumlah_hari]
        );
        
        // Kirim email jika ada email
        if (!empty($anggota['email'])) {
            $subject = 'Peringatan Tunggakan Angsuran - KSP Personel POLRI';
            $body = $this->getTunggakanTemplate($anggota, $jumlah_tunggakan, $jumlah_hari);
            return $this->sendEmail($anggota['email'], $subject, $body);
        }
        
        return ['success' => true, 'message' => 'Notifikasi UI dibuat, email tidak dikirim (tidak ada email]'];
    }
    
    /**
     * Kirim notifikasi persetujuan pinjaman + buat notifikasi UI
     */
    public function sendPinjamanApprovalNotification($anggota_id, $pinjaman_data) {
        $anggota = $this->getAnggotaData($anggota_id);
        if (!$anggota) {
            return ['success' => false, 'message' => 'Data anggota tidak ditemukan'];
        }
        
        $status_text = $pinjaman_data['status'] == 'DISETUJUI' ? 'disetujui' : 'ditolak';
        
        // Buat notifikasi UI
        $this->createInAppNotification(
            $anggota['user_id'],
            $anggota_id,
            'pinjaman_approval',
            'Status Pinjaman: ' . $pinjaman_data['status'],
            "Pinjaman Rp " . number_format($pinjaman_data['jumlah'], 0, ',', '.') . " telah {$status_text}",
            $pinjaman_data
        );
        
        // Kirim email jika ada
        if (!empty($anggota['email'])) {
            $subject = 'Pinjaman Anda Telah ' . $pinjaman_data['status'] . ' - KSP Personel POLRI';
            $body = $this->getPinjamanTemplate($anggota, $pinjaman_data);
            return $this->sendEmail($anggota['email'], $subject, $body);
        }
        
        return ['success' => true, 'message' => 'Notifikasi UI dibuat'];
    }
    
    /**
     * Kirim notifikasi SHU + buat notifikasi UI
     */
    public function sendShuNotification($anggota_id, $tahun, $jumlah_shu) {
        $anggota = $this->getAnggotaData($anggota_id);
        if (!$anggota) {
            return ['success' => false, 'message' => 'Data anggota tidak ditemukan'];
        }
        
        // Buat notifikasi UI
        $this->createInAppNotification(
            $anggota['user_id'],
            $anggota_id,
            'shu',
            'Pembagian SHU Tahun ' . $tahun,
            "Anda menerima SHU sebesar Rp " . number_format($jumlah_shu, 0, ',', '.'),
            ['tahun' => $tahun, 'jumlah' => $jumlah_shu]
        );
        
        // Kirim email jika ada
        if (!empty($anggota['email'])) {
            $subject = 'Pembagian SHU Tahun ' . $tahun . ' - KSP Personel POLRI';
            $body = $this->getShuTemplate($anggota, $tahun, $jumlah_shu);
            return $this->sendEmail($anggota['email'], $subject, $body);
        }
        
        return ['success' => true, 'message' => 'Notifikasi UI dibuat'];
    }
    
    /**
     * Proses pengiriman email berdasarkan driver yang dipilih
     */
    private function sendEmail($to, $subject, $body) {
        $driver = $this->config['driver'];
        
        switch ($driver) {
            case 'smtp':
                return $this->sendViaSmtp($to, $subject, $body);
            case 'sendgrid':
                return $this->sendViaSendGrid($to, $subject, $body);
            case 'mailgun':
                return $this->sendViaMailgun($to, $subject, $body);
            case 'php_mail':
                return $this->sendViaPhpMail($to, $subject, $body);
            default:
                return ['success' => false, 'message' => 'Driver email tidak dikenali'];
        }
    }
    
    /**
     * Kirim via SMTP (Gmail, dll)
     */
    private function sendViaSmtp($to, $subject, $body) {
        $smtp = $this->config['smtp'];
        
        // Headers untuk email HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: {$smtp['from_name']} <{$smtp['from_email']}>" . "\r\n";
        
        // Gunakan mail() jika tidak ada library SMTP
        // Untuk production, gunakan PHPMailer atau SwiftMailer
        $success = mail($to, $subject, $body, $headers);
        
        // Log pengiriman
        $this->logEmail($to, $subject, $success ? 'sent' : 'failed');
        
        return [
            'success' => $success,
            'message' => $success ? 'Email berhasil dikirim' : 'Gagal mengirim email'
        ];
    }
    
    /**
     * Kirim via SendGrid API
     */
    private function sendViaSendGrid($to, $subject, $body) {
        $sendgrid = $this->config['sendgrid'];
        
        $data = [
            'personalizations' => [
                [
                    'to' => [['email' => $to]]
                ]
            ],
            'from' => [
                'email' => $sendgrid['from_email'],
                'name' => $sendgrid['from_name']
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/html',
                    'value' => $body
                ]
            ]
        ];
        
        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $sendgrid['api_key'],
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $success = $http_code == 202;
        $this->logEmail($to, $subject, $success ? 'sent' : 'failed');
        
        return [
            'success' => $success,
            'message' => $success ? 'Email berhasil dikirim via SendGrid' : 'Gagal: ' . $response
        ];
    }
    
    /**
     * Kirim via Mailgun API
     */
    private function sendViaMailgun($to, $subject, $body) {
        $mailgun = $this->config['mailgun'];
        
        $data = [
            'from' => $mailgun['from_name'] . ' <' . $mailgun['from_email'] . '>',
            'to' => $to,
            'subject' => $subject,
            'html' => $body
        ];
        
        $ch = curl_init('https://api.mailgun.net/v3/' . $mailgun['domain'] . '/messages');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $mailgun['api_key']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $success = $http_code == 200;
        $this->logEmail($to, $subject, $success ? 'sent' : 'failed');
        
        return [
            'success' => $success,
            'message' => $success ? 'Email berhasil dikirim via Mailgun' : 'Gagal: ' . $response
        ];
    }
    
    /**
     * Kirim via PHP mail() function
     */
    private function sendViaPhpMail($to, $subject, $body) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: KSP Personel POLRI <noreply@ksp-polri.local>" . "\r\n";
        
        $success = mail($to, $subject, $body, $headers);
        $this->logEmail($to, $subject, $success ? 'sent' : 'failed');
        
        return [
            'success' => $success,
            'message' => $success ? 'Email berhasil dikirim' : 'Gagal mengirim email'
        ];
    }
    
    /**
     * Get data anggota
     */
    private function getAnggotaData($anggota_id) {
        $stmt = $this->db->prepare("
            SELECT a.*, u.email 
            FROM anggota a 
            LEFT JOIN users u ON a.user_id = u.id 
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * Log email yang dikirim
     */
    private function logEmail($to, $subject, $status) {
        $stmt = $this->db->prepare("
            INSERT INTO email_logs (recipient, subject, status, sent_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("sss", $to, $subject, $status);
        $stmt->execute();
    }
    
    // ==================== TEMPLATE EMAIL ====================
    
    private function getTunggakanTemplate($anggota, $jumlah_tunggakan, $jumlah_hari) {
        $rupiah = number_format($jumlah_tunggakan, 0, ',', '.');
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #d32f2f;'>Peringatan Tunggakan Angsuran</h2>
                <p>Yth. {$anggota['nama']} ({$anggota['nrp']}),</p>
                <p>Kami informasikan bahwa angsuran pinjaman Anda telah menunggak selama <strong>{$jumlah_hari} hari</strong>.</p>
                <div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>
                    <strong>Detail Tunggakan:</strong><br>
                    Jumlah Tunggakan: <strong>Rp {$rupiah}</strong><br>
                    Hari Terlambat: <strong>{$jumlah_hari} hari</strong>
                </div>
                <p>Mohon segera melakukan pembayaran untuk menghindari denda dan sanksi sesuai ketentuan KSP.</p>
                <p>Hubungi pengurus KSP jika ada kesulitan pembayaran.</p>
                <hr style='margin: 30px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    Email ini dikirim otomatis oleh sistem KSP Personel POLRI.<br>
                    Jika ada pertanyaan, silakan hubungi pengurus KSP.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    private function getPinjamanTemplate($anggota, $pinjaman_data) {
        $status_color = $pinjaman_data['status'] == 'DISETUJUI' ? '#4caf50' : '#f44336';
        $rupiah = number_format($pinjaman_data['jumlah'], 0, ',', '.');
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: {$status_color};'>Status Pinjaman</h2>
                <p>Yth. {$anggota['nama']} ({$anggota['nrp']}),</p>
                <p>Pengajuan pinjaman Anda telah <strong style='color: {$status_color};'>{$pinjaman_data['status']}</strong>.</p>
                <div style='background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 20px 0;'>
                    <strong>Detail Pinjaman:</strong><br>
                    Jumlah: <strong>Rp {$rupiah}</strong><br>
                    Tenor: <strong>{$pinjaman_data['tenor']} bulan</strong>
                </div>
                " . ($pinjaman_data['status'] == 'DISETUJUI' ? 
                '<p>Silakan mengambil dana pinjaman di kantor KSP sesuai jadwal yang telah ditentukan.</p>' : 
                '<p>Silakan hubungi pengurus KSP untuk informasi lebih lanjut.</p>') . "
                <hr style='margin: 30px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    Email ini dikirim otomatis oleh sistem KSP Personel POLRI.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    private function getShuTemplate($anggota, $tahun, $jumlah_shu) {
        $rupiah = number_format($jumlah_shu, 0, ',', '.');
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #4caf50;'>Pembagian Sisa Hasil Usaha (SHU)</h2>
                <p>Yth. {$anggota['nama']} ({$anggota['nrp']}),</p>
                <p>Kami informasikan pembagian SHU untuk tahun buku <strong>{$tahun}</strong>.</p>
                <div style='background: #e8f5e9; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                    <div style='font-size: 14px; color: #666;'>Jumlah SHU Anda:</div>
                    <div style='font-size: 32px; color: #4caf50; font-weight: bold;'>Rp {$rupiah}</div>
                </div>
                <p>SHU dapat diambil di kantor KSP atau dipotong untuk simpanan sukarela sesuai keinginan.</p>
                <p>Terima kasih atas partisipasi Anda sebagai anggota KSP Personel POLRI.</p>
                <hr style='margin: 30px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    Email ini dikirim otomatis oleh sistem KSP Personel POLRI.
                </p>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Buat notifikasi in-app
     */
    private function createInAppNotification($user_id, $anggota_id, $tipe, $judul, $pesan, $data = null) {
        $notif = new Notifikasi();
        $data_json = $data ? json_encode($data) : null;
        return $notif->create($user_id, $tipe, $judul, $pesan, $anggota_id, $data_json);
    }
}
?>

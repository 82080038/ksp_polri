<?php
// config/email_config.php

/**
 * Konfigurasi Email untuk KSP POLRI
 * 
 * Opsi Gratis yang tersedia:
 * 1. Gmail SMTP (500 email/hari) - RECOMMENDED untuk development
 * 2. SendGrid (100 email/hari) - RECOMMENDED untuk production
 * 3. Mailgun (5,000/bulan pertama 3 bulan)
 * 
 * Untuk Gmail:
 * - Gunakan App Password (bukan password biasa)
 * - Enable 2FA dulu di akun Google
 * - Buat App Password di: Google Account > Security > App Passwords
 */

return [
    'driver' => 'smtp', // smtp, sendgrid, mailgun, atau php_mail
    
    // SMTP Configuration (Gmail/Generic SMTP)
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls', // tls atau ssl
        'username' => 'ksp.polri.notifications@gmail.com', // GANTI dengan email Anda
        'password' => 'your_app_password_here', // GANTI dengan App Password
        'from_name' => 'KSP Personel POLRI',
        'from_email' => 'ksp.polri.notifications@gmail.com',
    ],
    
    // SendGrid Configuration (Production recommended)
    'sendgrid' => [
        'api_key' => 'SG.your_api_key_here', // GANTI dengan API Key SendGrid
        'from_name' => 'KSP Personel POLRI',
        'from_email' => 'notifications@ksp-polri.go.id',
    ],
    
    // Mailgun Configuration
    'mailgun' => [
        'api_key' => 'key-your_api_key',
        'domain' => 'mg.ksp-polri.go.id',
        'from_name' => 'KSP Personel POLRI',
        'from_email' => 'notifications@mg.ksp-polri.go.id',
    ],
    
    // Notifikasi Settings
    'notifications' => [
        'tunggakan_enabled' => true,
        'tunggakan_days_threshold' => 7, // Notifikasi setelah X hari jatuh tempo
        'shu_enabled' => true,
        'pinjaman_approval_enabled' => true,
    ],
];
?>

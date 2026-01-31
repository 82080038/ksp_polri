<?php
// app/controllers/NotifikasiController.php
require_once '../app/models/Notifikasi.php';
require_once '../app/views/json.php';

class NotifikasiController {
    
    /**
     * Get notifikasi untuk user yang login
     */
    public static function getMyNotifications() {
        $user_id = $_SESSION['user_id'];
        $limit = $_GET['limit'] ?? 20;
        $include_read = isset($_GET['include_read']) ? true : false;
        
        $notif = new Notifikasi();
        $data = $notif->getByUser($user_id, $limit, $include_read);
        $unread_count = $notif->countUnread($user_id);
        
        jsonResponse(true, 'Notifikasi user', [
            'notifications' => $data,
            'unread_count' => $unread_count
        ]);
    }
    
    /**
     * Get jumlah notifikasi belum dibaca (untuk badge/icon)
     */
    public static function getUnreadCount() {
        $user_id = $_SESSION['user_id'];
        $notif = new Notifikasi();
        $count = $notif->countUnread($user_id);
        
        jsonResponse(true, 'Unread count', ['count' => $count]);
    }
    
    /**
     * Tandai notifikasi sebagai dibaca
     */
    public static function markAsRead() {
        $notifikasi_id = $_POST['id'];
        $user_id = $_SESSION['user_id'];
        
        $notif = new Notifikasi();
        if ($notif->markAsRead($notifikasi_id, $user_id)) {
            jsonResponse(true, 'Notifikasi ditandai dibaca');
        } else {
            jsonResponse(false, 'Gagal menandai notifikasi');
        }
    }
    
    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public static function markAllAsRead() {
        $user_id = $_SESSION['user_id'];
        
        $notif = new Notifikasi();
        if ($notif->markAllAsRead($user_id)) {
            jsonResponse(true, 'Semua notifikasi ditandai dibaca');
        } else {
            jsonResponse(false, 'Gagal menandai notifikasi');
        }
    }
    
    /**
     * Dismiss notifikasi
     */
    public static function dismiss() {
        $notifikasi_id = $_POST['id'];
        $user_id = $_SESSION['user_id'];
        
        $notif = new Notifikasi();
        if ($notif->dismiss($notifikasi_id, $user_id)) {
            jsonResponse(true, 'Notifikasi disembunyikan');
        } else {
            jsonResponse(false, 'Gagal menyembunyikan notifikasi');
        }
    }
    
    /**
     * Get preferensi notifikasi
     */
    public static function getPreferences() {
        $user_id = $_SESSION['user_id'];
        
        $notif = new Notifikasi();
        $data = $notif->getPreferences($user_id);
        
        jsonResponse(true, 'Preferensi notifikasi', $data);
    }
    
    /**
     * Update preferensi notifikasi
     */
    public static function updatePreferences() {
        $user_id = $_SESSION['user_id'];
        
        $preferences = [];
        $allowed_fields = ['email_tunggakan', 'email_pinjaman', 'email_shu', 'ui_tunggakan', 'ui_pinjaman', 'ui_shu'];
        
        foreach ($allowed_fields as $field) {
            if (isset($_POST[$field])) {
                $preferences[$field] = $_POST[$field] ? 1 : 0;
            }
        }
        
        $notif = new Notifikasi();
        if ($notif->updatePreferences($user_id, $preferences)) {
            jsonResponse(true, 'Preferensi diperbarui');
        } else {
            jsonResponse(false, 'Gagal memperbarui preferensi');
        }
    }
    
    /**
     * Get semua notifikasi (untuk admin/pengurus)
     */
    public static function getAllNotifications() {
        Auth::requireRole('pengurus');
        
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $notif = new Notifikasi();
        $data = $notif->getAll($limit, $offset);
        
        jsonResponse(true, 'Semua notifikasi', $data);
    }
}
?>

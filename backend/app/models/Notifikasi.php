<?php
// app/models/Notifikasi.php
require_once '../core/Database.php';

class Notifikasi {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Buat notifikasi baru
     */
    public function create($user_id, $tipe, $judul, $pesan, $anggota_id = null, $data_json = null) {
        $stmt = $this->db->prepare("
            INSERT INTO notifikasi (user_id, anggota_id, tipe, judul, pesan, data_json) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $user_id, $anggota_id, $tipe, $judul, $pesan, $data_json);
        return $stmt->execute();
    }

    /**
     * Get notifikasi untuk user tertentu
     */
    public function getByUser($user_id, $limit = 20, $include_read = false) {
        $sql = "
            SELECT * FROM notifikasi 
            WHERE user_id = ? AND is_dismissed = 0
        ";
        if (!$include_read) {
            $sql .= " AND is_read = 0";
        }
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $user_id, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get semua notifikasi (untuk admin/pengurus)
     */
    public function getAll($limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("
            SELECT n.*, u.username, a.nama as nama_anggota 
            FROM notifikasi n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN anggota a ON n.anggota_id = a.id
            ORDER BY n.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead($notifikasi_id, $user_id) {
        $stmt = $this->db->prepare("
            UPDATE notifikasi 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $notifikasi_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Tandai semua notifikasi user sebagai dibaca
     */
    public function markAllAsRead($user_id) {
        $stmt = $this->db->prepare("
            UPDATE notifikasi 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Dismiss notifikasi (sembunyikan tapi tidak hapus)
     */
    public function dismiss($notifikasi_id, $user_id) {
        $stmt = $this->db->prepare("
            UPDATE notifikasi 
            SET is_dismissed = 1 
            WHERE id = ? AND user_id = ?
        ");
        $stmt->bind_param("ii", $notifikasi_id, $user_id);
        return $stmt->execute();
    }

    /**
     * Hitung notifikasi belum dibaca
     */
    public function countUnread($user_id) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM notifikasi 
            WHERE user_id = ? AND is_read = 0 AND is_dismissed = 0
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    /**
     * Hapus notifikasi lama (untuk cleanup)
     */
    public function deleteOld($days = 90) {
        $stmt = $this->db->prepare("
            DELETE FROM notifikasi 
            WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        $stmt->bind_param("i", $days);
        return $stmt->execute();
    }

    // ==================== PREFERENCES ====================

    /**
     * Get preferensi notifikasi user
     */
    public function getPreferences($user_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM user_notification_preferences WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if (!$result) {
            // Buat default preferences
            $this->createDefaultPreferences($user_id);
            return $this->getPreferences($user_id);
        }
        
        return $result;
    }

    /**
     * Buat default preferences
     */
    private function createDefaultPreferences($user_id) {
        $stmt = $this->db->prepare("
            INSERT INTO user_notification_preferences (user_id) VALUES (?)
        ");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Update preferensi
     */
    public function updatePreferences($user_id, $preferences) {
        $fields = [];
        $values = [];
        $types = "";
        
        foreach ($preferences as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
            $types .= "i";
        }
        
        $sql = "UPDATE user_notification_preferences SET " . implode(", ", $fields) . " WHERE user_id = ?";
        $values[] = $user_id;
        $types .= "i";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
}
?>

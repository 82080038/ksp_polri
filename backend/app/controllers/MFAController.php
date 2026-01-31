<?php
// app/controllers/MFAController.php
require_once '../app/models/MFA.php';
require_once '../app/views/json.php';

class MFAController {

    private static function checkRateLimit($key = 'mfa_attempts', $limit = 5, $windowSeconds = 300) {
        if (!isset($_SESSION)) { session_start(); }
        $now = time();
        if (!isset($_SESSION[$key])) { $_SESSION[$key] = []; }
        $_SESSION[$key] = array_filter($_SESSION[$key], fn($ts) => ($now - $ts) <= $windowSeconds);
        if (count($_SESSION[$key]) >= $limit) {
            jsonResponse(false, 'Terlalu banyak percobaan MFA. Coba lagi beberapa menit.');
            exit;
        }
        $_SESSION[$key][] = $now;
    }

    /**
     * Enable MFA for current user
     */
    public static function enableMFA() {
        self::checkRateLimit('mfa_enable');
        $user_id = $_SESSION['user_id'];
        $type = $_POST['type'] ?? 'email';

        $mfa = new MFA();

        // Check if MFA is required for user role
        $user_role = $_SESSION['role'] ?? 'anggota';
        if (!$mfa->isMFARequired($user_role) && $user_role !== 'pengurus') {
            jsonResponse(false, 'MFA hanya diperlukan untuk pengurus/admin');
            return;
        }

        if ($mfa->enableMFA($user_id, $type)) {
            // Generate backup codes
            $backup_codes = $mfa->generateBackupCodes($user_id);

            jsonResponse(true, 'MFA berhasil diaktifkan', [
                'type' => $type,
                'backup_codes' => $backup_codes['codes'] ?? []
            ]);
        } else {
            jsonResponse(false, 'Gagal mengaktifkan MFA');
        }
    }

    /**
     * Disable MFA for current user
     */
    public static function disableMFA() {
        self::checkRateLimit('mfa_disable');
        $user_id = $_SESSION['user_id'];

        $mfa = new MFA();
        if ($mfa->disableMFA($user_id)) {
            jsonResponse(true, 'MFA berhasil dinonaktifkan');
        } else {
            jsonResponse(false, 'Gagal menonaktifkan MFA');
        }
    }

    /**
     * Check MFA status for current user
     */
    public static function getMFAStatus() {
        self::checkRateLimit('mfa_status');
        $user_id = $_SESSION['user_id'];

        $mfa = new MFA();
        $status = $mfa->isMFAEnabled($user_id);

        jsonResponse(true, 'MFA status retrieved', [
            'enabled' => $status !== false,
            'type' => $status['mfa_type'] ?? null
        ]);
    }

    /**
     * Generate and send MFA code
     */
    public static function sendMFAChallenge() {
        self::checkRateLimit('mfa_send');
        $user_id = $_SESSION['user_id'];
        $type = $_POST['type'] ?? null;

        $mfa = new MFA();
        $result = $mfa->generateAndSendCode($user_id, $type);

        jsonResponse($result['success'], $result['message'], [
            'type' => $result['type'] ?? null
        ]);
    }

    /**
     * Verify MFA code
     */
    public static function verifyMFA() {
        self::checkRateLimit('mfa_verify');
        $user_id = $_SESSION['user_id'];
        $code = $_POST['code'] ?? '';
        $type = $_POST['type'] ?? 'email';

        if (empty($code)) {
            jsonResponse(false, 'Kode MFA diperlukan');
            return;
        }

        $mfa = new MFA();

        if ($type === 'authenticator') {
            // Verify TOTP
            $verified = $mfa->verifyTOTP($user_id, $code);
        } elseif ($type === 'backup') {
            // Verify backup code
            $verified = $mfa->verifyBackupCode($user_id, $code);
        } else {
            // Verify regular MFA code
            $verified = $mfa->verifyCode($user_id, $code);
        }

        if ($verified['success'] ?? $verified) {
            // Set MFA verified session
            $_SESSION['mfa_verified'] = true;
            $_SESSION['mfa_verified_time'] = time();

            jsonResponse(true, 'MFA berhasil diverifikasi');
        } else {
            jsonResponse(false, $verified['message'] ?? 'Kode MFA tidak valid');
        }
    }

    /**
     * Get QR code URL for authenticator app
     */
    public static function getQRCode() {
        self::checkRateLimit('mfa_qr');
        $user_id = $_SESSION['user_id'];

        $mfa = new MFA();
        $qr_url = $mfa->getQRCodeUrl($user_id);

        if ($qr_url) {
            jsonResponse(true, 'QR code URL generated', ['qr_url' => $qr_url]);
        } else {
            jsonResponse(false, 'Gagal generate QR code');
        }
    }

    /**
     * Generate new backup codes
     */
    public static function generateBackupCodes() {
        self::checkRateLimit('mfa_backup');
        $user_id = $_SESSION['user_id'];

        $mfa = new MFA();
        $result = $mfa->generateBackupCodes($user_id);

        if ($result['success']) {
            jsonResponse(true, 'Backup codes berhasil digenerate', [
                'backup_codes' => $result['codes']
            ]);
        } else {
            jsonResponse(false, $result['message']);
        }
    }

    /**
     * Get MFA settings for admin
     */
    public static function getMFASettings() {
        Auth::requireRole('pengurus');

        $mfa = new MFA();

        // Get all users with MFA status
        $stmt = $mfa->db->prepare("
            SELECT
                u.id,
                u.username,
                u.email,
                COALESCE(mfa.is_enabled, 0) as mfa_enabled,
                mfa.mfa_type,
                mfa.created_at as mfa_created_at
            FROM users u
            LEFT JOIN mfa_sessions mfa ON u.id = mfa.user_id
            ORDER BY u.username
        ");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        jsonResponse(true, 'MFA settings retrieved', ['users' => $users]);
    }

    /**
     * Force enable MFA for user (admin only)
     */
    public static function forceEnableMFA() {
        Auth::requireRole('pengurus');

        $target_user_id = $_POST['user_id'] ?? 0;
        $type = $_POST['type'] ?? 'email';

        if (!$target_user_id) {
            jsonResponse(false, 'User ID diperlukan');
            return;
        }

        $mfa = new MFA();
        if ($mfa->enableMFA($target_user_id, $type)) {
            jsonResponse(true, 'MFA berhasil diaktifkan untuk user');
        } else {
            jsonResponse(false, 'Gagal mengaktifkan MFA');
        }
    }

    /**
     * Force disable MFA for user (admin only)
     */
    public static function forceDisableMFA() {
        Auth::requireRole('pengurus');

        $target_user_id = $_POST['user_id'] ?? 0;

        if (!$target_user_id) {
            jsonResponse(false, 'User ID diperlukan');
            return;
        }

        $mfa = new MFA();
        if ($mfa->disableMFA($target_user_id)) {
            jsonResponse(true, 'MFA berhasil dinonaktifkan untuk user');
        } else {
            jsonResponse(false, 'Gagal menonaktifkan MFA');
        }
    }

    /**
     * Clean expired MFA codes (admin only)
     */
    public static function cleanExpiredCodes() {
        Auth::requireRole('pengurus');

        $mfa = new MFA();
        if ($mfa->cleanExpiredCodes()) {
            jsonResponse(true, 'Expired MFA codes cleaned');
        } else {
            jsonResponse(false, 'Failed to clean expired codes');
        }
    }

    /**
     * MFA middleware - check if MFA verification is required
     */
    public static function requireMFAVerification() {
        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['role'] ?? 'anggota';

        $mfa = new MFA();

        // Check if MFA is enabled for user
        $mfa_status = $mfa->isMFAEnabled($user_id);
        if (!$mfa_status) {
            return true; // MFA not enabled, allow access
        }

        // Check if MFA verification is still valid (within session timeout)
        if (isset($_SESSION['mfa_verified']) && $_SESSION['mfa_verified']) {
            $audit = new SecurityAudit();
            $session_timeout = $audit->getSetting('session_timeout') ?: 60; // minutes

            if (isset($_SESSION['mfa_verified_time']) &&
                (time() - $_SESSION['mfa_verified_time']) < ($session_timeout * 60)) {
                return true; // MFA still verified
            }
        }

        // MFA verification required
        return false;
    }
}
?>

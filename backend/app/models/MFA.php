<?php
// app/models/MFA.php
require_once '../core/Database.php';
require_once '../services/NotificationService.php';

class MFA {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Enable MFA for user
     */
    public function enableMFA($user_id, $type = 'email') {
        // Generate secret for authenticator app if needed
        $secret = null;
        if ($type === 'authenticator') {
            $secret = $this->generateSecret();
        }

        $stmt = $this->db->prepare("
            INSERT INTO mfa_sessions (user_id, mfa_type, secret, is_enabled)
            VALUES (?, ?, ?, 1)
            ON DUPLICATE KEY UPDATE
                mfa_type = VALUES(mfa_type),
                secret = VALUES(secret),
                is_enabled = 1,
                updated_at = CURRENT_TIMESTAMP
        ");
        $stmt->bind_param("iss", $user_id, $type, $secret);

        return $stmt->execute();
    }

    /**
     * Disable MFA for user
     */
    public function disableMFA($user_id) {
        $stmt = $this->db->prepare("
            UPDATE mfa_sessions SET is_enabled = 0 WHERE user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Check if MFA is enabled for user
     */
    public function isMFAEnabled($user_id) {
        $stmt = $this->db->prepare("
            SELECT is_enabled, mfa_type FROM mfa_sessions
            WHERE user_id = ? AND is_enabled = 1
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result ?: false;
    }

    /**
     * Generate MFA code and send to user
     */
    public function generateAndSendCode($user_id, $type = null) {
        // Get user's MFA settings
        $mfa_settings = $this->isMFAEnabled($user_id);
        if (!$mfa_settings) {
            return ['success' => false, 'message' => 'MFA not enabled for this user'];
        }

        $mfa_type = $type ?: $mfa_settings['mfa_type'];
        $code = $this->generateCode();

        // Save code to database
        $expires_at = date('Y-m-d H:i:s', time() + 300); // 5 minutes

        $stmt = $this->db->prepare("
            INSERT INTO mfa_codes (user_id, code, mfa_type, expires_at)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $user_id, $code, $mfa_type, $expires_at);

        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Failed to generate MFA code'];
        }

        // Send code based on type
        $result = $this->sendCode($user_id, $code, $mfa_type);

        if ($result['success']) {
            return ['success' => true, 'message' => 'MFA code sent successfully', 'type' => $mfa_type];
        } else {
            return $result;
        }
    }

    /**
     * Verify MFA code
     */
    public function verifyCode($user_id, $code) {
        $stmt = $this->db->prepare("
            SELECT id FROM mfa_codes
            WHERE user_id = ? AND code = ? AND expires_at > NOW() AND used_at IS NULL
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->bind_param("is", $user_id, $code);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result) {
            // Mark code as used
            $stmt = $this->db->prepare("UPDATE mfa_codes SET used_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $result['id']);
            $stmt->execute();

            return ['success' => true, 'message' => 'MFA code verified'];
        }

        return ['success' => false, 'message' => 'Invalid or expired MFA code'];
    }

    /**
     * Send MFA code via email or SMS
     */
    private function sendCode($user_id, $code, $type) {
        // Get user email/phone
        $stmt = $this->db->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || empty($user['email'])) {
            return ['success' => false, 'message' => 'User email not found'];
        }

        if ($type === 'email') {
            // Send via email
            $subject = 'Kode Verifikasi MFA - KSP POLRI';
            $body = $this->getEmailMFATemplate($code);

            $notification = new NotificationService();
            return $notification->sendEmail($user['email'], $subject, $body);

        } elseif ($type === 'sms') {
            // Send via SMS (placeholder - would need SMS service integration)
            return ['success' => false, 'message' => 'SMS MFA not implemented yet'];

        } elseif ($type === 'authenticator') {
            // For authenticator apps, code is generated locally
            return ['success' => true, 'message' => 'Code generated for authenticator app'];
        }

        return ['success' => false, 'message' => 'Invalid MFA type'];
    }

    /**
     * Generate random MFA code
     */
    private function generateCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate secret for authenticator app
     */
    private function generateSecret($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Generate QR code URL for authenticator app
     */
    public function getQRCodeUrl($user_id, $app_name = 'KSP POLRI') {
        $mfa_settings = $this->isMFAEnabled($user_id);
        if (!$mfa_settings || $mfa_settings['mfa_type'] !== 'authenticator') {
            return false;
        }

        // Get user info
        $stmt = $this->db->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) return false;

        // Get secret
        $stmt = $this->db->prepare("SELECT secret FROM mfa_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $mfa = $stmt->get_result()->fetch_assoc();

        if (!$mfa || !$mfa['secret']) return false;

        $secret = $mfa['secret'];
        $account_name = urlencode($user['email']);
        $issuer = urlencode($app_name);

        return "otpauth://totp/{$issuer}:{$account_name}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Verify TOTP code from authenticator app
     */
    public function verifyTOTP($user_id, $code) {
        $mfa_settings = $this->isMFAEnabled($user_id);
        if (!$mfa_settings || $mfa_settings['mfa_type'] !== 'authenticator') {
            return false;
        }

        // Get secret
        $stmt = $this->db->prepare("SELECT secret FROM mfa_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $mfa = $stmt->get_result()->fetch_assoc();

        if (!$mfa || !$mfa['secret']) return false;

        $secret = $mfa['secret'];

        // Verify TOTP code with 30-second window
        $current_time = floor(time() / 30);

        for ($i = -1; $i <= 1; $i++) {
            $time = $current_time + $i;
            $expected_code = $this->generateTOTP($secret, $time);
            if ($expected_code === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate TOTP code (RFC 6238)
     */
    private function generateTOTP($secret, $time) {
        $secret = base32_decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hmac = hash_hmac('sha1', $time, $secret, true);
        $offset = ord($hmac[19]) & 0x0F;
        $code = (ord($hmac[$offset]) & 0x7F) << 24 |
                (ord($hmac[$offset + 1]) & 0xFF) << 16 |
                (ord($hmac[$offset + 2]) & 0xFF) << 8 |
                (ord($hmac[$offset + 3]) & 0xFF);
        return str_pad($code % (10 ** 6), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get MFA email template
     */
    private function getEmailMFATemplate($code) {
        return "
        <html>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2196F3;'>Kode Verifikasi Multi-Factor Authentication</h2>
                <p>Yth. Anggota KSP Personel POLRI,</p>
                <p>Anda menerima email ini karena sedang melakukan login ke sistem KSP POLRI dengan Multi-Factor Authentication (MFA) diaktifkan.</p>

                <div style='background: #f5f5f5; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;'>
                    <div style='font-size: 14px; color: #666; margin-bottom: 10px;'>Kode Verifikasi Anda:</div>
                    <div style='font-size: 32px; color: #2196F3; font-weight: bold; letter-spacing: 5px;'>{$code}</div>
                    <div style='font-size: 12px; color: #666; margin-top: 10px;'>Kode ini akan kadaluarsa dalam 5 menit</div>
                </div>

                <p><strong>PENTING:</strong> Jangan bagikan kode ini dengan siapapun. Jika Anda tidak melakukan login, segera hubungi administrator sistem.</p>

                <p>Salam,<br>
                Sistem KSP Personel POLRI</p>

                <hr style='margin: 30px 0;'>
                <p style='font-size: 12px; color: #666;'>
                    Email ini dikirim otomatis oleh sistem KSP POLRI.<br>
                    Jika Anda memiliki pertanyaan, hubungi administrator.
                </p>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Generate backup codes for user
     */
    public function generateBackupCodes($user_id, $count = 10) {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = $this->generateBackupCode();
        }

        $codes_json = json_encode($codes);

        $stmt = $this->db->prepare("
            UPDATE mfa_sessions SET backup_codes = ? WHERE user_id = ?
        ");
        $stmt->bind_param("si", $codes_json, $user_id);

        if ($stmt->execute()) {
            return ['success' => true, 'codes' => $codes];
        }

        return ['success' => false, 'message' => 'Failed to generate backup codes'];
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode($user_id, $code) {
        $stmt = $this->db->prepare("SELECT backup_codes FROM mfa_sessions WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result || !$result['backup_codes']) {
            return false;
        }

        $codes = json_decode($result['backup_codes'], true);
        $index = array_search($code, $codes);

        if ($index !== false) {
            // Remove used code
            unset($codes[$index]);
            $codes_json = json_encode(array_values($codes));

            $stmt = $this->db->prepare("UPDATE mfa_sessions SET backup_codes = ? WHERE user_id = ?");
            $stmt->bind_param("si", $codes_json, $user_id);
            $stmt->execute();

            return true;
        }

        return false;
    }

    /**
     * Generate backup code
     */
    private function generateBackupCode() {
        return strtoupper(bin2hex(random_bytes(4))); // 8-character code
    }

    /**
     * Clean expired MFA codes (should be run by cron job)
     */
    public function cleanExpiredCodes() {
        $stmt = $this->db->prepare("DELETE FROM mfa_codes WHERE expires_at < NOW()");
        return $stmt->execute();
    }

    /**
     * Check if MFA is required for user role
     */
    public function isMFARequired($user_role) {
        // Check security setting
        $audit = new SecurityAudit();
        $required_for_admin = $audit->getSetting('mfa_required_for_admin');

        return ($user_role === 'pengurus' && $required_for_admin == 1);
    }
}

// Base32 decode function (needed for TOTP)
if (!function_exists('base32_decode')) {
    function base32_decode($input) {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $input = strtoupper($input);
        $output = '';

        $buffer = 0;
        $bufferSize = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            if ($char === '=') break;

            $value = strpos($alphabet, $char);
            if ($value === false) continue;

            $buffer = ($buffer << 5) | $value;
            $bufferSize += 5;

            if ($bufferSize >= 8) {
                $output .= chr(($buffer >> ($bufferSize - 8)) & 0xFF);
                $bufferSize -= 8;
            }
        }

        return $output;
    }
}
?>

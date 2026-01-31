<?php
// app/models/SecurityAudit.php
require_once '../core/Database.php';

class SecurityAudit {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Log security vulnerability
     */
    public function logVulnerability($type, $severity, $endpoint, $description, $payload = null, $user_id = null) {
        $stmt = $this->db->prepare("
            INSERT INTO security_audit_logs 
            (vulnerability_type, severity, endpoint, user_agent, ip_address, user_id, description, payload) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssis", $type, $severity, $endpoint, $_SERVER['HTTP_USER_AGENT'] ?? '', $this->getClientIP(), $user_id, $description, $payload);
        return $stmt->execute();
    }

    /**
     * Get security audit logs
     */
    public function getAuditLogs($limit = 100, $offset = 0, $type = null, $severity = null) {
        $sql = "
            SELECT sal.*, u.username 
            FROM security_audit_logs sal
            LEFT JOIN users u ON sal.user_id = u.id
            WHERE 1=1
        ";
        $params = [];
        $types = "";

        if ($type) {
            $sql .= " AND sal.vulnerability_type = ?";
            $params[] = $type;
            $types .= "s";
        }

        if ($severity) {
            $sql .= " AND sal.severity = ?";
            $params[] = $severity;
            $types .= "s";
        }

        $sql .= " ORDER BY sal.detected_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get security settings
     */
    public function getSetting($key) {
        $stmt = $this->db->prepare("SELECT setting_value FROM security_settings WHERE setting_key = ?");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? $result['setting_value'] : null;
    }

    /**
     * Update security setting
     */
    public function updateSetting($key, $value) {
        $stmt = $this->db->prepare("
            UPDATE security_settings SET setting_value = ? WHERE setting_key = ?
        ");
        $stmt->bind_param("ss", $value, $key);
        return $stmt->execute();
    }

    /**
     * Get all security settings
     */
    public function getAllSettings() {
        $stmt = $this->db->prepare("SELECT * FROM security_settings ORDER BY setting_key");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Test SQL Injection vulnerability
     */
    public function testSQLInjection($test_queries = []) {
        $vulnerabilities = [];

        if (empty($test_queries)) {
            $test_queries = [
                "1' OR '1'='1",
                "1; DROP TABLE users; --",
                "admin'--",
                "' UNION SELECT * FROM users--",
                "1 AND 1=1",
                "1 OR 1=1"
            ];
        }

        foreach ($test_queries as $query) {
            // Test login simulation
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
            $stmt->bind_param("s", $query);
            $success = $stmt->execute();

            if ($success) {
                $result = $stmt->get_result()->fetch_assoc();
                // If any of these malicious queries return results, it might indicate vulnerability
                // This is a basic test - real pentesting needs more sophisticated approaches
                if ($result['count'] > 0 && $query !== 'admin') { // admin is a legitimate username
                    $vulnerabilities[] = [
                        'type' => 'sql_injection',
                        'severity' => 'high',
                        'description' => 'Potential SQL injection vulnerability detected',
                        'payload' => $query
                    ];
                }
            }
        }

        return $vulnerabilities;
    }

    /**
     * Test XSS vulnerability
     */
    public function testXSS($test_inputs = []) {
        $vulnerabilities = [];

        if (empty($test_inputs)) {
            $test_inputs = [
                '<script>alert("XSS")</script>',
                '<img src=x onerror=alert("XSS")>',
                'javascript:alert("XSS")',
                '<iframe src="javascript:alert(\'XSS\')"></iframe>',
                '<svg onload=alert("XSS")>',
                '"><script>alert("XSS")</script>'
            ];
        }

        // This would typically test form inputs that get displayed back
        // For now, we'll just log potential XSS patterns in logs
        foreach ($test_inputs as $input) {
            if ($this->containsXSS($input)) {
                $vulnerabilities[] = [
                    'type' => 'xss',
                    'severity' => 'high',
                    'description' => 'Potential XSS payload detected',
                    'payload' => $input
                ];
            }
        }

        return $vulnerabilities;
    }

    /**
     * Test password strength
     */
    public function testPasswordStrength() {
        $issues = [];

        $min_length = $this->getSetting('password_min_length') ?: 8;
        $require_upper = $this->getSetting('password_require_uppercase') ?: 1;
        $require_lower = $this->getSetting('password_require_lowercase') ?: 1;
        $require_numbers = $this->getSetting('password_require_numbers') ?: 1;
        $require_symbols = $this->getSetting('password_require_symbols') ?: 1;

        // Check users with weak passwords
        $stmt = $this->db->prepare("SELECT id, username FROM users");
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($users as $user) {
            // Note: In real implementation, you'd hash passwords and check against common passwords
            // This is just a placeholder for the concept
            $issues[] = [
                'type' => 'weak_password',
                'severity' => 'medium',
                'description' => "Password strength check needed for user: {$user['username']}",
                'user_id' => $user['id']
            ];
        }

        return $issues;
    }

    /**
     * Check if input contains XSS patterns
     */
    private function containsXSS($input) {
        $xss_patterns = [
            '/<script/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/expression\s*\(/i',
            '/vbscript:/i',
            '/data:text\/html/i'
        ];

        foreach ($xss_patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip_headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ip_headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Handle comma-separated IPs (like X-Forwarded-For)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Clean input for security
     */
    public function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }

        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        // Remove potential SQL injection patterns (basic)
        $input = preg_replace('/(\b(union|select|insert|update|delete|drop|create|alter)\b)/i', '', $input);

        return $input;
    }

    /**
     * Generate secure random token
     */
    public function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            return false;
        }

        $token_expiry = $this->getSetting('csrf_token_expiry') ?: 3600;

        if (time() - $_SESSION['csrf_token_time'] > $token_expiry) {
            return false; // Token expired
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = $this->generateSecureToken();
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }
}
?>

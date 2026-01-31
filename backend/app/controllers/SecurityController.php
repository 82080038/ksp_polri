<?php
// app/controllers/SecurityController.php
require_once '../app/models/SecurityAudit.php';
require_once '../app/views/json.php';

class SecurityController {

    /**
     * Run security audit
     */
    public static function runAudit() {
        Auth::requireRole('pengurus');

        $audit = new SecurityAudit();
        $results = [];

        // Test SQL Injection
        $sql_injection_tests = $audit->testSQLInjection();
        if (!empty($sql_injection_tests)) {
            $results['sql_injection'] = $sql_injection_tests;
            foreach ($sql_injection_tests as $test) {
                $audit->logVulnerability(
                    $test['type'],
                    $test['severity'],
                    'security_audit',
                    $test['description'],
                    $test['payload']
                );
            }
        }

        // Test XSS
        $xss_tests = $audit->testXSS();
        if (!empty($xss_tests)) {
            $results['xss'] = $xss_tests;
            foreach ($xss_tests as $test) {
                $audit->logVulnerability(
                    $test['type'],
                    $test['severity'],
                    'security_audit',
                    $test['description'],
                    $test['payload']
                );
            }
        }

        // Test password strength
        $password_tests = $audit->testPasswordStrength();
        if (!empty($password_tests)) {
            $results['password_strength'] = $password_tests;
            foreach ($password_tests as $test) {
                $audit->logVulnerability(
                    $test['type'],
                    $test['severity'],
                    'security_audit',
                    $test['description'],
                    null,
                    $test['user_id']
                );
            }
        }

        // Check security settings
        $settings_issues = self::auditSecuritySettings($audit);
        if (!empty($settings_issues)) {
            $results['settings'] = $settings_issues;
        }

        jsonResponse(true, 'Security audit completed', [
            'total_vulnerabilities' => count($sql_injection_tests) + count($xss_tests) + count($password_tests) + count($settings_issues),
            'results' => $results
        ]);
    }

    /**
     * Audit security settings
     */
    private static function auditSecuritySettings($audit) {
        $issues = [];

        $critical_settings = [
            'password_min_length' => ['value' => 8, 'severity' => 'high'],
            'login_max_attempts' => ['value' => 5, 'severity' => 'medium'],
            'session_timeout' => ['value' => 30, 'severity' => 'medium'],
            'audit_log_enabled' => ['value' => 1, 'severity' => 'high']
        ];

        foreach ($critical_settings as $key => $config) {
            $current_value = $audit->getSetting($key);
            if ($current_value === null || (int)$current_value < $config['value']) {
                $issues[] = [
                    'type' => 'security_setting',
                    'severity' => $config['severity'],
                    'description' => "Security setting '$key' is not properly configured",
                    'recommended' => $config['value']
                ];
            }
        }

        return $issues;
    }

    /**
     * Get security audit logs
     */
    public static function getAuditLogs() {
        Auth::requireRole('pengurus');

        $audit = new SecurityAudit();
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 50;
        $type = $_GET['type'] ?? null;
        $severity = $_GET['severity'] ?? null;
        $offset = ($page - 1) * $limit;

        $logs = $audit->getAuditLogs($limit, $offset, $type, $severity);

        jsonResponse(true, 'Audit logs retrieved', [
            'logs' => $logs,
            'page' => $page,
            'limit' => $limit
        ]);
    }

    /**
     * Get security settings
     */
    public static function getSecuritySettings() {
        Auth::requireRole('pengurus');

        $audit = new SecurityAudit();
        $settings = $audit->getAllSettings();

        jsonResponse(true, 'Security settings retrieved', $settings);
    }

    /**
     * Update security setting
     */
    public static function updateSecuritySetting() {
        Auth::requireRole('pengurus');

        $key = $_POST['key'] ?? '';
        $value = $_POST['value'] ?? '';

        if (empty($key)) {
            jsonResponse(false, 'Setting key is required');
            return;
        }

        $audit = new SecurityAudit();
        if ($audit->updateSetting($key, $value)) {
            jsonResponse(true, 'Security setting updated');
        } else {
            jsonResponse(false, 'Failed to update security setting');
        }
    }

    /**
     * Get CSRF token
     */
    public static function getCSRFToken() {
        $audit = new SecurityAudit();
        $token = $audit->generateCSRFToken();

        jsonResponse(true, 'CSRF token generated', ['token' => $token]);
    }

    /**
     * Test endpoint for security testing
     */
    public static function testEndpoint() {
        // This endpoint is intentionally vulnerable for testing purposes
        // DO NOT USE IN PRODUCTION!

        $input = $_GET['input'] ?? '';
        $audit = new SecurityAudit();

        // Log potential security test
        $audit->logVulnerability(
            'security_test',
            'low',
            'security/test',
            'Security test endpoint accessed',
            $input,
            $_SESSION['user_id'] ?? null
        );

        // Echo back input (potentially vulnerable)
        echo "Input received: " . $input;
    }

    /**
     * Generate security report
     */
    public static function generateSecurityReport() {
        Auth::requireRole('pengurus');

        $audit = new SecurityAudit();

        // Get statistics
        $stmt = $audit->db->prepare("
            SELECT
                COUNT(*) as total_logs,
                vulnerability_type,
                severity,
                COUNT(*) as count
            FROM security_audit_logs
            GROUP BY vulnerability_type, severity
            ORDER BY count DESC
        ");
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        // Get recent vulnerabilities
        $recent = $audit->getAuditLogs(10);

        // Generate report
        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'total_vulnerabilities' => array_sum(array_column($stats, 'count')),
            'statistics' => $stats,
            'recent_vulnerabilities' => $recent,
            'security_settings' => $audit->getAllSettings(),
            'recommendations' => self::getSecurityRecommendations($stats)
        ];

        jsonResponse(true, 'Security report generated', $report);
    }

    /**
     * Get security recommendations based on findings
     */
    private static function getSecurityRecommendations($stats) {
        $recommendations = [];

        foreach ($stats as $stat) {
            switch ($stat['vulnerability_type']) {
                case 'sql_injection':
                    $recommendations[] = [
                        'priority' => 'critical',
                        'title' => 'SQL Injection Protection',
                        'description' => 'Implement prepared statements for all database queries',
                        'action' => 'Review and update all SQL queries to use prepared statements'
                    ];
                    break;

                case 'xss':
                    $recommendations[] = [
                        'priority' => 'high',
                        'title' => 'Cross-Site Scripting (XSS) Protection',
                        'description' => 'Implement output escaping and Content Security Policy',
                        'action' => 'Use htmlspecialchars() for all user input display'
                    ];
                    break;

                case 'weak_password':
                    $recommendations[] = [
                        'priority' => 'high',
                        'title' => 'Password Security',
                        'description' => 'Enforce strong password requirements',
                        'action' => 'Implement password complexity rules and regular updates'
                    ];
                    break;

                case 'csrf':
                    $recommendations[] = [
                        'priority' => 'high',
                        'title' => 'CSRF Protection',
                        'description' => 'Implement CSRF tokens for all forms',
                        'action' => 'Add CSRF token validation to all state-changing operations'
                    ];
                    break;
            }
        }

        // Default recommendations
        if (empty($recommendations)) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Regular Security Audits',
                'description' => 'Perform regular security audits',
                'action' => 'Schedule monthly security audits and vulnerability assessments'
            ];

            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'HTTPS Implementation',
                'description' => 'Implement HTTPS for all communications',
                'action' => 'Configure SSL certificate and enforce HTTPS redirects'
            ];
        }

        return $recommendations;
    }
}
?>

USE ksp_polri;

-- Security Audit Logs Table
CREATE TABLE security_audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vulnerability_type ENUM('sql_injection', 'xss', 'csrf', 'session_hijacking', 'weak_password', 'file_upload', 'rate_limit', 'other') NOT NULL,
  severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
  endpoint VARCHAR(255),
  user_agent TEXT,
  ip_address VARCHAR(45),
  user_id INT,
  description TEXT,
  payload TEXT,
  detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_type (vulnerability_type),
  INDEX idx_severity (severity),
  INDEX idx_detected_at (detected_at),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- MFA (Multi-Factor Authentication) Table
CREATE TABLE mfa_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  mfa_type ENUM('email', 'sms', 'authenticator') NOT NULL,
  secret_key VARCHAR(255), -- Untuk authenticator app
  backup_codes JSON, -- Array backup codes
  is_enabled TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id)
);

-- MFA Verification Codes (temporary)
CREATE TABLE mfa_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  code VARCHAR(10) NOT NULL,
  mfa_type ENUM('email', 'sms', 'authenticator') NOT NULL,
  expires_at TIMESTAMP NOT NULL,
  used_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_id (user_id),
  INDEX idx_expires_at (expires_at)
);

-- Security Settings Table
CREATE TABLE security_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) UNIQUE NOT NULL,
  setting_value TEXT,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_key (setting_key)
);

-- Insert default security settings
INSERT INTO security_settings (setting_key, setting_value, description) VALUES
('password_min_length', '8', 'Minimum password length'),
('password_require_uppercase', '1', 'Require uppercase letters'),
('password_require_lowercase', '1', 'Require lowercase letters'),
('password_require_numbers', '1', 'Require numbers'),
('password_require_symbols', '1', 'Require special symbols'),
('login_max_attempts', '5', 'Maximum login attempts before lockout'),
('login_lockout_duration', '15', 'Lockout duration in minutes'),
('session_timeout', '60', 'Session timeout in minutes'),
('csrf_token_expiry', '3600', 'CSRF token expiry in seconds'),
('rate_limit_requests', '100', 'Rate limit requests per minute'),
('rate_limit_window', '60', 'Rate limit window in seconds'),
('mfa_required_for_admin', '1', 'Require MFA for admin/pengurus roles'),
('audit_log_enabled', '1', 'Enable security audit logging'),
('ip_whitelist_enabled', '0', 'Enable IP whitelisting'),
('maintenance_mode', '0', 'Enable maintenance mode');

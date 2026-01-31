USE ksp_polri;

-- Email Logs Table untuk tracking pengiriman email
CREATE TABLE email_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipient VARCHAR(255) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
  error_message TEXT,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sent_at (sent_at),
  INDEX idx_status (status),
  INDEX idx_recipient (recipient)
);

-- Alter table anggota untuk menambah field email (jika belum ada)
-- ALTER TABLE anggota ADD COLUMN email VARCHAR(255) AFTER nama;

-- Alter table users untuk memastikan email field ada
-- ALTER TABLE users ADD COLUMN email VARCHAR(255) AFTER username;

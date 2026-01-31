CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  aksi VARCHAR(100),
  data_lama TEXT,
  data_baru TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

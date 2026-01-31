USE ksp_polri;

-- In-App Notifications Table untuk notifikasi di UI aplikasi
CREATE TABLE notifikasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  anggota_id INT,
  tipe ENUM('tunggakan', 'pinjaman_approval', 'shu', 'sistem', 'info') DEFAULT 'info',
  judul VARCHAR(255) NOT NULL,
  pesan TEXT NOT NULL,
  data_json TEXT,
  is_read TINYINT DEFAULT 0,
  is_dismissed TINYINT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  read_at TIMESTAMP NULL,
  INDEX idx_user_id (user_id),
  INDEX idx_is_read (is_read),
  INDEX idx_created_at (created_at),
  INDEX idx_tipe (tipe),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE SET NULL
);

-- Tabel untuk preferensi notifikasi user
CREATE TABLE user_notification_preferences (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL UNIQUE,
  email_tunggakan TINYINT DEFAULT 1,
  email_pinjaman TINYINT DEFAULT 1,
  email_shu TINYINT DEFAULT 1,
  ui_tunggakan TINYINT DEFAULT 1,
  ui_pinjaman TINYINT DEFAULT 1,
  ui_shu TINYINT DEFAULT 1,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

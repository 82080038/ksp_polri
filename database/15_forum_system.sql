USE ksp_polri;

-- Forum Anggota Tables

-- Forum Categories
CREATE TABLE forum_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  icon VARCHAR(50) DEFAULT '💬',
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_sort_order (sort_order),
  INDEX idx_is_active (is_active)
);

-- Forum Threads
CREATE TABLE forum_threads (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  author_id INT NOT NULL,
  is_pinned TINYINT DEFAULT 0,
  is_locked TINYINT DEFAULT 0,
  is_sticky TINYINT DEFAULT 0,
  view_count INT DEFAULT 0,
  reply_count INT DEFAULT 0,
  last_reply_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  last_reply_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES anggota(id) ON DELETE CASCADE,
  FOREIGN KEY (last_reply_by) REFERENCES anggota(id) ON DELETE SET NULL,
  INDEX idx_category (category_id),
  INDEX idx_author (author_id),
  INDEX idx_is_pinned (is_pinned),
  INDEX idx_is_sticky (is_sticky),
  INDEX idx_created_at (created_at),
  INDEX idx_last_reply (last_reply_at)
);

-- Forum Posts/Replies
CREATE TABLE forum_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  thread_id INT NOT NULL,
  content TEXT NOT NULL,
  author_id INT NOT NULL,
  parent_post_id INT DEFAULT NULL, -- For nested replies
  is_solution TINYINT DEFAULT 0, -- Mark as solution (if applicable)
  edited_at TIMESTAMP NULL,
  edited_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (thread_id) REFERENCES forum_threads(id) ON DELETE CASCADE,
  FOREIGN KEY (author_id) REFERENCES anggota(id) ON DELETE CASCADE,
  FOREIGN KEY (parent_post_id) REFERENCES forum_posts(id) ON DELETE CASCADE,
  FOREIGN KEY (edited_by) REFERENCES anggota(id) ON DELETE SET NULL,
  INDEX idx_thread (thread_id),
  INDEX idx_author (author_id),
  INDEX idx_parent (parent_post_id),
  INDEX idx_created_at (created_at)
);

-- Thread Subscriptions (for notifications)
CREATE TABLE forum_subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  thread_id INT NOT NULL,
  user_id INT NOT NULL,
  notify_replies TINYINT DEFAULT 1,
  notify_mentions TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (thread_id) REFERENCES forum_threads(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES anggota(id) ON DELETE CASCADE,
  UNIQUE KEY unique_subscription (thread_id, user_id),
  INDEX idx_thread (thread_id),
  INDEX idx_user (user_id)
);

-- User Forum Stats
CREATE TABLE forum_user_stats (
  anggota_id INT PRIMARY KEY,
  thread_count INT DEFAULT 0,
  post_count INT DEFAULT 0,
  reputation INT DEFAULT 0,
  last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE
);

-- Insert default categories
INSERT INTO forum_categories (name, description, icon, sort_order) VALUES
('Pengumuman', 'Pengumuman resmi dari pengurus KSP POLRI', '📢', 1),
('Diskusi Umum', 'Diskusi umum antar anggota', '💬', 2),
('Pertanyaan & Jawaban', 'Tanya jawab seputar KSP dan simpan pinjam', '❓', 3),
('Saran & Masukan', 'Saran dan masukan untuk kemajuan KSP', '💡', 4),
('Cerita Anggota', 'Berbagi cerita dan pengalaman sebagai anggota KSP', '📖', 5),
('Off Topic', 'Diskusi di luar topik KSP (dengan batas)', '🎭', 6);

-- Insert sample thread for testing
INSERT INTO forum_threads (category_id, title, content, author_id, created_at) VALUES
(1, 'Selamat Datang di Forum Anggota KSP POLRI', 'Selamat datang di forum komunikasi internal anggota KSP Personel POLRI. Forum ini dibuat untuk memfasilitasi komunikasi, diskusi, dan berbagi informasi antar anggota.\n\n**Aturan Forum:**\n- Hormati sesama anggota\n- Jaga kerahasiaan data pribadi\n- Diskusi on-topic sesuai kategori\n- Laporkan konten yang tidak pantas\n\nSelamat berdiskusi!', 1, NOW());

INSERT INTO forum_posts (thread_id, content, author_id, created_at) VALUES
(1, 'Terima kasih atas forum yang sangat berguna ini. Semoga bisa membantu komunikasi antar anggota.', 1, NOW());

USE ksp_polri;

-- RAT (Rapat Anggota Tahunan) Voting System Tables

-- RAT Agenda Table
CREATE TABLE rat_agenda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tahun YEAR NOT NULL,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  tanggal_mulai DATETIME NOT NULL,
  tanggal_selesai DATETIME NOT NULL,
  status ENUM('draft', 'active', 'completed', 'cancelled') DEFAULT 'draft',
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_tahun (tahun),
  INDEX idx_status (status),
  INDEX idx_tanggal_mulai (tanggal_mulai)
);

-- Voting Topics/Items within RAT Agenda
CREATE TABLE voting_topics (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rat_agenda_id INT NOT NULL,
  judul VARCHAR(255) NOT NULL,
  deskripsi TEXT,
  tipe ENUM('single_choice', 'multiple_choice', 'yes_no', 'ranking') DEFAULT 'single_choice',
  options JSON, -- Store voting options as JSON array
  required_quorum INT DEFAULT 0, -- Minimum votes required
  is_active TINYINT DEFAULT 1,
  voting_start DATETIME,
  voting_end DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rat_agenda_id) REFERENCES rat_agenda(id) ON DELETE CASCADE,
  INDEX idx_rat_agenda (rat_agenda_id),
  INDEX idx_is_active (is_active),
  INDEX idx_voting_start (voting_start)
);

-- Votes Table
CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voting_topic_id INT NOT NULL,
  anggota_id INT NOT NULL,
  pilihan JSON NOT NULL, -- Store vote choices as JSON
  voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45),
  user_agent TEXT,
  is_verified TINYINT DEFAULT 0, -- For additional verification if needed
  FOREIGN KEY (voting_topic_id) REFERENCES voting_topics(id) ON DELETE CASCADE,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
  INDEX idx_voting_topic (voting_topic_id),
  INDEX idx_anggota (anggota_id),
  INDEX idx_voted_at (voted_at),
  UNIQUE KEY unique_vote (voting_topic_id, anggota_id) -- One vote per topic per member
);

-- Voting Sessions (for real-time voting management)
CREATE TABLE voting_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  voting_topic_id INT NOT NULL,
  session_token VARCHAR(255) UNIQUE NOT NULL,
  started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ended_at TIMESTAMP NULL,
  is_active TINYINT DEFAULT 1,
  FOREIGN KEY (voting_topic_id) REFERENCES voting_topics(id) ON DELETE CASCADE,
  INDEX idx_session_token (session_token),
  INDEX idx_is_active (is_active)
);

-- Voting Results Cache (for performance)
CREATE TABLE voting_results_cache (
  voting_topic_id INT PRIMARY KEY,
  total_votes INT DEFAULT 0,
  results JSON, -- Cached vote counts
  last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (voting_topic_id) REFERENCES voting_topics(id) ON DELETE CASCADE
);

-- RAT Attendance/Participation Tracking
CREATE TABLE rat_participants (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rat_agenda_id INT NOT NULL,
  anggota_id INT NOT NULL,
  status ENUM('registered', 'attended', 'absent') DEFAULT 'registered',
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  attended_at TIMESTAMP NULL,
  FOREIGN KEY (rat_agenda_id) REFERENCES rat_agenda(id) ON DELETE CASCADE,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE CASCADE,
  INDEX idx_rat_agenda (rat_agenda_id),
  INDEX idx_anggota (anggota_id),
  UNIQUE KEY unique_participant (rat_agenda_id, anggota_id)
);

-- Insert sample data for testing
INSERT INTO rat_agenda (tahun, judul, deskripsi, tanggal_mulai, tanggal_selesai, status, created_by) VALUES
(2024, 'RAT KSP POLRI 2024', 'Rapat Anggota Tahunan KSP Personel POLRI Tahun 2024', '2024-12-15 08:00:00', '2024-12-15 17:00:00', 'completed', 1);

INSERT INTO voting_topics (rat_agenda_id, judul, deskripsi, tipe, options, required_quorum, voting_start, voting_end) VALUES
(1, 'Persetujuan Laporan Keuangan 2024', 'Apakah anggota menyetujui laporan keuangan tahun buku 2024?', 'yes_no', '["Ya", "Tidak"]', 10, '2024-12-15 10:00:00', '2024-12-15 12:00:00'),
(1, 'Pengurusan Simpanan Wajib', 'Pilih pengurusan simpanan wajib yang diinginkan:', 'single_choice', '["Tetap sama", "Ditingkatkan 10%", "Diturunkan 5%", "Dihapuskan"]', 15, '2024-12-15 13:00:00', '2024-12-15 15:00:00'),
(1, 'Pengangkatan Pengurus Baru', 'Apakah setuju dengan susunan pengurus baru periode 2025-2027?', 'yes_no', '["Setuju", "Tidak Setuju"]', 20, '2024-12-15 15:30:00', '2024-12-15 16:30:00');

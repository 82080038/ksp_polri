CREATE TABLE dokumen_hash (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_dokumen VARCHAR(50),
  jenis_dokumen VARCHAR(50),
  tahun INT,
  hash VARCHAR(64),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  dibuat_oleh INT,
  FOREIGN KEY (dibuat_oleh) REFERENCES users(id)
);

-- Seed data for roles
INSERT INTO roles (name) VALUES ('anggota'), ('pengurus'), ('pengawas'), ('admin');

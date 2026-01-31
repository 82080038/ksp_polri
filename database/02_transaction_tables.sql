CREATE TABLE simpanan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anggota_id INT,
  jenis ENUM('POKOK','WAJIB','SUKARELA'),
  jumlah DECIMAL(15,2),
  tanggal DATE,
  created_by INT,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE pinjaman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anggota_id INT,
  jumlah DECIMAL(15,2),
  tenor INT,
  bunga DECIMAL(5,2),
  status ENUM('DIAJUKAN','DISETUJUI','DITOLAK','LUNAS'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE angsuran (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pinjaman_id INT,
  jumlah DECIMAL(15,2),
  tanggal DATE,
  FOREIGN KEY (pinjaman_id) REFERENCES pinjaman(id)
);

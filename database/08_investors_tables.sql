USE ksp_polri;

-- Investors & Capital Tables
CREATE TABLE investors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(255),
  jenis ENUM('ANGGOTA','PIHAK_KETIGA','PENGURUS','PENGAWAS'),
  npwp VARCHAR(50),
  alamat TEXT,
  telepon VARCHAR(50),
  email VARCHAR(100),
  dokumen_path VARCHAR(255),
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE capital_investments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  investor_id INT,
  nomor_perjanjian VARCHAR(100),
  besar_modal DECIMAL(15,2),
  tanggal_penyertaan DATE,
  tanggal_berakhir DATE,
  persentase_kepemilikan DECIMAL(5,2),
  syarat_ketentuan TEXT,
  dokumen_perjanjian_path VARCHAR(255),
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (investor_id) REFERENCES investors(id)
);

CREATE TABLE capital_changes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  investment_id INT,
  jenis ENUM('PENAMBAHAN','PENGURANGAN'),
  jumlah DECIMAL(15,2),
  tanggal DATE,
  alasan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (investment_id) REFERENCES capital_investments(id)
);

CREATE TABLE profit_distributions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  periode VARCHAR(50),
  tanggal_distribusi DATE,
  total_keuntungan DECIMAL(15,2),
  shu_anggota DECIMAL(15,2),
  dividen_investor DECIMAL(15,2),
  cadangan_koperasi DECIMAL(15,2),
  status ENUM('PENDING','APPROVED','COMPLETED'),
  approved_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE member_shu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  distribution_id INT,
  member_id INT,
  shu_dari_transaksi DECIMAL(15,2),
  shu_dari_partisipasi DECIMAL(15,2),
  total_shu DECIMAL(15,2),
  status_pembayaran ENUM('PENDING','PAID'),
  tanggal_bayar DATE,
  metode_pembayaran VARCHAR(50),
  bukti_pembayaran_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (distribution_id) REFERENCES profit_distributions(id),
  FOREIGN KEY (member_id) REFERENCES anggota(id)
);

CREATE TABLE investor_dividends (
  id INT AUTO_INCREMENT PRIMARY KEY,
  distribution_id INT,
  investor_id INT,
  investment_id INT,
  persentase_dividen DECIMAL(5,2),
  jumlah_dividen DECIMAL(15,2),
  status_pembayaran ENUM('PENDING','PAID'),
  tanggal_bayar DATE,
  metode_pembayaran VARCHAR(50),
  bukti_pembayaran_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (distribution_id) REFERENCES profit_distributions(id),
  FOREIGN KEY (investor_id) REFERENCES investors(id),
  FOREIGN KEY (investment_id) REFERENCES capital_investments(id)
);

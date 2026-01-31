USE ksp_polri;

-- Accounting & Finance Tables
CREATE TABLE chart_of_accounts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_akun VARCHAR(20) UNIQUE,
  nama_akun VARCHAR(255),
  kategori VARCHAR(50),
  parent_id INT NULL,
  saldo_awal DECIMAL(15,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id)
);

CREATE TABLE journal_entries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE,
  nomor_jurnal VARCHAR(50) UNIQUE,
  deskripsi TEXT,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE journal_entry_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  journal_entry_id INT,
  account_id INT,
  debit DECIMAL(15,2) DEFAULT 0,
  kredit DECIMAL(15,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id),
  FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id)
);

CREATE TABLE general_ledger (
  id INT AUTO_INCREMENT PRIMARY KEY,
  account_id INT,
  tanggal DATE,
  debit DECIMAL(15,2) DEFAULT 0,
  kredit DECIMAL(15,2) DEFAULT 0,
  saldo DECIMAL(15,2),
  reference_type VARCHAR(50),
  reference_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id)
);

CREATE TABLE fixed_assets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_aset VARCHAR(50) UNIQUE,
  nama_aset VARCHAR(255),
  kategori VARCHAR(100),
  nilai_perolehan DECIMAL(15,2),
  tanggal_perolehan DATE,
  metode_depresiasi VARCHAR(50),
  umur_ekonomis INT,
  nilai_buku DECIMAL(15,2),
  status ENUM('ACTIVE','INACTIVE'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE asset_depreciations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  asset_id INT,
  periode VARCHAR(20),
  nilai_depresiasi DECIMAL(15,2),
  nilai_buku_setelah DECIMAL(15,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (asset_id) REFERENCES fixed_assets(id)
);

USE ksp_polri;

-- Suppliers & Partners Tables
CREATE TABLE suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_perusahaan VARCHAR(255),
  npwp VARCHAR(50),
  alamat TEXT,
  telepon VARCHAR(50),
  email VARCHAR(100),
  kategori VARCHAR(100),
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  rating DECIMAL(3,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE partners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_perusahaan VARCHAR(255),
  jenis_mitra VARCHAR(100),
  npwp VARCHAR(50),
  alamat TEXT,
  telepon VARCHAR(50),
  email VARCHAR(100),
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE contracts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  partner_id INT,
  nomor_kontrak VARCHAR(100),
  tanggal_mulai DATE,
  tanggal_berakhir DATE,
  nilai_kontrak DECIMAL(15,2),
  syarat_ketentuan TEXT,
  dokumen_path VARCHAR(255),
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (partner_id) REFERENCES partners(id)
);

CREATE TABLE purchase_orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_po VARCHAR(50) UNIQUE,
  supplier_id INT,
  tanggal_po DATE,
  tanggal_pengiriman DATE,
  total_nilai DECIMAL(15,2),
  syarat_pembayaran TEXT,
  status ENUM('PENDING','APPROVED','COMPLETED','CANCELLED'),
  approved_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
  FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE purchase_order_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  po_id INT,
  produk_id INT,
  qty INT,
  harga_satuan DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
  FOREIGN KEY (produk_id) REFERENCES produk(id)
);

CREATE TABLE supplier_invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  po_id INT,
  supplier_id INT,
  nomor_invoice VARCHAR(50),
  tanggal_invoice DATE,
  total_nilai DECIMAL(15,2),
  status_pembayaran ENUM('PENDING','PAID','OVERDUE'),
  tanggal_jatuh_tempo DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

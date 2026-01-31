USE ksp_polri;

-- Agents & Sales Tables
CREATE TABLE agents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  partner_id INT NULL,
  jenis_agen ENUM('ANGGOTA','PENGURUS','PENGAWAS','PIHAK_KETIGA'),
  wilayah_penjualan VARCHAR(255),
  komisi_persen DECIMAL(5,2),
  batas_kredit DECIMAL(15,2) DEFAULT 0,
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (partner_id) REFERENCES partners(id)
);

CREATE TABLE agent_sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  agent_id INT,
  nomor_transaksi VARCHAR(50) UNIQUE,
  tanggal_penjualan DATE,
  pelanggan_nama VARCHAR(255),
  pelanggan_alamat TEXT,
  pelanggan_telp VARCHAR(50),
  total_nilai DECIMAL(15,2),
  komisi DECIMAL(15,2),
  status_approval ENUM('PENDING','APPROVED','REJECTED'),
  bukti_transaksi_path VARCHAR(255),
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id),
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE agent_sales_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  agent_sale_id INT,
  produk_id INT,
  qty INT,
  harga_jual DECIMAL(15,2),
  subtotal DECIMAL(15,2),
  komisi_item DECIMAL(15,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_sale_id) REFERENCES agent_sales(id),
  FOREIGN KEY (produk_id) REFERENCES produk(id)
);

CREATE TABLE agent_commissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  agent_id INT,
  agent_sale_id INT,
  periode VARCHAR(50),
  total_penjualan DECIMAL(15,2),
  total_komisi DECIMAL(15,2),
  status_pembayaran ENUM('PENDING','PAID'),
  tanggal_bayar DATE,
  metode_pembayaran VARCHAR(50),
  bukti_pembayaran VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (agent_id) REFERENCES agents(id),
  FOREIGN KEY (agent_sale_id) REFERENCES agent_sales(id)
);

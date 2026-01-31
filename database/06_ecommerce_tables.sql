USE ksp_polri;

-- E-Commerce Tables
CREATE TABLE produk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_produk VARCHAR(50) UNIQUE,
  nama_produk VARCHAR(255),
  kategori_id INT,
  harga DECIMAL(15,2),
  stok INT DEFAULT 0,
  foto VARCHAR(255),
  deskripsi TEXT,
  status ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100),
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomor_order VARCHAR(50) UNIQUE,
  customer_id INT,
  customer_type ENUM('ANGGOTA','UMUM','PENGURUS','PENGAWAS'),
  tanggal_order DATE,
  total_harga DECIMAL(15,2),
  total_biaya_operasional DECIMAL(15,2),
  total_bayar DECIMAL(15,2),
  metode_pengambilan ENUM('DI_TOKO','LOKASI_PIHAK_KETIGA','KIRIM_ALAMAT'),
  lokasi_pengambilan_id INT,
  alamat_pengiriman TEXT,
  metode_pembayaran VARCHAR(50),
  status_pembayaran ENUM('PENDING','PAID','FAILED'),
  status_order ENUM('PENDING','DIPROSES','SIAP_DIAMBIL','DIKIRIM','SELESAI','DIBATALKAN'),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE order_details (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  produk_id INT,
  qty INT,
  harga_satuan DECIMAL(15,2),
  diskon DECIMAL(15,2) DEFAULT 0,
  subtotal DECIMAL(15,2),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (produk_id) REFERENCES produk(id)
);

CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  produk_id INT,
  qty INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (produk_id) REFERENCES produk(id)
);

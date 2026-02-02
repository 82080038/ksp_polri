-- Migration: Add koperasi table and address columns
-- Database: ksp_polri

-- Tabel Koperasi
CREATE TABLE IF NOT EXISTS koperasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_koperasi VARCHAR(255) NOT NULL,
    kode_koperasi VARCHAR(50) UNIQUE,
    npwp VARCHAR(50),
    telepon VARCHAR(50),
    email VARCHAR(100),
    
    -- Alamat dari alamat_db (hanya menyimpan ID)
    Prop_desa INT COMMENT 'ID desa/kelurahan dari alamat_db',
    alamat_lengkap TEXT COMMENT 'Alamat lengkap tambahan',
    
    -- Data pimpinan
    nama_pimpinan VARCHAR(100),
    jabatan_pimpinan VARCHAR(50),
    
    status ENUM('AKTIF', 'NONAKTIF') DEFAULT 'AKTIF',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tambah kolom alamat ke tabel users
ALTER TABLE users 
ADD COLUMN Prop_desa INT COMMENT 'ID desa/kelurahan dari alamat_db',
ADD COLUMN alamat_lengkap TEXT COMMENT 'Alamat lengkap tambahan',
ADD COLUMN koperasi_id INT COMMENT 'ID koperasi yang terdaftar',
ADD COLUMN nama_lengkap VARCHAR(100),
ADD COLUMN email VARCHAR(100),
ADD COLUMN telepon VARCHAR(50);

-- Tambah foreign key untuk koperasi_id di users
ALTER TABLE users 
ADD CONSTRAINT fk_user_koperasi 
FOREIGN KEY (koperasi_id) REFERENCES koperasi(id) 
ON DELETE SET NULL;

-- Tambah kolom alamat ke tabel anggota (jika diperlukan)
ALTER TABLE anggota 
ADD COLUMN Prop_desa INT COMMENT 'ID desa/kelurahan dari alamat_db',
ADD COLUMN alamat_lengkap TEXT COMMENT 'Alamat lengkap tambahan';

-- Insert data koperasi awal (untuk testing)
-- Hapus baris ini jika tidak ingin data awal
INSERT INTO koperasi (nama_koperasi, kode_koperasi, npwp, telepon, email, nama_pimpinan, jabatan_pimpinan, status) 
VALUES (
    'KSP POLRI Demo',
    'KSP001',
    '00.000.000.0-000.000',
    '021-12345678',
    'info@ksppolri.demo',
    'Drs. Demo Pimpinan',
    'Ketua',
    'AKTIF'
) ON DUPLICATE KEY UPDATE nama_koperasi = nama_koperasi;

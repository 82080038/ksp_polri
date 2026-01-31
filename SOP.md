SOP penggunaan **Sistem RAT KSP POLRI** sudah **saya susun lengkap, formal, dan siap diberlakukan** 📘
Bahasanya **administratif–institusional**, aman untuk:

* ✔️ Surat keputusan pengurus
* ✔️ Lampiran proposal ke pimpinan
* ✔️ Audit internal / eksternal
* ✔️ Sosialisasi ke pengurus & pengawas

### Ciri SOP ini

* Alur **jelas dari pra-RAT → generate → pengesahan → arsip**
* Role **Ketua, Pengurus, Pengawas, Admin** tegas
* Selaras dengan sistem **QR + tanda tangan digital**
* Tidak bergantung orang → **berbasis sistem**

# 📘 BLUEPRINT PROYEK APLIKASI KSP PERSONEL POLRI

## 1. Gambaran Umum

Aplikasi **KSP Personel POLRI** adalah sistem informasi koperasi simpan pinjam yang dirancang khusus untuk lingkungan internal Kepolisian Republik Indonesia. Sistem ini menyesuaikan **struktur keanggotaan, pola gaji, mutasi dinas, dan mekanisme RAT** yang khas di POLRI.

Tujuan utama:

* Transparansi keuangan
* Akuntabilitas pengurus
* Kemudahan anggota
* Kesiapan audit internal

---

## 2. Ruang Lingkup Fitur

### 2.1 Keanggotaan

* Registrasi anggota berbasis **NRP**
* Status dinas (aktif, mutasi, pensiun, keluar)
* Riwayat anggota tidak pernah dihapus

### 2.2 Simpanan

* Simpanan pokok (1x)
* Simpanan wajib (bulanan / potong gaji)
* Simpanan sukarela

### 2.3 Pinjaman

* Pengajuan pinjaman
* Validasi otomatis (status dinas, plafon, tenor)
* Persetujuan pengurus
* Jadwal angsuran otomatis

### 2.4 Angsuran

* Pembayaran angsuran
* Deteksi tunggakan
* Monitoring pinjaman aktif

### 2.5 SHU

* Perhitungan SHU otomatis per tahun buku
* Distribusi SHU per anggota
* Simulasi sebelum RAT

### 2.6 RAT

* Penyusunan laporan RAT
* Pengesahan SHU
* Penguncian data tahun buku

### 2.7 Audit & Keamanan

* Audit log semua perubahan data
* Hak akses berbasis role
* Data historis immutable

---

## 3. Struktur Peran (Role)

| Role     | Deskripsi                                 |
| -------- | ----------------------------------------- |
| Anggota  | Melihat data pribadi & pengajuan pinjaman |
| Pengurus | Mengelola operasional koperasi            |
| Pengawas | Audit & monitoring (read-only)            |
| Admin    | Teknis sistem & konfigurasi               |

---

## 4. Arsitektur Sistem

### 4.1 Arsitektur Umum

```
Frontend (Web)
   ↓ API
Backend (Node.js / Express)
   ↓
Database (MySQL)
```

### 4.2 Prinsip Arsitektur

* Modular
* Multi-tahun buku
* Audit-first design
* Soft delete

---

## 5. Struktur Database (Ringkas)

Entitas utama:

* anggota
* users
* roles
* simpanan
* pinjaman
* angsuran
* transaksi_keuangan
* shu_rekap
* shu_anggota
* rat
* audit_log

Relasi utama:

```
anggota -> simpanan
anggota -> pinjaman -> angsuran
anggota -> shu_anggota -> shu_rekap -> rat
users -> roles
users -> audit_log
```

---

## 6. Struktur Folder Backend

```
backend/
├── app.js
├── config/
├── routes/
├── controllers/
├── services/
├── models/
├── middlewares/
├── migrations/
└── utils/
```

---

## 7. API Utama (Ringkas)

### Auth

* POST /api/login

### Anggota

* GET /api/anggota
* POST /api/anggota
* PATCH /api/anggota/:id/status

### Simpanan

* POST /api/simpanan
* GET /api/simpanan/anggota/:id

### Pinjaman

* POST /api/pinjaman
* POST /api/pinjaman/:id/approve

### Angsuran

* POST /api/angsuran/bayar

### SHU

* POST /api/shu/hitung
* GET /api/shu/tahun/:tahun

### RAT

* POST /api/rat
* POST /api/rat/:id/sahkan

---

## 8. Flow Frontend (Ringkas)

### Anggota

Login → Dashboard → Simpanan → Pinjaman → SHU → Profil

### Pengurus

Dashboard → Anggota → Simpanan → Pinjaman → SHU → RAT

### Pengawas

Dashboard → Laporan → Audit Log → RAT

### Admin

User → Parameter → Tahun Buku → Backup

---

## 9. Keamanan & Audit

* JWT Authentication
* Role-based Access Control
* Semua perubahan data dicatat di audit_log

---

## 10. Roadmap Pengembangan

### Tahap 1 (Core)

* Keanggotaan
* Simpanan
* Pinjaman

### Tahap 2 (Governance)

* SHU
* RAT
* Audit

### Tahap 3 (Lanjutan)

* Dashboard analitik
* Notifikasi tunggakan
* Mobile App

---

## 11. Penutup

Blueprint ini dirancang agar aplikasi KSP Personel POLRI:

* Siap produksi
* Tidak kehilangan fitur penting
* Mudah dikembangkan jangka panjang
* Aman secara organisasi & audit

---

📌 *Dokumen ini menjadi acuan utama pengembangan. Semua perubahan sistem harus merujuk pada blueprint ini.*

---

## 12. Middleware Keamanan & Role Enforcement

### 12.1 Authentication

* JWT-based authentication
* Token berisi: user_id, role, anggota_id

### 12.2 Authorization

Setiap endpoint divalidasi oleh middleware:

* authMiddleware → validasi token
* roleMiddleware → validasi hak akses

Contoh aturan:

* Anggota hanya boleh akses data sendiri
* Pengawas read-only
* Pengurus transaksi

---

## 13. Query Laporan Penting

### 13.1 Laporan Bulanan

* Simpanan per bulan
* Angsuran masuk
* Tunggakan

### 13.2 Laporan Tahunan

* Neraca
* Perhitungan SHU
* Rekap pinjaman

### 13.3 Laporan RAT

* Laporan pengurus
* Laporan pengawas
* Distribusi SHU per anggota

---

## 14. Simulasi 1 Tahun Operasional KSP

Skenario:

* 100 anggota aktif
* 80 anggota simpanan wajib rutin
* 40 anggota pinjaman aktif
* 5 anggota mutasi

Hasil simulasi:

* Sistem tetap konsisten
* SHU terhitung otomatis
* Anggota mutasi tetap dapat SHU proporsional

---

## 15. SOP Penggunaan Sistem

### 15.1 SOP Anggota

* Login & cek saldo
* Ajukan pinjaman
* Pantau angsuran

### 15.2 SOP Pengurus

* Input simpanan bulanan
* Approval pinjaman
* Tutup buku

### 15.3 SOP Pengawas

* Audit laporan
* Cek log transaksi

---

## 16. Checklist Kesiapan Audit

* Data anggota lengkap
* Tidak ada transaksi tanpa log
* SHU terdokumentasi
* RAT tersimpan rapi
* Hak akses sesuai

---

## 17. Roadmap Teknis Lanjutan

* Mobile App (Android)
* Integrasi potong gaji
* Notifikasi WhatsApp
* Dashboard analitik

---

## 18. Status Dokumen

Dokumen ini bersifat:

* Final untuk pengembangan tahap awal
* Living document untuk pengembangan lanjutan

🟢 **STATUS: SIAP PRODUKSI**

---

## 19. Desain UI Konkret (Wireframe per Halaman)

> Desain UI bersifat **fungsional, sederhana, dan formal**, menyesuaikan lingkungan internal POLRI.

### 19.1 Halaman Login

```
+----------------------------------+
|  LOGO KOPERASI POLRI              |
|----------------------------------|
|  Username / NRP                  |
|  Password                        |
|  [ LOGIN ]                       |
+----------------------------------+
```

---

### 19.2 Dashboard Anggota

```
[ Total Simpanan ]   [ Pinjaman Aktif ]
[ Angsuran Bulan Ini ][ Estimasi SHU ]

Menu:
- Simpanan Saya
- Pinjaman Saya
- Ajukan Pinjaman
- SHU & RAT
- Profil (Read-only)
```

---

### 19.3 Dashboard Pengurus

```
[ Total Kas ] [ Pinjaman Berjalan ] [ Tunggakan ]

Menu:
- Data Anggota
- Simpanan Bulanan
- Approval Pinjaman
- Angsuran
- SHU
- RAT
- Laporan
```

---

### 19.4 Dashboard Pengawas

```
[ Ringkasan Keuangan ] [ Status SHU ]

Menu:
- Laporan Keuangan
- Transaksi Detail
- Audit Log
- RAT
```

---

### 19.5 Form Pengajuan Pinjaman

```
Nominal Pinjaman
Tenor (bulan)
Tujuan Pinjaman
[ SIMULASI ]  [ AJUKAN ]
```

---

### 19.6 Halaman RAT & SHU

```
Tahun Buku
- Total SHU
- Distribusi SHU
- Status Pengesahan

[ CETAK LAPORAN ]
```

---

## 20. Dokumen Proposal Resmi ke Pimpinan

### 20.1 Judul Proposal

**PROPOSAL PENGEMBANGAN SISTEM INFORMASI KOPERASI SIMPAN PINJAM PERSONEL POLRI**

---

### 20.2 Latar Belakang

Pengelolaan Koperasi Simpan Pinjam Personel POLRI membutuhkan sistem yang:

* Transparan
* Akuntabel
* Mudah diaudit
* Sesuai dinamika personel (mutasi, pensiun)

Pengelolaan manual berpotensi menimbulkan:

* Kesalahan pencatatan
* Keterlambatan laporan
* Minimnya transparansi

---

### 20.3 Tujuan

* Meningkatkan profesionalisme pengelolaan koperasi
* Menjamin transparansi keuangan
* Memudahkan pelayanan kepada anggota
* Mendukung pengawasan internal

---

### 20.4 Ruang Lingkup Sistem

* Keanggotaan berbasis NRP
* Simpanan & pinjaman
* Angsuran otomatis
* SHU & RAT
* Audit log

---

### 20.5 Manfaat

**Bagi Pimpinan:**

* Laporan real-time
* Minim risiko temuan

**Bagi Pengurus:**

* Operasional lebih cepat

**Bagi Anggota:**

* Akses informasi transparan

---

### 20.6 Penutup

Dengan implementasi sistem ini, diharapkan KSP Personel POLRI dapat dikelola secara modern, transparan, dan akuntabel sesuai prinsip koperasi dan tata kelola organisasi POLRI.

---

📝 *Proposal ini dapat dijadikan dasar persetujuan pimpinan untuk implementasi sistem.*

---

# IMPLEMENTASI DATABASE (TAHAP 1)

## Prinsip Desain Database

* Tidak ada DELETE permanen (soft delete)
* Semua transaksi tercatat historinya
* Siap audit & RAT
* Relasi jelas antar tabel

---

## ERD Inti (Ringkasan Relasi)

* users → roles
* anggota → users
* simpanan → anggota
* pinjaman → anggota
* angsuran → pinjaman
* shu → anggota
* rat → shu
* audit_log → users

---

## 00_create_database.sql

```sql
CREATE DATABASE ksp_polri;
USE ksp_polri;
```

---

## 01_master_tables.sql

```sql
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255),
  role_id INT,
  is_active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE anggota (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nrp VARCHAR(20) UNIQUE,
  nama VARCHAR(100),
  pangkat VARCHAR(50),
  satuan VARCHAR(100),
  user_id INT,
  status ENUM('AKTIF','NONAKTIF') DEFAULT 'AKTIF',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 02_transaction_tables.sql

```sql
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
```

---

## 03_shu_rat_tables.sql

```sql
CREATE TABLE shu (
  id INT AUTO_INCREMENT PRIMARY KEY,
  anggota_id INT,
  tahun INT,
  jumlah DECIMAL(15,2),
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE rat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tahun INT,
  tanggal DATE,
  status ENUM('DRAFT','DISAHKAN')
);
```

---

## 04_audit_tables.sql

```sql
CREATE TABLE audit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  aksi VARCHAR(100),
  data_lama TEXT,
  data_baru TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---


# SOP PENGGUNAAN SISTEM RAT KSP POLRI

## 1. TUJUAN

SOP ini menjadi pedoman resmi penggunaan **Sistem RAT Digital KSP POLRI** agar proses Rapat Anggota Tahunan berjalan **tertib, akuntabel, aman, dan sesuai prinsip koperasi serta pengawasan internal POLRI**.

## 2. RUANG LINGKUP

SOP ini mengatur:

* Persiapan data RAT
* Proses generate dokumen RAT
* Validasi & pengesahan dokumen
* Distribusi dan arsip dokumen
* Pengamanan & audit

## 3. PIHAK TERKAIT & PERAN

### 3.1 Ketua Koperasi

* Menyetujui pelaksanaan RAT
* Memberikan pengesahan akhir dokumen RAT
* Bertanggung jawab atas keabsahan laporan

### 3.2 Pengurus

* Mengelola data anggota, simpanan, pinjaman
* Menjalankan proses generate RAT
* Menyampaikan laporan kepada anggota

### 3.3 Pengawas

* Melakukan pemeriksaan data & laporan
* Memberikan catatan dan rekomendasi
* Mengisi dan mengesahkan laporan pengawas

### 3.4 Admin Sistem

* Menjaga sistem tetap berjalan
* Mengelola user & hak akses
* Menjaga keamanan data dan arsip

## 4. PRASYARAT SEBELUM RAT

Sebelum RAT digenerate, wajib dipastikan:

* Semua transaksi tahun buku telah diinput
* Tidak ada transaksi backdate terbuka
* Status pinjaman & angsuran valid
* Data anggota aktif/nonaktif mutakhir

Checklist sistem:

* ✔ Data simpanan lengkap
* ✔ Data pinjaman & angsuran valid
* ✔ SHU tahun berjalan terhitung

## 5. PROSEDUR GENERATE RAT DIGITAL

### 5.1 Login Sistem

* Pengurus/Admin login ke sistem
* Role minimal: **PENGURUS**

### 5.2 Generate RAT Massal

1. Pilih menu **RAT → Generate RAT Tahunan**
2. Pilih Tahun Buku
3. Klik tombol **Generate Massal**
4. Sistem otomatis:

   * Membuat seluruh PDF RAT
   * Menyematkan QR & tanda tangan digital
   * Mengunci dokumen (read-only)
   * Mengarsipkan ke folder tahun terkait

### 5.3 Output Sistem

* Folder arsip RAT per tahun
* File ZIP RAT lengkap
* Nomor dokumen & hash tercatat di sistem

## 6. VALIDASI & PENGESAHAN

### 6.1 Pemeriksaan Internal

* Pengawas memeriksa:

  * Neraca
  * Laba Rugi
  * SHU
* Jika ada koreksi → data diperbaiki lalu RAT digenerate ulang

### 6.2 Pengesahan

* Ketua Koperasi menyetujui hasil RAT
* Dokumen dinyatakan FINAL

⚠ Setelah disahkan, **data RAT tidak boleh diubah**

## 7. DISTRIBUSI DOKUMEN

* PDF RAT dibagikan kepada anggota
* SHU per anggota diberikan masing-masing
* Verifikasi keaslian melalui QR Code

## 8. ARSIP & KEAMANAN

* Arsip disimpan per tahun
* Akses arsip dibatasi role
* Arsip bersifat read-only
* Backup minimal 2 lokasi (server & offline)

## 9. AUDIT & PENELUSURAN

* Setiap dokumen memiliki:

  * Nomor dokumen
  * Hash SHA256
  * Log pembuat
* Digunakan untuk:

  * Audit internal
  * Audit eksternal
  * Pemeriksaan mendadak

## 10. PENUTUP

SOP ini bersifat **mengikat** dan menjadi pedoman resmi penggunaan Sistem RAT Digital KSP POLRI. Setiap pelanggaran terhadap SOP ini menjadi tanggung jawab pihak terkait sesuai ketentuan yang berlaku.

---

**Disahkan oleh:**
Ketua Koperasi KSP POLRI

Tanggal: ____________


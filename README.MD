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

# README.md (FINAL)

## Sistem Informasi Koperasi Simpan Pinjam (KSP) Personel POLRI

Sistem ini dirancang sebagai **platform resmi digital Koperasi Simpan Pinjam (KSP) Personel POLRI**, yang mendukung seluruh proses koperasi secara **transparan, akuntabel, aman, dan sesuai prinsip perkoperasian serta tata kelola institusi POLRI**.

---

## 🎯 Tujuan Sistem

* Digitalisasi penuh proses KSP POLRI
* Mengurangi kesalahan administrasi manual
* Meningkatkan transparansi keuangan
* Mempermudah pengawasan dan audit
* Mendukung RAT yang akurat dan sah

---

## 🧑‍🤝‍🧑 Role Pengguna

| Role         | Kewenangan                                 |
| ------------ | ------------------------------------------ |
| Anggota      | Simpanan, pinjaman, angsuran, SHU pribadi  |
| Pengurus     | Operasional koperasi, persetujuan, laporan |
| Pengawas     | Audit, monitoring, verifikasi              |
| Admin Sistem | Manajemen user & konfigurasi               |

---

## 🧩 Modul Utama

* Manajemen Anggota (Personel POLRI)
* Simpanan Wajib, Pokok, Sukarela
* Pinjaman & Angsuran
* Perhitungan SHU Otomatis
* Laporan Keuangan
* Rapat Anggota Tahunan (RAT)
* Audit Log & Pengawasan

---

## 🏗️ Arsitektur Sistem

* **Frontend**: React (Role-based UI)
* **Backend**: Node.js + Express (REST API)
* **Database**: MySQL
* **Auth**: JWT + Role Middleware

Arsitektur mengikuti prinsip:

* Separation of Concern
* Auditability
* Maintainability jangka panjang

---

## 📂 Struktur Repository

Lihat struktur lengkap di root repository. Pembagian utama:

* `docs/` → Dokumen resmi & SOP
* `database/` → SQL installer & schema
* `backend/` → API & business logic
* `frontend/` → UI per role

---

## 🚀 Instalasi Singkat (Development)

### 1️⃣ Database

```bash
mysql -u root -p < database/00_create_database.sql
```

### 2️⃣ Backend

```bash
cd backend
npm install
npm run dev
```

### 3️⃣ Frontend

```bash
cd frontend
npm install
npm run dev
```

---

## 🔐 Keamanan & Audit

* JWT Authentication
* Role-based Authorization
* Audit Log setiap transaksi
* Data tidak dapat dihapus permanen (soft delete)

---

## 📑 Dokumen Pendukung

Dokumen resmi tersedia di folder `docs/`:

* Blueprint Sistem
* Proposal ke Pimpinan
* SOP Anggota / Pengurus / Pengawas
* Checklist Audit

---

## 🏁 Status Proyek

📌 Blueprint & arsitektur: **FINAL**
📌 Siap pengembangan bertahap
📌 Siap presentasi ke pimpinan

---

## ⚖️ Catatan Institusional

Sistem ini dirancang khusus untuk **KSP Personel POLRI** dan harus digunakan sesuai dengan:

* UU Perkoperasian
* AD/ART Koperasi
* Aturan internal POLRI

---

**Pengembang & Tim Teknis**
Disesuaikan dengan Surat Perintah / Penunjukan Resmi

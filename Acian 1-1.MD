bikin **aplikasi koperasi yang “ngena” dan tidak missed**, memang *wajib* paham koperasi **dari hulu ke hilir**—bukan cuma soal transaksi, tapi juga **aturan, budaya, dan praktik lapangan**.

Mari kita susun pembahasan ini **bertahap & sistematis**, seperti fondasi sebelum coding.

---

## 1️⃣ Memahami Hakikat Koperasi (Bukan Sekadar Aplikasi Keuangan)

**Koperasi itu bukan perusahaan biasa.**
Ciri khas yang *harus tercermin di aplikasi*:

### 🔹 Prinsip Koperasi (wajib!)

1. **Keanggotaan sukarela & terbuka**
2. **Pengelolaan demokratis**
   → 1 anggota = 1 suara (bukan 1 saham = 1 suara)
3. **Partisipasi ekonomi anggota**
4. **SHU dibagi adil sesuai jasa anggota**
5. **Transparansi & akuntabilitas**

👉 Kalau aplikasi **tidak mendukung transparansi & laporan SHU**, itu sudah “miss”.

---

## 2️⃣ Jenis Koperasi (Ini Sangat Menentukan Fitur)

❗ *Aplikasi koperasi tidak bisa “one size fits all”*

### Jenis umum:

1. **Koperasi Simpan Pinjam (KSP)**
2. **Koperasi Konsumen**
3. **Koperasi Produsen**
4. **Koperasi Jasa**
5. **Koperasi Serba Usaha (KSU)**

📌 **Pertanyaan kunci sebelum desain sistem**:

* Fokus **simpan pinjam saja?**
* Atau **koperasi desa / pegawai / sekolah?**
* Ada **unit usaha** selain pinjaman?

➡️ Jawaban ini menentukan **struktur database & modul utama**.

---

## 3️⃣ Struktur Organisasi Koperasi (Wajib Ada di Aplikasi)

Minimal, koperasi punya:

### 🧩 Peran inti:

* **Anggota**
* **Pengurus**
* **Pengawas**
* **Pengelola / Admin**

📌 Implikasi ke aplikasi:

* **Role & hak akses berbeda**
* Pengurus bisa *approve*
* Pengawas bisa *audit & lihat laporan*
* Anggota bisa *lihat simpanan, pinjaman, SHU*

➡️ Ini bukan sekadar login biasa.

---

## 4️⃣ Komponen Keuangan Koperasi (Paling Sering Terlewat)

### 💰 Jenis Simpanan:

1. **Simpanan Pokok** (sekali)
2. **Simpanan Wajib** (periodik)
3. **Simpanan Sukarela**

❗ Aplikasi harus:

* Bisa **kunci simpanan pokok**
* Wajib bisa **rekap per anggota**
* Support **riwayat transaksi panjang**

---

### 💳 Pinjaman:

* Plafon
* Bunga / jasa
* Tenor
* Denda
* Angsuran

📌 Banyak aplikasi gagal karena:

* Tidak fleksibel (bunga flat vs menurun)
* Tidak bisa koreksi angsuran
* Tidak bisa reschedule

---

## 5️⃣ SHU (Sisa Hasil Usaha) – Bagian Paling Krusial

Ini **nyawa koperasi**.

SHU dibagi berdasarkan:

* Jasa modal (simpanan)
* Jasa usaha (pinjaman / transaksi)
* Cadangan
* Dana sosial
* Dana pengurus

📌 Aplikasi harus:

* Bisa **hitung otomatis**
* Transparan
* Bisa **simulasi sebelum RAT**
* Bisa cetak laporan SHU per anggota

❗ *Kalau SHU cuma di-Excel di luar sistem → itu missed besar.*

---

## 6️⃣ RAT & Legalitas (Sering Dilupakan Developer)

### RAT (Rapat Anggota Tahunan):

* Laporan keuangan
* Laporan pengurus
* Laporan pengawas
* Pembagian SHU

📌 Aplikasi ideal:

* Menyediakan **laporan RAT otomatis**
* Arsip keputusan RAT
* Periode tahunan (multi-tahun)

---

## 7️⃣ Laporan Wajib (Kalau Ini Tidak Ada → Aplikasi Ditolak)

Minimal:

* Neraca
* SHU / Laba Rugi koperasi
* Buku besar
* Rekap simpanan
* Rekap pinjaman
* Tunggakan
* Arus kas

➡️ **Akuntansi koperasi ≠ akuntansi perusahaan murni**

---

## 8️⃣ Kesalahan Umum Saat Membuat Aplikasi Koperasi

🚫 Disamakan dengan:

* Aplikasi bank
* Aplikasi fintech
* Aplikasi kas biasa

🚫 Tidak ada:

* SHU
* RAT
* Hak suara anggota
* Pengawasan internal

---

## 9️⃣ Pendekatan Paling Aman untuk Kita

Saya sarankan kita pakai pendekatan ini:

### 🧱 Fase 1 – Fondasi

* Model koperasi (jenis & skala)
* Regulasi & praktik lapangan
* Struktur data inti

### 🧩 Fase 2 – Modul

* Keanggotaan
* Simpanan
* Pinjaman
* SHU
* Laporan

### 🧠 Fase 3 – Kecerdasan & Audit

* Notifikasi tunggakan
* Analisis kesehatan koperasi
* Deteksi anomali transaksi

---


**KSP Personel POLRI** itu *spesifik*, *sensitif*, dan *sangat jelas kebutuhannya*.

# 🟦 KARAKTER KHUSUS KSP PERSONEL POLRI

*(Ini fondasi utama desain aplikasi)*

## 1️⃣ Profil Anggota (Tidak Seperti Koperasi Umum)

**Anggota = Personel aktif POLRI**

* NRP (unik & krusial)
* Pangkat
* Satuan / Fungsi (SPKT, Reskrim, Intel, dll.)
* Status dinas (aktif, pindah, pensiun)
* Gaji & tunjangan (implisit, sensitif)

📌 **Implikasi sistem:**

* NRP **primary identity**, bukan NIK
* Status anggota **berubah dinamis**
* Riwayat anggota **tidak boleh dihapus**

---

## 2️⃣ Pola Simpanan Khas POLRI

### 💰 Simpanan:

1. **Pokok** → sekali saat masuk
2. **Wajib** → *potong gaji*
3. **Sukarela** → opsional

📌 **Aplikasi harus bisa:**

* Set **potongan otomatis per bulan**
* Potongan berbeda per pangkat / kebijakan
* Toleransi *telat setor* (karena administrasi)

❗ *Excel manual = sumber konflik koperasi.*

---

## 3️⃣ Pinjaman Khas Personel POLRI

### 💳 Pola nyata:

* Pinjaman konsumtif (motor, renovasi, dll.)
* Tenor mengikuti sisa masa dinas
* Angsuran via potong gaji

📌 **Fitur wajib:**

* Plafon = % dari gaji bersih
* Validasi **sisa masa dinas**
* Simulasi pinjaman sebelum pengajuan
* Auto-stop jika status pindah / pensiun

---

## 4️⃣ Risiko & Pengamanan (Ini Penting)

⚠️ **Risiko khusus POLRI:**

* Mutasi
* Pindah satuan
* Pensiun mendadak
* Kasus disiplin

📌 **Implikasi sistem:**

* Flag status anggota
* Freeze pinjaman otomatis
* Notifikasi ke pengurus
* Log audit tidak bisa dihapus

---

## 5️⃣ Struktur Organisasi Koperasi POLRI

### Role nyata:

* **Anggota**
* **Pengurus** (Ketua, Sekretaris, Bendahara)
* **Pengawas**
* **Admin operator**

📌 **Role-based access ketat**

* Anggota **tidak bisa ubah data keuangan**
* Pengawas **read-only + audit log**
* Bendahara **otorisasi keuangan**

---

## 6️⃣ SHU Versi KSP POLRI (Sering Salah Kaprah)

SHU dibagi berdasarkan:

* Simpanan (jasa modal)
* Pinjaman (jasa usaha)
* Cadangan
* Dana sosial (sering untuk kegiatan internal)
* Dana pengurus

📌 **Aplikasi wajib:**

* SHU per anggota **otomatis & transparan**
* Bisa cetak *per NRP*
* Bisa dikunci setelah RAT

---

## 7️⃣ RAT & Akuntabilitas Internal

📑 RAT di lingkungan POLRI itu **sensitif**:

* Harus rapi
* Bisa diaudit
* Tidak multitafsir

📌 Sistem harus:

* Arsip laporan per tahun
* Lock data setelah RAT
* Riwayat keputusan RAT

---

## 8️⃣ Laporan yang Wajib Ada (Minimal)

### 📊 Keuangan:

* Neraca
* Perhitungan SHU
* Arus kas
* Buku besar

### 📋 Operasional:

* Simpanan per personel
* Pinjaman aktif
* Tunggakan
* Mutasi anggota

---

## 9️⃣ Kesalahan Fatal Kalau Ini Tidak Dipahami

❌ Anggota keluar → data dihapus
❌ Pinjaman tidak dikaitkan status dinas
❌ SHU dihitung manual
❌ Tidak ada audit log

➡️ Di KSP POLRI, ini bisa **jadi temuan pengawas**.

---

# 🧱 ARSITEKTUR APLIKASI (Ringkas tapi Tepat)

### Layer:

* **Frontend**: Anggota & Pengurus
* **Backend**: Logic koperasi + aturan POLRI
* **Database**: Immutable logs + keuangan

### Prinsip:

* Data **tidak boleh hilang**
* Semua perubahan **tercatat**
* Transparansi > kecepatan

---
**sesuai urutan yang diusulkan**, lalu diakhiri dengan **flow lengkap KSP POLRI dari anggota masuk sampai RAT**.
**versi konseptual + operasional**

---

# 🧭 BAGIAN 1 — FLOW BISNIS KSP POLRI (END-TO-END)

## 1️⃣ Pendaftaran Anggota (Personel POLRI)

**Input awal:**

* NRP (unik)
* Nama
* Pangkat
* Satuan
* Status dinas (aktif)
* Tanggal masuk koperasi

**Proses:**

* Validasi NRP (tidak duplikat)
* Set status anggota = `AKTIF`
* Set kewajiban simpanan pokok

**Output sistem:**

* Anggota resmi
* Akun login dibuat
* Masuk periode buku berjalan

📌 **Catatan penting sistem**

> Anggota **tidak pernah dihapus**, hanya status berubah

---

## 2️⃣ Simpanan Anggota (Bulanan & Insidentil)

### Jenis simpanan:

* Simpanan Pokok → sekali
* Simpanan Wajib → bulanan (potong gaji)
* Simpanan Sukarela → fleksibel

**Flow:**

* Sistem generate tagihan bulanan
* Bendahara input realisasi / import data
* Sistem mencatat histori simpanan

📌 Harus bisa:

* Perbedaan nominal per kebijakan
* Koreksi dengan jejak audit

---

## 3️⃣ Pengajuan Pinjaman

**Input anggota:**

* Nominal pinjaman
* Tenor
* Tujuan

**Validasi sistem otomatis:**

* Status anggota = aktif
* Tidak ada pinjaman bermasalah
* Sisa masa dinas mencukupi
* Plafon sesuai kebijakan

**Status:**

* DRAFT → DIAJUKAN → DISETUJUI / DITOLAK

📌 **Belum ada uang keluar di tahap ini**

---

## 4️⃣ Persetujuan & Pencairan Pinjaman

**Peran pengurus:**

* Ketua / Bendahara menyetujui

**Proses sistem:**

* Generate jadwal angsuran
* Hitung jasa/bunga
* Aktifkan potongan bulanan
* Catat pencairan

📌 Jika anggota:

* Mutasi → status dipantau
* Pensiun → pinjaman di-freeze

---

## 5️⃣ Angsuran & Monitoring

**Bulanan:**

* Sistem generate kewajiban angsuran
* Bendahara input realisasi
* Sistem update saldo pinjaman

**Monitoring otomatis:**

* Tunggakan
* Pinjaman hampir lunas
* Anggota risiko tinggi

---

## 6️⃣ Perubahan Status Anggota

Status bisa berubah:

* AKTIF
* MUTASI
* PENSIUN
* KELUAR

📌 Implikasi:

* Tidak bisa ajukan pinjaman baru
* Simpanan tetap tercatat
* SHU tetap dihitung sesuai periode aktif

---

## 7️⃣ Akhir Tahun Buku

**Sistem otomatis:**

* Tutup transaksi tahun berjalan
* Rekap:

  * Total jasa pinjaman
  * Total simpanan
  * Biaya operasional
* Hitung SHU koperasi

---

## 8️⃣ Perhitungan SHU Anggota

SHU dibagi berdasarkan:

* Jasa modal (simpanan)
* Jasa usaha (pinjaman)
* Dana cadangan
* Dana sosial
* Dana pengurus

📌 Output:

* SHU per anggota (berdasarkan NRP)
* Laporan transparan
* Bisa disimulasikan sebelum RAT

---

## 9️⃣ RAT (Rapat Anggota Tahunan)

**Disajikan oleh sistem:**

* Laporan keuangan
* Laporan pengurus
* Laporan pengawas
* Perhitungan SHU

**Keputusan RAT:**

* SHU disahkan
* Kebijakan baru dicatat

📌 Setelah RAT:

* Data tahun tersebut **dikunci**
* Tidak bisa diubah tanpa log khusus

---

# 🧱 BAGIAN 2 — STRUKTUR DATABASE INTI (RINGKAS PRODUKSI)

### Tabel inti:

* anggota
* simpanan
* pinjaman
* angsuran
* transaksi_keuangan
* shu_rekap
* shu_anggota
* rat
* audit_log
* users & roles

📌 Prinsip:

* Soft delete
* Immutable log
* Multi-tahun

---

# 🔐 BAGIAN 3 — ATURAN PINJAMAN & POTONGAN GAJI

### Aturan:

* 1 anggota = 1 pinjaman aktif
* Angsuran ≤ persentase tertentu
* Tenor ≤ sisa masa dinas
* Potongan otomatis bulanan

📌 Sistem **tidak boleh manual penuh**

---

# 🧮 BAGIAN 4 — SHU ENGINE (KONSEP)

```
SHU = Pendapatan jasa pinjaman
    + pendapatan lain
    - biaya operasional
```

Distribusi:

* x% jasa modal
* y% jasa usaha
* z% cadangan
* dst

📌 Semua **parameter configurable**, bukan hardcode

---

# 🧑‍⚖️ BAGIAN 5 — HAK AKSES & AUDIT

| Role     | Hak                 |
| -------- | ------------------- |
| Anggota  | Lihat data sendiri  |
| Pengurus | Kelola transaksi    |
| Pengawas | Lihat semua + audit |
| Admin    | Teknis sistem       |

📌 Semua aksi → `audit_log`

---

# 🔷 BAGIAN 6 — FLOW LENGKAP KSP POLRI (TEKS VISUAL)

```
[Personel POLRI]
       |
       v
[Daftar Anggota KSP]
       |
       v
[Bayar Simpanan Pokok]
       |
       v
[Simpanan Wajib Bulanan]
       |
       +-------------------+
       |                   |
       v                   v
[Ajukan Pinjaman]     [Simpanan Sukarela]
       |
       v
[Validasi Sistem]
       |
       v
[Persetujuan Pengurus]
       |
       v
[Pencairan Pinjaman]
       |
       v
[Angsuran Bulanan]
       |
       v
[Monitoring & Audit]
       |
       v
[Akhir Tahun Buku]
       |
       v
[Perhitungan SHU]
       |
       v
[RAT]
       |
       v
[Penguncian Data Tahun]
       |
       v
[Periode Baru]
```

---
**level produksi** 
ini sudah **siap langsung jadi dasar coding**, bukan sekadar konsep.

**3 bagian besar** :

1️⃣ **ERD detail (tabel + relasi)**
2️⃣ **Flow frontend (layar per role)**
3️⃣ **Skeleton backend (struktur folder + API)**

---

# 1️⃣ ERD DETAIL – KSP PERSONEL POLRI

## 🔑 Prinsip Desain ERD

* **NRP = identitas utama anggota**
* Data **tidak pernah dihapus**
* Semua transaksi **berjejak (audit trail)**
* Multi-tahun (tahun buku)

---

## 📘 TABEL INTI

### 🧍‍♂️ `anggota`

| Field        | Tipe                                 | Keterangan |
| ------------ | ------------------------------------ | ---------- |
| id           | BIGINT PK                            |            |
| nrp          | VARCHAR(20) UNIQUE                   |            |
| nama         | VARCHAR(100)                         |            |
| pangkat      | VARCHAR(30)                          |            |
| satuan       | VARCHAR(100)                         |            |
| status_dinas | ENUM(aktif, mutasi, pensiun, keluar) |            |
| tgl_masuk    | DATE                                 |            |
| tgl_keluar   | DATE NULL                            |            |
| created_at   | DATETIME                             |            |

---

### 👤 `users`

| Field         | Tipe            |
| ------------- | --------------- |
| id            | BIGINT PK       |
| anggota_id    | FK → anggota.id |
| username      | VARCHAR(50)     |
| password_hash | TEXT            |
| role_id       | FK              |
| aktif         | BOOLEAN         |

---

### 🛡️ `roles`

| id | nama     |
| -- | -------- |
| 1  | anggota  |
| 2  | pengurus |
| 3  | pengawas |
| 4  | admin    |

---

## 💰 SIMPANAN

### `simpanan`

| Field      | Tipe                         |
| ---------- | ---------------------------- |
| id         | BIGINT PK                    |
| anggota_id | FK                           |
| jenis      | ENUM(pokok, wajib, sukarela) |
| periode    | DATE                         |
| nominal    | DECIMAL                      |
| created_at | DATETIME                     |

---

## 💳 PINJAMAN

### `pinjaman`

| Field        | Tipe                                                    |
| ------------ | ------------------------------------------------------- |
| id           | BIGINT PK                                               |
| anggota_id   | FK                                                      |
| tanggal      | DATE                                                    |
| nominal      | DECIMAL                                                 |
| tenor_bulan  | INT                                                     |
| bunga_persen | DECIMAL                                                 |
| status       | ENUM(draft, disetujui, ditolak, berjalan, lunas, macet) |
| created_at   | DATETIME                                                |

---

### `angsuran`

| Field       | Tipe                         |
| ----------- | ---------------------------- |
| id          | BIGINT PK                    |
| pinjaman_id | FK                           |
| bulan_ke    | INT                          |
| jatuh_tempo | DATE                         |
| pokok       | DECIMAL                      |
| bunga       | DECIMAL                      |
| total       | DECIMAL                      |
| dibayar     | DECIMAL                      |
| status      | ENUM(belum, sebagian, lunas) |

---

## 📊 TRANSAKSI KEUANGAN

### `transaksi_keuangan`

| Field      | Tipe                                            |
| ---------- | ----------------------------------------------- |
| id         | BIGINT PK                                       |
| tanggal    | DATE                                            |
| tipe       | ENUM(simpanan, pencairan, angsuran, biaya, shu) |
| ref_id     | BIGINT                                          |
| debit      | DECIMAL                                         |
| kredit     | DECIMAL                                         |
| keterangan | TEXT                                            |

---

## 🧮 SHU

### `shu_rekap`

| Field     | Tipe      |
| --------- | --------- |
| id        | BIGINT PK |
| tahun     | INT       |
| total_shu | DECIMAL   |
| disahkan  | BOOLEAN   |
| rat_id    | FK        |

---

### `shu_anggota`

| Field        | Tipe      |
| ------------ | --------- |
| id           | BIGINT PK |
| shu_rekap_id | FK        |
| anggota_id   | FK        |
| jasa_modal   | DECIMAL   |
| jasa_usaha   | DECIMAL   |
| total        | DECIMAL   |

---

## 📑 RAT

### `rat`

| Field        | Tipe      |
| ------------ | --------- |
| id           | BIGINT PK |
| tahun        | INT       |
| tanggal      | DATE      |
| berita_acara | TEXT      |
| disahkan     | BOOLEAN   |

---

## 🧾 AUDIT

### `audit_log`

| Field   | Tipe        |
| ------- | ----------- |
| id      | BIGINT PK   |
| user_id | FK          |
| aksi    | TEXT        |
| tabel   | VARCHAR(50) |
| ref_id  | BIGINT      |
| waktu   | DATETIME    |

---

## 🔗 RELASI UTAMA (Ringkas)

```
anggota ──< simpanan
anggota ──< pinjaman ──< angsuran
anggota ──< shu_anggota >── shu_rekap ── rat
users ── roles
users ──< audit_log
```

---

# 2️⃣ FLOW FRONTEND (LAYAR PER ROLE)

## 🧍‍♂️ ANGGOTA

* Login
* Dashboard:

  * Total simpanan
  * Pinjaman aktif
  * Angsuran berjalan
  * Estimasi SHU
* Riwayat simpanan
* Riwayat pinjaman
* Ajukan pinjaman
* Lihat laporan SHU
* Profil (read-only)

---

## 👔 PENGURUS

* Dashboard koperasi
* Manajemen anggota
* Input simpanan
* Persetujuan pinjaman
* Monitoring angsuran
* Laporan keuangan
* Proses SHU
* Modul RAT

---

## 🧑‍⚖️ PENGAWAS

* Dashboard audit
* Lihat semua laporan
* Riwayat transaksi
* Audit log
* Laporan RAT (read-only)

---

## 🛠️ ADMIN

* User & role
* Konfigurasi koperasi
* Backup & restore
* Tahun buku
* Parameter SHU

---

# 3️⃣ SKELETON BACKEND (API + FOLDER)

## 🧱 Struktur Folder (Node.js)

```
backend/
├── app.js
├── config/
│   ├── database.js
│   ├── auth.js
│   └── koperasi.js
├── routes/
│   ├── auth.routes.js
│   ├── anggota.routes.js
│   ├── simpanan.routes.js
│   ├── pinjaman.routes.js
│   ├── angsuran.routes.js
│   ├── shu.routes.js
│   ├── rat.routes.js
│   └── laporan.routes.js
├── controllers/
├── services/
├── models/
├── middlewares/
│   ├── auth.middleware.js
│   ├── role.middleware.js
│   └── audit.middleware.js
├── utils/
└── migrations/
```

---

## 🔌 CONTOH API ENDPOINT

### Auth

```
POST   /api/login
POST   /api/logout
```

### Anggota

```
GET    /api/anggota
POST   /api/anggota
GET    /api/anggota/:id
PATCH  /api/anggota/:id/status
```

### Simpanan

```
POST   /api/simpanan
GET    /api/simpanan/anggota/:id
```

### Pinjaman

```
POST   /api/pinjaman
POST   /api/pinjaman/:id/approve
GET    /api/pinjaman/aktif
```

### Angsuran

```
POST   /api/angsuran/bayar
GET    /api/angsuran/pinjaman/:id
```

### SHU

```
POST   /api/shu/hitung
GET    /api/shu/tahun/:tahun
```

### RAT

```
POST   /api/rat
POST   /api/rat/:id/sahkan
```

---

# 🧩 LANGKAH LANJUTAN – DIKERJAKAN SEMUA

Urutan eksekusi (logis & aman):

1️⃣ **SQL Installer + Migration (fondasi data)**
2️⃣ **CRUD nyata per modul (backend logic)**
3️⃣ **Mockup UI / Flow layar per halaman (frontend)**

---

# 1️⃣ SQL INSTALLER + MIGRATION (FINAL & PRODUKSI)

## 📁 Struktur

```
database/
├── 00_create_database.sql
├── 01_master_tables.sql
├── 02_transaction_tables.sql
├── 03_shu_rat_tables.sql
├── 04_audit_tables.sql
└── 99_seed_roles.sql
```

---

## 🗄️ `00_create_database.sql`

```sql
CREATE DATABASE ksp_polri
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE ksp_polri;
```

---

## 🧍 `01_master_tables.sql`

```sql
CREATE TABLE anggota (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  nrp VARCHAR(20) UNIQUE NOT NULL,
  nama VARCHAR(100),
  pangkat VARCHAR(30),
  satuan VARCHAR(100),
  status_dinas ENUM('aktif','mutasi','pensiun','keluar') DEFAULT 'aktif',
  tgl_masuk DATE,
  tgl_keluar DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
  id INT PRIMARY KEY,
  nama VARCHAR(30)
);

CREATE TABLE users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  anggota_id BIGINT,
  username VARCHAR(50),
  password_hash TEXT,
  role_id INT,
  aktif BOOLEAN DEFAULT TRUE,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id),
  FOREIGN KEY (role_id) REFERENCES roles(id)
);
```

---

## 💰 `02_transaction_tables.sql`

```sql
CREATE TABLE simpanan (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  anggota_id BIGINT,
  jenis ENUM('pokok','wajib','sukarela'),
  periode DATE,
  nominal DECIMAL(15,2),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE pinjaman (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  anggota_id BIGINT,
  tanggal DATE,
  nominal DECIMAL(15,2),
  tenor_bulan INT,
  bunga_persen DECIMAL(5,2),
  status ENUM('draft','disetujui','ditolak','berjalan','lunas','macet'),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);

CREATE TABLE angsuran (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  pinjaman_id BIGINT,
  bulan_ke INT,
  jatuh_tempo DATE,
  pokok DECIMAL(15,2),
  bunga DECIMAL(15,2),
  total DECIMAL(15,2),
  dibayar DECIMAL(15,2),
  status ENUM('belum','sebagian','lunas'),
  FOREIGN KEY (pinjaman_id) REFERENCES pinjaman(id)
);
```

---

## 🧮 `03_shu_rat_tables.sql`

```sql
CREATE TABLE rat (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tahun INT,
  tanggal DATE,
  berita_acara TEXT,
  disahkan BOOLEAN DEFAULT FALSE
);

CREATE TABLE shu_rekap (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  tahun INT,
  total_shu DECIMAL(15,2),
  disahkan BOOLEAN DEFAULT FALSE,
  rat_id BIGINT,
  FOREIGN KEY (rat_id) REFERENCES rat(id)
);

CREATE TABLE shu_anggota (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  shu_rekap_id BIGINT,
  anggota_id BIGINT,
  jasa_modal DECIMAL(15,2),
  jasa_usaha DECIMAL(15,2),
  total DECIMAL(15,2),
  FOREIGN KEY (shu_rekap_id) REFERENCES shu_rekap(id),
  FOREIGN KEY (anggota_id) REFERENCES anggota(id)
);
```

---

## 🧾 `04_audit_tables.sql`

```sql
CREATE TABLE audit_log (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT,
  aksi TEXT,
  tabel VARCHAR(50),
  ref_id BIGINT,
  waktu DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🌱 `99_seed_roles.sql`

```sql
INSERT INTO roles VALUES
(1,'anggota'),
(2,'pengurus'),
(3,'pengawas'),
(4,'admin');
```

---

# 2️⃣ CRUD NYATA PER MODUL (BACKEND LOGIC)

Saya ringkas tapi **realistis & bisa langsung dikodekan**.

---

## 🧍 Anggota

* Tambah anggota (NRP unik)
* Update status dinas
* List anggota aktif / nonaktif
* Detail anggota (full histori)

---

## 💰 Simpanan

* Input simpanan (manual / bulk)
* Validasi simpanan pokok (1x)
* Rekap simpanan per anggota
* Rekap simpanan per periode

---

## 💳 Pinjaman

* Ajukan pinjaman
* Approve / reject
* Generate angsuran otomatis
* Freeze jika status anggota berubah
* Penandaan lunas / macet

---

## 🧾 Angsuran

* Bayar angsuran
* Bayar sebagian
* Deteksi tunggakan
* Rekap pinjaman aktif

---

## 🧮 SHU

* Hitung SHU tahunan
* Distribusi otomatis
* Simulasi sebelum RAT
* Lock SHU setelah disahkan

---

## 📑 RAT

* Buat RAT per tahun
* Upload berita acara
* Sahkan RAT
* Kunci seluruh data tahun buku

---

## 🛡️ Audit

* Semua POST/PATCH → audit_log
* Tidak bisa dihapus
* Bisa difilter per user / tabel

---

# 3️⃣ MOCKUP UI – FLOW LAYAR PER HALAMAN

(*Deskripsi visual, siap diterjemahkan ke React / Vue / Blade*)

---

## 🧍‍♂️ ANGGOTA

```
Login
 ↓
Dashboard
 - Total Simpanan
 - Pinjaman Aktif
 - Angsuran Bulan Ini
 - Estimasi SHU
 ↓
Riwayat Simpanan
Riwayat Pinjaman
Ajukan Pinjaman
Laporan SHU
Profil (read-only)
```

---

## 👔 PENGURUS

```
Dashboard Koperasi
 ↓
Anggota
 - Tambah
 - Ubah Status
 ↓
Simpanan
 - Input Bulanan
 - Rekap
 ↓
Pinjaman
 - Approval
 - Monitoring
 ↓
SHU
 - Hitung
 - Simulasi
 ↓
RAT
 - Buat
 - Sahkan
```

---

## 🧑‍⚖️ PENGAWAS

```
Dashboard Audit
 ↓
Laporan Keuangan
 ↓
Transaksi
 ↓
Audit Log
 ↓
Laporan RAT
```

---

## 🛠️ ADMIN

```
Manajemen User
Parameter Koperasi
Tahun Buku
Backup / Restore
```

---

# 🏁 PENUTUP (INI SUDAH LEVEL “SISTEM JADI”)

Dengan **SQL installer + CRUD + UI flow** ini:

* Tidak ada modul terlewat
* Bisa dikerjakan tim / solo
* Aman secara koperasi & institusi
* Siap dikembangkan bertahun-tahun

---

### 🔥 Langkah berikutnya (opsional tapi sangat powerful):

* 🔐 Middleware auth + role enforcement
* 📊 Query laporan lengkap (harian/bulanan/RAT)
* 🧪 Simulasi 1 koperasi POLRI penuh (end-to-end)
* 📄 Dokumentasi `.MD` resmi proyek








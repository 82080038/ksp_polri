# Panduan Pengembangan Aplikasi KSP POLRI

## Pendahuluan

Aplikasi KSP POLRI adalah sistem manajemen koperasi simpan pinjam yang dikembangkan untuk Koperasi Simpan Pinjam POLRI. Aplikasi ini mencakup modul-modul inti koperasi seperti manajemen anggota, simpanan, pinjaman, SHU, RAT, serta modul tambahan seperti e-commerce, pemasok, investor, agen, dan akuntansi. Aplikasi ini didasarkan pada prinsip koperasi: transparansi, partisipasi anggota, dan kepatuhan hukum.

Dokumen ini memberikan panduan lengkap bagi developer lain untuk mengembangkan, menjalankan, dan memperluas aplikasi ini.

## Teknologi yang Digunakan

- **Backend**: PHP native MVC (tanpa framework untuk kesederhanaan), MySQL/MariaDB
- **Frontend**: HTML5, CSS3, jQuery, Bootstrap (opsional)
- **Database**: MySQL 8.0+
- **Server**: Apache/Nginx dengan PHP 8.1+
- **Tools Tambahan**: PHPUnit untuk testing, Docker untuk deployment
- **Integrasi**: Payment Gateway (Midtrans), Shipping (RajaOngkir), dll.

## Struktur Folder

```
/var/www/html/ksp_polri/
├── backend/                    # Backend aplikasi
│   ├── app/
│   │   ├── controllers/        # Controller classes
│   │   ├── models/             # Model classes
│   │   ├── views/              # View helpers (json.php)
│   │   └── core/               # Core files (Database.php, Router.php, Auth.php)
│   ├── config/                 # Konfigurasi database
│   ├── libs/                   # Library eksternal (FPDF, phpqrcode)
│   ├── public/                 # Entry point (index.php, rat_generate.php)
│   └── services/               # Service classes (RatService.php)
├── frontend/                   # Frontend aplikasi
│   ├── assets/                 # CSS, JS, images
│   ├── pages/                  # Halaman HTML
│   └── index.html              # Halaman login
├── database/                   # SQL files untuk setup database
├── docs/                       # Dokumentasi tambahan
├── tests/                      # Unit dan integration tests
├── Dockerfile                  # Docker configuration
├── docker-compose.yml          # Docker compose
├── phpunit.phar                # PHPUnit binary
├── Dokumentasi_Lengkap.md      # Dokumentasi lengkap
├── roadmap.md                  # Roadmap pengembangan
└── GUIDE_PENGEMBANGAN.md       # File ini
```

## Persiapan Lingkungan Pengembangan

### 1. Installasi Software yang Dibutuhkan

- **PHP 8.1+**: Download dari [php.net](https://www.php.net/)
- **MySQL/MariaDB**: Install XAMPP atau standalone
- **Composer** (opsional, untuk dependency management)
- **Git**: Untuk version control
- **Docker** (opsional, untuk containerized development)

### 2. Clone Repository

```bash
git clone https://github.com/82080038/ksp_polri.git
cd ksp_polri
```

### 3. Setup Database

1. Buat database baru di MySQL:
   ```sql
   CREATE DATABASE ksp_polri;
   ```

2. Jalankan SQL files secara berurutan:
   ```bash
   mysql -u root -p ksp_polri < database/00_create_database.sql
   mysql -u root -p ksp_polri < database/01_master_tables.sql
   mysql -u root -p ksp_polri < database/02_transaction_tables.sql
   mysql -u root -p ksp_polri < database/03_shu_rat_tables.sql
   mysql -u root -p ksp_polri < database/04_audit_tables.sql
   mysql -u root -p ksp_polri < database/05_additional_tables.sql
   ```

3. Untuk modul tambahan:
   ```bash
   mysql -u root -p ksp_polri < database/06_ecommerce_tables.sql
   mysql -u root -p ksp_polri < database/07_suppliers_tables.sql
   mysql -u root -p ksp_polri < database/08_investors_tables.sql
   mysql -u root -p ksp_polri < database/09_agents_tables.sql
   mysql -u root -p ksp_polri < database/10_accounting_tables.sql
   ```

4. Update konfigurasi database di `backend/config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ksp_polri');
   ```

### 4. Setup Web Server

- **Apache**: Pastikan mod_rewrite enabled, document root ke `backend/public/`
- **Nginx**: Konfigurasi untuk PHP-FPM
- Atau gunakan XAMPP dengan virtual host.

### 5. Jalankan Aplikasi

1. Akses `http://localhost/ksp_polri/frontend/` untuk frontend.
2. API endpoints di `http://localhost/ksp_polri/backend/public/index.php?path=...`

## Arsitektur Aplikasi

### MVC Pattern

- **Model**: Menangani data dan business logic (contoh: Anggota.php, Simpanan.php)
- **View**: Helper untuk response (json.php)
- **Controller**: Menangani request dan response (contoh: AnggotaController.php)

### Routing

Routing berdasarkan parameter `path` di `backend/app/core/Router.php`. Contoh:
- `path=anggota/list` -> AnggotaController::list()
- `path=produk/create` -> ProdukController::create()

### Authentication & Authorization

- Session-based authentication
- Role-based access: pengurus, pengawas, anggota
- CSRF protection, rate limiting

## Menambahkan Modul Baru

### 1. Buat Tabel Database

Tambahkan SQL file di `database/`, contoh `11_new_module_tables.sql`

### 2. Buat Model

Di `backend/app/models/NewModule.php`:

```php
class NewModule {
    private $conn;
    public function __construct() { $this->conn = Database::getConnection(); }
    public function getAll() { /* query */ }
    // dll.
}
```

### 3. Buat Controller

Di `backend/app/controllers/NewModuleController.php`:

```php
class NewModuleController {
    public static function list() {
        $model = new NewModule();
        $data = $model->getAll();
        jsonResponse(true, 'Data', $data);
    }
    // dll.
}
```

### 4. Tambah Routes

Di `backend/app/core/Router.php`, tambah cases:

```php
case 'newmodule/list':
    Auth::requireLogin();
    require '../app/controllers/NewModuleController.php';
    NewModuleController::list();
    break;
// dll.
```

### 5. Buat Frontend

Tambahkan HTML di `frontend/pages/newmodule.html`, gunakan jQuery untuk AJAX.

### 6. Update Dokumentasi

Tambahkan ke `Dokumentasi_Lengkap.md` dan `roadmap.md`.

## Testing

### Unit Test

```bash
php phpunit.phar tests/AnggotaTest.php
```

### Integration Test

```bash
php phpunit.phar tests/IntegrationTest.php
```

## Deployment

### Menggunakan Docker

```bash
docker-compose up -d
```

### Production Deployment

- Setup SSL (Let's Encrypt)
- Backup database otomatis
- Monitoring dengan Prometheus/Grafana
- Load balancing jika diperlukan

## Troubleshooting

- **Error 500**: Cek log PHP, pastikan file permissions benar.
- **Database Connection**: Verifikasi credentials di config.php.
- **Routing Error**: Pastikan path parameter benar.
- **CSRF Error**: Pastikan token di-include di forms.

## Referensi

- `Dokumentasi_Lengkap.md`: Dokumentasi lengkap fitur dan implementasi.
- `roadmap.md`: Roadmap pengembangan.
- `docs/DOKUMENTASI_POLRES.md`: Dokumen asli rencana aplikasi.
- ICA Tools: https://ica.coop/en/tools-and-apps

## Kontribusi

1. Fork repository
2. Buat branch fitur baru
3. Commit changes
4. Push dan buat Pull Request

---

**Panduan ini dapat diperbarui sesuai perkembangan aplikasi.**

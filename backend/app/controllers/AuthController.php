<?php
// app/controllers/AuthController.php
require_once '../app/models/User.php';
require_once '../app/models/Koperasi.php';
require_once '../app/models/Alamat.php';
require_once '../app/views/json.php';

class AuthController {
    
    /**
     * Cek apakah koperasi sudah terdaftar
     * Digunakan untuk blokir login/register jika belum ada koperasi
     */
    public static function checkKoperasiExists() {
        $koperasi = new Koperasi();
        $exists = $koperasi->exists();
        
        jsonResponse(true, 'Koperasi check', [
            'exists' => $exists,
            'message' => $exists ? 'Koperasi tersedia' : 'Belum ada koperasi terdaftar. Silakan daftar koperasi terlebih dahulu.'
        ]);
    }
    
    /**
     * Get data koperasi untuk dropdown
     */
    public static function getKoperasiList() {
        $koperasi = new Koperasi();
        $list = $koperasi->getAllActive();
        
        jsonResponse(true, 'Daftar koperasi', ['koperasi' => $list]);
    }
    
    /**
     * Register koperasi baru (Tab 2)
     */
    public static function registerKoperasi() {
        $data = $_POST;
        
        // Validasi required fields
        if (empty($data['nama_koperasi']) || empty($data['kode_koperasi'])) {
            jsonResponse(false, 'Nama koperasi dan kode koperasi wajib diisi');
            return;
        }
        
        // Validasi format kode koperasi
        if (!preg_match('/^[A-Z0-9]{3,10}$/', $data['kode_koperasi'])) {
            jsonResponse(false, 'Kode koperasi harus 3-10 karakter huruf besar dan angka');
            return;
        }
        
        // Validasi email format jika diisi
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Format email tidak valid');
            return;
        }
        
        // Validasi format telepon jika diisi
        if (!empty($data['telepon']) && !preg_match('/^[0-9+\-\s]{10,15}$/', $data['telepon'])) {
            jsonResponse(false, 'Format telepon tidak valid (10-15 digit)');
            return;
        }
        
        // Validasi format NPWP jika diisi (Indonesia)
        if (!empty($data['npwp']) && !preg_match('/^[0-9]{2}\.[0-9]{3}\.[0-9]{3}\.[0-9]{1}-[0-9]{3}\.[0-9]{3}$/', $data['npwp'])) {
            jsonResponse(false, 'Format NPWP tidak valid (XX.XXX.XXX.X-XXX.XXX)');
            return;
        }
        
        // Validasi alamat
        if (empty($data['Prop_desa'])) {
            jsonResponse(false, 'Alamat lengkap wajib diisi (sampai desa/kelurahan)');
            return;
        }
        
        try {
            $koperasi = new Koperasi();
            $id = $koperasi->create($data);
            
            jsonResponse(true, 'Koperasi berhasil didaftarkan', [
                'koperasi_id' => $id,
                'nama_koperasi' => $data['nama_koperasi']
            ]);
        } catch (Exception $e) {
            jsonResponse(false, 'Gagal mendaftarkan koperasi: ' . $e->getMessage());
        }
    }
    
    /**
     * Register user baru (Tab 1)
     */
    public static function registerUser() {
        $data = $_POST;
        
        // Cek apakah koperasi sudah ada
        $koperasi = new Koperasi();
        if (!$koperasi->exists()) {
            jsonResponse(false, 'Belum ada koperasi terdaftar. Silakan daftar koperasi terlebih dahulu.');
            return;
        }
        
        // Validasi required fields
        if (empty($data['username']) || empty($data['password']) || empty($data['nama_lengkap'])) {
            jsonResponse(false, 'Username, password, dan nama lengkap wajib diisi');
            return;
        }
        
        if (empty($data['koperasi_id'])) {
            jsonResponse(false, 'Pilih koperasi terlebih dahulu');
            return;
        }
        
        // Validasi format username (alphanumeric + underscore, 3-20 chars)
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            jsonResponse(false, 'Username harus 3-20 karakter, hanya huruf, angka, dan underscore');
            return;
        }
        
        // Validasi password minimal 8 karakter
        if (strlen($data['password']) < 8) {
            jsonResponse(false, 'Password minimal 8 karakter');
            return;
        }
        
        // Validasi email format jika diisi
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Format email tidak valid');
            return;
        }
        
        // Validasi format telepon jika diisi (Indonesia)
        if (!empty($data['telepon']) && !preg_match('/^[0-9+\-\s]{10,15}$/', $data['telepon'])) {
            jsonResponse(false, 'Format telepon tidak valid (10-15 digit)');
            return;
        }
        
        // Validasi alamat
        if (empty($data['Prop_desa'])) {
            jsonResponse(false, 'Alamat lengkap wajib diisi (sampai desa/kelurahan)');
            return;
        }
            $user = new User();
            
            // Cek username sudah ada
            if ($user->exists($data['username'])) {
                jsonResponse(false, 'Username sudah digunakan');
                return;
            }
            
            $userId = $user->register([
                'username' => $data['username'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'nama_lengkap' => $data['nama_lengkap'],
                'email' => $data['email'] ?? null,
                'telepon' => $data['telepon'] ?? null,
                'role_id' => $data['role_id'] ?? 1, // default anggota
                'koperasi_id' => $data['koperasi_id'],
                'Prop_desa' => $data['Prop_desa'],
                'alamat_lengkap' => $data['alamat_lengkap'] ?? null
            ]);
            
            jsonResponse(true, 'User berhasil didaftarkan', [
                'user_id' => $userId,
                'username' => $data['username']
            ]);
        } catch (Exception $e) {
            jsonResponse(false, 'Gagal mendaftarkan user: ' . $e->getMessage());
        }
    }
    
    public static function login() {
        // Simple session-based rate limiting: max 5 attempts per 5 minutes
        if (!isset($_SESSION)) {
            session_start();
        }
        $attemptKey = 'login_attempts';
        $now = time();
        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [];
        }
        // Filter attempts within last 5 minutes
        $_SESSION[$attemptKey] = array_filter($_SESSION[$attemptKey], function($ts) use ($now) {
            return ($now - $ts) <= 300;
        });
        if (count($_SESSION[$attemptKey]) >= 5) {
            jsonResponse(false, 'Terlalu banyak percobaan login. Coba lagi setelah 5 menit.');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            jsonResponse(false, 'Username dan password wajib diisi');
            return;
        }

        $user = new User();
        $auth = $user->authenticate($username, $password);

        if ($auth) {
            // reset attempts on success
            $_SESSION[$attemptKey] = [];
            $_SESSION['user_id'] = $auth['id'];
            $_SESSION['role'] = $auth['name'];
            jsonResponse(true, 'Login berhasil', [
                'role' => $auth['name']
            ]);
        } else {
            $_SESSION[$attemptKey][] = $now;
            jsonResponse(false, 'Login gagal');
        }
    }
}
?>

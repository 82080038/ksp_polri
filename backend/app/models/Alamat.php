<?php
// app/models/Alamat.php
require_once __DIR__ . '/../../config/alamat_db.php';

class Alamat {
    private $pdo;
    private static $cache = [];
    private static $cacheExpiry = 3600; // 1 hour
    
    public function __construct() {
        $this->pdo = AlamatDB::getConnection();
    }
    
    /**
     * Get cached data or fetch from database
     */
    private function getCached($key, $callback) {
        $now = time();
        
        if (isset(self::$cache[$key]) && (self::$cache[$key]['expiry'] > $now)) {
            return self::$cache[$key]['data'];
        }
        
        $data = $callback();
        self::$cache[$key] = [
            'data' => $data,
            'expiry' => $now + self::$cacheExpiry
        ];
        
        return $data;
    }
    
    /**
     * Clear cache for specific key
     */
    private function clearCache($key = null) {
        if ($key) {
            unset(self::$cache[$key]);
        } else {
            self::$cache = [];
        }
    }
    
    /**
     * Get semua provinsi
     */
    public function getProvinsi() {
        return $this->getCached('provinsi', function() {
            if (!$this->pdo) return [];
            
            try {
                // Coba struktur tabel yang umum
                $tables = ['provinsi', 'provinces', 'propinsi', 'wilayah_provinsi'];
                foreach ($tables as $table) {
                    try {
                        $stmt = $this->pdo->query("SELECT id, nama FROM $table ORDER BY nama");
                        return $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        continue;
                    }
                }
                return [];
            } catch (Exception $e) {
                error_log("Error getProvinsi: " . $e->getMessage());
                return [];
            }
        });
    }
    
    /**
     * Get kabupaten/kota berdasarkan provinsi_id
     */
    public function getKabupaten($provinsiId) {
        if (!$this->pdo) return [];
        
        try {
            $tables = ['kabupaten', 'regencies', 'kota', 'wilayah_kabupaten'];
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE provinsi_id = ? ORDER BY nama");
                    $stmt->execute([$provinsiId]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    continue;
                }
            }
            return [];
        } catch (Exception $e) {
            error_log("Error getKabupaten: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get kecamatan berdasarkan kabupaten_id
     */
    public function getKecamatan($kabupatenId) {
        if (!$this->pdo) return [];
        
        try {
            $tables = ['kecamatan', 'districts', 'wilayah_kecamatan'];
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE kabupaten_id = ? ORDER BY nama");
                    $stmt->execute([$kabupatenId]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    continue;
                }
            }
            return [];
        } catch (Exception $e) {
            error_log("Error getKecamatan: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get desa/kelurahan berdasarkan kecamatan_id
     */
    public function getDesa($kecamatanId) {
        if (!$this->pdo) return [];
        
        try {
            $tables = ['desa', 'villages', 'kelurahan', 'wilayah_desa'];
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE kecamatan_id = ? ORDER BY nama");
                    $stmt->execute([$kecamatanId]);
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    continue;
                }
            }
            return [];
        } catch (Exception $e) {
            error_log("Error getDesa: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get detail desa berdasarkan ID
     */
    public function getDesaById($desaId) {
        if (!$this->pdo) return null;
        
        try {
            $tables = ['desa', 'villages', 'kelurahan', 'wilayah_desa'];
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = ?");
                    $stmt->execute([$desaId]);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    continue;
                }
            }
            return null;
        } catch (Exception $e) {
            error_log("Error getDesaById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get full address string from desa_id
     */
    public function getFullAddress($desaId) {
        if (!$this->pdo || !$desaId) return '';
        
        try {
            // Query untuk mendapatkan hierarki alamat
            // Sesuaikan dengan struktur tabel alamat_db
            $query = "
                SELECT 
                    d.nama as desa,
                    k.nama as kecamatan,
                    kb.nama as kabupaten,
                    p.nama as provinsi
                FROM desa d
                JOIN kecamatan k ON d.kecamatan_id = k.id
                JOIN kabupaten kb ON k.kabupaten_id = kb.id
                JOIN provinsi p ON kb.provinsi_id = p.id
                WHERE d.id = ?
            ";
            
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$desaId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return implode(', ', array_filter([
                    $result['desa'],
                    $result['kecamatan'],
                    $result['kabupaten'],
                    $result['provinsi']
                ]));
            }
            
            return '';
        } catch (Exception $e) {
            error_log("Error getFullAddress: " . $e->getMessage());
            return '';
        }
    }
}

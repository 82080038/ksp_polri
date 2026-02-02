<?php
// app/models/Koperasi.php
require_once __DIR__ . '/../../config/database.php';

class Koperasi {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    /**
     * Cek apakah ada koperasi yang terdaftar
     */
    public function exists() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM koperasi WHERE status = 'AKTIF'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Get koperasi by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM koperasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get semua koperasi aktif
     */
    public function getAllActive() {
        $stmt = $this->pdo->query("SELECT * FROM koperasi WHERE status = 'AKTIF' ORDER BY nama_koperasi");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Register koperasi baru
     */
    public function create($data) {
        $sql = "INSERT INTO koperasi (nama_koperasi, kode_koperasi, npwp, telepon, email, Prop_desa, alamat_lengkap, nama_pimpinan, jabatan_pimpinan) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['nama_koperasi'],
            $data['kode_koperasi'],
            $data['npwp'] ?? null,
            $data['telepon'] ?? null,
            $data['email'] ?? null,
            $data['Prop_desa'] ?? null,
            $data['alamat_lengkap'] ?? null,
            $data['nama_pimpinan'] ?? null,
            $data['jabatan_pimpinan'] ?? null
        ]);
        
        return $this->pdo->lastInsertId();
    }
    
    /**
     * Update koperasi
     */
    public function update($id, $data) {
        $sql = "UPDATE koperasi SET 
                nama_koperasi = ?, 
                kode_koperasi = ?,
                npwp = ?,
                telepon = ?,
                email = ?,
                Prop_desa = ?,
                alamat_lengkap = ?,
                nama_pimpinan = ?,
                jabatan_pimpinan = ?,
                status = ?
                WHERE id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['nama_koperasi'],
            $data['kode_koperasi'],
            $data['npwp'] ?? null,
            $data['telepon'] ?? null,
            $data['email'] ?? null,
            $data['Prop_desa'] ?? null,
            $data['alamat_lengkap'] ?? null,
            $data['nama_pimpinan'] ?? null,
            $data['jabatan_pimpinan'] ?? null,
            $data['status'] ?? 'AKTIF',
            $id
        ]);
    }
}

<?php
// app/controllers/AlamatController.php
require_once '../app/models/Alamat.php';
require_once '../app/views/json.php';

class AlamatController {
    
    /**
     * Get all provinces
     */
    public static function getProvinsi() {
        $alamat = new Alamat();
        $provinsi = $alamat->getProvinsi();
        
        jsonResponse(true, 'Daftar provinsi', ['provinsi' => $provinsi]);
    }
    
    /**
     * Get kabupaten/kota by province ID
     */
    public static function getKabupaten() {
        $provinsiId = $_GET['provinsi_id'] ?? '';
        
        if (empty($provinsiId)) {
            jsonResponse(false, 'Provinsi ID wajib diisi');
            return;
        }
        
        $alamat = new Alamat();
        $kabupaten = $alamat->getKabupaten($provinsiId);
        
        jsonResponse(true, 'Daftar kabupaten/kota', ['kabupaten' => $kabupaten]);
    }
    
    /**
     * Get kecamatan by kabupaten ID
     */
    public static function getKecamatan() {
        $kabupatenId = $_GET['kabupaten_id'] ?? '';
        
        if (empty($kabupatenId)) {
            jsonResponse(false, 'Kabupaten ID wajib diisi');
            return;
        }
        
        $alamat = new Alamat();
        $kecamatan = $alamat->getKecamatan($kabupatenId);
        
        jsonResponse(true, 'Daftar kecamatan', ['kecamatan' => $kecamatan]);
    }
    
    /**
     * Get desa/kelurahan by kecamatan ID
     */
    public static function getDesa() {
        $kecamatanId = $_GET['kecamatan_id'] ?? '';
        
        if (empty($kecamatanId)) {
            jsonResponse(false, 'Kecamatan ID wajib diisi');
            return;
        }
        
        $alamat = new Alamat();
        $desa = $alamat->getDesa($kecamatanId);
        
        jsonResponse(true, 'Daftar desa/kelurahan', ['desa' => $desa]);
    }
    
    /**
     * Get full address detail from desa ID
     */
    public static function getAlamatDetail() {
        $desaId = $_GET['desa_id'] ?? '';
        
        if (empty($desaId)) {
            jsonResponse(false, 'Desa ID wajib diisi');
            return;
        }
        
        $alamat = new Alamat();
        $detail = $alamat->getDesaById($desaId);
        $fullAddress = $alamat->getFullAddress($desaId);
        
        jsonResponse(true, 'Detail alamat', [
            'desa' => $detail,
            'full_address' => $fullAddress
        ]);
    }
}

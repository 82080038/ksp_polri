<?php
// app/controllers/ShuController.php
require_once '../app/models/Shu.php';
require_once '../app/views/json.php';

class ShuController {
    public static function generate() {
        $tahun = $_POST['tahun'];
        $total_shu = $_POST['total_shu'];

        $shu = new Shu();
        $shu->generate($tahun, $total_shu);

        // Audit
        jsonResponse(true, "SHU tahun $tahun berhasil digenerate");
    }

    public static function list() {
        $tahun = $_GET['tahun'];
        $shu = new Shu();
        $data = $shu->getByTahun($tahun);
        jsonResponse(true, 'Data SHU', $data);
    }

    public static function anggota() {
        $tahun = $_GET['tahun'];
        $shu = new Shu();
        $jumlah = $shu->getByAnggota($_SESSION['anggota_id'], $tahun);
        jsonResponse(true, 'SHU Anda', $jumlah);
    }
}
?>

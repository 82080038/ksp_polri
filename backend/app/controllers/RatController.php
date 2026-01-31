<?php
// app/controllers/RatController.php
require_once '../app/models/Rat.php';
require_once '../app/views/json.php';

class RatController {
    public static function create() {
        $tahun = $_POST['tahun'];
        $rat = new Rat();
        if ($rat->create($tahun)) {
            // Audit
            jsonResponse(true, "RAT tahun $tahun dibuat");
        } else {
            jsonResponse(false, 'Gagal');
        }
    }

    public static function laporan() {
        $tahun = $_GET['tahun'];
        $rat = new Rat();
        $data = $rat->getLaporan($tahun);
        jsonResponse(true, 'Laporan RAT', $data);
    }

    public static function sahkan() {
        $id = $_POST['rat_id'];
        $rat = new Rat();
        if ($rat->sahkan($id)) {
            // Audit
            jsonResponse(true, 'RAT disahkan');
        } else {
            jsonResponse(false, 'Gagal');
        }
    }
}
?>

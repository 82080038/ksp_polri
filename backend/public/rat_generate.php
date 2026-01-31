<?php
// backend/public/rat_generate.php
require_once '../config/database.php';
require_once '../services/RatService.php';
require_once '../pdf/RatPdf.php';
require_once '../libs/qrlib.php';

$tahun = $_GET['tahun'] ?? date('Y');

$service = new RatService($mysqli);
$data = $service->getRingkasan($tahun);

// Generate hash
$hash = hash('sha256', json_encode($data));

// Generate nomor dokumen
$nomor = "RAT/$tahun/KSP-POLRI/" . str_pad(rand(1,9999),4,'0',STR_PAD_LEFT);

// Save to dokumen_hash
$mysqli->query("INSERT INTO dokumen_hash (nomor_dokumen, jenis_dokumen, tahun, hash, dibuat_oleh) VALUES ('$nomor', 'RAT', $tahun, '$hash', 1)"); // Assume user_id 1

// Generate QR
$qrPath = "../temp/qr_$tahun.png";
$verifyUrl = "http://localhost/verifikasi.php?hash=$hash";
QRcode::png($verifyUrl, $qrPath);

$pdf = new RatPdf();
$pdf->AddPage();

$pdf->section("Ringkasan Keanggotaan");
$pdf->row("Anggota Awal Tahun", $data['anggota_awal']);
$pdf->row("Anggota Akhir Tahun", $data['anggota_akhir']);

$pdf->Ln(5);
$pdf->section("Ringkasan Keuangan");
$pdf->row("Total Simpanan", "Rp ".number_format($data['total_simpanan']));
$pdf->row("Pinjaman Beredar", "Rp ".number_format($data['pinjaman_edar']));
$pdf->row("SHU Tahun Berjalan", "Rp ".number_format($data['shu']));

// Add QR
$pdf->Ln(10);
$pdf->section("Verifikasi Dokumen");
$pdf->Image($qrPath, 10, $pdf->GetY(), 30, 30);
$pdf->Cell(0,10,"Nomor Dokumen: $nomor",0,1);
$pdf->Cell(0,10,"Hash: $hash",0,1);

// Add signatures
$pdf->Ln(20);
$pdf->Cell(90,10,"Ketua Koperasi",0,0,'C');
$pdf->Cell(90,10,"Pengawas",0,1,'C');
// Assume signature images exist
if (file_exists('../assets/signature/ketua.png')) {
    $pdf->Image('../assets/signature/ketua.png', 30, $pdf->GetY()-10, 40);
}
if (file_exists('../assets/signature/pengawas.png')) {
    $pdf->Image('../assets/signature/pengawas.png', 130, $pdf->GetY()-10, 40);
}

$pdf->Output("I", "RAT_KSP_POLRI_$tahun.pdf");

// Clean up
unlink($qrPath);
?>

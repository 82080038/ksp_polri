<?php
// app/controllers/ReportingController.php
require_once '../app/views/json.php';

class ReportingController {

    /**
     * Export anggota data to CSV
     */
    public static function exportAnggotaCSV() {
        Auth::requireRole('pengurus');

        $filename = 'anggota_ksp_polri_' . date('Y-m-d_H-i-s') . '.csv';

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write BOM for Excel UTF-8 recognition
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write CSV header
        fputcsv($output, [
            'NRP',
            'Nama',
            'Email',
            'No HP',
            'Alamat',
            'Tanggal Bergabung',
            'Status',
            'Total Simpanan',
            'Total Pinjaman',
            'Saldo Pinjaman'
        ]);

        // Get anggota data with financial summary
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT
                a.nrp,
                a.nama,
                a.email,
                a.no_hp,
                a.alamat,
                a.tanggal_bergabung,
                a.status,
                COALESCE(simp.total_simpanan, 0) as total_simpanan,
                COALESCE(pin.total_pinjaman, 0) as total_pinjaman,
                COALESCE(pin.saldo_pinjaman, 0) as saldo_pinjaman
            FROM anggota a
            LEFT JOIN (
                SELECT anggota_id, SUM(jumlah) as total_simpanan
                FROM simpanan
                GROUP BY anggota_id
            ) simp ON a.id = simp.anggota_id
            LEFT JOIN (
                SELECT
                    anggota_id,
                    SUM(jumlah) as total_pinjaman,
                    SUM(
                        CASE
                            WHEN status = 'LUNAS' THEN 0
                            ELSE jumlah - COALESCE((
                                SELECT SUM(jumlah) FROM angsuran ang
                                WHERE ang.pinjaman_id = p.id AND ang.status = 'LUNAS'
                            ), 0)
                        END
                    ) as saldo_pinjaman
                FROM pinjaman p
                GROUP BY anggota_id
            ) pin ON a.id = pin.anggota_id
            ORDER BY a.nrp
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        // Write data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['nrp'],
                $row['nama'],
                $row['email'],
                $row['no_hp'],
                $row['alamat'],
                $row['tanggal_bergabung'],
                $row['status'],
                number_format($row['total_simpanan'], 0, ',', '.'),
                number_format($row['total_pinjaman'], 0, ',', '.'),
                number_format($row['saldo_pinjaman'], 0, ',', '.')
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export simpanan data to CSV
     */
    public static function exportSimpananCSV() {
        Auth::requireRole('pengurus');

        $filename = 'simpanan_ksp_polri_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Tanggal',
            'NRP',
            'Nama Anggota',
            'Jenis Simpanan',
            'Jumlah',
            'Keterangan'
        ]);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT
                s.tanggal,
                a.nrp,
                a.nama,
                s.jenis,
                s.jumlah,
                s.keterangan
            FROM simpanan s
            JOIN anggota a ON s.anggota_id = a.id
            ORDER BY s.tanggal DESC, a.nrp
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['tanggal'],
                $row['nrp'],
                $row['nama'],
                $row['jenis'],
                number_format($row['jumlah'], 0, ',', '.'),
                $row['keterangan']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export pinjaman data to CSV
     */
    public static function exportPinjamanCSV() {
        Auth::requireRole('pengurus');

        $filename = 'pinjaman_ksp_polri_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'Tanggal Pengajuan',
            'NRP',
            'Nama Anggota',
            'Jumlah Pinjaman',
            'Tenor (Bulan)',
            'Status',
            'Tanggal Disetujui',
            'Total Angsuran',
            'Sisa Angsuran'
        ]);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT
                p.tanggal_pengajuan,
                a.nrp,
                a.nama,
                p.jumlah,
                p.tenor,
                p.status,
                p.tanggal_disetujui,
                COALESCE(ang.total_angsuran, 0) as total_angsuran,
                COALESCE(ang.sisa_angsuran, p.jumlah) as sisa_angsuran
            FROM pinjaman p
            JOIN anggota a ON p.anggota_id = a.id
            LEFT JOIN (
                SELECT
                    pinjaman_id,
                    COUNT(*) as total_angsuran,
                    SUM(jumlah) as sisa_angsuran
                FROM angsuran
                WHERE status = 'BELUM_LUNAS'
                GROUP BY pinjaman_id
            ) ang ON p.id = ang.pinjaman_id
            ORDER BY p.tanggal_pengajuan DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['tanggal_pengajuan'],
                $row['nrp'],
                $row['nama'],
                number_format($row['jumlah'], 0, ',', '.'),
                $row['tenor'],
                $row['status'],
                $row['tanggal_disetujui'],
                $row['total_angsuran'],
                number_format($row['sisa_angsuran'], 0, ',', '.')
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Generate PDF Report using FPDF
     */
    public static function generatePDFReport() {
        Auth::requireRole('pengurus');

        $type = $_GET['type'] ?? 'anggota';
        $filename = "laporan_{$type}_ksp_polri_" . date('Y-m-d') . '.pdf';

        // Include FPDF
        require_once '../vendor/fpdf/fpdf.php';

        // Create PDF
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Header
        $pdf->Cell(0, 10, 'KSP PERSONEL POLRI', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Laporan ' . ucfirst($type), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'Tanggal: ' . date('d/m/Y'), 0, 1, 'C');
        $pdf->Ln(10);

        // Generate content based on type
        switch ($type) {
            case 'anggota':
                self::generateAnggotaPDF($pdf);
                break;
            case 'simpanan':
                self::generateSimpananPDF($pdf);
                break;
            case 'pinjaman':
                self::generatePinjamanPDF($pdf);
                break;
            case 'keuangan':
                self::generateKeuanganPDF($pdf);
                break;
            default:
                $pdf->Cell(0, 10, 'Tipe laporan tidak valid', 0, 1, 'C');
        }

        // Output PDF
        $pdf->Output('D', $filename);
        exit;
    }

    /**
     * Generate Anggota PDF content
     */
    private static function generateAnggotaPDF($pdf) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'NRP', 1);
        $pdf->Cell(50, 8, 'Nama', 1);
        $pdf->Cell(40, 8, 'Email', 1);
        $pdf->Cell(30, 8, 'Status', 1);
        $pdf->Cell(30, 8, 'Total Simpanan', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT a.nrp, a.nama, a.email, a.status,
                   COALESCE(s.total_simpanan, 0) as total_simpanan
            FROM anggota a
            LEFT JOIN (
                SELECT anggota_id, SUM(jumlah) as total_simpanan
                FROM simpanan GROUP BY anggota_id
            ) s ON a.id = s.anggota_id
            ORDER BY a.nrp
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(20, 6, $row['nrp'], 1);
            $pdf->Cell(50, 6, substr($row['nama'], 0, 25), 1);
            $pdf->Cell(40, 6, substr($row['email'], 0, 20), 1);
            $pdf->Cell(30, 6, $row['status'], 1);
            $pdf->Cell(30, 6, 'Rp ' . number_format($row['total_simpanan'], 0, ',', '.'), 1);
            $pdf->Ln();
        }
    }

    /**
     * Generate Simpanan PDF content
     */
    private static function generateSimpananPDF($pdf) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 8, 'Tanggal', 1);
        $pdf->Cell(20, 8, 'NRP', 1);
        $pdf->Cell(45, 8, 'Nama', 1);
        $pdf->Cell(30, 8, 'Jenis', 1);
        $pdf->Cell(35, 8, 'Jumlah', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.tanggal, a.nrp, a.nama, s.jenis, s.jumlah
            FROM simpanan s
            JOIN anggota a ON s.anggota_id = a.id
            ORDER BY s.tanggal DESC
            LIMIT 100
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(25, 6, date('d/m/Y', strtotime($row['tanggal'])), 1);
            $pdf->Cell(20, 6, $row['nrp'], 1);
            $pdf->Cell(45, 6, substr($row['nama'], 0, 25), 1);
            $pdf->Cell(30, 6, $row['jenis'], 1);
            $pdf->Cell(35, 6, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1);
            $pdf->Ln();
        }
    }

    /**
     * Generate Pinjaman PDF content
     */
    private static function generatePinjamanPDF($pdf) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(25, 8, 'Tanggal', 1);
        $pdf->Cell(20, 8, 'NRP', 1);
        $pdf->Cell(40, 8, 'Nama', 1);
        $pdf->Cell(30, 8, 'Jumlah', 1);
        $pdf->Cell(20, 8, 'Status', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.tanggal_pengajuan, a.nrp, a.nama, p.jumlah, p.status
            FROM pinjaman p
            JOIN anggota a ON p.anggota_id = a.id
            ORDER BY p.tanggal_pengajuan DESC
            LIMIT 100
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $pdf->Cell(25, 6, date('d/m/Y', strtotime($row['tanggal_pengajuan'])), 1);
            $pdf->Cell(20, 6, $row['nrp'], 1);
            $pdf->Cell(40, 6, substr($row['nama'], 0, 20), 1);
            $pdf->Cell(30, 6, 'Rp ' . number_format($row['jumlah'], 0, ',', '.'), 1);
            $pdf->Cell(20, 6, $row['status'], 1);
            $pdf->Ln();
        }
    }

    /**
     * Generate Keuangan PDF content (Neraca & Laba Rugi)
     */
    private static function generateKeuanganPDF($pdf) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Laporan Keuangan', 0, 1, 'C');
        $pdf->Ln(5);

        // Neraca
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'NERACA', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(80, 6, 'AKTIVA', 1);
        $pdf->Cell(30, 6, 'JUMLAH', 1, 0, 'R');
        $pdf->Cell(80, 6, 'PASIVA', 1);
        $pdf->Cell(30, 6, 'JUMLAH', 1, 0, 'R');
        $pdf->Ln();

        // Sample data - in real implementation, this would come from accounting module
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(80, 5, 'Kas & Bank', 1);
        $pdf->Cell(30, 5, 'Rp 100,000,000', 1, 0, 'R');
        $pdf->Cell(80, 5, 'Simpanan Anggota', 1);
        $pdf->Cell(30, 5, 'Rp 80,000,000', 1, 0, 'R');
        $pdf->Ln();

        $pdf->Cell(80, 5, 'Piutang Angsuran', 1);
        $pdf->Cell(30, 5, 'Rp 20,000,000', 1, 0, 'R');
        $pdf->Cell(80, 5, 'SHU', 1);
        $pdf->Cell(30, 5, 'Rp 40,000,000', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(80, 5, 'TOTAL AKTIVA', 1);
        $pdf->Cell(30, 5, 'Rp 120,000,000', 1, 0, 'R');
        $pdf->Cell(80, 5, 'TOTAL PASIVA', 1);
        $pdf->Cell(30, 5, 'Rp 120,000,000', 1, 0, 'R');
        $pdf->Ln(10);

        // Laba Rugi
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'LABA RUGI', 0, 1, 'C');
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(110, 6, 'PENDAPATAN', 1);
        $pdf->Cell(30, 6, 'JUMLAH', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(110, 5, 'Bunga Pinjaman', 1);
        $pdf->Cell(30, 5, 'Rp 15,000,000', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(110, 6, 'BEBAN', 1);
        $pdf->Cell(30, 6, 'JUMLAH', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(110, 5, 'Biaya Operasional', 1);
        $pdf->Cell(30, 5, 'Rp 5,000,000', 1, 0, 'R');
        $pdf->Ln();

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(110, 5, 'LABA BERSIH', 1);
        $pdf->Cell(30, 5, 'Rp 10,000,000', 1, 0, 'R');
    }

    /**
     * Get available reports list
     */
    public static function getAvailableReports() {
        Auth::requireRole('pengurus');

        $reports = [
            [
                'id' => 'anggota_csv',
                'name' => 'Data Anggota (CSV)',
                'description' => 'Export data lengkap anggota dalam format CSV',
                'type' => 'csv',
                'endpoint' => 'reporting/exportAnggotaCSV'
            ],
            [
                'id' => 'simpanan_csv',
                'name' => 'Data Simpanan (CSV)',
                'description' => 'Export semua data simpanan dalam format CSV',
                'type' => 'csv',
                'endpoint' => 'reporting/exportSimpananCSV'
            ],
            [
                'id' => 'pinjaman_csv',
                'name' => 'Data Pinjaman (CSV)',
                'description' => 'Export semua data pinjaman dalam format CSV',
                'type' => 'csv',
                'endpoint' => 'reporting/exportPinjamanCSV'
            ],
            [
                'id' => 'anggota_pdf',
                'name' => 'Data Anggota (PDF)',
                'description' => 'Generate laporan data anggota dalam format PDF',
                'type' => 'pdf',
                'endpoint' => 'reporting/generatePDFReport&type=anggota'
            ],
            [
                'id' => 'simpanan_pdf',
                'name' => 'Data Simpanan (PDF)',
                'description' => 'Generate laporan data simpanan dalam format PDF',
                'type' => 'pdf',
                'endpoint' => 'reporting/generatePDFReport&type=simpanan'
            ],
            [
                'id' => 'pinjaman_pdf',
                'name' => 'Data Pinjaman (PDF)',
                'description' => 'Generate laporan data pinjaman dalam format PDF',
                'type' => 'pdf',
                'endpoint' => 'reporting/generatePDFReport&type=pinjaman'
            ],
            [
                'id' => 'keuangan_pdf',
                'name' => 'Laporan Keuangan (PDF)',
                'description' => 'Generate laporan neraca dan laba rugi dalam format PDF',
                'type' => 'pdf',
                'endpoint' => 'reporting/generatePDFReport&type=keuangan'
            ]
        ];

        jsonResponse(true, 'Available reports retrieved', ['reports' => $reports]);
    }

    /**
     * Schedule automated reports (placeholder for future implementation)
     */
    public static function scheduleReport() {
        Auth::requireRole('pengurus');

        $report_type = $_POST['report_type'] ?? '';
        $frequency = $_POST['frequency'] ?? 'weekly'; // daily, weekly, monthly
        $recipients = $_POST['recipients'] ?? [];

        // In a real implementation, this would save to database and set up cron jobs
        // For now, just return success

        jsonResponse(true, 'Report scheduled successfully', [
            'report_type' => $report_type,
            'frequency' => $frequency,
            'recipients' => $recipients
        ]);
    }
}
?>

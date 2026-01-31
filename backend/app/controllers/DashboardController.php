<?php
// app/controllers/DashboardController.php
require_once '../app/models/Anggota.php';
require_once '../app/models/Simpanan.php';
require_once '../app/models/Pinjaman.php';
require_once '../app/models/Angsuran.php';
require_once '../app/models/Shu.php';
require_once '../app/models/ChartOfAccounts.php';
require_once '../app/models/GeneralLedger.php';
require_once '../app/views/json.php';

class DashboardController {
    
    public static function getStats() {
        $db = Database::getConnection();
        $stats = [];
        
        // Total anggota
        $result = $db->query("SELECT COUNT(*) as total FROM anggota WHERE status = 'AKTIF'");
        $stats['total_anggota'] = $result->fetch_assoc()['total'];
        
        // Total simpanan
        $result = $db->query("SELECT SUM(jumlah) as total FROM simpanan");
        $stats['total_simpanan'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Pinjaman aktif
        $result = $db->query("SELECT COUNT(*) as total, SUM(jumlah) as nominal FROM pinjaman WHERE status IN ('DIAJUKAN', 'DISETUJUI')");
        $row = $result->fetch_assoc();
        $stats['pinjaman_aktif'] = $row['total'];
        $stats['nominal_pinjaman'] = $row['nominal'] ?? 0;
        
        // Tunggakan angsuran
        $result = $db->query("
            SELECT COUNT(DISTINCT p.id) as total 
            FROM pinjaman p 
            JOIN angsuran a ON p.id = a.pinjaman_id 
            WHERE p.status = 'DISETUJUI' 
            AND a.tanggal < CURDATE() - INTERVAL 30 DAY
        ");
        $stats['tunggakan'] = $result->fetch_assoc()['total'];
        
        // SHU tahun berjalan
        $current_year = date('Y');
        $result = $db->query("SELECT SUM(jumlah) as total FROM shu WHERE tahun = $current_year");
        $stats['shu_tahun_ini'] = $result->fetch_assoc()['total'] ?? 0;
        
        jsonResponse(true, 'Dashboard stats', $stats);
    }
    
    public static function getSimpananTrend() {
        $db = Database::getConnection();
        $current_year = date('Y');
        
        $result = $db->query("
            SELECT 
                MONTH(tanggal) as bulan,
                jenis,
                SUM(jumlah) as total
            FROM simpanan
            WHERE YEAR(tanggal) = $current_year
            GROUP BY MONTH(tanggal), jenis
            ORDER BY bulan ASC
        ");
        
        $data = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        // Initialize arrays
        $pokok = array_fill(0, 12, 0);
        $wajib = array_fill(0, 12, 0);
        $sukarela = array_fill(0, 12, 0);
        
        while ($row = $result->fetch_assoc()) {
            $index = (int)$row['bulan'] - 1;
            switch($row['jenis']) {
                case 'POKOK': $pokok[$index] = (float)$row['total']; break;
                case 'WAJIB': $wajib[$index] = (float)$row['total']; break;
                case 'SUKARELA': $sukarela[$index] = (float)$row['total']; break;
            }
        }
        
        jsonResponse(true, 'Simpanan trend', [
            'labels' => $months,
            'datasets' => [
                ['label' => 'Pokok', 'data' => $pokok, 'color' => '#4CAF50'],
                ['label' => 'Wajib', 'data' => $wajib, 'color' => '#2196F3'],
                ['label' => 'Sukarela', 'data' => $sukarela, 'color' => '#FF9800']
            ]
        ]);
    }
    
    public static function getPinjamanStats() {
        $db = Database::getConnection();
        
        // Status distribution
        $result = $db->query("
            SELECT status, COUNT(*) as total, SUM(jumlah) as nominal
            FROM pinjaman
            GROUP BY status
        ");
        
        $status_data = [];
        $colors = [
            'DIAJUKAN' => '#FFC107',
            'DISETUJUI' => '#4CAF50',
            'DITOLAK' => '#F44336',
            'LUNAS' => '#2196F3'
        ];
        
        while ($row = $result->fetch_assoc()) {
            $status_data[] = [
                'status' => $row['status'],
                'count' => (int)$row['total'],
                'nominal' => (float)$row['nominal'],
                'color' => $colors[$row['status']] ?? '#9E9E9E'
            ];
        }
        
        // Monthly trend
        $current_year = date('Y');
        $result = $db->query("
            SELECT 
                MONTH(created_at) as bulan,
                COUNT(*) as total,
                SUM(jumlah) as nominal
            FROM pinjaman
            WHERE YEAR(created_at) = $current_year
            GROUP BY MONTH(created_at)
            ORDER BY bulan ASC
        ");
        
        $monthly = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $monthly[(int)$row['bulan'] - 1] = (float)$row['nominal'];
        }
        
        jsonResponse(true, 'Pinjaman stats', [
            'status_distribution' => $status_data,
            'monthly_trend' => $monthly
        ]);
    }
    
    public static function getAngsuranStats() {
        $db = Database::getConnection();
        $current_year = date('Y');
        
        // Pembayaran per bulan
        $result = $db->query("
            SELECT 
                MONTH(tanggal) as bulan,
                SUM(jumlah) as total
            FROM angsuran
            WHERE YEAR(tanggal) = $current_year
            GROUP BY MONTH(tanggal)
            ORDER BY bulan ASC
        ");
        
        $pembayaran = array_fill(0, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $pembayaran[(int)$row['bulan'] - 1] = (float)$row['total'];
        }
        
        // Target angsuran (from pinjaman yang disetujui / tenor)
        $result = $db->query("
            SELECT 
                SUM(jumlah / tenor) as target_per_bulan
            FROM pinjaman
            WHERE status = 'DISETUJUI'
        ");
        $target = $result->fetch_assoc()['target_per_bulan'] ?? 0;
        
        jsonResponse(true, 'Angsuran stats', [
            'pembayaran_per_bulan' => $pembayaran,
            'target_per_bulan' => (float)$target,
            'total_terbayar' => array_sum($pembayaran)
        ]);
    }
    
    public static function getShuHistory() {
        $db = Database::getConnection();
        
        $result = $db->query("
            SELECT 
                tahun,
                SUM(jumlah) as total
            FROM shu
            GROUP BY tahun
            ORDER BY tahun ASC
            LIMIT 5
        ");
        
        $years = [];
        $values = [];
        
        while ($row = $result->fetch_assoc()) {
            $years[] = $row['tahun'];
            $values[] = (float)$row['total'];
        }
        
        jsonResponse(true, 'SHU history', [
            'labels' => $years,
            'data' => $values
        ]);
    }
    
    public static function getFinancialOverview() {
        $db = Database::getConnection();
        $ledger = new GeneralLedger();
        
        // Get current month data
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        
        // Aset vs Kewajiban
        $aset = $db->query("
            SELECT SUM(saldo_awal) as total FROM chart_of_accounts WHERE kategori = 'ASET'
        ")->fetch_assoc()['total'] ?? 0;
        
        $kewajiban = $db->query("
            SELECT SUM(saldo_awal) as total FROM chart_of_accounts WHERE kategori = 'KEWAJIBAN'
        ")->fetch_assoc()['total'] ?? 0;
        
        $modal = $db->query("
            SELECT SUM(saldo_awal) as total FROM chart_of_accounts WHERE kategori = 'MODAL'
        ")->fetch_assoc()['total'] ?? 0;
        
        jsonResponse(true, 'Financial overview', [
            'aset' => (float)$aset,
            'kewajiban' => (float)$kewajiban,
            'modal' => (float)$modal,
            'ekuitas' => (float)($modal + $aset - $kewajiban)
        ]);
    }
    
    public static function getRecentActivities() {
        $db = Database::getConnection();
        
        // Recent anggota
        $result = $db->query("
            SELECT nama, nrp, created_at, 'Anggota Baru' as type
            FROM anggota
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $activities = $result->fetch_all(MYSQLI_ASSOC);
        
        // Recent pinjaman
        $result = $db->query("
            SELECT a.nama, p.jumlah, p.status, p.created_at
            FROM pinjaman p
            JOIN anggota a ON p.anggota_id = a.id
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        $pinjaman = $result->fetch_all(MYSQLI_ASSOC);
        foreach ($pinjaman as &$p) {
            $p['type'] = 'Pinjaman ' . $p['status'];
            $activities[] = $p;
        }
        
        // Sort by date
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        jsonResponse(true, 'Recent activities', array_slice($activities, 0, 10));
    }
}
?>

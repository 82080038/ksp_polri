<?php
// app/models/GeneralLedger.php
require_once '../core/Database.php';

class GeneralLedger {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getByAccount($account_id, $start_date = null, $end_date = null) {
        if ($start_date && $end_date) {
            $stmt = $this->db->prepare("SELECT gl.*, coa.kode_akun, coa.nama_akun FROM general_ledger gl JOIN chart_of_accounts coa ON gl.account_id = coa.id WHERE gl.account_id = ? AND gl.tanggal BETWEEN ? AND ? ORDER BY gl.tanggal ASC, gl.id ASC");
            $stmt->bind_param("iss", $account_id, $start_date, $end_date);
        } else {
            $stmt = $this->db->prepare("SELECT gl.*, coa.kode_akun, coa.nama_akun FROM general_ledger gl JOIN chart_of_accounts coa ON gl.account_id = coa.id WHERE gl.account_id = ? ORDER BY gl.tanggal ASC, gl.id ASC");
            $stmt->bind_param("i", $account_id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getSaldoAkun($account_id) {
        $stmt = $this->db->prepare("SELECT SUM(debit) as total_debit, SUM(kredit) as total_kredit FROM general_ledger WHERE account_id = ?");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $stmt = $this->db->prepare("SELECT saldo_awal FROM chart_of_accounts WHERE id = ?");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $akun = $stmt->get_result()->fetch_assoc();
        
        $saldo_awal = $akun ? $akun['saldo_awal'] : 0;
        return $saldo_awal + ($result['total_debit'] ?? 0) - ($result['total_kredit'] ?? 0);
    }

    public function getNeracaSaldo($start_date = null, $end_date = null) {
        $query = "SELECT coa.id, coa.kode_akun, coa.nama_akun, coa.kategori, coa.saldo_awal, SUM(gl.debit) as total_debit, SUM(gl.kredit) as total_kredit FROM chart_of_accounts coa LEFT JOIN general_ledger gl ON coa.id = gl.account_id";
        
        if ($start_date && $end_date) {
            $query .= " AND gl.tanggal BETWEEN '$start_date' AND '$end_date'";
        }
        
        $query .= " GROUP BY coa.id ORDER BY coa.kode_akun ASC";
        
        $result = $this->db->query($query);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        
        foreach ($data as &$row) {
            $row['saldo_akhir'] = $row['saldo_awal'] + ($row['total_debit'] ?? 0) - ($row['total_kredit'] ?? 0);
        }
        
        return $data;
    }

    public function getLabaRugi($start_date, $end_date) {
        $pendapatan = $this->getByKategoriRange('PENDAPATAN', $start_date, $end_date);
        $beban = $this->getByKategoriRange('BEBAN', $start_date, $end_date);
        
        $total_pendapatan = 0;
        foreach ($pendapatan as $p) {
            $total_pendapatan += $p['saldo'];
        }
        
        $total_beban = 0;
        foreach ($beban as $b) {
            $total_beban += $b['saldo'];
        }
        
        return [
            'pendapatan' => $pendapatan,
            'total_pendapatan' => $total_pendapatan,
            'beban' => $beban,
            'total_beban' => $total_beban,
            'laba_rugi' => $total_pendapatan - $total_beban
        ];
    }

    private function getByKategoriRange($kategori, $start_date, $end_date) {
        $stmt = $this->db->prepare("SELECT coa.id, coa.kode_akun, coa.nama_akun, SUM(gl.debit) as total_debit, SUM(gl.kredit) as total_kredit, (coa.saldo_awal + SUM(gl.debit) - SUM(gl.kredit)) as saldo FROM chart_of_accounts coa LEFT JOIN general_ledger gl ON coa.id = gl.account_id AND gl.tanggal BETWEEN ? AND ? WHERE coa.kategori = ? GROUP BY coa.id");
        $stmt->bind_param("sss", $start_date, $end_date, $kategori);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

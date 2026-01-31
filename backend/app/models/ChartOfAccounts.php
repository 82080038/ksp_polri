<?php
// app/models/ChartOfAccounts.php
require_once '../core/Database.php';

class ChartOfAccounts {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO chart_of_accounts (kode_akun, nama_akun, kategori, parent_id, saldo_awal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdd", $data['kode_akun'], $data['nama_akun'], $data['kategori'], $data['parent_id'], $data['saldo_awal']);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM chart_of_accounts ORDER BY kode_akun ASC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM chart_of_accounts WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE chart_of_accounts SET kode_akun = ?, nama_akun = ?, kategori = ?, parent_id = ?, saldo_awal = ? WHERE id = ?");
        $stmt->bind_param("sssddi", $data['kode_akun'], $data['nama_akun'], $data['kategori'], $data['parent_id'], $data['saldo_awal'], $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM chart_of_accounts WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getByKategori($kategori) {
        $stmt = $this->db->prepare("SELECT * FROM chart_of_accounts WHERE kategori = ? ORDER BY kode_akun ASC");
        $stmt->bind_param("s", $kategori);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getSaldoAkun($account_id) {
        $stmt = $this->db->prepare("SELECT SUM(debit) as total_debit, SUM(kredit) as total_kredit FROM general_ledger WHERE account_id = ?");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $akun = $this->getById($account_id);
        $saldo_awal = $akun ? $akun['saldo_awal'] : 0;
        
        return $saldo_awal + ($result['total_debit'] ?? 0) - ($result['total_kredit'] ?? 0);
    }
}
?>

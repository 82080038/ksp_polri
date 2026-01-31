<?php
// app/models/JournalEntry.php
require_once '../core/Database.php';

class JournalEntry {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create($data) {
        $this->db->begin_transaction();

        try {
            // Validate debit = kredit
            $totalDebit = 0; $totalKredit = 0;
            foreach ($data['details'] as $detail) {
                $totalDebit += (float)$detail['debit'];
                $totalKredit += (float)$detail['kredit'];
            }
            if (abs($totalDebit - $totalKredit) > 0.0001) {
                throw new Exception('Jurnal tidak seimbang (debit != kredit)');
            }

            $stmt = $this->db->prepare("INSERT INTO journal_entries (tanggal, nomor_jurnal, deskripsi, created_by) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $data['tanggal'], $data['nomor_jurnal'], $data['deskripsi'], $data['created_by']);
            $stmt->execute();
            $journal_id = $this->db->insert_id;

            foreach ($data['details'] as $detail) {
                $stmt = $this->db->prepare("INSERT INTO journal_entry_details (journal_entry_id, account_id, debit, kredit) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iidd", $journal_id, $detail['account_id'], $detail['debit'], $detail['kredit']);
                $stmt->execute();

                $this->updateGeneralLedger($detail['account_id'], $data['tanggal'], $detail['debit'], $detail['kredit'], 'journal', $journal_id);
            }

            $this->db->commit();
            return $journal_id;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    private function updateGeneralLedger($account_id, $tanggal, $debit, $kredit, $ref_type, $ref_id) {
        $stmt = $this->db->prepare("SELECT saldo FROM general_ledger WHERE account_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        $saldo_sebelumnya = $result ? $result['saldo'] : 0;
        $saldo_baru = $saldo_sebelumnya + $debit - $kredit;

        $stmt = $this->db->prepare("INSERT INTO general_ledger (account_id, tanggal, debit, kredit, saldo, reference_type, reference_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isdddsi", $account_id, $tanggal, $debit, $kredit, $saldo_baru, $ref_type, $ref_id);
        $stmt->execute();
    }

    public function getAll($limit = 50, $offset = 0) {
        $stmt = $this->db->prepare("SELECT * FROM journal_entries ORDER BY tanggal DESC, id DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM journal_entries WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $journal = $stmt->get_result()->fetch_assoc();
        
        if ($journal) {
            $stmt = $this->db->prepare("SELECT jed.*, coa.kode_akun, coa.nama_akun FROM journal_entry_details jed JOIN chart_of_accounts coa ON jed.account_id = coa.id WHERE jed.journal_entry_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $journal['details'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        
        return $journal;
    }

    public function getByDateRange($start_date, $end_date) {
        $stmt = $this->db->prepare("SELECT * FROM journal_entries WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC");
        $stmt->bind_param("ss", $start_date, $end_date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function generateNomorJurnal() {
        $prefix = 'JRNL-' . date('Ymd');
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM journal_entries WHERE DATE(created_at) = CURDATE()");
        $count = $stmt->fetch_assoc()['count'] + 1;
        return $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
?>

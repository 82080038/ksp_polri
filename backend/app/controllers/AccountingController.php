<?php
// app/controllers/AccountingController.php
require_once '../app/models/ChartOfAccounts.php';
require_once '../app/models/JournalEntry.php';
require_once '../app/models/GeneralLedger.php';
require_once '../app/models/FixedAsset.php';
require_once '../app/views/json.php';

class AccountingController {
    
    // ==================== CHART OF ACCOUNTS ====================
    
    public static function coaList() {
        $coa = new ChartOfAccounts();
        $data = $coa->getAll();
        jsonResponse(true, 'Daftar kode akun', $data);
    }

    public static function coaCreate() {
        $data = [
            'kode_akun' => $_POST['kode_akun'],
            'nama_akun' => $_POST['nama_akun'],
            'kategori' => $_POST['kategori'],
            'parent_id' => $_POST['parent_id'] ?? null,
            'saldo_awal' => $_POST['saldo_awal'] ?? 0
        ];

        $validKategori = ['ASET', 'KEWAJIBAN', 'MODAL', 'PENDAPATAN', 'BEBAN'];
        if (!in_array($data['kategori'], $validKategori)) {
            jsonResponse(false, 'Kategori akun tidak valid');
            return;
        }

        $coa = new ChartOfAccounts();
        if ($coa->create($data)) {
            jsonResponse(true, 'Kode akun berhasil dibuat');
        } else {
            jsonResponse(false, 'Gagal membuat kode akun');
        }
    }

    public static function coaDetail() {
        $id = $_GET['id'];
        $coa = new ChartOfAccounts();
        $data = $coa->getById($id);
        if ($data) {
            $data['saldo_saat_ini'] = $coa->getSaldoAkun($id);
            jsonResponse(true, 'Detail kode akun', $data);
        } else {
            jsonResponse(false, 'Kode akun tidak ditemukan');
        }
    }

    public static function coaUpdate() {
        $id = $_POST['id'];
        $data = [
            'kode_akun' => $_POST['kode_akun'],
            'nama_akun' => $_POST['nama_akun'],
            'kategori' => $_POST['kategori'],
            'parent_id' => $_POST['parent_id'] ?? null,
            'saldo_awal' => $_POST['saldo_awal'] ?? 0
        ];

        $coa = new ChartOfAccounts();
        if ($coa->update($id, $data)) {
            jsonResponse(true, 'Kode akun berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal mengupdate kode akun');
        }
    }

    public static function coaDelete() {
        $id = $_POST['id'];
        $coa = new ChartOfAccounts();
        if ($coa->delete($id)) {
            jsonResponse(true, 'Kode akun berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal menghapus kode akun');
        }
    }

    // ==================== JOURNAL ENTRIES ====================

    public static function jurnalList() {
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $jurnal = new JournalEntry();
        $data = $jurnal->getAll($limit, $offset);
        jsonResponse(true, 'Daftar jurnal', $data);
    }

    public static function jurnalCreate() {
        $jurnal = new JournalEntry();
        $nomor_jurnal = $jurnal->generateNomorJurnal();
        
        $details = [];
        $total_debit = 0;
        $total_kredit = 0;
        
        $account_ids = $_POST['account_id'] ?? [];
        $debits = $_POST['debit'] ?? [];
        $kredits = $_POST['kredit'] ?? [];
        
        for ($i = 0; $i < count($account_ids); $i++) {
            $debit = (float)($debits[$i] ?? 0);
            $kredit = (float)($kredits[$i] ?? 0);
            
            if ($debit > 0 || $kredit > 0) {
                $details[] = [
                    'account_id' => $account_ids[$i],
                    'debit' => $debit,
                    'kredit' => $kredit
                ];
                $total_debit += $debit;
                $total_kredit += $kredit;
            }
        }

        if ($total_debit != $total_kredit) {
            jsonResponse(false, 'Total debit dan kredit tidak seimbang');
            return;
        }

        if (count($details) < 2) {
            jsonResponse(false, 'Jurnal minimal memiliki 2 detail transaksi');
            return;
        }

        $data = [
            'tanggal' => $_POST['tanggal'],
            'nomor_jurnal' => $nomor_jurnal,
            'deskripsi' => $_POST['deskripsi'],
            'created_by' => $_SESSION['user_id'],
            'details' => $details
        ];

        $jurnal_id = $jurnal->create($data);
        if ($jurnal_id) {
            jsonResponse(true, 'Jurnal berhasil dibuat', ['jurnal_id' => $jurnal_id, 'nomor_jurnal' => $nomor_jurnal]);
        } else {
            jsonResponse(false, 'Gagal membuat jurnal');
        }
    }

    public static function jurnalDetail() {
        $id = $_GET['id'];
        $jurnal = new JournalEntry();
        $data = $jurnal->getById($id);
        if ($data) {
            jsonResponse(true, 'Detail jurnal', $data);
        } else {
            jsonResponse(false, 'Jurnal tidak ditemukan');
        }
    }

    // ==================== GENERAL LEDGER ====================

    public static function bukuBesar() {
        $account_id = $_GET['account_id'] ?? null;
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;
        
        if (!$account_id) {
            jsonResponse(false, 'Account ID diperlukan');
            return;
        }
        
        $ledger = new GeneralLedger();
        $data = $ledger->getByAccount($account_id, $start_date, $end_date);
        jsonResponse(true, 'Buku besar', $data);
    }

    public static function neracaSaldo() {
        $start_date = $_GET['start_date'] ?? null;
        $end_date = $_GET['end_date'] ?? null;
        
        $ledger = new GeneralLedger();
        $data = $ledger->getNeracaSaldo($start_date, $end_date);
        jsonResponse(true, 'Neraca saldo', $data);
    }

    public static function labaRugi() {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
        
        if (!$start_date || !$end_date) {
            jsonResponse(false, 'Tanggal awal dan akhir diperlukan');
            return;
        }
        
        $ledger = new GeneralLedger();
        $data = $ledger->getLabaRugi($start_date, $end_date);
        jsonResponse(true, 'Laporan laba rugi', $data);
    }

    // ==================== FIXED ASSETS ====================

    public static function assetList() {
        $asset = new FixedAsset();
        $data = $asset->getAll();
        jsonResponse(true, 'Daftar aset tetap', $data);
    }

    public static function assetCreate() {
        $data = [
            'kode_aset' => $_POST['kode_aset'],
            'nama_aset' => $_POST['nama_aset'],
            'kategori' => $_POST['kategori'],
            'nilai_perolehan' => $_POST['nilai_perolehan'],
            'tanggal_perolehan' => $_POST['tanggal_perolehan'],
            'metode_depresiasi' => $_POST['metode_depresiasi'],
            'umur_ekonomis' => $_POST['umur_ekonomis']
        ];

        $asset = new FixedAsset();
        if ($asset->create($data)) {
            jsonResponse(true, 'Aset tetap berhasil dibuat');
        } else {
            jsonResponse(false, 'Gagal membuat aset tetap');
        }
    }

    public static function assetDetail() {
        $id = $_GET['id'];
        $asset = new FixedAsset();
        $data = $asset->getById($id);
        if ($data) {
            $data['depreciations'] = $asset->getDepreciations($id);
            jsonResponse(true, 'Detail aset tetap', $data);
        } else {
            jsonResponse(false, 'Aset tetap tidak ditemukan');
        }
    }

    public static function assetUpdate() {
        $id = $_POST['id'];
        $data = [
            'kode_aset' => $_POST['kode_aset'],
            'nama_aset' => $_POST['nama_aset'],
            'kategori' => $_POST['kategori'],
            'nilai_perolehan' => $_POST['nilai_perolehan'],
            'tanggal_perolehan' => $_POST['tanggal_perolehan'],
            'metode_depresiasi' => $_POST['metode_depresiasi'],
            'umur_ekonomis' => $_POST['umur_ekonomis'],
            'nilai_buku' => $_POST['nilai_buku'],
            'status' => $_POST['status']
        ];

        $asset = new FixedAsset();
        if ($asset->update($id, $data)) {
            jsonResponse(true, 'Aset tetap berhasil diupdate');
        } else {
            jsonResponse(false, 'Gagal mengupdate aset tetap');
        }
    }

    public static function assetDelete() {
        $id = $_POST['id'];
        $asset = new FixedAsset();
        if ($asset->delete($id)) {
            jsonResponse(true, 'Aset tetap berhasil dihapus');
        } else {
            jsonResponse(false, 'Gagal menghapus aset tetap');
        }
    }

    public static function assetCalculateDepreciation() {
        $id = $_GET['id'];
        $asset = new FixedAsset();
        $details = $asset->calculateDepreciation($id);
        if ($details) {
            jsonResponse(true, 'Perhitungan depresiasi', $details);
        } else {
            jsonResponse(false, 'Gagal menghitung depresiasi');
        }
    }

    public static function assetSaveDepreciation() {
        $id = $_POST['id'];
        $asset = new FixedAsset();
        $details = $asset->calculateDepreciation($id);
        
        if ($details) {
            foreach ($details as $d) {
                $asset->saveDepreciation($id, $d['periode'], $d['nilai_depresiasi'], $d['nilai_buku_setelah']);
            }
            jsonResponse(true, 'Depresiasi berhasil disimpan');
        } else {
            jsonResponse(false, 'Gagal menyimpan depresiasi');
        }
    }
}
?>

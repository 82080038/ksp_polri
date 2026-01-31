<?php
// tests/AccountingTest.php
use PHPUnit\Framework\TestCase;

require_once '../backend/app/models/ChartOfAccounts.php';
require_once '../backend/app/models/JournalEntry.php';
require_once '../backend/app/models/GeneralLedger.php';
require_once '../backend/app/models/FixedAsset.php';

class AccountingTest extends TestCase {
    
    // ==================== CHART OF ACCOUNTS TESTS ====================
    
    public function testCreateCoa() {
        $coa = new ChartOfAccounts();
        $data = [
            'kode_akun' => '1101-' . time(),
            'nama_akun' => 'Kas Test',
            'kategori' => 'ASET',
            'parent_id' => null,
            'saldo_awal' => 1000000
        ];
        $this->assertTrue($coa->create($data));
    }

    public function testGetAllCoa() {
        $coa = new ChartOfAccounts();
        $data = $coa->getAll();
        $this->assertIsArray($data);
    }

    public function testGetCoaByKategori() {
        $coa = new ChartOfAccounts();
        $data = $coa->getByKategori('ASET');
        $this->assertIsArray($data);
    }

    // ==================== JOURNAL ENTRY TESTS ====================

    public function testGenerateNomorJurnal() {
        $jurnal = new JournalEntry();
        $nomor = $jurnal->generateNomorJurnal();
        $this->assertStringStartsWith('JRNL-', $nomor);
    }

    public function testGetAllJurnal() {
        $jurnal = new JournalEntry();
        $data = $jurnal->getAll();
        $this->assertIsArray($data);
    }

    public function testGetJurnalByDateRange() {
        $jurnal = new JournalEntry();
        $data = $jurnal->getByDateRange('2024-01-01', '2024-12-31');
        $this->assertIsArray($data);
    }

    // ==================== GENERAL LEDGER TESTS ====================

    public function testGetNeracaSaldo() {
        $ledger = new GeneralLedger();
        $data = $ledger->getNeracaSaldo();
        $this->assertIsArray($data);
    }

    public function testGetNeracaSaldoWithDateRange() {
        $ledger = new GeneralLedger();
        $data = $ledger->getNeracaSaldo('2024-01-01', '2024-12-31');
        $this->assertIsArray($data);
    }

    public function testGetLabaRugi() {
        $ledger = new GeneralLedger();
        $data = $ledger->getLabaRugi('2024-01-01', '2024-12-31');
        $this->assertIsArray($data);
        $this->assertArrayHasKey('pendapatan', $data);
        $this->assertArrayHasKey('beban', $data);
        $this->assertArrayHasKey('laba_rugi', $data);
    }

    // ==================== FIXED ASSET TESTS ====================

    public function testCreateFixedAsset() {
        $asset = new FixedAsset();
        $data = [
            'kode_aset' => 'AST-' . time(),
            'nama_aset' => 'Komputer Test',
            'kategori' => 'ELEKTRONIK',
            'nilai_perolehan' => 5000000,
            'tanggal_perolehan' => '2024-01-01',
            'metode_depresiasi' => 'STRAIGHT_LINE',
            'umur_ekonomis' => 5
        ];
        $this->assertTrue($asset->create($data));
    }

    public function testGetAllFixedAssets() {
        $asset = new FixedAsset();
        $data = $asset->getAll();
        $this->assertIsArray($data);
    }

    public function testCalculateDepreciation() {
        $asset = new FixedAsset();
        // First create an asset
        $data = [
            'kode_aset' => 'AST-DEP-' . time(),
            'nama_aset' => 'Meja Test',
            'kategori' => 'FURNITURE',
            'nilai_perolehan' => 10000000,
            'tanggal_perolehan' => '2024-01-01',
            'metode_depresiasi' => 'STRAIGHT_LINE',
            'umur_ekonomis' => 5
        ];
        $asset->create($data);
        
        // Get the created asset to get its ID
        $assets = $asset->getAll();
        $lastAsset = end($assets);
        
        if ($lastAsset) {
            $depreciation = $asset->calculateDepreciation($lastAsset['id']);
            $this->assertIsArray($depreciation);
            $this->assertCount(5, $depreciation); // 5 years
        }
    }

    public function testGetDepreciations() {
        $asset = new FixedAsset();
        $assets = $asset->getAll();
        if (!empty($assets)) {
            $depreciations = $asset->getDepreciations($assets[0]['id']);
            $this->assertIsArray($depreciations);
        }
    }
}
?>

<?php
// tests/AnggotaTest.php
use PHPUnit\Framework\TestCase;

require_once '../backend/app/models/Anggota.php';

class AnggotaTest extends TestCase {
    public function testCreateAnggota() {
        $anggota = new Anggota();
        $data = ['nrp' => '12345', 'nama' => 'Test', 'pangkat' => 'Ipda', 'satuan' => 'Polres'];
        $this->assertTrue($anggota->create($data));
    }

    public function testGetAllAnggota() {
        $anggota = new Anggota();
        $data = $anggota->getAll();
        $this->assertIsArray($data);
    }
}
?>

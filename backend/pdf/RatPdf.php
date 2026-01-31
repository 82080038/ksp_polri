<?php
// backend/pdf/RatPdf.php
require_once '../libs/fpdf.php';

class RatPdf extends FPDF {

    function header() {
        $this->SetFont('Arial','B',14);
        $this->Cell(0,10,'LAPORAN RAT KSP POLRI',0,1,'C');
        $this->Ln(5);
    }

    function section($title) {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,8,$title,0,1);
    }

    function row($label, $value) {
        $this->SetFont('Arial','',11);
        $this->Cell(90,8,$label,1);
        $this->Cell(90,8,$value,1,1);
    }
}
?>

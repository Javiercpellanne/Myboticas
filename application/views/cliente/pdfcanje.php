<?php
$codigo=$datos->id.'&'.$datos->femision.'&'.$datos->dni;
$tamaño=array(80,100);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Vale');
$pdf->SetMargins(4,2,4);

$pdf->SetAutoPageBreak(true);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
$pdf->SetFont('helvetica', 'B', 7);
if ($empresa->logo!='') {
	$tblc = '<table cellspacing="0" cellpadding="0" border="0">
	        <tr>
	            <td align="center"><img src="'.$empresa->logo.'" border="0" height="55" /></td>
	        </tr>
	        </table>';
	$pdf->writeHTML($tblc, false, false, false, false, '');
	$pdf->Ln(2);
}

$pdf->MultiCell(0,3,$empresa->nombres,0,'C');
$pdf->Ln(1);

$pdf->SetFont('helvetica','B',20);
$pdf->Cell(0,4,"DESCUENTO S./ ".$datos->importe,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica', '', 9);
// define barcode style
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);

// CODE 128 AUTO
$pdf->write1DBarcode($codigo, 'C128', '', '', '', 18, 0.4, $style, 'N');
$pdf->Cell(0,4,$codigo,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',9);
$pdf->Cell(0,4,'- El descuento aplica sobre la compra realizada.',0,1,'L');
$pdf->Cell(0,4,'- Los puntos no son acumulables.',0,1,'L');
$pdf->Cell(0,4,'- Los puntos son personales e intransferibles.',0,1,'L');
$pdf->Cell(0,4,'- Si cancelas con el vale y el monto de tu compra',0,1,'L');
$pdf->Cell(0,4,'  no supera los S/.'.$datos->importe.' no procedera vuelto.',0,1,'L');
$pdf->Cell(0,3,'.                        ',0,1,'L');

$pdf->Output();

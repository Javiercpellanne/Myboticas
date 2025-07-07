<?php
$altura=100*$nro;
$tamaño=array(80,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Codigo Barra');
$pdf->SetMargins(4,2,4);

$pdf->SetAutoPageBreak(true);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

$pdf->SetFont('helvetica', 'B', 8);
$archivo=$datos->codbarra!='' ? './downloads/codigos/'.$datos->codbarra.'.png' : '' ;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">';
for ($i=0; $i < $nro ; $i++) {
$tblc .= '<tr>
    <td align="center">'.$datos->descripcion.' '.$datos->nlaboratorio.'</td>
  </tr>
  <tr>
    <td align="center"><img src="'.$archivo.'"/></td>
  </tr>
  <tr>
    <td align="center">'.$datos->codbarra.'</td>
  </tr>
  <tr>
    <td height="20"></td>
  </tr>';
}
$tblc .= '</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);


$pdf->Output();

<?php
$tamaño=array(80,250);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(4,1,4);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

$pdf->SetFont('dejavusanscondensed', 'B', 8);
$logo = $nestablecimiento->logot!='' ? $nestablecimiento->logot: $empresa->lticket;
if ($logo!='') {
    $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center"><img src="'.$logo.'" border="0" height="55" /></td>
            </tr>
            </table>';
    $pdf->writeHTML($tblc, false, false, false, false, '');
    $pdf->Ln(2);
}

// $pdf->MultiCell(0,3,$empresa->ncomercial,0,'C');
// $pdf->SetFont('dejavusanscondensed','',5);
$pdf->MultiCell(0,3,$empresa->nombres,0,'C');
$pdf->Ln(1);

$pdf->SetFont('dejavusanscondensed','',9);
$pdf->Cell(0,3,'RUC '.$empresa->ruc,0,1,'C');
$pdf->MultiCell(0,3,$nestablecimiento->direccion,0,'C');
$pdf->MultiCell(0,3,$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito,0,'C');
$pdf->Cell(0,3,'TELF '.$nestablecimiento->telefono,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',11);
$pdf->Cell(0,4,'BOLETA DE PAGO',0,1,'C');
$pdf->Cell(0,4,'BP-'.zerofill($datos->id, 8),0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('dejavusanscondensed','',8.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="40%"><b>Fecha de Pago :</b></td>
        <td width="60%">'.$datos->femision.'</td>
    </tr>
    <tr>
        <td width="30%"><b>Cliente :</b></td>
        <td width="70%">'.$ventas->cliente.'</td>
    </tr>
    <tr>
        <td width="40%"><b>Importe Pagado :</b></td>
        <td width="60%" align="right"><b style="font-size: 1.3 em;">S/. '.$datos->total.'</b></td>
    </tr>
    <tr>
        <td colspan="2">Recibi de la cantidad de :</td>
    </tr>
    <tr>
        <td colspan="2">'.MontoMonetarioEnLetras($datos->total).'</td>
    </tr>
    <tr>
        <td colspan="2">Por concepto de cobro de venta de mercaderia</td>
    </tr>
    <tr>
        <td width="60%"><b>Documento de referencia : </b></td>
        <td width="40%">'.$ventas->serie.' - '.$ventas->numero.'</td>
    </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','',8.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="center">Deuda total</td>
        <td align="center">Pagado</td>
        <td align="center">Saldo a pagar</td>
    </tr>
    <tr>
        <td align="center"><b>S/. '.$ventas->total.'</b></td>
        <td align="center"><b>S/. '.$datos->pago.'</b></td>
        <td align="center"><b>S/. '.$datos->saldo.'</b></td>
    </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, true, false, false, false, '');

$pdf->Output();

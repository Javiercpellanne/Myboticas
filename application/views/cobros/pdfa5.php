<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(10,10,10);

$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L","A5");

// contenido
$pdf->SetFont('dejavusanscondensed', '',8.5);
$logo = $nestablecimiento->logoe!='' ? $nestablecimiento->logoe: $empresa->logo;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="25%" align="center"><img src="'.$logo.'" border="0" height="70"/></td>
            <td width="45%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
            <td width="30%" align="center" style="border: 1px solid #000;"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.2 em;">BOLETA DE PAGO <br>BP-'.zerofill($datos->id, 8).'</b></td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(5);

$tblf = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="25%"><b>Fecha de Pago :</b></td>
        <td width="75%">'.$datos->femision.'</td>
    </tr>
    <tr>
        <td width="25%"><b>Cliente :</b></td>
        <td width="75%">'.$ventas->cliente.'</td>
    </tr>
    <tr>
        <td width="25%"><b>Importe Pagado :</b></td>
        <td width="75%"><b style="font-size: 1.3 em;">S/. '.$datos->total.'</b></td>
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
        <td width="25%"><b>Documento de referencia : </b></td>
        <td width="75%">'.$ventas->serie.' - '.$ventas->numero.'</td>
    </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','',8);
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

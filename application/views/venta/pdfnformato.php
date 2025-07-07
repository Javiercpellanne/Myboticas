<?php
if ($datos->tpago==1) { $tpago='Contado';} else {$tpago='Credito';}

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(13,18,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('dejavusanscondensed', '',9.5);
$logo = $nestablecimiento->logoe!='' ? $nestablecimiento->logoe: $empresa->logo;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="25%" align="center"><img src="'.$logo.'" border="0" height="70"/></td>
            <td width="45%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
            <td width="30%" align="center" style="border: 1px solid #000;"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.2 em;">'.$datos->ncomprobante.' ELECTRÓNICA'.'<br>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</b></td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());

$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="15%"><strong>Señor(es) : </strong></td>
            <td width="45%">'.$cliente->nombres.'</td>
            <td width="25%"><strong>Fecha Emision: </strong></td>
            <td width="15%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>'.$cliente->descripcion.' :</strong></td>
            <td>'.$cliente->documento.'</td>
            <td><strong></strong></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Direccion :</strong></td>
            <td>'.$cliente->direccion.$direccion.'</td>
            <td><strong></strong></td>
            <td></td>
        </tr>
        <tr>
            <td><strong>Tipo Nota :</strong></td>
            <td>'.$tiponota->descripcion.'</td>
            <td><strong>Doc. Afectado : </strong></td>
            <td>'.$docafectado->serie.'-'.zerofill($docafectado->numero, 8).'</td>
        </tr>
        <tr>
            <td><strong>Motivo :</strong></td>
            <td>'.$datos->motivo.'</td>
            <td><strong>Usuario :</strong></td>
            <td>'.($usuario->nombres??'').'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>CANT</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>UNIDAD</strong></th>
            <th align="center" width="60%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></th>
            <th align="center" width="12%" style="border: 1px solid #000;"><strong>P.UNIT</strong></th>
            <th align="center" width="12%" style="border: 1px solid #000;"><strong>TOTAL</strong></th>
        </tr>';
	$pdf->SetFont('dejavusanscondensed','',9);
	foreach ($detalles as $detalle) {
        $lotes='';
	   if ($detalle->lote!='') {
		$lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
	}
    $tbl .= '<tr>
            <td>'.$detalle->cantidad.'</td>
            <td>'.$detalle->unidad.'</td>
            <td>'.nl2br($detalle->descripcion).$ddscto.$lotes.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="right">'.$detalle->importe.'</td>
            </tr>';
	}
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
	if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP GRAVADAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tgravado.'</td>
        </tr>';
    }

    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP GRATUITAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

    if ($datos->tinafecto>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP INAFECTAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tinafecto.'</td>
        </tr>';
    }

    if ($datos->texonerado>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP EXONERADAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->texonerado.'</td>
        </tr>';
    }
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>IGV : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tigv.'</td>
        </tr>';
	$tblt .= '<tr>
        <td width="88%" align="right"><strong>TOTAL A PAGAR : S/.</strong></td>
        <td width="12%" align="right">'.$datos->total.'</td>
        </tr>';
$tblt .= '</table>';

$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',8);
$pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
$pdf->Ln(2);

// $pdf->SetFont('dejavusanscondensed','B',8);
// $pdf->Cell(0,4,'Numeros de Cuenta a Nombre de : '.$empresa->nombres,0,1,'L');
// $pdf->Cell(45,4,'BCP Cta Corriente',0,0,'L');
// $pdf->Cell(45,4,'BCP CCI',0,1,'L');
// $pdf->SetFont('dejavusanscondensed','',8);
// $pdf->Cell(45,4,'2152631067050',0,0,'L');
// $pdf->Cell(45,4,'00221500263106705025',0,1,'L');
// $pdf->Ln(2);

$qrcode = $empresa->ruc."|".$datos->tcomprobante."|".$datos->serie."|".$datos->numero."|".$datos->tigv."|".$datos->total."|".$datos->femision."|".$cliente->tdocumento."|".$cliente->documento."|".$datos->hash;

$style = array(
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
// QRCODE,Q : QR-CODE Better error correction
$pdf->write2DBarcode($qrcode, 'QRCODE,Q', $pdf->GetX()+75,$pdf->GetY(), 35, 35, $style, 'N');

if ($datos->hash!='') {
    $pdf->SetFont('dejavusanscondensed','',7);
    $pdf->Cell(0,3,'Codigo Hash : '.$datos->hash,0,1,'C');
    $pdf->Ln(2);
}

//$pdf->Output();
$archivo='./downloads/pdf/'.$datos->filename.'.pdf';
//$pdf->Output($archivo,'F');

$pdf_string = $pdf->Output('pseudo.pdf', 'S');
file_put_contents($archivo, $pdf_string);

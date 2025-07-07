<?php
$altura=$detalles!=null ? 200+(count($detalles)*5) : 200;
$tamaño=array(80,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(4,2,4);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
$pdf->SetFont('dejavusanscondensed', 'B', 7);
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

$pdf->SetFont('dejavusanscondensed','',7);
$pdf->Cell(0,3,'RUC '.$empresa->ruc,0,1,'C');
$pdf->MultiCell(0,3,$nestablecimiento->direccion,0,'C');
$pdf->Cell(0,3,$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito,0,1,'C');
$pdf->Cell(0,3,'TELF '.$nestablecimiento->telefono,0,1,'C');
$pdf->Ln(2);

$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','B',11);
$pdf->Cell(0,4,$datos->ncomprobante.' ELECTRÓNICA',0,1,'C');
$pdf->Cell(0,4,$datos->serie.'-'.zerofill($datos->numero, 8),0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','',7);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="35%"><strong>Fecha :</strong></td>
            <td width="65%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>Cliente :</strong></td>
            <td>'.$cliente->nombres.'</td>
        </tr>
        <tr>
            <td><strong>'.$cliente->descripcion.' :</strong></td>
            <td>'.$cliente->documento.'</td>
        </tr>
        <tr>
            <td><strong>Direccion :</strong></td>
            <td>'.$cliente->direccion.$direccion.'</td>
        </tr>
        <tr>
            <td><strong>Doc. Afectado :</strong></td>
            <td>'.$docafectado->serie.'-'.zerofill($docafectado->numero, 8).'</td>
        </tr>
        <tr>
            <td><strong>Tipo Nota :</strong></td>
            <td>'.$tiponota->descripcion.'</td>
        </tr>
        <tr>
            <td><strong>Motivo :</strong></td>
            <td>'.$datos->motivo.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td align="center" width="55%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></td>
            <td align="center" width="13%" style="border: 1px solid #000;"><strong>CANT</strong></td>
            <td align="center" width="15%" style="border: 1px solid #000;"><strong>P.UNIT</strong></td>
            <td align="center" width="17%" style="border: 1px solid #000;"><strong>IMPORTE</strong></td>
        </tr>';
	$gdscto=0;
	$pdf->SetFont('dejavusanscondensed','',6.5);
	foreach ($detalles as $detalle) {
    $tbl .= '<tr>
            <td colspan="4">'.nl2br($detalle->descripcion).'</td>
            </tr>';
    $tbl .= '<tr>
            <td></td>
            <td>'.$detalle->cantidad.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="right">'.$detalle->importe.'</td>
            </tr>';
	}
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(4, $pdf->getY(), $pdf->getPageWidth()-4, $pdf->getY());

$pdf->SetFont('dejavusanscondensed','',7);
$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
	if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP GRAVADAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tgravado.'</td>
        </tr>';
    }

    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP GRATUITAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

    if ($datos->tinafecto>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP INAFECTAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tinafecto.'</td>
        </tr>';
    }

    if ($datos->texonerado>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP EXONERADAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->texonerado.'</td>
        </tr>';
    }
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>IGV 18% : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tigv.'</td>
        </tr>';
	$tblt .= '<tr>
        <td width="83%" align="right"><strong>IMPORTE PAGAR : S/.</strong></td>
        <td width="17%" align="right">'.$datos->total.'</td>
        </tr>';
$tblt .= '</table>';

$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',7);
$pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
$pdf->Ln(2);

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
$pdf->write2DBarcode($qrcode, 'QRCODE,Q', $pdf->GetX()+20,$pdf->GetY(), 30, 30, $style, 'N');

if ($datos->hash!='') {
    $pdf->SetFont('dejavusanscondensed','',6);
    $pdf->Cell(0,3,'Codigo Hash : '.$datos->hash,0,1,'C');
    $pdf->Ln(2);
}

$pdf->SetFont('dejavusanscondensed','B',6);
$pdf->Cell(15,3.5,'Usuario : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',6);
$pdf->Cell(0,3.5,($usuario->nombres??''),0,1,'L');

//$pdf->Output();
$archivo='./downloads/pdf/'.$datos->filename.'.pdf';
//$pdf->Output($archivo,'F');

$pdf_string = $pdf->Output('pseudo.pdf', 'S');
file_put_contents($archivo, $pdf_string);

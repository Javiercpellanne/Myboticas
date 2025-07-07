<?php
$altura=$detalles!=null ? 250+(count($detalles)*5) : 250;
$tamaño=array(58,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(3,2,3);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
$pdf->SetFont('dejavusanscondensed', 'B', 6.5);
$logo = $nestablecimiento->logot!='' ? $nestablecimiento->logot: $empresa->lticket;
if ($logo!='') {
	$tblc = '<table cellspacing="0" cellpadding="0" border="0">
	        <tr>
	            <td align="center"><img src="'.$logo.'" border="0" height="45" /></td>
	        </tr>
	        </table>';
	$pdf->writeHTML($tblc, false, false, false, false, '');
	$pdf->Ln(2);
}

// $pdf->MultiCell(0,3,$empresa->ncomercial,0,'C');
// $pdf->SetFont('dejavusanscondensed','',5);
$pdf->MultiCell(0,3,$empresa->nombres,0,'C');
$pdf->Ln(1);

$pdf->SetFont('dejavusanscondensed','',6.5);
$pdf->Cell(0,3,'RUC '.$empresa->ruc,0,1,'C');
$pdf->MultiCell(0,3,$nestablecimiento->direccion,0,'C');
$pdf->MultiCell(0,3,$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito,0,'C');
$pdf->Cell(0,3,'TELF '.$nestablecimiento->telefono,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(0,4,'NOTA DE VENTA',0,1,'C');
$pdf->Cell(0,4,$datos->serie.'-'.zerofill($datos->numero, 8),0,1,'C');
$pdf->Ln(2);

$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$pdf->SetFont('dejavusanscondensed','',6.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="30%"><strong>Fecha :</strong></td>
            <td width="70%">'.$datos->femision.' '.$datos->hemision.'</td>
        </tr>
        <tr>
            <td><strong>Cliente :</strong></td>
            <td>'.$datos->cliente.$ncomercial.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',6.5);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <td align="center" width="55%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></td>
        <td align="center" width="13%" style="border: 1px solid #000;"><strong>CANT</strong></td>
        <td align="center" width="15%" style="border: 1px solid #000;"><strong>P.UNIT</strong></td>
        <td align="center" width="17%" style="border: 1px solid #000;"><strong>IMP.</strong></td>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',5.5);
$gdscto=0;
foreach ($detalles as $detalle) {
    $lotes='';
    if ($detalle->lote!='' && $datos->lote==1) {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }

    if ($detalle->dscto!='') {
        $gdscto+=floatval($detalle->dscto);
        $msubtotal=$detalle->cantidad*$detalle->precio;
        $factor=round(($detalle->dscto*100)/($msubtotal*100),4);
        $ddscto= ' ('.($factor*100).' % Descuento)';
    } else {
        $ddscto='';
    }
    $tbl .= '<tr>
        <td colspan="4">'.$detalle->descripcion.$ddscto.$lotes.'</td>
    </tr>';
    $tbl .= '<tr>
        <td align="right">'.$detalle->unidad.'</td>
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
$pdf->Line(3, $pdf->getY(), $pdf->getPageWidth()-3, $pdf->getY());

$pdf->SetFont('dejavusanscondensed','',6.5);
$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
if ($datos->dscto>0) {
$tblt .= '<tr>
    <td width="83%" align="right"><strong>DESCUENTO : S/.</strong></td>
    <td width="17%" align="right">'.formatoPrecio($datos->dscto).'</td>
    </tr>';
}
$tblt .= '<tr>
    <td width="83%" align="right"><strong>TOTAL : S/.</strong></td>
    <td width="17%" align="right">'.$datos->total.'</td>
    </tr>';
if ($datos->izipay!=null) {
    $pagar=$datos->total+$datos->izipay;
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>IZIPAY : S/.</strong></td>
        <td width="17%" align="right">'.formatoPrecio($datos->izipay).'</td>
        </tr>';
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>A PAGAR : S/.</strong></td>
        <td width="17%" align="right">'.formatoPrecio($pagar).'</td>
        </tr>';
}
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',6.5);
$pdf->Cell(0,2,'Descripcion Adicional : ',0,1,'L');
$pdf->SetFont('dejavusanscondensed','',6.5);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

if ($datos->izipay!=null) {
    $pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($pagar),0,'L');
}else{
    $pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
}
$pdf->Ln(2);

if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}
$pdf->SetFont('dejavusanscondensed','B',6);
$pdf->Cell(0,3,'T. Pago: '.$tpago,0,1,'C');
$pdf->Ln(2);

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(0,3,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',6);
    foreach ($cobros as $cobro) {
        $pdf->Cell(0,3,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(0,3,'Cuota / Fecha / Monto',0,1,'L');
    $pdf->SetFont('dejavusanscondensed','',6);
    $feinicial=$datos->fpago;
    for ($i=1; $i <= $datos->cuotas ; $i++) {
      if ($i == $datos->cuotas) {
          $cuota = $datos->total - ($datos->mcuota * ($datos->cuotas - 1));;
      } else {
          $cuota = $datos->mcuota;
      }
      $pdf->Cell(0,3,'• # '.$i.' / '.$feinicial.' / S/ '.$cuota,0,1,'L');
      $suma=tiempoCuota($datos->pcuota);
      $feinicial=SumarFecha($suma,$feinicial);
    }
}
$pdf->Ln(2);


$pdf->SetFont('dejavusanscondensed','B',6);
$pdf->Cell(15,3,'Usuario : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',6);
$pdf->Cell(0,3,($usuario->nombres??''),0,1,'L');
$pdf->Ln(2);

if ($datos->efectivo!=null) {
$pdf->SetFont('dejavusanscondensed','B',6);
$pdf->Cell(18,3,'Efectivo Soles ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',6);
$pdf->Cell(15,3,formatoPrecio($datos->efectivo),0,1,'L');
$pdf->SetFont('dejavusanscondensed','B',6);
$pdf->Cell(18,3,'Vuelto : S/ ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',6);
$pdf->Cell(15,3,formatoPrecio($datos->vuelto),0,1,'L');
$pdf->Ln(2);
}

if ($tpuntos->cantidad!=null) {
    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(30,3.5,'Puntos Acumulados : ',0,0,'L');
    $pdf->SetFont('dejavusanscondensed','',6);
    $pdf->Cell(0,3.5,$tpuntos->cantidad,0,1,'L');
    $pdf->Ln(2);
}

$pdf->SetFont('dejavusanscondensed','',6);
$pdf->writeHTML(nl2br($empresa->pie), true, false, true, false, '');

$pdf->Output();

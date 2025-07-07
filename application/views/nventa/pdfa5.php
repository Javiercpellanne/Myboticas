<?php
if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(10,10,10);

$pdf->SetAutoPageBreak(true,9.5);
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
            <td width="22%" align="center"><img src="'.$logo.'" border="0" width="140" /></td>
            <td width="53%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
            <td width="25%" align="center" style="border: 1px solid #000;"><br><br>'.'RUC Nº '.$empresa->ruc.'<br>NOTA DE VENTA'.'<br>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());

$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$pdf->SetFont('dejavusanscondensed','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="15%"><strong>Señor(es) : </strong></td>
            <td width="45%">'.$datos->cliente.$ncomercial.'</td>
            <td width="25%"><strong>Fecha Emision: </strong></td>
            <td width="15%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>Vendedor :</strong></td>
            <td>'.($vendedor->nombres??'').'</td>
            <td><strong>Condiciones Pago :</strong></td>
            <td>'.$tpago.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',6);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th align="center" width="8%" style="border: 1px solid #000;"><strong>CANT</strong></th>
        <th align="center" width="8%" style="border: 1px solid #000;"><strong>UNIDAD</strong></th>
        <th align="center" width="60%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><strong>P.UNIT</strong></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><strong>IMPORTE</strong></th>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',7.5);
foreach ($detalles as $detalle) {
    $lotes='';
    if ($detalle->lote!='' && $datos->lote==1) {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    $tbl .= '<tr>
        <td>'.$detalle->cantidad.'</td>
        <td>'.$detalle->unidad.'</td>
        <td>'.$detalle->descripcion.$lotes.'</td>
        <td align="right">'.$detalle->precio.'</td>
        <td align="right">'.$detalle->importe.'</td>
    </tr>';
}
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());

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
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',7);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(0,3,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',6);
    foreach ($cobros as $cobro) {
        $pdf->Cell(0,3,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','',6);
    $feinicial=$datos->fpago;
    for ($i=1; $i <= $datos->cuotas ; $i++) {
      if ($i == $datos->cuotas) {
          $cuota = $datos->total - ($datos->mcuota * ($datos->cuotas - 1));;
      } else {
          $cuota = $datos->mcuota;
      }
      $pdf->Cell(0,3,'• Cuota '.zerofill($i,3).' /  Fecha: '.$feinicial.' / Monto: S/ '.$cuota,0,1,'L');
      $suma=tiempoCuota($datos->pcuota);
      $feinicial=SumarFecha($suma,$feinicial);
    }
}
$pdf->Ln(2);

$pdf->Output();

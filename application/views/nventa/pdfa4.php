<?php
if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(13,15,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();
// contenido
$pdf->SetLineStyle(array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(1, 1, 1)));
// Establecer el color de relleno a blanco
$pdf->SetFillColor(255, 255, 255);
// Dibujar un rectángulo con bordes redondeados
$pdf->RoundedRect(133.3, 15.2, 63, 25.5, 4, '1234', 'DF');
$pdf->SetFont('dejavusanscondensed', '',11);
$logo = $nestablecimiento->logoe!='' ? $nestablecimiento->logoe: $empresa->logo;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="30%" align="center"><img src="'.$logo.'" border="0" height="90"/></td>
        <td width="35%" align="center"><span style="font-size: 0.8 em;">'.$empresa->nombres.'</span><br><span style="font-size: 0.6 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
        <td width="35%" align="center"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.5 em;">NOTA DE VENTA</b><br><b>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</b><br></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','',8.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="15%" style="border-top: 1px solid #000; border-left: 1px solid #000;"><b>Señor(es) : </b></td>
        <td width="50%" style="border-top: 1px solid #000;">'.$datos->cliente.$ncomercial.'</td>
        <td width="20%" style="border-top: 1px solid #000;"><b>Fecha Emision: </b></td>
        <td width="15%" style="border-top: 1px solid #000; border-right: 1px solid #000;">'.$datos->femision.'</td>
    </tr>
    <tr>
        <td style="border-left: 1px solid #000;"><b>Direccion :</b></td>
        <td>'.$cliente->direccion.$direccion.'</td>
        <td><b>Telefono :</b></td>
        <td style="border-right: 1px solid #000;">'.$cliente->telefono.'</td>
    </tr>
    <tr>
        <td style="border-bottom: 1px solid #000; border-left: 1px solid #000;"><b>Vendedor :</b></td>
        <td style="border-bottom: 1px solid #000;">'.($vendedor->nombres??'').'</td>
        <td style="border-bottom: 1px solid #000;"><b>Condiciones Pago :</b></td>
        <td style="border-bottom: 1px solid #000; border-right: 1px solid #000;">'.$tpago.'</td>
    </tr>
</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th align="center" width="8%" style="border: 1px solid #000;"><b>CANT</b></th>
        <th align="center" width="8%" style="border: 1px solid #000;"><b>UNIDAD</b></th>
        <th align="center" width="60%" style="border: 1px solid #000;"><b>DESCRIPCION</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>P.UNIT</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>IMPORTE</b></th>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',8.5);
foreach ($detalles as $detalle) {
    $lotes='';
    if ($detalle->lote!='' && $datos->lote==1) {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    $tbl .= '<tr>
        <td style="border-left: 1px solid #000;">'.$detalle->cantidad.'</td>
        <td style="border-left: 1px solid #000;" align="center">'.$detalle->unidad.'</td>
        <td style="border-left: 1px solid #000;">'.$detalle->descripcion.$lotes.'</td>
        <td style="border-left: 1px solid #000;" align="right">'.$detalle->precio.'</td>
        <td style="border-left: 1px solid #000; border-right: 1px solid #000;" align="right">'.$detalle->importe.'</td>
    </tr>';
}
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
if ($datos->dscto>0) {
$tblt .= '<tr>
    <td width="83%" align="right"><b>DESCUENTO : S/.</b></td>
    <td width="17%" align="right">'.formatoPrecio($datos->dscto).'</td>
    </tr>';
}
$tblt .= '<tr>
    <td width="83%" align="right"><b>TOTAL : S/.</b></td>
    <td width="17%" align="right">'.$datos->total.'</td>
    </tr>';
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',8);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',8);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',7);
    $pdf->Cell(0,3,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',7);
    foreach ($cobros as $cobro) {
        $pdf->Cell(0,3,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','',7);
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

<?php
$altura=$detalles!=null ? 200+(count($detalles)*5) : 200;
$tamaño=array(80,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Cotizacion de Venta');
$pdf->SetMargins(4,2,4);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
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

$pdf->SetFont('dejavusanscondensed','',8);
$pdf->Cell(0,3,'RUC '.$empresa->ruc,0,1,'C');
$pdf->MultiCell(0,3,$nestablecimiento->direccion,0,'C');
$pdf->Cell(0,3,$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito,0,1,'C');
$pdf->Cell(0,3,'TELF '.$nestablecimiento->telefono,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',9);
$pdf->Cell(0,4,'COTIZACION',0,1,'C');
$pdf->Cell(0,4,'COT-'.zerofill($id,8),0,1,'C');
$pdf->Ln(2);

if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}
$pdf->SetFont('dejavusanscondensed','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="30%"><strong>Fecha :</strong></td>
            <td width="70%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>Cliente :</strong></td>
            <td>'.$datos->cliente.'</td>
        </tr>
        <tr>
            <td><strong>'.$cliente->descripcion.' :</strong></td>
            <td>'.$cliente->documento.'</td>
        </tr>
        <tr>
            <td><strong>Direccion :</strong></td>
            <td>'.$cliente->direccion.'</td>
        </tr>
        <tr>
            <td width="40%"><strong>Tiempo de Validez :</strong></td>
            <td width="60%">'.$datos->tvalidez.'</td>
        </tr>
        <tr>
            <td><strong>Tiempo de Entrega :</strong></td>
            <td>'.$datos->tentrega.'</td>
        </tr>
        <tr>
            <td><strong>Condicion Pago :</strong></td>
            <td>'.$tpago.'</td>
        </tr>
        <tr>
            <td><strong>Usuario :</strong></td>
            <td>'.$usuario->nombres.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',8);
$pdf->Cell(23,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',8);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',8);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <td align="center" width="55%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></td>
        <td align="center" width="13%" style="border: 1px solid #000;"><strong>CANT</strong></td>
        <td align="center" width="15%" style="border: 1px solid #000;"><strong>P.UNIT</strong></td>
        <td align="center" width="17%" style="border: 1px solid #000;"><strong>IMP.</strong></td>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',7);
foreach ($detalles as $detalle) {
    $fdscto='';
    if ($detalle->descuentos!='') {
        $descuentos=json_decode($detalle->descuentos);
        $fdscto= ' ('.($descuentos->factor*100).' % Descuento)';
    }
    $tbl .= '<tr>
        <td colspan="4">'.$detalle->descripcion.$fdscto.'</td>
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

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
$tblt .= '<tr>
    <td width="83%" align="right"><strong>TOTAL : S/.</strong></td>
    <td width="17%" align="right">'.$datos->total.'</td>
    </tr>';
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->Output();

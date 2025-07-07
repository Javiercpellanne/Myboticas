<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Cotizacion de Venta');
$pdf->SetMargins(10,10,10);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L","A5");

// contenido
$pdf->SetFont('dejavusanscondensed', '',8.5);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr><td width="22%" align="center"><img src="'.$empresa->logo.'" border="0" height="70"/></td>
            <td width="48%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF : '.$nestablecimiento->telefono.'<br>'.'EMAIL : '.$nestablecimiento->email.'</span></td>
            <td width="30%" align="center" style="border: 1px solid #000;"><br><br><br> COTIZACION  <br> COT-'.zerofill($id,8).'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());
$pdf->Ln(1);

if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}
$pdf->SetFont('dejavusanscondensed','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="15%"><strong>Cliente: </strong></td>
            <td width="45%">'.$datos->cliente.'</td>
            <td width="25%"><strong>Fecha Emision: </strong></td>
            <td width="15%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>'.$cliente->descripcion.' :</strong></td>
            <td>'.$cliente->documento.'</td>
            <td><strong>Tiempo de Validez :</strong></td>
            <td>'.$datos->tvalidez.'</td>
        </tr>
        <tr>
            <td><strong>Direccion :</strong></td>
            <td>'.$cliente->direccion.'</td>
            <td><strong>Tiempo de Entrega :</strong></td>
            <td>'.$datos->tentrega.'</td>
        </tr>
        <tr>
            <td><strong>Usuario :</strong></td>
            <td>'.$usuario->nombres.'</td>
            <td><strong>Condicion Pago :</strong></td>
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
        <th align="center" width="52%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><strong>P.UNIT</strong></th>
        <th align="center" width="8%" style="border: 1px solid #000;"><strong>DSCTO</strong></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><strong>TOTAL</strong></th>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',7.5);
foreach ($detalles as $detalle) {
    $fdscto=''; $mdscto='';
    if ($detalle->descuentos!='') {
        $descuentos=json_decode($detalle->descuentos);
        $mdscto=$descuentos->monto;
        $fdscto= ' ('.($descuentos->factor*100).' % Descuento)';
    }
    $tbl .= '<tr>
        <td>'.$detalle->cantidad.'</td>
        <td>'.$detalle->unidad.'</td>
        <td>'.$detalle->descripcion.$ddscto.'</td>
        <td align="right">'.$detalle->precio.'</td>
        <td align="center">'.$mdscto.'</td>
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
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>TOTAL : S/.</strong></td>
        <td width="12%" align="right">'.$datos->total.'</td>
        </tr>';
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7.5);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

// $pdf->SetFont('dejavusanscondensed','B',8);
// $pdf->Cell(0,4,'Numeros de Cuenta a Nombre de : '.$empresa->nombres,0,1,'L');
// $pdf->Cell(45,4,'BCP Cta Corriente',0,0,'L');
// $pdf->Cell(45,4,'BCP CCI',0,1,'L');
// $pdf->SetFont('dejavusanscondensed','',8);
// $pdf->Cell(45,4,'2152631067050',0,0,'L');
// $pdf->Cell(45,4,'00221500263106705025',0,1,'L');
// $pdf->Ln(2);

$pdf->Output();

<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Cotizacion de Venta');
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
        <td width="35%" align="center"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.5 em;">COTIZACION</b><br><b>COT-'.zerofill($id,8).'</b><br></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}
$pdf->SetFont('dejavusanscondensed','',8.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="15%" style="border-top: 1px solid #000; border-left: 1px solid #000;"><b>Cliente: </b></td>
            <td width="45%" style="border-top: 1px solid #000;">'.$datos->cliente.'</td>
            <td width="25%" style="border-top: 1px solid #000;"><b>Fecha Emision: </b></td>
            <td width="15%" style="border-top: 1px solid #000; border-right: 1px solid #000;">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000;"><b>'.$cliente->descripcion.' :</b></td>
            <td>'.$cliente->documento.'</td>
            <td><b>Tiempo de Validez :</b></td>
            <td style="border-right: 1px solid #000;">'.$datos->tvalidez.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000;"><b>Direccion :</b></td>
            <td>'.$cliente->direccion.'</td>
            <td><b>Tiempo de Entrega :</b></td>
            <td style="border-right: 1px solid #000;">'.$datos->tentrega.'</td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #000; border-left: 1px solid #000;"><b>Usuario :</b></td>
            <td style="border-bottom: 1px solid #000;">'.$usuario->nombres.'</td>
            <td style="border-bottom: 1px solid #000;"><b>Condicion Pago :</b></td>
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
        <th align="center" width="52%" style="border: 1px solid #000;"><b>DESCRIPCION</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>P.UNIT</b></th>
        <th align="center" width="8%" style="border: 1px solid #000;"><b>DSCTO</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>TOTAL</b></th>
    </tr>';
$pdf->SetFont('dejavusanscondensed','',8.5);
foreach ($detalles as $detalle) {
    $fdscto=''; $mdscto='';
    if ($detalle->descuentos!='') {
        $descuentos=json_decode($detalle->descuentos);
        $mdscto=$descuentos->monto;
        $fdscto= ' ('.($descuentos->factor*100).' % Descuento)';
    }
    $tbl .= '<tr>
        <td style="border-left: 1px solid #000;">'.$detalle->cantidad.'</td>
        <td style="border-left: 1px solid #000;" align="center">'.$detalle->unidad.'</td>
        <td style="border-left: 1px solid #000;">'.$detalle->descripcion.$fdscto.'</td>
        <td style="border-left: 1px solid #000;" align="right">'.$detalle->precio.'</td>
        <td style="border-left: 1px solid #000;" align="center">'.$mdscto.'</td>
        <td style="border-left: 1px solid #000; border-right: 1px solid #000;" align="right">'.$detalle->importe.'</td>
    </tr>';
}
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
    $tblt .= '<tr>
        <td width="88%" align="right"><b>TOTAL : S/.</b></td>
        <td width="12%" align="right">'.$datos->total.'</td>
        </tr>';
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->SetFont('dejavusanscondensed','B',9);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',9);
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

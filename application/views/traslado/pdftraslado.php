<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Traslado Interno');
$pdf->SetMargins(11,10,11);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('helvetica', 'B',13);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="15%" align="center"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
            <td width="70%" align="center"><br><br> TRASLADO INTERNO</td>
            <td width="15%" align="center"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
        </tr>
    </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());
$pdf->Ln(1);

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(20,5,'Origen : ',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(115,5,$origen->descripcion ?? 'Almacen Principal',0,0,'L');
$pdf->SetFont('helvetica','B',9);
$pdf->Cell(30,5,'Fecha Emision : ',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(25,5,$datos->femision,0,1,'L');

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(20,5,'Destino : ',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(115,5,$destino->descripcion,0,'L');
$pdf->SetFont('helvetica','B',9);
$pdf->Cell(30,5,'Fecha Recepcion : ',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(25,5,$datos->frecepcion,0,1,'L');

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(20,5,'Generador : ',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(95,5,$nombre->nombres,0,1,'L');
$pdf->Ln(1);

$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="49%"><strong>Descripcion</strong></th>
            <th align="center" width="10%"><strong>Lote</strong></th>
            <th align="center" width="10%"><strong>Vcto</strong></th>
            <th align="center" width="10%"><strong>Cantidad</strong></th>
            <th align="center" width="8%"><strong>Precio</strong></th>
            <th align="center" width="8%"><strong>Importe</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    foreach ($detalles as $detalle) {
        $tbl .= '<tr>
            <td>'.$i.'</td>
            <td>'.$detalle->descripcion.'</td>
            <td>'.$detalle->lote.'</td>
            <td>'.$detalle->fvencimiento.'</td>
            <td align="right">'.$detalle->cantidad.' '.$detalle->unidad.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="right">'.$detalle->importe.'</td>
            </tr>';
        $i++;
    }

    $tbl .= '<tr>
            <td align="right" colspan="6"><strong>Total</strong></td>
            <td align="right">'.$datos->importe.'</td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

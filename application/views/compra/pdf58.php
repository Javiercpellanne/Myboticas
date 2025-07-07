<?php
$altura=$detalles!=null ? 250+(count($detalles)*5) : 250;
$tamaño=array(58,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Compra');
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
                <td align="center"><img src="'.$logo.'" border="0" height="55" /></td>
            </tr>
            </table>';
    $pdf->writeHTML($tblc, false, false, false, false, '');
    $pdf->Ln(2);
}

$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(0,4,'Compra  # '.$id,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',6.5);
$pdf->MultiCell(0,3,$nestablecimiento->descripcion,0,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',6.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="30%"><strong>Fecha: </strong></td>
            <td width="70%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><strong>Documento: </strong></td>
            <td>'.$datos->serie.'-'.$datos->numero.'</td>
        </tr>
        <tr>
            <td><strong>Proveedor : </strong></td>
            <td>'.$datos->proveedor.'</td>
        </tr>
        <tr>
            <td><strong>Direccion :</strong></td>
            <td>'.$proveedor->direccion.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',6.5);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <td align="center" width="55%" style="border: 1px solid #000;"><strong>DESCRIPCION</strong></td>
            <td align="center" width="13%" style="border: 1px solid #000;"><strong>CANT</strong></td>
            <td align="center" width="15%" style="border: 1px solid #000;"><strong>P.UNIT</strong></td>
            <td align="center" width="17%" style="border: 1px solid #000;"><strong>IMP.</strong></td>
        </tr>';
    $pdf->SetFont('helvetica','',5.5);
    foreach ($detalles as $detalle) {
    $lotes='';
    if ($detalle->lote!='') {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    $tbl .= '<tr>
        <td colspan="4">'.$detalle->descripcion.$lotes.'</td>
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
$pdf->Line(4, $pdf->getY(), $pdf->getPageWidth()-4, $pdf->getY());

$pdf->SetFont('helvetica','B',6.5);
$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP GRATUITAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

    if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>OP GRAVADAS : S/.</strong></td>
        <td width="17%" align="right">'.$datos->tgravado.'</td>
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
        <td width="83%" align="right"><strong>IGV (18%) : S/.</strong></td>
        <td width="17%" align="right">'.$datos->igv.'</td>
    </tr>';
    $tblt .= '<tr>
        <td width="83%" align="right"><strong>IMPORTE : S/.</strong></td>
        <td width="17%" align="right">'.$datos->total.'</td>
    </tr>';
    if ($datos->percepcion>0) {
        $tblt .= '<tr>
            <td width="83%" align="right"><strong>PERCEPCION : S/.</strong></td>
            <td width="17%" align="right">'.$datos->percepcion.'</td>
        </tr>';
        $tblt .= '<tr>
            <td width="83%" align="right"><strong>PAGAR : S/.</strong></td>
            <td width="17%" align="right">'.($datos->total+$datos->percepcion).'</td>
        </tr>';
    }
$tblt .= '</table>';

$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->Output();

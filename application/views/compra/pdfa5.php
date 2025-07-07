<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Compra');
$pdf->SetMargins(10,10,10);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L","A5");

// contenido
$pdf->SetFont('helvetica', 'B',8.5);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="25px"><img src="'.$empresa->logo.'" border="0" height="25px"/></td>
        <td width="70%" align="center" height="25px"><br><br> Compra  # '.$id.'</td>
        <td width="15%" align="center" height="25px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());
$pdf->Ln(1);

$pdf->SetFont('helvetica','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="20%"><strong>Fecha Emision: </strong></td>
            <td width="80%">'.$datos->femision.'</td>
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

$pdf->SetFont('helvetica','B',6);
$tbl = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="47%"><strong>DESCRIPCION</strong></th>
            <th align="center" width="10%"><strong>LOTE</strong></th>
            <th align="center" width="10%"><strong>VCTO</strong></th>
            <th align="center" width="5%"><strong>UNID</strong></th>
            <th align="center" width="8%"><strong>CANT</strong></th>
            <th align="center" width="10%"><strong>P.UNIT</strong></th>
            <th align="center" width="10%"><strong>IMPORTE</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',7.5);
    foreach ($detalles as $detalle) {
    $tbl .= '<tr>
            <td>'.$detalle->descripcion.'</td>
            <td>'.$detalle->lote.'</td>
            <td>'.$detalle->fvencimiento.'</td>
            <td>'.$detalle->unidad.'</td>
            <td>'.$detalle->cantidad.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="right">'.$detalle->importe.'</td>
            </tr>';
    }
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP GRATUITAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

    if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>OP GRAVADAS : S/.</strong></td>
        <td width="12%" align="right">'.$datos->tgravado.'</td>
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
        <td width="88%" align="right"><strong>IGV (18%) : S/.</strong></td>
        <td width="12%" align="right">'.$datos->igv.'</td>
    </tr>';
    $tblt .= '<tr>
        <td width="88%" align="right"><strong>IMPORTE : S/.</strong></td>
        <td width="12%" align="right">'.$datos->total.'</td>
    </tr>';
    if ($datos->percepcion>0) {
        $tblt .= '<tr>
            <td width="88%" align="right"><strong>PERCEPCION : S/.</strong></td>
            <td width="12%" align="right">'.$datos->percepcion.'</td>
        </tr>';
        $tblt .= '<tr>
            <td width="88%" align="right"><strong>PAGAR : S/.</strong></td>
            <td width="12%" align="right">'.($datos->total+$datos->percepcion).'</td>
        </tr>';
    }
$tblt .= '</table>';

$pdf->writeHTML($tblt, true, false, false, false, '');

$pdf->Output();

<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Actualizar Inventario');
$pdf->SetMargins(9,10,9);

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
        <td width="20%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="45px"/></td>
        <td width="80%" align="center" height="48px"><br><br> ACTUALIZAR INVENTARIO Nº '.$id.'</td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(1);

$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="2" border="1">
        <tr>
            <th align="center" width="4%"><strong>#</strong></th>
            <th align="center" width="6%"><strong>Codigo</strong></th>
            <th align="center" width="62%"><strong>Descripcion</strong></th>
            <th align="center" width="8%"><strong>Cantidad</strong></th>
            <th align="center" width="10%"><strong>Precio</strong></th>
            <th align="center" width="10%"><strong>Importe</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1; $importe=0;
    foreach ($detalles as $detalle) {
        $tbl .= '<tr>
            <td>'.$i.'</td>
            <td align="right">'.$detalle->id.'</td>
            <td>'.$detalle->descripcion.'</td>
            <td>'.$detalle->cantidad.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="right">'.$detalle->importe.'</td>
            </tr>';
        $i++; $importe+=$detalle->importe;
    }

    $tbl .= '<tr>
            <td align="right" colspan="5"><strong>Total</strong></td>
            <td align="right">'.$importe.'</td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);
$pdf->Output();

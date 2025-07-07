<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Solicitud de Compra');
$pdf->SetMargins(13,10,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('helvetica', '',9.5);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="25%" align="center"><img src="'.$empresa->logo.'" border="0" height="70"/></td>
        <td width="45%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF : '.$nestablecimiento->telefono.'<br>'.'EMAIL : '.$nestablecimiento->email.'</span></td>
        <td width="30%" align="center" style="border: 1px solid #000;"><br><br><br> SOLICITUD  <br> SOL-'.zerofill($id,8).'</td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());
$pdf->Ln(1);

$pdf->SetFont('helvetica','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="20%"><strong>Fecha Emision: </strong></td>
            <td width="80%">'.$datos->femision.'</td>
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

$pdf->SetFont('helvetica','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="80%"><strong>DESCRIPCION</strong></th>
            <th align="center" width="10%"><strong>UNIDAD</strong></th>
            <th align="center" width="10%"><strong>CANT</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',9);
    foreach ($detalles as $detalle) {
    $tbl .= '<tr>
            <td>'.$detalle->descripcion.'</td>
            <td>'.$detalle->unidad.'</td>
            <td>'.$detalle->cantidad.'</td>
            </tr>';
    }
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

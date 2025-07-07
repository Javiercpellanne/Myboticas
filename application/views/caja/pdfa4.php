<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Arqueo de Caja');
$pdf->SetMargins(13,15,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('helvetica', 'B',12);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
        <td width="70%" align="right" height="48px"><br><br> Arqueo de Caja  # '.$id.'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
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
            <td width="20%"><strong>Usuario Generador :</strong></td>
            <td width="80%" colspan="3">'.$nombre->nombres.'</td>
        </tr>
        <tr>
            <td width="20%"><strong>Fecha Inicial :</strong></td>
            <td width="30%">'.$datos->finicial.'</td>
            <td width="20%"><strong>Monto Inicial :</strong></td>
            <td width="30%">'.$datos->minicial.'</td>
        </tr>
        <tr>
            <td><strong>Fecha Final :</strong></td>
            <td>'.$datos->ffinal.'</td>
            <td><strong>Monto Final :</strong></td>
            <td>'.$datos->mfinal.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$mfinal=$datos->minicial+$datos->ventas+$datos->ingresos-$datos->compras-$datos->egresos;
$diferencia=round($datos->mfinal-$mfinal,2);
$tbld = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="20%"><strong>Ventas :</strong></td>
        <td width="80%" colspan="3">'.$datos->ventas.'</td>
    </tr>
    <tr>
        <td><strong>Compras :</strong></td>
        <td colspan="3">'.$datos->compras.'</td>
    </tr>
    <tr>
        <td><strong>Ingresos :</strong></td>
        <td colspan="3">'.$datos->ingresos.'</td>
    </tr>
    <tr>
        <td><strong>Egresos :</strong></td>
        <td colspan="3">'.$datos->egresos.'</td>
    </tr>';
    if ($diferencia!=0) {
    $tbld .= '<tr>
        <td><b>Diferencia :</b></td>
        <td>'.$diferencia.'</td>
    </tr>';
    }
$tbld .= '</table>';
$pdf->writeHTML($tbld, false, false, false, false, '');
$pdf->Ln(2);

$tblm = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <th width="5%"><strong>#</strong></th>
        <th width="20%"><strong>Medio Pago</strong></th>
        <th width="10%"><strong>Importe</strong></th>
    </tr>';
    $i=1;
    $pdf->SetFont('helvetica','',8);
    foreach ($detalles as $lista) {
    $tblm.='<tr>
        <td>'.$i.'</td>
        <td>'.$lista->ntpago.'</td>
        <td align="right">'.$lista->importe.'</td>
    </tr>';
    $i++;
    }
    $tblm.='</table>';
$pdf->writeHTML($tblm, false, false, false, false, '');

$pdf->AddPage();
$pdf->SetFont('helvetica','B',8);
$tblc = '<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <th width="5%"><strong>#</strong></th>
        <th width="13%"><strong>Tipo</strong></th>
        <th width="10%"><strong>Fecha</strong></th>
        <th width="37%"><strong>Adquiriente</strong></th>
        <th width="15%"><strong>Documento</strong></th>
        <th width="10%"><strong>Numero</strong></th>
        <th width="10%"><strong>Importe</strong></th>
    </tr>';
    $i=1; $gventas=0;
    $pdf->SetFont('helvetica','',8);
    foreach ($cobros as $lista) {
    $datos=$this->nventa_model->mostrar($lista->idnventa);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Nota de Venta</td>
        <td>'.$lista->femision.'</td>
        <td>'.$datos->cliente .'</td>
        <td>Nota de Venta</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($cobrose as $lista) {
    $datos=$this->venta_model->mostrar($lista->idventa);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>CPE</td>
        <td>'.$lista->femision.'</td>
        <td>'.$datos->cliente .'</td>
        <td>'.$datos->ncomprobante.'</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($cobrosn as $lista) {
    $datos=$this->nota_model->mostrar($lista->idnota);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>CPE</td>
        <td>'.$lista->femision.'</td>
        <td>'.$datos->cliente .'</td>
        <td>'.$datos->ncomprobante.'</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($ingresos as $lista) {
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Ingreso</td>
        <td>'.$lista->femision.'</td>
        <td>'.$lista->cliente .'</td>
        <td>'.$lista->ningreso.'</td>
        <td>'.$lista->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($pagos as $lista) {
    $datos=$this->compra_model->mostrar($lista->idcompra);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Compra</td>
        <td>'.$lista->femision.'</td>
        <td>'.$datos->proveedor .'</td>
        <td>'.$datos->ncomprobante.'</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">-'.$lista->total.'</td>
    </tr>';
    $i++; $gventas-=$lista->total;
    }
    foreach ($egresos as $lista) {
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Egresos</td>
        <td>'.$lista->femision.'</td>
        <td>'.$lista->proveedor .'</td>
        <td>'.$lista->negreso.'</td>
        <td>'.$lista->numero.'</td>
        <td align="right">-'.$lista->total.'</td>
    </tr>';
    $i++; $gventas-=$lista->total;
    }
    $tblc.='<tr>
        <td colspan="6" align="right"><b>Total General</b></td>
        <td align="right">'.formatoPrecio($gventas).'</td>
    </tr>';
    $tblc.='</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');

$pdf->Output();

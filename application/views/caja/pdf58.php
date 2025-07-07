<?php
$altura=$detalles!=null ? 250+(count($detalles)*5) : 250;
$tamaño=array(58,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Arqueo de Caja');
$pdf->SetMargins(3,2,3);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
$pdf->SetFont('helvetica', 'B', 6.5);
if ($empresa->lticket!='') {
    $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td align="center"><img src="'.$empresa->lticket.'" border="0" height="45" /></td>
            </tr>
            </table>';
    $pdf->writeHTML($tblc, false, false, false, false, '');
    $pdf->Ln(2);
}

$pdf->SetFont('helvetica','B',8);
$pdf->Cell(0,4,'Establecimiento '.$nestablecimiento->descripcion,0,1,'C');
$pdf->Cell(0,4,'Arqueo de Caja  # '.$id,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',6.5);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="40%"><strong>Generador :</strong></td>
            <td width="60%">'.$nombre->nombres.'</td>
        </tr>
        <tr>
            <td><strong>Fecha Inicial :</strong></td>
            <td>'.$datos->finicial.'</td>
        </tr>
        <tr>
            <td><strong>Monto Inicial :</strong></td>
            <td>'.$datos->minicial.'</td>
        </tr>
        <tr>
            <td><strong>Fecha Final :</strong></td>
            <td>'.$datos->ffinal.'</td>
        </tr>
        <tr>
            <td><strong>Monto Final :</strong></td>
            <td>'.$datos->mfinal.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(3, $pdf->getY(), $pdf->getPageWidth()-3, $pdf->getY());
$pdf->Ln(1);

$mfinal=$datos->minicial+$datos->ventas+$datos->ingresos-$datos->compras-$datos->egresos;
$diferencia=round($datos->mfinal-$mfinal,2);
$tbld = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="40%"><strong>Ventas :</strong></td>
        <td width="60%">'.$datos->ventas.'</td>
    </tr>
    <tr>
        <td><strong>Compras :</strong></td>
        <td>'.$datos->compras.'</td>
    </tr>
    <tr>
        <td><strong>Ingresos :</strong></td>
        <td>'.$datos->ingresos.'</td>
    </tr>
    <tr>
        <td><strong>Egresos :</strong></td>
        <td>'.$datos->egresos.'</td>
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

$pdf->SetFont('helvetica','B',6.5);
$tblm = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <th width="10%"><strong>#</strong></th>
        <th width="65%"><strong>Medio Pago</strong></th>
        <th width="25%"><strong>Importe</strong></th>
    </tr>';
    $i=1;
    $pdf->SetFont('helvetica','',6.5);
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
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',6);
$tblc = '<table cellspacing="0" cellpadding="1" border="1">
    <tr>
        <th width="10%"><strong>#</strong></th>
        <th width="35%"><strong>Transaccion</strong></th>
        <th width="35%"><strong>Numero</strong></th>
        <th width="20%"><strong>Importe</strong></th>
    </tr>';
    $i=1; $gventas=0;
    $pdf->SetFont('helvetica','',6);
    foreach ($cobros as $lista) {
    $datos=$this->nventa_model->mostrar($lista->idnventa);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Venta</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($cobrose as $lista) {
    $datos=$this->venta_model->mostrar($lista->idventa);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Venta</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($cobrosn as $lista) {
    $datos=$this->nota_model->mostrar($lista->idnota);
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Venta</td>
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">'.$lista->total.'</td>
    </tr>';
    $i++; $gventas+=$lista->total;
    }
    foreach ($ingresos as $lista) {
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Ingreso</td>
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
        <td>'.$datos->serie.'-'.$datos->numero.'</td>
        <td align="right">-'.$lista->total.'</td>
    </tr>';
    $i++; $gventas-=$lista->total;
    }
    foreach ($egresos as $lista) {
    $tblc.='<tr>
        <td>'.$i.'</td>
        <td>Egresos</td>
        <td>'.$lista->numero.'</td>
        <td align="right">-'.$lista->total.'</td>
    </tr>';
    $i++; $gventas-=$lista->total;
    }
    $tblc.='<tr>
        <td colspan="3" align="right"><b>Total General</b></td>
        <td align="right">'.formatoPrecio($gventas).'</td>
    </tr>';
    $tblc.='</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');

$pdf->Output();

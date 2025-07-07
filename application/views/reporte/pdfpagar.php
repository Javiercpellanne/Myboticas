<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Deudas Proveedor');
$pdf->SetMargins(8,10,8);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('helvetica', 'B',14);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
        <td width="70%" align="center" height="48px"><br> DEUDA DE : '.$this->input->post('proveedor',true).'<br>DESDE '.FormatoFecha($this->input->post('pinicio',true)).' AL '.FormatoFecha($this->input->post('pfin',true)).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Fecha</strong></th>
            <th align="center" width="15%" style="border: 1px solid #000;"><strong>Numero</strong></th>
            <th align="center" width="35%" style="border: 1px solid #000;"><strong>A cuenta</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Total</strong></th>
            <th align="center" width="15%" style="border: 1px solid #000;"><strong>Pagado</strong></th>
            <th align="center" width="15%" style="border: 1px solid #000;"><strong>Saldo</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $mtotal=0; $mpagado=0; $msaldo=0;
    foreach ($datos as $dato) {
        $pagos=$this->pago_model->mostrarTotal(array("idcompra"=>$dato->id));
        $pagado=$this->pago_model->montoTotal(array("idcompra"=>$dato->id));
        $saldo=$dato->total-($pagado->total??0);
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;"><table cellspacing="0" cellpadding="1" border="0">';
            foreach ($pagos as $pago) {
                $tbl .= '<tr>
                    <td align="right">'.$pago->femision.'</td>
                    <td> S/. '.$pago->total.'</td>
                </tr>';
            }
            $tbl .= '</table></td>
            <td align="right" style="border-top: 1px solid #000;">S/. '.$dato->total.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.($pagado->total??0).'</td>
            <td align="right" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.formatoPrecio($saldo).'</td>
        </tr>';
        $mtotal+=$dato->total; $mpagado+=($pagado->total??0); $msaldo+=$saldo;
        $i++;
    }
    $tbl .= '<tr>
        <td align="right" colspan="3" style="border-top: 1px solid #000;"><strong>Total</strong></td>
        <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($mtotal).'</td>
        <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($mpagado).'</td>
        <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($msaldo).'</td>
    </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

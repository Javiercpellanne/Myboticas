<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Compras');
$pdf->SetMargins(8,10,8);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L");

// contenido
$pdf->SetFont('helvetica', 'B',12);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
        <td width="70%" align="center" height="48px"><br> COMPRA DEL PRODUCTO '.$this->input->post('descripcion',true).'<br>DESDE '.FormatoFecha($this->input->post('finicio',true)).' AL '.FormatoFecha($this->input->post('ffin',true)).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Fecha</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Tipo</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Numero</strong></th>
            <th align="center" width="40%" style="border: 1px solid #000;"><strong>Proveedor</strong></th>
            <th align="center" width="7%" style="border: 1px solid #000;"><strong>Cantidad</strong></th>
            <th align="center" width="5%" style="border: 1px solid #000;"><strong>Unidad</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Impuesto</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Precio</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Total</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $general=0;
    foreach ($datos as $dato) {
        $impuesto='';
        if ($dato->tafectacion=='10') {
            $impuesto=$dato->incluye==0 ? 's/igv' : 'c/igv' ;
        }
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->proveedor.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->cantidad.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->unidad.'</td>
            <td align="center" style="border-top: 1px solid #000;">'.$impuesto.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->precio.'</td>
            <td align="right" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$dato->importe.'</td>
            </tr>';
        $general+=$dato->importe;
        $i++;
    }

    $tbl .= '<tr>
            <td align="right" colspan="8" style="border-top: 1px solid #000;"><strong>Total Compra</strong></td>
            <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($general).'</td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

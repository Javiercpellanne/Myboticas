<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Compras Proveedor');
$pdf->SetMargins(8,10,8);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L");

// contenido
$pdf->SetFont('helvetica', 'B',14);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
        <td width="70%" align="center" height="48px"><br><br> COMPRA POR PROVEEDOR DESDE '.FormatoFecha($this->input->post('pinicio',true)).' AL '.FormatoFecha($this->input->post('pfin',true)).'</td>
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
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Ruc</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Subtotal</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>IGV</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Total</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $general=0;
    foreach ($datos as $dato) {
        $proveedores=$this->proveedor_model->mostrar(array("p.id"=>$dato->idproveedor));
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->proveedor.'</td>
            <td style="border-top: 1px solid #000;">'.$proveedores->documento.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->subtotal.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->igv.'</td>
            <td align="right" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$dato->total.'</td>
            </tr>';
        $general+=$dato->total;
        $i++;

        if ($detallado==1) {
            $detalles=$this->comprad_model->mostrarTotal($dato->id);

            $tbl .= '<tr>
                    <td align="center" style="border-left: 1px solid #000;"></td>
                    <td align="center"><strong>Cantidad</strong></td>
                    <td align="center"><strong>Unidad</strong></td>
                    <td align="center"><strong>Descripcion</strong></td>
                    <td align="center"><strong>Lote</strong></td>
                    <td align="center"><strong>Fecha Vcto</strong></td>
                    <td align="center"><strong>Precio</strong></td>
                    <td align="center" style="border-right: 1px solid #000;"><strong>Importe</strong></td>
                    </tr>';
            foreach ($detalles as $detalle) {
                $tbl .= '<tr>
                    <td style="border-left: 1px solid #000;"></td>
                    <td align="right">'.$detalle->cantidad.'</td>
                    <td>'.$detalle->unidad.'</td>
                    <td>'.$detalle->descripcion.'</td>
                    <td>'.$detalle->lote.'</td>
                    <td>'.$detalle->fvencimiento.'</td>
                    <td align="right">'.$detalle->precio.'</td>
                    <td align="right" style="border-right: 1px solid #000;">'.$detalle->importe.'</td>
                    </tr>';
            }
        }
    }

    $tbl .= '<tr>
            <td align="right" colspan="7" style="border-top: 1px solid #000;"><strong>Total Compra</strong></td>
            <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($general).'</td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

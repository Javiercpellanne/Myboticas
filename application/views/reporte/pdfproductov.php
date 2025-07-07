<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Ventas');
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
        <td width="70%" align="center" height="48px"><br> VENTAS DEL PRODUCTO '.$this->input->post('descripcion',true).'<br>DESDE '.FormatoFecha($this->input->post('finicio',true)).' AL '.FormatoFecha($this->input->post('ffin',true)).'</td>
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
            <th align="center" width="35%" style="border: 1px solid #000;"><strong>Cliente</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Cantidad</strong></th>
            <th align="center" width="4%" style="border: 1px solid #000;"><strong>Unidad</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Precio</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Total</strong></th>
            <th align="center" width="13%" style="border: 1px solid #000;"><strong>Usuario</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $general=0;
    foreach ($nventas as $dato) {
        $nombre= $this->usuario_model->mostrar($dato->iduser);
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">Nota de Venta</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->cantidad.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->unidad.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->precio.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->importe.'</td>
            <td style="border-right: 1px solid #000; border-top: 1px solid #000;">'.($nombre->nombres??'').'</td>
            </tr>';
        $general+=$dato->importe;
        $i++;
    }

    foreach ($ventas as $dato) {
        $nombre= $this->usuario_model->mostrar($dato->iduser);
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->cantidad.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->unidad.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->precio.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->importe.'</td>
            <td style="border-right: 1px solid #000; border-top: 1px solid #000;">'.($nombre->nombres??'').'</td>
            </tr>';
        $general+=$dato->importe;
        $i++;
    }

    foreach ($notas as $dato) {
        $nombre= $this->usuario_model->mostrar($dato->iduser);
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->cantidad.'</td>
            <td style="border-top: 1px solid #000;">'.$dato->unidad.'</td>
            <td align="right" style="border-top: 1px solid #000;">'.$dato->precio.'</td>
            <td align="right" style="border-top: 1px solid #000;"><font color="#FF0000">-'.$dato->importe.'</font></td>
            <td style="border-right: 1px solid #000; border-top: 1px solid #000;">'.($nombre->nombres??'').'</td>
            </tr>';
        $general-=$dato->importe;
        $i++;
    }

    $tbl .= '<tr>
        <td align="right" colspan="7" style="border-top: 1px solid #000;"><strong>Total Venta</strong></td>
        <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($general).'</td>
        <td align="right" style="border-top: 1px solid #000;"></td>
    </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

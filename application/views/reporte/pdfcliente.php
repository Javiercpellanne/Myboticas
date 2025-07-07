<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Ventas Clientes');
$pdf->SetMargins(5,10,5);

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
        <td width="70%" align="center" height="48px"><br><br> VENTAS POR CLIENTE DESDE '.FormatoFecha($this->input->post('pinicio',true)).' AL '.FormatoFecha($this->input->post('pfin',true)).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',8);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Fecha</strong></th>
            <th align="center" width="5%" style="border: 1px solid #000;"><strong>Hora</strong></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><strong>Tipo</strong></th>
            <th align="center" width="9%" style="border: 1px solid #000;"><strong>Numero</strong></th>
            <th align="center" width="34%" style="border: 1px solid #000;"><strong>Cliente</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Subtotal</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>IGV</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Total</strong></th>
            <th align="center" width="7%" style="border: 1px solid #000;"><strong>Izipay</strong></th>
            <th align="center" width="13%" style="border: 1px solid #000;"><strong>Usuario</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',7);
    $i=1;
    $general=0; $tizipay=0;
    foreach ($comprobantes as $dato) {
        if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
            if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

            $tbl .= '<tr>
                <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
                <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
                <td width="8%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
                <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
                <td width="34%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
                <td width="7%" style="border-top: 1px solid #000;"><font color="#FF0000">'.$nestado.'</font></td>
                <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
                <td width="6%"align="right" style="border-top: 1px solid #000;"></td>
                <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
                <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;"></td>
                </tr>';
            $i++;
        } else {
            $nombre= $this->usuario_model->mostrar($dato->iduser);
            $nusuario=$nombre->nombres??'';
            $tbl .= '<tr>
                <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
                <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
                <td width="8%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
                <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
                <td width="34%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
                <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->total.'</td>
                <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->tigv.'</td>
                <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->total.'</td>
                <td width="7%" align="right" style="border-top: 1px solid #000;">'.$dato->izipay.'</td>
                <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$nusuario.'</td>
                </tr>';
            $general+=$dato->total; $tizipay+=$dato->izipay;
            $i++;

            if ($detallado==1) {
                $detalles=$this->ventad_model->mostrarTotal($dato->id);
                $tbl .= '<tr>
                        <td align="center" width="4%" style="border-left: 1px solid #000;"></td>
                        <td align="center" width="4%"><strong>Cantidad</strong></td>
                        <td align="center" width="40%"><strong>Descripcion</strong></td>
                        <td align="center" width="7%"><strong>Lote</strong></td>
                        <td align="center" width="6%"><strong>Fecha Vcto</strong></td>
                        <td align="center" width="6%"><strong>Precio</strong></td>
                        <td align="center" width="6%"><strong>Importe</strong></td>
                        <td align="center" width="14%"></td>
                        <td align="center" width="13%" style="border-right: 1px solid #000;"></td>
                        </tr>';
                foreach ($detalles as $detalle) {
                    $importeu=$dato->nulo==1 ? 0 : $detalle->importe ;
                    $tbl .= '<tr>
                        <td align="center" style="border-left: 1px solid #000;"></td>
                        <td align="right">'.$detalle->cantidad.'</td>
                        <td>'.$detalle->descripcion.'</td>
                        <td>'.$detalle->lote.'</td>
                        <td>'.$detalle->fvencimiento.'</td>
                        <td align="right">'.$detalle->precio.'</td>
                        <td align="right">'.$importeu.'</td>
                        <td></td>
                        <td style="border-right: 1px solid #000;"></td>
                        </tr>';
                }
            }
        }
    }

    foreach ($notas as $dato) {
        $nombre= $this->usuario_model->mostrar($dato->iduser);
        $nusuario=$nombre->nombres??'';
        if ($dato->tcomprobante=="07") {$color='#FF0000'; $signo='-';} else {$color='#000000'; $signo='-';}
        $tbl .= '<tr>
            <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
            <td width="8%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td width="34%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->total.'</font></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->tigv.'</font></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->total.'</font></td>
            <td width="7%" style="border-top: 1px solid #000;"></td>
            <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$nusuario.'</td>
            </tr>';
        if ($dato->tcomprobante=="07") {$general-=$dato->total;} else {$general+=$dato->total;}
        $i++;

        if ($detallado==1) {
            $detalles=$this->notad_model->mostrarTotal($dato->id);
            $tbl .= '<tr>
                    <td align="center" width="4%" style="border-left: 1px solid #000;"></td>
                    <td align="center" width="4%"><strong>Cantidad</strong></td>
                    <td align="center" width="40%"><strong>Descripcion</strong></td>
                    <td align="center" width="7%"><strong>Lote</strong></td>
                    <td align="center" width="6%"><strong>Fecha Vcto</strong></td>
                    <td align="center" width="6%"><strong>Precio</strong></td>
                    <td align="center" width="6%"><strong>Importe</strong></td>
                    <td align="center" width="14%"></td>
                    <td align="center" width="13%" style="border-right: 1px solid #000;"></td>
                    </tr>';
            foreach ($detalles as $detalle) {
                $tbl .= '<tr>
                    <td style="border-left: 1px solid #000;"></td>
                    <td align="right">'.$detalle->cantidad.'</td>
                    <td>'.$detalle->descripcion.'</td>
                    <td>'.$detalle->lote.'</td>
                    <td>'.$detalle->fvencimiento.'</td>
                    <td align="right"><font color="'.$color.'">'.$signo.$detalle->precio.'</font></td>
                    <td align="right"><font color="'.$color.'">'.$signo.$detalle->importe.'</font></td>
                    <td></td>
                    <td style="border-right: 1px solid #000;"></td>
                    </tr>';
            }
        }
    }

    foreach ($datos as $dato) {
        $nombre= $this->usuario_model->mostrar($dato->iduser);
        $nusuario=$nombre->nombres??'';
        $tbl .= '<tr>
                <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
                <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
                <td width="8%" style="border-top: 1px solid #000;">Nota de Venta</td>
                <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
                <td width="34%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
                <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
                <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
                <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->total.'</td>
                <td width="7%" align="right" style="border-top: 1px solid #000;">'.$dato->izipay.'</td>
                <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$nusuario.'</td>
            </tr>';
            $general+=$dato->total; $tizipay+=$dato->izipay;
            $i++;

            if ($detallado==1) {
                $detalles=$this->nventad_model->mostrarTotal($dato->id);
                $tbl .= '<tr>
                        <td align="center" width="4%" style="border-left: 1px solid #000;"></td>
                        <td align="center" width="4%"><strong>Cantidad</strong></td>
                        <td align="center" width="40%"><strong>Descripcion</strong></td>
                        <td align="center" width="7%"><strong>Lote</strong></td>
                        <td align="center" width="6%"><strong>Fecha Vcto</strong></td>
                        <td align="center" width="6%"><strong>Precio</strong></td>
                        <td align="center" width="6%"><strong>Importe</strong></td>
                        <td align="center" width="14%"></td>
                        <td align="center" width="13%" style="border-right: 1px solid #000;"></td>
                        </tr>';
                foreach ($detalles as $detalle) {
                    $importeu=$dato->nulo==1 ? 0 : $detalle->importe ;
                    $tbl .= '<tr>
                        <td align="center" style="border-left: 1px solid #000;"></td>
                        <td align="right">'.$detalle->cantidad.'</td>
                        <td>'.$detalle->descripcion.'</td>
                        <td>'.$detalle->lote.'</td>
                        <td>'.$detalle->fvencimiento.'</td>
                        <td align="right">'.$detalle->precio.'</td>
                        <td align="right">'.$importeu.'</td>
                        <td></td>
                        <td style="border-right: 1px solid #000;"></td>
                        </tr>';
                }
            }
    }
    $tbl .= '<tr>
            <td align="right" width="74%" style="border-top: 1px solid #000;"><strong>Total Comprobante</strong></td>
            <td align="right" width="6%" style="border-top: 1px solid #000;">'.formatoPrecio($general).'</td>
            <td align="right" width="7%" style="border-top: 1px solid #000;">'.formatoPrecio($tizipay).'</td>
            <td align="right" width="13%" style="border-top: 1px solid #000;"></td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Reporte Ventas');
$pdf->SetMargins(5,10,5);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L");

// contenido
$pdf->SetFont('helvetica', 'B',13);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="12%" align="center"><img src="'.$nestablecimiento->logoe.'" border="0" height="50"/></td>
            <td width="88%" align="center"><br><br> COMPROBANTES DESDE '.FormatoFecha($this->input->post('finicio',true)).' AL '.FormatoFecha($this->input->post('ffin',true)).' HORA INICIO '.$this->input->post('hinicio',true).' FIN '.$this->input->post('hfin',true).'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',8);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Fecha</strong></th>
            <th align="center" width="5%" style="border: 1px solid #000;"><strong>Hora</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Tipo</strong></th>
            <th align="center" width="9%" style="border: 1px solid #000;"><strong>Numero</strong></th>
            <th align="center" width="32%" style="border: 1px solid #000;"><strong>Cliente</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Subtotal</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>IGV</strong></th>
            <th align="center" width="6%" style="border: 1px solid #000;"><strong>Total</strong></th>
            <th align="center" width="7%" style="border: 1px solid #000;"><strong>Izipay</strong></th>
            <th align="center" width="13%" style="border: 1px solid #000;"><strong>Usuario</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',7);
    $i=1; $general=0; $tizipay=0;
    foreach ($ventas as $dato) {
        if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
            if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

            $tbl .= '<tr>
            <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
            <td width="10%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td width="32%" style="border-top: 1px solid #000;"><font color="#FF0000">'.$nestado.'</font></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
            <td width="6%"align="right" style="border-top: 1px solid #000;"></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"></td>
            <td width="7%" style="border-top: 1px solid #000;"></td>
            <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;"></td>
            </tr>';
            $i++;
        } else {
            $nombre= $this->usuario_model->mostrar($dato->iduser);
            $nusuario=$nombre->nombres??'';
            $tbl .= '<tr>
            <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
            <td width="10%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td width="32%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->subtotal.'</td>
            <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->tigv.'</td>
            <td width="6%" align="right" style="border-top: 1px solid #000;">'.$dato->total.'</td>
            <td width="7%" align="right" style="border-top: 1px solid #000;">'.$dato->izipay.'</td>
            <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$nusuario.'</td>
            </tr>';
            $general+=$dato->total; $tizipay+=$dato->izipay;
            $i++;
        }
    }

    foreach ($notas as $dato) {$nombre= $this->usuario_model->mostrar($dato->iduser);
        $nusuario=$nombre->nombres??'';
        if ($dato->tcomprobante=="07") {$color='#FF0000'; $signo='-';} else {$color='#000000'; $signo='';}
        $tbl .= '<tr>
            <td width="6%" style="border-left: 1px solid #000; border-top: 1px solid #000;">'.$dato->femision.'</td>
            <td width="5%" style="border-top: 1px solid #000;">'.$dato->hemision.'</td>
            <td width="10%" style="border-top: 1px solid #000;">'.$dato->ncomprobante.'</td>
            <td width="9%" style="border-top: 1px solid #000;">'.$dato->serie.'-'.$dato->numero.'</td>
            <td width="32%" style="border-top: 1px solid #000;">'.$dato->cliente.'</td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->subtotal.'</font></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->tigv.'</font></td>
            <td width="6%" align="right" style="border-top: 1px solid #000;"><font color="'.$color.'">'.$signo.$dato->total.'</font></td>
            <td width="7%" align="right" style="border-top: 1px solid #000;"></td>
            <td width="13%" style="border-right: 1px solid #000; border-top: 1px solid #000;">'.$nusuario.'</td>
            </tr>';
        if ($dato->tcomprobante=="07") {$general-=$dato->total;} else {$general+=$dato->total;}
        $i++;
    }
    $tbl .= '<tr>
            <td align="right" colspan="7" style="border-top: 1px solid #000;"><strong>Total Comprobante</strong></td>
            <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($general).'</td>
            <td align="right" style="border-top: 1px solid #000;">'.formatoPrecio($tizipay).'</td>
            <td align="right" style="border-top: 1px solid #000;"></td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

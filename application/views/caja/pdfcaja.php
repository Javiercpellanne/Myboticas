<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Consulta de Caja');
$pdf->SetMargins(8,13,8);

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
        <td width="70%" align="center" height="48px"><br><br> CONSULTA CAJA DEL '.FormatoFecha($inicio).' AL '.FormatoFecha($fin).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
      <th><strong>Modalidad</strong></th>';
      if ($empresa->facturacion==1) {
      $tblf .= '<th><strong>CPE</strong></th>';
      }
      $tblf .= '<th><strong>Nota Venta</strong></th>
      <th><strong>Ingresos</strong></th>
      <th><strong>Compras</strong></th>
      <th><strong>Egresos</strong></th>
    </tr>';
    $tcomprobantes=0; $tnventas=0; $tingresos=0; $tcompras=0; $tegresos=0;
    foreach ($medios as $medio) {
        $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin,"idtpago"=>$medio->id);
        if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
        //cobros comprobante
        $mcobrosc=$this->cobroe_model->montoTotal($filtros);
        //pagos comprobante
        $mcobrosn=$this->cobron_model->montoTotal($filtros);
        $totalComprobante=$mcobrosc->total+$mcobrosn->total;

        //cobros
        $mcobros=$this->cobro_model->montoTotal($filtros);
        $totalNventas=$mcobros->total;

        //ingresos
        $mingresos=$this->ingreso_model->montoTotal($filtros);
        $totalIngresos=$mingresos->total;

         //pagos
        $mpagoc=$this->pago_model->montoTotal($filtros);
        $totalCompras=$mpagoc->total;

        //egresos
        $megresoegresos=$this->egreso_model->montoTotal($filtros);
        $totalEgresos=$megresos->total;

        $tcomprobantes+=$totalComprobante; $tnventas+=$totalNventas; $tingresos+=$totalIngresos; $tcompras+=$totalCompras; $tegresos+=$totalEgresos;
        $tblf .= '<tr>
            <td><strong>'.$medio->descripcion.'</strong></td>';
            if ($empresa->facturacion==1) {
            $tblf .= '<td align="right">'.formatoPrecio($totalComprobante).'</td>';
            }
            $tblf .= '<td align="right">'.formatoPrecio($totalNventas).'</td>
            <td align="right">'.formatoPrecio($totalIngresos).'</td>
            <td align="right">'.formatoPrecio($totalCompras).'</td>
            <td align="right">'.formatoPrecio($totalEgresos).'</td>
        </tr>';
    }
    $tblf .= '<tr>
        <td align="right"><strong>Totales</strong></td>';
        if ($empresa->facturacion==1) {
        $tblf .= '<td align="right"><strong>'.formatoPrecio($tcomprobantes).'</strong></td>';
        }
        $tblf .= '<td align="right"><strong>'.formatoPrecio($tnventas).'</strong></td>
        <td align="right"><strong>'.formatoPrecio($tingresos).'</strong></td>
        <td align="right"><strong>'.formatoPrecio($tcompras).'</strong></td>
        <td align="right"><strong>'.formatoPrecio($tegresos).'</strong></td>
    </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->Output();

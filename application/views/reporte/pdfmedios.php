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
if ($this->session->userdata("tipo")=='admin') {
    if ($this->input->post('cusuario',true)=='') {
        $nusuario='Todos';
    } else {
        $usuarios=$this->usuario_model->mostrar($this->input->post('cusuario',true));
        $nusuario=$usuarios->nombres;
    }
} else {
    $usuarios=$this->usuario_model->mostrar($this->session->userdata("id"));
    $nusuario=$usuarios->nombres;
}
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="15%" align="center" height="48px"><img src="'.$empresa->logo.'" border="0" height="44px"/></td>
        <td width="70%" align="center" height="48px"><br> VENTA SEGUN MEDIO PAGO DEL '.FormatoFecha($this->input->post('minicio',true)).' AL '.FormatoFecha($this->input->post('mfin',true)).'<br>'.$nusuario.'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
      <th><strong>Modalidad</strong></th>
      <th><strong>CPE</strong></th>
      <th><strong>Nota Venta</strong></th>
      <th><strong>Total</strong></th>
    </tr>';
    $tcomprobantes=0; $tnventas=0; $tmedios=0;
    foreach ($mpagos as $mpago) {
        if ($this->input->post('cusuario',true)=='') {
        $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('minicio',true),"femision<="=>$this->input->post('mfin',true),"idtpago"=>$mpago->id);
        } else {
        $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),'iduser'=>$this->input->post('cusuario',true),"nulo"=>0,"femision>="=>$this->input->post('minicio',true),"femision<="=>$this->input->post('mfin',true),"idtpago"=>$mpago->id);
        }
        if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
        //cobros comprobante
        $mcobrosc=$this->cobroe_model->montoTotal($filtros);
        //pagos comprobante
        $mcobrosn=$this->cobron_model->montoTotal($filtros);
        $totalComprobante=$mcobrosc->total+$mcobrosn->total;

        //cobros
        $mcobros=$this->cobro_model->montoTotal($filtros);
        $totalNventas=$mcobros->total;

        $totalmedio=$totalComprobante+$totalNventas;
        $tcomprobantes+=$totalComprobante; $tnventas+=$totalNventas; $tmedios+=$totalmedio;
        $tblf .= '<tr>
            <td><strong>'.$mpago->descripcion.'</strong></td>
            <td align="right">'.formatoPrecio($totalComprobante).'</td>
            <td align="right">'.formatoPrecio($totalNventas).'</td>
            <td align="right">'.formatoPrecio($totalmedio).'</td>
        </tr>';
    }
    $tblf .= '<tr>
                <td align="right"><strong>Totales</strong></td>
                <td align="right"><strong>'.formatoPrecio($tcomprobantes).'</strong></td>
                <td align="right"><strong>'.formatoPrecio($tnventas).'</strong></td>
                <td align="right"><strong>'.formatoPrecio($tmedios).'</strong></td>
            </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

if ($detallado==1) {
    $pdf->SetFont('helvetica','',8);
    $tblv = '<table cellspacing="0" cellpadding="2" border="1">
        <tr>
          <td width="8%"><strong>Fecha</strong></td>
          <td width="7%"><strong>Hora</strong></td>
          <td width="11%"><strong>Tipo</strong></td>
          <td width="11%"><strong>Numero</strong></td>
          <td width="45%"><strong>Cliente</strong></td>
          <td width="10%"><strong>Ruc</strong></td>
          <td width="8%"><strong>Total</strong></td>
        </tr>';
        $pdf->SetFont('helvetica','',7);
        foreach ($mpagos as $mpago) {
            if ($this->input->post('cusuario',true)=='') {
            $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('minicio',true),"femision<="=>$this->input->post('mfin',true),"idtpago"=>$mpago->id);
            } else {
            $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),'iduser'=>$this->input->post('cusuario',true),"nulo"=>0,"femision>="=>$this->input->post('minicio',true),"femision<="=>$this->input->post('mfin',true),"idtpago"=>$mpago->id);
            }
            if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
            $comprobantes=$this->cobroe_model->mostrarTotal($filtros);
            $notas=$this->cobron_model->mostrarTotal($filtros);
            $ventas=$this->cobro_model->mostrarTotal($filtros);

            if ($comprobantes!=null ||  $notas!=null || $ventas!=null) {
                $general=0;
                $tblv .= '<tr>
                  <td colspan="7"><strong>'.$mpago->descripcion.'</strong></td>
                </tr>';

                foreach ($comprobantes as $dato){
                $venta=$this->venta_model->mostrar($dato->idventa);
                $clientes=$this->cliente_model->mostrar($venta->idcliente);
                $tblv .= '<tr>
                      <td width="8%">'.$venta->femision.'</td>
                      <td width="7%">'.$venta->hemision.'</td>
                      <td width="11%">'.$venta->ncomprobante.'</td>
                      <td width="11%">'.$venta->serie.'-'.$venta->numero.'</td>
                      <td width="45%">'.$venta->cliente.'</td>
                      <td width="10%">'.$clientes->documento.'</td>
                      <td width="8%" align="right">'.$dato->total.'</td>
                    </tr>';
                    $general+=$dato->total;
                }

                foreach ($notas as $dato){
                $venta=$this->nota_model->mostrar($dato->idnota);
                $clientes=$this->cliente_model->mostrar($venta->idcliente);
                if ($dato->total<0) {$color='#FF0000';} else {$color='#000000';}
                $tblv .= '<tr>
                      <td width="8%">'.$venta->femision.'</td>
                      <td width="7%">'.$venta->hemision.'</td>
                      <td width="11%">'.$venta->ncomprobante.'</td>
                      <td width="11%">'.$venta->serie.'-'.$venta->numero.'</td>
                      <td width="45%">'.$venta->cliente.'</td>
                      <td width="10%">'.$clientes->documento.'</td>
                      <td width="8%" align="right"><font color="'.$color.'">'.$dato->total.'</font></td>
                    </tr>';
                    $general+=$dato->total;
                }

                foreach ($ventas as $dato){
                $venta=$this->nventa_model->mostrar($dato->idnventa);
                $clientes=$this->cliente_model->mostrar($venta->idcliente);
                $tblv .= '<tr>
                      <td width="8%">'.$venta->femision.'</td>
                      <td width="7%">'.$venta->hemision.'</td>
                      <td width="11%"> Nota Venta </td>
                      <td width="11%">'.$venta->serie.'-'.$venta->numero.'</td>
                      <td width="45%">'.$venta->cliente.'</td>
                      <td width="10%">'.$clientes->documento.'</td>
                      <td width="8%" align="right">'.$dato->total.'</td>
                    </tr>';
                    $general+=$dato->total;
                }

                $tblv .= '<tr>
                    <td colspan="6" align="right"><strong>Total '.$mpago->descripcion.'</strong></td>
                    <td align="right">'.formatoPrecio($general).'</td>
                </tr>';
            }
        }
    $tblv .= '</table>';
    $pdf->writeHTML($tblv, false, false, false, false, '');
    $pdf->Ln(2);
}

$pdf->Output();

<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Consulta de Ventas Anual');
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
        <td width="70%" align="center" height="48px"><br><br> VENTA ANUAL DEL '.$this->input->post('ganuos',true).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
      <th align="center"><b>MESES</b></th>
      <th align="center"><b>BOLETAS</b></th>
      <th align="center"><b>FACTURAS</b></th>
      <th align="center"><b>NOTAS DE VENTAS</b></th>
      <th align="center"><b>TOTAL</b></th>
    </tr>';
    foreach ($listas as $lista) {
        $boletas=$this->venta_model->montoTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>$this->input->post('ganuos',true),"month(femision)"=>$lista->id,"tcomprobante"=>'03',"tipo_estado<"=>'09'));
        $facturas=$this->venta_model->montoTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>$this->input->post('ganuos',true),"month(femision)"=>$lista->id,"tcomprobante"=>'01',"tipo_estado<"=>'09'));
        $notas=$this->nventa_model->montoTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>$this->input->post('ganuos',true),"month(femision)"=>$lista->id));
        $totales=$boletas->total+$facturas->total+$notas->total;
        $tblf .= '<tr>
            <td><b>'.$lista->descripcion.'</b></td>
            <td align="right">'.number_format($boletas->total, 2, '.', ',').'</td>
            <td align="right">'.number_format($facturas->total, 2, '.', ',').'</td>
            <td align="right">'.number_format($notas->total, 2, '.', ',').'</td>
            <td align="right">'.number_format($totales, 2, '.', ',').'</td>
        </tr>';
    }
$tblf .= '</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->Output();

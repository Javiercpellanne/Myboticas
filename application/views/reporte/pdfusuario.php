<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Consulta de Ventas');
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
        <td width="70%" align="center" height="48px"><br><br> VENTA POR USUARIOS DEL '.FormatoFecha($this->input->post('uinicio',true)).' AL '.FormatoFecha($this->input->post('ufin',true)).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',9);
$tblf = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
      <th align="center"><b>Usuario</b></th>
      <th align="center"><b>CPE</b></th>
      <th align="center"><b>Nota Venta</b></th>
      <th align="center"><b>Total</b></th>
    </tr>';
    $tcomprobantes=0; $tnventas=0; $tusuario=0;
    foreach ($listas as $lista) {
        $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"iduser"=>$lista->id,"nulo"=>0,"femision>="=>$this->input->post('uinicio',true),"femision<="=>$this->input->post('ufin',true));
        //venta comprobante
        $mventac=$this->venta_model->montoTotal($filtros);
        $totalcomprobante=$mventac->total;
        //venta
        $mventa=$this->nventa_model->montoTotal($filtros);
        $totalnventa=$mventa->total;
        $totalusuario=$totalcomprobante+$totalnventa;

        $tcomprobantes+=$totalcomprobante; $tnventas+=$totalnventa; $tusuario+=$totalusuario;
        $tblf .= '<tr>
            <td><b>'.$lista->nombres.'</b></td>
            <td align="right">'.formatoMonto($totalcomprobante??0).'</td>
            <td align="right">'.formatoMonto($totalnventa??0).'</td>
            <td align="right">'.formatoMonto($totalusuario).'</td>
        </tr>';
    }
    $tblf .= '<tr>
                <td align="right"><b>Totales</b></td>
                <td align="right"><b>'.formatoMonto($tcomprobantes).'</b></td>
                <td align="right"><b>'.formatoMonto($tnventas).'</b></td>
                <td align="right"><b>'.formatoMonto($tusuario).'</b></td>
            </tr>';
$tblf .= '</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',9);
$pdf->Cell(50,5,'Notas Creditos',0,0,'L');
$pdf->SetFont('helvetica','',9);
$pdf->Cell(20,5,$ncreditos->total,0,0,'L');
$pdf->Ln(2);

$pdf->Output();

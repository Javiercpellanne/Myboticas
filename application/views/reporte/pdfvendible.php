<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Productos mas Vendidos');
$pdf->SetMargins(13,10,13);

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
        <td width="70%" align="center" height="48px"><br><br> PRODUCTOS MAS VENDIDOS DESDE '.FormatoFecha($this->input->post('vinicio',true)).' AL '.FormatoFecha($this->input->post('vfin',true)).'</td>
        <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.$nestablecimiento->descripcion.'</b></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="65%"><strong>Descripcion</strong></th>
            <th align="center" width="10%"><strong>Stock</strong></th>
            <th align="center" width="10%"><strong>Cantidad</strong></th>
            <th align="center" width="10%"><strong>Total</strong></th>
        </tr>';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $general=0;
    foreach ($datos as $dato) {
    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->idproducto);
    $tbl .= '<tr>
        <td>'.$i.'</td>
        <td>'.$dato->descripcion.'</td>
        <td align="center">'.$cantidad->stock.'</td>
        <td align="center">'.$dato->cantidad.'</td>
        <td align="right">'.formatoPrecio($dato->importe).'</td>
    </tr>';
    $general+=$dato->importe;
    $i++;
    }
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

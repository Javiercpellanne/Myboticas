<?php
define("logo", $empresa->logo, false);
define("anexo", $nestablecimiento->descripcion, false);
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B',14);
        $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="15%" align="center" height="48px"><img src="'.logo.'" border="0" height="44px"/></td>
                <td width="70%" align="center" height="48px"><br><br>STOCK DE PRODUCTOS AL DIA '.FormatoFecha(date("Y-m-d")).'</td>
                <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.anexo.'</b></td>
            </tr>
        </table>';
        $this->writeHTML($tblc, false, false, false, false, '');
        $this->Ln(2);

        $this->SetFont('helvetica','B',8);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="3%"><strong>#</strong></th>
            <th align="center" width="52%"><strong>Descripcion</strong></th>
            <th align="center" width="30%"><strong>Lotes</strong></th>
            <th align="center" width="7%"><strong>Cant</strong></th>
            <th align="center" width="8%"><strong>P. Venta</strong></th>
        </tr>
        </table>';
        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'top', true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-7);
        // Set font
        $this->SetFont('helvetica', 'I', 7);        // Page number
        $this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 1, 'R', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Stock de Productos');
$pdf->SetMargins(8,31.5,8);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(9);
// set auto page breaks
$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');

$pdf->AddPage();
// contenido
$pdf->SetFont('helvetica','',8);
$tbl = '<table cellspacing="0" cellpadding="2" border="1">';
    $i=1;
    foreach ($datos as $dato) {
    $nproducto=$dato->descripcion;
    if ($dato->nlaboratorio!='') {$nproducto.=' ['.$dato->nlaboratorio.']';}
    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->id);
    $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $dato->pventa;

    $lotes=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$dato->id);
    $lotesProducto='';
    $n=1;
    $salto='';
    foreach ($lotes as $lote) {
        if ($n>1) {
            $lotesProducto.='<br>'.'Nº: '.$lote->nlote.' - Vcto: '.FormatoFecha($lote->fvencimiento).' ('.$lote->stock.')';
        }else{
            $lotesProducto.='Nº: '.$lote->nlote.' - Vcto: '.FormatoFecha($lote->fvencimiento).' ('.$lote->stock.')';
        }
        $n++;
    }
    $tbl .= '<tr>
        <td width="3%">'.$i.'</td>
        <td width="52%">'.$nproducto.'</td>
        <td width="30%">'.$lotesProducto.'</td>
        <td width="7%" align="center">'.$cantidad->stock.'</td>
        <td width="8%" align="right">'.$pventa.'</td>
    </tr>';
    $i++;
    }
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

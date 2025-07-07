<?php
define("logo", $empresa->logo, false);
define("titulo", 'PRODUCTOS VENTAS Y STOCK DESDE '.FormatoFecha($inicio).' HASTA '.FormatoFecha($fin), false);
define("anexos", json_decode(json_encode($establecimientos),true), false);
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B',14);
        $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="20%" align="center" height="48px"><img src="'.logo.'" border="0" height="44px"/></td>
                <td width="80%" align="center" height="48px"><br><br>'.titulo.'</td>
            </tr>
        </table>';
        $this->writeHTML($tblc, false, false, false, false, '');
        $this->Ln(2);

        $ancho=50/((int)count(anexos)*2+2);
        $this->SetFont('helvetica','B',9);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="4%"><b>#</b></th>
            <th align="center" width="46%"><b>Descripcion</b></th>';
        $html .= '<th align="center" width="'.$ancho.'%"><b>Cantidad Vendida</b></th>';
        foreach (anexos as $anexo) {
            $html .= '<th align="center" width="'.$ancho.'%"><b>'.$anexo['descripcion'].'</b></th>';
        }
        $html .= '<th align="center" width="'.$ancho.'%"><b>Stock Actual</b></th>';
        foreach (anexos as $anexo) {
            $html .= '<th align="center" width="'.$ancho.'%"><b>'.$anexo['descripcion'].'</b></th>';
        }
        $html .= '</tr>
        </table>';
        $this->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, 'top', true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-6);
        // Set font
        $this->SetFont('helvetica', 'I', 7);        // Page number
        $this->Cell(0, 5, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, 1, 'R');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Producto por Vencer');
$pdf->SetMargins(8,31.5,8);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(9);
// set auto page breaks
$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');
$pdf->AddPage('L');

$ancho=50/((int)count($establecimientos)*2+2);
$pdf->SetFont('helvetica','',8);
$tbl = '<table cellspacing="0" cellpadding="2" border="1">';
$i=1;
foreach ($listas as $dato) {
    $nproducto=$dato->descripcion;
    if ($dato->nlaboratorio!='') {$nproducto.=' ['.$dato->nlaboratorio.']';}
    $tbl .= '<tr>
            <td width="4%">'.$i.'</td>
            <td width="46%">'.$nproducto.'</td>';

    $cantidadn=$this->nventa_model->productoTotal(array("v.femision>="=>$inicio,"v.femision<="=>$fin,"v.nulo"=>0,"d.idproducto"=>$dato->id));
    $cantidadv=$this->venta_model->productoTotal(array("v.femision>="=>$inicio,"v.femision<="=>$fin,"v.nulo"=>0,"d.idproducto"=>$dato->id));
    $vendidos=$cantidadn->calmacen+$cantidadv->calmacen;
    $tbl .= '<td align="center" width="'.$ancho.'%"><b>'.$vendidos.'</b></td>';
    foreach ($establecimientos as $anexo) {
        $cantidadn=$this->nventa_model->productoTotal(array("v.idestablecimiento"=>$anexo->id,"v.femision>="=>$inicio,"v.femision<="=>$fin,"v.nulo"=>0,"d.idproducto"=>$dato->id));
        $cantidadv=$this->venta_model->productoTotal(array("v.idestablecimiento"=>$anexo->id,"v.femision>="=>$inicio,"v.femision<="=>$fin,"v.nulo"=>0,"d.idproducto"=>$dato->id));
        $cvendidos=$cantidadn->calmacen+$cantidadv->calmacen;
        $tbl .= '<td align="right" width="'.$ancho.'%">'.$cvendidos.'</td>';
    }

    $actual=$this->inventario_model->cantidadTotal(array("idproducto"=>$dato->id));
    $tbl .= '<td align="center" width="'.$ancho.'%"><b>'.$actual->stock.'</b></td>';
    foreach ($establecimientos as $anexo) {
        $cantidad=$this->inventario_model->mostrar($anexo->id,$dato->id);
        $tbl .= '<td align="right" width="'.$ancho.'%">'.$cantidad->stock.'</td>';
    }
    $tbl .= '</tr>';
    $i++;
}
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');

$pdf->Output();

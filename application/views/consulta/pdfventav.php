<?php
define("logo", $empresa->logo, false);
define("fechas", $inicio.' AL '.$fin, false);
define("anexo", $nestablecimiento->descripcion, false);
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B',14);
        $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="15%" align="center" height="48px"><img src="'.logo.'" border="0" height="44px"/></td>
                <td width="70%" align="center" height="48px"><br><br>VENTAS VALORIZADAS DEL '.fechas.'</td>
                <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.anexo.'</b></td>
            </tr>
        </table>';
        $this->writeHTML($tblc, false, false, false, false, '');
        $this->Ln(2);

        $this->SetFont('helvetica','B',9);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="45%"><strong>Producto</strong></th>
            <th align="center" width="9%"><strong>Cant</strong></th>
            <th align="center" width="7%"><strong>(Dscto)</strong></th>
            <th align="center" width="9%"><strong>Ventas</strong></th>
            <th align="center" width="9%"><strong>Costo P.</strong></th>
            <th align="center" width="8%"><strong>Utilidad</strong></th>
            <th align="center" width="8%"><strong>(%)</strong></th>
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
$pdf->SetTitle('Ventas Valorizadas');
$pdf->SetMargins(8,31.5,8);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(9);
// set auto page breaks
$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');
$pdf->AddPage();
$pdf->SetFont('helvetica','B',9);
$tbl = '<table cellspacing="0" cellpadding="2" border="1">';
    $pdf->SetFont('helvetica','',8);
    $i=1;
    $tcompra=0;
    $tventa=0;
    $tutilidad=0;
    foreach ($listas as $lista) {
    $notas=$this->nota_model->ganancia(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin,"nulo"=>0,"idproducto"=>$lista->idproducto));
    $cantidad=$lista->cantidad-$notas->cantidad;
    $venta=$lista->importe-$notas->importe;

    $compra=$lista->costo-$notas->costo;//
    $utilidad=$venta-$compra;
    $margen=gananciav($venta,$compra,1);
    $tbl .= '<tr>
        <td width="5%">'.$i.'</td>
        <td width="45%">'.$lista->descripcion.'</td>
        <td align="right" width="9%">'.$cantidad.'</td>
        <td align="right" width="7%">'.round($lista->dscto,2).'</td>
        <td align="right" width="9%">'.formatoPrecio($venta).'</td>
        <td align="right" width="9%">'.formatoPrecio($compra).'</td>
        <td align="right" width="8%">'.formatoPrecio($utilidad).'</td>
        <td align="right" width="8%">'.formatoPrecio($margen).'</td>
    </tr>';
    $tcompra+=$compra;
    $tventa+=$venta;
    $tutilidad+=$utilidad;
    $i++;
    }
$tbl .= '<tr>
            <td align="right" width="66%">Totales</td>
            <td align="right" width="9%">'.formatoPrecio($tventa).'</td>
            <td align="right" width="9%">'.formatoPrecio($tcompra).'</td>
            <td align="right" width="8%">'.formatoPrecio($tutilidad).'</td>
            <td align="right" width="8%"></td>
            </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

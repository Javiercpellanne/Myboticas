<?php
define("logo", $empresa->logo, false);
define("fechas", $inicio.' AL '.$fin, false);
define("anexo", $nestablecimiento->descripcion, false);
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B',13);
        $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="15%" align="center" height="48px"><img src="'.logo.'" border="0" height="44px"/></td>
                <td width="70%" align="center" height="48px"><br><br>EGRESOS DIVERSOS DEL '.fechas.'</td>
                <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.anexo.'</b></td>
            </tr>
        </table>';
        $this->writeHTML($tblc, false, false, false, false, '');
        $this->Ln(2);

        $this->SetFont('helvetica','B',8);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="9%"><strong>Fecha</strong></th>
            <th align="center" width="9%"><strong>Numero</strong></th>
            <th align="center" width="39%"><strong>Motivo</strong></th>
            <th align="center" width="7%"><strong>Importe</strong></th>
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
$pdf->SetTitle('Egresos Diversos');
$pdf->SetMargins(8,31.5,8);
$pdf->SetHeaderMargin(8);
$pdf->SetFooterMargin(9);
// set auto page breaks
$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');
$pdf->AddPage();
$pdf->SetFont('helvetica','',7.5);
$tbl = '<table cellspacing="0" cellpadding="2" border="1">';
    $i=1;
    $totales=0;
    foreach ($listas as $lista) {
    $tbl .= '<tr>
            <td width="5%">'.$i.'</td>
            <td width="9%">'.$lista->femision.'</td>
            <td width="9%">'.$lista->numero.'</td>
            <td width="39%">'.$lista->motivo.'</td>
            <td align="right" width="7%">'.$lista->total.'</td>
        </tr>';
        $totales+=$lista->total;
    $i++;
    }
$tbl .= '<tr>
            <td align="right" colspan="5">Totales</td>
            <td align="right">'.formatoPrecio($totales).'</td>
        </tr>';
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

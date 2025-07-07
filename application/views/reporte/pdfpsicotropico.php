<?php
define("logo", $empresa->logo, false);
define("anexo", $nestablecimiento->descripcion, false);
define("producto", $datos->descripcion.'<br> '.$meses->descripcion.' DEL '.$fecha[0], false);
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B',12);
        $tblc = '<table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="15%" align="center" height="48px"><img src="'.logo.'" border="0" height="44px"/></td>
                <td width="70%" align="center" height="48px">PRODUCTOS PSICOTROPICOS <br>'.producto.'</td>
                <td width="15%" align="center" height="48px"><b style="font-size: 0.7 em;">'.anexo.'</b></td>
            </tr>
        </table>';
        $this->writeHTML($tblc, false, false, false, false, '');
        $this->Ln(2);

        $this->SetFont('helvetica','B',9);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="10%"><strong>Fecha</strong></th>
            <th align="center" width="65%"><strong>Descripcion</strong></th>
            <th align="center" width="10%"><strong>Debe</strong></th>
            <th align="center" width="10%"><strong>Haber</strong></th>
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
$pdf->SetTitle('Productos Psicotropicos');
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
    foreach ($listas as $lista) {
    if ($lista->tipo=='I') {
        $descripcion=$lista->detalle;
        $debe=$lista->calmacen;
        $haber='';
    }else{
        $nombre=json_decode($lista->detalle);
        $descripcion='Pac. '.$nombre->paciente.' / Dr. '.$nombre->doctor.' / RUE '.$nombre->colegiatura;
        $debe='';
        $haber=$lista->calmacen;
    }
    $tbl .= '<tr>
            <td width="5%">'.$i.'</td>
            <td width="10%" align="center">'.$lista->femision.'</td>
            <td width="65%">'.$descripcion.'</td>
            <td width="10%" align="center">'.$debe.'</td>
            <td width="10%" align="center">'.$haber.'</td>
            </tr>';
    $i++;
    }
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$pdf->Output();

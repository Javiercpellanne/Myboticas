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

        $this->SetFont('helvetica','B',9);
        $html = '<table cellspacing="0" cellpadding="1" border="1">
        <tr>
            <th align="center" width="5%"><strong>#</strong></th>
            <th align="center" width="70%"><strong>Descripcion</strong></th>
            <th align="center" width="7%"><strong>Cant</strong></th>
            <th align="center" width="9%"><strong>P. Compra</strong></th>
            <th align="center" width="9%"><strong>P. Venta</strong></th>
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

// Paginación
$limit = 200; // Ajustar según la capacidad del servidor
$offset = 0;

do {
    $datos = $this->inventario_model->mostrarStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tipo" => 'B', "estado" => 1), $limit, $offset);
    if (count($datos) > 0) {
        // Agregar primera página
        $pdf->AddPage();

        // Configuración de la tabla HTML
        $pdf->SetFont('helvetica', '', 8);
        $html = '<table cellspacing="0" cellpadding="2" border="1">';
        foreach ($datos as $dato) {
            $nproducto = $dato->descripcion;
            if ($dato->nlaboratorio != '') {
                $nproducto .= ' [' . $dato->nlaboratorio . ']';
            }

            $html .= '<tr>
                        <td width="5%">' . $dato->id . '</td>
                        <td width="70%">' . $nproducto . '</td>
                        <td width="7%" align="center">'. $dato->stock.'</td>
                        <td width="9%" align="right">' . $dato->pcompra . '</td>
                        <td width="9%" align="right"></td>
                    </tr>';

            if ($pdf->getY() + 10 > $pdf->getPageHeight()) {
                $pdf->writeHTML($html, true, false, false, false, '');
                $pdf->AddPage();
                $pdf->SetFont('helvetica', '', 8);
                $html = '<table cellspacing="0" cellpadding="2" border="1">
                        <tr>
                            <th align="center" width="5%"><strong>#</strong></th>
                            <th align="center" width="70%"><strong>Descripcion</strong></th>
                            <th align="center" width="7%"><strong>Cant</strong></th>
                            <th align="center" width="9%"><strong>P. Compra</strong></th>
                            <th align="center" width="9%"><strong>P. Venta</strong></th>
                        </tr>';
            }
        }

        $html .= '</table>';
        $pdf->writeHTML($html, true, false, false, false, '');

        // Incrementar offset
        $offset += $limit;
    } else {
        break;
    }
} while (count($datos) > 0);

$pdf->Output();

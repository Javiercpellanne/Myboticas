<?php
$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Guia de Remision');
$pdf->SetMargins(13,18,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// contenido
$pdf->SetFont('helvetica', '',9.5);
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="25%" align="center"><img src="'.$empresa->logo.'" border="0" height="70"/></td>
            <td width="45%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
            <td width="30%" align="center" style="border: 1px solid #000;"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.2 em;">'.str_replace('Remisión', 'Remisión Electrónica',$datos->ncomprobante).'<br>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</b></td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',8);
$tble = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td colspan="4" style="border: 1px solid #000;"><strong>DATOS DEL ENVIO</strong></td>
        </tr>
        <tr>
            <td width="25%" style="border-left: 1px solid #000;"><strong>Fecha Emision :</strong></td>
            <td width="30%">'.$datos->femision.'</td>
            <td width="25%"><strong>Fecha Inicio de Traslado :</strong></td>
            <td width="20%" style="border-right: 1px solid #000;">'.$datos->fenvio.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000;"><strong>Motivo de traslado :</strong></td>
            <td>'.$datos->motivot.'</td>
            <td><strong>Modalidad de transporte :</strong></td>
            <td style="border-right: 1px solid #000;">'.$datos->nmodot.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000; border-bottom: 1px solid #000;"><strong>Peso Bruto Total (KGM) :</strong></td>
            <td style="border-bottom: 1px solid #000;">'.$datos->peso_total.'</td>
            <td style="border-bottom: 1px solid #000;"><strong>Número de Paquetes :</strong></td>
            <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;">'.$datos->paquetes.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tble, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',8);
$tbld = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td colspan="2" style="border: 1px solid #000;"><strong>DATOS DEL DESTINATARIO</strong></td>
        </tr>
        <tr>
            <td width="25%" style="border-left: 1px solid #000;"><strong>Razón Social :</strong></td>
            <td width="75%" style="border-right: 1px solid #000;">'.$clientes->nombres.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000; border-bottom: 1px solid #000;"><strong>RUC :</strong></td>
            <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;">'.$clientes->documento.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tbld, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('helvetica','',8);
$tblp = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td colspan="2" style="border: 1px solid #000;"><strong>DATOS DEL PUNTO DE PARTIDA Y PUNTO DE LLEGADA</strong></td>
        </tr>
        <tr>
            <td width="27%" style="border-left: 1px solid #000;"><strong>Dirección del punto de partida :</strong></td>
            <td width="73%" style="border-right: 1px solid #000;">'.$datos->direccion_origen.' -- '.$pdistritos->descripcion.' - '.$pprovincias->descripcion.' - '.$pdepartamentos->descripcion.'</td>
        </tr>
        <tr>
            <td style="border-left: 1px solid #000; border-bottom: 1px solid #000;"><strong>Dirección del punto de llegada :</strong></td>
            <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;">'.$datos->direccion_entrega.' -- '.$edistritos->descripcion.' - '.$eprovincias->descripcion.' - '.$edepartamentos->descripcion.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblp, false, false, false, false, '');
$pdf->Ln(2);

if ($datos->m1l==0) {
    $pdf->SetFont('helvetica','',8);
    $tble = '<table cellspacing="0" cellpadding="2" border="0">
            <tr>
                <td colspan="4" style="border: 1px solid #000;"><strong>DATOS DEL TRANSPORTE</strong></td>
            </tr>';

    if ($datos->idttransporte=='01') {
    $tble .= '<tr>
                <td style="border-left: 1px solid #000;"><strong>Razón Social :</strong></td>
                <td style="border-right: 1px solid #000;" colspan="3">'.$datos->nombres_transporte.'</td>
            </tr>
            <tr>
                <td style="border-left: 1px solid #000; border-bottom: 1px solid #000;"><strong>RUC :</strong></td>
                <td style="border-right: 1px solid #000; border-bottom: 1px solid #000;" colspan="3">'.$datos->ndocumento_transporte.'</td>
            </tr>';
    } else {
    $tble .= '<tr>
                <td style="border-left: 1px solid #000;"><b>Conductor :</b></td>
                <td style="border-right: 1px solid #000;" colspan="3">'.$datos->ndocumento_transporte.' '.$datos->nombres_transporte.'</td>
            </tr>
            <tr>
                <td width="25%" style="border-left: 1px solid #000; border-bottom: 1px solid #000;"><b>Número de placa del vehículo :</b></td>
                <td width="25%" style="border-bottom: 1px solid #000;">'.$datos->placa.'</td>
                <td width="30%" style="border-bottom: 1px solid #000;"><b>Licencia del conductor :</b></td>
                <td width="20%" style="border-right: 1px solid #000; border-bottom: 1px solid #000;">'.$datos->licencia_conducir.'</td>
            </tr>';
    }
    $tble .= '</table>';
    $pdf->writeHTML($tble, false, false, false, false, '');
    $pdf->Ln(2);
}else{
    $pdf->SetFont('helvetica','',8);
    $pdf->Cell(0,3,'Indicador de traslado en vehículos de categoría M1 o L: Sí',0,1,'L');
    $pdf->Ln(2);
}

$pdf->SetFont('helvetica','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="5%" style="border: 1px solid #000;"><strong>Nro</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Codigo</strong></th>
            <th align="center" width="65%" style="border: 1px solid #000;"><strong>Descripcion</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Unidad</strong></th>
            <th align="center" width="10%" style="border: 1px solid #000;"><strong>Cantidad</strong></th>
        </tr>';
	$i=1;
	$pdf->SetFont('helvetica','',8);

	foreach ($detalles as $detalle) {
    $lotes='';
    if ($detalle->lote!='') {
        $lotesn=explode("|",$detalle->lote);
        $lotesf=explode("|",$detalle->fvencimiento);

        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    $tbl .= '<tr>
            <td style="border-left: 1px solid #000;">'.$i.'</td>
            <td>'.$detalle->idproducto.'</td>
            <td>'.$detalle->descripcion.$lotes.'</td>
            <td>'.$detalle->unidad.'</td>
            <td style="border-right: 1px solid #000;" align="right">'.$detalle->cantidad.'</td>
            </tr>';
    $i++;
	}
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());
$pdf->Ln(2);

$pdf->SetFont('helvetica','',8);
$tblp = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td style="border: 1px solid #000;"><strong>Observaciones</strong></td>
        </tr>
        <tr>
            <td style="border: 1px solid #000;">'.$datos->observaciones.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblp, false, false, false, false, '');
$pdf->Ln(2);

if ($datos->qr!='') {
    $style = array(
        'border' => 0,
        'vpadding' => 'auto',
        'hpadding' => 'auto',
        'fgcolor' => array(0,0,0),
        'bgcolor' => false, //array(255,255,255)
        'module_width' => 1, // width of a single module in points
        'module_height' => 1 // height of a single module in points
    );
    // QRCODE,Q : QR-CODE Better error correction
    $pdf->write2DBarcode($datos->qr, 'QRCODE,Q', $pdf->GetX()+70,$pdf->GetY(), 35, 35, $style, 'N');
}

//$pdf->Output();
$archivo='./downloads/pdf/'.$datos->filename.'.pdf';
//$pdf->Output($archivo,'F');

$pdf_string = $pdf->Output('pseudo.pdf', 'S');
file_put_contents($archivo, $pdf_string);

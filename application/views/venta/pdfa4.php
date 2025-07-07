<?php
if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(13,15,13);

$pdf->SetAutoPageBreak(true,9.5);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();
// contenido
$pdf->SetLineStyle(array('width' => 0.8, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(1, 1, 1)));
// Establecer el color de relleno a blanco
$pdf->SetFillColor(255, 255, 255);
// Dibujar un rectángulo con bordes redondeados
$pdf->RoundedRect(133.3, 15.2, 63, 33, 4, '1234', 'DF');
$pdf->SetFont('dejavusanscondensed', '',11);
$logo = $nestablecimiento->logoe!='' ? $nestablecimiento->logoe: $empresa->logo;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="30%" align="center"><img src="'.$logo.'" border="0" height="90"/></td>
        <td width="35%" align="center"><span style="font-size: 0.8 em;">'.$empresa->nombres.'</span><br><span style="font-size: 0.6 em;">';
        // $tblc .= '<b>PRINCIPAL:</b> '.$pestablecimiento->direccion.'<br>'.$pestablecimiento->ndepartamento.'-'.$pestablecimiento->nprovincia.'-'.$pestablecimiento->ndistrito.'<br>'.'TELF '.$pestablecimiento->telefono.'';
        //if ($this->session->userdata("predeterminado")!=1) {
        $tblc .= ''.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'';
        //}
        $tblc .= '</span></td>
        <td width="35%" align="center"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.5 em;">'.$datos->ncomprobante.' ELECTRÓNICA</b><br><b>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</b><br></td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','',8.5);
$tblc = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="15%" style="border-top: 1px solid #000; border-left: 1px solid #000;"><b>Señor(es) : </b></td>
        <td width="58%" style="border-top: 1px solid #000;">'.$cliente->nombres.$ncomercial.'</td>
        <td width="15%" style="border-top: 1px solid #000;"><b>Telefono :</b></td>
        <td width="12%" style="border-top: 1px solid #000; border-right: 1px solid #000;">'.$cliente->telefono.'</td>
    </tr>
    <tr>
        <td width="15%" style="border-left: 1px solid #000;"><b>Direccion :</b></td>
        <td width="85%" style="border-right: 1px solid #000;">'.$cliente->direccion.$direccion.'</td>
    </tr>
    <tr>
        <td width="15%" style="border-bottom: 1px solid #000; border-left: 1px solid #000;"><b>'.$cliente->descripcion.' :</b></td>
        <td width="50%" style="border-bottom: 1px solid #000;">'.$cliente->documento.'</td>
        <td width="23%" style="border-bottom: 1px solid #000;"><b>Condiciones Pago :</b></td>
        <td width="12%" style="border-bottom: 1px solid #000; border-right: 1px solid #000;">'.$tpago.'</td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$tblc = '<table cellspacing="0" cellpadding="2" border="1">
    <tr>
        <td align="center"><b>Fecha Emision</b></td>
        <td align="center"><b>Fecha Vencimiento</b></td>
        <td align="center"><b>Vendedor</b></td>
        <td align="center"><b>Orden Compra</b></td>
    </tr>
    <tr>
        <td align="center">'.$datos->femision.'</td>
        <td align="center">'.$datos->fvencimiento.'</td>
        <td align="center">'.($vendedor->nombres??'').'</td>
        <td align="center">'.$datos->ocompra.'</td>
    </tr>
</table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <th align="center" width="8%" style="border: 1px solid #000;"><b>CANT</b></th>
        <th align="center" width="8%" style="border: 1px solid #000;"><b>UNID</b></th>
        <th align="center" width="60%" style="border: 1px solid #000;"><b>DESCRIPCION</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>P.UNIT</b></th>
        <th align="center" width="12%" style="border: 1px solid #000;"><b>TOTAL</b></th>
    </tr>';
	$gdscto=0;
	$pdf->SetFont('dejavusanscondensed','',8.5);
	foreach ($detalles as $detalle) {
        $lotes='';
        if ($detalle->descuentos!='') {
            $descuentos=json_decode($detalle->descuentos);

            $mdscto=$descuentos->monto;
            $gdscto+=floatval($descuentos->monto);
            $ddscto= ' ('.($descuentos->factor*100).' % Descuento)';
        } else {
            $mdscto='';
            $ddscto='';
        }
        if ($detalle->lote!='' && $datos->lote==1) {
            $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
        }
        $tbl .= '<tr>
            <td style="border-left: 1px solid #000;">'.$detalle->cantidad.'</td>
            <td style="border-left: 1px solid #000;" align="center">'.$detalle->unidad.'</td>
            <td style="border-left: 1px solid #000;">'.nl2br($detalle->descripcion).$ddscto.$lotes.'</td>
            <td style="border-left: 1px solid #000;" align="right">'.$detalle->precio.'</td>
            <td style="border-left: 1px solid #000; border-right: 1px solid #000;" align="right">'.$detalle->importe.'</td>
        </tr>';
	}
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(13, $pdf->getY(), $pdf->getPageWidth()-13, $pdf->getY());

$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
    if ($datos->descuentos!='') {
    $descuentos=json_decode($datos->descuentos);
    $tblt .= '<tr>
        <td width="88%" align="right"><b>DESCUENTO GLOBAL : S/.</b></td>
        <td width="12%" align="right">'.formatoPrecio($descuentos->monto+($descuentos->monto*0.18)).'</td>
        </tr>';
    }

    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><b>OP GRATUITAS : S/.</b></td>
        <td width="12%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

	if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><b>OP GRAVADAS : S/.</b></td>
        <td width="12%" align="right">'.$datos->tgravado.'</td>
        </tr>';
    }

    if ($datos->tinafecto>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><b>OP INAFECTAS : S/.</b></td>
        <td width="12%" align="right">'.$datos->tinafecto.'</td>
        </tr>';
    }

    if ($datos->texonerado>0) {
    $tblt .= '<tr>
        <td width="88%" align="right"><b>OP EXONERADAS : S/.</b></td>
        <td width="12%" align="right">'.$datos->texonerado.'</td>
        </tr>';
    }
    $tblt .= '<tr>
        <td width="88%" align="right"><b>IGV : S/.</b></td>
        <td width="12%" align="right">'.$datos->tigv.'</td>
        </tr>';
	$tblt .= '<tr>
        <td width="88%" align="right"><b>TOTAL : S/.</b></td>
        <td width="12%" align="right">'.$datos->total.'</td>
        </tr>';
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

if ($datos->dadicional!='') {
$pdf->SetFont('dejavusanscondensed','B',8.5);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',8.5);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(1);
}

$pdf->SetFont('dejavusanscondensed','B',8.5);
$pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
$pdf->Ln(1);

if ($datos->retencion!='') {
    $retencion=json_decode($datos->retencion);

    $pdf->SetFont('dejavusanscondensed','B',8);
    $pdf->Cell(0,4,'Informacion de la retencion : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',8);
    $tblr = '<table cellspacing="0" cellpadding="1" border="0">';
        $tblr .= '<tr>
            <td width="17%">Base de la retencion</td>
            <td width="9%" align="center">'.monedaSimbolo($datos->moneda).$retencion->base.'</td>
            <td width="17%">Porcentaje de retencion</td>
            <td width="9%" align="center">'.($retencion->factor*100).'%</td>
            <td width="17%">Monto de la retencion</td>
            <td width="9%" align="center">'.monedaSimbolo($datos->moneda).$retencion->monto.'</td>
            <td width="13%">Monto a pagar</td>
            <td width="9%" align="right">'.monedaSimbolo($datos->moneda).($datos->total-$retencion->monto).'</td>
        </tr>';
    $tblr .= '</table>';
    $pdf->writeHTML($tblr, true, false, false, false, '');
}

if ($datos->detraccion!='') {
    $detraccion=json_decode($datos->detraccion);
    $codigo=$this->tdetraccion_model->mostrar($detraccion->codigo);
    $medio=$this->tmedio_model->mostrar($detraccion->medio);

    $pdf->SetFont('dejavusanscondensed','B',8);
    $pdf->Cell(0,4,'Informacion de la detraccion : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',8);
    $tblr = '<table cellspacing="0" cellpadding="1" border="0">';
        $tblr .= '<tr>
            <td width="25%">Leyenda:</td>
            <td width="75%">Operación sujeta al Sistema de Pago de Obligaciones Tributarias con el Gobierno Central</td>
        </tr>
        <tr>
            <td width="25%">Bien o Servicio:</td>
            <td width="75%">'.$detraccion->codigo.' '.$codigo->descripcion.'</td>
        </tr>
        <tr>
            <td width="25%">Medio Pago:</td>
            <td width="75%">'.$detraccion->medio.' '.$medio->descripcion.'</td>
        </tr>
        <tr>
            <td width="25%">Nro Cta Banco de la Nacion:</td>
            <td width="15%">'.$detraccion->ncuenta.'</td>
            <td width="25%">Porcentaje de detraccion:</td>
            <td width="9%">'.$detraccion->factor.'</td>
            <td width="17%">Monto detraccion:</td>
            <td width="9%">'.$detraccion->monto.'</td>
        </tr>';
    $tblr .= '</table>';
    $pdf->writeHTML($tblr, true, false, false, false, '');
}

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',7);
    $pdf->Cell(0,3,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',7);
    foreach ($cobros as $cobro) {
        $pdf->Cell(0,3,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','',7);
    $feinicial=$datos->fpago;
    for ($i=1; $i <= $datos->cuotas ; $i++) {
      if ($i == $datos->cuotas) {
          $cuota = $datos->total - ($datos->mcuota * ($datos->cuotas - 1));;
      } else {
          $cuota = $datos->mcuota;
      }
      $pdf->Cell(0,3,'• Cuota '.zerofill($i,3).' /  Fecha: '.$feinicial.' / Monto: S/ '.$cuota,0,1,'L');
      $suma=tiempoCuota($datos->pcuota);
      $feinicial=SumarFecha($suma,$feinicial);
    }
}
$pdf->Ln(1);

$x = $pdf->GetX();
$y = $pdf->GetY();
$pdf->SetFont('dejavusanscondensed','',7.5);
$tblq = '<table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td width="81%" height="98px">'.nl2br($empresa->piec).'</td>
        <td width="19%"></td>
    </tr>
</table>';
$pdf->writeHTML($tblq, true, false, false, false, '');

// Ajuste de la posición según sea necesario
$x += 149; // Ajusta según la posición de tu tabla
$y += 1;  // Ajusta según la posición de tu tabla

$qrcode = $empresa->ruc."|".$datos->tcomprobante."|".$datos->serie."|".$datos->numero."|".$datos->tigv."|".$datos->total."|".$datos->femision."|".$cliente->tdocumento."|".$cliente->documento."|".$datos->hash;

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
$pdf->write2DBarcode($qrcode, 'QRCODE,Q', $x, $y, 35, 35, $style, 'N');

// if ($datos->hash!='') {
//     $pdf->SetFont('dejavusanscondensed','',7);
//     $pdf->Cell(0,3,'Codigo Hash : '.$datos->hash,0,1,'C');
// }
$pdf->Ln(2);

if (!empty($tipo)) {
    $archivo='./downloads/pdf/'.$datos->filename.'.pdf';
    $pdf_string = $pdf->Output('pseudo.pdf', 'S');
    file_put_contents($archivo, $pdf_string);
} else {
    $pdf->Output();
}

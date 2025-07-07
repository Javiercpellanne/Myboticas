<?php
$altura=$detalles!=null ? 250+(count($detalles)*5) : 250;
$tamaño=array(80,$altura);

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(4,1,4);

$pdf->SetAutoPageBreak(false);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("P",$tamaño);

// contenido
$pdf->SetFont('dejavusanscondensed', 'B', 8);
$logo = $nestablecimiento->logot!='' ? $nestablecimiento->logot: $empresa->lticket;
if ($logo!='') {
	$tblc = '<table cellspacing="0" cellpadding="0" border="0">
	        <tr>
	            <td align="center"><img src="'.$logo.'" border="0" height="55" /></td>
	        </tr>
	        </table>';
	$pdf->writeHTML($tblc, false, false, false, false, '');
	$pdf->Ln(2);
}

// $pdf->MultiCell(0,3,$empresa->ncomercial,0,'C');
// $pdf->SetFont('dejavusanscondensed','',5);
$pdf->MultiCell(0,3,$empresa->nombres,0,'C');
$pdf->Ln(1);

$pdf->SetFont('dejavusanscondensed','',9);
$pdf->Cell(0,3,'RUC '.$empresa->ruc,0,1,'C');
$pdf->MultiCell(0,3,$nestablecimiento->direccion,0,'C');
$pdf->MultiCell(0,3,$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito,0,'C');
$pdf->Cell(0,3,'TELF '.$nestablecimiento->telefono,0,1,'C');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',11);
$pdf->Cell(0,4,$datos->ncomprobante.' ELECTRÓNICA',0,1,'C');
$pdf->Cell(0,4,$datos->serie.'-'.zerofill($datos->numero, 8),0,1,'C');
$pdf->Ln(2);

$anchol=$cliente->tdocumento==0 ? '50': '26';
$anchoc=$cliente->tdocumento==0 ? '50': '74';
$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
    <tr>
        <td width="'.$anchol.'%"><b>F. Emision :</b></td>
        <td width="'.$anchoc.'%">'.$datos->femision.' '.$datos->hemision.'</td>
    </tr>
    <tr>
        <td><b>Cliente :</b></td>
        <td>'.$cliente->nombres.$ncomercial.'</td>
    </tr>
    <tr>
        <td><b>'.$cliente->descripcion.' :</b></td>
        <td>'.$cliente->documento.'</td>
    </tr>
    <tr>
        <td><b>Direccion :</b></td>
        <td>'.$cliente->direccion.$direccion.'</td>
    </tr>';
    if ($datos->ocompra!='') {
    $tblf .= '<tr>
        <td><b>Orden Compra :</b></td>
        <td>'.$datos->ocompra.'</td>
    </tr>';
    }
$tblf .= '</table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',8);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
    <tr>
        <td align="center" width="55%" style="border: 1px solid #000;"><b>DESCRIPCION</b></td>
        <td align="center" width="13%" style="border: 1px solid #000;"><b>CANT</b></td>
        <td align="center" width="15%" style="border: 1px solid #000;"><b>P.UNIT</b></td>
        <td align="center" width="17%" style="border: 1px solid #000;"><b>IMP.</b></td>
    </tr>';
	$gdscto=0;
	$pdf->SetFont('dejavusanscondensed','',7);
	foreach ($detalles as $detalle) {
        $lotes='';
        if ($detalle->descuentos!='') {
            $descuentos=json_decode($detalle->descuentos);

            $gdscto+=floatval($descuentos->monto);
            $ddscto= ' ('.($descuentos->factor*100).' % Descuento)';
        } else {
            $ddscto='';
        }
        if ($detalle->lote!='' && $datos->lote==1) {
            $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
        }
        $tbl .= '<tr>
                <td colspan="4">'.nl2br($detalle->descripcion).$ddscto.$lotes.'</td>
            </tr>';
        $tbl .= '<tr>
                <td align="right">'.$detalle->unidad.'</td>
                <td>'.$detalle->cantidad.'</td>
                <td align="right">'.$detalle->precio.'</td>
                <td align="right">'.$detalle->importe.'</td>
            </tr>';
	}
$tbl .= '</table>';

$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(1, $pdf->getY(), $pdf->getPageWidth()-1, $pdf->getY());

$pdf->SetFont('dejavusanscondensed','',7.5);
$tblt = '<table cellspacing="0" cellpadding="1" border="0">';
    if ($datos->descuentos!='') {
    $descuentos=json_decode($datos->descuentos);
    $tblt .= '<tr>
        <td width="83%" align="right"><b>DESCUENTO GLOBAL : S/.</b></td>
        <td width="17%" align="right">'.formatoPrecio($descuentos->monto+($descuentos->monto*0.18)).'</td>
        </tr>';
    }

    if ($datos->tgratuito>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><b>OP GRATUITAS : S/.</b></td>
        <td width="17%" align="right">'.$datos->tgratuito.'</td>
        </tr>';
    }

	if ($datos->tgravado>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><b>OP GRAVADAS : S/.</b></td>
        <td width="17%" align="right">'.$datos->tgravado.'</td>
        </tr>';
    }

    if ($datos->tinafecto>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><b>OP INAFECTAS : S/.</b></td>
        <td width="17%" align="right">'.$datos->tinafecto.'</td>
        </tr>';
    }

    if ($datos->texonerado>0) {
    $tblt .= '<tr>
        <td width="83%" align="right"><b>OP EXONERADAS : S/.</b></td>
        <td width="17%" align="right">'.$datos->texonerado.'</td>
        </tr>';
    }
    $tblt .= '<tr>
        <td width="83%" align="right"><b>IGV 18% : S/.</b></td>
        <td width="17%" align="right">'.$datos->tigv.'</td>
        </tr>';
	$tblt .= '<tr>
        <td width="83%" align="right"><b>TOTAL : S/.</b></td>
        <td width="17%" align="right">'.$datos->total.'</td>
        </tr>';
    if ($datos->izipay!=null) {
        $pagar=$datos->total+$datos->izipay;
        $tblt .= '<tr>
            <td width="83%" align="right"><b>IZIPAY : S/.</b></td>
            <td width="17%" align="right">'.formatoPrecio($datos->izipay).'</td>
            </tr>';
        $tblt .= '<tr>
            <td width="83%" align="right"><b>A PAGAR : S/.</b></td>
            <td width="17%" align="right">'.formatoPrecio($pagar).'</td>
            </tr>';
    }
$tblt .= '</table>';
$pdf->writeHTML($tblt, true, false, false, false, '');

if ($datos->dadicional!='') {
    $pdf->SetFont('dejavusanscondensed','B',7.5);
    $pdf->Cell(0,2,'Descripcion Adicional : ',0,1,'L');
    $pdf->SetFont('dejavusanscondensed','',7.5);
    $pdf->MultiCell(0,2,$datos->dadicional,0,'L');
    $pdf->Ln(2);
}

$pdf->SetFont('dejavusanscondensed','B',7.5);
if ($datos->izipay!=null) {
    $pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($pagar),0,'L');
}else{
    $pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
}
$pdf->Ln(2);

if ($datos->retencion!='') {
    $retencion=json_decode($datos->retencion);

    $pdf->SetFont('dejavusanscondensed','B',7);
    $pdf->Cell(0,4,'Informacion de la retencion : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',6.5);
    $tblr = '<table cellspacing="0" cellpadding="1" border="0">';
        $tblr .= '<tr>
            <td width="50%">Base de la retencion</td>
            <td width="40%" align="center">'.monedaSimbolo($datos->moneda).$retencion->base.'</td>
        </tr>
        <tr>
            <td width="50%">Porcentaje de retencion</td>
            <td width="40%" align="center">'.($retencion->factor*100).'%</td>
        </tr>
        <tr>
            <td width="50%">Monto de la retencion</td>
            <td width="40%" align="center">'.monedaSimbolo($datos->moneda).$retencion->monto.'</td>
        </tr>
        <tr>
            <td width="50%">Monto a pagar</td>
            <td width="40%" align="center">'.monedaSimbolo($datos->moneda).($datos->total-$retencion->monto).'</td>
        </tr>';
    $tblr .= '</table>';
    $pdf->writeHTML($tblr, true, false, false, false, '');
}

if ($datos->detraccion!='') {
    $detraccion=json_decode($datos->detraccion);
    $codigo=$this->tdetraccion_model->mostrar($detraccion->codigo);
    $medio=$this->tmedio_model->mostrar($detraccion->medio);

    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(0,4,'Informacion de la detraccion : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',5.5);
    $tblr = '<table cellspacing="0" cellpadding="1" border="0">';
        $tblr .= '<tr>
            <td width="50%">Leyenda:</td>
            <td width="50%">Operación sujeta al Sistema de Pago de Obligaciones Tributarias con el Gobierno Central</td>
        </tr>
        <tr>
            <td>Bien o Servicio:</td>
            <td>'.$detraccion->codigo.' '.$codigo->descripcion.'</td>
        </tr>
        <tr>
            <td>Medio Pago:</td>
            <td>'.$detraccion->medio.' '.$medio->descripcion.'</td>
        </tr>
        <tr>
            <td>Nro Cta Banco de la Nacion:</td>
            <td>'.$detraccion->ncuenta.'</td>
        </tr>
        <tr>
            <td>Porcentaje de detraccion:</td>
            <td>'.$detraccion->factor.'</td>
        </tr>
        <tr>
            <td>Monto detraccion:</td>
            <td>'.$detraccion->monto.'</td>
        </tr>';
    $tblr .= '</table>';
    $pdf->writeHTML($tblr, true, false, false, false, '');
}

$inicio=$pdf->GetY();
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
$pdf->write2DBarcode($qrcode, 'QRCODE,Q', '','', 28, 28, $style, 'N');

$pdf->SetY($inicio+2);
if ($datos->hash!='') {
    $pdf->SetFont('dejavusanscondensed','B',7.5);
    $pdf->Cell(27,3.5,'',0,0,'L');
    $pdf->Cell(15,3.5,'Codigo Hash : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',7.5);
    $pdf->Cell(27,3.5,'',0,0,'L');
    $pdf->Cell(0,3.5,$datos->hash,0,1,'L');
    $pdf->Ln(2);
}

if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}
$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(27,3.5,'',0,0,'L');
$pdf->Cell(0,3.5,'Condicion Pago: '.$tpago,0,1,'L');

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',7.5);
    $pdf->Cell(27,3.5,'',0,0,'L');
    $pdf->Cell(0,3.5,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',7.5);
    foreach ($cobros as $cobro) {
        $pdf->Cell(27,3.5,'',0,0,'L');
        $pdf->Cell(0,3.5,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','B',7.5);
    $pdf->Cell(27,3.5,'',0,0,'L');
    $pdf->Cell(0,3.5,'Cuota / Fecha / Monto',0,1,'L');
    $pdf->SetFont('dejavusanscondensed','',7.5);
    $feinicial=$datos->fpago;
    for ($i=1; $i <= $datos->cuotas ; $i++) {
      if ($i == $datos->cuotas) {
          $cuota = $datos->total - ($datos->mcuota * ($datos->cuotas - 1));;
      } else {
          $cuota = $datos->mcuota;
      }
      $pdf->Cell(27,3.5,'',0,0,'L');
      $pdf->Cell(0,3.5,'• # '.$i.' / '.$feinicial.' / S/ '.$cuota,0,1,'L');
      $suma=tiempoCuota($datos->pcuota);
      $feinicial=SumarFecha($suma,$feinicial);
    }
}

$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(27,3.5,'',0,0,'L');
$pdf->Cell(15,3.5,'Vendedor : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7.5);
$pdf->Cell(0,3.5,($vendedor->nombres??''),0,1,'L');
$pdf->Ln(2);

if ($datos->efectivo!=null) {
$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(20,3,'Efectivo Soles ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7.5);
$pdf->Cell(15,3,formatoPrecio($datos->efectivo),0,1,'L');
$pdf->SetFont('dejavusanscondensed','B',7.5);
$pdf->Cell(20,3,'Vuelto : S/ ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7.5);
$pdf->Cell(15,3,formatoPrecio($datos->vuelto),0,1,'L');
$pdf->Ln(2);
}

if ($tpuntos->cantidad!=null) {
    $pdf->SetFont('dejavusanscondensed','B',7.5);
    $pdf->Cell(27,3.5,'Puntos Acumulados : ',0,0,'L');
    $pdf->SetFont('dejavusanscondensed','',7.5);
    $pdf->Cell(0,3.5,$tpuntos->cantidad,0,1,'L');
}

$pdf->Ln(2);
$pdf->SetFont('dejavusanscondensed','',7.5);
$pdf->writeHTML(nl2br($empresa->piec), true, false, true, false, '');

if (!empty($tipo)) {
    $archivo='./downloads/pdf/'.$datos->filename.'.pdf';
    $pdf_string = $pdf->Output('pseudo.pdf', 'S');
    file_put_contents($archivo, $pdf_string);
} else {
    $pdf->Output();
}

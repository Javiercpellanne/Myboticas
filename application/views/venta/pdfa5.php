<?php
if ($datos->condicion==1) { $tpago='Contado';} else {$tpago='Credito';}

$pdf = new Mytcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetTitle('Comprobante Pago');
$pdf->SetMargins(10,10,10);

$pdf->SetAutoPageBreak(true,9);
$pdf->SetAuthor('SGFarma');

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage("L","A5");

// contenido
$pdf->SetFont('dejavusanscondensed', '',8.5);
$logo = $nestablecimiento->logoe!='' ? $nestablecimiento->logoe: $empresa->logo;
$tblc = '<table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="20%" align="center"><img src="'.$logo.'" border="0" height="70"/></td>
            <td width="50%" align="center">'.$empresa->nombres.'<br>'.'RUC '.$empresa->ruc.'<br> <span style="font-size: 0.7 em;">'.$nestablecimiento->direccion.'<br>'.$nestablecimiento->ndepartamento.'-'.$nestablecimiento->nprovincia.'-'.$nestablecimiento->ndistrito.'<br>'.'TELF '.$nestablecimiento->telefono.'</span></td>
            <td width="30%" align="center" style="border: 1px solid #000;"><br><br>'.'RUC Nº '.$empresa->ruc.'<br><b style="font-size: 1.2 em;">'.$datos->ncomprobante.' ELECTRÓNICA'.'<br>'.$datos->serie.'-'.zerofill($datos->numero, 8).'</b></td>
        </tr>
        </table>';
$pdf->writeHTML($tblc, false, false, false, false, '');
$pdf->Ln(2);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());

$ncomercial=$cliente->ncomercial!='' ? ' -- '.$cliente->ncomercial: '';
$direccion=$datos->idcliente>1 ? ' -- '.$cliente->ndistrito.' - '.$cliente->nprovincia.' - '.$cliente->ndepartamento: '';
$pdf->SetFont('dejavusanscondensed','',8);
$tblf = '<table cellspacing="0" cellpadding="2" border="0">
        <tr>
            <td width="15%"><b>Señor(es) : </b></td>
            <td width="45%">'.$cliente->nombres.$ncomercial.'</td>
            <td width="25%"><b>Fecha Emision: </b></td>
            <td width="15%">'.$datos->femision.'</td>
        </tr>
        <tr>
            <td><b>'.$cliente->descripcion.' :</b></td>
            <td>'.$cliente->documento.'</td>
            <td><b>Fecha Vencimiento :</b></td>
            <td>'.$datos->fvencimiento.'</td>
        </tr>
        <tr>
            <td><b>Direccion :</b></td>
            <td>'.$cliente->direccion.$direccion.'</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><b>Telefono :</b></td>
            <td>'.$cliente->telefono.'</td>
            <td><b>Orden Compra :</b></td>
            <td>'.$datos->ocompra.'</td>
        </tr>
        <tr>
            <td><b>Vendedor :</b></td>
            <td>'.($vendedor->nombres??'').'</td>
            <td><b>Condiciones Pago :</b></td>
            <td>'.$tpago.'</td>
        </tr>
        </table>';
$pdf->writeHTML($tblf, false, false, false, false, '');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',6);
$tbl = '<table cellspacing="0" cellpadding="1" border="0">
        <tr>
            <th align="center" width="8%" style="border: 1px solid #000;"><b>CANT</b></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><b>UNIDAD</b></th>
            <th align="center" width="52%" style="border: 1px solid #000;"><b>DESCRIPCION</b></th>
            <th align="center" width="12%" style="border: 1px solid #000;"><b>P.UNIT</b></th>
            <th align="center" width="8%" style="border: 1px solid #000;"><b>DSCTO</b></th>
            <th align="center" width="12%" style="border: 1px solid #000;"><b>TOTAL</b></th>
        </tr>';
	$gdscto=0;
	$pdf->SetFont('dejavusanscondensed','',7.5);
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
            <td>'.$detalle->cantidad.'</td>
            <td>'.$detalle->unidad.'</td>
            <td>'.nl2br($detalle->descripcion).$ddscto.$lotes.'</td>
            <td align="right">'.$detalle->precio.'</td>
            <td align="center">'.$mdscto.'</td>
            <td align="right">'.$detalle->importe.'</td>
        </tr>';
	}
$tbl .= '</table>';
$pdf->writeHTML($tbl, false, false, false, false, '');
$pdf->Ln(1);

$style = ['width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
$pdf->SetLineStyle($style);
$pdf->Line(10, $pdf->getY(), $pdf->getPageWidth()-10, $pdf->getY());

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

$pdf->SetFont('dejavusanscondensed','B',7);
$pdf->Cell(38,3,'Descripcion Adicional : ',0,0,'L');
$pdf->SetFont('dejavusanscondensed','',7);
$pdf->MultiCell(0,2,$datos->dadicional,0,'L');
$pdf->Ln(2);

$pdf->SetFont('dejavusanscondensed','B',7);
$pdf->MultiCell(0,2,'SON : '.MontoMonetarioEnLetras($datos->total),0,'L');
$pdf->Ln(2);

if ($datos->retencion!='') {
    $retencion=json_decode($datos->retencion);

    $pdf->SetFont('dejavusanscondensed','B',7);
    $pdf->Cell(0,4,'Informacion de la retencion : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',6.5);
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
$pdf->write2DBarcode($qrcode, 'QRCODE,Q', $pdf->GetX()+75,$pdf->GetY(), 30, 30, $style, 'N');

if ($datos->hash!='') {
    $pdf->SetFont('dejavusanscondensed','',6);
    $pdf->Cell(0,3,'Codigo Hash : '.$datos->hash,0,1,'C');
    $pdf->Ln(2);
}

if ($datos->condicion==1) {
    $pdf->SetFont('dejavusanscondensed','B',6);
    $pdf->Cell(0,3,'Pagos : ',0,1,'L');

    $pdf->SetFont('dejavusanscondensed','',6);
    foreach ($cobros as $cobro) {
        $pdf->Cell(0,3,'• '.$cobro->ntpago.' -- S/ '.$cobro->total,0,1,'L');
    }
} else {
    $pdf->SetFont('dejavusanscondensed','',6);
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
$pdf->Ln(2);

$pdf->Output();

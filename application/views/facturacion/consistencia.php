<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Consistencia documentos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active">Consistencia documentos</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <?php $anuo=date("Y"); $mes=date("m"); ?>
            <h3 class="card-title">Comprobantes Estado <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover table-striped table-bordered table-sm">
              <thead>
                <tr>
                  <th>Comprobantes</th>
                  <?php foreach ($estados as $estado): ?>
                  <th><?php echo $estado->descripcion; ?></th>
                  <?php endforeach ?>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Facturas</td>
                  <?php $tfactura=0; ?>
                  <?php foreach ($estados as $estado): ?>
                  <?php $contadorf=$this->venta_model->contador(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>$estado->id,'tcomprobante'=>'01')); ?>
                  <td><?php echo $contadorf; ?></td>
                  <?php $tfactura+=$contadorf; ?>
                  <?php endforeach ?>
                  <td><?php echo $tfactura; ?></td>
                </tr>
                <tr>
                  <td>Boletas</td>
                  <?php $tboleta=0; ?>
                  <?php foreach ($estados as $estado): ?>
                  <?php $contadorb=$this->venta_model->contador(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>$estado->id,'tcomprobante'=>'03')); ?>
                  <td><?php echo $contadorb; ?></td>
                  <?php $tboleta+=$contadorb; ?>
                  <?php endforeach ?>
                  <td><?php echo $tboleta; ?></td>
                </tr>
                <tr>
                  <td>Notas Credito</td>
                  <?php $tcredito=0; ?>
                  <?php foreach ($estados as $estado): ?>
                  <?php $contadorn=$this->nota_model->contador(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>$estado->id,'tcomprobante'=>'07'));?>
                  <td><?php echo $contadorn; ?></td>
                  <?php $tcredito+=$contadorn; ?>
                  <?php endforeach ?>
                  <td><?php echo $tcredito; ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Consultas Facturacion</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover table-striped table-bordered table-sm">
              <thead>
                <tr>
                  <th>Meses</th>
                  <?php for ($i=0; $i < 12 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}
                    ?>
                    <th><?php echo zerofill($mes,2).'/'.$anuo; ?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>Comprobantes emitidos</strong></td>
                  <?php for ($i=0; $i < 12 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtros=array('year(femision)'=>$anuo,'month(femision)'=>$mes);
                    $contadorv=$this->venta_model->contador($filtros);
                    $contadorn=$this->nota_model->contador($filtros);
                    $emitidos=$contadorv+$contadorn;
                    ?>
                    <td align="center"><?php echo $emitidos; ?></td>
                  <?php } ?>
                </tr>
                <tr>
                  <td><strong>Comprobantes no enviados</strong></td>
                  <?php for ($i=0; $i < 12 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtros=array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>"01");
                    $contadorv=$this->venta_model->contador($filtros);
                    $contadorn=$this->nota_model->contador($filtros);
                    $noenviados=$contadorv+$contadorn;
                    ?>
                    <td align="center"><?php echo $noenviados; ?></td>
                  <?php } ?>
                </tr>
                <tr>
                  <td><strong>Resumenes no consultados</strong></td>
                  <?php for ($i=0; $i < 12 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtror=array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>"01");
                    $resumenes=$this->resumen_model->contador($filtror);
                    ?>
                    <td align="center"><?php echo $resumenes; ?></td>
                  <?php } ?>
                </tr>
                <tr>
                  <td><strong>Anulaciones no consultadas</strong></td>
                  <?php for ($i=0; $i < 12 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtror=array('year(femision)'=>$anuo,'month(femision)'=>$mes,"tipo_estado"=>"01");
                    $anulaciones=$this->anulado_model->contador($filtror);
                    ?>
                    <td align="center"><?php echo $anulaciones; ?></td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Resumen de Documentos</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover table-striped table-bordered table-sm">
              <thead>
                <tr>
                  <th class="p-1">Documento</th>
                  <?php for ($i=0; $i < 6 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}
                    ?>
                    <th class="p-1" colspan="3"><?php echo zerofill($mes,2).'/'.$anuo; ?></th>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="p-1">Facturas</td>
                  <?php for ($i=0; $i < 6 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtros=array('tcomprobante'=>'01','year(femision)'=>$anuo,'month(femision)'=>$mes);
                    $facturas=$this->venta_model->contador($filtros);
                    $mfacturas=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'01','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'05');
                    $faceptado=$this->venta_model->contador($filtros);
                    $mfaceptado=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'01','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'09');
                    $frechazado=$this->venta_model->contador($filtros);
                    $mfrechazado=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'01','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'11');
                    $fanulado=$this->venta_model->contador($filtros);
                    $mfanulado=$this->venta_model->montoTotal($filtros);
                    ?>
                    <td class="p-1" align="center"><?php echo $facturas; ?></td>
                    <td class="p-1" align="center"><?php echo $mfacturas->total; ?></td>
                    <td class="p-1" style="font-size: .65rem;">
                      Acep : <?php echo $faceptado.' / '.$mfaceptado->total; ?> <br>
                      Rech : <?php echo $frechazado.' /'.$mfrechazado->total; ?>  <br>
                      Anul : <?php echo $fanulado.' / '.$mfanulado->total; ?>  <br>
                      <hr class="my-0">
                      Total : <?php echo ($faceptado+$fanulado).' / '.$mfaceptado->total; ?>  <br>
                    </td>
                  <?php } ?>
                </tr>
                <tr>
                  <td class="p-1">Boletas</td>
                  <?php for ($i=0; $i < 6 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtros=array('tcomprobante'=>'03','year(femision)'=>$anuo,'month(femision)'=>$mes);
                    $boletas=$this->venta_model->contador($filtros);
                    $mboletas=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'03','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'05');
                    $baceptado=$this->venta_model->contador($filtros);
                    $mbaceptado=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'03','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'09');
                    $brechazado=$this->venta_model->contador($filtros);
                    $mbrechazado=$this->venta_model->montoTotal($filtros);

                    $filtros=array('tcomprobante'=>'03','year(femision)'=>$anuo,'month(femision)'=>$mes,'tipo_estado'=>'11');
                    $banulado=$this->venta_model->contador($filtros);
                    $mbanulado=$this->venta_model->montoTotal($filtros);
                    ?>
                    <td class="p-1" align="center"><?php echo $boletas; ?></td>
                    <td class="p-1" align="center"><?php echo $mboletas->total; ?></td>
                    <td class="p-1" style="font-size: .65rem;">
                      Acep : <?php echo $baceptado.' / '.$mbaceptado->total; ?> <br>
                      Rech : <?php echo $brechazado.' /'.$mbrechazado->total; ?>  <br>
                      Anul : <?php echo $banulado.' / '.$mbanulado->total; ?>  <br>
                      <hr class="my-0">
                      Total : <?php echo ($baceptado+$banulado).' / '.$mbaceptado->total; ?>  <br>
                    </td>
                  <?php } ?>
                </tr>
                <tr>
                  <td class="p-1">Notas Credito</td>
                  <?php for ($i=0; $i < 6 ; $i++) { ?>
                    <?php
                    $mes=date("m")-$i; $anuo=date("Y");
                    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

                    $filtros=array('tcomprobante'=>'07','year(femision)'=>$anuo,'month(femision)'=>$mes);
                    $creditos=$this->nota_model->contador($filtros);
                    $mcreditos=$this->nota_model->montoTotal($filtros);
                    ?>
                    <td class="p-1" align="center"><?php echo $creditos; ?></td>
                    <td class="p-1" align="center"><?php echo $mcreditos->total; ?></td>
                    <td class="p-1"></td>
                  <?php } ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Numeracion Documentos</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped table-hover table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Comprobantes</th>
                  <th>Serie</th>
                  <th>Numeracion</th>
                  <th>Registrado</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; foreach ($listas as $lista) { ?>
                  <?php
                  $rangoc=$this->venta_model->rangoSerie($lista->serie);
                  $emitidac=$this->venta_model->contador(array('serie'=>$lista->serie));

                  $rangon=$this->nota_model->rangoSerie($lista->serie);
                  $emitidan=$this->nota_model->contador(array('serie'=>$lista->serie));

                  $rangov=$this->nventa_model->rangoSerie($lista->serie);
                  $emitidav=$this->nventa_model->contador(array('serie'=>$lista->serie));

                  $rangog=$this->despacho_model->rangoSerie($lista->serie);
                  $emitidag=$this->despacho_model->contador(array('serie'=>$lista->serie));
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->ncomprobante; ?></td>
                    <td><?php echo $lista->serie; ?></td>
                    <td>
                      <?php
                      if ($emitidac>0) {
                        echo $rangoc->minimo.' - '.$rangoc->maximo;
                      }
                      if ($emitidan>0) {
                        echo $rangon->minimo.' - '.$rangon->maximo;
                      }
                      if ($emitidav>0) {
                        echo $rangov->minimo.' - '.$rangov->maximo;
                      }
                      if ($emitidag>0) {
                        echo $rangog->minimo.' - '.$rangog->maximo;
                      }
                      ?>
                    </td>
                    <td>
                      <?php
                      if ($emitidac>0) {
                        echo $emitidac;
                      }
                      if ($emitidan>0) {
                        echo $emitidan;
                      }
                      if ($emitidav>0) {
                        echo $emitidav;
                      }
                      if ($emitidag>0) {
                        echo $emitidag;
                      }
                      ?>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title" id="modalTitle">Datos de la Serie</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>


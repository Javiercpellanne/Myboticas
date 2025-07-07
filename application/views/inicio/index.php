<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <?php $anuo=date("Y"); $mes=date("m"); ?>
        <h4 class="m-0 text-dark">Panel Principal al  <?php echo $mes.'/'.$anuo; ?></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Inicio</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <?php if ($empresa->facturacion==1): ?>
        <?php $rventa=$arqueoc>0 ? base_url().'venta/ventai': base_url().'venta'; ?>
        <div class="col-12 col-sm-6 col-md-3">
          <a href="<?php echo $rventa; ?>">
            <div class="card card-widget widget-user-2">
              <div class="widget-user-header bg-info p-2">
                <div class="widget-user-image">
                  <img class="img-circle elevation-2" src="public/logo/ventas.png" alt="User Avatar" style="width: 55px;">
                </div>

                <h5 class="widget-user-desc">Ir Nuevo <br> CPE</h5>
              </div>
            </div>
          </a>
        </div>
      <?php endif ?>

      <div class="col-12 col-sm-6 col-md-3">
        <?php $rnventa=$arqueoc>0 ? base_url().'nventa/nventai': base_url().'nventa'; ?>
        <a href="<?php echo $rnventa; ?>">
          <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-primary p-2">
              <div class="widget-user-image">
                <img class="img-circle elevation-2" src="public/logo/ventas.png" alt="User Avatar" style="width: 55px;">
              </div>

              <h5 class="widget-user-desc">Ir Nueva <br> Nota Venta</h5>
            </div>
          </div>
        </a>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <?php $rcompra=$arqueoc>0 ? base_url().'compra/comprai': base_url().'compra'; ?>
        <a href="<?php echo $rcompra; ?>">
          <div class="card card-widget widget-user-2">
            <div class="widget-user-header bg-lightblue p-2">
              <div class="widget-user-image">
                <img class="img-circle elevation-2" src="public/logo/compras.png" alt="User Avatar" style="width: 55px;">
              </div>

              <h5 class="widget-user-desc">Ir Nueva <br> Compra</h5>
            </div>
          </div>
        </a>
      </div>
    </div>
    <?php if ($this->session->userdata("tipo")=='admin'): ?>
    <div class="row">
      <div class="col-sm-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Alertas de Fechas de Vencimiento:</h3>
          </div>

          <div class="card-body p-0">
            <table class="table table-hover">
              <tbody>
                <?php foreach ($fecvencimientos as $fecvencimiento): ?>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                    <?php echo $fecvencimiento->fecha; ?>
                  </td>
                </tr>
                <tr class="expandable-body">
                  <td>
                    <div class="p-0" style="display: none;">
                      <?php
                      $fecha=explode('-', $fecvencimiento->fecha);
                      $nlotes=$this->lote_model->productosVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'year(fvencimiento)'=>$fecha[0],'month(fvencimiento)'=>$fecha[1],"estado"=>1,"stock>"=>0));
                      ?>
                      <div class="table-responsive" style="height: 440px; font-size: .78rem;">
                        <table class="table table-hover table-sm table-striped">
                          <thead class="thead-dark">
                            <tr>
                                <th align="center" width="5%"><strong>#</strong></th>
                                <th align="center" width="60%"><strong>Descripcion</strong></th>
                                <th align="center" width="10%"><strong>Lote</strong></th>
                                <th align="center" width="15%"><strong>F. Vcto</strong></th>
                                <th align="center" width="10%"><strong>Cantidad</strong></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $i=1; ?>
                            <?php foreach ($nlotes as $nlote): ?>
                              <?php
                              $nproducto=$nlote->descripcion;
                              if ($nlote->nlaboratorio!='') {$nproducto.=' ['.$nlote->nlaboratorio.']';}
                              ?>
                            <tr>
                              <td width="5%"><?php echo $i; ?></td>
                              <td width="60%"><?php echo $nproducto; ?></td>
                              <td width="10%"><?php echo $nlote->lote; ?></td>
                              <td width="15%"><?php echo $nlote->fvencimiento; ?></td>
                              <td width="10%" align="center"><?php echo $nlote->stock; ?></td>
                            </tr>
                            <?php $i++; ?>
                            <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Alertas de Stocks Mínimos:</h3>
          </div>

          <div class="card-body p-0">
            <table class="table table-hover">
              <tbody>
                <?php
                $pocas=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1,"stock>"=>0));
                ?>
                <?php if ($pocas!=NULL): ?>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                    Pocas existencias
                  </td>
                </tr>
                <tr class="expandable-body">
                  <td>
                    <div class="p-0" style="display: none;">
                      <div class="table-responsive" style="height: 440px; font-size: .78rem;">
                        <table class="table table-hover table-sm table-striped">
                          <thead class="thead-dark">
                              <tr>
                                  <th align="center" width="5%"><strong>#</strong></th>
                                  <th align="center" width="75%"><strong>Descripcion</strong></th>
                                  <th align="center" width="10%"><strong>Minimo</strong></th>
                                  <th align="center" width="10%"><strong>Cantidad</strong></th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php $i=1; ?>
                              <?php foreach ($pocas as $lista): ?>
                                  <?php
                                  $nproducto=$lista->descripcion;
                                  if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                                  ?>
                                  <tr>
                                      <td width="5%"><?php echo $i; ?></td>
                                      <td width="75%"><?php echo $nproducto; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->mstock; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->stock; ?></td>
                                  </tr>
                                  <?php $i++; ?>
                              <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endif ?>

                <?php
                $ningunas=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1,"stock"=>0));
                ?>
                <?php if ($ningunas!=NULL): ?>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                    No hay existencias
                  </td>
                </tr>
                <tr class="expandable-body">
                  <td>
                    <div class="p-0" style="display: none;">
                      <div class="table-responsive" style="height: 440px; font-size: .78rem;">
                        <table class="table table-hover table-sm table-striped">
                          <thead class="thead-dark">
                              <tr>
                                  <th align="center" width="5%"><strong>#</strong></th>
                                  <th align="center" width="75%"><strong>Descripcion</strong></th>
                                  <th align="center" width="10%"><strong>Minimo</strong></th>
                                  <th align="center" width="10%"><strong>Cantidad</strong></th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php $i=1; ?>
                              <?php foreach ($ningunas as $lista): ?>
                                  <?php
                                  $nproducto=$lista->descripcion;
                                  if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                                  ?>
                                  <tr>
                                      <td width="5%"><?php echo $i; ?></td>
                                      <td width="75%"><?php echo $nproducto; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->mstock; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->stock; ?></td>
                                  </tr>
                                  <?php $i++; ?>
                              <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endif ?>

                <?php
                $negativos=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1,"stock<"=>0));
                ?>
                <?php if ($negativos!=NULL): ?>
                <tr data-widget="expandable-table" aria-expanded="false">
                  <td>
                    <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
                    Saldo Negativo
                  </td>
                </tr>
                <tr class="expandable-body">
                  <td>
                    <div class="p-0" style="display: none;">
                      <div class="table-responsive" style="height: 440px; font-size: .78rem;">
                        <table class="table table-hover table-sm table-striped">
                          <thead class="thead-dark">
                              <tr>
                                  <th align="center" width="5%"><strong>#</strong></th>
                                  <th align="center" width="75%"><strong>Descripcion</strong></th>
                                  <th align="center" width="10%"><strong>Minimo</strong></th>
                                  <th align="center" width="10%"><strong>Cantidad</strong></th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php $i=1; ?>
                              <?php foreach ($negativos as $lista): ?>
                                  <?php
                                  $nproducto=$lista->descripcion;
                                  if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                                  ?>
                                  <tr>
                                      <td width="5%"><?php echo $i; ?></td>
                                      <td width="75%"><?php echo $nproducto; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->mstock; ?></td>
                                      <td width="10%" align="center"><?php echo $lista->stock; ?></td>
                                  </tr>
                                  <?php $i++; ?>
                              <?php endforeach ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </td>
                </tr>
                <?php endif ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Ventas <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-1">
            <div class="chart">
              <canvas id="barVenta" style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="col-sm-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Compras <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-1">
            <div class="chart">
              <canvas id="barCompra" style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-4">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Nota Venta <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-2">
            <div class="chart">
              <canvas id="doughnutNventa"></canvas>
            </div>
          </div>
        </div>
      </div>

      <?php if ($empresa->facturacion==1): ?>
      <div class="col-sm-4">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Comprobante Electronico <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-2">
            <div class="chart">
              <canvas id="doughnutComprobante"></canvas>
            </div>
          </div>
        </div>
      </div>
      <?php endif ?>

      <div class="col-sm-4">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Compra <?php echo $mes.'/'.$anuo; ?></h3>
          </div>
          <div class="card-body p-2">
            <div class="chart">
              <canvas id="doughnutCompra"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12 d-none d-sm-block">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Ventas Emitidas <?php echo $mes.'/'.$anuo; ?></h3>
          </div>

          <div class="card-body p-0">
            <table class="table table-hover table-bordered table-sm table-info" style="font-size: .71rem;">
              <thead>
                <tr>
                  <th width="5%" class="pl-1">Dias</th>
                  <?php
                  $finicio='01-'.date("m").'-'.date("Y");
                  $ndias=cal_days_in_month(CAL_GREGORIAN,date("m"),date("Y"));
                  $ancho=round(95/$ndias,2);
                  ?>
                  <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                    <?php $fecha=SumarFecha('+'.$i.' day',$finicio); ?>
                    <th width="<?php echo $ancho; ?>" class="pr-1" <?php if ($fecha==date("Y-m-d")) {echo 'class="table-danger"';} ?>><?php echo date("d",strtotime($fecha)); ?></th>
                    <?php $total[$i]=0; ?>
                  <?php } ?>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="pl-1">Nota Venta</td>
                  <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                    <?php
                    $fecha=SumarFecha('+'.$i.' day',$finicio);
                    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
                    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
                    $ntotal=$this->nventa_model->montoTotal($filtros);
                    ?>
                    <td align="right" class="pr-1" <?php if ($fecha==date("Y-m-d")) {echo 'class="table-danger"';} ?>><?php echo $ntotal->total??''; ?></td>
                    <?php $total[$i]+=$ntotal->total; ?>
                  <?php } ?>
                </tr>
                <?php if ($empresa->facturacion==1): ?>
                  <tr>
                    <td class="pl-1">BV/Factura</td>
                    <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                      <?php
                      $fecha=SumarFecha('+'.$i.' day',$finicio);
                      $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
                      if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
                      $vtotal=$this->venta_model->montoTotal($filtros);
                      ?>
                      <td align="right" class="pr-1" <?php if ($fecha==date("Y-m-d")) {echo 'class="table-danger"';} ?>><?php echo $vtotal->total??''; ?></td>
                      <?php $total[$i]+=$vtotal->total; ?>
                    <?php } ?>
                  </tr>
                  <tr>
                    <td class="pl-1">Nota Credito</td>
                    <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                      <?php
                      $fecha=SumarFecha('+'.$i.' day',$finicio);
                      $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
                      if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
                      $dtotal=$this->nota_model->montoTotal($filtros);
                      ?>
                      <td align="right" class="pr-1" <?php if ($fecha==date("Y-m-d")) {echo 'class="table-danger"';} ?>><?php echo $dtotal->total??''; ?></td>
                      <?php $total[$i]-=$dtotal->total; ?>
                    <?php } ?>
                  </tr>
                <?php endif ?>
                <tr style="border-top: 2px dashed red; border-bottom: 2px dashed red;">
                  <td class="pl-1"><i>Total Venta</i></td>
                  <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                    <td align="right" class="pr-1 text-muted"><b><?php echo formatoMonto($total[$i]); ?></b></td>
                  <?php } ?>
                </tr>
                <?php foreach ($mpagos as $mpago): ?>
                <tr>
                  <td class="pl-1"><i><?php echo $mpago->descripcion; ?></i></td>
                  <?php for ($i=0; $i < $diasmes ; $i++) {  ?>
                    <?php
                    $fecha=SumarFecha('+'.$i.' day',$finicio);
                    $filtrop=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision"=>$fecha,"idtpago"=>$mpago->id);
                    if ($this->session->userdata("tipo")!='admin') {$filtrop['iduser']=$this->session->userdata("id");}
                    //cobros comprobantes
                    $mcobros=$this->cobro_model->montoTotal($filtrop);
                    $mcobrosc=$this->cobroe_model->montoTotal($filtrop);
                    $mcobrosn=$this->cobron_model->montoTotal($filtrop);
                    $totalm=$mcobros->total+$mcobrosc->total+$mcobrosn->total;
                    ?>
                    <td align="right" class="pr-1 text-primary"><?php echo $totalm>0 ? formatoMonto($totalm):''; ?></td>
                  <?php } ?>
                </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php endif ?>
  </div>
</section>

<!-- <audio id="miAudio" src="<?php echo base_url(); ?>public/img/inicio.mp3" muted></audio>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      document.getElementById('miAudio').muted = false;
      document.getElementById('miAudio').play();
  });
</script> -->

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

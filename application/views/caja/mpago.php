<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Medio Pago</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Caja</li>
          <li class="breadcrumb-item active">Medio pago</li>
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
          <div class="card-body p-3">
            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="inicio" class="col-sm-1 col-form-label">DESDE</label>
                <div class="col-sm-2">
                  <input name="inicio" type="date" id="inicio" class="form-control form-control-sm" value="<?php echo $inicio; ?>" required/>
                </div>

                <label for="fin" class="col-sm-1 col-form-label">HASTA</label>
                <div class="col-sm-2">
                  <input name="fin" type="date" id="fin" class="form-control form-control-sm" value="<?php echo $fin; ?>" required/>
                </div>

                <div class="col-sm-2">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>

                <div class="col-sm-2 text-right">
                  <a href="<?php echo base_url(); ?>caja/pdfcaja/<?php echo $inicio; ?>/<?php echo $fin; ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>
                </div>
              </div>
            <?php echo form_close(); ?>

            <table class="table table-striped table-bordered table-sm">
              <thead class="table-dark">
                <tr>
                  <th>Modalidad</th>
                  <?php if ($empresa->facturacion==1): ?>
                  <th>CPE</th>
                  <?php endif ?>
                  <th>Nota Venta</th>
                  <th>Ingresos</th>
                  <th>Compras</th>
                  <th>Egresos</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $tcomprobantes=0; $tnventas=0; $tingresos=0; $tcompras=0; $tegresos=0;
                ?>
                <?php foreach ($medios as $medio): ?>
                  <?php
                  $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin,"idtpago"=>$medio->id);
                  if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}

                  //cobros comprobante
                  $mcobrosc=$this->cobroe_model->montoTotal($filtros);
                  //pagos comprobante
                  $mcobrosn=$this->cobron_model->montoTotal($filtros);
                  $totalComprobante=$mcobrosc->total+$mcobrosn->total;

                  $mcobros=$this->cobro_model->montoTotal($filtros);
                  $totalNventas=$mcobros->total;

                  //ingresos
                  $mingresos=$this->ingreso_model->montoTotal($filtros);
                  $totalIngresos=$mingresos->total;

                  //pagos
                  $mpagos=$this->pago_model->montoTotal($filtros);
                  $totalCompras=$mpagos->total;

                  //egresos
                  $megresos=$this->egreso_model->montoTotal($filtros);
                  $totalegresos=$megresos->total;

                  //$tmedio=$totalComprobante+$totalNventas;
                  ?>
                  <tr>
                    <td><?php echo $medio->descripcion; ?></td>
                    <?php if ($empresa->facturacion==1): ?>
                    <td align="right">
                      <?php echo formatoPrecio($totalComprobante); ?>
                    </td>
                    <?php endif ?>
                    <td align="right">
                      <?php echo formatoPrecio($totalNventas); ?>
                    </td>
                    <td align="right">
                      <?php echo formatoPrecio($totalIngresos); ?>
                    </td>
                    <td align="right">
                      <?php echo formatoPrecio($totalCompras); ?>
                    </td>
                    <td align="right">
                      <?php echo formatoPrecio($totalegresos); ?>
                    </td>
                  </tr>
                  <?php
                  $tcomprobantes+=$totalComprobante; $tnventas+=$totalNventas; $tingresos+=$totalIngresos; $tcompras+=$totalCompras; $tegresos+=$totalegresos;
                  ?>
                <?php endforeach ?>
              </tbody>
              <tfoot class="table-primary">
                <tr>
                  <td align="right"><strong>Totales</strong></td>
                  <?php if ($empresa->facturacion==1): ?>
                  <td align="right"><strong><?php echo formatoPrecio($tcomprobantes); ?></strong></td>
                  <?php endif ?>
                  <td align="right"><strong><?php echo formatoPrecio($tnventas); ?></strong></td>
                  <td align="right"><strong><?php echo formatoPrecio($tingresos); ?></strong></td>
                  <td align="right"><strong><?php echo formatoPrecio($tcompras); ?></strong></td>
                  <td align="right"><strong><?php echo formatoPrecio($tegresos); ?></strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <h4 class="mt-2">Movimientos Caja</h4>
        <div class="card card-outline card-primary">
          <div class="card-body p-3">
            <ul class="nav nav-pills mb-2" id="pills-tab" role="tablist">
              <?php $n=1; ?>
              <?php foreach ($medios as $medio): ?>
                <?php if ($n==1) {$estilo='active'; $espacio='';} else {$estilo=''; $espacio='ml-1';}?>
                <li class="nav-item <?php echo $espacio; ?>" role="presentation">
                  <button class="nav-link py-1 <?php echo $estilo; ?>" id="pills-<?php echo $n; ?>-tab" data-toggle="pill" data-target="#pills-<?php echo $n; ?>" type="button" role="tab" aria-controls="pills-<?php echo $n; ?>" aria-selected="true"><?php echo $medio->descripcion ?></button>
                </li>
                <?php $n++; ?>
              <?php endforeach ?>
            </ul>

            <div class="tab-content" id="pills-tabContent">
              <?php $n=1; ?>
              <?php foreach ($medios as $medio): ?>
                <?php if ($n==1) {$estilo='show active';} else {$estilo='';}?>
              <div class="tab-pane fade <?php echo $estilo; ?>" id="pills-<?php echo $n; ?>" role="tabpanel" aria-labelledby="pills-<?php echo $n; ?>-tab">
                <?php
                $filtros=array("p.idestablecimiento"=>$this->session->userdata("predeterminado"),"p.nulo"=>0,"p.femision>="=>$inicio,"p.femision<="=>$fin,"idtpago"=>$medio->id);
                if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}

                  //ingresos
                  $cobros=$this->cobro_model->mostrarTotal($filtros);
                  $cobrose=$this->cobroe_model->mostrarTotal($filtros);
                  $cobrosn=$this->cobron_model->mostrarTotal($filtros);
                  $ingresos=$this->ingreso_model->mostrarTotal($filtros);

                  //egresos
                  $pagos=$this->pago_model->mostrarTotal($filtros);
                  $egresos=$this->egreso_model->mostrarTotal($filtros);
                ?>
                <div class="table-responsive" style="height: 460px;">
                  <table class="table table-hover table-sm">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Adquiriente</th>
                        <th>Documento</th>
                        <th>Numero</th>
                        <th>Tipo</th>
                        <th>Ingresos</th>
                        <th>egresos</th>
                        <th>Saldo</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; $inicial=0; ?>
                      <?php foreach ($cobros as $lista): ?>
                        <?php
                        $datos=$this->nventa_model->mostrar($lista->idnventa);
                        $inicial+=$lista->total;
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $datos->cliente ; ?></td>
                          <td><?php echo "Nota de Venta"; ?></td>
                          <td><?php echo $datos->serie.'-'.$datos->numero; ?></td>
                          <td><?php echo "Nota de Venta"; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td><?php echo ""; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>

                      <?php foreach ($cobrose as $lista): ?>
                        <?php
                        $datos=$this->venta_model->mostrar($lista->idventa);
                        $inicial+=$lista->total;
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $datos->cliente ; ?></td>
                          <td><?php echo $datos->ncomprobante; ?></td>
                          <td><?php echo $datos->serie.'-'.$datos->numero; ?></td>
                          <td><?php echo "CPE"; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td><?php echo ""; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>

                      <?php foreach ($cobrosn as $lista): ?>
                        <?php
                        $datos=$this->nota_model->mostrar($lista->idnota);
                        $inicial+=$lista->total;
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $datos->cliente ; ?></td>
                          <td><?php echo $datos->ncomprobante; ?></td>
                          <td><?php echo $datos->serie.'-'.$datos->numero; ?></td>
                          <td><?php echo "CPE"; ?></td>
                          <td><?php echo ""; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>

                      <?php foreach ($ingresos as $lista): ?>
                        <?php $inicial+=$lista->total; ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $lista->cliente ; ?></td>
                          <td><?php echo $lista->ningreso; ?></td>
                          <td><?php echo $lista->numero; ?></td>
                          <td><?php echo "Ingreso"; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td><?php echo ""; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>

                      <?php foreach ($pagos as $lista): ?>
                        <?php
                        $datos=$this->compra_model->mostrar($lista->idcompra);
                        $inicial-=$lista->total;
                        ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $datos->proveedor ; ?></td>
                          <td><?php echo $datos->ncomprobante; ?></td>
                          <td><?php echo $datos->serie.'-'.$datos->numero; ?></td>
                          <td><?php echo "Compra"; ?></td>
                          <td><?php echo ""; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>

                      <?php foreach ($egresos as $lista): ?>
                        <?php $inicial-=$lista->total; ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista->femision; ?></td>
                          <td><?php echo $lista->proveedor ; ?></td>
                          <td><?php echo $lista->negreso; ?></td>
                          <td><?php echo $lista->numero; ?></td>
                          <td><?php echo "Egresos"; ?></td>
                          <td><?php echo ""; ?></td>
                          <td><?php echo $lista->total; ?></td>
                          <td align="right"><?php echo formatoPrecio($inicial); ?></td>
                        </tr>
                        <?php $i++; ?>
                      <?php endforeach ?>
                    </tbody>
                  </table>
                </div>
              </div>
                <?php $n++; ?>
              <?php endforeach ?>
            </div>
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


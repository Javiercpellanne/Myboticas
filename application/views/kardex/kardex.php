<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Productos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><b class=" text-danger"><i class="fa fa-home"></i> <?php echo $nestablecimiento->descripcion; ?></b></li>
          <li class="breadcrumb-item">Kardex</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>kardex">Producto</a></li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <div class="form-group row mb-1">
              <label for="stock" class="col-sm-1 col-form-label">Producto</label>
              <div class="col-sm-9">
                <h5 class="my-0"><b><?php echo $datos->descripcion; ?></b></h5>
              </div>

              <label for="stock" class="col-sm-1 col-form-label">Stock</label>
              <div class="col-sm-1">
                <h4 class="my-0"><b><?php echo $cantidad->stock; ?></b></h4>
              </div>
            </div>

            <ul class="nav nav-pills mb-2">
              <?php foreach ($meses as $dato): ?>
              <?php $clase= $anio.' - '.$mes==$dato->anio.' - '.$dato->mes ?  'active': 'border border-info'; ?>
              <li class="nav-item"><a class="nav-link py-1 ml-1 <?php echo $clase; ?>" href="<?php echo base_url(); ?>kardex/kardex/<?php echo $id; ?>/<?php echo $dato->anio; ?>/<?php echo $dato->mes; ?>"><?php echo $dato->anio.' - '.zerofill($dato->mes,2); ?></a></li>
              <?php endforeach ?>
            </ul>

            <table class="table table-bordered table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th width="10%">Fecha y hora</th>
                  <th width="37%">Tipo transacción</th>
                  <th width="10%">Número</th>
                  <th width="5%">Entrada</th>
                  <th width="5%">Salida</th>
                  <th width="5%">Saldo</th>
                  <th width="5%">Costo</th>
                  <th width="5%">Entrada</th>
                  <th width="5%">Salida</th>
                  <th width="5%">Saldo</th>
                  <th width="5%">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $i=1;
                $kardex=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id,"fecha<"=>$anio.'-'.zerofill($mes,2).'-01'));
                ?>
                <?php $i=1; $iniciof=$kardex->saldof??0; $iniciov=$kardex->saldov??0; ?>
                <tr>
                  <td colspan="6" class="text-right font-weight-bold">Saldo mes anterior</td>
                  <td class="text-right font-weight-bold"><?php echo $iniciof; ?></td>
                  <td class="text-right font-weight-bold"></td>
                  <td colspan="2"></td>
                  <td class="text-right font-weight-bold"><?php echo $iniciov; ?></td>
                  <td></td>
                </tr>
                <?php foreach ($listas as $lista) { ?>
                  <?php
                  if ($lista->salidaf!=NULL) {
                    $costo=round($iniciov/$iniciof,4);
                    $saldof=$iniciof-$lista->salidaf;
                    $saldov=round($iniciov-$lista->salidav,4);
                  } else {
                    $costo=$lista->costo;
                    $saldof=($lista->documento=='' ? 0 : $iniciof)+$lista->entradaf;
                    $saldov=round(($lista->documento=='' ? 0 : $iniciov)+$lista->entradav,4);
                  }
                  $clase=$lista->documento=='' ? 'class="table-success"': '';
                  ?>
                  <tr <?php echo $clase ; ?>>
                    <td><?php echo $i; ?></td>
                    <td><a href="javascript:void(0)" onclick="kardexActualizar('<?php echo base_url(); ?>kardex/kardexe/<?php echo $id; ?>/<?php echo $lista->id; ?>','<?php echo $i; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><?php echo $lista->fregistro; ?></a></td>
                    <td><?php echo $lista->concepto; ?></td>
                    <td><?php echo $lista->documento; ?></td>
                    <td align="right"><?php echo $lista->entradaf; ?></td>
                    <td align="right"><?php echo $lista->salidaf; ?></td>
                    <td align="right" <?php if ($lista->saldof<0 || $lista->saldof!=$saldof) {echo 'class="table-danger"';}else{echo '';} ?>>
                      <?php echo $lista->saldof; ?>
                      <input type="hidden" id="saldof<?php echo $i; ?>" name="saldof<?php echo $i; ?>" value="<?php echo $saldof; ?>">
                    </td>
                    <td align="right"<?php if ($costo<=0 || $lista->costo!=$costo) {echo 'class="table-danger"';}else{echo '';} ?>>
                      <?php echo $lista->costo; ?>
                      <input type="hidden" id="costo<?php echo $i; ?>" name="costo<?php echo $i; ?>" value="<?php echo $costo; ?>">
                    </td>
                    <td align="right"><?php echo $lista->entradav; ?></td>
                    <td align="right"><?php echo $lista->salidav; ?></td>
                    <td align="right" <?php if ($lista->saldov<0 || $lista->saldov!=$saldov) {echo 'class="table-danger"';}else{echo '';} ?>>
                      <?php echo $lista->saldov; ?>
                      <input type="hidden" id="saldov<?php echo $i; ?>" name="saldov<?php echo $i; ?>" value="<?php echo $saldov; ?>">
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($ultimo_mes->anio == $anio && $ultimo_mes->mes == $mes): ?>
                        <a href="<?php echo base_url(); ?>kardex/recalcular/<?php echo $id; ?>/<?php echo $lista->id; ?>" class="btn btn-secondary btn-sm py-0" title="Recalcular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-calculator"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; $iniciof=$lista->saldof; $iniciov=$lista->saldov; ?>
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
        <h4 class="modal-title" id="modalTitle">Datos del Kardex</h4>
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


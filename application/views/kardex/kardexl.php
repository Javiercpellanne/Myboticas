<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Productos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><b class=" text-danger"><i class="fa fa-home"></i> <?php echo $nestablecimiento->descripcion; ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active">Producto</li>
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
              <div class="col-sm-11">
                <h4 class="my-0"><b><?php echo $datos->descripcion; ?></b></h4>
              </div>
            </div>

            <div class="row">
              <div class="col-2">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista): ?>
                  <?php $cantidad=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id,"nlote"=>$lista->nlote)); ?>
                  <a class="nav-link <?php echo $i==1 ? ' active show': ''; ?>" id="v-pills-<?php echo $i; ?>-tab" data-toggle="pill" href="#v-pills-<?php echo $i; ?>" role="tab" aria-controls="v-pills-<?php echo $i; ?>" aria-selected="false"><?php echo $lista->nlote.' ( Stock '.($cantidad->stock??0).')'; ?></a>
                  <?php $i++; ?>
                  <?php endforeach ?>
                </div>
              </div>
              <div class="col-10">
                <div class="tab-content" id="v-pills-tabContent">
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista): ?>
                  <div class="tab-pane fade <?php echo $i==1 ? ' active show': ''; ?>" id="v-pills-<?php echo $i; ?>" role="tabpanel" aria-labelledby="v-pills-<?php echo $i; ?>-tab">
                    <?php $lotes=$this->kardexl_model->mostrarTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id,"nlote"=>$lista->nlote));
                    ?>


                    <table class="table table-bordered table-hover table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="2%">#</th>
                          <th width="16%">Fecha y hora</th>
                          <th width="10%">Lote</th>
                          <th width="38">Tipo transacción</th>
                          <th width="13%">Número</th>
                          <th width="7%">Entrada</th>
                          <th width="7%">Salida</th>
                          <th width="7%">Saldo</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $l=1; $iniciof=0; ?>
                        <?php foreach ($lotes as $lote) { ?>
                          <?php
                          if ($lote->salidaf!=NULL) {
                            $saldof=$iniciof-$lote->salidaf;
                          } else {
                            $saldof=($lote->documento=='' ? 0 : $iniciof)+$lote->entradaf;
                          }
                          $clase=$lote->documento=='' ? 'class="table-success"': '';
                          ?>
                          <tr <?php echo $clase ; ?>>
                            <td><?php echo $l; ?></td>
                            <td><a href="javascript:void(0)" onclick="kardexlActualizar('<?php echo base_url(); ?>kardex/kardexle/<?php echo $id; ?>/<?php echo $lote->id; ?>','<?php echo $lote->nlote.$l; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><?php echo fechaHoraria('-5 hour',$lote->fregistro); ?></a></td>
                            <td><?php echo $lote->nlote ?></td>
                            <td><?php echo $lote->concepto; ?></td>
                            <td><?php echo $lote->documento; ?></td>
                            <td><?php echo $lote->entradaf; ?></td>
                            <td><?php echo $lote->salidaf; ?></td>
                            <td align="right" <?php if ($lote->saldof<0 || $lote->saldof!=$saldof) {echo 'class="table-danger"';}else{echo '';} ?>>
                              <?php echo $lote->saldof; ?>
                              <input type="hidden" id="saldof<?php echo $lote->nlote.$l; ?>" name="saldof<?php echo $lote->nlote.$l; ?>" value="<?php echo $saldof; ?>">
                            </td>
                          </tr>
                          <?php $l++; $iniciof=$lote->saldof; ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <?php $i++; ?>
                  <?php endforeach ?>
                </div>
              </div>
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


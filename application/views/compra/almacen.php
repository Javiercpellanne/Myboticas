<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Ingreso Mercaderia Almacen</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>compra">Mercadria</a></li>
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
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."compra/almacen/".$id."');")); ?>
              <input type="hidden" name="documento" value="<?php echo $datos->serie.'-'.$datos->numero; ?>">
              <input type="hidden" name="incluye" value="<?php echo $datos->incluye; ?>">
              <div class="table-responsive p-0" style="height: 370px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="44%">DESCRIPCION</th>
                      <th width="12%">LOTE</th>
                      <th width="10%">F VCTO</th>
                      <th width="8%">U.M</th>
                      <th width="8%">CANT</th>
                      <th width="8%">P.U</th>
                      <th width="8%">IMPORTE</th>
                    </tr>
                  </thead>
                  <tbody id="grilla">
                    <?php foreach ($detalles as $detalle): ?>
                      <?php
                      $pventa=0; $venta=0; $blister=0;
                      if ($detalle->pventas!='') {
                          $precios=json_decode($detalle->pventas);
                          $pventa=$precios->pventa;
                          $venta=$precios->venta;
                          $blister= $precios->pblister;
                      }
                      ?>
                      <tr>
                        <td>
                          <input type="hidden" name="id[]" value="<?php echo $detalle->id; ?>">
                          <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto; ?>">
                          <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion; ?>" class="campo">
                        </td>
                        <td><input type="text" name="lote[]" value="<?php echo $detalle->lote; ?>" class="campo"></td>
                        <td><input type="text" name="fvencimiento[]" value="<?php echo $detalle->fvencimiento; ?>" class="campo"></td>
                        <td>
                          <input type="text" name="unidad[]" value="<?php echo $detalle->unidad; ?>" class="campo">
                          <input type="hidden" name="tafectacion[]" value="<?php echo $detalle->tafectacion; ?>">
                          <input type="hidden" name="almacenc[]" value="<?php echo $detalle->calmacen; ?>">
                          <input type="hidden" name="almacenp[]" value="<?php echo $detalle->palmacen; ?>">
                        </td>
                        <td><input type="text" name="cantidad[]" value="<?php echo $detalle->cantidad; ?>" class="campo"></td>
                        <td>
                          <input type="text" name="precio[]" value="<?php echo $detalle->precio; ?>" class="campo">
                          <input type="hidden" name="pventa[]" value="<?php echo $pventa; ?>">
                          <input type="hidden" name="venta[]" value="<?php echo $venta; ?>">
                          <input type="hidden" name="blister[]" value="<?php echo $blister; ?>">
                        </td>
                        <td><input type="text" name="importe[]" value="<?php echo $detalle->importe; ?>" class="campo"></td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>

              <div class="col-sm-12 text-center">
                <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR"/>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

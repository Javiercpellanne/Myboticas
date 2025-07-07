<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "enctype"=>"multipart/form-data")); ?>
  <div class="form-group row mb-1">
    <span class="col-sm-2 col-form-label">Producto</span>
    <div class="col-sm-8">
      <h5 class="my-0"><input type="text" id="descripcion" name="descripcion" class="campo" value="<?php echo $datos->descripcion; ?>"></h5>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="cantidad" class="col-sm-2 col-form-label">Cantidad</label>
    <div class="col-sm-2">
      <input type="text" id="cantidad" name="cantidad" class="form-control form-control-sm text-right" value="" onkeyup="inventario(this.value,'<?php echo $datos->lote; ?>')" required>
    </div>

    <div class="col-sm-3">
      <small class="text-danger">REEMPLAZA EL STOCK ACTUAL</small>
    </div>
  </div>
  <input type="hidden" id="precio" name="precio" value="<?php echo $datos->pcompra; ?>">
  <?php if ($datos->lote==1): ?>
    <div class="form-group row mb-1">
      <label class="col-sm-2 col-form-label">Codigo Lote </label>
      <div class="col-sm-3">
        <input name="lote" id="lote" type="text" class="form-control form-control-sm" value="" <?php echo $datos->lote==1 ? 'required': ''; ?>>
      </div>
    </div>

    <div class="form-group row mb-1">
      <label class="col-sm-2 col-form-label">Fec. Vencimiento </label>
      <div class="col-sm-4">
        <input name="fvencimiento" id="fvencimiento" type="date" class="form-control form-control-sm" value="">
      </div>
    </div>
  <?php endif ?>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

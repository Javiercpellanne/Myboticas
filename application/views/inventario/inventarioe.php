<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-0">
    <span class="col-sm-2 col-form-label">Producto</span>
    <div class="col-sm-10">
      <h4 class="mb-0"><b><?php echo $datos->descripcion; ?></b></h4>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="munidades" class="col-sm-2 col-form-label">Cantidad</label>
    <div class="col-sm-2">
      <input type="text" class="form-control form-control-sm text-right" id="munidades" name="munidades" value="<?php echo $datos->cantidad; ?>" required>
    </div>
  </div>

  <input type="hidden" name="mactivar" id="mactivar" value="<?php echo $producto->lote; ?>">
  <div id="mdetalle" class="form-group" <?php if ($producto->lote==0){ echo 'style="display: none;"';} ?>>
    <div class="row">
      <label for="mlote" class="col-sm-2 col-form-label">Codigo Lote </label>
      <div class="col-sm-3">
        <input name="mlote" id="mlote" type="text" class="form-control form-control-sm" value="<?php echo $datos->lote; ?>">
      </div>

      <label for="mfecha" class="col-sm-3 col-form-label">Fec. Vencimiento </label>
      <div class="col-sm-4">
        <input name="mfecha" id="mfecha" type="date" class="form-control form-control-sm" value="<?php echo $datos->fvencimiento; ?>">
      </div>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_productoi();">CERRAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

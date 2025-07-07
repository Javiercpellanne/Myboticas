<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <span class="col-sm-2 control-label">Comprobante</span>
    <div class="col-sm-5">
      <?php if ($id!=null): ?>
        <h4 class="my-0"><b><?php echo $datos->ncomprobante; ?></b></h4>
      <?php else: ?>
        <select name="tcomprobante" id="tcomprobante" class="form-control form-control-sm" required>
          <option value="" <?php echo set_value_select($datos,'tcomprobante','',$datos->tcomprobante) ?>>::Selec</option>
          <?php foreach ($tcomprobantes as $tcomprobante) {?>
          <option value="<?php echo $tcomprobante->id ?>" <?php echo set_value_select($datos,'tcomprobante',$tcomprobante->id,$datos->tcomprobante) ?>><?php echo $tcomprobante->descripcion ?></option>
          <?php  }  ?>
        </select>
      <?php endif ?>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="serie" class="col-sm-2 control-label">Serie*</label>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm" id="serie" name="serie" value="<?php echo set_value_input($datos,'serie',$datos->serie); ?>" required>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

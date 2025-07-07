<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="ffinal" class="col-sm-2 col-form-label">Fecha Final</label>
    <div class="col-sm-3">
      <input name="ffinal" type="text" id="ffinal" class="form-control form-control-sm" value="<?php echo date("Y-m-d H:i:s"); ?>" readonly/>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="mfinal" class="col-sm-2 control-label">Monto Final*</label>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm" id="mfinal" name="mfinal" value="" required>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

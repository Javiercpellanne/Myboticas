<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="finicial" class="col-sm-2 col-form-label">Fecha Inicial</label>
    <div class="col-sm-3">
      <input name="finicial" type="text" id="finicial" class="form-control form-control-sm" value="<?php echo date("Y-m-d H:i:s"); ?>" readonly/>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="minicial" class="col-sm-2 col-form-label">Monto Inicial</label>
    <div class="col-sm-2">
      <input name="minicial" type="text" id="minicial" class="form-control form-control-sm" value="" required/>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="tdocumento" class="col-sm-3 control-label">Tipo Doc. Identidad*</label>
    <div class="col-sm-3">
      <select class="form-control form-control-sm" id="tdocumento" name="tdocumento" required>
        <?php foreach ($identidades as $identidad): ?>
          <option value="<?php echo $identidad->id; ?>" <?php echo set_value_select($datos,"tdocumento",$identidad->id,$datos->tdocumento); ?>><?php echo $identidad->descripcion; ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="documento" class="col-sm-3 control-label">Número*</label>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm" id="documento" name="documento" value="<?php echo set_value_input($datos,'documento',$datos->documento); ?>" required>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="nombres" class="col-sm-3 control-label">Nombres*</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" value="<?php echo set_value_input($datos,'nombres',$datos->nombres); ?>" required>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

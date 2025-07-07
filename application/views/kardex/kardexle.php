<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-0">
    <label for="serie" class="col-sm-2 control-label">Producto</label>
    <div class="col-sm-10">
      <h5><b><?php echo $datos->descripcion; ?></b></h5>
    </div>
  </div>

  <div class="form-group row mb-2">
    <label for="entradaf" class="col-sm-2 control-label">Entrada Fisica*</label>
    <div class="col-sm-3 text-right">
      <?php echo $datos->entradaf; ?>
    </div>

    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm text-right" id="entradaf" name="entradaf" value="<?php echo $datos->entradaf; ?>">
    </div>
  </div>

  <div class="form-group row mb-2">
    <label for="salidaf" class="col-sm-2 control-label">Salida Fisica*</label>
    <div class="col-sm-3 text-right">
      <?php echo $datos->salidaf; ?>
    </div>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm text-right" id="salidaf" name="salidaf" value="<?php echo $datos->salidaf; ?>">
    </div>
  </div>

  <div class="form-group row mb-2">
    <label for="saldof" class="col-sm-2 control-label">Saldo Fisico*</label>
    <div class="col-sm-3 text-right">
      <?php echo $datos->saldof; ?>
    </div>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm text-right" id="saldof" name="saldof" value="<?php echo $this->input->post('saldof',true); ?>">
    </div>
  </div>
  <input type="hidden" id="valor" name="valor" value="1">

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

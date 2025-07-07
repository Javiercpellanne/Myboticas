<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <input type="hidden" name="tipo" id="tipo" value="I">
  <div class="form-group row mb-1">
    <label for="comprobante" class="col-sm-2 col-form-label">Tipo*</label>
    <div class="col-sm-5">
      <select name="comprobante" id="comprobante" class="form-control form-control-sm" required>
        <option value="">::Selec</option>
        <?php foreach ($comprobantes as $comprobante): ?>
          <option value="<?php echo $comprobante->id ?>"><?php echo $comprobante->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="numero" class="col-sm-2 col-form-label">Numero*</label>
    <div class="col-sm-4" >
       <input name="numero" type="text" id="numero" value="" class="form-control form-control-sm" required/>
    </div>

    <label for="fecha" class="col-sm-2 col-form-label">Fecha Emision*</label>
    <div class="col-sm-3">
      <input name="fecha" type="date" id="fecha" class="form-control form-control-sm" value="<?php echo date("Y-m-d") ?>" required/>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="motivo" class="col-sm-2 col-form-label">Motivo*</label>
    <div class="col-sm-10">
      <input name="motivo" type="text" id="motivo" class="form-control form-control-sm" value="" required/>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="importe" class="col-sm-2 col-form-label">Importe*</label>
    <div class="col-sm-3">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text text-sm py-0">S/.</span>
        </div>
        <input name="importe" type="text" id="importe" class="form-control form-control-sm" value="" required/>
      </div>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="mpago" class="col-sm-2 col-form-label">Medio Pago</label>
    <div class="col-sm-4">
      <select name="mpago" id="mpago" class="form-control form-control-sm" required>
        <?php foreach ($mpagos as $mpago): ?>
          <option value="<?php echo $mpago->id ?>"><?php echo $mpago->descripcion; ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

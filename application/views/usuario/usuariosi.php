<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="nombres" class="col-sm-3 control-label">Nombres*</label>
    <div class="col-sm-7">
      <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" value="<?php echo $datos->nombres; ?>" required>
    </div>
  </div>

  <div class="form-group row">
    <label for="establecimiento[]" class="col-sm-3 control-label">Acceso Establecimiento*</label>
    <div class="col-sm-8">
      <select name="establecimiento[]" id="establecimiento[]" class="form-control select2" style="width: 100%" multiple required>
        <?php foreach ($establecimientos as $establecimiento): ?>
          <?php if ($establecimiento->id!=1): ?>
          <?php endif ?>
          <option value="<?php echo $establecimiento->id ?>" <?php echo set_value_smultiple($establecimiento->id,$datos->idestablecimiento,",") ?>><?php echo $establecimiento->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="perfil" class="col-sm-3 control-label">Perfil Vista*</label>
    <div class="col-sm-4">
      <select name="perfil" id="perfil" class="form-control form-control-sm" required>
        <option value="" <?php echo set_value_select($datos,'perfil','',$datos->perfil) ?>>::Selec</option>
        <option value="admin" <?php echo set_value_select($datos,'perfil','admin',$datos->perfil) ?>>Administrador</option>
        <option value="vendedor" <?php echo set_value_select($datos,'perfil','vendedor',$datos->perfil) ?>>Vendedor</option>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="anulacion" class="col-sm-3 control-label">Anulacion Venta</label>
    <div class="col-sm-4">
      <input type="checkbox" name="anulacion" id="anulacion" value="1" <?php echo set_value_check($datos,'anulacion',$datos->anulacion,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
    </div>
  </div>

  <?php if ($id==null): ?>
    <hr class="my-2">
    <div class="form-group row mb-1">
      <label for="usuario" class="col-sm-3 control-label">Usuario*</label>
      <div class="col-sm-5">
        <input type="text" class="form-control form-control-sm" id="usuario" name="usuario" value="" required>
      </div>
    </div>

    <div class="form-group row mb-1">
      <label for="clave" class="col-sm-3 control-label">Contraseña*</label>
      <div class="col-sm-5">
        <input type="password" class="form-control form-control-sm" id="clave" name="clave" value="" required>
      </div>
    </div>

    <div class="form-group row mb-1">
      <label for="clavn" class="col-sm-3 control-label">Repetir Contraseña*</label>
      <div class="col-sm-5">
        <input name="clavn" type="password" id="clavn" class="form-control form-control-sm" value="" required/>
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

<script type="text/javascript">
  $('.select2').select2();

  $("input[data-bootstrap-switch]").each(function(){
    $(this).bootstrapSwitch('state', $(this).prop('checked'));
  });
</script>


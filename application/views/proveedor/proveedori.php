<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div id="mensajeerror"></div>
  <div class="form-group row mb-1">
    <label for="documento" class="col-sm-3 control-label">Documento*</label>
    <div class="col-sm-2">
        <select class="form-control form-control-sm" id="tipo" name="tipo" required>
          <option value="6" <?php echo set_value_select($datos,'tipo',"6",$datos->tdocumento) ?>>RUC</option>
          <option value="1" <?php echo set_value_select($datos,'tipo',"1",$datos->tdocumento) ?>>DNI</option>
        </select>
    </div>
    <div class="col-sm-4">
      <div class="input-group">
        <input type="text" class="form-control form-control-sm" id="documento" name="documento" value="<?php echo set_value_input($datos,'documento',$datos->documento); ?>" required>
        <div class="input-group-append">
          <button class="btn btn-success btn-sm" type="button" onclick="dcliente('<?php echo base_url(); ?>cliente/busDatos')"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="nombres" class="col-sm-3 control-label">Nombres*</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" value="<?php echo set_value_input($datos,'nombres',$datos->nombres); ?>" required>
    </div>
  </div>

  <div class="row">
    <div class="form-group col-md-4">
      <label for="departamento" class="control-label">Departamento*</label>
      <select class="form-control form-control-sm" id="departamento" name="departamento" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busProvincia',this.value,'provincia')">
        <option value="" <?php echo set_value_select($datos,'departamento',"",$datos->iddepartamento) ?>>::Selecc</option>
        <?php foreach ($departamentos as $departamento): ?>
          <option value="<?php echo $departamento->id ?>" <?php echo set_value_select($datos,'departamento',$departamento->id,$datos->iddepartamento) ?>><?php echo $departamento->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="form-group col-md-4">
      <label for="provincia" class="control-label">Provincia*</label>
      <select class="form-control form-control-sm" id="provincia" name="provincia" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busDistrito',this.value,'distrito')">
        <option value="" <?php echo set_value_select($datos,"provincia","",$datos->idprovincia) ?>>::Selecc</option>
        <?php foreach ($provincias as $provincia): ?>
          <option value="<?php echo $provincia->id ?>" <?php echo set_value_select($datos,'provincia',$provincia->id,$datos->idprovincia) ?>><?php echo $provincia->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="form-group col-md-4">
      <label for="distrito" class="control-label">Distrito*</label>
      <select class="form-control form-control-sm" id="distrito" name="distrito">
        <option value="" <?php echo set_value_select($datos,'distrito',"",$datos->iddistrito) ?>>::Selecc</option>
        <?php foreach ($distritos as $distrito): ?>
          <option value="<?php echo $distrito->id ?>" <?php echo set_value_select($datos,'distrito',$distrito->id,$datos->iddistrito) ?>><?php echo $distrito->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="direccion" class="col-sm-3 control-label">Direccion*</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="direccion" name="direccion" value="<?php echo set_value_input($datos,'direccion',$datos->direccion); ?>">
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="telefono" class="col-sm-3 control-label">Telefono</label>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="<?php echo set_value_input($datos,'telefono',$datos->telefono); ?>">
    </div>

    <label for="email" class="col-sm-2 control-label">Correo</label>
    <div class="col-sm-4">
      <input type="text" class="form-control form-control-sm" id="email" name="email" value="<?php echo set_value_input($datos,'email',$datos->email); ?>" autocomplete="off">
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

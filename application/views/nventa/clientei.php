<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"fdatos", "id"=>"fdatos", "onsubmit"=>"envioCliente('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="documento" class="col-sm-3 control-label">Documento*</label>
    <div class="col-sm-2">
      <select class="form-control form-control-sm" id="tipo" name="tipo" required>
        <?php foreach ($identidades as $identidad): ?>
          <option value="<?php echo $identidad->id; ?>" <?php echo set_value_select(1,'tipo',$identidad->id,1) ?>><?php echo $identidad->descripcion; ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="col-sm-4">
      <div class="input-group">
        <input type="text" class="form-control form-control-sm" id="documento" name="documento" value="" autocomplete="off" maxlength="11" required>
        <div class="input-group-append">
          <button class="btn btn-success btn-sm" type="button" onclick="dcliente('<?php echo base_url(); ?>cliente/busDatos')"><i class="fa fa-search" aria-hidden="true"></i></button>
        </div>
      </div>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="nombres" class="col-sm-3 control-label">Razon Social*</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="nombres" name="nombres" value="" required>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="ncomercial" class="col-sm-3 control-label">Nombre Comercial</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="ncomercial" name="ncomercial" value="">
    </div>
  </div>

  <div class="row">
    <div class="form-group col-md-4">
      <label for="departamento" class="control-label">Departamento*</label>
      <select class="form-control form-control-sm" id="departamento" name="departamento" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busProvincia',this.value,'provincia')">
        <option value="">::Selecc</option>
        <?php foreach ($departamentos as $departamento): ?>
          <option value="<?php echo $departamento->id ?>"><?php echo $departamento->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="form-group col-md-4">
      <label for="provincia" class="control-label">Provincia*</label>
      <select class="form-control form-control-sm" id="provincia" name="provincia" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busDistrito',this.value,'distrito')">
        <option value="">::Selecc</option>
        <?php foreach ($provincias as $provincia): ?>
          <option value="<?php echo $provincia->id ?>"><?php echo $provincia->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>

    <div class="form-group col-md-4">
      <label for="distrito" class="control-label">Distrito*</label>
      <select class="form-control form-control-sm" id="distrito" name="distrito">
        <option value="">::Selecc</option>
        <?php foreach ($distritos as $distrito): ?>
          <option value="<?php echo $distrito->id ?>"><?php echo $distrito->descripcion ?></option>
        <?php endforeach ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="direccion" class="col-sm-3 control-label">Direccion*</label>
    <div class="col-sm-9">
      <input type="text" class="form-control form-control-sm" id="direccion" name="direccion" value="">
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="telefono" class="col-sm-3 control-label">Telefono</label>
    <div class="col-sm-3">
      <input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="">
    </div>

    <label for="email" class="col-sm-2 control-label">Correo</label>
    <div class="col-sm-4">
      <input type="text" class="form-control form-control-sm" id="email" name="email" value="" autocomplete="off">
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

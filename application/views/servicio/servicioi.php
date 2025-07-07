<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <div class="form-group row mb-1">
    <label for="categoria" class="col-sm-2 col-form-label">Categoria*</label>
    <div class="col-sm-8">
      <select name="categoria" id="categoria" class="form-control form-control-sm" required>
        <option value="" <?php echo set_value_select($datos,'categoria','',$datos->idcategoria) ?>>::Selec</option>
        <?php foreach ($categorias as $categoria) {?>
        <option value="<?php echo $categoria->id ?>" <?php echo set_value_select($datos,'categoria',$categoria->id,$datos->idcategoria) ?>><?php echo $categoria->descripcion; ?></option>
        <?php  }  ?>
      </select>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="descripcion" class="col-sm-2 col-form-label">Descripcion*</label>
    <div class="col-sm-10">
      <input type="text" class="form-control form-control-sm" id="descripcion" name="descripcion" value="<?php echo set_value_input($datos,'descripcion',$datos->descripcion); ?>" required>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="tafectacion" class="col-sm-2 col-form-label">Afectacion IGV*</label>
    <div class="col-sm-5">
      <select name="tafectacion" id="tafectacion" class="form-control form-control-sm" required>
        <option value="" <?php echo set_value_select($datos,'tafectacion','',$datos->tafectacion) ?>>::Selec</option>
        <?php foreach ($tafectaciones as $tafectacion) {?>
          <option value="<?php echo $tafectacion->id ?>" <?php echo set_value_select($datos,'tafectacionc',$tafectacion->id,$datos->tafectacion) ?>><?php echo $tafectacion->descripcion ?></option>
        <?php  }  ?>
      </select>
    </div>
  </div>

  <?php $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $datos->pventa; ?>
  <div class="form-group row mb-1">
    <label for="pventa" class="col-sm-2 col-form-label">Precio Unitario*</label>
    <div class="col-sm-3">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text text-sm py-0">S/</span>
        </div>
        <input type="text" class="form-control form-control-sm text-right" id="pventa" name="pventa" value="<?php echo $pventa; ?>" required>
      </div>
    </div>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

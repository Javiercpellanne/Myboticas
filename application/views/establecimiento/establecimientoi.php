<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
	<div class="form-group row mb-1">
		<label for="descripcion" class="col-md-2 col-form-label">Descripción*</label>
    <div class="col-md-5">
			<input type="text" class="form-control form-control-sm" id="descripcion" name="descripcion" value="<?php echo $datos->descripcion; ?>" required>
		</div>

		<label for="codigo" class="col-md-3 col-form-label">Código Domicilio Fiscal*</label>
		<div class="col-md-2">
			<input type="text" class="form-control form-control-sm" id="codigo" name="codigo" value="<?php echo $datos->codigo; ?>" required>
		</div>
	</div>

	<div class="row">
      <div class="form-group col-md-4 mb-2">
      	<label for="departamento" class="col-form-label">Departamento*</label>
		<select class="form-control form-control-sm" id="departamento" name="departamento" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busProvincia',this.value,'provincia')">
			<option value="" <?php echo set_value_select($datos,'departamento',"",$datos->iddepartamento) ?>>::Selecc</option>
			<?php foreach ($departamentos as $departamento): ?>
				<option value="<?php echo $departamento->id ?>" <?php echo set_value_select($datos,'departamento',$departamento->id,$datos->iddepartamento) ?>><?php echo $departamento->descripcion ?></option>
			<?php endforeach ?>
		</select>
      </div>

      <div class="form-group col-md-4 mb-2">
      	<label for="provincia" class="col-form-label">Provincia*</label>
				<select class="form-control form-control-sm" id="provincia" name="provincia" onchange="bubicacion('<?php echo base_url(); ?>establecimiento/busDistrito',this.value,'distrito')">
					<option value="" <?php echo set_value_select($datos,"provincia","",$datos->idprovincia) ?>>::Selecc</option>
					<?php foreach ($provincias as $provincia): ?>
						<option value="<?php echo $provincia->id ?>" <?php echo set_value_select($datos,'provincia',$provincia->id,$datos->idprovincia) ?>><?php echo $provincia->descripcion ?></option>
					<?php endforeach ?>
				</select>
      </div>

      <div class="form-group col-md-4 mb-2">
      	<label for="distrito" class="col-form-label">Distrito* </label>
				<select class="form-control form-control-sm" id="distrito" name="distrito">
					<option value="" <?php echo set_value_select($datos,'distrito',"",$datos->iddistrito) ?>>::Selecc</option>
					<?php foreach ($distritos as $distrito): ?>
						<option value="<?php echo $distrito->id ?>" <?php echo set_value_select($datos,'distrito',$distrito->id,$datos->iddistrito) ?>><?php echo $distrito->descripcion ?></option>
					<?php endforeach ?>
				</select>
      </div>
    </div>

	<div class="form-group row mb-1">
		<label for="direccion" class=" col-sm-2 col-form-label">Direccion*</label>
  	<div class="col-md-10">
			<input type="text" class="form-control form-control-sm" id="direccion" name="direccion" value="<?php echo $datos->direccion; ?>" required>
		</div>
	</div>

	<div class="form-group row mb-1">
		<label for="telefono" class="col-md-2 col-form-label">Telefono*</label>
  	<div class="col-md-4">
			<input type="text" class="form-control form-control-sm" id="telefono" name="telefono" value="<?php echo $datos->telefono; ?>" required>
		</div>

		<label for="email" class="col-md-2 col-form-label">Email</label>
		<div class="col-md-4">
			<input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo $datos->email; ?>" autocomplete="off">
		</div>
	</div>

	<div class="form-group row mb-1">
		<label for="cdigemid" class="col-md-4 col-form-label">Código Establecimiento Digemid</label>
		<div class="col-md-4">
			<input type="text" class="form-control form-control-sm" id="cdigemid" name="cdigemid" value="<?php echo $datos->cdigemid; ?>">
		</div>
	</div>

	<?php if ($empresa->lestablecimiento==1): ?>
	<div class="form-group row mb-1">
		<label class="col-md-4 col-form-label">Logo</label>
		<div class="col-sm-5">
			<input type="file" id="logo" name="logo" value=""> <br>
      <span class="text-danger">Las medidas recomendables son 700x300</span>
		</div>
	</div>

	<div class="form-group row mb-1">
		<label class="col-md-4 col-form-label">Logo Ticket</label>
		<div class="col-sm-5">
			<input type="file" id="lticket" name="lticket" value=""> <br>
			<span class="text-danger">Las medidas recomendables son 700x300</span>
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

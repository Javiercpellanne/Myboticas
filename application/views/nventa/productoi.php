<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"fdatos", "id"=>"fdatos", "onsubmit"=>"envioProducto('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
<div id="busnombres"></div>
<div class="form-group row mb-1">
	<label for="categoria" class="control-label col-sm-2">Categoria*</label>
	<div class="col-sm-6">
		<select name="categoria" id="categoria" class="form-control form-control-sm" required>
			<option value="">::Selec</option>
			<?php foreach ($categorias as $categoria) {?>
				<option value="<?php echo $categoria->id ?>"><?php echo $categoria->descripcion ?></option>
			<?php  }  ?>
		</select>
	</div>
</div>

<div class="form-group row mb-1">
	<label for="productos" class="col-sm-2 col-form-label">Descripcion*</label>
	<div class="col-sm-10">
		<input type="text" class="form-control form-control-sm" id="productos" name="productos" value="" required>
	</div>
</div>

<div class="form-group row mb-1">
	<label for="laboratorio" class="col-sm-2 col-form-label">Laboratorio </label>
	<div class="col-sm-6">
		<select name="laboratorio" id="laboratorio" class="form-control form-control-sm">
			<option value="">::Selec</option>
			<?php foreach ($laboratorios as $laboratorio) {?>
				<option value="<?php echo $laboratorio->id ?>"><?php echo $laboratorio->descripcion; ?></option>
			<?php  }  ?>
		</select>
	</div>

	<label for="mstock" class="col-sm-2 col-form-label">Stock Minimo*</label>
	<div class="col-sm-2">
		<input type="text" class="form-control form-control-sm" id="mstock" name="mstock" value="0" required>
	</div>
</div>

<div class="form-group row mb-1">
	<label class="col-sm-2 col-form-label">Afectacion IGV</label>
  <div class="col-sm-4">
    <select name="tafectacion" id="tafectacion" class="form-control form-control-sm" required>
      <option value="" <?php echo set_value_select(10,'tafectacion','',10) ?>>::Selec</option>
      <?php foreach ($tafectaciones as $tafectacion) {?>
        <option value="<?php echo $tafectacion->id ?>" <?php echo set_value_select(10,'tafectacionc',$tafectacion->id,10) ?>><?php echo $tafectacion->descripcion ?></option>
      <?php  }  ?>
    </select>
  </div>

  <label class="col-sm-2 col-form-label">Codigo Barra </label>
  <div class="col-sm-4">
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text text-sm py-0"><i class="fa fa-barcode"></i></span>
			</div>
			<input id="codbarra" name="codbarra" type="text" class="form-control form-control-sm" placeholder="Codigo Barra" aria-label="Codigo Barra" aria-describedby="basic-addon1" value="" onkeydown="consultaCodigo(event,this.value,'busnombres','<?php echo base_url(); ?>producto/busCodigo');">
		</div>
	</div>
</div>

<div class="form-group row mb-1">
	<label for="stock" class="col-sm-1 col-form-label">Stock*</label>
	<div class="col-sm-2">
		<input type="text" class="form-control form-control-sm" id="stock" name="stock" value="0" required>
	</div>

	<div class="col-sm-2">
		<div class="form-check mt-2">
			<label class="form-check-label">
				<input class="form-check-input" name="lote" type="checkbox" id="lote" value="1" onchange="mostrarLotes(this)">
				¿Maneja Lotes?
			</label>
		</div>
	</div>

	<div id="mostrarlote" class="col-sm-7" style="display: none;">
		<div class="row">
			<div class="col-sm-6">
				<input type="text" class="form-control form-control-sm" id="clote" name="clote" value="" placeholder="Codigo de Lote">
			</div>

			<div class="col-sm-6">
				<input type="date" class="form-control form-control-sm" id="fvencimiento" name="fvencimiento" value="" placeholder="Fecha de Lote">
			</div>
		</div>
  </div>
</div>

<hr class="my-1">
<fieldset class="border border-info mb-2 px-2">
  <legend class="h6 pl-1 mb-0">Precio Compra</legend>

	<div class="form-group row mb-1">
    <label for="compra" class="col-sm-2 col-form-label">Precio Caja*</label>
    <div class="col-sm-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text text-sm py-0">S/.</span>
        </div>
        <input type="text" class="form-control form-control-sm text-right" id="compra" name="compra" value="" onkeyup="divisores('compra','factor','pcompra');" required>
      </div>
    </div>

    <label for="factor" class="col-sm-2 col-form-label">Factor* <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Factor por el cual multiplicara para ingreso a Almacen al elegir precio por caja"></i></label>
    <div class="col-sm-2">
    	<input type="text" class="form-control form-control-sm text-right" id="factor" name="factor" value="1" onkeyup="divisores('compra','factor','pcompra');unidades(this.value);" required>
    </div>

    <label for="pcompra" class="col-sm-2 col-form-label">Precio Unitario*</label>
    <div class="col-sm-2">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text text-sm py-0">S/.</span>
        </div>
        <input type="text" class="form-control form-control-sm text-right" id="pcompra" name="pcompra" value="" readonly>
      </div>
    </div>
  </div>
</fieldset>

<fieldset class="border border-info mb-2 px-2">
	<legend class="h6 pl-1 mb-0">Precio Unidad Venta</legend>

	<div class="form-group row mb-1">
		<label for="pventa" class="col-sm-2 col-form-label">Precio Unitario*</label>
		<div class="col-sm-2">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text text-sm py-0">S/.</span>
				</div>
				<input type="text" class="form-control form-control-sm text-right" id="pventa" name="pventa" value="" onkeyup="margen('pcompra','pventa','factoru','utilidadu');factores('pventa','factor','venta');margen('compra','venta','factoru','utilidadc');" required>
			</div>
		</div>

    <div class="col-sm-2">
      <h4 class="my-0"><input type="text" id="umedidav" name="umedidav" class="campo text-center" value="NIU"></h4>
    </div>

    <div class="col-sm-2">
      <input type="text" class="form-control form-control-sm text-right" id="factoru" name="factoru" value="1" readonly>
    </div>

		<label for="utilidadu" class="col-sm-2 col-form-label">Ganancia</label>
		<div class="col-sm-2">
			<div class="input-group">
				<input type="text" class="form-control form-control-sm text-right" id="utilidadu" name="utilidadu" value="" onkeyup="precios('pcompra','utilidadu','pventa','factoru');">
				<div class="input-group-append">
					<span class="input-group-text text-sm py-0">%</span>
				</div>
			</div>
		</div>
	</div>
</fieldset>

<div id="precios" style="display: none;">
	<fieldset class="border border-info mb-2 px-2">
		<legend class="h6 pl-1 mb-0">Precio Caja Venta</legend>

		<div class="form-group row mb-1">
				<label for="venta" class="col-sm-2 col-form-label">Precio Caja*</label>
			<div class="col-sm-2">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text text-sm py-0">S/.</span>
					</div>
					<input type="text" class="form-control form-control-sm text-right" id="venta" name="venta" value="" onkeyup="margen('compra','venta','factoru','utilidadc');">
				</div>
			</div>

      <div class="col-sm-2">
        <h4 class="my-0"><input type="text" id="umedidac" name="umedidac" class="campo text-center" value="BX"></h4>
      </div>

	    <div class="col-sm-2">
	      <input type="text" class="form-control form-control-sm text-right" id="factorc" name="factorc" value="1" readonly>
	    </div>

				<label for="utilidadc" class="col-sm-2 col-form-label">Ganancia</label>
			<div class="col-sm-2">
				<div class="input-group">
					<input type="text" class="form-control form-control-sm text-right" id="utilidadc" name="utilidadc" value="" onkeyup="precios('compra','utilidadc','venta');">
					<div class="input-group-append">
						<span class="input-group-text text-sm py-0">%</span>
					</div>
				</div>
			</div>
		</div>
	</fieldset>

	<fieldset class="border border-info mb-2 px-2">
		<legend class="h6 pl-1 mb-0">Precio Blister Venta</legend>

		<div class="form-group row mb-1">
				<label for="pblister" class="col-sm-2 col-form-label">Precio Blister</label>
			<div class="col-sm-2">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text text-sm py-0">S/.</span>
					</div>
					<input type="text" class="form-control form-control-sm text-right" id="pblister" name="pblister" value="" onkeyup="margen('pcompra','pblister','utilidadb');">
				</div>
			</div>

      <div class="col-sm-2">
        <h4 class="my-0"><input type="text" id="umedidab" name="umedidab" class="campo text-center" value="PK"></h4>
      </div>

	    <div class="col-sm-2">
	      <input type="text" class="form-control form-control-sm text-right" id="factorb" name="factorb" value="" onkeyup="margen('pcompra','pblister','factorb','utilidadb');">
	    </div>

				<label for="utilidadb" class="col-sm-2 col-form-label">Ganancia</label>
			<div class="col-sm-2">
				<div class="input-group">
					<input type="text" class="form-control form-control-sm text-right" id="utilidadb" name="utilidadb" value="" onkeyup="precios('pcompra','utilidadb','pblister');">
					<div class="input-group-append">
						<span class="input-group-text text-sm py-0">%</span>
					</div>
				</div>
			</div>
		</div>
	</fieldset>
</div>

<hr class="my-1">
<div class="row">
	<div class="form-group col-sm-3">
		<label for="rsanitario" class="col-form-label">Registro Sanitario</label>
		<input type="text" class="form-control form-control-sm" id="rsanitario" name="rsanitario" value="">
	</div>

    <div class="form-group col-sm-5">
		<label for="pactivo" class="col-form-label">Principio Activo</label>
      	<select name="pactivo" id="pactivo" class="form-control form-control-sm">
	        <option value="">::Selec</option>
	        <?php foreach ($pactivos as $pactivo) {?>
	        <option value="<?php echo $pactivo->id ?>"><?php echo $pactivo->descripcion ?></option>
	        <?php  }  ?>
      	</select>
    </div>

    <div class="form-group col-sm-4">
    	<label for="aterapeutica" class="col-form-label">Accion Terapeutica</label>
      	<select name="aterapeutica" id="aterapeutica" class="form-control form-control-sm">
	        <option value="">::Selec</option>
	        <?php foreach ($aterapeuticas as $aterapeutica) {?>
	        <option value="<?php echo $aterapeutica->id ?>"><?php echo $aterapeutica->descripcion ?></option>
	        <?php  }  ?>
      	</select>
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

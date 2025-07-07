<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Ingreso Producto</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>traslado">Traslados Internos</a></li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      	<div class="col-12">
		    <div class="card card-primary card-outline">
		      <div class="card-body">
		        <?php if($this->session->flashdata('mensaje')!=''){ ?>
		          <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
		            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		            <?php echo $this->session->flashdata('mensaje') ?>
		          </div>
		        <?php } ?>

		        <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."traslado/ingresog/".$id."');")); ?>
		          <div class="form-group row mb-1">
		            <label class="col-sm-2 col-form-label">Motivo de Traslado</label>
		            <div class="col-sm-4">
		              <select name="motivo" id="motivo" class="form-control form-control-sm" required>
		                <?php foreach ($motivos as $motivo) {?>
		                  <option value="<?php echo $motivo->id.'-'.$motivo->descripcion ?>"><?php echo $motivo->descripcion ?></option>
		                <?php  }  ?>
		              </select>
		            </div>
		          </div>

		          <div class="table-responsive mb-2" style="height: 450px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
			              <tr>
			                <th width="46%">DESCRIPCION</th>
			                <th width="12%">LOTE</th>
			                <th width="10%">F VCTO</th>
			                <th width="8%">U.M</th>
			                <th width="8%">CANT</th>
	                    <th width="8%">P.U</th>
	                    <th width="8%">IMPORTE</th>
			              </tr>
			            </thead>
			            <tbody id="grilla">
			              <?php foreach ($listas as $lista): ?>
			                <?php
			                  $producto=$this->producto_model->mostrar(array("p.id"=>$lista->idproducto));
			                  $factor= $lista->unidad=="BX" ? $producto->factor : 1 ;
			                  $cantidad=$lista->cantidad;
			                  $precio=$lista->precio;

			                  if ($lista->unidad=="BX") {
			                    $almacenc=$cantidad*$factor;
			                    $almacenp=$precio/$factor;
			                  }else{
			                    $almacenc=$cantidad;
			                    $almacenp=$precio;
			                  }
			                  ?>
			                <tr>
			                  <td>
			                    <input type="hidden" name="id[]" value="<?php echo $lista->id ?>">
			                    <input type="hidden" name="idproducto[]" value="<?php echo $lista->idproducto ?>">
			                    <input type="hidden" name="descripcion[]" value="<?php echo $lista->descripcion ?>">
			                    <?php echo $lista->descripcion ?>
			                  </td>
			                  <td><?php echo $lista->lote ?></td>
			                  <td>
			                    <?php echo $lista->fvencimiento ?>
			                    <input type="hidden" name="fvencimiento[]" value="<?php echo $lista->fvencimiento ?>">
			                    <input type="hidden" name="lote[]" value="<?php echo $lista->lote ?>">
			                    <input type="hidden" name="clote[]" value="<?php echo $lista->clote ?>">
			                  </td>
			                  <td>
			                    <input type="hidden" name="almacenc[]" value="<?php echo $almacenc ?>">
			                    <input type="hidden" name="almacenp[]" value="<?php echo $almacenp ?>">
			                    <input type="hidden" name="unidad[]" value="<?php echo $lista->unidad ?>">
			                    <?php echo $lista->unidad ?>
			                  </td>
			                  <td>
			                    <input type="hidden" name="cantidad[]" value="<?php echo $lista->cantidad ?>">
			                    <?php echo $lista->cantidad ?>
			                  </td>
			                  <td align="right">
			                  	<input type="hidden" name="precio[]" value="<?php echo $lista->precio ?>">
			                  	<?php echo $lista->precio ?>
			                  </td>
			                  <td align="right">
			                  	<input type="hidden" name="importe[]" value="<?php echo $lista->importe ?>">
			                  	<?php echo $lista->importe ?>
			                  </td>
			                </tr>
			              <?php endforeach ?>
			            </tbody>
			          </table>
			        </div>

		          <div class="form-group row mb-0">
		            <div class="col-sm-12 text-center">
		              <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR"/>
		            </div>
		          </div>
		        <?php echo form_close(); ?>
		      </div>
		    </div>
		</div>
	</div>
  </div>
</section>

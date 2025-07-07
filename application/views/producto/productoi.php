<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Producto <button class="btn btn-info btn-sm py-0" type="button" data-toggle="modal" data-target="#busconsulta"><i class="fa fa-search"></i> Buscador Productos Farmaceuticos</button></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>producto">Producto</a></li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Informacion General Producto <small><code>(<?php if ($cantidades->stock!=0){echo 'Control de Lotes esta desabilitado por tener stock diferente de cero';} ?>)</code></small></h3>
          </div>

          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "enctype"=>"multipart/form-data")); ?>
              <div class="form-group row mb-1">
                <label for="categoria" class="col-sm-2 col-form-label">Categoria*</label>
                <div class="col-sm-5">
                  <select name="categoria" id="categoria" class="form-control form-control-sm" required>
                    <option value="" <?php echo set_value_select($datos,'categoria','',$datos->idcategoria) ?>>::Selec</option>
                    <?php foreach ($categorias as $categoria) {?>
                    <option value="<?php echo $categoria->id ?>" <?php echo set_value_select($datos,'categoria',$categoria->id,$datos->idcategoria) ?>><?php echo $categoria->descripcion; ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="clasificacion" class="col-sm-1 col-form-label">Clasificacion*</label>
                <div class="col-sm-2">
                  <select name="clasificacion" id="clasificacion" class="form-control form-control-sm" onchange="mostrarGenerico('<?php echo base_url(); ?>producto/busGenericos')" required>
                    <option value="" <?php echo set_value_select($datos,'clasificacion','',$datos->clasificacion) ?>>::Selec</option>
                    <option value="1" <?php echo set_value_select($datos,'clasificacion','1',$datos->clasificacion) ?>>Generico</option>
                    <option value="2" <?php echo set_value_select($datos,'clasificacion','2',$datos->clasificacion) ?>>Marca</option>
                    <option value="0" <?php echo set_value_select($datos,'clasificacion','0',$datos->clasificacion) ?>>Otros</option>
                  </select>
                </div>

                <div class="col-sm-2 text-center">
                  <div class="custom-control custom-switch mt-2">
                      <input class="custom-control-input" name="vsujeta" type="checkbox" id="vsujeta" value="1" <?php echo set_value_check($datos,'vsujeta',$datos->vsujeta,1); ?>>
                    <label class="custom-control-label" for="vsujeta">Venta con Receta</label>
                  </div>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="descripcion" class="col-sm-2 col-form-label">Descripcion*</label>
                <div class="col-sm-5">
                  <input type="text" class="form-control form-control-sm" id="descripcion" name="descripcion" value="<?php echo set_value_input($datos,'descripcion',$datos->descripcion); ?>" required>
                </div>

                <label for="laboratorio" class="col-sm-1 col-form-label">Laboratorio </label>
                <div class="col-sm-4">
                  <select name="laboratorio" id="laboratorio" class="form-control form-control-sm select2">
                    <option value="" <?php echo set_value_select($datos,'laboratorio','',$datos->idlaboratorio) ?>>::Selec</option>
                    <?php foreach ($laboratorios as $laboratorio) {?>
                      <option value="<?php echo $laboratorio->id ?>" <?php echo set_value_select($datos,'laboratorio',$laboratorio->id,$datos->idlaboratorio) ?>><?php echo $laboratorio->descripcion; ?></option>
                    <?php  }  ?>
                  </select>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="codbarra" class="col-sm-2 col-form-label">Codigo Barra </label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text text-sm py-0"><i class="fa fa-barcode"></i></span>
                    </div>
                    <input id="codbarra" name="codbarra" type="text" class="form-control form-control-sm" placeholder="Codigo Barra" aria-label="Codigo Barra" aria-describedby="basic-addon1" value="<?php echo set_value_input($datos,'codbarra',$datos->codbarra); ?>" onkeydown="consultaCodigo(event,'<?php echo base_url(); ?>producto/busCodigo',this.value);">
                      <div class="input-group-append">
                        <button type="button" class="btn btn-info btn-sm" title="Generar Codigo Barra" data-toggle="tooltip" data-placement="bottom" onclick="generarCodigo('<?php echo base_url(); ?>producto/generarCodigo')" <?php if ($datos->codbarra) {echo 'disabled';} ?>><i class="fa fa-bars"></i></button>
                      </div>
                  </div>
                </div>

                <label for="rsanitario" class="col-sm-2 col-form-label">Registro Sanitario</label>
                <div class="col-sm-2">
                  <input type="text" class="form-control form-control-sm" id="rsanitario" name="rsanitario" value="<?php echo $datos->rsanitario; ?>">
                </div>

                <label for="cdigemid" class="col-sm-2 col-form-label">Codigo DIGEMID</label>
                <div class="col-sm-2">
                  <input type="text" class="form-control form-control-sm" id="cdigemid" name="cdigemid" value="<?php echo set_value_input($datos,'cdigemid',$datos->cdigemid); ?>" placeholder="Codigo de DIGEMID">
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="tafectacion" class="col-sm-2 col-form-label">Afectacion IGV*</label>
                <div class="col-sm-3">
                  <select name="tafectacion" id="tafectacion" class="form-control form-control-sm" required>
                    <option value="" <?php echo set_value_select($datos,'tafectacion','',$datos->tafectacion) ?>>::Selec</option>
                    <?php foreach ($tafectaciones as $tafectacion) {?>
                      <option value="<?php echo $tafectacion->id ?>" <?php echo set_value_select($datos,'tafectacionc',$tafectacion->id,$datos->tafectacion) ?>><?php echo $tafectacion->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="mstock" class="col-sm-1 col-form-label">Stock Minimo*</label>
                <div class="col-sm-1">
                  <input type="text" class="form-control form-control-sm" id="mstock" name="mstock" value="<?php echo $datos->mstock; ?>" required>
                </div>

                <label for="ubicacion" class="col-sm-1 col-form-label">Ubicacion</label>
                <div class="col-sm-2">
                  <select name="ubicacion" id="ubicacion" class="form-control form-control-sm">
                    <option value="" <?php echo set_value_select($datos,'ubicacion','',$datos->idubicacion) ?>>::Selec</option>
                    <?php foreach ($ubicaciones as $ubicacion) {?>
                      <option value="<?php echo $ubicacion->id ?>" <?php echo set_value_select($datos,'ubicacionc',$ubicacion->id,$datos->idubicacion) ?>><?php echo $ubicacion->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <input type="hidden" id="stock" name="stock" value="<?php echo $cantidades->stock; ?>">
                <div class="col-sm-2">
                  <div class="custom-control custom-switch mt-2">
                      <input class="custom-control-input" name="lote" type="checkbox" id="lote" value="1" <?php echo set_value_check($datos,'lote',$datos->lote,1); ?> <?php if ($cantidades->stock!=0){echo 'disabled';} ?>>
                    <label class="custom-control-label" for="lote">¿Maneja Lotes?</label>
                  </div>
                </div>
              </div>

              <hr class="my-1">
              <fieldset class="border border-info mb-1 px-2">
                <legend class="h6 pl-1 mb-0">Precio Compra</legend>

                <div class="form-group row mb-1">
                  <label for="compra" class="col-sm-2 col-form-label">Precio Caja*</label>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text text-sm py-0">S/</span>
                      </div>
                      <input type="text" class="form-control form-control-sm text-right" id="compra" name="compra" value="<?php echo set_value_input($datos,'compra',$datos->compra); ?>" onkeyup="divisores('compra','factor','pcompra');" required>
                    </div>
                  </div>

                  <label for="factor" class="col-sm-2 col-form-label">Factor (Cant x Caja)* <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Factor por el cual multiplicara para ingreso a Almacen al elegir precio por caja"></i></label>
                  <div class="col-sm-1">
                    <input type="text" min="1" class="form-control form-control-sm text-right" id="factor" name="factor" value="<?php echo set_value_input($datos,'factor',$datos->factor); ?>" onkeyup="divisores('compra','factor','pcompra');unidades(this.value);" required>
                  </div>

                  <label for="pcompra" class="col-sm-2 col-form-label">Precio Unitario*</label>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text text-sm py-0">S/</span>
                      </div>
                      <input type="text" class="form-control form-control-sm text-right" id="pcompra" name="pcompra" value="<?php echo $datos->pcompra; ?>" readonly>
                    </div>
                  </div>
                </div>
              </fieldset>

              <fieldset class="border border-info mb-1 px-2">
                <legend class="h6 pl-1 mb-0">Precio Unidad Venta</legend>

                <?php $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $datos->pventa; ?>
                <div class="form-group row mb-1">
                  <label for="pventa" class="col-sm-2 col-form-label">Precio Unitario*</label>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text text-sm py-0">S/</span>
                      </div>
                      <input type="text" class="form-control form-control-sm text-right" id="pventa" name="pventa" value="<?php echo $pventa; ?>" onkeyup="margen('pcompra','pventa','factoru','utilidadu');factores('pventa','factor','venta');margen('compra','venta','factoru','utilidadc');" required>
                    </div>
                  </div>

                  <div class="col-sm-2">
                    <h4 class="my-0"><input type="text" id="umedidav" name="umedidav" class="campo text-center" value="NIU"></h4>
                  </div>

                  <label for="factoru" class="col-sm-2 col-form-label">Factor (Unidades)</label>
                  <div class="col-sm-1">
                    <input type="text" class="form-control form-control-sm text-right" id="factoru" name="factoru" value="1" readonly>
                  </div>

                  <?php $margenu=gananciav($pventa,$datos->pcompra,1); ?>
                  <label for="utilidadu" class="col-sm-1 col-form-label">Margen Ganancia</label>
                  <div class="col-sm-2">
                    <div class="input-group">
                      <input type="text" class="form-control form-control-sm text-right" id="utilidadu" name="utilidadu" value="<?php echo $margenu; ?>" onkeyup="precios('pcompra','utilidadu','pventa','factoru');validarGanancia(this);">
                      <div class="input-group-append">
                        <span class="input-group-text text-sm py-0">%</span>
                      </div>
                    </div>
                  </div>
                </div>
              </fieldset>

              <div id="precios" <?php echo $datos->factor==1 ? 'style="display: none;"': 'style="display: block;"'; ?>>
                <fieldset class="border border-info mb-1 px-2">
                  <legend class="h6 pl-1 mb-0">Precio Caja Venta</legend>

                  <?php $venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $datos->venta; ?>
                  <div class="form-group row mb-1">
                    <label for="venta" class="col-sm-2 col-form-label">Precio Caja*</label>
                    <div class="col-sm-2">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text text-sm py-0">S/</span>
                        </div>
                        <input type="text" class="form-control form-control-sm text-right" id="venta" name="venta" value="<?php echo $venta; ?>" onkeyup="margen('compra','venta','factoru','utilidadc');">
                      </div>
                    </div>

                    <div class="col-sm-2">
                      <h4 class="my-0"><input type="text" id="umedidac" name="umedidac" class="campo text-center" value="BX"></h4>
                    </div>

                    <label for="factorc" class="col-sm-2 col-form-label">Factor (Unidades)</label>
                    <div class="col-sm-1">
                      <input type="text" class="form-control form-control-sm text-right" id="factorc" name="factorc" value="<?php echo set_value_input($datos,'factorc',$datos->factor); ?>" readonly>
                    </div>

                    <?php $margenc=gananciav($venta,$datos->compra,1);?>
                    <label for="utilidadc" class="col-sm-1 col-form-label">Margen Ganancia</label>
                    <div class="col-sm-2">
                      <div class="input-group">
                        <input type="text" class="form-control form-control-sm text-right" id="utilidadc" name="utilidadc" value="<?php echo $margenc; ?>" onkeyup="precios('compra','utilidadc','venta','factoru');validarGanancia(this);">
                        <div class="input-group-append">
                          <span class="input-group-text text-sm py-0">%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>

                <fieldset class="border border-info mb-1 px-2">
                  <legend class="h6 pl-1 mb-0">Precio Blister Venta</legend>

                  <?php $pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $datos->pblister; ?>
                  <div class="form-group row mb-1">
                    <label for="pblister" class="col-sm-2 col-form-label">Precio Blister</label>
                    <div class="col-sm-2">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text text-sm py-0">S/</span>
                        </div>
                        <input type="text" class="form-control form-control-sm text-right" id="pblister" name="pblister" value="<?php echo $pblister; ?>" onkeyup="margen('pcompra','pblister','factorb','utilidadb');">
                      </div>
                    </div>

                    <div class="col-sm-2">
                      <h4 class="my-0"><input type="text" id="umedidab" name="umedidab" class="campo text-center" value="PK"></h4>
                    </div>

                    <label for="factorb" class="col-sm-2 col-form-label">Factor (Unidades)</label>
                    <div class="col-sm-1">
                      <input type="text" class="form-control form-control-sm text-right" id="factorb" name="factorb" value="<?php echo set_value_input($datos,'factorb',$datos->factorb); ?>" onkeyup="margen('pcompra','pblister','factorb','utilidadb');">
                    </div>

                    <?php $margenb=gananciav($pblister,$datos->pcompra,$datos->factorb); ?>
                    <label for="utilidadb" class="col-sm-1 col-form-label">Margen Ganancia</label>
                    <div class="col-sm-2">
                      <div class="input-group">
                        <input type="text" class="form-control form-control-sm text-right" id="utilidadb" name="utilidadb" value="<?php echo $margenb; ?>" onkeyup="precios('pcompra','utilidadb','pblister','factorb');validarGanancia(this);">
                        <div class="input-group-append">
                          <span class="input-group-text text-sm py-0">%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>

              <hr class="my-2">
              <div class="form-group row mb-1">
                <label for="pactivo" class="col-sm-2 col-form-label">Principio Activo</label>
                <div class="col-sm-3">
                  <select name="pactivo" id="pactivo" class="form-control form-control-sm select2" onchange="mostrarGenerico('<?php echo base_url(); ?>producto/busGenericos')">
                    <option value="" <?php echo set_value_select($datos,'pactivo','',$datos->idpactivo) ?>>::Selec</option>
                    <?php foreach ($pactivos as $pactivo) {?>
                      <?php $resolucion=$pactivo->escenciales==1 ? ' --- (RM 220-2024)': ''; ?>
                    <option value="<?php echo $pactivo->id ?>" <?php echo set_value_select($datos,'pactivo',$pactivo->id,$datos->idpactivo) ?>><?php echo $pactivo->descripcion.$resolucion; ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="egenerico" class="col-sm-3 col-form-label">Medicamentos esenciales genéricos</label>
                <div class="col-sm-3">
                  <select name="egenerico" id="egenerico" class="form-control form-control-sm">
                    <option value="" <?php echo set_value_select($datos,'egenerico','',$datos->idegenerico) ?>>::Selec</option>
                    <?php if ($datos->clasificacion!=0 && $datos->idpactivo>0): ?>
                    <?php foreach ($egenericos as $egenerico): ?>
                      <option value="<?php echo $egenerico->id ?>" <?php echo set_value_select($datos,'egenerico',$egenerico->id,$datos->idegenerico) ?>><?php echo $egenerico->descripcion; ?></option>
                    <?php endforeach ?>
                    <?php endif ?>
                  </select>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="aterapeutica" class="col-sm-2 col-form-label">Accion Terapeutica</label>
                <div class="col-sm-3">
                  <select name="aterapeutica" id="aterapeutica" class="form-control form-control-sm select2">
                    <option value="" <?php echo set_value_select($datos,'aterapeutica','',$datos->idaterapeutica) ?>>::Selec</option>
                    <?php foreach ($aterapeuticas as $aterapeutica) {?>
                    <option value="<?php echo $aterapeutica->id ?>" <?php echo set_value_select($datos,'aterapeutica',$aterapeutica->id,$datos->idaterapeutica) ?>><?php echo $aterapeutica->descripcion; ?></option>
                    <?php  }  ?>
                  </select>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="informacion" class="col-sm-2 col-form-label">Informacion Adicional</label>
                <div class="col-sm-10">
                  <textarea name="informacion" id="informacion" class="form-control form-control-sm" rows="3"><?php echo $datos->informacion; ?></textarea>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="foto" class="col-sm-2">FOTO</label>
                <div class="col-sm-1">
                  <?php if ($id!=null): ?>
                    <?php if ($datos->ruta!=NULL): ?>
                    <img src="<?php echo $datos->ruta; ?>" class="rounded img-fluid">
                    <?php else: ?>
                    <img src="<?php echo base_url(); ?>downloads/productos/default.jpg" class="img-thumbnail">
                    <?php endif ?>
                  <?php endif ?>
                </div>
                <div class="col-sm-5">
                  <input type="file" id="foto" name="foto" value=""> <br>
                  <span class="text-danger">Solo se aceptan archivos jpeg, jpg y peso de 100KB</span><br>
                  <span class="text-danger">Las medidas recomendables son 300x300</span>
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-sm-2 offset-5">
                  <button type="submit" class="btn btn-primary btn-sm">GUARDAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="busconsulta">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title">Buscar Precios Digemid</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form name="formd" id="formd">
          <div class="form-group row mb-0">
            <label for="bconsulta" class="col-sm-4 col-form-label">Nombre Producto Farmaceutico</label>
            <div class="col-sm-7">
              <input class="form-control form-control-sm" name="bconsulta" id="bconsulta" autocomplete="off" onkeyup="busConsulta('<?php echo base_url(); ?>producto/busConsultas',this.value)">
            </div>
          </div>

          <div class="table-responsive p-0" style="height: 550px; font-size: .73rem">
            <table class="table table-striped table-bordered table-hover table-sm">
              <thead class="thead-dark">
                <tr>
                  <th width="3%">Codigo</th>
                  <th width="19%">Descripcion</th>
                  <th width="10%">Concentracion</th>
                  <th width="14%">Forma</th>
                  <th width="16%">Fabricante</th>
                  <th width="7%">Fraccion (Cant. Caja)</th>
                  <th width="7%">R. Sanitario</th>
                  <th width="7%">Precio Promedio (Media)</th>
                  <th width="7%">Precio Intermedio (Mediana)</th>
                  <th width="7%">Precio Frecuente (Moda)</th>
                </tr>
              </thead>
              <tbody id="tblconsulta">
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title" id="modalTitle">Datos para Actualizar Stock</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>

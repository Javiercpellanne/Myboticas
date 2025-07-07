<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Ingreso Producto</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>movimiento">Movimiento</a></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary card-outline">
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."movimiento/ingresog');")); ?>
              <div class="form-group row mb-1">
                <label for="motivo" class="col-sm-1 control-label">Motivo*</label>
                <div class="col-sm-2">
                  <select name="motivo" id="motivo" class="form-control form-control-sm" required>
                    <option value="">::Selec</option>
                    <?php foreach ($motivos as $motivo) {?>
                      <option value="<?php echo $motivo->id.'-'.$motivo->descripcion ?>" <?php echo set_value_select(16,'motivo',$motivo->id,16) ?>><?php echo $motivo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="observaciones" class="col-sm-1 col-form-label">Observaciones</label>
                <div class="col-sm-4">
                  <input type="text" id="observaciones" name="observaciones" class="form-control form-control-sm">
                </div>

                <div class="col-sm-2">
                  <button id="buscar" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#busingreso"><i class="fa fa-cart-plus"></i> AGREGAR PRODUCTO</button>
                </div>

                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                    </div>
                    <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Codigo Barra" aria-label="Codigo Barra" aria-describedby="basic-addon1" onkeydown="productoBarrai(event,'<?php echo base_url(); ?>producto/busCodigobarra',this.value);">
                  </div>
                </div>
              </div>

              <div class="table-responsive mb-2" style="height: 460px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="42%">DESCRIPCION</th>
                      <th width="12%">LOTE</th>
                      <th width="10%">F VCTO</th>
                      <th width="8%">U.M</th>
                      <th width="8%">CANT</th>
                      <th width="8%">P.U</th>
                      <th width="8%">IMPORTE</th>
                      <th width="4%"></th>
                    </tr>
                  </thead>
                  <tbody id="grilla">
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

<div class="modal fade" id="busingreso">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title">Datos del Producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form name="fproducto" id="fproducto" autocomplete="off">
          <div id="mensajeerror"></div>
          <input name="mcodigo" id="mcodigo" type="hidden">
          <div class="form-group row mb-1">
            <label for="mdescripcion" class="col-sm-2 col-form-label">Producto</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="mdescripcion" id="mdescripcion" type="text" class="form-control form-control-sm" onkeyup="productoNombrei('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off">
                <div class="input-group-append">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                </div>
              </div>

              <div id="tbldescripcion" style="position:absolute; z-index: 1051; width: 98%; overflow: overlay; max-height:300px; display: none;">
                <dl class="bg-buscador" id="grdescripcion">
                </dl>
              </div>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="mmedida" class="col-sm-2 col-form-label">Tipo Precio</label>
            <div class="col-sm-3">
              <input type="hidden" class="form-control form-control-sm text-right" id="mfactor" name="mfactor" value="1"  readonly>
              <select name="mmedida" id="mmedida" class="form-control form-control-sm" onchange="conversion(this.value)">
                <option value="">Seleccione</option>
              </select>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="munidades" class="col-sm-2 col-form-label">Cantidad</label>
            <div class="col-sm-2">
              <input type="text" class="form-control form-control-sm text-right" id="munidades" name="munidades" value="" onkeyup="factores('munidades','mcosto','mtotal');factores('munidades','mfactor','mcantidad');" required>
            </div>

            <label for="mcosto" class="col-sm-2 col-form-label">Precio Compra</label>
            <div class="col-sm-2">
              <input type="text" class="form-control form-control-sm text-right" id="mcosto" name="mcosto" value="" onkeyup="factores('munidades','mcosto','mtotal');divisores('mcosto','mfactor','mmonto');" required>
            </div>

            <label for="mtotal" class="col-sm-2 col-form-label">Subtotal</label>
            <div class="col-sm-2">
              <h3 class="my-0"><input name="mtotal" id="mtotal" type="text" class="campo text-right" value=""></h3>
            </div>
          </div>
          <input type="hidden" name="mcantidad" id="mcantidad" value="">
          <input type="hidden" name="mmonto" id="mmonto" value="">
          <input type="hidden" name="mactivar" id="mactivar" value="">

          <div id="mdetalle" class="form-group mb-2" style="display: none;">
            <div class="row">
              <label for="mlote" class="col-sm-2 col-form-label">Codigo Lote </label>
              <div class="col-sm-3">
                <input name="mlote" id="mlote" type="text" class="form-control form-control-sm" value="">
              </div>

              <label for="mfecha" class="col-sm-3 col-form-label">Fec. Vencimiento </label>
              <div class="col-sm-4">
                <input name="mfecha" id="mfecha" type="date" class="form-control form-control-sm" value="">
              </div>
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-sm-12 text-right">
              <button type="button" class="btn btn-primary btn-sm ml-4" onclick="appingreso();">AGREGAR</button>
              <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_ingreso();">CERRAR</button>
            </div>
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
        <h5 class="modal-title" id="modalTitle">Datos de la Serie</h5>
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


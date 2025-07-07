<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Actualizar Inventario <small class="text-danger">REEMPLAZA EL STOCK ACTUAL</small></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>inventario">Actualizar Inventario</a></li>
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

            <div class="form-group row mb-1">
              <div class="col-sm-2">
                <button id="buscar" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#busproductosu"><i class="fa fa-cart-plus"></i> AGREGAR PRODUCTO</button>
              </div>

              <div class="col-sm-2">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                  </div>
                  <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Codigo Barra" aria-label="Codigo Barra" aria-describedby="basic-addon1" onkeydown="productoBarrau(event,'<?php echo base_url(); ?>producto/busCodigobarra',this.value);">
                </div>
              </div>
            </div>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."inventario/inventariog');")); ?>
              <input type="hidden" id="numero" name="numero" value="<?php echo $id; ?>">
              <div class="table-responsive mb-2" style="height: 450px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="6%">COD</th>
                      <th width="48%">DESCRIPCION</th>
                      <th width="6%">CANTIDAD</th>
                      <th width="7%">P.U</th>
                      <th width="7%">IMPORTE</th>
                      <th width="10%">LOTE</th>
                      <th width="12%">F. VCTO</th>
                      <th width="3%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; ?>
                    <?php foreach ($listas as $lista): ?>
                      <?php $producto=$this->producto_model->mostrar(array("p.id"=>$lista->idproducto)); ?>
                      <tr>
                        <input type="hidden" name="id[]" id="id[]" value="<?php echo $lista->id; ?>">
                        <input type="hidden" name="femision[]" id="femision[]" value="<?php echo $lista->femision; ?>">
                        <td><input type="text" name="idproducto[]" value="<?php echo $lista->idproducto; ?>" class="campo"/></td>
                        <td><input type="text" name="descripcion[]" value="<?php echo $lista->descripcion; ?>" class="campo"/></td>
                        <td><input type="text" name="cantidad[]" value="<?php echo $lista->cantidad; ?>" class="campo text-right"/></td>
                        <td><input type="text" name="precio[]" value="<?php echo $producto->pcompra; ?>" class="campo text-right"/></td>
                        <td><input type="text" name="importe[]" value="<?php echo $producto->pcompra*$lista->cantidad; ?>" class="campo text-right"/></td>
                        <td <?php if ($producto->lote==1 && $lista->lote=='') {echo 'class="table-danger"';} ?>><input type="text" name="lote[]" value="<?php echo $lista->lote; ?>" class="campo text-right" <?php if ($producto->lote==1) {echo 'required';} ?>/></td>
                        <td><input type="text" name="fvencimiento[]" value="<?php echo $lista->fvencimiento; ?>" class="campo text-right"/></td>
                        <td>
                          <div class="btn-group">
                            <button type="button"class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>inventario/inventarioe/<?php echo $id; ?>/<?php echo $lista->id; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>

                            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>inventario/inventarioItemd/<?php echo $lista->id; ?>','<?php echo "Desea borrar ".$lista->descripcion."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>

              <div class="form-group row mb-0">
                <div class="col-sm-12 text-center">
                  <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="ACTUALIZAR ALMACEN"/>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title" id="modalTitle">Datos de la Actualizacion</h4>
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

<div class="modal fade" id="busproductosu">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title">Datos del Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
          <div id="mensajeerror"></div>
          <input name="mcodigo" id="mcodigo" type="hidden">
          <div class="form-group row mb-1">
            <label for="mdescripcion" class="col-sm-2 col-form-label">Producto</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="mdescripcion" id="mdescripcion" type="text" class="form-control form-control-sm" onkeyup="productoNombreu('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off" required>
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
            <label for="munidades" class="col-sm-2 col-form-label">Cantidad</label>
            <div class="col-sm-2">
              <input type="text" id="munidades" name="munidades" class="form-control form-control-sm text-right" value="" required>
            </div>
          </div>

          <input type="hidden" name="mstock" id="mstock" value="">
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
              <button type="submit" class="btn btn-primary btn-sm ml-4">ACEPTAR</button>
              <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_inventario();">CERRAR</button>
            </div>
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>

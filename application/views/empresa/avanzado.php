<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Empresa</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active">Empresa</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary">
      <div class="card-header py-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>empresa">Generales</a></li>
          <?php if ($empresa->facturacion==1): ?>
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/facturacion">Facturacion</a></li>
          <?php endif ?>
          <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>empresa/avanzado">Avanzado</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/configuracion">Acciones</a></li>
        </ul>
      </div>
      <div class="card-body p-3">
        <?php if($this->session->flashdata('mensaje')!=''){ ?>
          <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('mensaje') ?>
          </div>
        <?php } ?>

        <?php echo form_open(null,array("name"=>"form1", "id"=>"form1", "enctype"=>"multipart/form-data", "class"=>"form-horizontal")); ?>
          <div class="form-group row mb-1">
            <label for="compra" class="col-sm-3">Compra Almacen</label>
            <div class="col-sm-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="compra" id="compra1" value="0" <?php echo set_value_check($empresa,'compra',$empresa->compra,0); ?>>
                <label class="form-check-label" for="compra1">
                  Automatico
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="compra" id="compra2" value="1" <?php echo set_value_check($empresa,'compra',$empresa->compra,1); ?>>
                <label class="form-check-label" for="compra2">
                  Edicion
                </label>
              </div>
            </div>

            <div class="col-sm-7 text-danger">
              poder editar al ingresar una compra (Automatico: el ingreso no se edita y se guarda directamente al almacen)
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="pventa" class="col-sm-3">Editar Precio en Venta</label>
            <div class="col-sm-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pventa" id="pventa1" value="0" <?php echo set_value_check($empresa,'pventa',$empresa->pventa,0); ?>>
                <label class="form-check-label" for="pventa1">
                  Fijo
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="pventa" id="pventa2" value="1" <?php echo set_value_check($empresa,'pventa',$empresa->pventa,1); ?>>
                <label class="form-check-label" for="pventa2">
                  Edicion
                </label>
              </div>
            </div>

            <div class="col-sm-7 text-danger">
              poder modificar los precios de ventas en la venta ( fijo:  no se podra modificarlos precios de ventas)
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="arqueo" class="col-sm-3">Cierre Arqueo Caja</label>
            <div class="col-sm-2">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="arqueo" id="arqueo1" value="0" <?php echo set_value_check($empresa,'arqueo',$empresa->arqueo,0); ?>>
                <label class="form-check-label" for="arqueo1">
                  Automatico
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="arqueo" id="arqueo2" value="1" <?php echo set_value_check($empresa,'arqueo',$empresa->arqueo,1); ?>>
                <label class="form-check-label" for="arqueo2">
                  Edicion
                </label>
              </div>
            </div>

            <div class="col-sm-7 text-danger">
              cierre de caja directo sin pedir monto  (Edicion: al cerrar caja te pide la cantidad con la que cerrara)
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="spuntos" class="col-sm-3">Sistema por puntos </label>
            <div class="col-sm-2">
              <input type="checkbox" name="spuntos" id="spuntos" value="1" <?php echo set_value_check($empresa,'spuntos',$empresa->spuntos,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              Habilita la acumulacion de puntos en las ventas realizadas a personas naturales
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="vbonificar" class="col-sm-3">Bonificar Producto en Venta</label>
            <div class="col-sm-2">
              <input type="checkbox" name="vbonificar" id="vbonificar" value="1" <?php echo set_value_check($empresa,'vbonificar',$empresa->vbonificar,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              Habilita la venta de producto gratutito o con monto 0.
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="lstock" class="col-sm-3">Mostrar Stock +99</label>
            <div class="col-sm-2">
              <input type="checkbox" name="lstock" id="lstock" value="1" <?php echo set_value_check($empresa,'lstock',$empresa->lstock,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              Habilita para que el stock se muestre con +99 al tener un monto mayor de 100 productos.
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="pcompra" class="col-sm-3">Actualizar Precio Venta desde Compra</label>
            <div class="col-sm-2">
              <input type="checkbox" name="pcompra" id="pcompra" value="1" <?php echo set_value_check($empresa,'pcompra',$empresa->pcompra,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No" onchange="mostrarGanancia(this)">
            </div>

            <div class="col-sm-7 text-danger">
              Habilita la actualizacion de precio de venta a la hora de realizar una compra.
            </div>
          </div>

          <div id="mganancia" <?php echo $empresa->pcompra==0 ? 'style="display: none;"': 'style="display: block;"'; ?>>
            <div class="form-group row mb-1">
              <span class="col-sm-3"></span>
              <div class="col-sm-2">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">G. Unidad</span>
                  </div>
                  <input name="gunidad" id="gunidad" type="text" class="form-control form-control-sm text-right" value="<?php echo $empresa->gunidad; ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">G. Blister</span>
                  </div>
                  <input name="gblister" id="gblister" type="text" class="form-control form-control-sm text-right" value="<?php echo $empresa->gblister; ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="input-group input-group-sm">
                  <div class="input-group-prepend">
                    <span class="input-group-text">G. Caja</span>
                  </div>
                  <input name="gcaja" id="gcaja" type="text" class="form-control form-control-sm text-right" value="<?php echo $empresa->gcaja; ?>">
                  <div class="input-group-append">
                    <span class="input-group-text">%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-sm-12 text-right">
              <button type="submit" class="btn btn-primary btn-sm float-right">GUARDAR</button>
            </div>
          </div>
        <?php echo form_close(); ?>

        <?php if ($contador>1): ?>
        <hr class="border-info">
          <?php echo form_open(base_url().'empresa/precio',array("name"=>"form1", "id"=>"form1", "enctype"=>"multipart/form-data", "class"=>"form-horizontal")); ?>

            <div class="form-group row mb-1">
              <label for="pestablecimiento" class="col-sm-3">Separa Precio Establecimiento</label>
              <div class="col-sm-2">
                <input type="checkbox" name="pestablecimiento" id="pestablecimiento" value="1" <?php echo set_value_check($empresa,'pestablecimiento',$empresa->pestablecimiento,1); ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
              </div>

              <div class="col-sm-7 text-danger">
                Separa los precios de ventas por establecimiento tomando como inicial el precio actual
              </div>
            </div>

            <?php if ($empresa->pestablecimiento==1): ?>
              <div class="form-group row mb-1">
                <span class="col-sm-3"></span>
                <div class="col-sm-2">
                  <select name="establecimiento" id="establecimiento" class="form-control form-control-sm" required>
                    <?php foreach ($establecimientos as $establecimiento) {?>
                      <option value="<?php echo $establecimiento->id ?>"><?php echo $establecimiento->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>
                <div class="col-sm-7 text-danger">
                  Para unificar precios tendra que especificar de que establecimiento tomara como precio actual
                </div>
              </div>
            <?php endif ?>

            <div class="form-group row mb-0">
              <div class="col-sm-12 text-right">
                <button type="submit" class="btn btn-primary btn-sm float-right">GUARDAR</button>
              </div>
            </div>
          <?php echo form_close(); ?>
        <?php endif ?>
      </div>
    </div>
  </div>
</section>

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


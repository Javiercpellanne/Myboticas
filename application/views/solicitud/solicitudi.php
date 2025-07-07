<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Solicitud Compra</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>solicitud">Solicitud Compra</a></li>
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
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
              <div class="form-group row mb-1">
                <label for="proveedor" class="col-sm-2 col-form-label">Proveedor*</label>
                <input name="idproveedor" id="idproveedor" type="hidden" value="" required/>
                <div class="col-sm-5">
                  <div class="input-group">
                    <input name="proveedor" type="text" id="proveedor" class="form-control form-control-sm" value="" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>proveedor/buscador','bdatos','Buscar Proveedor')"><i class="fa fa-search" aria-hidden="true"></i></button>

                      <button type="button" class="btn btn-info btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>compra/proveedori','bdatos','Datos del Proveedor')"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>

                <label for="fecha" class="col-sm-1 col-form-label">Fecha Emision*</label>
                <div class="col-sm-2">
                  <input name="fecha" type="date" id="fecha" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" max="<?php echo date("Y-m-d") ?>" required/>
                </div>

                <div class="col-sm-2 text-right">
                  <button id="buscar" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#bussolicitud"><i class="fa fa-cart-plus"></i> AGREGAR PRODUCTO</button>
                </div>
              </div>

              <div class="table-responsive" style="height: 460px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="76%">DESCRIPCION</th>
                      <th width="10%">U.M</th>
                      <th width="10%">CANT</th>
                      <th width="4%"></th>
                    </tr>
                  </thead>
                  <tbody id="grilla">
                  </tbody>
                </table>
              </div>

              <div class="form-group mb-0 col-sm-12 text-center">
                <button type="submit" class="btn btn-primary btn-sm">GUARDAR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="bussolicitud">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title">Datos del Producto</h4>
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
                <input name="mdescripcion" id="mdescripcion" type="text" class="form-control form-control-sm" onkeyup="productoNombret('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off">
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
              <input type="text" class="form-control form-control-sm text-right" id="munidades" name="munidades" value="" required>
            </div>

            <label for="mmedida" class="col-sm-2 col-form-label">Unidad Medida</label>
            <div class="col-sm-3">
              <select name="mmedida" id="mmedida" class="form-control form-control-sm">
              </select>
            </div>
          </div>

          <div class="form-group row mb-0">
            <div class="col-sm-12 text-right">
              <button type="button" class="btn btn-primary btn-sm ml-4" onclick="appsolicitud();">AGREGAR</button>
              <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_solicitud();">CERRAR</button>
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


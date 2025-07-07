<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Compra Gastos y Otros</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>gasto">Gastos y Otros</a></li>
        </ol>
      </div>
    </div>
  </div>
</div>

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

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."gasto/guardar/".$id."');")); ?>
              <div class="form-group row mb-1">
                <label for="comprobante" class="col-sm-2 col-form-label">Comprobante*</label>
                <div class="col-sm-2">
                  <select name="comprobante" id="comprobante" class="form-control form-control-sm" required>
                    <option value="">::Selec</option>
                    <option value="01">Factura</option>
                    <option value="03">Boleta</option>
                    <option value="09">Guia Remision</option>
                    <option value="99">Nota Venta</option>
                  </select>
                </div>

                <label for="fecha" class="col-sm-2 col-form-label">Fecha*</label>
                <div class="col-sm-2">
                  <input name="fecha" type="date" id="fecha" class="form-control form-control-sm" value="<?php echo $datos->femision; ?>" max="<?php echo date("Y-m-d") ?>" required/>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="serie" class="col-sm-2 col-form-label">Serie*</label>
                <div class="col-sm-2" >
                   <input name="serie" type="text" id="serie" value="<?php echo $datos->serie; ?>" class="form-control form-control-sm" required/>
                </div>

                <label for="numero" class="col-sm-2 col-form-label">Numero*</label>
                <div class="col-sm-2" >
                   <input name="numero" type="text" id="numero" value="<?php echo $datos->numero; ?>" class="form-control form-control-sm" required/>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="proveedor" class="col-sm-2 col-form-label">Proveedor*</label>
                <input name="idproveedor" id="idproveedor" type="hidden" value="<?php echo $datos->idproveedor ?>" required/>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input name="proveedor" type="text" id="proveedor" class="form-control form-control-sm" value="<?php echo $datos->proveedor ?>" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>proveedor/buscador','bdatos','Buscar Proveedor')"><i class="fa fa-search" aria-hidden="true"></i></button>

                      <button type="button" class="btn btn-info btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>compra/proveedori','bdatos','Datos del Proveedor')"><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="dadicional" class="col-sm-2 col-form-label">Descripcion</label>
                <div class="col-sm-10">
                  <input type="text" id="dadicional" name="dadicional" class="form-control form-control-sm" value="<?php echo $datos->dadicional; ?>">
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="total" class="col-sm-2 col-form-label">Total*</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text py-0">S/.</span>
                    </div>
                    <input name="total" type="text" id="total" class="form-control form-control-sm text-right" value="<?php echo $datos->total; ?>"/>
                  </div>
                </div>
              </div>

              <?php if ($id==null): ?>
              <div class="form-group row mb-1">
                <label for="tpago" class="col-sm-2 col-form-label">Tipo Pago*</label>
                <div class="col-sm-2">
                  <select name="tpago" id="tpago" class="form-control form-control-sm" required onchange="pagoCreditoc(this.value)">
                    <option value="1">Contado</option>
                    <option value="2">Credito</option>
                  </select>
                </div>

                <div class="col-sm-3" id="contado" style="display: block;">
                  <div class="row">
                    <label for="mpago" class="col-sm-4 col-form-label">Medio Pago</label>
                    <div class="col-sm-8">
                      <select name="mpago" id="mpago" class="form-control form-control-sm" required>
                        <?php foreach ($mpagos as $mpago): ?>
                          <option value="<?php echo $mpago->id ?>"><?php echo $mpago->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <?php endif ?>

              <div class="form-group row mb-1">
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

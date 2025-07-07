<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Puntos Acumulables</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active">Puntos Acumulables</li>
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

            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="valorp" class="col-sm-4 col-form-label">Valor Puntos Acumulables en Venta*</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-sm" id="valor" name="valor" value="1" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text text-sm py-0" id="basic-addon2">Puntos</span>
                    </div>
                  </div>
                </div>

                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text text-sm py-0">S/</span>
                    </div>
                    <input type="text" name="valorp" id="valorp" class="form-control form-control-sm" value="<?php echo $datos->valorp; ?>" required/>
                  </div>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="caducidad" class="col-sm-4 col-form-label">Caducidad Puntos Acumubles*</label>
                <div class="col-sm-2">
                  <select class="form-control form-control-sm" id="caducidad" name="caducidad">
                    <option value="0">Ilimitado</option>
                    <option value="6">6 meses</option>
                    <option value="12">1 año</option>
                  </select>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="canjep" class="col-sm-4 col-form-label">Canje de Puntos a Vale*</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-sm" id="canjep" name="canjep" value="<?php echo $datos->canjep; ?>" required>
                    <div class="input-group-append">
                        <span class="input-group-text text-sm py-0" id="basic-addon2">Puntos</span>
                    </div>
                  </div>
                </div>

                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text text-sm py-0">S/</span>
                    </div>
                    <input type="text" name="canjev" id="canjev" class="form-control form-control-sm" value="<?php echo $datos->canjev; ?>" required/>
                  </div>
                </div>
              </div>

              <hr>
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


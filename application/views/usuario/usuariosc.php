<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Cambiar Contraseña</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active">Cambiar Contraseña</li>
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

              <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1")); ?>
                <div class="form-group row">
                  <label for="anterior" class="col-sm-2 control-label">Contraseña Actual*</label>
                  <div class="col-sm-3">
                    <input name="anterior" type="password" id="anterior" class="form-control form-control-sm" value="" required/>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="posterior" class="col-sm-2 control-label">Nueva Contraseña*</label>
                  <div class="col-sm-3">
                    <input name="posterior" type="password" id="posterior" class="form-control form-control-sm" value="" required/>
                  </div>
                </div>

                <div class="form-group row">
                  <div class="col-sm-2 offset-5">
                    <button type="submit" class="btn btn-primary">GUARDAR</button>
                  </div>
                </div>
              <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

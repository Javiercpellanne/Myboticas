<div class="login-box">
  <div class="login-logo">
    <h1 class="text-white">
      <?php $empresa=$this->empresa_model->mostrar(); ?>
      <?php $archivo=$empresa->logo; ?>
      <?php if (@getimagesize($archivo)): ?>
        <img src="<?php echo $archivo; ?>" class="img-fluid rounded" style="max-height: 115px">
      <?php else: ?>
        Sistema Botica
      <?php endif ?>
    </h1>
  </div>

  <div class="card" style="border-radius: 15px;">
    <div class="card-body login-card-body">
      <h4 class="login-box-msg">INICIO SESION</h4>

      <?php echo form_open("login/acceso",array("class"=>"login-form", "name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
        <?php if($this->session->flashdata('mensaje')!=''){ ?>
          <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('mensaje') ?>
          </div>
        <?php } ?>

        <div class="input-group mb-3">
          <input name="usuario" type="text" class="form-control form-control-sm" placeholder="Usuario" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input name="clave" type="password" class="form-control form-control-sm" placeholder="Clave" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-8">
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block btn-sm">Ingresar</button>
          </div>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

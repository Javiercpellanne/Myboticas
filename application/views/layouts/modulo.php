<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SGFarma | Establecimientos</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/adminlte.min.css">
  <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/style.css">
  <link rel="icon" href="<?php echo base_url(); ?>public/logo/favicon.ico">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
</head>
<body class="hold-transition layout-top-nav text-sm">
  <div class="wrapper">
    <?php if ($this->session->userdata("predeterminado")==1) {$cplantilla="primary";} elseif ($this->session->userdata("predeterminado")==2) {$cplantilla="info";} elseif ($this->session->userdata("predeterminado")==3) {$cplantilla="orange";}elseif ($this->session->userdata("predeterminado")==4) {$cplantilla="purple";} else {$cplantilla="warning";}?>
    <nav class="main-header navbar navbar-expand-md navbar-dark navbar-<?php echo $cplantilla; ?>">
      <div class="container-fluid">
        <a href="<?php echo base_url() ?>" class="navbar-brand">
          <img src="<?php echo base_url();?>public/logo/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Botica</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
          <div class="btn-group">
            <button type="button" class="btn btn-light dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo $this->session->userdata('nombre') ?>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item py-1" href="<?php echo base_url();?>login/logout"><i class="fa fa-sign-out-alt"></i> Cerrar Sesion</a>
            </div>
          </div>
        </ul>
      </div>
    </nav>

    <div class="content-wrapper">
      <?php echo $content_for_layout; ?>
    </div>

    <footer class="main-footer">
      <div class="float-right d-none d-sm-inline">
        Version 5.1
      </div>
      <strong>Copyright &copy; <?php echo date("Y"); ?>.</strong> Todos los derechos reservados.
    </footer>
  </div>

  <!-- jQuery -->
  <script src="<?php echo base_url();?>public/js/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo base_url();?>public/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo base_url();?>public/js/adminlte.min.js"></script>
</body>
</html>

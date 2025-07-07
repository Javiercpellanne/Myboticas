<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SGFarma | <?php echo $this->layout->getTitle(); ?></title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/all.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/responsive.bootstrap4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/select2.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/sweetalert2.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/toastr.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/adminlte.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/daterangepicker.css">

  <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/style.css?v=<?php echo(rand()); ?>">
  <link rel="icon" href="<?php echo base_url(); ?>public/logo/favicon.ico">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
</head>
<body class="hold-transition sidebar-mini sidebar-collapse text-sm">
<div class="wrapper">
  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__wobble" src="<?php echo base_url();?>public/logo/loading.gif" alt="logo" height="250" width="250">
  </div>

  <?php if ($this->session->userdata("predeterminado")==1) {$cplantilla="primary";} elseif ($this->session->userdata("predeterminado")==2) {$cplantilla="info";} elseif ($this->session->userdata("predeterminado")==3) {$cplantilla="orange";}elseif ($this->session->userdata("predeterminado")==4) {$cplantilla="purple";} else {$cplantilla="warning";}?>
  <nav class="main-header navbar navbar-expand navbar-<?php echo $cplantilla; ?> navbar-dark">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <?php
    $minimo=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1));
    $nminimo=count($minimo)<200 ? count($minimo) : '+200';
    $vencidos=$this->lote_model->productosVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fvencimiento<="=>SumarFecha('+3 month'),"estado"=>1,"stock>"=>0));
    $nvencidos=count($vencidos);

    $filtros=array("tipo_estado"=>"01");
    $contadorv=$this->venta_model->contador($filtros);
    $contadorn=$this->nota_model->contador($filtros);
    $noenviados=$contadorv+$contadorn;

    $filtros=array("tipo_estado"=>"01","rectificar"=>1);
    $contadorv=$this->venta_model->contador($filtros);
    $contadorn=$this->nota_model->contador($filtros);
    $corregir=$contadorv+$contadorn;

    $filtros=array("tproceso"=>1,"tipo_estado"=>'01');
    $cresumenes=$this->resumen_model->contador($filtros);

    $filtrosf=array("tipo_estado"=>'01');
    $contadorf=$this->anulado_model->contador($filtrosf);

    $filtrosb=array("tproceso"=>3,"tipo_estado"=>'01');
    $contadorb=$this->resumen_model->contador($filtrosb);
    $canulaciones=$contadorf+$contadorb;
    ?>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)">
          <img src="<?php echo base_url();?>public/logo/vigilancia.png" width="60" class="d-inline-block align-top" title="Farmaco Vigilancia" onclick="mostrarModal('<?php echo base_url(); ?>inicio/vigilancia','bdatos','Farmaco Vigilancia')" data-toggle="tooltip" data-placement="bottom">
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)">
          <img src="<?php echo base_url();?>public/logo/digemid.gif" width="45" class="d-inline-block align-top" title="Alertas DIGEMID" onclick="mostrarModal('<?php echo base_url(); ?>inicio/digemid','bdatos','Alertas DIGEMID')" data-toggle="tooltip" data-placement="bottom">
        </a>
      </li>

      <?php //if ($nminimo>0): ?>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)" title="Stock Minimo" onclick="mostrarModal('<?php echo base_url(); ?>inicio/minimo','bdatos','Stock Minimo')" data-toggle="tooltip" data-placement="bottom">
          <i class="fa fa-cubes"></i>
          <span class="badge bg-purple navbar-badge" style="top: -3px;"><?php echo $nminimo; ?></span>
        </a>
      </li>
      <?php //endif ?>

      <?php //if ($nvencidos>0): ?>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)" title="Proximo Vencer" onclick="mostrarModal('<?php echo base_url(); ?>inicio/vencido','bdatos','Proximo Vencer')" data-toggle="tooltip" data-placement="bottom">
          <i class="fa fa-calendar-alt"></i>
          <span class="badge bg-dark navbar-badge" style="top: -3px;"><?php echo $nvencidos; ?></span>
        </a>
      </li>
      <?php //endif ?>

      <?php if ($empresa->facturacion==1): ?>
        <?php if ($noenviados>0): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url(); ?>facturacion" title="Comprobantes pendientes de envio" data-toggle="tooltip" data-placement="bottom">
            <i class="fa fa-bell"></i>
            <span class="badge badge-danger navbar-badge" style="top: -3px;"><?php echo $noenviados; ?></span>
          </a>
        </li>
        <?php endif ?>

        <?php if ($corregir>0): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url(); ?>facturacion/rectificaciones" title="Comprobantes pendientes de rectificacion" data-toggle="tooltip" data-placement="bottom">
            <i class="fa fa-exclamation-triangle"></i>
            <span class="badge badge-danger navbar-badge" style="top: -3px;"><?php echo $corregir; ?></span>
          </a>
        </li>
        <?php endif ?>

        <?php if ($cresumenes>0): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url(); ?>facturacion/resumenes" title="Resumenes pendientes consulta" data-toggle="tooltip" data-placement="bottom">
            <i class="fa fa-archive"></i>
            <span class="badge badge-danger navbar-badge" style="top: -3px;"><?php echo $cresumenes; ?></span>
          </a>
        </li>
        <?php endif ?>

        <?php if ($canulaciones>0): ?>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo base_url(); ?>facturacion/anulaciones" title="Anulacion pendientes consulta" data-toggle="tooltip" data-placement="bottom">
            <i class="fa fa-bell-slash"></i>
            <span class="badge badge-danger navbar-badge" style="top: -3px;"><?php echo $canulaciones; ?></span>
          </a>
        </li>
        <?php endif ?>
      <?php endif ?>

      <div class="d-none d-sm-none d-md-block">
        <li class="nav-item">
          <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
          </a>
        </li>
      </div>

      <div class="btn-group">
        <button type="button" class="btn btn-light dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <?php echo $this->session->userdata('nombre') ?>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <?php if (count($anexos)>1): ?>
            <a class="dropdown-item py-1" href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>inicio/establecimientos','bdatos','Establecimientos de la Empresa')"><i class="fa fa-building"></i> Establecimientos</a>
            <div class="dropdown-divider"></div>
          <?php endif ?>
          <a class="dropdown-item py-1" href="<?php echo base_url();?>usuario/usuariosc"><i class="fa fa-edit"></i> Contraseña</a>
          <a class="dropdown-item py-1" href="<?php echo base_url();?>login/logout"><i class="fa fa-sign-out-alt"></i> Cerrar Sesion</a>
        </div>
      </div>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-<?php echo $cplantilla; ?> elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo base_url(); ?>" class="brand-link">
      <img src="<?php echo base_url();?>public/logo/logo.png" class="brand-image img-fluid rounded elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Botica</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <?php include('menu.php'); ?>
    </div>
  </aside>

  <div class="content-wrapper">
    <?php echo $content_for_layout; ?>
  </div>

  <footer class="main-footer">
    <div class="float-right d-none d-sm-inline">
      <i class="fa fa-cogs" title="Uso de memoria" data-toggle="tooltip" data-placement="bottom"></i> {memory_usage} | <i class="fa fa-clock" title="Duración de la solicitud" data-toggle="tooltip" data-placement="bottom"></i> {elapsed_time}s | Version 6.1
    </div>
    <strong>Copyright &copy; <?php echo date("Y"); ?>.</strong> Todos los derechos reservados.
  </footer>
</div>

  <!-- jQuery -->
  <script src="<?php echo base_url();?>public/js/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo base_url();?>public/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables -->
  <script src="<?php echo base_url();?>public/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url();?>public/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url();?>public/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo base_url();?>public/js/responsive.bootstrap4.min.js"></script>
  <!-- Select2 -->
  <script src="<?php echo base_url();?>public/js/select2.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="<?php echo base_url();?>public/js/sweetalert2.min.js"></script>
  <!-- Toastr -->
  <script src="<?php echo base_url();?>public/js/toastr.min.js"></script>
  <!-- daterangepicker -->
  <script src="<?php echo base_url();?>public/js/moment.min.js"></script>
  <script src="<?php echo base_url();?>public/js/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="<?php echo base_url();?>public/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- ChartJS -->
  <script src="<?php echo base_url();?>public/js/chart.min.js"></script>
  <!-- Bootstrap Switch -->
  <script src="<?php echo base_url();?>public/js/bootstrap-switch.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo base_url();?>public/js/adminlte.min.js"></script>
  <!-- propios -->
  <script src="<?php echo base_url();?>public/js/funciones.js?v=<?php echo(rand()); ?>"></script>
  <script src="<?php echo base_url();?>public/js/graficos.js"></script>

  <?php if (($this->uri->segment(1)=='venta' || $this->uri->segment(1)=='nventa') && $this->uri->segment(2)!='' && $this->uri->segment(2)!='ncredito'): ?>
  <script src="<?php echo base_url();?>public/js/atajos.js"></script>
  <?php endif ?>

  <script type="text/javascript">
    var base_url = '<?php echo addslashes(base_url()); ?>';
    $(function() {
      <?php if ($this->uri->segment(1)=='inicio'){?>
        vmensual();
        canual();
        nventa();
        comprobante();
        compra();
      <?php } ?>

      <?php if ($this->uri->segment(2)=='vhorario'){?>
        vhorario();
      <?php } ?>

      <?php if ($this->uri->segment(2)=='pclasificacion'){?>
        productos();
        clasificacion();
      <?php } ?>

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });

      $('#calendario').datetimepicker({
        format: 'Y-MM-DD',
        inline: true,
        //minDate:new Date(),
      })
    });

    $('[data-toggle="tooltip"]').tooltip();
    $('.select2').select2();

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    $('#sampleTable').DataTable({
      // "pageLength": 15,
      // "lengthMenu": [ 15, 25, 50, 75, 100 ],
      "oLanguage": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      }
    });
  </script>
</body>
</html>

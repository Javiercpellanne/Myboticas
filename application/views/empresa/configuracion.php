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
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/avanzado">Avanzado</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>empresa/configuracion">Acciones</a></li>
        </ul>
      </div>
      <div class="card-body p-3">
        <?php if($this->session->flashdata('mensaje')!=''){ ?>
          <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('mensaje') ?>
          </div>
        <?php } ?>

        <h5 class="mb-2 font-weight-bold">
          <u>DEJAR EN STOCK 0</u>
        </h5>
        <div class="form-group row mb-2 pl-2">
          <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>producto/resetear','Desea resetear el stock de los productos activos a 0')" class="btn btn-outline-danger btn-sm mr-2">RESETEAR PRODUCTOS ACTIVOS</a>

          <button type="button" class="btn btn-outline-danger btn-sm mr-2" onclick="mostrarModal('<?php echo base_url(); ?>producto/categorias','bdatos','Datos de la Categoria')">RESETEAR PRODUCTOS X CATEGORIA</button>

          <button type="button" class="btn btn-outline-danger btn-sm mr-2" onclick="mostrarModal('<?php echo base_url(); ?>producto/laboratorios','bdatos','Datos del Laboratorio')">RESETEAR PRODUCTOS X LABORATORIO</button>
        </div>
        <hr>

        <h5 class="mb-2 font-weight-bold">
          <u>COPIA SEGURIDAD</u>
        </h5>
        <a href="<?php echo base_url(); ?>empresa/backup" class="btn btn-outline-info btn-sm mr-2">GENERAR BACKUP SQL</a>
        <hr>

        <?php if ($empresa->facturacion==1): ?>
        <!-- <h5 class="mb-2 font-weight-bold">
          <u>FACTURACION</u>
        </h5>
        <a href="<?php echo base_url(); ?>empresa/directorioPdf" class="btn btn-outline-primary mr-2">DESCARGAR ARCHIVOS PDF</a>
        <a href="<?php echo base_url(); ?>empresa/directorioXml" class="btn btn-outline-success mr-2">DESCARGAR ARCHIVOS XML</a>
        <a href="<?php echo base_url(); ?>empresa/directorioCdr" class="btn btn-outline-warning mr-2">DESCARGAR ARCHIVOS CDR</a> -->
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
      <div class="modal-body p-3">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>


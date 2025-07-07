<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Comprobante no enviados</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active">Comprobante no enviados</li>
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
          <div class="card-header py-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1" href="<?php echo base_url(); ?>facturacion">Documentos Individuales</a></li>
              <li class="nav-item"><a class="nav-link py-1" href="<?php echo base_url(); ?>facturacion/resumenesi">Resumenes de Boletas</a></li>
              <li class="nav-item"><a class="nav-link py-1" href="<?php echo base_url(); ?>facturacion/anulacionesi">Anulaciones (Facturas)</a></li>
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>facturacion/anulacionesb">Anulaciones (Boletas)</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Fecha Emisión</th>
                  <th>Boletas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
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


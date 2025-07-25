<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Transporte <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>transporte/privadoi','bdatos')"><i class="fa fa-plus"></i> Nuevo T. Privado</button></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>establecimiento">Transporte</a></li>
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
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>transporte">T. Privado</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>transporte/publico">T. Publico</a></li>
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
                  <th>Nombre</th>
                  <th>Documento</th>
                  <th>Licencia</th>
                  <th>Placa</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; foreach ($listas as $lista) { ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->nombres; ?></td>
                    <td><?php echo $lista->documento; ?></td>
                    <td><?php echo $lista->licencia; ?></td>
                    <td><?php echo $lista->placa; ?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>transporte/privadoi/<?php echo $lista->id; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php } ?>
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
        <h5 class="modal-title" id="modalTitle">Datos de la Transporte</h5>
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

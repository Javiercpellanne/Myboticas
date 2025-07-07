<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Usuario</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active">Usuario</li>
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
              <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>usuario">Activos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>usuario/inactivos">Inactivos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/conectados">Conectados</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/movimientos">Movimientos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/logines">Logines</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <table class="table table-hover table-striped table-bordered table-sm" id="sampleTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Perfil</th>
                  <th>Usuario</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listas as $lista) { ?>
                  <?php if ($lista->id>1): ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->nombres; ?></td>
                    <td><?php echo $lista->perfil; ?></td>
                    <td><?php echo $lista->usuario; ?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button"class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>usuario/usuariosi/<?php echo $lista->id; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>

                        <a href="<?php echo base_url(); ?>usuario/usuariosr/<?php echo $lista->id; ?>" class="btn bg-pink btn-sm py-0" title="Restaurar Contraseña" data-toggle="tooltip" data-placement="bottom"><i class="fas fa-sync-alt"></i></a>

                        <?php if ($lista->id>2): ?>
                          <a href="<?php echo base_url(); ?>usuario/habilitar/<?php echo $lista->id; ?>" class="btn btn-outline-success btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Activar"><i class="fa fa-thumbs-up"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php endif ?>
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
        <h4 class="modal-title" id="modalTitle">Datos del Usuario</h4>
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

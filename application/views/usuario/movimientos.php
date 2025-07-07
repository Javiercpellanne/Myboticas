<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Usuario</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i></li>
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
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario">Activos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/inactivos">Inactivos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/conectados">Conectados</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>usuario/movimientos">Movimientos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>usuario/logines">Logines</a></li>
            </ul>
          </div>

          <div class="card-body py-3">
            <div class="table-responsive" style="height: 600px;">
              <table class="table table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Descripcion</th>
                    <th>Pagina</th>
                    <th>Tiempo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista) { ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $lista->user; ?></td>
                      <td><?php echo $lista->descripcion; ?></td>
                      <td><?php echo $lista->pagina; ?></td>
                      <td><?php echo $lista->tiempo; ?></td>
                      <td></td>
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
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title">Datos del Usuario</h4>
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

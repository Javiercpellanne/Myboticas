<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Proveedores <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>proveedor/proveedori','bdatos')"><i class="fa fa-plus"></i> Nuevo Proveedor</button></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active">Proveedores</li>
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

            <?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Apellidos y Nombres</th>
                  <th>Documento</th>
                  <th>Telefono</th>
                  <th>Email</th>
                  <th>Direccion</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listas as $lista) { ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->nombres; ?></td>
                    <td><?php echo $lista->documento; ?></td>
                    <td><?php echo $lista->telefono; ?></td>
                    <td><?php echo $lista->email; ?></td>
                    <td><?php echo $lista->direccion; ?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>proveedor/proveedori/<?php echo $lista->id; ?>','bdatos')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>

                        <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>proveedor/proveedord/<?php echo $lista->id; ?>','<?php echo "Desea borrar ".$lista->nombres."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
                      </div>
                    </td>
                  </tr>
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
        <h4 class="modal-title" id="modalTitle">Datos del Proveedor</h4>
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

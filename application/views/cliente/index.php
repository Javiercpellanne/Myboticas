<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Clientes <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>cliente/clientei','bdatos','Datos del Cliente')"><i class="fa fa-plus"></i> Nuevo cliente</button></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active">Clientes</li>
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
            <div class="form-group row mb-1">
              <label for="bcliente" class="col-sm-1 col-form-label">BUSCAR</label>
              <div class="col-sm-6">
                <div class="input-group">
                  <input name="bcliente" type="text" id="bcliente" placeholder="Buscar Nombre del Cliente" class="form-control form-control-sm" value="" onkeyup="clienteListado('<?php echo base_url(); ?>cliente/busCliente',this.value)" autocomplete="off" autofocus>
                  <div class="input-group-append">
                    <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                  </div>
                </div>
              </div>
            </div>

            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <div class="table-responsive" style="height: 480px;">
              <table class="table table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th width="3%">#</th>
                    <th width="27%">Apellidos y Nombres</th>
                    <th width="8%">Documento</th>
                    <th width="10%">Telefono</th>
                    <th width="10%">Email</th>
                    <th width="27%">Direccion</th>
                    <th width="5%">Acciones</th>
                  </tr>
                </thead>
                <tbody id="grcliente">
                  <?php foreach ($listas as $lista) { ?>
                    <tr>
                      <td><?php echo $lista->id; ?></td>
                      <td><?php echo $lista->nombres; ?></td>
                      <td><?php echo $lista->documento; ?></td>
                      <td><?php echo $lista->telefono; ?></td>
                      <td><?php echo $lista->email; ?></td>
                      <td><?php echo $lista->direccion; ?></td>
                      <td>
                        <?php if ($lista->id>1): ?>
                          <div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>cliente/clientei/<?php echo $lista->id; ?>','bdatos','Datos del Cliente')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>

                            <?php if ($empresa->spuntos==1): ?>
                            <a href="<?php echo base_url(); ?>cliente/pacumulados/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0" title="Puntos Acumulados" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-plus-circle"></i></a>
                            <?php endif ?>

                            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>cliente/cliented/<?php echo $lista->id; ?>','<?php echo "Desea borrar ".$lista->nombres."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
                          </div>
                        <?php endif ?>
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
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title" id="modalTitle">Datos del Cliente</h4>
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

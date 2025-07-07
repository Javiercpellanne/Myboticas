<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Solicitudes Compra <a href="<?php echo base_url(); ?>solicitud/solicitudi" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nueva Solicitud</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active">Solicitudes Compra</li>
        </ol>
      </div>
    </div>
  </div>
</section>

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

            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="inicio" class="col-sm-1 control-label">DESDE</label>
                <div class="col-sm-2">
                  <input name="inicio" type="date" id="inicio" class="form-control form-control-sm" value="<?php echo $inicio; ?>" required/>
                </div>

                <label for="fin" class="col-sm-1 control-label">HASTA</label>
                <div class="col-sm-2">
                  <input name="fin" type="date" id="fin" class="form-control form-control-sm" value="<?php echo $fin; ?>" required/>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Numero</th>
                  <th>Fecha</th>
                  <th>Proveedor</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <?php if ($lista->estado==1) {$estado='Petición presupuesto';}elseif ($lista->estado==2) {$estado='Orden Compra';}else{$estado='Cancelado';}  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo 'SOL-'.$lista->id; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->proveedor; ?></td>
                    <td><?php echo $estado; ?></td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->estado==1): ?>
                          <a href="<?php echo base_url(); ?>compra/ordeni/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0" title="Generar Compra" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-sign-out-alt"></i></a>

                          <a href="<?php echo base_url(); ?>solicitud/pdfsolicitud/<?php echo $lista->id; ?>" class="btn btn-success btn-sm py-0" target="_blank" title="Detalle" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></a>

                          <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>solicitud/solicituda/<?php echo $lista->id; ?>','<?php echo "Desea anular solicitud compra Nº ".$lista->id."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Anular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a>
                        <?php endif ?>

                        <?php if ($lista->estado==2): ?>
                          <a href="<?php echo base_url(); ?>solicitud/pdfsolicitud/<?php echo $lista->id; ?>" class="btn btn-success btn-sm py-0" target="_blank" title="Detalle" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach ?>
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
        <h5 class="modal-title" id="modalTitle">Datos Movimientos</h5>
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


<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Traslados Internos (Salidas e Ingresos) <a href="<?php echo base_url(); ?>traslado/traslados" class="btn btn-info btn-sm py-0"><i class="fa fa fa-plus-circle"></i> Nuevo Traslado</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active">Traslados Internos</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="inicio" class="col-sm-1 col-form-label">DESDE</label>
                <div class="col-sm-2">
                  <input name="inicio" type="date" id="inicio" class="form-control form-control-sm" value="<?php echo $inicio; ?>" required/>
                </div>

                <label for="fin" class="col-sm-1 col-form-label">HASTA</label>
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
                  <th>Generador</th>
                  <th>Almacen Origen</th>
                  <th>Fecha Emision</th>
                  <th>Estado</th>
                  <th>Almacen Destino</th>
                  <th>Fecha Recepcion</th>
                  <th>Total Productos</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <?php
                  $cantidad=$this->trasladod_model->contador($lista->id);
                  $origen=$this->establecimiento_model->mostrar($lista->idestablecimiento);
                  $destino=$this->establecimiento_model->mostrar($lista->idestablecimientod);
                  $nombre= $this->usuario_model->mostrar($lista->iduser);
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo 'TI-'.$lista->id; ?></td>
                    <td><?php echo $nombre->nombres; ?> </td>
                    <td><?php echo $origen->descripcion ?? 'Almacen Principal'; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td>
                      <?php
                      if ($lista->nulo==1) {
                        echo '<h5 class="my-0"><span class="badge bg-danger">Anulado</span></h5>';
                      } else {
                        echo $lista->frecepcion=='' ? '<h5 class="my-0"><span class="badge bg-secondary">En transito</span></h5>' : '<h5 class="my-0"><span class="badge bg-success">Procesado</span></h5>';
                      }
                      ?>
                    </td>
                    <td><?php echo $destino->descripcion; ?></td>
                    <td><?php echo $lista->frecepcion; ?></td>
                    <td><?php echo $cantidad; ?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>traslado/consulta/<?php echo $lista->id; ?>','bdatos','Consulta de Traslado')"><i class="fa fa-eye"></i></button>

                        <?php if ($lista->nulo==0): ?>
                          <?php if ($lista->idestablecimiento==$this->session->userdata("predeterminado")): ?>
                          <a href="<?php echo base_url(); ?>despacho/despachot/<?php echo $lista->id; ?>" class="btn btn-secondary btn-sm py-0" title="Guia de Remision" data-toggle="tooltip" data-placement="bottom">GR</a>
                          <?php endif ?>

                          <a href="<?php echo base_url(); ?>traslado/pdftraslado/<?php echo $lista->id; ?>" class="btn btn-success btn-sm py-0" target="_blank" title="Imprimir Traslado" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></a>

                          <?php if ($lista->idestablecimientod==$this->session->userdata("predeterminado") && $lista->frecepcion==NULL): ?>
                              <a href="<?php echo base_url(); ?>traslado/trasladoi/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"title="Ingreso Productos" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-cubes"></i></a>
                          <?php endif ?>

                          <?php if ($lista->idestablecimiento==$this->session->userdata("predeterminado") && $lista->frecepcion==NULL): ?>
                            <a href="<?php echo base_url(); ?>traslado/trasladoa/<?php echo $lista->id; ?>" class="btn btn-danger btn-sm py-0"title="Anular Traslado" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a>
                          <?php endif ?>
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
        <h5 class="modal-title" id="modalTitle">Consulta Movimiento</h5>
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

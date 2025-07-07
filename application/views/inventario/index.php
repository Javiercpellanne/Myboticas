<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Actualizar Inventario <a href="<?php echo base_url(); ?>inventario/inventarioi" class="btn btn-info btn-sm py-0"><i class="fa fa fa-plus"></i> Nuevo Actualizar</a> <button type="button"class="btn btn-warning btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>inventario/inventariox','bdatos','Actualizar inventario de los productos')"><i class="fa fa-upload"></i> Actualizar Excel</button></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active">Actualizar Inventario</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary card-outline">
          <div class="card-body p-3">
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

                <div class="col-sm-3">
                  <h5 class="text-danger">REEMPLAZA EL STOCK ACTUAL</h5>
                </div>
              </div>
            <?php echo form_close(); ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Numeracion</th>
                  <th>Fecha Emision</th>
                  <th>Importe</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo 'Inventario N° '.$lista->numero; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->importe; ?></td>
                    <td>
                      <?php
                      if ($lista->estado==0) {
                        echo '<h5 class="my-0"><span class="badge bg-secondary">Registrado</span></h5>';
                      }else{
                        echo '<h5 class="my-0"><span class="badge bg-success">Actualizado</span></h5>';
                      }
                      ?>
                      </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->estado==0): ?>
                          <a href="<?php echo base_url(); ?>inventario/inventarioi/<?php echo $lista->numero; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>
                        <?php endif ?>

                        <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>inventario/consulta/<?php echo $lista->numero; ?>','bdatos','Consulta de inventario')"><i class="fa fa-eye"></i></button>

                        <a href="<?php echo base_url(); ?>inventario/pdfinventario/<?php echo $lista->numero; ?>" class="btn btn-success btn-sm py-0" target="_blank" title="Imprimir inventario" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></a>
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
        <h5 class="modal-title" id="modalTitle">Datos del Inventario</h5>
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


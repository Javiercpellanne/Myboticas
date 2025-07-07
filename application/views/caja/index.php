<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Arqueo de Caja <?php if ($arqueoc==0): ?><button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>caja/arqueoi','bdatos','Datos del Arqueo')"><i class="fa fa-plus"></i> Nuevo Arqueo</button><?php endif ?></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Caja</li>
          <li class="breadcrumb-item active">Arqueo de Caja</li>
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
                <?php echo $this->session->flashdata('mensaje'); ?>
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
                  <th>Usuario</th>
                  <th>Fecha Inicial</th>
                  <th>Monto Inicial</th>
                  <th>Fecha Final</th>
                  <th>Monto Final</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <?php $nombre=$this->usuario_model->mostrar($lista->iduser); ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $nombre->nombres??''; ?></td>
                    <td><?php echo $lista->finicial; ?></td>
                    <td><?php echo $lista->minicial ; ?></td>
                    <td><?php echo $lista->ffinal; ?></td>
                    <td><?php echo $lista->mfinal; ?></td>
                    <td>
                      <?php if ($lista->estado==1): ?>
                        <?php if ($empresa->arqueo==1): ?>
                          <button type="button" class="btn btn-danger btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>caja/cerrar/<?php echo $lista->id; ?>','bdatos','Datos del Arqueo')" title="Cerrar Caja" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-lock"></i></button>
                        <?php else: ?>
                          <a href="<?php echo base_url(); ?>caja/arqueoc/<?php echo $lista->id; ?>" class="btn btn-danger btn-sm py-0" title="Cerrar Caja" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-lock"></i></a>
                        <?php endif ?>
                      <?php else: ?>
                        <button type="button"class="btn btn-success btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>caja/opciones/<?php echo $lista->id; ?>','bdatos','Opciones Impresion')" title="Impresion" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></button>
                      <?php endif ?>
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
        <h4 class="modal-title" id="modalTitle">Datos del Arqueo</h4>
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

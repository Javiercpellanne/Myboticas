<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Nota de Venta
          <?php if ($arqueoc>0): ?>
            <a href="<?php echo base_url(); ?>nventa/nventai" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nueva Venta</a>
          <?php else: ?>
            <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>nventa/arqueoi','bdatos','Datos del Arqueo')"><i class="fa fa-plus"></i> Nuevo Arqueo</button>
          <?php endif ?>
        </h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active">Nota de Venta</li>
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

            <?php if ($empresa->lventa==1): ?>
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
            <?php endif ?>

            <?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%">
              <thead>
                  <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Comprobante</th>
                    <th>Cliente</th>
                    <th>Importe</th>
                    <th>Emitido</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista): ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $lista->femision; ?></td>
                      <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
                      <td><?php echo $lista->cliente; ?></td>
                      <td><?php echo $lista->total; ?></td>
                      <td><?php echo $lista->emitido; ?></td>
                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>nventa/consulta/<?php echo $lista->id; ?>','bdatos','Consulta de Documento')"><i class="fa fa-eye"></i></button>

                          <?php if ($lista->nulo==0): ?>
                            <?php if ($arqueoc>0 && $anulacionv->anulacion==1): ?>
                              <?php if ($lista->emitido=='' && $empresa->facturacion==1): ?>
                              <a href="<?php echo base_url(); ?>venta/nventai/<?php echo $lista->id; ?>" class="btn btn-primary btn-sm py-0" title="Generar CPE" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-sign-out-alt"></i></a>
                              <?php endif ?>

                              <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>nventa/nventaa/<?php echo $lista->id; ?>','<?php echo "Desea anular ".$lista->serie.'-'.$lista->numero."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Anular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a>
                            <?php endif ?>

                            <button type="button"class="btn btn-success btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>nventa/opciones/<?php echo $lista->id; ?>','bdatos','Opciones Impresion')" title="Impresion" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></button>
                          <?php else: ?>
                            <a href="<?php echo base_url(); ?>nventa/copias/<?php echo $lista->id; ?>" class="btn bg-purple btn-sm py-0" title="Duplicar" data-toggle="tooltip" data-placement="bottom"><i class="fas fa-copy"></i></a>
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
        <h4 class="modal-title" id="modalTitle">Datos de la Venta</h4>
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

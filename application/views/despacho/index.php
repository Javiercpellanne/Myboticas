<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Guia de Remision <a href="<?php echo base_url(); ?>despacho/despachoi" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nueva Guia de Remision</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active">Guia de Remision</li>
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
                  <th>Fecha</th>
                  <th>Numero</th>
                  <th>Cliente</th>
                  <th>Fecha Envío</th>
                  <th>Estado</th>
                  <th>Descargas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <?php $estadod=$this->testado_model->mostrar($lista->tipo_estado); ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
                    <td><?php echo $lista->cliente; ?></td>
                    <td><?php echo $lista->fenvio; ?></td>
                    <td><h5 class="my-0"><span class="badge <?php echo $estadod->badge; ?>"><?php echo $estadod->descripcion; ?></span></h5></td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->has_xml==1): ?>
                          <a href="<?php echo base_url() ?>downloads/xml/<?php echo $lista->filename.'.xml' ?>" class="btn btn-success btn-sm py-0" target="_blank">XML</a>
                        <?php endif ?>

                        <?php if ($lista->has_pdf==1): ?>
                          <a href="<?php echo base_url() ?>downloads/pdf/<?php echo $lista->filename.'.pdf' ?>" class="btn btn-primary btn-sm py-0" target="_blank">PDF</a>
                        <?php endif ?>

                        <?php if ($lista->has_cdr==1): ?>
                          <a href="<?php echo base_url(); ?>downloads/cdr/<?php echo 'R-'.$lista->filename.'.zip'; ?>" class="btn btn-info btn-sm py-0" target="_blank">CDR</a>
                        <?php endif ?>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>despacho/consulta/<?php echo $lista->id; ?>','bdatos','Consulta de Documento')"><i class="fa fa-eye"></i></button>

                        <?php if ($lista->tipo_estado=='03' && $lista->has_cdr==0): ?>
                        <a href="<?php echo base_url(); ?>facturacion/consultarGuia/<?php echo $lista->id; ?>/1" class="btn btn-primary btn-sm py-0"><i class="fa fa-upload"></i></a>
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
        <h5 class="modal-title" id="modalTitle">Datos de la Serie</h5>
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


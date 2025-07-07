<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Comprobante</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active">Comprobante</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary">
      <div class="card-header py-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>venta">Boletas y Facturas</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>venta/ncredito">Notas Credito</a></li>
        </ul>
      </div>
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

        <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
          <thead>
            <tr>
              <th>#</th>
              <th>Fecha</th>
              <th>Comprobante</th>
              <th>Numero</th>
              <th>Cliente</th>
              <th>Doc. Afectado</th>
              <th>Importe</th>
              <th>Estado</th>
              <th>Descargas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1; ?>
            <?php foreach ($listas as $lista): ?>
              <?php
              $estadod=$this->testado_model->mostrar($lista->tipo_estado);
              $ventas=$this->venta_model->mostrar($lista->idventa);
              $color='';
              if ($ventas->tcomprobante=='01' && $lista->tipo_estado>='05') {
                $color=file_exists('./downloads/cdr/R-'.$lista->filename.'.zip') ? '': 'table-info';
              }
              ?>
              <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $lista->femision; ?></td>
                <td><?php echo $lista->ncomprobante; ?></td>
                <td class="<?php echo $color; ?>"><?php echo $lista->serie.'-'.$lista->numero; ?></td>
                <td><?php echo $lista->cliente; ?></td>
                <td><?php echo $ventas->serie.'-'.$ventas->numero; ?></td>
                <td><?php echo $lista->total; ?></td>
                <td><h5 class="my-0"><span class="badge <?php echo $estadod->badge; ?>"><?php echo $estadod->descripcion; ?></span></h5></td>
                <td>
                  <div class="btn-group">
                    <?php if ($lista->has_xml==1): ?>
                      <a href="<?php echo base_url().'venta/descarga/'.$lista->filename; ?>" class="btn btn-success btn-sm py-0">XML</a>
                      <!-- <a href="<?php echo base_url(); ?>downloads/xml/<?php echo $lista->filename.'.xml' ?>" class="btn btn-success btn-sm py-0">XML</a> -->
                    <?php endif ?>

                    <?php if ($lista->has_pdf==1): ?>
                      <a href="<?php echo base_url(); ?>downloads/pdf/<?php echo $lista->filename.'.pdf' ?>" class="btn btn-primary btn-sm py-0" target="_blank">PDF</a>
                    <?php endif ?>

                    <?php if ($lista->has_cdr==1): ?>
                      <a href="<?php echo base_url(); ?>downloads/cdr/<?php echo 'R-'.$lista->filename.'.zip' ?>" class="btn btn-info btn-sm py-0">CDR</a>
                    <?php endif ?>
                  </div>
                </td>
                <td>
                  <div class="btn-group">
                    <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>venta/consultan/<?php echo $lista->id; ?>','bdatos','Consulta de Documento')"><i class="fa fa-eye"></i></button>

                    <?php if (SumarFecha('+7 day',$lista->femision)>date("Y-m-d")): ?>
                      <!-- <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>venta/anulacionn/<?php echo $lista->id; ?>','<?php echo "Desea anular ".$lista->serie.'-'.$lista->numero."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Anular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a> -->
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
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title" id="modalTitle">Datos del Comprobante</h4>
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

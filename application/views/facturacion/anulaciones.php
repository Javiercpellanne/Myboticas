<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Listado de Anulaciones</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active">Listado de Anulaciones</li>
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
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>facturacion/anulaciones">Generales</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>facturacion/pendientea">Pendientes</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row">
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

            <?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Fecha Emisión</th>
                  <th>Fecha Referencia</th>
                  <th>Identificador</th>
                  <th>Ticket</th>
                  <th>Estado</th>
                  <th>Numeracion Comprobante</th>
                  <th>Descargas</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listasb as $lista): ?>
                  <?php
                  $numerosb=$this->resumend_model->mostrarVentas($lista->id);
                  $numerosn=$this->resumend_model->mostrarNotas($lista->id);
                  $estadod=$this->testado_model->mostrar($lista->tipo_estado);
                  $color= $lista->validado==1 ? 'text-danger' : '';
                  $colorr='';
                  if ($lista->tipo_estado>='05') {
                    $colorr=file_exists('./downloads/cdr/R-'.$lista->filename.'.zip') ? '': 'table-info';
                  }
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->fdocumento; ?></td>
                    <td class="<?php echo $colorr; ?>"><?php echo $lista->identificador; ?></td>
                    <td class="<?php echo $color ?>"><?php echo $lista->ticket; ?></td>
                    <td><h5 class="my-0"><span class="badge <?php echo $estadod->badge; ?>"><?php echo $estadod->descripcion; ?></span></h5></td>
                    <td>
                      <?php
                      foreach ($numerosb as $numero) {
                        echo $numero->venta.', ';
                      }
                      foreach ($numerosn as $numero) {
                        echo $numero->nota.', ';
                      }
                      ?>
                      <?php if ($lista->tipo_estado=='09'): ?>
                        <?php echo '<br>'.$lista->respuesta_sunat; ?>
                      <?php endif ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->has_xml==1): ?>
                          <a href="<?php echo base_url(); ?>downloads/xml/<?php echo $lista->filename.'.xml'; ?>" class="btn btn-success btn-sm py-0" target="_blank">XML</a>
                        <?php endif ?>

                        <?php if ($lista->has_cdr==1): ?>
                          <a href="<?php echo base_url(); ?>downloads/cdr/<?php echo 'R-'.$lista->filename.'.zip'; ?>" class="btn btn-info btn-sm py-0" target="_blank">CDR</a>
                        <?php endif ?>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->has_cdr==0): ?>
                          <?php if ($lista->ticket!=''): ?>
                            <a href="<?php echo base_url(); ?>facturacion/consultarResumen/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
                          <?php endif ?>

                          <?php if ($lista->validado==0): ?>
                            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>facturacion/eliminarb/<?php echo $lista->id; ?>','<?php echo "Desea borrar ".$lista->identificador."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
                          <?php endif ?>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach ?>

                <?php foreach ($listasf as $lista): ?>
                  <?php
                  $numerosf=$this->anuladod_model->mostrarVentas($lista->id);
                  $estadod=$this->testado_model->mostrar($lista->tipo_estado);
                  $color= $lista->validado==1 ? 'text-danger' : '';
                  $colorr='';
                  if ($lista->tipo_estado>='05') {
                    $colorr=file_exists('./downloads/cdr/R-'.$lista->filename.'.zip') ? '': 'table-info';
                  }
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->fdocumento; ?></td>
                    <td class="<?php echo $colorr; ?>"><?php echo $lista->identificador; ?></td>
                    <td class="<?php echo $color ?>"><?php echo $lista->ticket; ?></td>
                    <td><h5 class="my-0"><span class="badge <?php echo $estadod->badge; ?>"><?php echo $estadod->descripcion; ?></span></h5></td>
                    <td>
                      <?php
                      foreach ($numerosf as $numero) {
                        echo $numero->venta.', ';
                      }
                      ?>
                      <?php if ($lista->tipo_estado=='09'): ?>
                        <?php echo '<br>'.$lista->respuesta_sunat; ?>
                      <?php endif ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->has_xml==1): ?>
                          <a href="<?php echo base_url(); ?>downloads/xml/<?php echo $lista->filename.'.xml'; ?>" class="btn btn-success btn-sm py-0" target="_blank">XML</a>
                        <?php endif ?>

                        <?php if ($lista->has_cdr==1): ?>
                          <a href="<?php echo base_url(); ?>downloads/cdr/<?php echo 'R-'.$lista->filename.'.zip'; ?>" class="btn btn-info btn-sm py-0" target="_blank">CDR</a>
                        <?php endif ?>
                      </div>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->has_cdr==0): ?>
                          <?php if ($lista->ticket!=''): ?>
                            <a href="<?php echo base_url(); ?>facturacion/consultarBaja/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
                          <?php endif ?>

                          <?php if ($lista->validado==0): ?>
                            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>facturacion/eliminara/<?php echo $lista->id; ?>','<?php echo "Desea borrar ".$lista->identificador."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
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
      <h5 class="modal-title" id="modalTitle">Datos del Cliente</h5>
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

<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Comprobante no enviados</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active">Comprobante no enviados</li>
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
              <li class="nav-item"><a class="nav-link py-1" href="<?php echo base_url(); ?>facturacion">Documentos Individuales</a></li>
              <li class="nav-item"><a class="nav-link py-1" href="<?php echo base_url(); ?>facturacion/resumenesi">Resumenes de Boletas</a></li>
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>facturacion/anulacionesi">Anulaciones</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Fecha Emisión</th>
                  <th>Documentos</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listaf as $lista): ?>
                  <?php
                  $numerosb=$this->venta_model->mostrarTotal(array('femision'=>$lista->femision,"tcomprobante"=>'01',"tipo_estado<"=>'11',"nulo"=>1),"asc");
                  $numeracion=array(); $repetidos=0; $enviar=0;
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td style="font-size: 0.7rem;">
                      <?php
                      foreach ($numerosb as $numero) {
                        if (in_array($numero->serie.'-'.$numero->numero,$numeracion)) {
                          $repetidos+=1; $color='text-danger';
                        } else {
                          array_push($numeracion,$numero->serie.'-'.$numero->numero); $color='text-muted';
                        }
                        echo '<span class="'.$color.'">'.$numero->serie.'-'.$numero->numero.'</span>, ';
                        if ($numero->tipo_estado=='01') {$enviar+=1;}
                      }
                      ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($enviar==0 && $repetidos==0 && date("Y-m-d")<=SumarFecha('+7 day',$lista->femision)): ?>
                          <a href="<?php echo base_url(); ?>facturacion/enviarAnulado/<?php echo $lista->femision; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach ?>

                <?php foreach ($listab as $lista): ?>
                  <?php
                  $numerosb=$this->venta_model->mostrarTotal(array('femision'=>$lista->femision,"grupo"=>'02',"tipo_estado<"=>'11',"nulo"=>1),"asc");
                  $numerosn=$this->nota_model->mostrarTotal(array('femision'=>$lista->femision,"grupo"=>'02',"tipo_estado<"=>'11',"nulo"=>1),"asc");
                  $numeracion=array(); $repetidos=0; $enviar=0;
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td style="font-size: 0.7rem;">
                      <?php
                      foreach ($numerosb as $numero) {
                        if (in_array($numero->serie.'-'.$numero->numero,$numeracion)) {
                          $repetidos+=1; $color='text-danger';
                        } else {
                          array_push($numeracion,$numero->serie.'-'.$numero->numero); $color='text-muted';
                        }
                        echo '<span class="'.$color.'">'.$numero->serie.'-'.$numero->numero.'</span>, ';
                        if ($numero->tipo_estado<='03') {$enviar+=1;}
                      }

                      foreach ($numerosn as $numero) {
                        if (in_array($numero->serie.'-'.$numero->numero,$numeracion)) {
                          $repetidos+=1; $color='text-danger';
                        } else {
                          array_push($numeracion,$numero->serie.'-'.$numero->numero); $color='text-muted';
                        }
                        echo '<span class="'.$color.'">'.$numero->serie.'-'.$numero->numero.'</span>, ';
                        if ($numero->tipo_estado<='03') {$enviar+=1;}
                      }
                      ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($enviar==0 && $repetidos==0 && date("Y-m-d")<=SumarFecha('+7 day',$lista->femision)): ?>
                          <a href="<?php echo base_url(); ?>facturacion/enviarAnuladob/<?php echo $lista->femision; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
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


<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Pagos <a href="<?php echo base_url(); ?>pagos/excelpagar" class="btn btn-success btn-sm py-0"><i class="fa fa-file-excel"></i> EXCEL</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Caja</li>
          <li class="breadcrumb-item active">Pagos</li>
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
              <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>pagos">Efectuados</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>pagos/pagar">Cta por Pagar</a></li>
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
                  <th>Fecha</th>
                  <th>Comprobante</th>
                  <th>Proveedor</th>
                  <th>Importe</th>
                  <th>Pagado</th>
                  <th>Saldo</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista): ?>
                  <?php $pagado=$this->pago_model->montoTotal(array("idcompra"=>$lista->id)); ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $lista->femision; ?></td>
                      <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
                      <td><?php echo $lista->proveedor; ?></td>
                      <td align="right"><?php echo $lista->total; ?></td>
                      <td align="right"><?php echo $pagado->total; ?></td>
                      <td align="right"><?php echo $lista->total-$pagado->total; ?></td>
                      <td>
                        <a href="<?php echo base_url(); ?>pagos/pagari/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0" title="Registrar Pago" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>
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


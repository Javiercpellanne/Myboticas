<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Puntos Acumulados Cliente</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>cliente">Cliente</a></li>
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

            <div class="form-group row my-0">
              <label for="nombres" class="col-sm-2 col-form-label">Cliente</label>
              <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="nombres" value="<?php echo $datos->nombres ?>">
              </div>
            </div>

            <div class="form-group row my-0">
              <label for="documento" class="col-sm-2 col-form-label">Documento</label>
              <div class="col-sm-10">
                <input type="text" readonly class="form-control-plaintext" id="documento" value="<?php echo $datos->documento ?>">
              </div>
            </div>

            <table class="table table-bordered table-sm">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Fecha Emision</th>
                  <th>Cantidad</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; $importe=0; ?>
                <?php foreach ($listas as $lista) { ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->year.'/'.$lista->month; ?></td>
                    <td><?php echo $lista->cantidad; ?></td>
                  </tr>
                  <?php $i++; $importe+=$lista->cantidad; ?>
                <?php } ?>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" align="right" class="table-dark"><strong>Total Puntos</strong></td>
                  <td><?php echo $importe; ?></td>
                </tr>
              </tfoot>
            </table>

            <?php if ($importe>=$vcanje->canjep): ?>
              <div class="row">
                <div class="col-md-12 text-right">
                  <a href="<?php echo base_url(); ?>cliente/canjerv/<?php echo $id; ?>" class="btn btn-info btn-sm" title="Canjear Vale" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ticket"></i>Canjear Vales</a>
                </div>
              </div>
            <?php endif ?>

            <hr>
            <h5>Vale Generado</h5>
            <table class="table table-bordered table-sm">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th>Fecha Emision</th>
                  <th>DNI</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($vales as $vale) { ?>
                  <tr>
                    <td><?php echo $vale->id; ?></td>
                    <td><?php echo $vale->femision; ?></td>
                    <td><?php echo $vale->dni; ?></td>
                    <td>
                      <a href="<?php echo base_url(); ?>cliente/pdfcanje/<?php echo $vale->id; ?>" class="btn btn-success btn-sm" target="_blank" title="Imprimir Vale" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

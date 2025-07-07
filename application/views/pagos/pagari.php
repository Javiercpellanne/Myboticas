<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Cuentas por Pagar</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Caja</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>pagar">Cuentas por Pagar</a></li>
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

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
              <div class="form-group row my-0">
                <label class="col-sm-2 control-label"><b>Proveedor</b></label>
                <div class="col-sm-6">
                  <?php echo $datos->proveedor; ?>
                </div>

                <label class="col-sm-2 control-label"><b>Comprobante</b></label>
                <div class="col-sm-2">
                  <?php echo $datos->serie.'-'.$datos->numero; ?>
                </div>
              </div>

              <div class="form-group row my-0">
                <label class="col-sm-2 control-label"><b>Importe</b></label>
                <div class="col-sm-2">
                  <?php echo $datos->total; ?>
                </div>
              </div>

              <hr>
              <table class="table table-bordered table-sm">
                <thead class="table-dark">
                  <tr>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Medio Pago</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $importe=0; ?>
                  <?php foreach ($pagos as $pago): ?>
                    <tr>
                      <td><?php echo $pago->femision; ?></td>
                      <td align="right"><?php echo $pago->total; ?></td>
                      <td><?php echo $pago->ntpago; ?></td>
                    </tr>
                    <?php $importe+=$pago->total; ?>
                  <?php endforeach ?>
                </tbody>
                <?php $saldo=$datos->total-$importe; ?>
                <tfoot>
                  <tr>
                    <td align="right" class="table-dark"><strong>Total Pagado</strong></td>
                    <td align="right"><?php echo formatoPrecio($importe) ?></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td align="right" class="table-dark"><strong>Deuda Actual</strong></td>
                    <td align="right"><?php echo formatoPrecio($saldo); ?> </td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>

              <div class="form-group row mb-1">
                <input type="hidden" name="saldo" id="saldo" value="<?php echo $saldo  ?>">
                <label for="importe" class="col-sm-1 control-label">Monto*</label>
                <div class="col-sm-2">
                  <input type="text" class="form-control form-control-sm" name="importe" id="importe" value="<?php echo $saldo ?>" required>
                </div>

                  <label for="mpago" class="col-sm-2 control-label">Medio Pago*</label>
                <div class="col-sm-3">
                  <select name="mpago" id="mpago" class="form-control form-control-sm" required>
                    <option value="">::Seleccione</option>
                    <?php foreach ($mpagos as $mpago): ?>
                      <option value="<?php echo $mpago->id ?>"><?php echo $mpago->descripcion ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <label for="documento" class="col-sm-2 control-label">Doc sustenta</label>
                <div class="col-sm-2">
                  <input type="text" class="form-control form-control-sm" name="documento" id="documento" value="">
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-sm-2 offset-5">
                  <button type="submit" class="btn btn-primary btn-sm">GUARDAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
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


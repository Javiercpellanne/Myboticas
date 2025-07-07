<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Comprobante</label>
  <div class="col-sm-3">
    <?php echo $datos->serie.'-'.$datos->numero; ?>
  </div>

  <label class="col-sm-3 control-label">Fecha Emision</label>
  <div class="col-sm-3">
    <?php echo $datos->femision; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Proveedor</label>
  <div class="col-sm-9">
    <?php echo $datos->proveedor; ?>
  </div>
</div>

<table class="table table-striped table-sm">
  <thead>
    <tr>
        <td align="center"><strong>FECHA</strong></td>
        <td align="center"><strong>IMPORTE</strong></td>
        <td align="center"><strong>MEDIO PAGO</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php $importe=0; ?>
    <?php foreach ($listas as $lista) { ?>
    <tr>
      <td><?php echo $lista->femision; ?></td>
      <td align="right"><?php echo $lista->total; ?></td>
      <td><?php echo $lista->ntpago.' '.$lista->documento; ?></td>
    </tr>
    <?php $importe+=$lista->total; ?>
    <?php } ?>
  </tbody>
  <tfoot>
    <tr>
      <td align="right"><strong>PAGADO : S/.</strong></td>
      <td align="right"><?php echo formatoPrecio($importe); ?></td>
      <td></td>
    </tr>
  </tfoot>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>


<table class="table table-bordered table-sm">
  <thead>
    <tr>
        <td align="center" width="65%"><strong>DESCRIPCION</strong></td>
        <td align="center" width="10%"><strong>CANTIDAD</strong></td>
        <td align="center" width="10%"><strong>P.UNIT</strong></td>
        <td align="center" width="15%"><strong>IMPORTE</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php $importe=0; ?>
    <?php foreach ($detalles as $detalle) { ?>
    <?php
    $lotes='';
    if ($detalle->lote!='') {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    ?>
    <tr>
      <td><?php echo $detalle->descripcion.$lotes; ?></td>
      <td><?php echo $detalle->cantidad; ?></td>
      <td align="right"><?php echo $detalle->precio; ?></td>
      <td align="right"><?php echo $detalle->importe; ?></td>
    </tr>
    <?php $importe+=$detalle->importe; ?>
    <?php } ?>
  </tbody>
  <tfoot>
  <tr>
    <td align="right" colspan="3"><strong>IMPORTE TOTAL: S/</strong></td>
    <td align="right"><?php echo formatoPrecio($importe); ?></td>
  </tr>
  </tfoot>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

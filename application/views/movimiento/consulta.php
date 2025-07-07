<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Motivo</label>
  <div class="col-sm-3">
    <?php echo $datos->nmtraslado; ?>
  </div>

  <label class="col-sm-3 control-label">Fecha Emision</label>
  <div class="col-sm-3">
    <?php echo $datos->femision; ?>
  </div>
</div>

<table class="table table-striped table-sm">
  <thead>
    <tr>
        <td align="center" width="60%"><strong>DESCRIPCION</strong></td>
        <td align="center" width="5%"><strong>MEDIDA</strong></td>
        <td align="center" width="10%"><strong>CANT</strong></td>
        <td align="center" width="10%"><strong>P.UNIT</strong></td>
        <td align="center" width="15%"><strong>IMPORTE</strong></td>
    </tr>
  </thead>
  <tbody>
    <?php $gdscto=0; ?>
    <?php foreach ($detalles as $detalle) { ?>
    <?php
    $lotes='';
    if ($detalle->lote!='') {
        $lotes="<br> Lote : ".$detalle->lote." -- Vcto : ".$detalle->fvencimiento;
    }
    ?>
    <tr>
      <td><?php echo $detalle->descripcion.$lotes; ?></td>
      <td><?php echo $detalle->unidad; ?></td>
      <td><?php echo $detalle->cantidad; ?></td>
      <td align="right"><?php echo $detalle->precio; ?></td>
      <td align="right"><?php echo $detalle->importe; ?></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
  <tr>
    <td align="right" colspan="4"><strong>IMPORTE PAGAR : S/.</strong></td>
    <td align="right"><?php echo $datos->importe; ?></td>
  </tr>
  </tfoot>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

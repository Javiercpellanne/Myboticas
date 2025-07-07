<?php if ($datos->tipo_estado!='01'): ?>
  <?php $color= $datos->tipo_estado=='05' ? 'success': 'danger'; ?>
  <div class="alert alert-<?php echo $color; ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php echo $datos->respuesta_sunat; ?>
  </div>
<?php endif ?>

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
  <label class="col-sm-3 control-label">Cliente</label>
  <div class="col-sm-9">
    <?php echo $datos->cliente; ?>
  </div>
</div>

<table class="table table-striped table-sm">
  <thead>
    <tr>
        <td align="center" width="61%"><strong>DESCRIPCION</strong></td>
        <td align="center" width="5%"><strong>MEDIDA</strong></td>
        <td align="center" width="8%"><strong>CANT</strong></td>
        <td align="center" width="8%"><strong>P.UNIT</strong></td>
        <td align="center" width="8%"><strong>DSCTO</strong></td>
        <td align="center" width="10%"><strong>IMPORTE</strong></td>
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

    if ($detalle->descuentos!='') {
        $descuentos=json_decode($detalle->descuentos);
        $ddscto= formatoPrecio($descuentos->monto+($descuentos->monto*0.18));
        $gdscto+=floatval($ddscto);
    } else {
        $ddscto='';
    }
    ?>
    <tr>
      <td><?php echo $detalle->descripcion.$lotes; ?></td>
      <td><?php echo $detalle->unidad; ?></td>
      <td><?php echo $detalle->cantidad; ?></td>
      <td align="right"><?php echo $detalle->precio; ?></td>
      <td align="right"><?php echo $ddscto; ?></td>
      <td align="right"><?php echo $detalle->importe; ?></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
  <?php if ($datos->descuentos!=''): ?>
    <?php $descuentos=json_decode($datos->descuentos); ?>
  <tr>
    <td align="right" colspan="5"><strong>DESCUENTO : S/.</strong></td>
    <td align="right"><?php echo formatoPrecio($descuentos->monto+($descuentos->monto*0.18)); ?></td>
  </tr>
  <?php endif ?>
  <tr>
    <td align="right" colspan="5"><strong>IMPORTE : S/.</strong></td>
    <td align="right"><?php echo $datos->total; ?></td>
  </tr>
  </tfoot>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

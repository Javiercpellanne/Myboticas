<?php
$nproducto=$datos->descripcion;
if ($datos->nlaboratorio!='') {$nproducto.=' ['.$datos->nlaboratorio.']';}

$pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $datos->pventa;
$pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $datos->pblister;
$venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $datos->venta;
?>

<h5><b>PRODUCTO : <?php echo $nproducto; ?></b></h5>
<table class="table table-bordered table-striped table-hover table-sm">
  <thead class="thead-dark">
    <tr>
      <th>DESCRIPCION</th>
      <th>MEDIDA</th>
      <th>FACTOR</th>
      <th>PRECIO</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Precio Unidad</td>
      <td>UNIDAD</td>
      <td></td>
      <td align="center"><?php echo $pventa; ?></td>
    </tr>
    <tr>
      <td>Precio Caja</td>
      <td>CAJA</td>
      <td align="center"><?php echo ' X '.$datos->factor; ?></td>
      <td align="center">
        <?php if (intval($cantidad->stock)>=$datos->factor): ?>
        <a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="appvrapido('<?php echo $datos->id; ?>', `<?php echo $nproducto.'CJ X '.$datos->factor; ?>`,'<?php echo $datos->umedidac; ?>','<?php echo $datos->factor; ?>','<?php echo $datos->tafectacion; ?>','<?php echo $venta; ?>','<?php echo $datos->lote; ?>','<?php echo $cantidad->stock; ?>','<?php echo $datos->tipo; ?>','<?php echo $empresa->pventa; ?>');">
          <?php echo $venta; ?>
        </a>
        <?php else: ?>
          <?php echo $venta; ?>
        <?php endif ?>
      </td>
    </tr>
    <?php if ($datos->factorb>0 && $pblister>0 && $datos->umedidab!=''): ?>
    <tr>
      <td>Precio Blister</td>
      <td>PAQUETE</td>
      <td align="center"><?php echo ' X '.$datos->factorb; ?></td>
      <td align="center">
        <?php if (intval($cantidad->stock)>=$datos->factorb): ?>
        <a href="javascript:void(0)" class="btn btn-primary btn-sm py-0" onclick="appvrapido('<?php echo $datos->id; ?>', `<?php echo $nproducto.'BLISTER X '.$datos->factorb; ?>`,'<?php echo $datos->umedidab; ?>','<?php echo $datos->factorb; ?>','<?php echo $datos->tafectacion; ?>','<?php echo $pblister; ?>','<?php echo $datos->lote; ?>','<?php echo $cantidad->stock; ?>','<?php echo $datos->tipo; ?>','<?php echo $empresa->pventa; ?>');">
          <?php echo $pblister; ?>
        </a>
        </a>
        <?php else: ?>
          <?php echo $pblister; ?>
        <?php endif ?>
      </td>
    </tr>
    <?php endif ?>
  </tbody>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

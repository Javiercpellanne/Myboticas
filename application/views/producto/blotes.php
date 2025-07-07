<div id="mensajeerrol"></div>
<table class="table table-striped table-sm">
  <thead>
    <tr>
      <th>Codigo</th>
      <th>Cantidad</th>
      <th>Fecha vencimiento</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($lotes as $lote): ?>
    <tr>
      <td><div class="form-check"><label class="form-check-label"><input class="form-check-input nlote" type="checkbox" value="<?php echo $lote->nlote.'|'.$lote->stock; ?>" onclick="marcadol(this)"><?php echo $lote->nlote; ?></label><div></td>
      <td><?php echo $lote->stock; ?></td>
      <td><?php echo $lote->fvencimiento; ?></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>

<input type="hidden" name="lcantidad" id="lcantidad" value="0">
<input type="hidden" name="lentregar" id="lentregar" value="0">
<input type="hidden" name="rlote" id="rlote" value="">
<input type="hidden" name="orden" id="orden" value="<?php echo $nro; ?>">

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-primary btn-sm ml-4" onclick="applotes();">AGREGAR</button>
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

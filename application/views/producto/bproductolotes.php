<?php
$nproducto=$datos->descripcion;
if ($datos->nlaboratorio!='') {$nproducto.=' ['.$datos->nlaboratorio.']';}
$pventa=$empresa->pestablecimiento==1 ? $cantidades->pventa: $datos->pventa;
$venta=$empresa->pestablecimiento==1 ? $cantidades->venta: $datos->venta;
?>
<form name="fproducto" id="fproducto" autocomplete="off">
  <div id="mensajeerror"></div>
  <input type="hidden" name="mcodigo" id="mcodigo" value="<?php echo $datos->id; ?>">
  <div class="form-group row mb-1">
    <label for="mdescripcion" class="col-sm-2 col-form-label">Producto</label>
    <div class="col-sm-10">
      <input name="mdescripcion" id="mdescripcion" type="text" class="form-control form-control-sm" value="<?php echo $nproducto; ?>" readonly>
    </div>
  </div>

  <div class="form-group row mb-1">
    <label for="mmedida" class="col-sm-2 col-form-label">Tipo Precio</label>
    <div class="col-sm-3">
      <select name="mmedida" id="mmedida" class="form-control form-control-sm" onchange="conversion(this.value)">
        <option value="<?php echo $datos->umedidav.'|1|'.$pventa; ?>">Precio Unidad</option>
        <?php if ($datos->factor>1): ?>
          <option value="<?php echo $datos->umedidac.'|'.$datos->factor.'|'.$venta; ?>">Precio Caja</option>
        <?php endif ?>
      </select>
    </div>
    <input type="hidden" id="mfactor" name="mfactor" value="1">
    <input type="hidden" name="mafectacion" id="mafectacion" value="<?php echo $datos->tafectacion; ?>">

    <label for="mstock" class="col-sm-2 col-form-label">Stock Actual</label>
    <div class="col-sm-2">
      <input name="mstock" id="mstock" type="text" class="form-control form-control-sm" value="<?php echo $cantidades->stock; ?>" readonly>
    </div>
  </div>

  <hr>
  <div class="form-group row mb-1">
    <label for="munidades" class="col-sm-2 col-form-label">Cantidad</label>
    <div class="col-sm-2">
      <input name="munidades" id="munidades" type="text" class="form-control form-control-sm" value="1" onkeyup="factores('munidades','mcosto','mtotal');factores('munidades','mfactor','mcantidad');" required>
    </div>

    <label for="mcosto" class="col-sm-2 col-form-label">Precio</label>
    <div class="col-sm-2">
      <h4><input name="mcosto" id="mcosto" type="text" class="campo text-right" value="<?php echo $pventa; ?>"></h4>
    </div>

    <label for="mtotal" class="col-sm-2 col-form-label">Total</label>
    <div class="col-sm-2">
      <h4><input name="mtotal" id="mtotal" type="text" class="campo text-right"value="<?php echo $pventa; ?>"></h4>
    </div>
  </div>
  <input type="hidden" name="mcantidad" id="mcantidad" value="1">
  <input type="hidden" name="mmonto" id="mmonto" value="<?php echo $pventa; ?>">

  <input type="hidden" name="mtipo" id="mtipo" value="<?php echo $datos->tipo; ?>">
  <input type="hidden" name="mreceta" id="mreceta" value="<?php echo $datos->vsujeta; ?>">
  <input type="hidden" name="mdscto" id="mdscto" value="<?php echo $empresa->dscto; ?>">
  <input type="hidden" name="medicion" id="medicion" value="<?php echo $empresa->pventa; ?>">
  <input type="hidden" name="mbonificar" id="mbonificar" value="<?php echo $empresa->vbonificar; ?>">
  <input type="hidden" name="mactivar" id="mactivar" value="<?php echo $datos->lote; ?>">

  <?php if ($datos->lote==1): ?>
  <h5>Lotes</h5>
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
        <td><div class="form-check"><label class="form-check-label"><input class="form-check-input nlote" type="checkbox" value="<?php echo $lote->nlote.'|'.$lote->stock; ?>" onclick="marcados(this)"><?php echo $lote->nlote; ?></label><div></td>
        <td><?php echo $lote->stock; ?></td>
        <td><?php echo $lote->fvencimiento; ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
  <?php endif ?>

  <input type="hidden" name="centregar" id="centregar" value="0">
  <input type="hidden" name="clote" id="clote" value="">
  <div class="form-group row mb-1">
    <div class="col-sm-12 text-right">
      <button type="button" class="btn btn-primary btn-sm ml-4" onclick="appvental();">AGREGAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
    </div>
  </div>
</form>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Fecha Emision</label>
  <div class="col-sm-3">
    <?php echo $datos->femision; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Comprobante</label>
  <div class="col-sm-3">
    <?php echo $datos->serie.'-'.$datos->numero; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Proveedor</label>
  <div class="col-sm-9">
    <?php echo $datos->proveedor; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Descripcion</label>
  <div class="col-sm-9">
    <?php echo $datos->dadicional; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <label class="col-sm-3 control-label">Total</label>
  <div class="col-sm-9">
    <?php echo monedaSimbolo($datos->moneda).' '.$datos->total; ?>
  </div>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

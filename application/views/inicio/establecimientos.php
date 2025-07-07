<div class="row">
  <?php foreach ($establecimientos as $establecimiento): ?>
    <div class="col-sm-12">
      <a  href="javascript:void(0)" onclick="establecimiento('<?php echo base_url() ?>inicio/asignacion/<?php echo $establecimiento->id ?>/<?php echo $establecimiento->codigo ?>')">
        <div class="info-box mb-2 bg-info">
          <span class="info-box-icon"><i class="fa fa-building"></i></span>

          <div class="info-box-content">
            <span class="info-box-text"><?php echo $establecimiento->descripcion ?></span>
            <span class="info-box-number"><?php echo $establecimiento->direccion ?></span>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach ?>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

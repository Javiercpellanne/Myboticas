<div class="row">
  <div class="col-sm-12">
    <div class="form-group row mb-2">
      <label for="buscador" class="col-sm-4 col-form-label">Razon Social o Documento del Cliente</label>
      <div class="col-sm-7">
        <input class="form-control form-control-sm" name="buscador" id="buscador" autocomplete="off" placeholder="Buscador por DNI/RUC/Nombre" onkeyup="clienteNombre('<?php echo base_url(); ?>cliente/busCliente',this.value,'<?php echo $envio; ?>')">
      </div>
    </div>
  </div>
</div>

<div class="table-responsive" style="height: 500px; font-size: .79rem">
  <table class="table table-hover table-sm">
    <thead class="thead-dark">
      <tr>
        <th width="35%">NOMBRE</th>
        <th width="15%">RUC</th>
        <th width="40%">DIRECCION</th>
        <th width="10%">AGRE.</th>
      </tr>
    </thead>
    <tbody id="tblbuscador">
      <?php foreach ($datos as $dato): ?>
        <?php
        $tpuntos=$this->clientep_model->cantidadTotal($dato->id);
        $puntos=$tpuntos->cantidad==null ? 0 : $tpuntos->cantidad ;
        ?>
        <tr>
          <td><?php echo $dato->nombres; ?></td>
          <td><?php echo $dato->documento; ?></td>
          <td><?php echo $dato->direccion.' - <i>'.$dato->ndistrito.'</i>'; ?></td>
          <td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosCliente('<?php echo $dato->id; ?>',`<?php echo $dato->nombres; ?>`,'<?php echo $dato->tdocumento; ?>','<?php echo 'Puntos Acumulados : '.$puntos; ?>','<?php echo $envio; ?>');" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

<script>
    // Enfoca el input cuando el modal se muestra
  $('#busdatos').on('shown.bs.modal', function () {
    $('#buscador').trigger('focus');
  });
</script>

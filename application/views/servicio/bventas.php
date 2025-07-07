<table class="table table-hover table-sm">
  <thead class="thead-dark">
    <tr>
      <th>Mes</th>
      <th>Precio</th>
    </tr>
  </thead>
  <tbody>
     <?php for ($i=0; $i < 13 ; $i++) { ?>
    <?php
    $mes=date("m")-$i; $anuo=date("Y");
    if ($mes<1) {$mes=$mes+12; $anuo-=1;}

    $precios=$this->nventa_model->mostrarPrecios($anuo,$mes,$id);
    ?>
    <tr>
      <td><?php echo zerofill($mes,2).'/'.$anuo; ?></td>
      <td>
        <?php foreach ($precios as $dato){
          echo $dato->precio.', ';
        } ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

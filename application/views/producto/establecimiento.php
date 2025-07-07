<h5><b>PRODUCTO : <?php echo $datos->descripcion ?></b></h5>
<div class="table-responsive" style="height: 500px;">
  <table class="table table-hover table-sm">
    <thead class="thead-dark">
      <tr>
        <th>Establecimiento</th>
        <th>Stock</th>
        <th>Precio Unidad Venta</th>
        <th>Precio Blister Venta</th>
        <th>Precio Caja Venta</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($listas as $lista): ?>
        <?php $precios=$this->inventario_model->mostrar($lista->id,$id); ?>
        <tr>
          <td><?php echo $lista->descripcion; ?></td>
          <td><?php echo $precios->stock; ?></td>
          <td><?php echo $empresa->pestablecimiento==1 ? $precios->pventa: $datos->pventa; ?></td>
          <td><?php echo $empresa->pestablecimiento==1 ? $precios->pblister: $datos->pblister; ?></td>
          <td><?php echo $empresa->pestablecimiento==1 ? $precios->venta: $datos->venta; ?></td>
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

<h5><strong>PRODUCTO : <?php echo $datos->descripcion ?></strong></h5>
<div class="table-responsive" style="height: 500px;">
  <table class="table table-hover table-sm">
    <thead class="thead-dark">
      <tr>
        <th>Lote</th>
        <th>Fecha Vcto</th>
        <th>Cantidad</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($listas as $lista): ?>
        <tr>
          <td><?php echo $lista->nlote; ?></td>
          <td><?php echo $lista->fvencimiento; ?></td>
          <td><?php echo $lista->stock; ?></td>
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

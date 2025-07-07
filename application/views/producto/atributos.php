<table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
  <thead>
    <tr>
      <th>#</th>
      <th>Descripcion</th>
      <th>Cantidad Productos Stock > 0</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php $i=1; foreach ($listas as $lista) { ?>
      <?php
      $productos=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0,$buscador=>$lista->id));
      $contador=count($productos);
      ?>
      <?php if ($contador>0): ?>
      <?php $archivo= $buscador=='idlaboratorio' ? 'resetearLaboratorio': 'resetearCategoria'; ?>
      <tr>
        <td><?php echo $i; ?></td>
        <td><?php echo $lista->descripcion; ?></td>
        <td align="center"><?php echo $contador; ?></td>
        <td>
          <div class="btn-group">
            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>producto/<?php echo $archivo; ?>/<?php echo $lista->id; ?>','<?php echo 'Desea resetear el stock de los productos del laboratorio a 0'; ?>')" class="btn bg-pink btn-sm py-0" title="Resetear stock 0" data-toggle="tooltip" data-placement="bottom"><i class="fas fa-sync-alt"></i></a>
          </div>
        </td>
      </tr>
      <?php endif ?>
    <?php $i++; } ?>
  </tbody>
</table>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

  <script type="text/javascript">
    $('[data-toggle="tooltip"]').tooltip();

    $('#sampleTable').DataTable({
      // "pageLength": 15,
      // "lengthMenu": [ 15, 25, 50, 75, 100 ],
      "oLanguage": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sInfoPostFix":    "",
        "sSearch":         "Buscar:",
        "sUrl":            "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
          "sFirst":    "Primero",
          "sLast":     "Último",
          "sNext":     "Siguiente",
          "sPrevious": "Anterior"
        },
        "oAria": {
          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
        }
      }
    });
  </script>

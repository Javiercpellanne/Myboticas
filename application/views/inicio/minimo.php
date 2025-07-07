<div class="table-responsive" style="height: 550px; font-size: .78rem;">
    <table class="table table-hover table-sm">
        <thead class="thead-dark">
            <tr>
                <th align="center" width="5%"><strong>#</strong></th>
                <th align="center" width="75%"><strong>Descripcion</strong></th>
                <th align="center" width="10%"><strong>Minimo</strong></th>
                <th align="center" width="10%"><strong>Cantidad</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; ?>
            <?php foreach ($listas as $lista): ?>
                <?php
                $producto=$this->producto_model->mostrar(array("p.id"=>$lista->id));
                $nproducto=$lista->descripcion;
                if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
                ?>
                <tr>
                    <td width="5%"><?php echo $i; ?></td>
                    <td width="75%"><?php echo $nproducto; ?></td>
                    <td width="10%" align="center"><?php echo $producto->mstock; ?></td>
                    <td width="10%" align="center"><?php echo $lista->stock; ?></td>
                </tr>
                <?php $i++; ?>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

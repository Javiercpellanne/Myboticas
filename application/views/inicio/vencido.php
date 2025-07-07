<div class="table-responsive" style="height: 550px; font-size: .78rem;">
    <table class="table table-hover table-sm">
        <thead class="thead-dark">
            <tr>
                <th align="center" width="5%"><strong>#</strong></th>
                <th align="center" width="65%"><strong>Descripcion</strong></th>
                <th align="center" width="10%"><strong>Lote</strong></th>
                <th align="center" width="10%"><strong>F. Vcto</strong></th>
                <th align="center" width="10%"><strong>Cantidad</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; ?>
            <?php foreach ($listas as $lista): ?>
                <?php
                $nproducto=$lista->descripcion;
                if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}

                if ($lista->fvencimiento<=SumarFecha('+1 month')) {$estilo='table-danger';} elseif ($lista->fvencimiento<=SumarFecha('+2 month')) {$estilo='table-warning';}else{$estilo='table-success';}
                ?>
                <tr class="<?php echo $estilo; ?>">
                    <td width="5%"><?php echo $i; ?></td>
                    <td width="65%"><?php echo $nproducto; ?></td>
                    <td width="10%"><?php echo $lista->lote; ?></td>
                    <td width="10%"><?php echo $lista->fvencimiento; ?></td>
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

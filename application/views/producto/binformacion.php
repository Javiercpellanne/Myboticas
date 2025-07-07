<ul class="nav nav-pills mb-2">
  <li class="nav-item"><a class="nav-link active" href="#tab_3" data-toggle="tab"><i class="fa fa-book"></i> Informacion Producto</a></li>
  <?php if ($opcion==null): ?>
  <li class="nav-item"><a class="nav-link" href="#tab_1" data-toggle="tab"><i class="fa fa-flask"></i> Principio Activo</a></li>
  <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab"><i class="fa fa-tint"></i> Accion Terapeutica</a></li>
  <?php endif ?>
  <?php if ($canexos>1): ?>
    <li class="nav-item"><a class="nav-link" href="#tab_4" data-toggle="tab"><i class="fa fa-cubes"></i> Stock Establecimientos</a></li>
  <?php endif ?>
</ul>
<div class="tab-content">
  <div class="tab-pane active" id="tab_3">
    <table class="table table-hover table-striped table-bordered table-sm">
      <tr>
        <td width="23%"><b>CATEGORIA</b></td>
        <td width="77%"><?php echo $categoria->descripcion; ?></td>
      </tr>
      <tr>
        <td><b>PRODUCTO</b></td>
        <td><?php echo $datos->descripcion; ?></td>
      </tr>
      <tr>
        <td><b>LABORATORIO</b></td>
        <td><?php echo $datos->nlaboratorio; ?></td>
      </tr>
      <tr>
        <td><b>PRINCIPIO ACTIVO</b></td>
        <td><?php echo $pactivo->descripcion??''; ?></td>
      </tr>
      <tr>
        <td><b>ACCION TERAPEUTICA</b></td>
        <td><?php echo $aterapeutica->descripcion??''; ?></td>
      </tr>
      <tr>
        <td><b>REGISTRO SANITARIO</b></td>
        <td><?php echo $datos->rsanitario; ?></td>
      </tr>
      <tr>
        <td><b>VENTA CON RECETA</b></td>
        <td><?php echo $datos->vsujeta==1 ? 'Si': 'No'; ?></td>
      </tr>
      <tr>
        <td><b>AFECTACION IGV</b></td>
        <td><?php echo $tafectacion->descripcion; ?></td>
      </tr>
      <tr>
        <td><b>UBICACION</b></td>
        <td><?php echo $ubicacion->descripcion??''; ?></td>
      </tr>
      <tr>
        <td><b>INFORMACION ADICIONAL</b></td>
        <td><?php echo nl2br($datos->informacion); ?></td>
      </tr>
    </table>
  </div>

  <div class="tab-pane" id="tab_1">
    <div class="table-responsive p-0" style="height: 500px; font-size: .79rem">
      <table class="table table-striped table-hover table-sm">
        <thead class="thead-light">
          <tr>
            <th>PRODUCTO</th>
            <th>STOCK</th>
            <th>P. UNID</th>
            <th>P. BLIS</th>
            <th>P. CAJA</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($datos->idpactivo>0): ?>
            <?php foreach ($principios as $producto): ?>
              <?php
              $nproducto=$producto->descripcion;
              if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
              $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
              $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
              $pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
              $venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;

              $bonificados=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id));
              if ($cantidad->stock<1 && $producto->tipo=='B'){
                $color='red';
              }else{
                if ($bonificados!=NULL) {$color='blueviolet';} else {$color='black';}
              }
              if ($producto->vsujeta==1) {$tcolor='table-success';} else {$tcolor='';}
              ?>
              <tr style="color: <?php echo $color; ?>;" class="<?php echo $tcolor; ?>">
                <td><?php echo $nproducto; ?></td>
                <td><?php echo $empresa->lstock==1 && $cantidad->stock>99 ? '+99': $cantidad->stock; ?></td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($cantidad->stock > 0 || $producto->tipo == 'S'): ?>
                      <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto; ?>`, '<?php echo $producto->umedidav; ?>', '1', '<?php echo $producto->tafectacion; ?>', '<?php echo $pventa; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-info btn-sm py-0" title="Click para seleccionar">
                         <?php echo $pventa; ?>
                      </a>
                  <?php else: ?>
                      <?php echo $pventa; ?>
                  <?php endif; ?>
                </td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($producto->umedidab!='' && $producto->factorb > 1 && $pblister > 0): ?>
                    <?php if (intval($cantidad->stock) >= $producto->factorb): ?>
                      <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto . ' BLISTER X ' . $producto->factorb; ?>`, '<?php echo $producto->umedidab; ?>', '<?php echo $producto->factorb; ?>', '<?php echo $producto->tafectacion; ?>', '<?php echo $pblister; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-primary btn-sm py-0" title="Click para seleccionar" style="position: relative;">
                        <?php echo $pblister; ?>
                        <span class="badge-precio"><?php echo $producto->factorb; ?></span>
                      </a>
                    <?php else: ?>
                      <?php echo $pblister; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($producto->umedidac!='' && $producto->factor > 1 && $venta > 0): ?>
                    <?php if (intval($cantidad->stock) >= $producto->factor): ?>
                      <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto . ' CJ X ' . $producto->factor; ?>`, '<?php echo $producto->umedidac; ?>', '<?php echo $producto->factor; ?>', '<?php echo $producto->tafectacion; ?>', '<?php echo $venta; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-success btn-sm py-0" title="Click para seleccionar" style="position: relative;">
                       <?php echo $venta; ?>
                       <span class="badge-precio"><?php echo $producto->factor; ?></span>
                      </a>
                    <?php else: ?>
                      <?php echo $venta; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach ?>
          <?php else: ?>
            <tr>
              <td colspan="5"><b>No hay datos de la busqueda</b></td>
            </tr>
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-pane" id="tab_2">
    <div class="table-responsive p-0" style="height: 500px; font-size: .79rem">
      <table class="table table-striped table-hover table-sm">
        <thead class="thead-light">
          <tr>
            <th>PRODUCTO</th>
            <th>STOCK</th>
            <th>P. UNID</th>
            <th>P. BLIS</th>
            <th>P. CAJA</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($datos->idaterapeutica>0): ?>
            <?php foreach ($terapeuticos as $producto): ?>
              <?php
              $nproducto=$producto->descripcion;
              if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
              $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
              $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
              $pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
              $venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;

              $bonificados=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id));
              if ($cantidad->stock<1 && $producto->tipo=='B'){
                $color='red';
              }else{
                if ($bonificados!=NULL) {$color='blueviolet';} else {$color='black';}
              }
              if ($producto->vsujeta==1) {$tcolor='table-success';} else {$tcolor='';}
              ?>
              <tr style="color: <?php echo $color; ?>;" class="<?php echo $tcolor; ?>">
                <td><?php echo $nproducto; ?></td>
                <td><?php echo $empresa->lstock==1 && $cantidad->stock>99 ? '+99': $cantidad->stock; ?></td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($cantidad->stock > 0 || $producto->tipo == 'S'): ?>
                      <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto; ?>`, '<?php echo $producto->umedidav; ?>', '1', '<?php echo $producto->tafectacion; ?>', '<?php echo $pventa; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-info btn-sm py-0" title="Click para seleccionar">
                         <?php echo $pventa; ?>
                      </a>
                  <?php else: ?>
                      <?php echo $pventa; ?>
                  <?php endif; ?>
                </td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($producto->factorb > 1 && $pblister > 0 && intval($cantidad->stock) >= $producto->factorb): ?>
                    <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto . ' BLISTER X ' . $producto->factorb; ?>`, '<?php echo $producto->umedidab; ?>', '<?php echo $producto->factorb; ?>', '<?php echo $producto->tafectacion; ?>', '<?php echo $pblister; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-primary btn-sm py-0" title="Click para seleccionar" style="position: relative;">
                      <?php echo $pblister; ?>
                      <span class="badge-precio"><?php echo $producto->factorb; ?></span>
                    </a>
                  <?php else: ?>
                    <?php if ($producto->factorb > 1 && $pblister > 0): ?>
                        <?php echo $pblister; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td align="center" style="font-weight: 700;">
                  <?php if ($producto->factor > 1 && $venta > 0 && intval($cantidad->stock) >= $producto->factor): ?>
                    <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto . ' CJ X ' . $producto->factor; ?>`, '<?php echo $producto->umedidac; ?>', '<?php echo $producto->factor; ?>', '<?php echo $producto->tafectacion; ?>', '<?php echo $venta; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-success btn-sm py-0" title="Click para seleccionar" style="position: relative;">
                     <?php echo $venta; ?>
                     <span class="badge-precio"><?php echo $producto->factor; ?></span>
                    </a>
                  <?php else: ?>
                    <?php if ($producto->factor > 1 && $venta > 0): ?>
                      <?php echo $venta; ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach ?>
          <?php else: ?>
            <tr>
              <td colspan="5"><b>No hay datos de la busqueda</b></td>
            </tr>
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="tab-pane" id="tab_4">
    <table class="table table-bordered table-striped table-sm">
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
        <?php foreach ($establecimientos as $establecimiento): ?>
          <?php $precios=$this->inventario_model->mostrar($establecimiento->id,$id); ?>
          <tr>
            <td><?php echo $establecimiento->descripcion; ?></td>
            <td><?php echo $precios->stock; ?></td>
            <td><?php echo $empresa->pestablecimiento==1 ? $precios->pventa: $datos->pventa; ?></td>
            <td><?php echo $empresa->pestablecimiento==1 ? $precios->pblister: $datos->pblister; ?></td>
            <td><?php echo $empresa->pestablecimiento==1 ? $precios->venta: $datos->venta; ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

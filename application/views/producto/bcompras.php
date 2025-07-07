<h5><strong>PRODUCTO : <?php echo $datos->descripcion ?></strong></h5>
<ul class="nav nav-pills ml-auto">
  <li class="nav-item"><a class="nav-link border border-info py-1 active" href="#tab_1" data-toggle="tab">Compras</a></li>
  <li class="nav-item"><a class="nav-link border border-info py-1 ml-1" href="#tab_2" data-toggle="tab">Ventas</a></li>
  <li class="nav-item"><a class="nav-link border border-info py-1 ml-1" href="#tab_3" data-toggle="tab">Precios Ventas</a></li>
</ul>
<div class="tab-content">
  <div class="tab-pane pt-2 active" id="tab_1">
		<div class="table-responsive" style="height: 460px;">
			<table class="table table-hover table-striped table-bordered table-sm">
				<thead class="thead-dark">
					<tr>
						<th width="10%">Fecha</th>
			      <th width="44%">Proveedor</th>
						<th width="10%">Documento</th>
						<th width="8%">Cantidad</th>
			      <th width="8%">Precio</th>
			      <th width="10%">Lote</th>
			      <th width="10%">F. Vcto</th>
					</tr>
				</thead>
				<tbody style="font-size: .78rem">
					<?php foreach ($compras as $lista): ?>
						<?php
						$cantidadu= $lista->factor;
						if ($lista->incluye==0 && $lista->tafectacion==10) {
							$preciov=round(($lista->precio+($lista->precio*0.18))/$cantidadu,2);
						}else{
							$preciov=round($lista->precio/$cantidadu,2);
						}
						?>
						<tr>
							<td><?php echo $lista->femision; ?></td>
			        <td><?php echo $lista->proveedor; ?></td>
			        <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
			        <td align="right"><?php echo $lista->cantidad.' '.$lista->unidad; ?></td>
			        <td><?php echo $preciov; ?></td>
			        <td><?php echo $lista->lote; ?></td>
			        <td><?php echo $lista->fvencimiento; ?></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="tab-pane pt-2" id="tab_2">
			<div class="table-responsive" style="height: 460px;">
				<table class="table table-hover table-striped table-bordered table-sm">
					<thead class="thead-dark">
						<tr>
							<th width="10%">Fecha</th>
				      <th width="44%">Cliente</th>
							<th width="10%">Documento</th>
							<th width="8%">Cantidad</th>
				      <th width="8%">Precio</th>
				      <th width="10%">Lote</th>
				      <th width="10%">F. Vcto</th>
						</tr>
					</thead>
					<tbody style="font-size: .78rem">
						<?php foreach ($ventas as $lista): ?>
							<?php
							$nombre= $this->usuario_model->mostrar($lista->iduser);
	            $nusuario=$nombre->nombres??'';
	            ?>
							<tr>
								<td><?php echo $lista->femision; ?></td>
				        <td><?php echo $lista->cliente.'<br> Usuario: '.$nusuario; ?></td>
				        <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
				        <td align="right"><?php echo $lista->cantidad.' '.$lista->unidad; ?></td>
				        <td><?php echo round($lista->importe/$lista->calmacen,2); ?></td>
				        <td><?php echo $lista->lote; ?></td>
				        <td><?php echo $lista->fvencimiento; ?></td>
							</tr>
						<?php endforeach ?>
						<?php foreach ($nventas as $lista): ?>
							<?php
							$nombre= $this->usuario_model->mostrar($lista->iduser);
	            $nusuario=$nombre->nombres??'';
	            ?>
							<tr>
								<td><?php echo $lista->femision; ?></td>
				        <td><?php echo $lista->cliente.'<br> Usuario: '.$nusuario; ?></td>
				        <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
				        <td align="right"><?php echo $lista->cantidad.' '.$lista->unidad; ?></td>
				        <td><?php echo round($lista->importe/$lista->calmacen,2); ?></td>
				        <td><?php echo $lista->lote; ?></td>
				        <td><?php echo $lista->fvencimiento; ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>
	</div>

	<div class="tab-pane pt-2" id="tab_3">
		<div class="table-responsive" style="height: 460px;">
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
		</div>
	</div>
</div>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

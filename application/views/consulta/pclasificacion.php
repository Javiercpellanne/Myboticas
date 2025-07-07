<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Consulta Productos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Consulta</li>
          <li class="breadcrumb-item active">Productos</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-8">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Marca vs Generico (RM 220-2024/MINSA)</h3>
          </div>

          <div class="card-body p-0">
              <table class="table table-hover table-bordered table-sm table-info">
                <thead>
                  <tr>
                    <th>Principio Activo</th>
                    <th>Medicamento</th>
                    <th>Stock Marca</th>
                    <th>Stock Generico</th>
                    <th>Stock Minimo (<?php echo $empresa->pesencial ?>%)</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($listas as $lista): ?>
                    <?php $egenericos=$this->egenerico_model->mostrarTotal($lista->id); ?>
                    <?php foreach ($egenericos as $egenerico): ?>
                      <?php
                      $totalm=0; //totales en marca
                      $marcas=$this->producto_model->mostrarTotal(array("estado"=>1,'clasificacion'=>2,'idpactivo'=>$lista->id,'idegenerico'=>$egenerico->id,"factor>"=>0));
                      foreach ($marcas as $marca) {
                        $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$marca->id);
                        $totalm+=$cantidad->stock;
                      }

                      $totalg=0; //totales en generico
                      $genericos=$this->producto_model->mostrarTotal(array("estado"=>1,'clasificacion'=>1,'idpactivo'=>$lista->id,'idegenerico'=>$egenerico->id,"factor>"=>0));
                      foreach ($genericos as $generico) {
                        $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$generico->id);
                        $totalg+=$cantidad->stock;
                      }
                      ?>
                      <?php if ($marcas!=NULL || $genericos!=NULL): ?>
                        <?php $minimo=round($totalm*($empresa->pesencial/100)) ?>
                      <tr>
                        <td><?php echo $lista->descripcion; ?></td>
                        <td><?php echo $egenerico->descripcion; ?></td>
                        <td align="center"><?php echo $totalm; ?></td>
                        <td align="center" <?php echo $minimo>$totalg ? 'class="text-danger"': ''; ?>><b><?php echo $totalg; ?></b></td>
                        <td align="center"><?php echo $minimo; ?></td>
                        <td></td>
                      </tr>
                      <?php endif ?>
                    <?php endforeach ?>
                  <?php endforeach ?>
                </tbody>
              </table>
          </div>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="row">
          <div class="col-sm-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Por Marca y Generico (Cantidad Productos)</h3>
              </div>
              <div class="card-body p-2">
                <div class="chart">
                  <canvas id="doughnutProductos"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-sm-12">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title">Por Clasificacion (Cantidad Productos)</h3>
              </div>
              <div class="card-body p-2">
                <div class="chart">
                  <canvas id="doughnutClasificacion"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

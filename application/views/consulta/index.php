<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Ventas Valorizadas</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Consulta</li>
          <li class="breadcrumb-item active">Ventas Valorizadas</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <!-- <div class="card-header py-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>consulta">Ventas Valorizadas</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>consulta/ventau">Usuarios</a></li>
            </ul>
          </div> -->
          <div class="card-body p-3">
            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-2">
                  <label for="inicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="inicio" type="date" id="inicio" class="form-control form-control-sm" value="<?php echo $inicio; ?>" required/>
                </div>

                <label for="fin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="fin" type="date" id="fin" class="form-control form-control-sm" value="<?php echo $fin; ?>" required/>
                </div>

                <label for="usuario" class="col-sm-1 control-label">Usuario</label>
                <div class="col-sm-2">
                  <select name="usuario" id="usuario" class="form-control form-control-sm">
                    <option value="">::Todos Usuarios</option>
                    <?php foreach ($usuarios as $usuario) {?>
                    <option value="<?php echo $usuario->id ?>" <?php echo set_value_select($user,'usuario',$usuario->id,$user) ?>><?php echo $usuario->nombres ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-1 text-center">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>

                <div class="col-sm-2 text-right">
                  <a href="<?php echo base_url(); ?>consulta/pdfventav/<?php echo $inicio; ?>/<?php echo $fin; ?>/<?php echo $user; ?>" class="btn btn-secondary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>

                  <a href="<?php echo base_url(); ?>consulta/excelventav/<?php echo $inicio; ?>/<?php echo $fin; ?>/<?php echo $user; ?>" class="btn btn-success btn-sm ml-2" title="Kardex" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-file-excel"></i> EXCEL</a>
                </div>
              </div>
            <?php echo form_close(); ?>

            <div class="table-responsive p-0" style="height: 500px;">
              <table class="table table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th width="5%">#</th>
                    <th width="44%">Producto</th>
                    <th width="10%">Unidades vendidas</th>
                    <th width="5%">(Dscto)</th>
                    <th width="10%">Ventas</th>
                    <th width="10%">Costo Prom.</th>
                    <th width="8%">Utilidad</th>
                    <th width="8%">Margen (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista) { ?>
                    <?php
                    $notas=$this->nota_model->ganancia(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin,"nulo"=>0,"idproducto"=>$lista->idproducto));
                    $cantidad=$lista->cantidad-$notas->cantidad;
                    $venta=$lista->importe-$notas->importe;

                    $compra=$lista->costo-$notas->costo;//
                    $utilidad=$venta-$compra;
                    $margen=gananciav($venta,$compra,1);
                    ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $lista->descripcion; ?></td>
                      <td align="center"><?php echo $cantidad; ?></td>
                      <td align="right"><?php echo round($lista->dscto,2); ?></td>
                      <td align="right"><?php echo formatoPrecio($venta); ?></td>
                      <td align="right"><?php echo formatoPrecio($compra); ?></td>
                      <td align="right"><?php echo formatoPrecio($utilidad); ?></td>
                      <td align="center"><?php echo $margen; ?></td>
                    </tr>
                    <?php $i++; ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title" id="modalTitle">Datos de Unidades Vendidas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>


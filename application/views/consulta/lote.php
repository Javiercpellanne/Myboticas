<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Kardex</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Consulta</li>
          <li class="breadcrumb-item active">Kardex</li>
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
          <div class="card-header py-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>consulta/kardex">General</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>consulta/producto">Producto</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>consulta/lote">Por Lotes</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="descripcion" class="col-sm-1 col-form-label">Producto</label>
                <div class="col-sm-4">
                  <input name="idproducto" id="idproducto" type="hidden" value="<?php echo $idproducto ?>" required="">
                  <div class="input-group">
                    <input name="descripcion" type="text" id="descripcion" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" onkeyup="productoNombre('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off" value="<?php echo $descripcion ?>" autofocus required>
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                  </div>

                  <div id="tbldescripcion" style="position:absolute; z-index: 1051; width: 98%; overflow: overlay; max-height:300px; display: none;">
                    <dl class="bg-buscador" id="grdescripcion">
                    </dl>
                  </div>
                </div>

                <label for="finicio" class="col-sm-1 col-form-label">Desde</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control form-control-sm" name="finicio" id="finicio" value="<?php echo $finicio ?>">
                </div>

                <label for="ffinal" class="col-sm-1 col-form-label">Hasta</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control form-control-sm" name="ffinal" id="ffinal" value="<?php echo $ffinal ?>">
                </div>

                <div class="col-sm-1 text-right">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <div class="table-responsive border border-dark" style="height: 475px;">
              <table class="table table-hover table-striped table-bordered table-sm mb-0">
                <thead class="thead-dark">
                  <tr>
                    <th class="border border-white mb-1 text-center" width="3%">#</th>
                    <th class="border border-white mb-1 text-center" width="11%">Fecha</th>
                    <th class="border border-white mb-1 text-center" width="17%">Usuario</th>
                    <th class="border border-white mb-1 text-center" width="10%">Documento</th>
                    <th class="border border-white mb-1 text-center" width="25%">Detalle</th>
                    <th class="border border-white mb-1 text-center" width="10%">Lote</th>
                    <th class="border border-white mb-1 text-center" width="8%">Entrada</th>
                    <th class="border border-white mb-1 text-center" width="8%">Salida</th>
                    <th class="border border-white mb-1 text-center" width="8%">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; $nlote = array(); ?>
                  <?php foreach ($listas as $lista) { ?>
                    <?php
                    $nombre= $this->usuario_model->mostrar($lista->iduser);
                    if (!in_array($lista->nlote, $nlote)) {
                        array_push($nlote, $lista->nlote);
                        $borde = 'style="border-top: solid black;"';
                    } else {
                        $borde = '';
                    }
                    ?>
                    <tr <?php echo $borde; ?>>
                      <td><?php echo $i; ?></td>
                      <td><?php echo fechaHoraria('-5 hour',$lista->fregistro);//$lista->fecha ?></td>
                      <td><?php echo $nombre->nombres??''; ?></td>
                      <td><?php echo $lista->documento; ?></td>
                      <td><?php echo $lista->concepto; ?></td>
                      <td><?php echo $lista->nlote; ?></td>
                      <td align="right"><?php echo $lista->entradaf; ?></td>
                      <td align="right"><?php echo $lista->salidaf; ?></td>
                      <td align="right"><?php echo $lista->saldof; ?></td>
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
        <h5 class="modal-title" id="modalTitle">Datos de la consulta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body p-3">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>


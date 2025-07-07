<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Bonos Vendedor</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Consulta</li>
          <li class="breadcrumb-item active">Bonos Vendedor</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary card-outline">
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="canuo" class="col-sm-1 col-form-label">AÑO</label>
                <div class="col-sm-2">
                  <select name="canuo" id="canuo" class="form-control form-control-sm">
                    <option value="" <?php echo set_value_select($canuo,'canuo',$canuo,'') ?>>::Seleccione</option>
                    <?php foreach ($anuos as $anuo) {?>
                      <option value="<?php echo $anuo->descripcion ?>" <?php echo set_value_select($canuo,'canuo',$canuo,$anuo->descripcion) ?>><?php echo $anuo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="cmes" class="col-sm-1 col-form-label">MES</label>
                <div class="col-sm-2">
                  <select name="cmes" id="cmes" class="form-control form-control-sm">
                    <option value="" <?php echo set_value_select($cmes,'cmes',$cmes,'') ?>>::Seleccione</option>
                    <?php foreach ($meses as $mes) {?>
                      <option value="<?php echo $mes->id ?>" <?php echo set_value_select($cmes,'cmes',$cmes,$mes->id) ?>><?php echo $mes->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="nusuario" class="col-sm-1 col-form-label">USUARIO</label>
                <div class="col-sm-3">
                  <select name="nusuario" id="nusuario" class="form-control form-control-sm">
                    <option value="" <?php echo set_value_select($nusuario,'nusuario',$nusuario,'') ?>>::Seleccione</option>
                    <?php foreach ($usuarios as $usuario) {?>
                    <option value="<?php echo $usuario->id ?>" <?php echo set_value_select($nusuario,'nusuario',$nusuario,$usuario->id) ?>><?php echo $usuario->nombres ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 text-right">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <div class="table-responsive p-0" style="height: 460px;">
              <table class="table table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th width="10%">COD</th>
                    <th width="60%">PRODUCTO</th>
                    <th width="10%">BONIF</th>
                    <th width="10%">VENDIDOS</th>
                    <th width="10%">IMPORTE</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total=0; ?>
                  <?php foreach ($listas as $lista): ?>
                    <?php
                    $producto=$this->producto_model->mostrar(array("p.id"=>$lista->idproducto));
                    $nproducto=$producto->descripcion;
                    if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}

                    $bonificados=$this->bonificado_model->mostrar(array("anuo"=>$canuo,"mes"=>$cmes,"idproducto"=>$lista->idproducto));
                    $monto=$bonificados->monto??'';
                    $importe=$lista->cantidad*floatval($monto);
                    ?>
                    <?php if ($monto!=''): ?>
                      <tr>
                        <td><?php echo $lista->idproducto; ?></td>
                        <td><?php echo $nproducto; ?></td>
                        <td align="right"><?php echo $monto; ?></td>
                        <td><?php echo $lista->cantidad; ?></td>
                        <td align="right"><?php echo formatoPrecio($importe); ?></td>
                      </tr>
                      <?php $total+=$importe; ?>
                    <?php endif ?>
                  <?php endforeach ?>
                </tbody>
              </table>
            </div>

            <table class="table table-bordered table-sm">
              <tr>
                <td width="90%" align="right"><b>TOTALES</b></td>
                <td width="10%" align="right"><?php echo formatoPrecio($total); ?></td>
              </tr>
            </table>
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
        <h5 class="modal-title" id="modalTitle">Datos de la Serie</h5>
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



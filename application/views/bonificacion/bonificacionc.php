<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Bonificados</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> </li>
          <li class="breadcrumb-item">Producto</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>bonificacion">Bonificados</a></li>
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
              <div class="row">
                <div class="col-sm-5">
                  <div class="form-group row mb-1">
                    <label for="canuo" class="col-sm-2 col-form-label">AÑO</label>
                    <div class="col-sm-3">
                      <select name="canuo" id="canuo" class="form-control form-control-sm" required>
                        <?php foreach ($anuos as $anuo) {?>
                          <option value="<?php echo $anuo->descripcion; ?>" <?php echo set_value_select(date("Y"),'canuo',$anuo->descripcion,date("Y")) ?>><?php echo $anuo->descripcion; ?></option>
                        <?php  }  ?>
                      </select>
                    </div>

                    <label for="cmes" class="col-sm-2 col-form-label">MES</label>
                    <div class="col-sm-3">
                      <select name="cmes" id="cmes" class="form-control form-control-sm" onchange="mostrarb('<?php echo base_url() ?>bonificacion/busBonificacion');" required>
                        <option value="">::Seleccione</option>
                        <?php foreach ($meses as $mes) {?>
                          <option value="<?php echo $mes->id ?>"><?php echo $mes->descripcion; ?></option>
                        <?php  }  ?>
                      </select>
                    </div>
                  </div>

                  <div class="table-responsive" style="height: 460px;">
                    <table class="table table-hover table-sm">
                      <thead class="thead-dark">
                        <tr>
                          <th width="81%">DESCRIPCION</th>
                          <th width="15%">MONTO</th>
                          <th width="4%"></th>
                        </tr>
                      </thead>
                      <tbody id="grilla">
                        <?php $i=1; ?>
                        <?php foreach ($listas as $lista): ?>
                          <tr id="item<?php echo $i; ?>">
                            <td>
                              <input type="hidden" name="idproducto[]" value="<?php echo $lista->idproducto ?>">
                              <input type="text" name="descripcion[]" value="<?php echo $lista->descripcion ?>" class="campo" readonly="">
                            </td>
                            <td>
                              <input name="monto[]" type="number" min="0.01" step="0.01" value="<?php echo $lista->monto ?>" class="form-control form-control-sm">
                            </td>
                            <td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom" onclick="borrarb('<?php echo 'item'.$i; ?>')"><i class="fa fa-trash"></i></a></td>
                          </tr>
                          <?php $i++; ?>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-sm float-sm-right"><i class="fa fa-save"></i> GUARDAR</button>
                  </div>
                </div>

                <div class="col-sm-7 border-left border-primary">
                  <div class="row mb-2">
                    <div class="col-sm-4">
                    </div>

                    <div class="col-sm-3">
                      <!-- <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                        </div>
                        <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Precio Unidad" aria-label="Codigo Barra" aria-describedby="basic-addon1" onkeydown="productoBarran(event,'<?php echo base_url(); ?>bonificacion/busCodigobarra',this.value);">
                      </div> -->
                    </div>

                    <div class="col-sm-5">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </div>
                        <input name="bproducto" type="text" id="bproducto" class="form-control form-control-sm" value="" placeholder="Buscar Producto" onkeyup="productoNombreb('<?php echo base_url(); ?>producto/busProductos',this.value)" autofocus>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive" style="height: 500px; font-size: .78rem">
                    <table class="table table-hover table-sm">
                      <thead class="thead-light">
                        <tr>
                          <th>COD</th>
                          <th>PRODUCTO</th>
                          <th>BONIF</th>
                          <th>STOCK</th>
                          <th>P. UNID</th>
                        </tr>
                      </thead>
                      <tbody id="tblproducto">
                        <?php foreach ($productos as $producto): ?>
                          <?php
                          $nproducto=$producto->descripcion;
                          if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
                          $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
                          $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
                          $bonificados=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id)); ?>
                          <tr>
                            <td><?php echo $producto->id; ?></td>
                            <td><?php echo $nproducto; ?></td>
                            <td><?php echo $bonificados->monto??''; ?></td>
                            <td><?php echo $cantidad->stock; ?></td>
                            <td align="right"><a href="javascript:void(0)" onclick="appbonificacion('<?php echo $producto->id; ?>', '<?php echo $nproducto; ?>');" class="btn btn-info btn-sm py-0" title="Click para seleccionar"><?php echo $pventa; ?></a></td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

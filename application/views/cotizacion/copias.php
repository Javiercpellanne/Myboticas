<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Cotizacion de Venta</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>cotizacion">Cotizacion</a></li>
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
          <div class="card-body p-2">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(base_url().'cotizacion/guardar',array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
              <div class="row">
                <div class="col-sm-6">
                  <div class="row mb-2">
                    <div class="col-sm-2 col-6">
                    </div>

                    <div class="col-sm-3 col-6">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                        </div>
                        <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Precio Unidad" aria-label="Codigo Barra" aria-describedby="basic-addon1" onfocus="limpiarBuscadorz('<?php echo base_url(); ?>producto/busProductos')" onkeydown="productoBarraz(event,'<?php echo base_url(); ?>producto/busCodigobarra',this.value);">
                      </div>
                    </div>

                    <div class="col-sm-7">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </div>
                        <input name="bproducto" type="text" id="bproducto" class="form-control form-control-sm" value="" placeholder="Buscar Producto" onkeyup="productoNombrez('<?php echo base_url(); ?>producto/busProductos',this.value)" autofocus>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive border border-dark" style="height: 352px; font-size: .79rem">
                    <table class="table table-striped table-hover table-sm">
                      <thead class="thead-light">
                        <tr>
                          <th class="priority" width="6%">COD</th>
                          <th width="52%">PRODUCTO</th>
                          <th width="7%">CAN</th>
                          <th width="11%">P. UNID</th>
                          <th width="12%">P. BLIS</th>
                          <th width="12%">P. CAJA</th>
                        </tr>
                      </thead>
                      <tbody id="tblproducto">
                        <?php foreach ($productos as $producto): ?>
                          <?php
                          $nproducto=$producto->descripcion;
                          if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
                          $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
                          $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
                          $pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
                          $venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;
                          ?>
                          <tr <?php if ($cantidad->stock<1): ?>style="color: #dc3545;"<?php endif ?>>
                            <td class="priority"><?php echo $producto->id; ?></td>
                            <td><a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busInformacion/<?php echo $producto->id; ?>','bdatos','Informacion Producto')"><?php echo $nproducto; ?></a></td>
                            <td><?php echo $empresa->lstock==1 && $cantidad->stock>99 ? '+99': $cantidad->stock; ?></td>
                            <td align="center"><a href="javascript:void(0)" onclick="appcotizacion('<?php echo $producto->id; ?>', `<?php echo $nproducto; ?>`,'<?php echo $producto->umedidav; ?>','<?php echo 1; ?>','<?php echo $producto->tafectacion; ?>','<?php echo $pventa; ?>','<?php echo $cantidad->stock; ?>','<?php echo $empresa->pventa; ?>');" class="btn btn-info btn-sm py-0" title="Click para seleccionar"><?php echo $pventa; ?></a></td>
                            <td align="center">
                              <?php if ($producto->umedidab!='' && $producto->factorb>1 && $pblister>0): ?>
                                <a href="javascript:void(0)" onclick="appcotizacion('<?php echo $producto->id; ?>', `<?php echo $nproducto.' BLISTER X '.$producto->factorb; ?>`,'<?php echo $producto->umedidab; ?>','<?php echo $producto->factorb; ?>','<?php echo $producto->tafectacion; ?>','<?php echo $pblister; ?>','<?php echo $cantidad->stock; ?>','<?php echo $empresa->pventa; ?>');" class="btn btn-primary btn-sm py-0" title="Click para seleccionar" style="position: relative;"><?php echo $pblister; ?><span class="badge-precio"><?php echo $producto->factorb; ?></span></a>
                              <?php endif ?>
                            </td>
                            <td align="center">
                              <?php if ($producto->umedidac!='' && $producto->factor>1 && $venta>0): ?>
                              <a href="javascript:void(0)" onclick="appcotizacion('<?php echo $producto->id; ?>', `<?php echo $nproducto.' CJ X '.$producto->factor; ?>`,'<?php echo $producto->umedidac; ?>','<?php echo $producto->factor; ?>','<?php echo $producto->tafectacion; ?>','<?php echo $venta; ?>','<?php echo $cantidad->stock; ?>','<?php echo $empresa->pventa; ?>');" class="btn btn-success btn-sm py-0" title="Click para seleccionar" style="position: relative;"><?php echo $venta; ?><span class="badge-precio"><?php echo $producto->factor; ?></span></a>
                              <?php endif ?>
                            </td>
                          </tr>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-sm-6">
                  <hr class="m-2" style="border-bottom: solid #17a2b8;">
                  <div class="table-responsive table-striped p-0 border border-info" style="height: 330px;">
                    <table class="table table-striped table-hover table-sm">
                      <thead class="thead-dark priority">
                        <tr>
                          <th width="95%">
                            <div class="row">
                              <div class="col-sm-3"><b>DESCRIPCION</b></div>
                              <div class="col-sm-3"><b>CANT</b></div>
                              <div class="col-sm-3"><b>PRECIO</b></div>
                              <div class="col-sm-3"><b>IMPORTE</b></div>
                            </div>
                          </th>
                          <th width="5%"></th>
                        </tr>
                      </thead>
                      <tbody id="grilla">
                        <?php $i=1; ?>
                        <?php foreach ($detalles as $detalle): ?>
                          <?php
                          $estilo=$empresa->pventa==0 ? 'campo' : 'form-control form-control-sm';
                          $bloquear=$empresa->pventa==0 ? 'onkeydown="return false"' : '';
                          ?>
                          <tr id="item<?php echo $i; ?>">
                            <td>
                              <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo" readonly />
                              <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto; ?>" class="productoc"/>
                            <div class="row"><div class="col-sm-3">
                              <input type="hidden" name="factor[]" id="factor[]" value="<?php echo $detalle->factor; ?>" class="factorc">
                              <input type="text" name="unidad[]" value="<?php echo $detalle->unidad; ?>" class="campo"/>
                            </div>
                            <div class="col-sm-3"><input type="number" name="cantidad[]" value="<?php echo $detalle->cantidad; ?>" min="1" class="form-control form-control-sm cantidadc"/></div>
                            <div class="col-sm-3"><input type="text" name="precio[]" value="<?php echo $detalle->precio; ?>" class="<?php echo $estilo; ?> text-right precioc" <?php echo $bloquear; ?>/></div>
                            <div class="col-sm-3"><input type="number" min="0.01" step="0.01"  name="importe[]" value="<?php echo $detalle->importe; ?>" class="campo text-right importec" onkeydown="return false"/></div></div></td>
                            <td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>
                          </tr>
                          <?php $i++; ?>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>

                  <table>
                    <tr>
                      <td width="24%" align="right"><strong></strong></td>
                      <td width="23%"></td>
                      <td width="24%" align="right"><strong>TOTAL</strong></td>
                      <td width="29%">
                        <h4><input name="totalg" type="text" id="totalg" class="campo text-right" value="<?php echo $cotizacion->total; ?>"/></h4>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group row mb-2 mt-2">
                    <label for="tvalidez" class="col-sm-3 col-6 control-label">Tiempo Validez</label>
                    <div class="col-sm-3 col-6">
                      <input name="tvalidez" type="text" id="tvalidez" class="form-control form-control-sm" value="<?php echo $cotizacion->tvalidez; ?>">
                    </div>

                    <label for="tentrega" class="col-sm-3 col-6 control-label">Tiempo Entrega</label>
                    <div class="col-sm-3 col-6">
                      <input name="tentrega" type="text" id="tentrega" class="form-control form-control-sm" value="<?php echo $cotizacion->tentrega; ?>">
                    </div>
                  </div>

                  <div class="form-group row mb-2">
                    <label for="dadicional" class="col-sm-3 control-label">Descrip. Adicional</label>
                    <div class="col-sm-9">
                      <input name="dadicional" type="text" id="dadicional" value="<?php echo $cotizacion->dadicional; ?>" class="form-control form-control-sm"/>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group row mb-1 mt-2">
                    <label for="cliente" class="col-sm-2 col-6 control-label">Cliente*</label>
                    <input name="idcliente" id="idcliente" type="hidden" value="<?php echo $cotizacion->idcliente; ?>"/>
                    <input name="tdocumento" id="tdocumento" type="hidden" value="0"/>
                    <div class="col-sm-7">
                      <div class="input-group">
                        <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="<?php echo $cotizacion->cliente; ?>" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>cliente/buscador/C','bdatos','Buscar Cliente')"><i class="fa fa-search" aria-hidden="true"></i></button>

                          <button type="button" class="btn btn-info btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>nventa/clientei','bdatos','Datos del Cliente')"><i class="fa fa-plus"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group col-sm-12 text-center mb-0">
                    <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR COTIZACION</button>
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

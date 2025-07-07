<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Venta > 700 con DNI</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>venta">Comprobante</a></li>
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
            <?php if($this->session->flashdata('mensaje')!=''){?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioVenta('".base_url()."venta/guardar/".$id."');")); ?>
              <div class="row">
                <div class="col-sm-6">
                  <div class="row mb-2">
                    <div class="col-sm-2 col-6">
                      <b>Dscto : (<?php echo $empresa->dscto==0 ? '%' : 'S/.'; ?>)</b>
                    </div>

                    <div class="col-sm-3 col-6">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                        </div>
                        <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Precio Unidad" aria-label="Codigo Barra" aria-describedby="basic-addon1" onfocus="limpiarBuscadorv('<?php echo base_url(); ?>producto/busProductos')" onkeydown="productoBarrav(event,'<?php echo base_url(); ?>producto/busCodigobarra',this.value);">
                      </div>
                    </div>

                    <div class="col-sm-7">
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                        </div>
                        <input name="bproducto" type="text" id="bproducto" class="form-control form-control-sm" value="" placeholder="Buscar Producto" onkeyup="productoNombrev('<?php echo base_url(); ?>producto/busProductos',this.value)" autofocus>
                        <div class="input-group-append">
                          <button type="button" class="btn bg-info btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>nventa/productoi','bdatos','Datos del Producto')" title="Producto Nuevo" data-toggle="tooltip" data-placement="bottom"><i class="fas fa-plus"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="table-responsive border border-dark" style="height: 340px; font-size: .79rem">
                    <table class="table table-striped table-hover table-sm">
                      <thead class="thead-light">
                        <tr>
                          <th width="52%">PRODUCTO</th>
                          <th class="priority" width="6%">BONO</th>
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

                          $bonificados=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id));
                          if ($cantidad->stock<1 && $producto->tipo=='B'){
                            $color='red';
                          }else{
                            if ($bonificados!=NULL) {$color='blueviolet';} else {$color='black';}
                          }
                          if ($producto->vsujeta==1) {$tcolor='table-success';} else {$tcolor='';}
                          ?>
                          <tr style="color: <?php echo $color; ?>;" class="<?php echo $tcolor; ?>">
                            <td><a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busInformacion/<?php echo $producto->id; ?>','bdatos','Informacion Producto')"><?php echo $nproducto; ?></a></td>
                            <td class="priority" align="right"><?php echo $bonificados->monto??''; ?></td>
                            <?php if ($producto->lote==1 && $cantidad->stock>0): ?>
                            <td><a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busProductoLotes/<?php echo $producto->id; ?>','bdatos','Mostrar Lotes')" class="badge badge-dark" style="font-size: 100%;" title="Seleccionar Lotes"><?php echo $empresa->lstock==1 && $cantidad->stock>99 ? '+99': $cantidad->stock; ?></a></td>
                            <?php else: ?>
                            <td><?php echo $empresa->lstock==1 && $cantidad->stock>99 ? '+99': $cantidad->stock; ?></td>
                            <?php endif ?>
                            <td align="center" style="font-weight: 700;">
                              <?php if ($cantidad->stock > 0 || $producto->tipo == 'S'): ?>
                                  <a href="javascript:void(0)" onclick="appventa('<?php echo $producto->id; ?>', `<?php echo $nproducto; ?>`, '<?php echo $producto->umedidav; ?>', '1', '<?php echo $producto->tafectacion; ?>', '<?php echo $pventa; ?>', '<?php echo $producto->lote; ?>', '<?php echo $cantidad->stock; ?>', '<?php echo $producto->tipo; ?>', '<?php echo $empresa->dscto; ?>', '<?php echo $empresa->pventa; ?>', '<?php echo $producto->vsujeta; ?>', '<?php echo $empresa->vbonificar; ?>');" class="btn btn-info btn-sm py-0 punidad" title="Click para seleccionar">
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
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="table-responsive table-striped p-0 border border-info" style="height: 330px;">
                    <table class="table table-striped table-hover table-sm">
                      <thead class="thead-dark priority">
                        <tr>
                          <th width="95%">
                            <div class="row">
                              <div class="col-sm-2"><b>DESCRIPCION</b></div>
                              <div class="col-sm-1"><b>L</b></div>
                              <div class="col-sm-1"><b>UND</b></div>
                              <div class="col-sm-2"><b>CANT</b></div>
                              <div class="col-sm-2"><b>PRECIO</b></div>
                              <div class="col-sm-2"><b>DSCTO</b></div>
                              <div class="col-sm-2"><b>IMPORTE</b></div>
                            </div>
                          </th>
                          <th width="5%"></th>
                        </tr>
                      </thead>
                      <tbody id="grilla">
                        <?php $igravado=0; $exonerado=0; $inafecto=0; $i=1; ?>
                        <?php foreach ($detalles as $detalle): ?>
                          <?php
                            $producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
                            $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
                            $factor= $detalle->unidad=='BX' ? $producto->factor : 1;
                            if ($producto->tafectacion==30) {
                              $inafecto+=$detalle->importe;
                            } else if($producto->tafectacion==20) {
                              $exonerado+=$detalle->importe;
                            } else {
                              $igravado+=$detalle->importe;
                            }

                            $almacenc=$detalle->cantidad*$factor;
                            if ($cantidad->stock<$almacenc) {$color='text-danger';} else {$color='';}

                            $mdscto=''; $fdscto='';
                            if ($detalle->descuentos!='') {
                              $descuentos=json_decode($detalle->descuentos);

                              $mdscto=$descuentos->monto;
                              $fdscto= $descuentos->factor*100;
                            }
                            $mdscto=$empresa->dscto==0 ? $fdscto : $mdscto;
                            $descuento=$empresa->dscto==0 ? '%' : 'S/.';
                          ?>
                          <tr>
                            <td>
                              <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo <?php echo $color; ?>"/>
                              <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto ?>"/>
                              <input type="hidden" name="tipo[]" value="<?php echo $producto->tipo ?>"/>
                              <input type="hidden" class="colegiaturan" name="colegiatura[]" value="">
                              <input type="hidden" class="doctorn" name="doctor[]" value="">
                              <input type="hidden" class="pacienten" name="paciente[]" value="">
                              <div class="row">
                                <div class="col-sm-2 col-4">
                                <?php if ($empresa->vbonificar==1) { ?>
                                cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
                                <?php } ?>
                                </div>
                                <div class="col-sm-1 col-4">
                                  <input type="text" name="nlote[]" id="mlote<?php echo $i; ?>" value="" class="campo"/>
                                  <input type="hidden" name="lote[]" value="<?php echo $producto->lote ?>" class="campo"/>
                                </div>
                                <div class="col-sm-1 col-4">
                                  <input type="hidden" class="factorn" name="factor[]" id="factor[]" value="<?php echo $factor; ?>">
                                  <input type="text" name="unidad[]" value="<?php echo $detalle->unidad ?>" class="campo"/>
                                  <input type="hidden" name="tafectacion[]" value="<?php echo $producto->tafectacion ?>"/>
                                </div>
                                <div class="col-sm-2 col-4">
                                  <input type="hidden" name="stock[]" value="<?php echo $cantidad->stock; ?>" class="stockn">
                                  <input type="number" min="1" name="cantidad[]" value="<?php echo $detalle->cantidad ?>" class="campo cantidadn" onkeypress="return event.keyCode != 13;"/>
                                  <input type="hidden" class="calmacenn" id="mcantidad<?php echo $i; ?>" name="almacenc[]" value="<?php echo $almacenc; ?>"/>
                                </div>
                                <div class="col-sm-2 col-4"><input type="text" name="precio[]" value="<?php echo $detalle->precio ?>" class="campo text-right precion"/></div>
                                <div class="col-sm-2 col-4">
                                  <input type="hidden" class="dscton" name="tdscto[]" id="tdscto[]" value="<?php echo $empresa->dscto; ?>">
                                  <input type="number" name="dscto[]" value="<?php echo $mdscto ?>" class="form-control form-control-sm border border-danger porcentajen" step="0.01" placeholder="(<?php echo $descuento; ?>)Dscto"/>
                                </div>
                                <div class="col-sm-2 col-4"><input type="number" min="0.01" step="0.01" name="importe[]" value="<?php echo $detalle->importe ?>" class="campo text-right importen no-spinners"/></div>
                              </div>
                            </td>
                            <td>
                              <a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar"><i class="fa fa-trash"></i></a>
                              <?php if ($producto->lote==1): ?>
                              <br><a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busLotes/<?php echo $detalle->idproducto ?>/<?php echo $i; ?>','bdatos','Mostrar Lotes');" class="btn btn-secondary btn-sm py-0 mt-2" title="Lotes"><i class="fa fa-cubes"></i></a>
                              <?php endif ?>
                            </td>
                          </tr>
                          <?php $i++; ?>
                        <?php endforeach ?>
                      </tbody>
                    </table>
                  </div>

                  <table>
                    <tr>
                      <?php $gravado=round($igravado/1.18,4); $igv=round($gravado*0.18,2); ?>
                      <td width="24%" align="right"><strong>IGV S./</strong></td>
                      <td width="23%">
                        <input name="gratuito" type="hidden" id="gratuito" value=""/>
                        <input name="bimponible" type="hidden" id="bimponible" value="<?php echo $gravado; ?>"/>
                        <input name="gravado" type="hidden" id="gravado" value="<?php echo $gravado; ?>"/>
                        <input name="inafecto" type="hidden" id="inafecto" value="<?php echo $inafecto; ?>"/>
                        <input name="exonerado" type="hidden" id="exonerado" value="<?php echo $exonerado; ?>"/>
                        <h4><input name="igv" type="text" id="igv" class="campo text-right" value="<?php echo formatoMonto($igv); ?>"/></h4>
                      </td>
                      <td width="24%" align="right"><strong>TOTAL S./</strong></td>
                      <td width="29%">
                        <h4><input name="totalg" type="text" id="totalg" class="campo text-right" value="<?php echo formatoMonto($gravado+$inafecto+$exonerado+$igv); ?>"/></h4>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group row mb-1 mt-2">
                    <div class="col-sm-4 col-6">
                      <select name="comprobante" id="comprobante" class="form-control form-control-sm" onchange="mostrarDato('<?php echo base_url(); ?>venta/busSerie',this.value,'serie')" required>
                        <?php foreach ($comprobantes as $comprobante): ?>
                          <option value="<?php echo $comprobante->id ?>"><?php echo $comprobante->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>

                    <div class="col-sm-2 col-6">
                      <input name="serie" type="text" id="serie" value="<?php echo $nserie->serie ?>" class="form-control form-control-sm" readonly required/>
                    </div>

                    <div class="col-sm-4 col-6">
                      <select name="toperacion" id="toperacion" class="form-control form-control-sm" onchange="mostrarDetraccion('<?php echo base_url(); ?>empresa/busDetraccion',this.value)">
                        <?php foreach ($toperaciones as $toperacion): ?>
                          <option value="<?php echo $toperacion->id ?>"><?php echo $toperacion->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>

                    <div class="col-sm-2 col-6">
                      <select name="vendedor" id="vendedor" class="form-control form-control-sm" required>
                        <option value="">::Vendedor</option>
                        <?php foreach ($vendedores as $vendedor): ?>
                          <?php if ($vendedor->id!=1): ?>
                          <option value="<?php echo $vendedor->id ?>"  <?php echo set_value_select($cotizacion,'vendedor',$vendedor->id,$cotizacion->iduser); ?>><?php echo $vendedor->nombres ?></option>
                          <?php endif ?>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row mb-1">
                    <div class="col-sm-3">
                      <label for="ocompra" class="control-label mb-0">Orden Compra</label>
                      <input name="ocompra" type="text" id="ocompra" class="form-control form-control-sm" value="">
                    </div>

                    <div class="col-sm-9">
                      <label for="dadicional" class="control-label mb-0">Descrip. Adicional</label>
                      <input name="dadicional" type="text" id="dadicional" value="" class="form-control form-control-sm">
                    </div>
                  </div>

                  <div id="detraccion" style="display: none;">
                    <fieldset class="border border-info mb-1 px-2">
                      <legend class="h6 pl-1 mb-0">Detraccion</legend>
                      <div class="form-group row mb-1">
                        <label for="codigo" class="col-sm-2 col-form-label">Bien/Servicio</label>
                        <div class="col-sm-4">
                          <select name="codigo" id="codigo" class="form-control form-control-sm" onchange="mostrarDato('<?php echo base_url(); ?>venta/busDetraccion',this.value,'pdetraccion');setTimeout(function() { porcentajes('totalg','pdetraccion','mdetraccion',0); }, 300);">
                            <option value="">::Seleccione</option>
                            <?php foreach ($codigos as $codigo): ?>
                              <option value="<?php echo $codigo->id ?>"><?php echo $codigo->descripcion ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>

                        <label for="medio" class="col-sm-2 col-form-label">Medio de Pago</label>
                        <div class="col-sm-4">
                          <select name="medio" id="medio" class="form-control form-control-sm">
                            <option value="">::Seleccione</option>
                            <?php foreach ($medios as $medio): ?>
                              <option value="<?php echo $medio->id ?>"><?php echo $medio->descripcion ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>
                      </div>

                      <div class="form-group row mb-1">
                        <label for="ncuenta" class="col-sm-2 col-form-label">Cta Banco Nacion</label>
                        <div class="col-sm-3">
                          <input name="ncuenta" type="text" id="ncuenta" class="form-control form-control-sm" value="" />
                        </div>

                        <label for="pdetraccion" class="col-sm-2 col-form-label">Porcentaje</label>
                        <div class="col-sm-2">
                          <div class="input-group">
                            <input name="pdetraccion" type="text" id="pdetraccion" class="form-control form-control-sm" value="" onkeyup="porcentajes('totalg','pdetraccion','mdetraccion',0);" />
                            <div class="input-group-append">
                              <span class="input-group-text py-0 text-sm">%</span>
                            </div>
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text py-0 text-sm">S/.</span>
                            </div>
                            <input name="mdetraccion" type="text" id="mdetraccion" class="form-control form-control-sm" value="" readonly/>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  </div>

                  <div id="retencion" style="display: none;">
                    <div class="form-group row mb-1">
                      <label for="pretencion" class="col-sm-2 control-label">Retencion</label>
                      <div class="col-sm-2">
                        <div class="input-group">
                          <input name="pretencion" type="text" id="pretencion" class="form-control form-control-sm" value="" onkeyup="porcentajes('totalg','pretencion','mretencion');diferencia('totalg','mretencion','pagarg');"/>
                          <div class="input-group-append">
                            <span class="input-group-text py-0 text-sm">%</span>
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text py-0 text-sm">S/.</span>
                          </div>
                          <input name="mretencion" type="text" id="mretencion" class="form-control form-control-sm" value="" readonly/>
                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text py-0 text-sm">S/.</span>
                          </div>
                          <input name="pagarg" type="text" id="pagarg" class="form-control form-control-sm text-right" value="" readonly/>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row mb-1">
                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="canjear" name="canjear" value="1" onchange="mostrarDescuento(this.checked)">
                        <label class="custom-control-label" for="canjear">Canjear Vale</label>
                      </div>
                    </div>

                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="dizipay" name="dizipay" value="1" onchange="pagoIzipay(this);">
                        <label class="custom-control-label" for="dizipay">Izipay</label>
                      </div>
                    </div>

                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                          <input class="custom-control-input" name="cretencion" type="checkbox" id="cretencion" value="1" onchange="mostrarRetencion(this.checked);">
                        <label class="custom-control-label" for="cretencion">Retencion</label>
                      </div>
                    </div>

                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="impresion" name="impresion" value="1">
                        <label class="custom-control-label" for="impresion">Imp. Lote</label>
                      </div>
                    </div>

                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="formato" name="formato" value="1">
                        <label class="custom-control-label" for="formato">Formato A4</label>
                      </div>
                    </div>

                    <div class="col-sm-2 col-6">
                      <div class="custom-control custom-switch">
                          <input type="checkbox" class="custom-control-input" id="pcredito" name="pcredito" value="1" onchange="pagoCredito(this);">
                          <label class="custom-control-label" for="pcredito">Al Credito</label>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group row mb-1 mt-2">
                    <label for="cliente" class="col-sm-2 col-6 control-label">Cliente*</label>
                    <input name="idcliente" id="idcliente" type="hidden" value="<?php echo $cotizacion->idcliente ?>"/>
                    <input name="tdocumento" id="tdocumento" type="hidden" value="<?php echo $cliente->tdocumento ?>"/>
                    <div class="col-sm-7">
                      <div class="input-group">
                        <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="<?php echo $cotizacion->cliente ?>" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                        <div class="input-group-append">
                          <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>cliente/buscador/V','bdatos','Buscar Cliente')"><i class="fa fa-search" aria-hidden="true"></i></button>

                          <button type="button" class="btn btn-info btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>nventa/clientei','bdatos','Datos del Cliente')"><i class="fa fa-plus"></i></button>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <span class="col-form-label" id="puntaje">Puntos Acumulados : 0</span>
                    </div>
                  </div>

                  <div id="cvale" style="display: none;">
                    <div class="form-group row mb-1">
                      <div class="col-sm-7">
                        <div class="input-group">
                          <input type="text" class="form-control form-control-sm" id="nvale" name="nvale" value="" autocomplete="off" onkeypress="return event.keyCode != 13;" placeholder="Codigo del vale">
                          <div class="input-group-append">
                            <button class="btn btn-warning btn-sm" type="button" onclick="validarVale('<?php echo base_url(); ?>cliente/busVale')"><i class="fa fa-search" aria-hidden="true"></i> Validar</button>

                            <button class="btn btn-danger btn-sm" type="button" onclick="limpiarVale('nvale')"><i class="fa fa-ban" aria-hidden="true"></i> Limpiar</button>
                          </div>
                        </div>
                      </div>

                      <input type="hidden" id="validador" name="validador" value="0">
                      <div class="col-sm-5">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text text-sm py-0"><strong>DESCUENTO S./</strong></span>
                          </div>
                          <input name="mdsctog" type="text" id="mdsctog" class="form-control form-control-sm" value="" readonly/>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div id="cizipay" style="display: none;">
                    <div class="form-group row mb-1">
                      <label for="pizipay" class="col-sm-2 control-label">Izipay</label>
                      <div class="col-sm-3">
                        <div class="input-group">
                          <input name="pizipay" type="text" id="pizipay" class="form-control form-control-sm" value="" onkeyup="porcentajes('totalg','pizipay','mizipay');suma('totalg','mizipay','pagar');" />
                          <div class="input-group-append">
                            <span class="input-group-text py-0 text-sm">%</span>
                          </div>
                        </div>
                      </div>

                      <div class="col-sm-3">
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text py-0 text-sm">S/.</span>
                          </div>
                          <input name="mizipay" type="text" id="mizipay" class="form-control form-control-sm" value=""  onkeyup="suma('totalg','mizipay','pagar');"/>
                        </div>
                      </div>

                      <label for="pagar" class="col-sm-1 control-label">Pagar</label>
                      <div class="col-sm-3">
                        <input name="pagar" type="text" id="pagar" class="form-control form-control-sm text-right" value=""/>
                      </div>
                    </div>
                  </div>

                  <div id="contado" style="display: block;">
                    <div class="form-group row mb-1">
                      <label for="mpago1" class="col-sm-2 col-6 col-form-label">Medio Pago*</label>
                      <div class="col-sm-3 col-6">
                        <select name="mpago[]" id="mpago1" class="form-control form-control-sm">
                          <?php foreach ($mpagos as $mpago): ?>
                            <option value="<?php echo $mpago->id ?>"><?php echo $mpago->descripcion ?></option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <label for="monto1" class="col-sm-1 col-6 col-form-label">Monto</label>
                      <div class="col-sm-2 col-6">
                        <input name="monto[]" type="text" id="monto1" class="form-control form-control-sm" value="<?php echo $cotizacion->total; ?>" placeholder="Monto" readonly/>
                      </div>

                      <label for="documento1" class="col-sm-1 col-6 col-form-label">Doc.</label>
                      <div class="col-sm-2 col-6">
                        <input name="documento[]" type="text" id="documento1" class="form-control form-control-sm" value="" placeholder="Doc sustenta"/>
                      </div>

                      <div class="col-sm-1 col-2">
                        <button type="button" class="btn btn-info btn-sm" onclick="apppagos('<?php echo base_url(); ?>nventa/metodos');"><i class="fa fa-plus"></i></button>
                      </div>
                    </div>
                  </div>

                  <div id="credito" style="display: none;">
                    <div class="form-group row mb-1">
                      <label for="pcuota" class="col-sm-2 control-label">Periodo*</label>
                      <div class="col-sm-4">
                        <select name="pcuota" id="pcuota" class="form-control form-control-sm">
                          <option value="">::Seleccione</option>
                          <option value="Semanal">Semanal</option>
                          <option value="Quincenal">Quincenal</option>
                          <option value="Mensual">Mensual</option>
                        </select>
                      </div>

                      <label for="cuotas" class="col-sm-1 control-label">Cuotas*</label>
                      <div class="col-sm-2">
                        <input name="cuotas" type="text" id="cuotas" class="form-control form-control-sm" value="" onkeyup="divisores('totalg','cuotas','mcuota')"/>
                      </div>

                      <label for="mcuota" class="col-sm-1 control-label">Monto*</label>
                      <div class="col-sm-2">
                        <input name="mcuota" type="text" id="mcuota" class="form-control form-control-sm" value="" readonly/>
                      </div>
                    </div>
                  </div>

                  <div class="form-group row mb-1">
                    <div class="col-sm-8">
                      <div id="cvuelto" style="display: block;">
                        <div class="row">
                          <div class="col-sm-4 col-6">
                            <input name="efectivo" type="text" id="efectivo" class="form-control form-control-sm" value="" onkeyup="diferencia('efectivo','totalg','vuelto')" placeholder="Monto en Efectivo">
                          </div>

                          <div class="col-sm-4 col-6">
                            <input name="vuelto" type="text" id="vuelto" class="form-control form-control-sm" value="" placeholder="Vuelto" readonly>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="col-sm-4 text-right">
                      <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR VENTA"/>
                    </div>
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

<div class="modal fade" id="busreceta">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title">Datos de Receta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row mb-1">
          <label for="mpaciente" class="col-sm-3 col-form-label">Paciente</label>
          <div class="col-sm-7">
            <input name="mpaciente" id="mpaciente" type="text" class="form-control form-control-sm">
          </div>
        </div>

        <div class="form-group row mb-1">
          <label for="mdoctor" class="col-sm-3 col-form-label">Nombre Doctor</label>
          <div class="col-sm-7">
            <input name="mdoctor" id="mdoctor" type="text" class="form-control form-control-sm">
          </div>
        </div>

        <div class="form-group row mb-1">
          <label for="mcolegiatura" class="col-sm-3 col-form-label">N° Colegiatura Doctor</label>
          <div class="col-sm-7">
            <input name="mcolegiatura" id="mcolegiatura" type="text" class="form-control form-control-sm">
          </div>
        </div>
        <input type="hidden" id="mtabla" name="mtabla" value="">

        <div class="form-group row mb-1">
          <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-primary btn-sm ml-4" onclick="appreceta();">AGREGAR</button>
            <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title" id="modalTitle">Datos de la Serie</h5>
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

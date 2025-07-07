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

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioVenta('".base_url()."venta/guardav/".$id."');")); ?>
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group row mb-1">
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

                    <div class="col-sm-6 col-6">
                      <select name="toperacion" id="toperacion" class="form-control form-control-sm" onchange="mostrarDetraccion('<?php echo base_url(); ?>empresa/busDetraccion',this.value)">
                        <?php foreach ($toperaciones as $toperacion): ?>
                          <option value="<?php echo $toperacion->id ?>"><?php echo $toperacion->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group row mb-1">
                    <label for="cliente" class="col-sm-3 col-3 col-form-label">Cliente</label>
                    <div class="col-sm-9 col-9">
                      <input name="idcliente" id="idcliente" type="hidden" value="<?php echo $nventa->idcliente ?>"/>
                      <input name="tdocumento" id="tdocumento" type="hidden" value="<?php echo $cliente->tdocumento ?>"/>
                      <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="<?php echo $nventa->cliente ?>" readonly>
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

                  <div class="form-group row mb-1">
                    <div class="col-sm-3 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="impresion" name="impresion" value="1">
                        <label class="custom-control-label" for="impresion">Imp. Lote</label>
                      </div>
                    </div>

                    <div class="col-sm-3 col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="formato" name="formato" value="1">
                        <label class="custom-control-label" for="formato">Formato A4</label>
                      </div>
                    </div>

                    <div class="col-sm-6 text-right">
                      <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR VENTA"/>
                    </div>
                  </div>
                </div>

                <div class="col-sm-8">
                  <div class="table-responsive table-striped border border-info" style="height: 500px; font-size: .78rem">
                    <table class="table table-hover table-sm">
                      <thead class="thead-dark">
                        <tr>
                          <th width="56%">Descripcion</th>
                          <th width="10%">Lote</th>
                          <th width="6%">Unidad</th>
                          <th width="7%">Cantidad</th>
                          <th width="7%">Precio</th>
                          <th width="7%">Dscto</th>
                          <th width="9%">Importe</th>
                        </tr>
                      </thead>
                      <tbody id="grilla">
                        <?php $igravado=0; $exonerado=0; $inafecto=0; $i=1; ?>
                        <?php foreach ($detalles as $detalle): ?>
                          <?php
                            $producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
                            if ($producto->tafectacion==30) {
                              $inafecto+=$detalle->importe;
                            } else if($producto->tafectacion==20) {
                              $exonerado+=$detalle->importe;
                            } else {
                              $igravado+=$detalle->importe;
                            }
                            $dscto=$detalle->dscto>0 ? $detalle->dscto : '' ;
                          ?>
                          <tr>
                            <td>
                              <input type="hidden" name="tipo[]" value="<?php echo $producto->tipo ?>"/>
                              <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto ?>"/>
                              <input type="hidden"  name="controlado[]" value="<?php echo $detalle->controlado ?>">
                              <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo"/>
                            </td>
                            <td>
                              <input type="hidden" name="lote[]" value="<?php echo $producto->lote ?>"/>
                              <input type="text" name="nlote[]" value="<?php echo $detalle->lote; ?>" class="campo"/>
                              <input type="hidden" name="clote[]" value="<?php echo preg_replace('/\d/', '0', $detalle->clote); ?>"/>
                              <input type="hidden" name="flote[]" value="<?php echo $detalle->fvencimiento; ?>"/>
                            </td>
                            <td>
                              <input type="text" name="unidad[]" value="<?php echo $detalle->unidad ?>" class="campo"/>
                              <input type="hidden" name="tafectacion[]" value="<?php echo $producto->tafectacion ?>"/>
                            </td>
                            <td>
                              <input type="number" name="cantidad[]" value="<?php echo $detalle->cantidad ?>" class="campo"/>
                              <input type="hidden" name="calmacen[]" value="<?php echo 0; ?>"/>
                              <input type="hidden" name="palmacen[]" value="<?php echo 0; ?>"/>
                            </td>
                            <td><input type="text" name="precio[]" value="<?php echo $detalle->precio ?>" class="campo text-right"/></td>
                            <td>
                              <input type="hidden" class="dscton" name="tdscto[]" id="tdscto[]" value="1">
                              <input type="number" name="dscto[]" value="<?php echo $dscto ?>" class="campo" step="0.01"/>
                            </td>
                            <td>
                              <input type="number" min="0.01" step="0.01" name="importe[]" value="<?php echo $detalle->importe ?>" class="campo text-right"/>
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


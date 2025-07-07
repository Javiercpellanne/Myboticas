<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Compra Mercaderia</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>compra">Mercaderia</a></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioFormulario('".base_url()."compra/guardar/".$id."');")); ?>
              <div class="form-group row mb-1">
                <label for="comprobante" class="col-sm-1 col-form-label">Comprobante*</label>
                <div class="col-sm-2">
                  <select name="comprobante" id="comprobante" class="form-control form-control-sm" required>
                    <option value="">::Selec</option>
                    <option value="01">Factura</option>
                    <option value="03">Boleta</option>
                    <option value="09">Guia Remision</option>
                    <option value="99">Nota Venta</option>
                  </select>
                </div>

                <label for="serie" class="col-sm-1 col-form-label">Serie*</label>
                <div class="col-sm-1" >
                   <input name="serie" type="text" id="serie" value=""  class="form-control form-control-sm" required/>
                </div>

                <label for="numero" class="col-sm-1 col-form-label">Numero*</label>
                <div class="col-sm-1" >
                   <input name="numero" type="text" id="numero" value=""  class="form-control form-control-sm" required/>
                </div>

                <label for="fecha" class="col-sm-1 col-form-label">Fecha Emision*</label>
                <div class="col-sm-2">
                  <input name="fecha" type="date" id="fecha" class="form-control form-control-sm" value="<?php echo date("Y-m-d") ?>" max="<?php echo date("Y-m-d") ?>" required/>
                </div>

                <div class="col-sm-2">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                    </div>
                    <input id="codbarra" type="text" class="form-control form-control-sm" placeholder="Codigo Barra" aria-label="Codigo Barra" aria-describedby="basic-addon1" onkeydown="productoBarrac(event,'<?php echo base_url(); ?>producto/busCodigobarra',this.value);">
                  </div>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="proveedor" class="col-sm-1 col-form-label">Proveedor*</label>
                <div class="col-sm-4">
                  <input name="idproveedor" id="idproveedor" type="hidden" value="<?php echo $solicitud->idproveedor ?>" required/>
                  <input name="proveedor" type="text" id="proveedor" class="form-control form-control-sm" value="<?php echo $solicitud->proveedor ?>" required data-readonly onkeydown="return false">
                </div>

                <div class="col-sm-2">
                  <div class="custom-control custom-switch mt-1">
                      <input class="custom-control-input" name="incluye" type="checkbox" id="incluye" value="1" onclick="calcularCompra();" checked>
                    <label class="custom-control-label" for="incluye">Precio Incluye IGV</label>
                  </div>
                </div>

                <div class="col-sm-2">
                  <button id="buscar" type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#busproductosi"><i class="fa fa-cart-plus"></i> AGREGAR PRODUCTO</button>
                </div>
              </div>

              <div class="table-responsive p-0" style="height: 370px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="40%">DESCRIPCION</th>
                      <th width="12%">LOTE</th>
                      <th width="10%">F VCTO</th>
                      <th width="8%">U.M</th>
                      <th width="8%">CANT</th>
                      <th width="8%">P.U</th>
                      <th width="8%">IMPORTE</th>
                      <th width="4%"></th>
                    </tr>
                  </thead>
                  <tbody id="grilla">
                    <?php foreach ($detalles as $detalle): ?>
                      <?php
                      $producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
                      if ($producto->lote==1) {$estilo='form-control form-control-sm'; $tinput='date';} else {$estilo='campo'; $tinput='text';}
                      ?>
                      <tr>
                        <td>
                          <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto ?>">
                          <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo" readonly="">
                        </td>
                        <td><input type="text" name="lote[]" value="" class="<?php echo $estilo ?>"></td>
                        <td><input type="<?php echo $tinput ?>" name="fvencimiento[]" value="" class="<?php echo $estilo ?>"></td>
                        <td>
                          <input type="text" name="unidad[]" value="<?php echo $detalle->unidad ?>" class="campo unidades">
                          <input type="hidden" name="tafectacion[]" value="<?php echo $producto->tafectacion; ?>"/>
                          <input type="hidden" class="factores" name="factor[]" id="factor[]" value="<?php echo $detalle->factor; ?>">
                          <input type="hidden" class="calmacenes" name="almacenc[]" value="<?php echo $detalle->cantidad*$detalle->factor; ?>">
                          <input type="hidden" class="palmacenes" name="almacenp[]" value="0">
                        </td>
                        <td><input type="text" name="cantidad[]" value="<?php echo $detalle->cantidad ?>" class="form-control form-control-sm cantidades"></td>
                        <td>
                          <input type="text" name="precio[]" value="0" class="form-control form-control-sm text-right precios">
                          <input type="hidden" name="pventa[]" value="">
                          <input type="hidden" name="venta[]" value="">
                          <input type="hidden" name="blister[]" value="">
                        </td>
                        <td><input type="text" name="importe[]" value="0" class="campo text-right importes"></td>
                        <td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 eliminac" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>

              <table cellpadding="3">
                <tr>
                  <td width="10%" align="right"><strong>SUBTOTAL </strong></td>
                  <td width="10%">
                    <input name="gratuito" type="hidden" id="gratuito" value="0"/>
                    <input name="gravado" type="hidden" id="gravado" value="0"/>
                    <input name="inafecto" type="hidden" id="inafecto" value="0"/>
                    <input name="exonerado" type="hidden" id="exonerado" value="0"/>
                    <h4><input name="subtotal" type="text" id="subtotal" class="campo text-right" value="0"/></h4>
                  </td>
                  <td width="10%" align="right"><strong>IGV </strong></td>
                  <td width="10%">
                    <h4><input name="igv" type="text" id="igv" class="campo text-right" value="0"/></h4>
                  </td>
                  <td width="10%" align="right"><strong>TOTAL </strong></td>
                  <td width="10%">
                    <h4><input name="total" type="text" id="total" class="campo text-right" value="0"/></h4>
                  </td>
                  <td width="10%" align="right"><b>PERCEPCION </b></td>
                  <td width="10%">
                    <input name="mpercepcion" type="text" id="mpercepcion" class="form-control form-control-sm text-right"onkeyup="suma('total','mpercepcion','pagar');" value=""/>
                  </td>
                  <td width="10%" align="right"><strong>PAGAR </strong></td>
                  <td width="10%">
                    <h4><input name="pagar" type="text" id="pagar" class="campo text-right" value="" readonly/></h4>
                  </td>
                </tr>
              </table>

              <div class="form-group row mb-1">
                <label for="tpago" class="col-sm-1 col-form-label">Tipo Pago*</label>
                <div class="col-sm-2">
                  <select name="tpago" id="tpago" class="form-control form-control-sm" required onchange="pagoCreditoc(this.value)">
                    <option value="1">Contado</option>
                    <option value="2">Credito</option>
                  </select>
                </div>

                <div class="col-sm-6" id="contado" style="display: block;">
                  <div class="row">
                    <label for="mpago" class="col-sm-2 col-form-label">Medio Pago</label>
                    <div class="col-sm-4">
                      <select name="mpago" id="mpago" class="form-control form-control-sm" required>
                        <?php foreach ($mpagos as $mpago): ?>
                          <option value="<?php echo $mpago->id ?>"><?php echo $mpago->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="col-sm-12 text-right">
                  <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR"/>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="busproductosi">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h4 class="modal-title">Datos del Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">
        <form name="fproducto" id="fproducto" autocomplete="off">
          <div id="mensajeerror"></div>
          <input name="mcodigo" id="mcodigo" type="hidden">
          <div class="form-group row mb-1">
            <label for="mdescripcion" class="col-sm-2 col-form-label">Producto</label>
            <div class="col-sm-10">
              <div class="input-group">
                <input name="mdescripcion" id="mdescripcion" type="text" class="form-control form-control-sm" onkeyup="productoNombrec('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off">
                <div class="input-group-append">
                  <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                </div>
              </div>

              <div id="tbldescripcion" style="position:absolute; z-index: 1051; width: 98%; overflow: overlay; max-height:300px; display: none;">
                <dl class="bg-buscador" id="grdescripcion">
                </dl>
              </div>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="mmedida" class="col-sm-2 col-form-label">Tipo Precio</label>
            <div class="col-sm-3">
              <input type="hidden" class="form-control form-control-sm text-right" id="mfactor" name="mfactor" value="1"  readonly>
              <select name="mmedida" id="mmedida" class="form-control form-control-sm" onchange="conversion(this.value)">
                <option value="">Seleccione</option>
              </select>
            </div>

            <label for="mtafectacion" class="col-sm-2 col-form-label">Afectacion IGV</label>
            <div class="col-sm-4">
              <select name="mtafectacion" id="mtafectacion" class="form-control form-control-sm" required>
                <option value="">::Selec</option>
                <?php foreach ($tafectaciones as $tafectacion) {?>
                  <option value="<?php echo $tafectacion->id ?>"><?php echo $tafectacion->descripcion ?></option>
                <?php  }  ?>
              </select>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="munidades" class="col-sm-2 col-form-label">Cantidad</label>
            <div class="col-sm-2">
              <input type="text" class="form-control form-control-sm text-right" id="munidades" name="munidades" value="" onkeyup="divisores('mtotal','munidades','mcosto');factores('munidades','mfactor','mcantidad');divisores('mcosto','mfactor','mmonto');" required>
            </div>

            <label for="mcosto" class="col-sm-2 col-form-label">Precio</label>
            <div class="col-sm-2">
              <h4><input type="text" class="campo text-right" id="mcosto" name="mcosto" value=""></h4>
            </div>

            <label for="mtotal" class="col-sm-2 col-form-label">Subtotal</label>
            <div class="col-sm-2">
              <input name="mtotal" id="mtotal" type="text" class="form-control form-control-sm text-right" value="" onkeyup="divisores('mtotal','munidades','mcosto');divisores('mcosto','mfactor','mmonto');" required>
            </div>
          </div>
          <input name="mcantidad" id="mcantidad" type="hidden" value="">
          <input name="mmonto" id="mmonto" type="hidden" value="">
          <input type="hidden" name="mactivar" id="mactivar" value="">
          <div id="mdetalle" class="form-group mb-2" style="display: none;">
            <div class="row">
              <label for="mlote" class="col-sm-2 col-form-label">Codigo Lote </label>
              <div class="col-sm-3">
                <input name="mlote" id="mlote" type="text" class="form-control form-control-sm" value="">
              </div>

              <label for="mfecha" class="col-sm-3 col-form-label">Fec. Vencimiento </label>
              <div class="col-sm-4">
                <input name="mfecha" id="mfecha" type="date" class="form-control form-control-sm" value="">
              </div>
            </div>
          </div>

          <input type="hidden" id="pactualizar" name="pactualizar" value="<?php echo $empresa->pcompra; ?>">
          <?php if ($empresa->pcompra==1): ?>
            <fieldset class="border border-info mb-2 px-2">
              <legend class="h6 pl-1">Actualizar Precios Venta</legend>
              <div class="form-group row mb-2">
                <input type="hidden" id="gunidad" name="gunidad" value="<?php echo $empresa->gunidad; ?>">
                <label for="mutilidadu" class="col-sm-2 col-form-label">Margen Ganancia</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-sm text-right" id="mutilidadu" name="mutilidadu" value="">
                    <div class="input-group-append">
                      <span class="input-group-text text-sm py-0">%</span>
                    </div>
                  </div>
                </div>

                <input type="hidden" id="mfactoru" name="mfactoru" value="1">
                <label for="mpventa" class="col-sm-2 col-form-label">P. V. Unidad</label>
                <div class="col-sm-2">
                  <input name="mpventa" id="mpventa" type="text" class="form-control form-control-sm" value="">
                </div>

                <div class="col-sm-3 text-center">
                  <button type="button" class="btn btn-info btn-sm" onclick="calcularPrecios();"><i class="fa fa-calculator"></i> Calcular Precios</button>
                </div>
              </div>

              <div class="form-group row mb-2">
                <input type="hidden" id="gcaja" name="gcaja" value="<?php echo $empresa->gcaja; ?>">
                <label for="mutilidadc" class="col-sm-2 col-form-label">Margen Ganancia</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-sm text-right" id="mutilidadc" name="mutilidadc" value="" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text text-sm py-0">%</span>
                    </div>
                  </div>
                </div>

                <input type="hidden" id="mfactorc" name="mfactorc" value="">
                <label for="mventa" class="col-sm-2 col-form-label">P. V. Caja</label>
                <div class="col-sm-2">
                  <input name="mventa" id="mventa" type="text" class="form-control form-control-sm" value="" readonly>
                </div>
              </div>

              <div class="form-group row mb-2">
                <input type="hidden" id="gblister" name="gblister" value="<?php echo $empresa->gblister; ?>">
                <label for="mutilidadb" class="col-sm-2 col-form-label">Margen Ganancia</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control form-control-sm text-right" id="mutilidadb" name="mutilidadb" value="" readonly>
                    <div class="input-group-append">
                      <span class="input-group-text text-sm py-0">%</span>
                    </div>
                  </div>
                </div>

                <input type="hidden" id="mfactorb" name="mfactorb" value="">
                <label for="mblister" class="col-sm-2 col-form-label">P. V. Blister</label>
                <div class="col-sm-2">
                  <input name="mblister" id="mblister" type="text" class="form-control form-control-sm" value="" readonly>
                </div>
              </div>
            </fieldset>
          <?php endif ?>

          <div class="form-group row mb-0">
            <div class="col-sm-12 text-right">
              <button type="button" class="btn btn-primary btn-sm ml-4" onclick="appcompra();">AGREGAR</button>
              <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="reset_compra();">CERRAR</button>
            </div>
          </div>
        </form>
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
      <div class="modal-body">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>


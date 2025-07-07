<section class="content-header pb-1">
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
</section>

<section class="content">
  <div class="container-fluid">
    <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioPos('".base_url()."pos/guardar');")); ?>
    <div class="row">
      <div class="col-sm-7">
        <div class="card card-primary card-outline">
          <div class="card-header py-2">
            <div class="d-flex flex-nowrap overflow-auto">
              <a href="javascript:void(0)" class="btn btn-primary py-1 mb-0 mr-2 text-nowrap" style="font-size: .8rem!important;" onclick="mcategoria('<?php echo base_url(); ?>producto/busCategoria','0');">TODAS</a>
              <?php foreach ($categorias as $categoria): ?>
                <?php $contador=$this->producto_model->contador(array('idcategoria'=>$categoria->id,'estado'=>1,"factor>"=>0)); ?>
                <?php if ($contador>0): ?>
                <a href="javascript:void(0)" class="btn btn-primary py-1 mb-0 mr-2 text-nowrap" style="font-size: .8rem!important;" onclick="mcategoria('<?php echo base_url(); ?>producto/busCategoria','<?php echo $categoria->id; ?>');"><?php echo $categoria->descripcion; ?></a>
                <?php endif ?>
              <?php endforeach ?>
            </div>
          </div>

          <div class="card-body p-2">
            <div class="form-group row mb-2">
              <div class="col-sm-12">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="basic-addon1"><i class="fas fa-search" aria-hidden="true"></i></span>
                  </div>
                  <input name="bproducto" type="text" id="bproducto" class="form-control form-control-sm" value="" placeholder="Nombre producto" onkeyup="productoNombrep('<?php echo base_url(); ?>producto/busProductos',this.value)">
                </div>
              </div>
            </div>

            <div class="row" style="height: 540px; overflow: auto; font-size: .74rem" id="tblproducto">
              <?php foreach ($productos as $producto): ?>
                <?php
                $nproducto=$producto->descripcion;
                if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
                $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
                $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
                ?>
                <div class="col-sm-3 col-6">
                  <div class="card mb-2">
                    <div class="card-body p-2 position-relative">
                      <?php if ($cantidad->stock < 1): ?>
                        <a href="javascript:void(0)" style="pointer-events: none; cursor: not-allowed;">
                      <?php else: ?>
                        <a href="javascript:void(0)"
                           onclick="appvrapido('<?php echo $producto->id; ?>', `<?php echo $nproducto; ?>`,'<?php echo $producto->umedidav; ?>','<?php echo 1; ?>','<?php echo $producto->tafectacion; ?>','<?php echo $pventa; ?>','<?php echo $producto->lote; ?>','<?php echo $cantidad->stock; ?>','<?php echo $producto->tipo; ?>','<?php echo $empresa->pventa; ?>');">
                      <?php endif ?>

                        <!-- Contenedor de las imágenes superpuestas -->
                        <div class="image-container position-relative">

                          <!-- Imagen base (producto) -->
                          <?php if ($producto->ruta!=NULL): ?>
                          <img src="<?php echo $producto->ruta; ?>" class="img-thumbnail img-fluid">
                          <?php else: ?>
                          <img src="<?php echo base_url(); ?>downloads/productos/default.jpg" class="img-thumbnail">
                          <?php endif ?>

                          <?php if ($cantidad->stock<1): ?>
                          <!-- Imagen superpuesta -->
                          <img src="<?php echo base_url(); ?>downloads/productos/sinstock.png" class="img-overlay position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">
                          <?php endif ?>

                          <!-- Texto superpuesto -->
                          <div class="overlay-text position-absolute w-100 text-center"
                               style="bottom: 0; left: 0; background: rgba(0, 0, 0, 0.5); color: #fff;">
                            <?php echo $nproducto; ?>
                          </div>
                        </div>
                      </a>
                    </div>

                    <div class="card-footer p-2">
                      <h5 class="text-right my-0">
                        <a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busInformacion/<?php echo $producto->id; ?>/pos','bdatos','Informacion Producto')" class="btn btn-primary btn-sm py-0 float-left" title="Informacion Producto" data-toggle="tooltip" data-placement="bottom">
                          <i class="fa fa-search"></i> <?php echo $cantidad->stock; ?>
                        </a>

                        <?php if ($producto->factor>1): ?>
                        <a href="javascript:void(0)" onclick="mostrarModal('<?php echo base_url(); ?>producto/busPrecios/<?php echo $producto->id; ?>','bdatos','Precios Disponibles')" class="btn btn-info btn-sm py-0 float-left ml-1" title="Precios Disponibles" data-toggle="tooltip" data-placement="bottom">
                          <i class="fa fa-tag"></i>
                        </a>
                        <?php endif ?>

                        <?php echo $pventa; ?>
                      </h5>
                    </div>
                  </div>
                </div>
              <?php endforeach ?>
              </div>
          </div>
        </div>
      </div>

      <div class="col-sm-5">
        <div class="card card-primary card-outline">
          <div class="card-body p-2">
            <div class="form-group row mb-1">
              <label for="cliente" class="col-sm-3 col-6 control-label">Comprobante*</label>
              <div class="col-sm-4">
                <select name="comprobante" id="comprobante" class="form-control form-control-sm" onchange="tcomprobante(this.value,'<?php echo base_url(); ?>venta/busSerie')" required>
                  <option value="99">NOTA DE VENTA</option>
                  <?php if ($empresa->facturacion==1): ?>
                    <?php foreach ($comprobantes as $comprobante): ?>
                      <option value="<?php echo $comprobante->id ?>"><?php echo $comprobante->descripcion ?></option>
                    <?php endforeach ?>
                  <?php endif ?>
                </select>
              </div>

              <div class="col-sm-2 col-6">
                <input name="serie" type="text" id="serie" value="<?php echo $nserie->serie ?>" class="form-control form-control-sm" readonly required/>
              </div>
            </div>

            <div class="table-responsive table-striped" style="height: 417px; overflow-x: hidden; border-top: solid #17a2b8; font-size: .79rem">
              <table class="table table-striped table-hover table-sm">
                <tbody id="grilla">
                </tbody>
              </table>
            </div>

            <div class="form-group row my-1">
              <label for="cliente" class="col-sm-2 col-6 control-label">Cliente*</label>
              <input name="idcliente" id="idcliente" type="hidden" value="1"/>
              <input name="tdocumento" id="tdocumento" type="hidden" value="0"/>
              <div class="col-sm-7">
                <div class="input-group">
                  <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="CLIENTES VARIOS" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
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

            <div class="row">
              <div class="col-sm-6 pr-1">
                <table style="border-top: solid #17a2b8;">
                  <thead>
                    <tr>
                      <td width="70%"><b>Pagos Agregados</b></td>
                      <td width="30%"><button type="button" class="btn btn-info btn-sm btn-block" onclick="mostrarPagos('<?php echo base_url(); ?>nventa/metodos');"><i class="fa fa-plus"></i> Agregar</button></td>
                    </tr>
                  </thead>
                  <tbody id="tblpagos">
                    <tr>
                      <td>Efectivo<input type="hidden" name="medios[]" value="1/Efectivo"></td>
                      <td>
                        <h5 class="my-0"><input name="montos[]" id="monto1" type="text" class="campo" value="0.00"/></h5>
                        <input name="referencia[]" type="hidden" value=""/>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="col-sm-6 pl-0">
                <input name="gratuito" type="hidden" id="gratuito" value=""/>
                <input name="bimponible" type="hidden" id="bimponible" value=""/>
                <input name="inafecto" type="hidden" id="inafecto" value=""/>
                <input name="exonerado" type="hidden" id="exonerado" value=""/>
                <table style="border-top: solid #17a2b8;">
                  <tr>
                    <td width="50%"> <b>OP. GRAVADA <span class="float-sm-right">S./</span></b></td>
                    <td width="50%">
                      <h4 class="my-0"><input name="gravado" type="text" id="gravado" class="campo text-right" value="0.00"/></h4>
                    </td>
                  </tr>
                  <tr>
                    <td> <b>IGV <span class="float-sm-right">S./</span></b></td>
                    <td>
                      <h4 class="my-0"><input name="igv" type="text" id="igv" class="campo text-right" value="0.00"/></h4>
                    </td>
                  </tr>
                  <tr>
                    <td> <b>TOTAL <span class="float-sm-right">S./</span></b></td>
                    <td>
                      <h4 class="my-0"><input name="totalg" type="text" id="totalg" class="campo text-right" value="0.00"/></h4>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="form-group row mb-0">
              <div class="col-sm-12">
                <input type="submit" class="btn btn-primary btn-sm btn-block font-weight-bold" id="btsubmit" value="PAGAR" style="font-size: 1.2rem !important;"/>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php echo form_close(); ?>
  </div>
</section>

<div class="modal fade" id="busdatos">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title" id="modalTitle">Datos de la Venta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body p-3">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>

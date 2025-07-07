<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Nota de Credito (<?php echo $datos->serie.'-'.$datos->numero; ?>)</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active">Nota de Credito</li>
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

            <?php if ($existentes!=null): ?>
              <h4>Notas Generadas :
                <?php foreach ($existentes as $existente): ?>
                  <small class="text-muted"> <?php echo $existente->serie.'-'.$existente->numero; ?></small>
                    <?php endforeach ?>
              </h4>
            <?php endif ?>

            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off", "onsubmit"=>"return envioVenta('".base_url()."venta/guardan/".$id."');")); ?>
              <input type="hidden" id="vcomprobante" name="vcomprobante" value="<?php echo $datos->tcomprobante; ?>">
              <input type="hidden" id="vnumero" name="vnumero" value="<?php echo $datos->serie.'-'.$datos->numero; ?>">
              <div class="form-group row mb-1">
                <label for="serie" class="control-label col-sm-1">Serie*</label>
                <div class="col-sm-1">
                  <input name="serie" type="text" id="serie" value="<?php echo $nserie->serie ?>" class="form-control form-control-sm" readonly required/>
                </div>

                <label for="tnota" class="control-label col-sm-1">Tipo Nota*</label>
                <div class="col-sm-3">
                  <select name="tnota" id="tnota" class="form-control form-control-sm" required>
                    <?php foreach ($tcreditos as $tcredito): ?>
                      <option value="<?php echo $tcredito->id ?>"><?php echo $tcredito->descripcion ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <label for="motivo" class="control-label col-sm-1">Motivo*</label>
                <div class="col-sm-5" >
                  <input name="motivo" type="text" id="motivo" value="" class="form-control form-control-sm" required/>
                </div>
              </div>

              <div class="form-group row mb-1">
                <label for="idcliente" class="control-label col-sm-1">Cliente*</label>
                <div class="col-sm-5">
                  <input name="idcliente" id="idcliente" type="hidden" value="<?php echo $datos->idcliente ?>" required/>
                  <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="<?php echo $datos->cliente ?>" required readonly>
                </div>

                <label for="fecha" class="control-label col-sm-2">Fecha Emision*</label>
                <div class="col-sm-2">
                  <input name="fecha" type="date" id="fecha" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr>
                      <th width="50%">DESCRIPCION</th>
                      <th width="17%">LOTE</th>
                      <th width="5%">U.M</th>
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
                    $factor=$detalle->calmacen/$detalle->cantidad;
                    ?>
                      <tr>
                        <td>
                          <input type="hidden" name="tipo[]" value="<?php echo $producto->tipo ?>" class="tipov"/>
                          <input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto ?>"/>
                          <input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo"/>
                        </td>
                        <td>
                          <input type="text" name="lote[]" value="<?php echo $detalle->lote ?>" class="campo"/>
                          <input type="hidden" name="fvencimiento[]" value="<?php echo $detalle->fvencimiento ?>"/>
                        </td>
                        <td>
                          <input type="hidden" name="tafectacion[]" value="<?php echo $detalle->tafectacion ?>" class="campo"/>
                          <input type="hidden" name="factor[]" value="<?php echo $factor; ?>" class="factorv">
                          <input type="text" name="unidad[]" value="<?php echo $detalle->unidad ?>" class="campo"/>
                        </td>
                        <td>
                          <input type="hidden" name="clote[]" value="<?php echo $detalle->clote ?>"/>
                          <input type="hidden" name="almacenc[]" value="<?php echo $detalle->calmacen ?>" class="calmacenv">
                          <input type="hidden" name="almacenp[]" value="<?php echo $detalle->palmacen ?>">
                          <input type="text" name="cantidad[]" value="<?php echo $detalle->cantidad ?>" class="form-control form-control-sm cantidadv"/>
                        </td>
                        <td>
                          <input type="hidden" name="tprecio[]" value="<?php echo $detalle->tprecio ?>" class="campo"/>
                          <input type="text" name="precio[]" value="<?php echo $detalle->precio ?>" class="campo text-right preciov"/>
                        </td>
                        <td>
                          <input type="text"  name="importe[]" value="<?php echo $detalle->importe ?>" class="campo text-right importev"/>
                        </td>
                        <td>
                          <a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 eliminat" title="Eliminar"><i class="fa fa-trash"></i></a>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="5" align="right"><strong>IGV S./</strong></td>
                      <td>
                        <input name="gratuito" type="hidden" id="gratuito" value="<?php echo $datos->tgratuito; ?>"/>
                        <input name="bimponible" type="hidden" id="bimponible" value="<?php echo $datos->tgravado; ?>"/>
                        <input name="gravado" type="hidden" id="gravado" value="<?php echo $datos->tgravado ?>"/>
                        <input name="inafecto" type="hidden" id="inafecto" value="<?php echo $datos->tinafecto ?>"/>
                        <input name="exonerado" type="hidden" id="exonerado" value="<?php echo $datos->texonerado ?>"/>
                        <input name="igv" type="text" id="igv" class="campo text-right" value="<?php echo $datos->tigv ?>"/>
                      </td>
                      <td></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><strong>TOTAL S./</strong></td>
                      <td><input name="totalg" type="text" id="totalg" class="campo text-right" value="<?php echo $datos->total ?>"/></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <input type="hidden" name="tpago" value="<?php echo $datos->condicion;?>">
              <?php foreach ($cobros as $cobro): ?>
              <input type="hidden" name="mpago[]" value="<?php echo $cobro->idtpago;?>">
              <input type="hidden" name="monto[]" value="<?php echo $cobro->total;?>">
              <?php endforeach ?>

              <div class="form-group col-sm-12 text-center">
                <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="GUARDAR"/>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

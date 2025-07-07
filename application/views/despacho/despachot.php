<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Guia de Remision</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Venta</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url() ?>despacho">Guia de Remision</a></li>
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

            <?php echo form_open(base_url()."despacho/guardar",array("name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
              <div class="row">
                  <div class="form-group col-sm-1 mb-2">
                    <label for="serie" class="control-label">Serie*</label>
                    <input name="serie" type="text" id="serie" value="<?php echo $nserie->serie ?>" class="form-control form-control-sm" readonly required/>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="fenvio" class="control-label">Fecha Traslado*</label>
                    <input name="fenvio" type="date" id="fenvio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                  </div>

                  <div class="form-group col-sm-4 mb-2">
                    <label for="cliente" class="control-label">Cliente*</label>
                    <input name="idcliente" id="idcliente" type="hidden" value="0" required/>
                    <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="<?php echo $empresa->nombres; ?>" readonly>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="modot" class="control-label">Modo de traslado*</label>
                    <select name="modot" id="modot" class="form-control form-control-sm" onchange="traslado(this.value);" required>
                      <?php foreach ($modost as $modot): ?>
                        <option value="<?php echo $modot->id ?>"><?php echo $modot->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-3 mb-2">
                    <label for="motivot" class="control-label">Motivo de traslado*</label>
                    <select name="motivot" id="motivot" class="form-control form-control-sm" required>
                      <option value="<?php echo $motivost->id ?>"><?php echo $motivost->descripcion ?></option>
                    </select>
                  </div>
              </div>

              <div class="row">
                  <div class="form-group col-sm-4 mb-2">
                    <label for="descripciont" class="control-label">Descripción de motivo de traslado*</label>
                     <input name="descripciont" type="text" id="descripciont" value="TRASLADO DE MERCADERIA" class="form-control form-control-sm" required />
                  </div>
                  <div class="form-group col-sm-2 mb-2">
                    <label for="peso_total" class="control-label">Peso total (KGM)*</label>
                    <input name="peso_total" type="text" id="peso_total" value="" class="form-control form-control-sm" required/>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="paquetes" class="control-label">Número de paquetes*</label>
                    <input name="paquetes" type="text" id="paquetes" value="1" class="form-control form-control-sm" required/>
                  </div>

                  <div class="form-group col-sm-4 mb-2">
                    <label for="observaciones" class="control-label">Observaciones*</label>
                    <input name="observaciones" type="text" id="observaciones" value="" class="form-control form-control-sm" required />
                  </div>
              </div>

              <fieldset class="border border-info mb-2 px-2">
                <legend class="h6 pl-1">Datos envío</legend>
                <input type="hidden" id="codigop" name="codigop" value="<?php echo $nestablecimiento->codigo; ?>">
                <h6>- Dirección partida</h6>
                <div class="row">
                  <div class="form-group col-sm-2 mb-2">
                    <label for="departamentop" class="control-label">Departamento*</label>
                    <select class="form-control form-control-sm" id="departamentop" name="departamentop" onchange="bubicacion('<?php echo base_url(); ?>cliente/busProvincia',this.value,'provinciap')" required>
                      <option value="" <?php echo set_value_select($nestablecimiento,'departamento',"",$nestablecimiento->iddepartamento) ?>>::Selecc</option>
                      <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?php echo $departamento->id ?>" <?php echo set_value_select($nestablecimiento,'departamento',$departamento->id,$nestablecimiento->iddepartamento) ?>><?php echo $departamento->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="provinciap" class="control-label">Provincia*</label>
                    <select class="form-control form-control-sm" id="provinciap" name="provinciap" onchange="bubicacion('<?php echo base_url(); ?>cliente/busDistrito',this.value,'distritop')" required>
                      <option value="" <?php echo set_value_select($nestablecimiento,"provincia","",$nestablecimiento->idprovincia) ?>>::Selecc</option>
                      <?php foreach ($provincias as $provincia): ?>
                        <option value="<?php echo $provincia->id ?>" <?php echo set_value_select($nestablecimiento,'provincia',$provincia->id,$nestablecimiento->idprovincia) ?>><?php echo $provincia->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="distritop" class="control-label">Distrito*</label>
                    <select class="form-control form-control-sm" id="distritop" name="distritop" required>
                      <option value="" <?php echo set_value_select($nestablecimiento,'distrito',"",$nestablecimiento->iddistrito) ?>>::Selecc</option>
                      <?php foreach ($distritos as $distrito): ?>
                        <option value="<?php echo $distrito->id ?>" <?php echo set_value_select($nestablecimiento,'distrito',$distrito->id,$nestablecimiento->iddistrito) ?>><?php echo $distrito->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-6 mb-2">
                    <label for="direccionp" class="control-label">Direccion Partida*</label>
                    <input type="text" class="form-control form-control-sm" id="direccionp" name="direccionp" value="<?php echo $nestablecimiento->direccion ?>" required>
                  </div>
                </div>

                <input type="hidden" id="codigoe" name="codigoe" value="<?php echo $destino->codigo; ?>">
                <h6>- Dirección llegada</h6>
                <div class="row">
                  <div class="form-group col-sm-2 mb-2">
                    <label for="departamentoe" class="control-label">Departamento*</label>
                    <select class="form-control form-control-sm" id="departamentoe" name="departamentoe" onchange="bubicacion('<?php echo base_url(); ?>cliente/busProvincia',this.value,'provinciae')" required>
                      <option value="" <?php echo set_value_select($destino,'departamentoe',"",$destino->iddepartamento) ?>>::Selecc</option>
                      <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?php echo $departamento->id ?>" <?php echo set_value_select($destino,'departamentoe',$departamento->id,$destino->iddepartamento) ?>><?php echo $departamento->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="provinciae" class="control-label">Provincia*</label>
                    <select class="form-control form-control-sm" id="provinciae" name="provinciae" onchange="bubicacion('<?php echo base_url(); ?>cliente/busDistrito',this.value,'distritoe')" required>
                      <option value="" <?php echo set_value_select($destino,"provinciae","",$destino->idprovincia) ?>>::Selecc</option>
                      <?php foreach ($dprovincias as $provincia): ?>
                        <option value="<?php echo $provincia->id ?>" <?php echo set_value_select($destino,'provinciae',$provincia->id,$destino->idprovincia) ?>><?php echo $provincia->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-2 mb-2">
                    <label for="distritoe" class="control-label">Distrito*</label>
                    <select class="form-control form-control-sm" id="distritoe" name="distritoe" required>
                      <option value="" <?php echo set_value_select($destino,'distritoe',"",$destino->iddistrito) ?>>::Selecc</option>
                      <?php foreach ($ddistritos as $distrito): ?>
                        <option value="<?php echo $distrito->id ?>" <?php echo set_value_select($destino,'distritoe',$distrito->id,$destino->iddistrito) ?>><?php echo $distrito->descripcion ?></option>
                      <?php endforeach ?>
                    </select>
                  </div>

                  <div class="form-group col-sm-6 mb-2">
                    <label for="direccione" class="control-label">Direccion Llegada*</label>
                    <input type="text" class="form-control form-control-sm" id="direccione" name="direccione" value="<?php echo $destino->direccion ?>" required>
                  </div>
                </div>
              </fieldset>

              <div class="form-group row mb-2">
                <div class="col-sm-12">
                  <div class="custom-control custom-switch mt-1">
                    <input class="custom-control-input" name="m1l" type="checkbox" id="m1l" value="1" onclick="agregarM1L(this.checked)">
                    <label class="custom-control-label" for="m1l">Traslado en vehículos de categoría M1 o L</label>
                  </div>
                </div>
              </div>

              <div id="tpublico" style="display: block;">
                <fieldset class="border border-info mb-2 px-2">
                  <legend class="h6 pl-1">Datos transportista (Transporte Publico)</legend>
                  <input type="hidden" id="documentot" name="documentot" value="6">
                  <div class="form-group row mb-2">
                    <label for="ndocumentot" class="col-sm-2 control-label">Número de RUC*</label>
                    <div class="col-sm-2">
                      <input name="ndocumentot" type="text" id="ndocumentot" value="" class="form-control form-control-sm" required/>
                    </div>

                    <label for="nombrest" class="col-sm-2 control-label">Nombre y/o razón social*</label>
                    <div class="col-sm-6">
                      <div class="input-group">
                        <input name="nombrest" type="text" id="nombrest" value="" class="form-control form-control-sm" required/>
                        <div class="input-group-append">
                          <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#mdtransportista"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>

              <div id="tprivado" style="display: none;">
                <fieldset class="border border-info mb-2 px-2">
                  <legend class="h6 pl-1">Datos conductor (Transporte Privado)</legend>
                  <div class="row">
                    <div class="form-group col-sm-2 mb-2">
                      <label for="documentoc" class="control-label">Tipo Doc. Identidad *</label>
                      <select class="form-control form-control-sm" id="documentoc" name="documentoc" required>
                        <?php foreach ($didentidades as $didentidad): ?>
                          <option value="<?php echo $didentidad->id ?>"><?php echo $didentidad->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>

                    <div class="form-group col-sm-2 mb-2">
                      <label for="ndocumentoc" class="control-label">Número*</label>
                      <input name="ndocumentoc" type="text" id="ndocumentoc" value="-" class="form-control form-control-sm" required/>
                    </div>

                    <div class="form-group col-sm-4 mb-2">
                      <label for="nombresc" class="control-label">Apellidos y Nombres*</label>
                      <div class="input-group">
                        <input name="nombresc" type="text" id="nombresc" value="-" class="form-control form-control-sm" required/>
                        <div class="input-group-append">
                          <button class="btn btn-success btn-sm" type="button" data-toggle="modal" data-target="#mdconductor"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </div>
                      </div>
                    </div>

                    <div class="form-group col-sm-2 mb-2">
                      <label for="licencia" class="control-label">Licencia del conductor</label>
                      <input name="licencia" type="text" id="licencia" value="-" class="form-control form-control-sm" required />
                    </div>

                    <div class="form-group col-sm-2 mb-2">
                      <label for="placa" class="control-label">Numero de placa del vehiculo*</label>
                      <input name="placa" type="text" id="placa" value="-" class="form-control form-control-sm" required />
                    </div>
                  </div>
                </fieldset>
              </div>

              <table class="table table-striped table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th width="63%">DESCRIPCION</th>
                    <th width="5%">U.M</th>
                    <th width="6%">CANT</th>
                    <th width="14%">LOTE</th>
                    <th width="12%">F VCTO</th>
                  </tr>
                </thead>
                <tbody id="grilla">
                  <?php foreach ($detalles as $detalle): ?>
                    <tr>
                      <td><input type="hidden" name="idproducto[]" value="<?php echo $detalle->idproducto ?>"/><input type="text" name="descripcion[]" value="<?php echo $detalle->descripcion ?>" class="campo"/></td>
                      <td><input type="text" name="unidad[]" value="<?php echo $detalle->unidad ?>" class="campo"/></td>
                      <td><input type="text" name="cantidad[]" value="<?php echo $detalle->cantidad ?>" class="campo"/></td>
                      <td><input type="text" name="lote[]" value="<?php echo $detalle->lote ?>" class="campo"/></td>
                      <td><input type="text" name="fvencimiento[]" value="<?php echo $detalle->fvencimiento ?>" class="campo"/>
                    </tr>
                  <?php endforeach ?>
                </tbody>
              </table>

              <div class="form-group row">
                <div class="col-sm-2 offset-5">
                  <button type="submit" class="btn btn-primary">GUARDAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="mdconductor">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title">Buscar Conductor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form name="form_add" id="form_add">
          <div class="table-responsive p-0" style="height: 500px;">
            <table class="table table-head-fixed text-nowrap table-sm">
              <thead>
                <tr>
                  <th>NUMERO</th>
                  <th>NOMBRE</th>
                  <th>LICENCIA</th>
                  <th>PLACA</th>
                  <th>AGREGAR</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($conductores as $dato): ?>
                  <tr>
                    <td><?php echo $dato->documento; ?></td>
                    <td><?php echo $dato->nombres; ?></td>
                    <td><?php echo $dato->licencia; ?></td>
                    <td><?php echo $dato->placa; ?></td>
                    <td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosPrivado('<?php echo $dato->tdocumento; ?>','<?php echo $dato->documento; ?>','<?php echo $dato->nombres; ?>','<?php echo $dato->licencia; ?>','<?php echo $dato->placa; ?>');" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mdtransportista">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header py-1">
        <h5 class="modal-title">Buscar Transportista</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <form name="form_add" id="form_add">
          <div class="table-responsive p-0" style="height: 500px;">
            <table class="table table-head-fixed text-nowrap table-sm">
              <thead>
                <tr>
                  <th>NUMERO</th>
                  <th>NOMBRE</th>
                  <th>AGREGAR</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($transportistas as $dato): ?>
                  <tr>
                    <td><?php echo $dato->documento; ?></td>
                    <td><?php echo $dato->nombres; ?></td>
                    <td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosPublico('<?php echo $dato->tdocumento; ?>','<?php echo $dato->documento; ?>','<?php echo $dato->nombres; ?>');" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
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


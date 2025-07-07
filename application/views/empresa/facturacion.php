<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Empresa</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Configuracion</li>
          <li class="breadcrumb-item active">Empresa</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="card card-outline card-primary">
      <div class="card-header py-2">
        <ul class="nav nav-pills">
          <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>empresa">Generales</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>empresa/facturacion">Facturacion</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/avanzado">Avanzado</a></li>
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/configuracion">Acciones</a></li>
        </ul>
      </div>
      <div class="card-body p-3">
        <?php if($this->session->flashdata('mensaje')!=''){ ?>
          <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $this->session->flashdata('mensaje') ?>
          </div>
        <?php } ?>

        <?php echo form_open(null,array("name"=>"form1", "id"=>"form1", "enctype"=>"multipart/form-data", "class"=>"form-horizontal")); ?>
          <input type="hidden" id="edicion" name="edicion" value="<?php echo $empresa->edicion; ?>">
          <div class="row">
            <div class="col-sm-6">
              <fieldset class="border border-info mb-2 px-2">
                <legend class="h6 pl-1">Entorno del sistema</legend>

                <div class="form-group row mb-1">
                  <label for="soap" class="col-sm-3 col-form-label">SOAP Tipo*</label>
                  <div class="col-sm-5">
                    <select name="soap" id="soap" class="form-control form-control-sm" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                      <option value="01" <?php echo set_value_select($empresa,'soap','01',$empresa->tipo_soap) ?>>Demo</option>
                      <option value="02" <?php echo set_value_select($empresa,'soap','02',$empresa->tipo_soap) ?>>Produccion</option>
                    </select>
                  </div>
                </div>

                <div class="form-group row mb-1">
                  <label for="usuario" class="col-sm-3 col-form-label">SOAP Usuario</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" id="usuario" name="usuario" value="<?php echo $empresa->usuario_soap; ?>" placeholder="RUC + Usuario. Ej: 01234567890USUARIO" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>

                <div class="form-group row mb-1">
                  <label for="secundario" class="col-sm-3 col-form-label">SOAP Password</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" id="secundario" name="secundario" value="<?php echo $empresa->clave_soap; ?>" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>
              </fieldset>
            </div>

            <div class="col-sm-6">
              <fieldset class="border border-info mb-2 px-2">
                <legend class="h6 pl-1">Certificado</legend>

                <div class="form-group row mb-1">
                  <label for="soap" class="col-sm-3 col-form-label">Certificado</label>
                  <div class="col-sm-7">
                    <?php if ($empresa->certificado==''): ?>
                    <input type="file" id="certificado" name="certificado" value="" accept="application/pkcs12" ><!-- accept="application/x-pkcs12" -->
                    <?php else: ?>
                      <table width="100%">
                        <tr>
                          <td><h5><b><?php echo $empresa->certificado; ?></b></h5></td>
                          <td>
                            <?php if ($empresa->edicion==1): ?>
                            <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>empresa/certificadod','<?php echo "Desea borrar el certificado actual?"; ?>')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>
                            <?php endif ?>
                          </td>
                        </tr>
                      </table>
                    <?php endif ?>
                  </div>
                </div>

                <div class="form-group row mb-1">
                  <label for="clave" class="col-sm-3 col-form-label">Clave Certificado</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" id="clave" name="clave" value="<?php echo $empresa->certificado_clave; ?>" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>

                <div class="form-group row mb-1">
                  <label for="vencimiento" class="col-sm-3 col-form-label">Fecha Vencimiento</label>
                  <div class="col-sm-4">
                    <input type="date" class="form-control form-control-sm" id="vencimiento" name="vencimiento" value="<?php echo $empresa->certificado_vence; ?>" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-6">
              <fieldset class="border border-info mb-2 px-2">
                <legend class="h6 pl-1">Guías electrónicas</legend>

                <div class="form-group row mb-1">
                  <label for="idguia" class="col-sm-3 col-form-label">Client ID</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" id="idguia" name="idguia" value="<?php echo $empresa->id_gre; ?>" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>

                <div class="form-group row mb-1">
                  <label for="secretg" class="col-sm-3 col-form-label">Client Secret (Clave)</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control form-control-sm" id="secretg" name="secretg" value="<?php echo $empresa->secret_gre; ?>" <?php if ($empresa->edicion==0) {echo 'disabled';} ?>>
                  </div>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="form-group row">
            <label for="envio" class="col-sm-3 col-form-label">Envío de comprobantes automático</label>
            <div class="col-sm-2 mt-1">
              <input type="checkbox" name="envio" id="envio" value="1" <?php if (isset($empresa)) {echo set_value_check($empresa,'envio_automatico',$empresa->envio_automatico,1);} ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              *Realiza el envio inmediato de facturas, notas de credito facturas, anulaciones.<br>
              *De las anulaciones se tendra que realizar la consulta del estado manualmente (Envio-Consulta).
            </div>
          </div>

          <div class="form-group row">
            <label for="boleta" class="col-sm-3 col-form-label">Enviar boletas de forma individual</label>
            <div class="col-sm-2 mt-1">
              <input type="checkbox" name="boleta" id="boleta" value="1" <?php if (isset($empresa)) {echo set_value_check($empresa,'envio_boleta',$empresa->envio_boleta,1);} ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              *Realiza el envio inmediato de boletas, notas de credito boletas en forma individual.
            </div>
          </div>

          <div class="form-group row">
            <label for="guia" class="col-sm-3 col-form-label">Envío de guía de remisión automático</label>
            <div class="col-sm-2 mt-1">
              <input type="checkbox" name="guia" id="guia" value="1" <?php if (isset($empresa)) {echo set_value_check($empresa,'envio_guia',$empresa->envio_guia,1);} ?> data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Si" data-off-text="No">
            </div>

            <div class="col-sm-7 text-danger">
              *Realiza el envio inmediato de guias de remision siempre y cuando esten llenos los datos de envio.<br>
              *De las guias de remision se tendra que realizar la consulta del estado manualmente (Envio-Consulta).
            </div>
          </div>

          <div class="form-group mb-0 text-center">
            <button type="submit" class="btn btn-primary btn-sm">GUARDAR</button>
          </div>
        <?php echo form_close(); ?>
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



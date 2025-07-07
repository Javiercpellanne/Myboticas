<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Facturacion Electronica</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url();?>public/css/adminlte.min.css">
  <link rel="icon" href="<?php echo base_url(); ?>public/logo/favicon.ico">
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
</head>
<body class="hold-transition login-page">
  <div class="login-box" style="width: 800px;">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header">
        <h4 class="my-0">Buscar comprobante electrónico</h4>
      </div>
      <div class="card-body p-3">
        <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
          <div class="row">
            <div class="form-group col-sm-8 mb-2">
              <label for="tcomprobante" class="control-label">Tipo Documento *</label>
              <select name="tcomprobante" id="tcomprobante" class="form-control form-control-sm" required>
                <?php foreach ($comprobantec as $comprobante): ?>
                  <option value="<?php echo $comprobante->id ?>" <?php echo set_value_select($tcomprobante,'tcomprobante',$comprobante->id,$tcomprobante) ?>><?php echo $comprobante->descripcion ?></option>
                <?php endforeach ?>
              </select>
            </div>

            <div class="form-group col-sm-4 mb-2">
              <label for="femision" class="control-label">Fecha de emisión *</label>
              <input type="date" name="femision" id="femision" class="form-control form-control-sm" value="<?php echo $femision; ?>" required>
            </div>
          </div>

          <div class="row">
            <div class="form-group col-sm-6 mb-2">
              <label for="serie" class="control-label">Serie *</label>
              <input type="text" name="serie" id="serie" class="form-control form-control-sm" value="<?php echo $serie; ?>" required>
            </div>

            <div class="form-group col-sm-6 mb-2">
              <label for="numero" class="control-label">Número *</label>
              <input type="text" name="numero" id="numero" class="form-control form-control-sm" value="<?php echo $numero; ?>" required>
            </div>
          </div>

          <div class="row">
            <div class="form-group col-sm-6 mb-2">
              <label for="documento" class="control-label">Número Cliente (RUC/DNI/CE) *</label>
              <input type="text" name="documento" id="documento" class="form-control form-control-sm" value="<?php echo $documento; ?>" required>
            </div>

            <div class="form-group col-sm-6 mb-2">
              <label for="total" class="control-label">Monto total *</label>
              <input type="text" name="total" id="total" class="form-control form-control-sm" value="<?php echo $total; ?>" required>
            </div>
          </div>

          <div class="form-group col-sm-12 text-right">
            <input type="submit" class="btn btn-primary btn-sm" id="btsubmit" value="Buscar"/>
          </div>

          <?php if ($this->input->post()): ?>
            <?php if ($dato!=NULL): ?>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>Cliente</th>
                      <th>Número</th>
                      <th class="text-right">Total</th>
                      <th class="text-right">Descargas</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?php echo $dato->documento; ?></td>
                      <td><?php echo $dato->serie.'-'.$dato->numero; ?></td>
                      <td class="text-right"><?php echo $dato->total; ?></td>
                      <td class="text-right">
                        <?php if ($dato->has_xml==1): ?>
                        <a href="<?php echo base_url().'buscar/descarga/'.$dato->filename; ?>" class="btn btn-success btn-sm py-0">XML</a>
                        <?php endif ?>

                        <?php if ($dato->has_pdf==1): ?>
                        <a href="<?php echo base_url(); ?>downloads/pdf/<?php echo $dato->filename.'.pdf'; ?>" class="btn btn-primary btn-sm py-0" target="_blank">PDF</a>
                        <?php endif ?>

                        <?php if ($dato->has_cdr==1): ?>
                        <a href="<?php echo base_url(); ?>downloads/cdr/<?php echo 'R-'.$dato->filename.'.zip'; ?>" class="btn btn-info btn-sm py-0" target="_blank">CDR</a>
                        <?php endif ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <h4><b>No se encontro documento con los datos indicados</b></h4>
            <?php endif ?>
          <?php endif ?>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <script src="<?php echo base_url();?>public/js/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?php echo base_url();?>public/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo base_url();?>public/js/adminlte.min.js"></script>
</body>
</html>

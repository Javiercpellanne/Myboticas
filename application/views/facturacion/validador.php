<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Validador documentos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active">Validador documentos</li>
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
          <div class="card-header py-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>facturacion/validador">Documentos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>facturacion/validadorb">Resumenes y Anulaciones (Boletas)</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>facturacion/validadora">Anulaciones (Facturas)</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(null,array("class"=>"form-horizontal","name"=>"form1", "id"=>"form1", "autocomplete"=>"off")); ?>
              <div class="row">
                <div class="form-group col-sm-3 mb-2">
                  <label for="comprobante" class="control-label">Comprobante*</label>
                  <select name="comprobante" id="comprobante" class="form-control form-control-sm" onchange="vcomprobante(this.value,'<?php echo base_url(); ?>facturacion/busSerie')" required>
                    <?php foreach ($comprobantec as $comprobante): ?>
                      <option value="<?php echo $comprobante->id ?>"><?php echo $comprobante->descripcion ?></option>
                    <?php endforeach ?>
                    <?php foreach ($comprobanten as $comprobante): ?>
                      <option value="<?php echo $comprobante->id ?>"><?php echo $comprobante->descripcion ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group col-sm-2 mb-2">
                  <label for="serie" class="control-label">Serie*</label>
                  <select name="serie" id="serie" class="form-control form-control-sm" required>
                    <?php foreach ($nseries as $nserie): ?>
                    <option value="<?php echo $nserie->serie ?>"><?php echo $nserie->serie ?></option>
                    <?php endforeach ?>
                  </select>
                </div>

                <div class="form-group col-sm-2 mb-2">
                  <label for="inicio" class="control-label">Número inicial*</label>
                  <input name="inicio" type="text" id="inicio" value="" class="form-control form-control-sm" required/>
                </div>

                <div class="form-group col-sm-2 mb-2">
                  <label for="fin" class="control-label">Número final</label>
                  <input name="fin" type="text" id="fin" value="" class="form-control form-control-sm"/>
                </div>

                <div class="col-sm-2 offset-1 mb-2">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-check"></i> VALIDAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <?php echo form_open(base_url("facturacion/regularizar"),array("class"=>"form-horizontal","name"=>"fvalidador", "id"=>"fvalidador", "autocomplete"=>"off")); ?>
              <div class="table-responsive" style="height: 420px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th>#</th>
                      <th>Comprobante</th>
                      <th>F. Emisión</th>
                      <th>Cliente</th>
                      <th>Estado sistema</th>
                      <th>Estado Sunat</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if ($this->input->post()): ?>
                    <?php if ($listas!=NULL): ?>
                      <?php $i=1; foreach ($listas as $lista) { ?>
                        <tr>
                          <td><?php echo $i; ?></td>
                          <td><?php echo $lista['ncomprobante'].' '.$lista['serie'].'-'.$lista['numero']; ?></td>
                          <td><?php echo $lista['femision']; ?></td>
                          <td><?php echo $lista['cliente']; ?></td>
                          <td><h5 class="my-0"><span class="badge <?php echo $lista['badged']; ?>"><?php echo $lista['estadod']; ?></span></h5></td>
                          <td>
                            <h5 class="my-0"><span class="badge <?php echo $lista['badges']; ?>"><?php echo $lista['estados']; ?></span></h5>
                            <input type="hidden" id="id[]" name="id[]" value="<?php echo $lista['id']; ?>">
                            <input type="hidden" id="estadocp[]" name="estadocp[]" value="<?php echo $lista['estadocp']; ?>">
                          </td>
                        </tr>
                        <?php $i++; ?>
                      <?php } ?>
                    <?php else: ?>
                      No hay conexion
                    <?php endif ?>
                  <?php endif ?>
                  </tbody>
                </table>
              </div>

              <?php if ($listas!=NULL): ?>
                <div class="col-sm-12 text-center">
                  <input type="hidden" id="tipo" name="tipo" value="<?php echo $this->input->post('comprobante',true); ?>">
                  <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-pencil-alt"></i> Regularizar Documentos</button>
                </div>
              <?php endif ?>
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


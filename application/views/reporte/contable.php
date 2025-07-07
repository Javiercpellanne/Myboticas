<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Registros Contables</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Registros Contables</li>
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
          <div class="card-header py-2">
            <h3 class="card-title">Registros Compras</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/cexcel"),array("name"=>"formc", "id"=>"formc")); ?>
              <div class="form-group row mb-1">
                <label for="canuo" class="col-sm-1 control-label">AÑO</label>
                <div class="col-sm-3">
                  <select name="canuo" id="canuo" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                    <option value="<?php echo $anuo->descripcion ?>"><?php echo $anuo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="cmes" class="col-sm-1 control-label">MES</label>
                <div class="col-sm-3">
                  <select name="cmes" id="cmes" class="form-control form-control-sm" required>
                    <?php foreach ($meses as $mes) {?>
                    <option value="<?php echo $mes->id ?>"><?php echo $mes->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-file-excel"></i> DESCARGAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Registros Ventas</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/vexcel"),array("name"=>"formv", "id"=>"formv")); ?>
              <div class="form-group row mb-1">
                <label for="vanuo" class="col-sm-1 control-label">AÑO</label>
                <div class="col-sm-3">
                  <select name="vanuo" id="vanuo" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                    <option value="<?php echo $anuo->descripcion ?>"><?php echo $anuo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="vmes" class="col-sm-1 control-label">MES</label>
                <div class="col-sm-3">
                  <select name="vmes" id="vmes" class="form-control form-control-sm" required>
                    <?php foreach ($meses as $mes) {?>
                    <option value="<?php echo $mes->id ?>"><?php echo $mes->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-file-excel"></i> DESCARGAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <!-- <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Descarga Ventas (XML y CDR)</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/directorio"),array("name"=>"formd", "id"=>"formd")); ?>
              <div class="form-group row mb-1">
                <label for="danuo" class="col-sm-1 control-label">AÑO</label>
                <div class="col-sm-3">
                  <select name="danuo" id="danuo" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                    <option value="<?php echo $anuo->anuo ?>"><?php echo $anuo->anuo ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="dmes" class="col-sm-1 control-label">MES</label>
                <div class="col-sm-3">
                  <select name="dmes" id="dmes" class="form-control form-control-sm" required>
                    <?php foreach ($meses as $mes) {?>
                    <option value="<?php echo $mes->id ?>"><?php echo $mes->mes ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-file-archive"></i> DESCARGAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div> -->
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

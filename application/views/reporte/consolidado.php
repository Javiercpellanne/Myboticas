<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Reporte Consolidado</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Reporte Consolidado</li>
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
            <h3 class="card-title">Reporte Consolidado Compras</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/excelcompras"),array("name"=>"fcompra", "id"=>"fcompra")); ?>
              <div class="form-group row mb-0">
                <label for="cinicio" class="col-sm-1 control-label">Dede</label>
                <div class="col-sm-2">
                  <input name="cinicio" type="date" id="cinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="cfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="cfin" type="date" id="cfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2 text-right">
                  <button type="submit" class="btn bg-teal btn-sm"><i class="fa fa-file-excel"></i>  EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Reporte Consolidado CPE</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/excelventas"),array("name"=>"fcomprobante", "id"=>"fcomprobante")); ?>
              <div class="form-group row mb-0">
                <label for="vinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="vinicio" type="date" id="vinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="vfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="vfin" type="date" id="vfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn bg-teal btn-sm"><i class="fa fa-file-excel"></i> EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Reporte Consolidado Productos Ventas y Stock</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfvendidos"),array("name"=>"fvendidos", "id"=>"fvendidos","target"=>"_blank")); ?>
              <div class="form-group row mb-0">
                <label for="pinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="pinicio" type="date" id="pinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="pfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="pfin" type="date" id="pfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

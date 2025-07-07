<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Reporte Caja</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Reporte Caja</li>
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
          <div class="card-header">
            <h3 class="card-title">Reporte Cobros</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/excelcobros"),array("name"=>"fnota", "id"=>"fnota")); ?>
              <div class="form-group row mb-1">
                <label for="ginicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="ginicio" type="date" id="ginicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="gfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="gfin" type="date" id="gfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="gusuario" class="col-sm-1 control-label">Usuario</label>
                <div class="col-sm-3">
                  <select name="gusuario" id="gusuario" class="form-control form-control-sm">
                    <option value="">::Todos</option>
                    <?php foreach ($usuarios as $usuario) {?>
                    <option value="<?php echo $usuario->id ?>"><?php echo $usuario->nombres ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 text-center">
                  <button type="submit" class="btn bg-teal btn-sm"><i class="fa fa-file-excel"></i> EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Deudas por Proveedor</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfpagar"),array("name"=>"form1", "id"=>"form1","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="proveedor" class="col-sm-1 control-label">Proveedor</label>
                <input name="idproveedor" id="idproveedor" type="hidden" value="" required/>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input name="proveedor" type="text" id="proveedor" class="form-control form-control-sm" value="" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>proveedor/buscador','bdatos','Buscar Proveedor')"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                  </div>
                </div>

                <label for="pinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="pinicio" type="date" id="pinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="pfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="pfin" type="date" id="pfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Deudas por Cliente</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfcobrar"),array("name"=>"form1", "id"=>"form1","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="cliente" class="col-sm-1 control-label">Cliente</label>
                <input name="idcliente" id="idcliente" type="hidden" value="" required/>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input name="cliente" type="text" id="cliente" class="form-control form-control-sm" value="" placeholder="Nombre o Razon Social" onkeydown="return false" readonly>
                    <div class="input-group-append">
                      <button type="button" class="btn btn-success btn-sm" onclick="mostrarModal('<?php echo base_url(); ?>cliente/buscador','bdatos','Buscar Cliente')"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                  </div>
                </div>

                <label for="cinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="cinicio" type="date" id="cinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="cfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="cfin" type="date" id="cfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Reporte Montos Cobros por Medios de Pago</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfmedios"),array("name"=>"fcompra", "id"=>"fcompra","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="minicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="minicio" type="date" id="minicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="mfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="mfin" type="date" id="mfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <?php if ($this->session->userdata("tipo")=='admin'): ?>
                <label for="cusuario" class="col-sm-1 control-label">Usuario</label>
                <div class="col-sm-3">
                  <select name="cusuario" id="cusuario" class="form-control form-control-sm">
                    <option value="">::Todos</option>
                    <?php foreach ($usuarios as $usuario) {?>
                    <option value="<?php echo $usuario->id ?>"><?php echo $usuario->nombres ?></option>
                    <?php  }  ?>
                  </select>
                </div>
                <?php endif ?>

                <div class="col-sm-1">
                  <div class="form-check mt-1">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="detallado" id="detallado" value="1">Detallado
                    </label>
                  </div>
                </div>
              </div>

              <div class="col-sm-2 offset-5">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
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

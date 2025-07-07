<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Reporte Ventas</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Reporte Ventas</li>
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
            <h3 class="card-title">Reporte Ventas</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfventa"),array("name"=>"fventa", "id"=>"fventa","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <div class="col-sm-2">
                  <label for="cinicio" class="control-label my-0">Desde</label>
                  <input name="cinicio" type="date" id="cinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2">
                <label for="cfin" class="control-label my-0">Hasta</label>
                  <input name="cfin" type="date" id="cfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2">
                  <label for="ctipo" class="control-label my-0">Tipo</label>
                  <select name="ctipo" id="ctipo" class="form-control form-control-sm">
                    <option value="">::Todos</option>
                    <option value="N">Notas Ventas</option>
                    <?php if ($empresa->facturacion==1) { ?>
                    <option value="C">CPE</option>
                    <?php } ?>
                  </select>
                </div>

                <?php if ($this->session->userdata("tipo")=='admin'): ?>
                <div class="col-sm-3">
                  <label for="cusuario" class="control-label my-0">Usuario</label>
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

              <div class="form-group row mb-0">
                <div class="col-6 text-center">
                  <button type="button" class="btn btn-success btn-sm" id="pdfventa"><i class="fa fa-print"></i> IMPRIMIR PDF</button>
                </div>

                <div class="col-6 text-center">
                  <button type="button" class="btn bg-teal btn-sm" id="excelventa"><i class="fa fa-file-excel"></i> DESCARGAR EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Ventas por Productos</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfproductov"),array("name"=>"fproducto", "id"=>"fproducto","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <input name="idproducto" id="idproducto" type="hidden" value="" required="">
                <div class="col-sm-5">
                  <label for="descripcion" class="control-label my-0">Producto</label>
                  <div class="input-group">
                    <input name="descripcion" type="text" id="descripcion" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" onkeyup="productoNombre('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off" value="" required="">
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                  </div>

                  <div id="tbldescripcion" style="position:absolute; z-index: 1051; width: 98%; overflow: overlay; height:300px; display: none;">
                    <dl class="bg-buscador" id="grdescripcion">
                    </dl>
                  </div>
                </div>

                <div class="col-sm-2">
                  <label for="finicio" class="control-label my-0">Desde</label>
                  <input type="date" class="form-control form-control-sm" name="finicio" id="finicio" value="<?php echo date("Y-m-d"); ?>">
                </div>

                <div class="col-sm-2">
                  <label for="ffin" class="control-label my-0">Hasta</label>
                  <input type="date" class="form-control form-control-sm" name="ffin" id="ffin" value="<?php echo date("Y-m-d"); ?>">
                </div>

                <?php if ($this->session->userdata("tipo")=='admin'): ?>
                <div class="col-sm-3">
                  <label for="nusuario" class="control-label my-0">Usuario</label>
                  <select name="nusuario" id="nusuario" class="form-control form-control-sm">
                    <option value="">::Todos</option>
                    <?php foreach ($usuarios as $usuario) {?>
                    <option value="<?php echo $usuario->id ?>"><?php echo $usuario->nombres ?></option>
                    <?php  }  ?>
                  </select>
                </div>
                <?php endif ?>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Ventas por Cliente</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfcliente"),array("name"=>"form1", "id"=>"form1","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
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

                <label for="pinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="pinicio" type="date" id="pinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="pfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="pfin" type="date" id="pfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-1">
                  <div class="form-check mt-2">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="detalladop" id="detalladop" value="1">Detallado
                    </label>
                  </div>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Productos mas Vendidos</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfvendible"),array("name"=>"fproducto", "id"=>"fproducto","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <label for="vinicio" class="col-sm-2 control-label">Desde</label>
                <div class="col-sm-4">
                  <input name="vinicio" type="date" id="vinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="cfin" class="col-sm-2 control-label">Hasta</label>
                <div class="col-sm-4">
                  <input name="vfin" type="date" id="vfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Montos Ventas por Usuarios</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfusuario"),array("name"=>"fnota", "id"=>"fnota","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <label for="uinicio" class="col-sm-2 control-label">Desde</label>
                <div class="col-sm-4">
                  <input name="uinicio" type="date" id="uinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="ufin" class="col-sm-2 control-label">Hasta</label>
                <div class="col-sm-4">
                  <input name="ufin" type="date" id="ufin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Reporte Ventas CPE por Fecha y Hora</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfhoras"),array("name"=>"fcomprobantef", "id"=>"fcomprobantef","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <div class=" col-sm-2">
                  <label for="finicio" class="control-label">Desde</label>
                  <input name="finicio" type="date" id="finicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2">
                  <label for="ffin" class="control-label">Hasta</label>
                  <input name="ffin" type="date" id="ffin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-2">
                  <label for="hinicio" class="control-label">Hora Inicio</label>
                  <input name="hinicio" type="time" id="hinicio" class="form-control form-control-sm" value="<?php echo date("H:i:s"); ?>"/>
                </div>

                <div class="col-sm-2">
                  <label for="hfin" class="control-label">Hora Fin</label>
                  <input name="hfin" type="time" id="hfin" class="form-control form-control-sm" value="<?php echo date("H:i:s"); ?>"/>
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-sm-2 offset-5">
                  <button type="submit" class="btn btn-success"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Reporte Productos Vendidos Trimestral</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/excelmensual"),array("name"=>"fmensual", "id"=>"fmensual")); ?>
              <div class="form-group row">
                <label for="tanuos" class="col-sm-2 control-label">AÑO</label>
                <div class="col-sm-4">
                  <select name="tanuos" id="tanuos" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                    <option value="<?php echo $anuo->descripcion ?>"><?php echo $anuo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="tmeses" class="col-sm-2 control-label">MES</label>
                <div class="col-sm-4">
                  <select name="tmeses" id="tmeses" class="form-control form-control-sm" required>
                    <?php foreach ($meses as $mes) {?>
                    <option value="<?php echo $mes->id ?>"><?php echo $mes->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>
              </div>

              <div class="col-sm-12 text-center">
                <button type="submit" class="btn bg-teal btn-sm"><i class="fa fa-file-excel"></i> EXCEL</button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Montos Ventas Mensualizado</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfanual"),array("name"=>"fnota", "id"=>"fnota","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <label for="ganuos" class="col-sm-2 control-label">AÑO</label>
                <div class="col-sm-4">
                  <select name="ganuos" id="ganuos" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                    <option value="<?php echo $anuo->descripcion ?>"><?php echo $anuo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>
              </div>

              <div class="col-sm-12 text-center">
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

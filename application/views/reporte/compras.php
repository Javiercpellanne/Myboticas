<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Reporte Compras</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Reporte Compras</li>
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
            <h3 class="card-title">Reporte Compras</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfcompra"),array("name"=>"fcompra", "id"=>"fcompra","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <label for="cinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="cinicio" type="date" id="cinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="cfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="cfin" type="date" id="cfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-1">
                  <div class="form-check mt-2">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="detallado" id="detallado" value="1">Detallado
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-6 text-center">
                  <button type="button" class="btn btn-success btn-sm" id="pdfcompra"><i class="fa fa-print"></i> IMPRIMIR PDF</button>
                </div>

                <div class="col-6 text-center">
                  <button type="button" class="btn bg-teal btn-sm" id="excelcompra"><i class="fa fa-file-excel"></i> DESCARGAR EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Compras por Productos</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfproductoc"),array("name"=>"fproductoc", "id"=>"fproductoc","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
                <label for="descripcion" class="col-sm-1 control-label">Producto</label>
                <input name="idproducto" id="idproducto" type="hidden" value="" required="">
                <div class="col-sm-4">
                  <div class="input-group">
                    <input name="descripcion" type="text" id="descripcion" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" onkeyup="productoNombre('<?php echo base_url(); ?>producto/busProductos',this.value)" autocomplete="off" value="" required="">
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                  </div>

                  <div id="tbldescripcion" style="position:absolute; z-index: 1051; width: 98%; overflow: overlay; max-height:300px; display: none;">
                    <dl class="bg-buscador" id="grdescripcion">
                    </dl>
                  </div>
                </div>

                <label for="finicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control form-control-sm" name="finicio" id="finicio" value="<?php echo date("Y-m-d"); ?>">
                </div>

                <label for="ffin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input type="date" class="form-control form-control-sm" name="ffin" id="ffin" value="<?php echo date("Y-m-d"); ?>">
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-6 text-center">
                  <button type="button" class="btn btn-success btn-sm" id="pdfproductoc"><i class="fa fa-print"></i> IMPRIMIR PDF</button>
                </div>

                <div class="col-6 text-center">
                  <button type="button" class="btn bg-teal btn-sm" id="excelproductoc"><i class="fa fa-file-excel"></i> DESCARGAR EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header">
            <h3 class="card-title">Reporte Compras por Proveedor</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfproveedor"),array("name"=>"fproveedor", "id"=>"fproveedor","target"=>"_blank")); ?>
              <div class="form-group row mb-2">
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

                <div class="col-sm-1">
                  <div class="form-check mt-2">
                    <label class="form-check-label">
                      <input class="form-check-input" type="checkbox" name="detalladop" id="detalladop" value="1">Detallado
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group row mb-0">
                <div class="col-6 text-center">
                  <button type="button" class="btn btn-success btn-sm" id="pdfproveedor"><i class="fa fa-print"></i> IMPRIMIR PDF</button>
                </div>

                <div class="col-6 text-center">
                  <button type="button" class="btn bg-teal btn-sm" id="excelproveedor"><i class="fa fa-file-excel"></i> DESCARGAR EXCEL</button>
                </div>
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

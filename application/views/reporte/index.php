<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Reporte Producto</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Reporte</li>
          <li class="breadcrumb-item active">Reporte Producto</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-primary card-outline">
          <div class="card-header py-2">
            <h3 class="card-title">General Producto</h3>
          </div>
          <div class="card-body p-3">
            <a href="<?php echo base_url(); ?>reporte/pdfminimo" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> Minimo Stock PDF</a>

            <a href="<?php echo base_url(); ?>reporte/pdfstock" class="btn btn-secondary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> Stock Actual PDF</a>

            <a href="<?php echo base_url(); ?>reporte/excelstock" class="btn btn-success btn-sm"><i class="fa fa-file-excel"></i> Stock Actual Excel</a>

            <a href="<?php echo base_url(); ?>reporte/pdfpstock" class="btn btn-secondary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> Productos Lotes PDF</a>

            <a href="<?php echo base_url(); ?>reporte/excelpstock" class="btn btn-success btn-sm"><i class="fa fa-file-excel"></i> Productos Lotes Excel</a>

            <a href="<?php echo base_url(); ?>reporte/pdfcinventario" class="btn btn-secondary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> Corte Inventario PDF</a>

            <a href="<?php echo base_url(); ?>reporte/excelprecios" class="btn btn-info btn-sm"><i class="fa fa-file-excel"></i> Precios y Lotes Excel</a>
          </div>
        </div>

        <div class="card card-primary card-outline">
          <div class="card-header py-2">
            <h3 class="card-title">Reportes DIGEMID</h3>
          </div>
          <div class="card-body p-3">
            <a href="<?php echo base_url(); ?>reporte/exceldigemid" class="btn btn-info btn-sm"><i class="fa fa-file-excel"></i> Envio Precios Medicamentos</a>

            <a href="<?php echo base_url(); ?>reporte/excelescencial" class="btn btn-success btn-sm"><i class="fa fa-file-excel"></i> Medicamentos Esenciales Genéricos</a>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Consulta por Atributos</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfatributos"),array("name"=>"form1", "id"=>"form1","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="buscar" class="col-sm-1 control-label">BUSCAR </label>
                    <div class="col-sm-3">
                      <select name="buscar" id="buscar" class="form-control form-control-sm" onchange="atributos('<?php echo base_url(); ?>reporte/busFiltros',this.value)" required>
                        <option value="Cat">Categorias</option>
                        <option value="Lab">Laboratorios</option>
                        <option value="Pac">Principio Activo</option>
                        <option value="Ate">Accion Terapeutica</option>
                        <option value="Ubi">Ubicacion</option>
                      </select>
                    </div>

                    <div class="col-sm-4">
                      <select name="nombres" id="nombres" class="form-control form-control-sm" required>
                        <?php foreach ($categorias as $categoria): ?>
                          <option value="<?php echo $categoria->id ?>"><?php echo $categoria->descripcion ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>

                <div class="col-sm-2">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Consulta de Ingresos</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfingreso"),array("name"=>"fingreso", "id"=>"fingreso","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="motivoi" class="col-sm-1 control-label">Motivo</label>
                <div class="col-sm-4">
                  <select name="motivoi" id="motivoi" class="form-control form-control-sm" required>
                    <option value="">::Selec</option>
                    <?php foreach ($ingresos as $motivo) {?>
                      <option value="<?php echo $motivo->id.'-'.$motivo->descripcion ?>"><?php echo $motivo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="iinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="iinicio" type="date" id="iinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="ifin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="ifin" type="date" id="ifin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-1">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Consulta de Salidas</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfsalida"),array("name"=>"fsalida", "id"=>"fsalida","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="motivos" class="col-sm-1 control-label">Motivo </label>
                <div class="col-sm-4">
                  <select name="motivos" id="motivos" class="form-control form-control-sm" required>
                    <option value="">::Selec</option>
                    <?php foreach ($salidas as $motivo) {?>
                      <option value="<?php echo $motivo->id.'-'.$motivo->descripcion ?>"><?php echo $motivo->descripcion ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <label for="sinicio" class="col-sm-1 control-label">Desde</label>
                <div class="col-sm-2">
                  <input name="sinicio" type="date" id="sinicio" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <label for="sfin" class="col-sm-1 control-label">Hasta</label>
                <div class="col-sm-2">
                  <input name="sfin" type="date" id="sfin" class="form-control form-control-sm" value="<?php echo date("Y-m-d"); ?>" required/>
                </div>

                <div class="col-sm-1">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>

        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Consulta de Psicotropicos (Compras y Ventas)</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfpsicotropico"),array("name"=>"form3", "id"=>"form3","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="descripcion" class="col-sm-1 col-form-label">Producto</label>
                <input name="idproducto" id="idproducto" type="hidden" value="" required="">
                <div class="col-sm-5">
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

                <label for="fvencer" class="col-sm-1 col-form-label">Mes</label>
                <div class="col-sm-2">
                  <input type="month" name="minicia" id="minicia" class="form-control form-control-sm" value="<?php echo date('Y-m') ?>">
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-print"></i> IMPRIMIR</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Productos por Vencer</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/excelvencido"),array("name"=>"form3", "id"=>"form3")); ?>
              <div class="form-group row mb-1">
                <label for="fvencer" class="col-sm-3 col-form-label">Meses a Vencer </label>
                <div class="col-sm-4">
                  <select name="fvencer" id="fvencer" class="form-control form-control-sm" required>
                    <?php for ($i=1; $i < 13 ; $i++) { ?>
                      <option value="+<?php echo $i; ?> month"><?php echo $i; ?> Mes</option>
                    <?php } ?>
                  </select>
                </div>

                <div class="col-sm-4 text-center">
                  <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-file-excel"></i> EXCEL</button>
                </div>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="card card-outline card-primary">
          <div class="card-header py-2">
            <h3 class="card-title">Productos por Clasificacion</h3>
          </div>
          <div class="card-body p-3">
            <?php echo form_open(base_url("reporte/pdfclasificacion"),array("name"=>"form3", "id"=>"form3","target"=>"_blank")); ?>
              <div class="form-group row mb-1">
                <label for="fvencer" class="col-sm-3 col-form-label">Clasificacion</label>
                <div class="col-sm-4">
                  <select name="clasificacion" id="clasificacion" class="form-control form-control-sm" required>
                    <option value="1">Generico</option>
                    <option value="2">Marca</option>
                    <option value="0">Otros</option>
                  </select>
                </div>

                <div class="col-sm-4 text-center">
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

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
          <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>empresa">Generales</a></li>
          <?php if ($empresa->facturacion==1): ?>
          <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>empresa/facturacion">Facturacion</a></li>
          <?php endif ?>
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

        <?php echo form_open(null,array("name"=>"form1", "id"=>"form1", "enctype"=>"multipart/form-data", "class"=>"form-horizontal")); ?> <!-- , "onsubmit"=>"event.preventDefault();" -->
          <div class="form-group row mb-1">
            <label for="nombres" class="col-sm-2 col-form-label">Razon Social*</label>
            <div class="col-sm-7">
              <input type="text" name="nombres" id="nombres" class="form-control form-control-sm" value="<?php echo $empresa->nombres; ?>" required/>
            </div>

            <label for="ruc" class="col-sm-1 col-form-label">RUC*</label>
            <div class="col-sm-2">
              <input type="text" name="ruc" id="ruc" class="form-control form-control-sm" value="<?php echo $empresa->ruc; ?>" required/>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="ncomercial" class="col-sm-2 col-form-label">Nombre Comercial</label>
            <div class="col-sm-3">
              <input type="text" name="ncomercial" id="ncomercial" class="form-control form-control-sm" value="<?php echo $empresa->ncomercial; ?>"/>
            </div>

            <label for="detraccion" class="col-sm-2 col-form-label">N° Cuenta de detracción</label>
            <div class="col-sm-2">
              <input type="text" name="detraccion" id="detraccion" class="form-control form-control-sm" value="<?php echo $empresa->detraccion; ?>"/>
            </div>
          </div>

          <div class="form-group row mb-1">
            <div class="col-sm-3">
              <label for="logo">Logo</label> <br>
              <input type="file" id="logo" name="logo" value=""> <br>
              <span class="text-danger">Las medidas recomendables son 700x300</span>
            </div>

            <div class="col-sm-3">
              <img src="<?php echo $empresa->logo; ?>" class="rounded img-fluid" style="max-height: 180px;">
            </div>

            <div class="col-sm-3">
              <label for="lticket">Logo Ticket</label> <br>
              <input type="file" id="lticket" name="lticket" value=""> <br>
              <span class="text-danger">Las medidas recomendables son 700x300</span>
            </div>

            <div class="col-sm-3">
              <img src="<?php echo $empresa->lticket; ?>" class="rounded img-fluid" style="max-height: 180px;">
            </div>
          </div>

          <hr class="my-2">
          <div class="form-group row mb-1">
            <span class="col-sm-2">Listado Producto/Servicio</span>
            <div class="col-sm-4">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="producto" id="producto1" value="0" <?php echo set_value_check($empresa,'producto',$empresa->producto,0); ?>>
                <label class="form-check-label" for="producto1">
                  Maximo 100
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="producto" id="producto2" value="1" <?php echo set_value_check($empresa,'producto',$empresa->producto,1); ?>>
                <label class="form-check-label" for="producto2">
                  Todos item
                </label>
              </div>
            </div>

            <span class="col-sm-2">Tipo Descuento</span>
            <div class="col-sm-4">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="dscto" id="dscto1" value="0" <?php echo set_value_check($empresa,'dscto',$empresa->dscto,0); ?>>
                <label class="form-check-label" for="dscto1">
                  Porcentaje (%)
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="dscto" id="dscto2" value="1" <?php echo set_value_check($empresa,'dscto',$empresa->dscto,1); ?>>
                <label class="form-check-label" for="dscto2">
                  Monto (S./)
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="dscto" id="dscto3" value="2" <?php echo set_value_check($empresa,'dscto',$empresa->dscto,2); ?>>
                <label class="form-check-label" for="dscto3">
                  Sin Dscto
                </label>
              </div>
            </div>
          </div>

          <div class="form-group row mb-1">
            <span class="col-sm-2">Listado Ventas</span>
            <div class="col-sm-4">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="lventa" id="lventa1" value="0" <?php echo set_value_check($empresa,'lventa',$empresa->lventa,0); ?>>
                <label class="form-check-label" for="lventa1">
                  Ultimos 3
                </label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="lventa" id="lventa2" value="1" <?php echo set_value_check($empresa,'lventa',$empresa->lventa,1); ?>>
                <label class="form-check-label" for="lventa2">
                  Por fecha
                </label>
              </div>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="ticket" class="col-sm-2 col-form-label">Tamaño Ticket*</label>
            <div class="col-sm-2">
              <select name="ticket" id="ticket" class="form-control form-control-sm" required>
                <option value="" <?php echo set_value_select($empresa,'ticket','',$empresa->ticket) ?>>::Selec</option>
                <option value="80" <?php echo set_value_select($empresa,'ticket','80',$empresa->ticket) ?>>80 mm</option>
                <option value="58" <?php echo set_value_select($empresa,'ticket','58',$empresa->ticket) ?>>50 mm</option>
              </select>
            </div>

            <label for="pesencial" class="col-sm-3 col-form-label">Medicamentos esenciales genéricos (stock mínimo)</label>
            <div class="col-sm-2">
              <div class="input-group">
                <input type="text" class="form-control form-control-sm text-right" id="pesencial" name="pesencial" value="<?php echo $empresa->pesencial; ?>">
                <div class="input-group-append">
                  <span class="input-group-text text-sm py-0">%</span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row mb-1">
            <label for="pie" class="col-sm-2 control-label">Pie Impresion Nota Ventas</label>
            <div class="col-sm-4">
              <textarea id="pie" name="pie" class="form-control form-control-sm" rows="5"><?php echo set_value_input($empresa,'pie',$empresa->pie); ?></textarea>
            </div>

            <?php if ($empresa->facturacion==1): ?>
            <label for="piec" class="col-sm-2 control-label">Pie Impresion CPE</label>
            <div class="col-sm-4">
              <textarea id="piec" name="piec" class="form-control form-control-sm" rows="5"><?php echo set_value_input($empresa,'piec',$empresa->piec); ?></textarea>
            </div>
            <?php endif ?>
          </div>

          <div class="form-group mb-0">
            <button type="submit" class="btn btn-primary btn-sm float-right">GUARDAR</button>
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


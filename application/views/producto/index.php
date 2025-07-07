<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Productos <a href="<?php echo base_url(); ?>producto/productoi" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nuevo Producto</a> <!-- <button type="button"class="btn btn-warning btn-sm py-0 text-white" onclick="mostrarModal('<?php echo base_url(); ?>producto/precios','bdatos','Actualizar precio de los productos')"><i class="fa fa-upload"></i> Actualizar precios</button> --></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active">Producto</li>
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
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>producto">Activos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/inactivos">Inactivos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/deficit">Stock vs Cantidad</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/catalogo">Cambio Estado</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/gestores">Gestor Precios</a></li>
            </ul>
          </div>

          <div class="card-body p-3">
            <?php if ($empresa->producto==0): ?>
              <div class="form-group row mb-1">
                <label for="bproducto" class="col-sm-1 col-form-label">BUSCAR</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input name="bproducto" type="text" id="bproducto" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" value="" onkeyup="productoListado('<?php echo base_url(); ?>producto/busListado',this.value,1)" autocomplete="off" autofocus>
                    <div class="input-group-append">
                      <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                    </div>
                  </div>
                </div>
              </div>

              <?php if($this->session->flashdata('mensaje')!=''){ ?>
                <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <?php echo $this->session->flashdata('mensaje') ?>
                </div>
              <?php } ?>

              <div class="table-responsive" style="height: 460px;">
                <table class="table table-hover table-sm">
                  <thead class="thead-dark">
                    <tr>
                      <th width="3%">#</th>
                      <th width="38%">Descripcion</th>
                      <th width="25%">Laboratorio</th>
                      <th width="4%">Factor</th>
                      <th width="8%">R. Sanitario</th>
                      <th width="7%">P. Compra</th>
                      <th width="5%">Stock</th>
                      <th width="7%">P. Venta</th>
                      <th width="3%">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="tblproducto">
                    <?php $i=1; ?>
                    <?php foreach ($listas as $lista) { ?>
                      <?php $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id); ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $lista->descripcion; ?></td>
                        <td><?php echo $lista->nlaboratorio; ?></td>
                        <td><?php echo $lista->factor; ?></td>
                        <td><?php echo $lista->rsanitario; ?></td>
                        <td align="right"><?php echo $lista->pcompra; ?></td>
                        <td align="center">
                          <?php if ($canexos>1): ?>
                            <button type="button" class="btn btn-<?php echo $cantidad->stock<=$lista->mstock ? 'danger': 'success'; ?> btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/establecimiento/<?php echo $lista->id; ?>','bdatos','Stock Establecimientos')"><?php echo $cantidad->stock; ?></button>
                          <?php else: ?>
                          <h5 class="my-0"><span class="badge badge-<?php echo $cantidad->stock<=$lista->mstock ? 'danger': 'success'; ?>"><?php echo $cantidad->stock; ?></span></h5>
                          <?php endif ?>
                        </td>
                        <td align="right"><?php echo $empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa; ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo base_url(); ?>producto/productoi/<?php echo $lista->id; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>

                            <?php if ($lista->tipo=='B'): ?>
                            <button type="button" class="btn bg-teal btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/consulta/<?php echo $lista->id; ?>','bdatos','Impresion Codigo Barra')" data-toggle="tooltip" data-placement="bottom" title="Codigo Barra"><i class="fa fa-barcode"></i> </button>

                              <button type="button" class="btn bg-secondary btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/inventario/<?php echo $lista->id; ?>','bdatos','Actualizar Inventario')" data-toggle="tooltip" data-placement="bottom" title="Actualizar Inventario"><i class="fa fa-server"></i> </button>
                            <?php endif ?>

                            <?php if ($lista->lote==1): ?>
                              <button type="button" class="btn bg-purple btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/lotes/<?php echo $lista->id; ?>','bdatos','Datos Lotes')" data-toggle="tooltip" data-placement="bottom" title="Lotes"><i class="fa fa-cubes"></i> </button>
                            <?php endif ?>

                            <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/buscompras/<?php echo $lista->id; ?>','bdatos','Datos Ultimos Movimientos')" data-toggle="tooltip" data-placement="bottom" title="Ultimos Movimientos"><i class="fa fa-sync-alt"></i> </button>

                            <?php if ($lista->estado==1): ?>
                              <a href="<?php echo base_url(); ?>producto/deshabilitar/<?php echo $lista->id; ?>" class="btn btn-outline-danger btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Inactivar"><i class="fa fa-thumbs-down"></i></a>
                            <?php else: ?>
                              <a href="<?php echo base_url(); ?>producto/habilitar/<?php echo $lista->id; ?>" class="btn btn-outline-success btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Activar"><i class="fa fa-thumbs-up"></i></a>
                            <?php endif ?>
                          </div>
                        </td>
                      </tr>
                      <?php $i++; ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php else: ?>
              <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
                <thead>
                  <tr>
                    <th width="3%">#</th>
                    <th width="38%">Descripcion</th>
                    <th width="25%">Laboratorio</th>
                    <th width="4%">Factor</th>
                    <th width="8%">R. Sanitario</th>
                    <th width="7%">P. Compra</th>
                    <th width="5%">Stock</th>
                    <th width="7%">P. Venta</th>
                    <th width="3%">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($detalles as $lista) { ?>
                      <?php $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id); ?>
                      <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $lista->descripcion; ?></td>
                        <td><?php echo $lista->nlaboratorio; ?></td>
                        <td><?php echo $lista->factor; ?></td>
                        <td><?php echo $lista->rsanitario; ?></td>
                        <td align="right"><?php echo $lista->pcompra; ?></td>
                        <td align="center">
                          <?php if ($canexos>1): ?>
                            <button type="button" class="btn btn-<?php echo $cantidad->stock<=$lista->mstock ? 'danger': 'success'; ?> btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/establecimiento/<?php echo $lista->id; ?>','bdatos','Stock Establecimientos')"><?php echo $cantidad->stock; ?></button>
                          <?php else: ?>
                          <h5 class="my-0"><span class="badge badge-<?php echo $cantidad->stock<=$lista->mstock ? 'danger': 'success'; ?>"><?php echo $cantidad->stock; ?></span></h5>
                          <?php endif ?>
                        </td>
                        <td align="right"><?php echo $empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa; ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo base_url(); ?>producto/productoi/<?php echo $lista->id; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>

                            <?php if ($lista->tipo=='B'): ?>
                            <button type="button" class="btn bg-teal btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/consulta/<?php echo $lista->id; ?>','bdatos','Impresion Codigo Barra')" data-toggle="tooltip" data-placement="bottom" title="Codigo Barra"><i class="fa fa-barcode"></i> </button>

                              <button type="button" class="btn bg-secondary btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/inventario/<?php echo $lista->id; ?>','bdatos','Actualizar Inventario')" data-toggle="tooltip" data-placement="bottom" title="Actualizar Inventario"><i class="fa fa-server"></i> </button>
                            <?php endif ?>

                            <?php if ($lista->lote==1): ?>
                              <button type="button" class="btn bg-purple btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/lotes/<?php echo $lista->id; ?>','bdatos','Datos Lotes')" data-toggle="tooltip" data-placement="bottom" title="Lotes"><i class="fa fa-cubes"></i> </button>
                            <?php endif ?>

                            <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>producto/buscompras/<?php echo $lista->id; ?>','bdatos','Datos Ultimos Movimientos')" data-toggle="tooltip" data-placement="bottom" title="Ultimos Movimientos"><i class="fa fa-sync-alt"></i> </button>

                            <?php if ($lista->estado==1): ?>
                              <a href="<?php echo base_url(); ?>producto/deshabilitar/<?php echo $lista->id; ?>" class="btn btn-outline-danger btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Inactivar"><i class="fa fa-thumbs-down"></i></a>
                            <?php else: ?>
                              <a href="<?php echo base_url(); ?>producto/habilitar/<?php echo $lista->id; ?>" class="btn btn-outline-success btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Activar"><i class="fa fa-thumbs-up"></i></a>
                            <?php endif ?>
                          </div>
                        </td>
                      </tr>
                    <?php $i++; ?>
                  <?php } ?>
                </tbody>
              </table>
            <?php endif ?>
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
        <h5 class="modal-title" id="modalTitle">Datos Movimientos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body p-3">
        <div name="bdatos" id="bdatos">

        </div>
      </div>
    </div>
  </div>
</div>

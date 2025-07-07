<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Productos</h4>
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
              <li class="nav-item"><a class="nav-link py-1 border border-info" href="<?php echo base_url(); ?>producto">Activos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/inactivos">Inactivos</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/deficit">Stock vs Cantidad</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>producto/catalogo">Cambio Estado</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/gestores">Gestor Precios</a></li>
            </ul>
          </div>

          <div class="card-body p-3">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group row mb-1">
                  <label for="bproducto" class="col-sm-3 col-form-label">BUSCAR</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <input name="bproducto" type="text" id="bproducto" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" value="" onkeyup="activos('<?php echo base_url(); ?>producto/busActivos',this.value)" autocomplete="off" autofocus>
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="table-responsive" style="height: 460px;">
                  <table class="table table-hover table-sm border border-info">
                    <thead class="bg-success">
                      <tr>
                        <th width="90%">ACTIVOS (Muestra 100 ultimos) -- TOTAL : <span class="col-form-label font-weight-bold" id="contador1"><?php echo $contador1; ?></span></th>
                        <th width="10%"></th>
                      </tr>
                    </thead>
                    <tbody id="grcatalogo">
                      <?php $i=1; ?>
                      <?php foreach ($activos as $lista) { ?>
                        <?php
                        $nproducto=$lista->descripcion;
                        if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                        ?>
                        <tr id="<?php echo 'items'.$i; ?>">
                          <td><?php echo $nproducto; ?></td>
                          <td align="center"><a href="javascript:void(0)" class="btn btn-sm btn-warning py-0" onclick="asignarInactivo('<?php echo base_url(); ?>producto/inactivar','<?php echo $lista->id; ?>','<?php echo 'items'.$i; ?>')"><i class="fa fa-arrow-right"></i></a></td>
                        </tr>
                        <?php $i++; ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group row mb-1">
                  <label for="bproducta" class="col-sm-3 col-form-label">BUSCAR</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <input name="bproducta" type="text" id="bproducta" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" value="" onkeyup="inactivos('<?php echo base_url(); ?>producto/busInactivos',this.value)" autocomplete="off" autofocus>
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="table-responsive" style="height: 460px;">
                  <table class="table table-hover table-sm border border-info">
                    <thead class="bg-warning">
                      <tr>
                        <th width="10%"></th>
                        <th width="80%">INACTIVOS (Muestra 100 ultimos) -- TOTAL : <span class="col-form-label font-weight-bold" id="contador2"><?php echo $contador2; ?></span></th>
                        <th width="10%"></th>
                      </tr>
                    </thead>
                    <tbody id="grsistema">
                      <?php $j=1; ?>
                      <?php foreach ($inactivos as $lista) { ?>
                        <?php
                        $nproducto=$lista->descripcion;
                        if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                        ?>
                        <tr id="<?php echo 'itemi'.$j; ?>">
                          <td><a href="javascript:void(0)" class="btn btn-sm btn-success py-0" onclick="asignarActivo('<?php echo base_url(); ?>producto/activar','<?php echo $lista->id; ?>','<?php echo 'itemi'.$j; ?>')"><i class="fa fa-arrow-left"></i></a></td>
                          <td><?php echo $nproducto; ?></td>
                          <td><a href="javascript:void(0)" class="btn btn-sm btn-danger py-0" onclick="asignarRetiro('<?php echo base_url(); ?>producto/retirar','<?php echo $lista->id; ?>','<?php echo 'itemi'.$j; ?>')"><i class="fa fa-arrow-right"></i></a></td>
                        </tr>
                        <?php $j++; ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group row mb-1">
                  <label for="bproductr" class="col-sm-3 col-form-label">BUSCAR</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <input name="bproductr" type="text" id="bproductr" placeholder="Buscar Nombre del producto" class="form-control form-control-sm" value="" onkeyup="retirados('<?php echo base_url(); ?>producto/busRetirados',this.value)" autocomplete="off" autofocus>
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon1"><i class="fa fa-search"></i></span>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="table-responsive" style="height: 460px;">
                  <table class="table table-hover table-sm border border-info">
                    <thead class="bg-danger">
                      <tr>
                        <th width="10%"></th>
                        <th width="90%">RETIRADOS (Muestra 100 ultimos) -- TOTAL : <span class="col-form-label font-weight-bold" id="contador3"><?php echo $contador3; ?></span></th>
                      </tr>
                    </thead>
                    <tbody id="grprueba">
                      <?php $j=1; ?>
                      <?php foreach ($retirados as $lista) { ?>
                        <?php
                        $nproducto=$lista->descripcion;
                        if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                        ?>
                        <tr id="<?php echo 'itemr'.$j; ?>">
                          <td><a href="javascript:void(0)" class="btn btn-sm btn-success py-0" onclick="asignarActivo('<?php echo base_url(); ?>producto/activar','<?php echo $lista->id; ?>','<?php echo 'itemr'.$j; ?>')"><i class="fa fa-arrow-left"></i></a></td>
                          <td><?php echo $nproducto; ?></td>
                        </tr>
                        <?php $j++; ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
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


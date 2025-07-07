<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Stock vs Cantidad</h4>
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
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>producto/deficit">Stock vs Cantidad</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/catalogo">Cambio Estado</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/gestores">Gestor Precios</a></li>
            </ul>
          </div>
          <div class="card-body p-3">
            <table class="table table-hover table-striped table-bordered table-sm" id="sampleTable">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Producto</th>
                  <th>Laboratorio</th>
                  <th>Stock</th>
                  <th>Cantidad Lote</th>
                  <th>Diferencia</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista) { ?>
                  <?php
                  $lotes=$this->lote_model->stock($this->session->userdata("predeterminado"),$lista->id);
                  $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
                  ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->descripcion; ?></td>
                    <td><?php echo $lista->nlaboratorio; ?></td>
                    <td align="right"><?php echo $cantidad->stock; ?></td>
                    <td align="right"><?php echo $lotes->stock; ?></td>
                    <td><?php echo $cantidad->stock-$lotes->stock; ?></td>
                  </tr>
                <?php $i++; ?>
                <?php } ?>
              </tbody>
            </table>
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

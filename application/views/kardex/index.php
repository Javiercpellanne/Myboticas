<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Listado de Productos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><b class=" text-danger"><i class="fa fa-home"></i> <?php echo $nestablecimiento->descripcion; ?></b></li>
          <li class="breadcrumb-item">Kardex</li>
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
          <div class="card-body p-2">
            <form method="GET" action="<?php echo base_url('kardex/index'); ?>">
              <div class="form-group row mb-1">
                <label for="bproducto" class="col-sm-1 col-form-label">BUSCADOR</label>
                <div class="col-sm-4">
                  <input name="search" class="form-control form-control-sm mb-1" type="text" placeholder="Buscar nombre del producto o laboratorio"  value="<?php echo isset($search) ? $search : ''; ?>" autocomplete="off" autofocus>
                </div>
              </div>
            </form>

            <table class="table table-bordered table-hover table-sm mb-2">
              <thead class="thead-dark">
                <tr>
                  <th>#</th>
                  <th>Producto</th>
                  <th>Laboratorio</th>
                  <th>Stock</th>
                  <th>Lote</th>
                  <th>Diferencia</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($listas)): ?>
                  <?php foreach ($listas as $lista): ?>
                  <?php
                    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
                    $lotes=$this->lote_model->stock($this->session->userdata("predeterminado"),$lista->id);
                  ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->descripcion; ?></td>
                    <td><?php echo $lista->nlaboratorio; ?></td>
                    <td><?php echo $cantidad->stock; ?></td>
                    <td><?php echo $lotes->stock; ?></td>
                    <td><?php echo $lista->lote==1 ? $cantidad->stock-$lotes->stock: 0; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="<?php echo base_url(); ?>kardex/kardex/<?php echo $lista->id; ?>" class="btn btn-primary btn-sm py-0" title="Kardex Producto" data-toggle="tooltip" data-placement="bottom" target="_blank"><i class="fa fa-server"></i></a>

                        <?php if ($lista->lote==1): ?>
                        <a href="<?php echo base_url(); ?>kardex/kardexl/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0" title="Kardex Producto Lote" data-toggle="tooltip" data-placement="bottom" target="_blank"><i class="fa fa-cubes"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7">No se encontraron registros de productos</td>
                </tr>
                <?php endif ?>
              </tbody>
            </table>

            <nav aria-label="Page navigation">
              <?php echo $pagination; ?>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

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
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>producto/catalogo">Cambio Estado</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 active" href="<?php echo base_url(); ?>producto/gestores">Gestor Precios</a></li>
            </ul>
          </div>

          <div class="card-body p-3">
            <?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%;  font-size: .79rem">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th width="34%">Descripcion</th>
                  <th width="7%">Stock</th>
                  <th width="8%">P. Uni Compra</th>
                  <th width="8%">P. Uni Venta</th>
                  <th width="7%">Margen Unitario</th>
                  <th width="5%">Variacion</th>
                  <th width="8%">P. Cj Compra</th>
                  <th width="8%">P. Cj Venta</th>
                  <th width="7%">Margen Caja</th>
                  <th width="5%">Variacion</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                  <?php
                  // Paginación
                  $limit = 200; // Ajustar según la capacidad del servidor
                  $offset = 0;
                  do {
                  $datos = $this->inventario_model->mostrarStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"), "tipo" => 'B', "estado" => 1, "stock>"=>0), $limit, $offset);
                  if (count($datos) > 0) {
                      foreach ($datos as $dato) {
                        $nproducto = $dato->descripcion;
                        if ($dato->nlaboratorio != '') {
                            $nproducto .= ' [' . $dato->nlaboratorio . ']';
                        }
                        $pventa=$empresa->pestablecimiento==1 ? $dato->pventa2: $dato->pventa;
                        $venta=$empresa->pestablecimiento==1 ? $dato->venta2: $dato->venta;
                        $margenu=gananciav($pventa,$dato->pcompra,1);
                        $margenc=gananciav($venta,$dato->compra,1);

                        $variacionu=$empresa->gunidad>0 ? $margenu-$empresa->gunidad: '';
                        $variacionc=$empresa->gcaja>0 && $dato->factor>1 ? $margenc-$empresa->gcaja: '';
                  ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $nproducto; ?></td>
                    <td><?php echo $dato->stock; ?></td>
                    <td align="right"><?php echo $dato->pcompra; ?></td>
                    <td align="right" align="right"><?php echo $pventa; ?></td>
                    <td align="center"><?php echo $margenu.' %'; ?></td>
                    <td align="center" <?php echo $variacionu>0 ? 'class="text-success"': 'class="text-danger"'; ?>><b><?php echo $variacionu; ?></b></td>
                    <td align="right"><?php echo $dato->factor>1 ? $dato->compra: ''; ?></td>
                    <td align="right"><?php echo $dato->factor>1 ? $venta: ''; ?></td>
                    <td align="center"><?php echo $dato->factor>1 ? $margenc.' %': ''; ?></td>
                    <td align="center" <?php echo $variacionc>0 ? 'class="text-success"': 'class="text-danger"'; ?>><b><?php echo $variacionc; ?></b></td>
                  </tr>
                  <?php $i++; ?>
                  <?php
                        }
                          // Incrementar offset
                        $offset += $limit;
                      } else {
                          break;
                      }
                  } while (count($datos) > 0);
                  ?>
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

<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Stock Valorizado <a href="<?php echo base_url(); ?>consulta/pdfstockv" class="btn btn-secondary btn-sm py-0" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a> <a href="<?php echo base_url(); ?>consulta/excelstockv" class="btn btn-success btn-sm py-0 ml-2" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-file-excel"></i> EXCEL</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Consulta</li>
          <li class="breadcrumb-item active">Stock Valorizado</li>
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
          <div class="card-body p-3">
            <div class="table-responsive" style="height: 525px;">
              <table class="table table-hover table-sm">
                <thead class="thead-dark">
                  <tr>
                    <th width="5%">#</th>
                    <th width="45%">Producto</th>
                    <th width="6%">P. Venta</th>
                    <th width="6%">Costo Prom.</th>
                    <th width="8%">Cantidad</th>
                    <th width="8%">Total Ventas</th>
                    <th width="8%">Total Costo Prom.</th>
                    <th width="7%">Utilidad</th>
                    <th width="7%">Margen (%)</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; ?>
                  <?php foreach ($listas as $lista) { ?>
                    <?php
                    $nproducto=$lista->descripcion;
                    if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
                    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
                    $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa;

                    $cantidad=$lista->stock;
                    $venta=$cantidad*$pventa;

                    $kardex=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));
                    $costo=$kardex!=NULL ? round($kardex->saldov/$kardex->saldof,2) : $lista->pcompra;
                    $compra=$cantidad*$costo;
                    $utilidad=$venta-$compra;
                    $margen=gananciav($venta,$compra,1);
                    ?>
                    <tr>
                      <td><?php echo $i; ?></td>
                      <td><?php echo $nproducto; ?></td>
                      <td align="right"><?php echo formatoPrecio($pventa); ?></td>
                      <td align="right"><?php echo formatoPrecio($kardex!=NULL ? $costo : $lista->pcompra); ?></td>
                      <td align="center"><?php echo $cantidad; ?></td>
                      <td align="right"><?php echo formatoPrecio($venta); ?></td>
                      <td align="right"><?php echo formatoPrecio($compra); ?></td>
                      <td align="right"><?php echo formatoPrecio($utilidad); ?></td>
                      <td align="right"><?php echo formatoPrecio($margen); ?></td>
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


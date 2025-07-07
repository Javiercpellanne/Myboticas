<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Compra Mercaderia <a href="<?php echo base_url(); ?>compra/comprai" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nueva Compra</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active">Mercadria</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card card-outline card-primary">
          <div class="card-body p-3">
            <?php if($this->session->flashdata('mensaje')!=''){ ?>
              <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo $this->session->flashdata('mensaje') ?>
              </div>
            <?php } ?>

            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="inicio" class="col-sm-1 col-form-label">DESDE</label>
                <div class="col-sm-2">
                  <input name="inicio" type="date" id="inicio" class="form-control form-control-sm" value="<?php echo $inicio; ?>" required/>
                </div>

                <label for="fin" class="col-sm-1 col-form-label">HASTA</label>
                <div class="col-sm-2">
                  <input name="fin" type="date" id="fin" class="form-control form-control-sm" value="<?php echo $fin; ?>" required/>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th width="8%">Fecha</th>
                  <th width="8%">Documento</th>
                  <th width="10%">Numero</th>
                  <th width="37%">Proveedor</th>
                  <th width="8%">Importe</th>
                  <th width="7%">Estado</th>
                  <th width="8%">Estado Pago</th>
                  <th width="14%">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; ?>
                <?php foreach ($listas as $lista): ?>
                  <?php $pagado=$this->pago_model->montoTotal(array("idcompra"=>$lista->id)); ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $lista->femision; ?></td>
                    <td><?php echo $lista->ncomprobante; ?></td>
                    <td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
                    <td><?php echo $lista->proveedor; ?></td>
                    <td><?php echo $lista->total; ?></td>
                    <td>
                      <?php
                      if ($lista->nulo==1) {
                        echo '<h5 class="my-0"><span class="badge bg-danger">Anulado</span></h5>';
                      } else {
                        if ($lista->almacen==0) {
                          echo '<h5 class="my-0"><span class="badge bg-secondary">Registrado</span></h5>';
                        }else{
                          echo '<h5 class="my-0"><span class="badge bg-success">Procesado</span></h5>';
                        }
                      }
                      ?>
                    </td>
                    <td>
                      <?php if ($lista->nulo==0): ?>
                      <h5 class="my-0"><?php echo estadoPago($lista->cancelado); ?></h5>
                      <?php endif ?>
                    </td>
                    <td>
                      <div class="btn-group">
                        <?php if ($lista->nulo==0 && $lista->almacen==0): ?>
                          <a href="<?php echo base_url(); ?>compra/comprae/<?php echo $lista->id; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>

                          <a href="<?php echo base_url(); ?>compra/almacen/<?php echo $lista->id; ?>" class="btn btn-secondary btn-sm py-0" title="Ingreso Almacen" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-cubes"></i></a>
                        <?php endif ?>

                        <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>compra/consulta/<?php echo $lista->id; ?>','bdatos','Consulta de Documento')"><i class="fa fa-eye"></i></button>

                        <?php if ($lista->nulo==0): ?>
                          <button type="button" class="btn btn-primary btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>compra/pagos/<?php echo $lista->id; ?>','bdatos','Consulta de Pagos')" title="Pagos" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-money-bill-alt"></i></button>

                          <button type="button"class="btn btn-success btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>compra/opciones/<?php echo $lista->id; ?>','bdatos','Opciones Impresion')" title="Impresion" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-print"></i></button>

                          <a href="javascript:void(0)" onclick="borrar('<?php echo base_url(); ?>compra/compraa/<?php echo $lista->id; ?>','<?php echo "Desea anular la compra Nº ".$lista->id."?"; ?>')" class="btn btn-danger btn-sm py-0" title="Anular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a>
                        <?php endif ?>
                      </div>
                    </td>
                  </tr>
                  <?php $i++; ?>
                <?php endforeach ?>
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


<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Compras Gastos y Otros <a href="<?php echo base_url(); ?>gasto/gastoi" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nueva Compra</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Compra</li>
          <li class="breadcrumb-item active">Gastos y Otros</li>
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
          <!-- <div class="card-header py-2">
            <ul class="nav nav-pills">
              <li class="nav-item"><a class="nav-link py-1 active" href="<?php echo base_url(); ?>gasto">Facturas</a></li>
              <li class="nav-item"><a class="nav-link py-1 ml-1 border border-info" href="<?php echo base_url(); ?>gasto/ncredito">Notas Credito</a></li>
            </ul>
          </div> -->
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

            <?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%">
              <thead>
                <tr>
                  <th width="3%">#</th>
                  <th width="6%">Fecha</th>
                  <th width="8%">Documento</th>
                  <th width="9%">Numero</th>
                  <th width="36%">Proveedor</th>
                  <th width="5%">Moneda</th>
                  <th width="8%">Importe</th>
                  <th width="7%">Estado</th>
                  <th width="7%">Estado Pago</th>
                  <th width="12%">Acciones</th>
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
                    <td><?php echo $lista->moneda; ?></td>
                    <td align="right"><?php echo $lista->total; ?></td>
                    <td>
                      <?php
                      if ($lista->nulo==1) {
                        echo '<h5 class="my-0"><span class="badge bg-danger">Anulado</span></h5>';
                      } else {
                        echo '<h5 class="my-0"><span class="badge bg-success">Procesado</span></h5>';
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
                        <a href="<?php echo base_url(); ?>gasto/gastoi/<?php echo $lista->id; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>

                        <button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>gasto/consulta/<?php echo $lista->id; ?>','bdatos','Consulta de Documento')"><i class="fa fa-eye"></i></button>

                        <?php if ($lista->nulo==0): ?>
                          <button type="button" class="btn btn-primary btn-sm py-0" onclick="mostrarModal('<?php echo base_url(); ?>gasto/pagos/<?php echo $lista->id; ?>','bdatos','Consulta de Pagos')" title="Pagos" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-money-bill-alt"></i></button>

                          <!-- <?php $ncredito=$this->cnota_model->montoTotal(array('idcompra'=>$lista->id,'comprobante'=>'07')); ?>
                          <?php if ($ncredito->total==0): ?> -->
                          <a href="javascript:void(0)" onclick="anular('<?php echo base_url(); ?>gasto/gastoa/<?php echo $lista->id; ?>','<?php echo 'Compra Nº '.$lista->id; ?>','<?php echo base_url(); ?>gasto')" class="btn btn-danger btn-sm py-0" title="Anular" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-ban"></i></a>
                          <!-- <?php endif ?> -->

                          <!-- <?php if ($ncredito->total<$lista->total): ?>
                            <a href="<?php echo base_url(); ?>gasto/ncreditoi/<?php echo $lista->id; ?>" class="btn bg-fuchsia btn-sm py-0" title="Nota de Credito" data-toggle="tooltip" data-placement="bottom">NC</a>
                          <?php endif ?> -->
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


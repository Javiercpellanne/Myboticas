<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Comprobantes por rectificar</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> <b class="text-danger migaja"><?php echo $nestablecimiento->descripcion ?></b></li>
          <li class="breadcrumb-item">Facturacion</li>
          <li class="breadcrumb-item active" aria-current="page">Comprobantes por rectificar</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  	<div class="container-fluid">
    	<div class="row">
      		<div class="col-12">
        		<div class="card card-primary card-outline">
				  <div class="card-body p-3">
				    <?php if($this->session->flashdata('mensaje')!=''){?>
				      <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
				        <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
				        <?php echo $this->session->flashdata('mensaje') ?>
				      </div>
				    <?php } ?>

						<?php
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
            $escondido= strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false ? 'dt-responsive nowrap': '';
            ?>
            <table id="sampleTable" class="table table-striped table-bordered table-sm <?php echo $escondido; ?>" style="width:100%">
							<thead>
								<tr>
									<th>#</th>
									<th>Fecha</th>
									<th>Comprobante</th>
									<th>Numero</th>
									<th>Cliente</th>
									<th>Importe</th>
									<th>Descripcion</th>
									<th>Consulta CDR</th>
									<th>Enviar</th>
								</tr>
							</thead>
							<tbody>
								<?php $i=1; ?>
								<?php foreach ($listas as $lista): ?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $lista->femision; ?></td>
										<td><?php echo $lista->ncomprobante; ?></td>
										<td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
										<td><?php echo $lista->cliente; ?></td>
										<td><?php echo $lista->total; ?></td>
										<td><?php echo $lista->respuesta_rectificar; ?></td>
										<td>
											<a href="<?php echo base_url(); ?>facturacion/consultacdr/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
										</td>
										<td><a href="<?php echo base_url(); ?>facturacion/enviarFactura/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a></td>
									</tr>
									<?php $i++; ?>
								<?php endforeach ?>

								<?php foreach ($listasn as $lista): ?>
									<tr>
										<td><?php echo $i; ?></td>
										<td><?php echo $lista->femision; ?></td>
										<td><?php echo $lista->ncomprobante; ?></td>
										<td><?php echo $lista->serie.'-'.$lista->numero; ?></td>
										<td><?php echo $lista->cliente; ?></td>
										<td><?php echo $lista->total; ?></td>
										<td><?php echo $lista->respuesta_rectificar; ?></td>
										<td>
											<a href="<?php echo base_url(); ?>facturacion/consultancdr/<?php echo $lista->id; ?>" class="btn btn-info btn-sm py-0"><i class="fa fa-upload"></i></a>
										</td>
										<td><a href="<?php echo base_url(); ?>facturacion/enviarNota/<?php echo $lista->id; ?>" class="btn btn-primary btn-sm py-0"><i class="fa fa-upload"></i></a></td>
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
      <h5 class="modal-title" id="modalTitle">Datos del Cliente</h5>
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

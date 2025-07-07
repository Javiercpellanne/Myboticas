<div class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Bonificados <a href="<?php echo base_url(); ?>bonificacion/bonificacioni" class="btn btn-info btn-sm py-0"><i class="fa fa-plus"></i> Nuevas Bonificaciones</a></h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><i class="fa fa-home"></i> </li>
          <li class="breadcrumb-item">Almacen</li>
          <li class="breadcrumb-item active">Bonificados</li>
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
            <?php echo form_open(null,array("name"=>"form1", "id"=>"form1")); ?>
              <div class="form-group row mb-1">
                <label for="canuo" class="col-sm-1 col-form-label">AÑO</label>
                <div class="col-sm-2">
                  <select name="canuo" id="canuo" class="form-control form-control-sm" required>
                    <?php foreach ($anuos as $anuo) {?>
                      <option value="<?php echo $anuo->descripcion; ?>" <?php echo set_value_select($anua,'canuo',$anuo->descripcion,$anua) ?>><?php echo $anuo->descripcion; ?></option>
                    <?php  }  ?>
                  </select>
                </div>

                <div class="col-sm-2 offset-1">
                  <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-server"></i> MOSTRAR</button>
                </div>
              </div>
            <?php echo form_close(); ?>

            <table id="sampleTable" class="table table-striped table-bordered table-sm dt-responsive nowrap" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Mes</th>
                  <th>Cantidad Productos</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php $i=1; foreach ($listas as $lista) { ?>
                  <?php $nombre=$this->mes_model->mostrar($lista->mes); ?>
                  <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $nombre->descripcion; ?></td>
                    <td><?php echo $lista->cantidad; ?></td>
                    <td>
                      <div class="btn-group">
                        <a href="<?php echo base_url(); ?>bonificacion/bonificacioni/<?php echo $lista->anuo; ?>/<?php echo $lista->mes; ?>" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>

                        <a href="<?php echo base_url(); ?>bonificacion/bonificacionc/<?php echo $lista->anuo; ?>/<?php echo $lista->mes; ?>" class="btn btn-primary btn-sm py-0" title="Duplicar" data-toggle="tooltip" data-placement="bottom"><i class="fa fas fa-copy"></i></a>
                      </div>
                    </td>
                  </tr>
                <?php $i++; } ?>
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
        <h5 class="modal-title" id="modalTitle">Datos de la bonificacion</h5>
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

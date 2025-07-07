<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "enctype"=>"multipart/form-data")); ?>
  <div class="form-group row mb-1">
    <label for="archivo" class="col-sm-2 col-form-label">Archivo</label>
    <div class="col-sm-8">
      <input type="file" id="archivo" name="archivo" value="">
      <input type="hidden" id="enviar" name="fenvio" value="Si">
    </div>
  </div>

  <div class="form-group">
    <a href="<?php echo base_url(); ?>inventario/inventarioexcel">Descargar formato de ejemplo para importar <i class="fa fa-download"></i></a>
  </div>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

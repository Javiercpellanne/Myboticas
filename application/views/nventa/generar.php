<?php if($this->session->flashdata('mensaje')!=''){?>
  <div class="alert alert-<?php echo $this->session->flashdata('css') ?> alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <?php echo $this->session->flashdata('mensaje') ?>
  </div>
<?php } ?>

<iframe id="mostrarbarra" src="<?php echo $impresion; ?>" width="100%" height="500px"></iframe>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

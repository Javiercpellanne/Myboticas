<div class="form-group row mb-1">
  <label for="numero" class="col-sm-3 control-label">Cantidad Impresion</label>
  <div class="col-sm-2">
    <input type="text" class="form-control form-control-sm" id="numero" name="numero" value="1" required>
  </div>

  <div class="col-sm-5 text-center">
    <button type="button" class="btn btn-primary btn-sm ml-4" onclick="document.getElementById('mostrarbarra').src = '<?php echo base_url().'producto/pdfcodigobarra/'.$id.'/'; ?>'+document.getElementById('numero').value;">MOSTRAR</button>
  </div>
</div>
<hr class="my-2">
<iframe id="mostrarbarra" src="<?php echo base_url(); ?>producto/pdfcodigobarra/<?php echo $id; ?>" width="100%" height="500px"></iframe>

<div class="form-group row mb-0">
  <div class="col-sm-12 text-right">
    <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CERRAR</button>
  </div>
</div>

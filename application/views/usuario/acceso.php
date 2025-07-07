<?php echo form_open(null,array("class"=>"form-horizontal","name"=>"formdatos", "id"=>"formdatos", "onsubmit"=>"enviodatos('".base_url().$this->uri->uri_string()."');", "autocomplete"=>"off")); ?>
  <table class="table table-sm">
    <thead>
      <tr>
        <th>#</th>
        <th>Menu</th>
        <th>Submenu</th>
        <th>Acceso</th>
      </tr>
    </thead>
    <tbody>
      <?php $i=0; ?>
      <?php foreach ($datos as $dato): ?>
        <?php $i++; ?>
        <?php $accesom=$this->ausuario_model->mostrar(array("idacceso"=>$dato->id,"iduser"=>$id)) ?>
        <tr>
          <td><?php echo $i ?></td>
          <td><?php echo $dato->menu ?></td>
          <td></td>
          <td align="center">
            <input class="form-check-input" type="checkbox" name="menu[]" id="menu[]" value="<?php echo $dato->id ?>" <?php if ($accesom!=NULL) {echo "checked";} ?> onchange="accesocheck(this,'submenu<?php echo $dato->id ?>')">
          </td>
        </tr>
          <?php
          if ($empresa->facturacion==1) {
            $listas=$this->anivel_model->mostrarTotal($dato->id);
          } else {
            $listas=$this->anivel_model->mostrarLimite($dato->id);
          }
          ?>
          <?php foreach ($listas as $lista): ?>
            <?php if ($lista->submenu!='Traslados Internos'): ?>
              <?php $i++; ?>
              <?php $accesos=$this->anusuario_model->mostrar(array("idacceson"=>$lista->id,"iduser"=>$id)) ?>
              <tr>
                <td><?php echo $i ?></td>
                <td></td>
                <td><?php echo $lista->submenu ?></td>
                <td align="center">
                  <input class="form-check-input submenu<?php echo $dato->id ?>" type="checkbox" name="submenu[]" id="submenu[]" value="<?php echo $lista->id ?>" <?php if ($accesos!=NULL) {echo "checked";} ?>>
                </td>
              </tr>
            <?php else: ?>
              <?php if ($establecimientos>1): ?>
                <?php $i++; ?>
                <?php $accesos=$this->anusuario_model->mostrar(array("idacceson"=>$lista->id,"iduser"=>$id)) ?>
                <tr>
                  <td><?php echo $i ?></td>
                  <td></td>
                  <td><?php echo $lista->submenu ?></td>
                  <td align="center">
                    <input class="form-check-input submenu<?php echo $dato->id ?>" type="checkbox" name="submenu[]" id="submenu[]" value="<?php echo $lista->id ?>" <?php if ($accesos!=NULL) {echo "checked";} ?>>
                  </td>
                </tr>
              <?php endif ?>
            <?php endif ?>
          <?php endforeach ?>
      <?php endforeach ?>
    </tbody>
  </table>
  <hr>

  <div class="form-group row mb-0">
    <div class="col-sm-12 text-right">
      <button type="submit" class="btn btn-primary btn-sm ml-4">GUARDAR</button>
      <button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close">CANCELAR</button>
    </div>
  </div>
<?php echo form_close(); ?>

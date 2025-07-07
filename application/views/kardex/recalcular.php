<section class="content-header pb-1">
  <div class="container-fluid">
    <div class="row mb-0">
      <div class="col-sm-6">
        <h4 class="m-0 text-dark">Productos</h4>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><b class=" text-danger"><i class="fa fa-home"></i> <?php echo $nestablecimiento->descripcion; ?></b></li>
          <li class="breadcrumb-item">Kardex</li>
          <li class="breadcrumb-item active"><a href="<?php echo base_url(); ?>kardex">Producto</a></li>
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

            <table class="table table-bordered table-hover table-striped table-sm">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th width="10%">Fecha y hora</th>
                  <th width="40%">Tipo transacción</th>
                  <th width="10%">Número</th>
                  <th width="5%">Entrada</th>
                  <th width="5%">Salida</th>
                  <th width="5%">Saldo</th>
                  <th width="5%">Costo</th>
                  <th width="5%">Entrada</th>
                  <th width="5%">Salida</th>
                  <th width="5%">Saldo</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $iniciof=$inicial->saldof??0; $iniciov=$inicial->saldov??0;
                ?>
                <tr>
                  <td colspan="5" class="text-right font-weight-bold">Saldo anterior</td>
                  <td class="text-right font-weight-bold"><?php echo $iniciof; ?></td>
                  <td class="text-right font-weight-bold"></td>
                  <td colspan="2"></td>
                  <td class="text-right font-weight-bold"><?php echo $iniciov; ?></td>
                  <td></td>
                </tr>
                <?php foreach ($listas as $lista): ?>
                    <?php
                    if (substr($lista->documento,0,2)=='MV') {
                      $consulta=$this->movimiento_model->productoTotal(array("concat('MV-',v.id)"=>$lista->documento,"idproducto"=>$producto));
                    } elseif (substr($lista->documento,0,2)=='NV') {
                      $consulta=$this->nventa_model->productoTotal(array("concat(serie,'-',numero)"=>$lista->documento,"idproducto"=>$producto));
                    } elseif (substr($lista->documento,0,1)=='B' || substr($lista->documento,0,1)=='F') {
                      $consulta=$this->venta_model->productoTotal(array("concat(serie,'-',numero)"=>$lista->documento,"idproducto"=>$producto));
                    } else {
                      $iniciof=0; $iniciov=0;
                      $consulta=(object) array("calmacen"=>$lista->entradaf,"palmacen"=>$lista->costo);
                    }

                    if ($lista->salidaf!=NULL) {
                        $ingreso='';
                        $ingresov='';

                        $salida=$consulta->calmacen;
                        $saldof=$iniciof-$salida;
                        $costo=round($iniciov/$iniciof,4);
                        $salidav=$salida*$costo;
                        $saldov=round($iniciov-$salidav,4);

                        $datas=array("palmacen"=>$costo);
                        if (substr($lista->documento,0,2)=='MV') {
                            $actualizar=$this->movimientod_model->update($datas, $consulta->id);
                        } elseif (substr($lista->documento,0,2)=='NV') {
                              $actualizar=$this->nventad_model->update($datas,array("id"=>$consulta->id));
                        } elseif (substr($lista->documento,0,1)=='B' || substr($lista->documento,0,1)=='F') {
                              $actualizar=$this->ventad_model->update($datas,array("id"=>$consulta->id));
                        } else {
                        }

                        $data=array(
                            "costo"=>$costo,
                            "salidav"=>$salidav,
                            "saldov"=>$saldov,
                        );
                        $actualizar=$this->kardex_model->update($data,$lista->id);
                    } else {
                        $ingreso=$consulta->calmacen;;
                        $saldof=$iniciof+$ingreso;
                        $costo=$consulta->palmacen;
                        $ingresov=$ingreso*$costo;
                        $saldov=round($iniciov+$ingresov,4);

                        $salida='';
                        $salidav='';

                        $data=array(
                            "costo"=>$costo,
                            "entradav"=>$ingresov,
                            "saldov"=>$saldov,
                        );
                        $actualizar=$this->kardex_model->update($data,$lista->id);
                    }
                    ?>
                  <tr>
                    <td><?php echo $lista->id; ?></td>
                    <td><?php echo $lista->fregistro; ?></td>
                    <td><?php echo $lista->concepto; ?></td>
                    <td><?php echo $lista->documento; ?></td>
                    <td><?php echo $ingreso; ?></td>
                    <td><?php echo $salida; ?></td>
                    <td><?php echo $saldof; ?></td>
                    <td><?php echo $costo; ?></td>
                    <td><?php echo $ingresov; ?></td>
                    <td><?php echo $salidav; ?></td>
                    <td><?php echo $saldov; ?></td>
                  </tr>
                  <?php $iniciof=$saldof; $iniciov=$saldov; ?>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

?>

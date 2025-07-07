<div class="form-inline my-2">
  <img src="<?php echo base_url();?>public/logo/logo_sgfarma.png" class="img-fluid" id="logos">
</div>

<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">
    <?php $saccioni=''; if ($this->uri->segment(1)=='inicio'){$saccioni='active text-light';} ?>
    <li class="nav-item">
      <a href="<?php echo base_url(); ?>inicio" class="nav-link <?php echo $saccioni ?>">
        <i class="nav-icon fas fa-tachometer-alt"></i>
        <p>Inicio</p>
      </a>
    </li>

    <?php
    $empresa=$this->empresa_model->mostrar();
    $establecimientos=$this->establecimiento_model->contador();
    $menu=$this->ausuario_model->mostrarTotal($this->session->userdata("id"));
    $submenu=$this->anusuario_model->mostrarTotal($this->session->userdata("id"));
    ?>

    <?php if (in_array('venta',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccionz=''; $saccionv=''; $sacciong=''; $saccionl='';$saccionn=''; $saccionp='';
      if ($this->uri->segment(1)=='cotizacion' || $this->uri->segment(1)=='cliente' || $this->uri->segment(1)=='venta' || $this->uri->segment(1)=='nventa' || $this->uri->segment(1)=='despacho' || $this->uri->segment(1)=='pos'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='cotizacion'){$saccionz='active';}
      if ($this->uri->segment(1)=='venta'){$saccionv='active';}
      if ($this->uri->segment(1)=='nventa'){$saccionn='active';}
      if ($this->uri->segment(1)=='despacho'){$sacciong='active';}
      if ($this->uri->segment(1)=='cliente'){$saccionl='active';}
      if ($this->uri->segment(1)=='pos'){$saccionp='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc ?>">
        <a href="#" class="nav-link <?php echo $acciona ?>">
          <i class="nav-icon fas fa-credit-card"></i>
          <p>
            Venta
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('pos',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionp ?>" href="<?php echo base_url(); ?>pos">
              <i class="far fa-circle nav-icon"></i> <p>Punto de Venta</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('cotizacion',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionz ?>" href="<?php echo base_url(); ?>cotizacion">
              <i class="far fa-circle nav-icon"></i> <p>Cotizaciones de Venta</p>
            </a>
          </li>
          <?php endif ?>

          <?php if ($empresa->facturacion==1 && is_numeric($this->session->userdata('codigo'))): ?>
            <?php if (in_array('comprobante',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccionv ?>" href="<?php echo base_url(); ?>venta">
                <i class="far fa-circle nav-icon"></i> <p>Comprobante Electronico</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('despacho',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $sacciong ?>" href="<?php echo base_url(); ?>despacho">
                <i class="far fa-circle nav-icon"></i> <p>Guia de Remision</p>
              </a>
            </li>
            <?php endif ?>
          <?php endif ?>

          <?php if (in_array('nventa',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionn ?>" href="<?php echo base_url(); ?>nventa">
              <i class="far fa-circle nav-icon"></i> <p>Notas de Venta</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('cliente',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionl ?>" href="<?php echo base_url(); ?>cliente">
              <i class="far fa-circle nav-icon"></i> <p>Clientes</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if ($empresa->facturacion==1 && is_numeric($this->session->userdata('codigo'))): ?>
      <?php if (in_array('facturacion',$menu)): ?>
        <?php $accionc=''; $acciona='';  $saccionv=''; $sacciond=''; $saccionr=''; $sacciona=''; $saccionc=''; $saccionl='';
        if ($this->uri->segment(1)=='facturacion'){$accionc='menu-open'; $acciona='active text-light';}
        if ($this->uri->segment(1)=='facturacion' && $this->uri->segment(2)=='' || $this->uri->segment(2)=='resumenesi' || $this->uri->segment(2)=='anulacionesi' || $this->uri->segment(2)=='anulacionesb'){$saccionv='active';}
        if ($this->uri->segment(2)=='rectificaciones'){$sacciond='active';}
        if ($this->uri->segment(2)=='resumenes' || $this->uri->segment(2)=='pendienter'){$saccionr='active';}
        if ($this->uri->segment(2)=='anulaciones' || $this->uri->segment(2)=='pendientea'){$sacciona='active';}
        if ($this->uri->segment(2)=='consistencia'){$saccionc='active';}
        if ($this->uri->segment(2)=='validador' || $this->uri->segment(2)=='validadorb' || $this->uri->segment(2)=='validadora'){$saccionl='active';}
        ?>
        <li class="nav-item has-treeview <?php echo $accionc ?>">
          <a href="#" class="nav-link <?php echo $acciona ?>">
            <i class="nav-icon fas fa-file-signature"></i>
            <p>
              Facturacion
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <?php if (in_array('nenviado',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccionv ?>" href="<?php echo base_url(); ?>facturacion">
                <i class="far fa-circle nav-icon"></i> <p>Comprobante no enviado</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('rectificaciones',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $sacciond ?>" href="<?php echo base_url(); ?>facturacion/rectificaciones">
                <i class="far fa-circle nav-icon"></i> <p>Comprobante por rectificar</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('resumenes',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccionr ?>" href="<?php echo base_url(); ?>facturacion/resumenes">
                <i class="far fa-circle nav-icon"></i> <p>Resumenes</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('anulaciones',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $sacciona ?>" href="<?php echo base_url(); ?>facturacion/anulaciones">
                <i class="far fa-circle nav-icon"></i> <p>Anulaciones</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('consistencia',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccionc ?>" href="<?php echo base_url(); ?>facturacion/consistencia">
                <i class="far fa-circle nav-icon"></i> <p>Consistencia Documentos</p>
              </a>
            </li>
            <?php endif ?>

            <?php if (in_array('validador',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccionl ?>" href="<?php echo base_url(); ?>facturacion/validador">
                <i class="far fa-circle nav-icon"></i> <p>Validador Documentos</p>
              </a>
            </li>
            <?php endif ?>
          </ul>
        </li>
      <?php endif ?>
    <?php endif ?>

    <?php if (in_array('compra',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccions=''; $saccionc='';  $saccionp=''; $sacciong='';
      if ($this->uri->segment(1)=='solicitud' || $this->uri->segment(1)=='compra' || $this->uri->segment(1)=='gasto' || $this->uri->segment(1)=='proveedor'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='solicitud'){$saccions='active';}
      if ($this->uri->segment(1)=='compra'){$saccionc='active';}
      if ($this->uri->segment(1)=='gasto'){$sacciong='active';}
      if ($this->uri->segment(1)=='proveedor'){$saccionp='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-cart-plus"></i>
          <p>
            Compra
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('presupuesto',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccions ?>" href="<?php echo base_url(); ?>solicitud">
              <i class="far fa-circle nav-icon"></i> <p>Solicitudes de Compra</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('compra',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionc ?>" href="<?php echo base_url(); ?>compra">
              <i class="far fa-circle nav-icon"></i> <p>Compra - Mercaderia</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('gasto',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciong ?>" href="<?php echo base_url(); ?>gasto">
              <i class="far fa-circle nav-icon"></i> <p>Compra - Gastos y Otros</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('proveedor',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionp ?>" href="<?php echo base_url(); ?>proveedor">
              <i class="far fa-circle nav-icon"></i> <p>Proveedores</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if (in_array('caja',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccionj=''; $saccionv=''; $saccionc=''; $saccionp=''; $sacciond=''; $saccioni='';
      if ($this->uri->segment(1)=='caja' || $this->uri->segment(1)=='cobros' || $this->uri->segment(1)=='pagos' || $this->uri->segment(1)=='ingreso' || $this->uri->segment(1)=='egreso'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='caja' && $this->uri->segment(2)=='' || $this->uri->segment(2)=='arqueoi' || $this->uri->segment(2)=='arqueoc'){$saccionj='active';}
      if ($this->uri->segment(2)=='mpago'){$saccionv='active';}
      if ($this->uri->segment(1)=='cobros'){$saccionc='active';}
      if ($this->uri->segment(1)=='pagos'){$saccionp='active';}
      if ($this->uri->segment(1)=='egreso'){$sacciond='active';}
      if ($this->uri->segment(1)=='ingreso'){$saccioni='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-money-bill-alt"></i>
          <p>
            Caja
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('arqueo',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionj ?>" href="<?php echo base_url(); ?>caja">
              <i class="far fa-circle nav-icon"></i> <p>Arqueo Caja</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('cobros',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionc ?>" href="<?php echo base_url(); ?>cobros">
              <i class="far fa-circle nav-icon"></i> <p>Cobros</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('pagos',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionp ?>" href="<?php echo base_url(); ?>pagos">
              <i class="far fa-circle nav-icon"></i> <p>Pagos</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('egreso',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciond ?>" href="<?php echo base_url(); ?>egreso">
              <i class="far fa-circle nav-icon"></i> <p>Egresos</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('ingreso',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccioni ?>" href="<?php echo base_url(); ?>ingreso">
              <i class="far fa-circle nav-icon"></i> <p>Ingresos</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('mediopago',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionv ?>" href="<?php echo base_url(); ?>caja/mpago">
              <i class="far fa-circle nav-icon"></i> <p>Medio Pago</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if (in_array('reporte',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccionc=''; $saccionv=''; $saccionp=''; $saccionj=''; $sacciont=''; $sacciond='';
      if ($this->uri->segment(1)=='reporte'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='reporte' && $this->uri->segment(2)==''){$saccionp='active';}
      if ($this->uri->segment(2)=='ventas'){$saccionv='active';}
      if ($this->uri->segment(2)=='compras'){$saccionc='active';}
      if ($this->uri->segment(2)=='caja'){$saccionj='active';}
      if ($this->uri->segment(2)=='contable'){$sacciont='active';}
      if ($this->uri->segment(2)=='consolidado'){$sacciond='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-file"></i>
          <p>
            Reportes
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('rproducto',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionp ?>" href="<?php echo base_url(); ?>reporte">
              <i class="far fa-circle nav-icon"></i> <p>Producto</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('rventa',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionv ?>" href="<?php echo base_url(); ?>reporte/ventas">
              <i class="far fa-circle nav-icon"></i> <p>Ventas</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('rcompra',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionc ?>" href="<?php echo base_url(); ?>reporte/compras">
              <i class="far fa-circle nav-icon"></i> <p>Compras</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('rcaja',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionj ?>" href="<?php echo base_url(); ?>reporte/caja">
              <i class="far fa-circle nav-icon"></i> <p>Caja</p>
            </a>
          </li>
          <?php endif ?>

          <?php if ($empresa->facturacion==1 && is_numeric($this->session->userdata('codigo'))): ?>
            <?php if (in_array('rregistro',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $sacciont ?>" href="<?php echo base_url(); ?>reporte/contable">
                <i class="far fa-circle nav-icon"></i> <p>Registros Contables</p>
              </a>
            </li>
            <?php endif ?>
          <?php endif ?>

          <?php if ($establecimientos>1): ?>
            <?php if (in_array('rconsolidado',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $sacciond ?>" href="<?php echo base_url(); ?>reporte/consolidado">
                <i class="far fa-circle nav-icon"></i> <p>Consolidado</p>
              </a>
            </li>
            <?php endif ?>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if (in_array('consulta',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccionv=''; $saccions=''; $saccionk=''; $saccionh=''; $saccionb='';
      if ($this->uri->segment(1)=='consulta'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='consulta' && $this->uri->segment(2)=='' || $this->uri->segment(2)=='ventau'){$saccionv='active';}
      if ($this->uri->segment(2)=='stockv'){$saccions='active';}
      if ($this->uri->segment(2)=='kardex' || $this->uri->segment(2)=='producto' || $this->uri->segment(2)=='lote'){$saccionk='active';}
      if ($this->uri->segment(2)=='vhorario'){$saccionh='active';}
      if ($this->uri->segment(2)=='bvendedor'){$saccionb='active';}
      if ($this->uri->segment(2)=='pclasificacion'){$saccionc='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-search"></i>
          <p>
            Consultas
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('vvalorizada',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionv ?>" href="<?php echo base_url(); ?>consulta">
              <i class="far fa-circle nav-icon"></i> <p>Ventas Valorizado</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('svalorizado',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccions ?>" href="<?php echo base_url(); ?>consulta/stockv">
              <i class="far fa-circle nav-icon"></i> <p>Stock Valorizado</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('kardex',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionk ?>" href="<?php echo base_url(); ?>consulta/kardex">
              <i class="far fa-circle nav-icon"></i> <p>Kardex</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('vhorario',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionh ?>" href="<?php echo base_url(); ?>consulta/vhorario">
              <i class="far fa-circle nav-icon"></i> <p>Ventas Horario</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('bvendedor',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionb ?>" href="<?php echo base_url(); ?>consulta/bvendedor">
              <i class="far fa-circle nav-icon"></i> <p>Bonos Vendedor</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('pclasificacion',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionc; ?>" href="<?php echo base_url(); ?>consulta/pclasificacion">
              <i class="far fa-circle nav-icon"></i> <p>Producto Clasificacion</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if (in_array('almacen',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccionm=''; $saccionj=''; $saccioni=''; $saccionb=''; $sacciont=''; $saccionp=''; $saccions='';
      if ($this->uri->segment(1)=='atributo' || $this->uri->segment(1)=='producto' || $this->uri->segment(1)=='servicio' || $this->uri->segment(1)=='bonificacion' || $this->uri->segment(1)=='movimiento' || $this->uri->segment(1)=='inventario' || $this->uri->segment(1)=='traslado'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='movimiento'){$saccionm='active';}
      if ($this->uri->segment(1)=='inventario'){$saccionj='active';}
      if ($this->uri->segment(1)=='traslado'){$saccioni='active';}
      if ($this->uri->segment(1)=='bonificacion'){$saccionb='active';}
      if ($this->uri->segment(1)=='servicio'){$saccions='active';}
      if ($this->uri->segment(1)=='producto'){$sacciont='active';}
      if ($this->uri->segment(1)=='atributo'){$saccionp='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-cubes"></i>
          <p>
            Almacen
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('inventario',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionj ?>" href="<?php echo base_url(); ?>inventario">
              <i class="far fa-circle nav-icon"></i> <p>Ajustes Inventario</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('movimientoa',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionm ?>" href="<?php echo base_url(); ?>movimiento">
              <i class="far fa-circle nav-icon"></i> <p>Movimientos</p>
            </a>
          </li>
          <?php endif ?>

          <?php if ($establecimientos>1): ?>
            <?php if (in_array('traslado',$submenu)): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo $saccioni ?>" href="<?php echo base_url(); ?>traslado">
                <i class="far fa-circle nav-icon"></i> <p>Traslados Internos</p>
              </a>
            </li>
            <?php endif ?>
          <?php endif ?>

          <?php if (in_array('bonificacion',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionb ?>" href="<?php echo base_url(); ?>bonificacion">
              <i class="far fa-circle nav-icon"></i> <p>Productos con Bono</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('servicio',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccions ?>" href="<?php echo base_url(); ?>servicio">
              <i class="far fa-circle nav-icon"></i> <p>Servicios</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('producto',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciont ?>" href="<?php echo base_url(); ?>producto">
              <i class="far fa-circle nav-icon"></i> <p>Productos</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('atributo',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionp ?>" href="<?php echo base_url(); ?>atributo">
              <i class="far fa-circle nav-icon"></i> <p>Atributos del Producto</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>

    <?php if (in_array('configuracion',$menu)): ?>
     <?php
      $accionc=''; $acciona=''; $saccions='';  $sacciong=''; $sacciont=''; $saccionm=''; $saccionl=''; $sacciond='';
      if ($this->uri->segment(1)=='empresa'  || $this->uri->segment(1)=='usuario' || $this->uri->segment(1)=='establecimiento' || $this->uri->segment(1)=='serie' || $this->uri->segment(1)=='utilitario' || $this->uri->segment(1)=='punto' || $this->uri->segment(1)=='transporte'){$accionc='menu-open'; $acciona='active text-light';}
      if ($this->uri->segment(1)=='empresa'){$saccions='active';}
      if ($this->uri->segment(1)=='usuario'){$sacciong='active';}
      if ($this->uri->segment(1)=='establecimiento' || $this->uri->segment(1)=='serie'){$sacciont='active';}
      if ($this->uri->segment(1)=='utilitario'){$saccionm='active';}
      if ($this->uri->segment(1)=='punto'){$saccionl='active';}
      if ($this->uri->segment(1)=='transporte'){$sacciond='active';}
      ?>
      <li class="nav-item has-treeview <?php echo $accionc; ?>">
        <a href="#" class="nav-link <?php echo $acciona; ?>">
          <i class="nav-icon fas fa-cogs"></i>
          <p>
            Configuracion
            <i class="fas fa-angle-left right"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
          <?php if (in_array('usuario',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciong ?>" href="<?php echo base_url(); ?>usuario">
              <i class="far fa-circle nav-icon"></i> <p>Usuario</p>
            </a>
          </li>
          <?php endif ?>

          <?php if ($empresa->facturacion==1 && is_numeric($this->session->userdata('codigo'))): ?>
          <?php if (in_array('transporte',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciond ?>" href="<?php echo base_url() ?>transporte">
              <i class="far fa-circle nav-icon"></i><p>Transportes</p>
            </a>
          </li>
          <?php endif ?>
          <?php endif ?>

          <?php if (in_array('punto',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionl ?>" href="<?php echo base_url() ?>punto">
              <i class="far fa-circle nav-icon"></i><p>Puntos Acumulables</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('establecimiento',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $sacciont ?>" href="<?php echo base_url(); ?>establecimiento">
            <i class="far fa-circle nav-icon"></i> <p>Establecimiento & Serie</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('empresa',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccions ?>" href="<?php echo base_url(); ?>empresa">
              <i class="far fa-circle nav-icon"></i> <p>Empresa</p>
            </a>
          </li>
          <?php endif ?>

          <?php if (in_array('utilitario',$submenu)): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo $saccionm ?>" href="<?php echo base_url(); ?>utilitario">
              <i class="far fa-circle nav-icon"></i> <p>Utilitarios</p>
            </a>
          </li>
          <?php endif ?>
        </ul>
      </li>
    <?php endif ?>
  </ul>
</nav>

<div class="user-panel py-2 mb-2 d-flex border-top border-secondary">
  <div class="image">
    <img src="<?php echo base_url();?>public/logo/whatsapp.png" class="img-circle elevation-2" alt="User Image">
  </div>
  <div class="info">
    <a href="https://api.whatsapp.com/send?phone=967178743&text=Buenos%20dias%20mi%20consulta%20es" target="_blank" class="d-block">Soporte Tecnico</a>
  </div>
</div>

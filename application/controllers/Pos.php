<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata('login')){redirect(base_url().'login');}
    if (!$this->acciones(47)){redirect(base_url()."inicio");}

    $this->layout->setLayout('contraido');
    $this->load->model('tcomprobante_model');
    $this->load->model('serie_model');
    $this->load->model('tidentidad_model');
    $this->load->model('cliente_model');
    $this->load->model('tpago_model');
    $this->load->model("categoria_model");
    $this->load->model('producto_model');
    $this->load->model('lote_model');
    $this->load->model('kardex_model');
    $this->load->model("kardexl_model");
    $this->load->model("nventa_model");
    $this->load->model("nventad_model");
    $this->load->model("cobro_model");
    $this->load->model('venta_model');
    $this->load->model('ventad_model');
    $this->load->model('cobroe_model');
    $this->load->model("arqueo_model");

    $this->load->model("punto_model");
    $this->load->model("clientep_model");

    $this->load->library("mytcpdf");
    $this->load->library("generadorXML");
    $this->load->library("firmarXML");
    $this->load->library("apiFacturacion");
    $this->generadoXML = new GeneradorXML();
    $this->firmadoXML = new FirmarXML();
    $this->apiFacturacion = new ApiFacturacion();
  }

  public function index()
  {
    $controlip=$this->controlip('pos');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
    if ($arqueoc==0) {redirect(base_url().'venta');}
    $comprobantes=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
    $nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tcomprobante"=>"99"));
    $categorias=$this->categoria_model->mostrarTotal('F');
    $productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
    $mpagos=$this->tpago_model->mostrarTotal();
    $this->layout->setTitle('Punto Venta');
    $this->layout->view('index',compact('empresa',"anexos","nestablecimiento",'categorias','mpagos','productos','comprobantes','nserie'));
  }

  public function guardar()
  {
    if ($this->input->post())
    {
      $impresion='';
      if ($this->input->post('idproducto',true)==null) {
        $mensaje='No envio productos en la venta!';
      } else {
        $pagado=round(array_sum($this->input->post('montos',true)),2);
        if ($pagado!=floatval($this->input->post('totalg',true))) {
          $mensaje='El monto cobrado es diferente al comprobante';
        }else{
          if ($this->input->post('comprobante',true)==99) {
            $datos=$this->nventa();
          } else {
            $datos=$this->comprobante();
          }
        }
      }

      echo json_encode($datos);
      exit();
    }
  }

  public function nventa()
  {
    $empresa=$this->empresa_model->mostrar();
    $numero=$this->nventa_model->maximo($this->input->post('serie',true));
    $ninicio= $numero==null ? '' : $numero->numero;
    $numeracion=$ninicio+1;

    $consulta=$this->nventa_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
    if ($consulta==0) {
      $data=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        'iduser'            =>$this->session->userdata('id'),
        'femision'          =>date("Y-m-d"),
        'hemision'          =>date('H:i:s'),
        'fvencimiento'      =>date("Y-m-d"),
        'serie'             =>$this->input->post('serie',true),
        'numero'            =>$numeracion,
        'formato'           =>valor_check($this->input->post('formato',true)),
        'idcliente'         =>$this->input->post('idcliente',true),
        'cliente'           =>$this->input->post('cliente',true),
        'total'             =>$this->input->post('totalg',true),
        'izipay'            =>valor_fecha($this->input->post('mizipay',true)),
        'lote'              =>valor_check($this->input->post('impresion',true)),
        //'dadicional'        =>$this->input->post('dadicional',true),
        'condicion'         =>1,
        'cancelado'         =>1,
        // 'efectivo'          =>valor_fecha($this->input->post('efectivo',true)),
        // 'vuelto'            =>valor_fecha($this->input->post('vuelto',true)),
        'idvendedor'        =>$this->session->userdata('id'),
      );
      $insertar=$this->nventa_model->insert($data);

      for ($i=0; $i < count($this->input->post('medios',true)) ; $i++) {
        $datap=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          'iduser'            =>$this->session->userdata('id'),
          'idnventa'          =>$insertar,
          'femision'          =>date("Y-m-d"),
          'total'             =>$this->input->post('montos',true)[$i],
          'idtpago'           =>$this->input->post('medios',true)[$i],
          'documento'         =>$this->input->post("referencia",true)[$i],
        );
        $insertarp=$this->cobro_model->insert($datap);
      }

      for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
        if ($this->input->post('tipo',true)[$i]=='B') {
          $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
          $inicalf=$saldos==null ? 0: $saldos->saldof;
          $inicalv=$saldos==null ? 0: $saldos->saldov;
          //costos promedio
          $costo=$inicalf==0 ? 0 : round($inicalv/$inicalf,4);
          $salidav=$this->input->post("almacenc",true)[$i]*$costo;

          $saldof=$inicalf-$this->input->post("almacenc",true)[$i];
          $saldov=$inicalv-$salidav;
          $datak=array
          (
            "idestablecimiento" =>$this->session->userdata("predeterminado"),
            "iduser"            =>$this->session->userdata('id'),
            "fecha"             =>date("Y-m-d"),
            "idtmovimiento"     =>1,
            "concepto"          =>"Nota de venta",
            "idproducto"        =>$this->input->post("idproducto",true)[$i],
            "descripcion"       =>trim($this->input->post("descripcion",true)[$i]),
            "salidaf"           =>$this->input->post("almacenc",true)[$i],
            "saldof"            =>$saldof,
            "costo"             =>$costo,
            "salidav"           =>$salidav,
            "saldov"            =>$saldov,
            "documento"         =>$this->input->post("serie",true)."-".$numeracion,
          );
          $insertark=$this->kardex_model->insert($datak);

          $datas=array('stock'=>$saldof);
          $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));

          $nlotes="";
          $clotes="";
          $flotes="";
          if ($this->input->post("lote",true)[$i]==1) {
            $cantidad=$this->input->post("almacenc",true)[$i];
            $clotes=array();
            $flotes=array();
            $nlotes=array();

            for ($l=0; $l < 3 ; $l++) {
              $consultal=$this->lote_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i]);
              if ($cantidad>=$consultal->stock) {
                $elimnarl=$this->lote_model->delete(array("idestablecimiento" =>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$consultal->nlote));
                $inicialf=$consultal->stock;
              } else {
                $datal=array('stock'=>$consultal->stock-$cantidad);
                $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
                $inicialf=$cantidad;
              }

              $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
              $inicial=$saldos==null ? 0: $saldos->saldof;
              $saldosl=$inicial-$inicialf;
              $datac=array
              (
                'idestablecimiento' =>$this->session->userdata("predeterminado"),
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date('Y-m-d'),
                'idtmovimiento'     =>1,
                'concepto'          =>'Nota de venta',
                'idproducto'        =>$this->input->post('idproducto',true)[$i],
                'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
                'nlote'             =>$consultal->nlote,
                'salidaf'           =>$inicialf,
                'saldof'            =>$saldosl,
                'documento'         =>$this->input->post('serie',true).'-'.$numeracion,
              );
              $insertarc=$this->kardexl_model->insert($datac);

              array_push($nlotes,$consultal->nlote);
              array_push($clotes,$inicialf);
              array_push($flotes,$consultal->fvencimiento);

              $cantidad-=$consultal->stock;
              if ($cantidad<=0) {break;}
            }

            $nlotes=implode('|', $nlotes);
            $clotes=implode('|', $clotes);
            $flotes=implode('|', $flotes);
          }
        }

        $datad=array
        (
          'idnventa'    =>$insertar,
          'idproducto'  =>$this->input->post("idproducto",true)[$i],
          'descripcion' =>trim($this->input->post('descripcion',true)[$i]),
          'cantidad'    =>$this->input->post("cantidad",true)[$i],
          'unidad'      =>$this->input->post("unidad",true)[$i],
          'precio'      =>$this->input->post("precio",true)[$i],
          'importe'     =>$this->input->post("importe",true)[$i],
        );

        if ($this->input->post('tipo',true)[$i]=='B') {
          $datad["calmacen"]    =$this->input->post("almacenc",true)[$i];
          $datad["palmacen"]    =$costo;
          $datad["lote"]        =$nlotes;
          $datad["clote"]       =$clotes;
          $datad["fvencimiento"]=$flotes;
        }
        $insertard=$this->nventad_model->insert($datad);
      }

      if ($empresa->spuntos==1 && $this->input->post("idcliente",true)>1 && $this->input->post("tdocumento",true)!=6) {
        $vpuntos=$this->punto_model->mostrar();
        $punto_acumulado = intval($this->input->post("totalg",true)/$vpuntos->valorp);
        $datap=array
        (
          "idnventa"  =>$insertar,
          "idcliente" =>$this->input->post("idcliente",true),
          "femision"  =>date("Y-m-d"),
          "inicial"   =>$punto_acumulado,
          "cantidad"  =>$punto_acumulado,
        );
        $insertarp=$this->clientep_model->insert($datap);
      }

      $impresion=base_url()."nventa/generar/".$insertar;
      $control_movimiento=$this->movimientos('nventa/guardar','Emitio Nota Venta '.$this->input->post("serie",true).'-'.$numeracion);
      $mensaje='';
    } else {
      $mensaje='El comprobante ya existe';
    }

    $datos['mensaje']=$mensaje;
    $datos['impresion']=$impresion;
    $datos['url']=base_url().'pos';
    return $datos;
  }

  public function comprobante()
  {
    $empresa=$this->empresa_model->mostrar();
    $tcomprobante=$this->input->post('comprobante',true);
    $serie=SUBSTR($this->input->post('serie',true),0,1);
    $cliente=$this->cliente_model->mostrar($this->input->post('idcliente',true));

    if ($tcomprobante=='01' && $serie=='F' && $cliente->tdocumento==6 || $tcomprobante=='03' && $serie=='B') {
      if ($serie=="B" && $this->input->post('totalg',true)>700 && $cliente->tdocumento==0) {
        $mensaje='El monto a emitir es mayor a S/.700 requiere DNI del cliente';
      }else{
        $numero=$this->venta_model->maximo($this->input->post('serie',true));
        $ninicio= $numero==null ? '' : $numero->numero;
        $numeracion=$ninicio+1;

        $consulta=$this->venta_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
        if ($consulta==0) {
          $comprobante=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            'iduser'            =>$this->session->userdata('id'),
            "grupo"             =>$this->input->post('comprobante',true)=='01' ? '01' : '02',
            'tipo_soap'         =>$empresa->tipo_soap,
            'femision'          =>date("Y-m-d"),
            'hemision'          =>date('H:i:s'),
            'fvencimiento'      =>date("Y-m-d"),
            'tcomprobante'      =>$this->input->post('comprobante',true),
            'serie'             =>$this->input->post('serie',true),
            'numero'            =>$numeracion,
            'toperacion'        =>'0101',
            'moneda'            =>'PEN',
            'idcliente'         =>$this->input->post('idcliente',true),
            'cliente'           =>$this->input->post('cliente',true),
            'tgravado'          =>$this->input->post('gravado',true),
            'tinafecto'         =>$this->input->post('inafecto',true),
            'texonerado'        =>$this->input->post('exonerado',true),
            'tgratuito'         =>$this->input->post('gratuito',true),
            'subtotal'          =>$this->input->post('gravado',true)+$this->input->post('inafecto',true)+$this->input->post('exonerado',true),
            'tigv'              =>$this->input->post('igv',true),
            'total'             =>$this->input->post('totalg',true),
            //'dadicional'        =>$this->input->post('dadicional',true),
            'idvendedor'        =>$this->session->userdata("id"),
            'condicion'         =>1,
            'cancelado'         =>1,
            'tipo_estado'       =>'01',
            // 'efectivo'          =>valor_fecha($this->input->post('efectivo',true)),
            // 'vuelto'            =>valor_fecha($this->input->post('vuelto',true)),
          );
          $insertar=$this->venta_model->insert($comprobante);

          for ($i=0; $i < count($this->input->post('medios',true)) ; $i++) {
            $datap=array
            (
              'idestablecimiento' =>$this->session->userdata("predeterminado"),
              'iduser'            =>$this->session->userdata('id'),
              'idventa'           =>$insertar,
              'femision'          =>date("Y-m-d"),
              'idtpago'           =>$this->input->post('medios',true)[$i],
              'total'             =>$this->input->post('montos',true)[$i],
              'documento'         =>$this->input->post("referencia",true)[$i],
            );
            $insertarp=$this->cobroe_model->insert($datap);
          }

          for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
            if ($this->input->post('tipo',true)[$i]=='B') {
              $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
              $inicalf=$saldos==null ? 0: $saldos->saldof;
              $inicalv=$saldos==null ? 0: $saldos->saldov;
              //costos promedio
              $costo=$inicalf==0 ? 0 : round($inicalv/$inicalf,4);
              $salidav=$this->input->post("almacenc",true)[$i]*$costo;

              $saldof=$inicalf-$this->input->post("almacenc",true)[$i];
              $saldov=$inicalv-$salidav;
              $datak=array
              (
                'idestablecimiento' =>$this->session->userdata("predeterminado"),
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date('Y-m-d'),
                "idtmovimiento"     =>1,
                'concepto'          =>'Venta',
                'idproducto'        =>$this->input->post("idproducto",true)[$i],
                'descripcion'       =>trim($this->input->post("descripcion",true)[$i]),
                'salidaf'           =>$this->input->post("almacenc",true)[$i],
                'saldof'            =>$saldof,
                'costo'             =>$costo,
                'salidav'           =>$salidav,
                'saldov'            =>$saldov,
                'documento'         =>$this->input->post('serie',true).'-'.$numeracion,
              );
              $insertark=$this->kardex_model->insert($datak);

              $datas=array('stock'=>$saldof);
              $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));

              $nlotes='';
              $clotes='';
              $flotes='';
              if ($this->input->post("lote",true)[$i]==1) {
                $cantidad=$this->input->post("almacenc",true)[$i];

                $nlotes=array();
                $clotes=array();
                $flotes=array();
                for ($l=0; $l < 3 ; $l++) {
                  $consultal=$this->lote_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i]);

                  if ($cantidad>=$consultal->stock) {
                    $elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$consultal->nlote));
                    $inicialf=$consultal->stock;
                  } else {
                    $datal=array('stock'=>$consultal->stock-$cantidad);
                    $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
                    $inicialf=$cantidad;
                  }

                  $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
                  $inicial=$saldos==null ? 0: $saldos->saldof;
                  $saldosl=$inicial-$inicialf;
                  $datac=array
                  (
                    'idestablecimiento' =>$this->session->userdata("predeterminado"),
                    'iduser'            =>$this->session->userdata('id'),
                    'fecha'             =>date('Y-m-d'),
                    'idtmovimiento'     =>1,
                    'concepto'          =>'Venta',
                    'idproducto'        =>$this->input->post('idproducto',true)[$i],
                    'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
                    'nlote'             =>$consultal->nlote,
                    'salidaf'           =>$inicialf,
                    'saldof'            =>$saldosl,
                    'documento'         =>$this->input->post('serie',true).'-'.$numeracion,
                  );
                  $insertarc=$this->kardexl_model->insert($datac);

                  array_push($nlotes,$consultal->nlote);
                  array_push($clotes,$inicialf);
                  array_push($flotes,$consultal->fvencimiento);

                  $cantidad-=$consultal->stock;
                  if ($cantidad<=0) {break;}
                }

                $nlotes=implode('|', $nlotes);
                $clotes=implode('|', $clotes);
                $flotes=implode('|', $flotes);
              }
            }

              /*----------  conversion tipo afectacion  ----------*/
            if ($this->input->post('tafectacion',true)[$i]==10) {
              $tprecio='01';
              $valoru=round($this->input->post('precio',true)[$i]/1.18,6);
              $valor=round($this->input->post('importe',true)[$i]/1.18,2);
              $igv=round(($this->input->post('importe',true)[$i]*0.18)/1.18,2);
              $importe=$this->input->post('importe',true)[$i];
            } elseif ($this->input->post('tafectacion',true)[$i]==20 || $this->input->post('tafectacion',true)[$i]==30) {
              $tprecio='01';
              $valoru=$this->input->post("precio",true)[$i];
              $valor=$this->input->post('importe',true)[$i];
              $importe=$this->input->post('importe',true)[$i];
              $igv=0;
            } else {
              $tprecio='02';
              $valoru=0;
              $valor=$this->input->post('importe',true)[$i];
              $igv=round($this->input->post('importe',true)[$i]*0.18,2);
              $importe=0;
            }

            $itemx=array
            (
              'idventa'     =>$insertar,
              'idproducto'  =>$this->input->post('idproducto',true)[$i],
              'descripcion' =>trim($this->input->post('descripcion',true)[$i]),
              'unidad'      =>$this->input->post("unidad",true)[$i],
              'tafectacion' =>$this->input->post('tafectacion',true)[$i],
              'cantidad'    =>$this->input->post('cantidad',true)[$i],
              'valor'       =>$valoru,
              'tprecio'     =>$tprecio,
              'precio'      =>$this->input->post('precio',true)[$i],
              'total'       =>$valor,
              'igv'         =>$igv,
              'importe'     =>$importe,
            );

            if ($this->input->post('tipo',true)[$i]=='B') {
              $itemx["calmacen"]    =$this->input->post("almacenc",true)[$i];
              $itemx["palmacen"]    =$costo;
              $itemx["lote"]        =$nlotes;
              $itemx["clote"]       =$clotes;
              $itemx["fvencimiento"]=$flotes;
            }
            $insertard=$this->ventad_model->insert($itemx);
          }

          if ($empresa->spuntos==1 && $this->input->post("idcliente",true)>1 && $this->input->post("tdocumento",true)!=6) {
            $vpuntos=$this->punto_model->mostrar();
            $punto_acumulado = intval($this->input->post("totalg",true)/$vpuntos->valorp);
            $datap=array
            (
              "idventa"   =>$insertar,
              "idcliente" =>$this->input->post("idcliente",true),
              "femision"  =>date("Y-m-d"),
              "inicial"   =>$punto_acumulado,
              "cantidad"  =>$punto_acumulado,
            );
            $insertarp=$this->clientep_model->insert($datap);
          }

          $impresion=base_url()."venta/generar/".$insertar;
          $control_movimiento=$this->movimientos('pos/guardar','Emitio Venta '.$this->input->post("serie",true).'-'.$numeracion);
          $mensaje='';
        } else {
          $mensaje='El comprobante ya existe';
        }
      }
    } else {
      $mensaje='El tipo de comprobante no corresponde con la serie o cliente';
    }

    $datos['mensaje']=$mensaje;
    $datos['impresion']=$impresion;
    $datos['url']=base_url().'pos';
    return $datos;
  }

  public function movimientos($pagina,$descripcion)
  {
    $tiempo = date('Y-m-d H:i:s',time());
    $data=array
    (
      'user'        =>$this->session->userdata('user'),
      'descripcion' =>$descripcion,
      'tiempo'      =>$tiempo,
      'pagina'      =>$pagina,
    );
    $insertar=$this->controlm_model->insertar($data);
  }

  public function controlip($pagina)
  {
    $nomcpu=gethostbyaddr($_SERVER["REMOTE_ADDR"]);
    $ip = $_SERVER["REMOTE_ADDR"];
      $info=$this->detectar();
      $tiempo = date('Y-m-d H:i:s',time());
      $limite = time()-5*60;  //borrando los registros de las ip inactivas (5 minutos)

    $borrar=$this->controlip_model->delete($limite);
    $consulta=$this->controlip_model->contador($this->session->userdata('user'));

    if ($consulta==0) {
      $data=array
      (
        'ip'  =>$ip,
        'fecha' =>time(),
        'tiempo'=>$tiempo,
        'nombre'=>$nomcpu,
        'soperativo'=>$info["os"],
        'navegador'=>$info["browser"],
        'dispositivo'=>$info["device"],
        'pagina'=>$pagina,
        'user'  =>$this->session->userdata('user'),
      );
      $insertar=$this->controlip_model->insertar($data);
    }else{
      $data=array
      (
        'ip'  =>$ip,
        'fecha' =>time(),
        'tiempo'=>$tiempo,
        'nombre'=>$nomcpu,
        'soperativo'=>$info["os"],
        'navegador'=>$info["browser"],
        'dispositivo'=>$info["device"],
        'pagina'=>$pagina,
      );
      $guardar=$this->controlip_model->update($data,$this->session->userdata('user'));
    }
  }

  function detectar()
  {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    # definimos unos valores por defecto para el navegador y el sistema operativo
    $info['browser'] = "Otros";
    $info['os'] = "Otros";
    $info['device'] = "Otros";

    # buscamos el navegador con su sistema operativo
    if(strpos($user_agent, 'MSIE') !== FALSE)
       $parent='Internet explorer';
    elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
       $parent='Microsoft Edge';
    elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
        $parent='Internet explorer';
    elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
       $parent="Opera Mini";
    elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
       $parent="Opera";
    elseif(strpos($user_agent, 'Firefox') !== FALSE)
       $parent='Mozilla Firefox';
    elseif(strpos($user_agent, 'Chrome') !== FALSE)
       $parent='Google Chrome';
    elseif(strpos($user_agent, 'Safari') !== FALSE)
       $parent="Safari";
    else
       $parent='No hemos podido detectar su navegador';
    $info['browser'] = $parent;

    # obtenemos el sistema operativo
    $plataformas = array(
      'Windows 10' => 'Windows NT 10.0+',
      'Windows 8.1' => 'Windows NT 6.3+',
      'Windows 8' => 'Windows NT 6.2+',
      'Windows 7' => 'Windows NT 6.1+',
      'Windows Vista' => 'Windows NT 6.0+',
      'Windows XP' => 'Windows NT 5.1+',
      'Windows 2003' => 'Windows NT 5.2+',
      'Windows' => 'Windows otros',
      'iPhone' => 'iPhone',
      'iPad' => 'iPad',
      'Mac OS X' => '(Mac OS X+)|(CFNetwork+)',
      'Mac otros' => 'Macintosh',
      'Android' => 'Android',
      'BlackBerry' => 'BlackBerry',
      'Linux' => 'Linux',
    );
    foreach($plataformas as $plataforma=>$pattern){
        if (preg_match('/(?i)'.$pattern.'/', $user_agent))
           $info['os'] = $plataforma;
    }

    if(preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i",$user_agent)){
      $info['device'] = 'Mobile';
    }
    else {
      $info['device'] = 'Desktop';
    }

    # devolvemos el array de valores
    return $info;
  }

  public function acciones($numero)
  {
    $accesos=$this->anusuario_model->mostrar(array("idacceson"=>$numero,"iduser"=>$this->session->userdata('id')));
    $activoc=$accesos!=NULL??0;
    // $activoi=$accesos!=NULL? $accesos->insertar: 0;
    // $activoe=$accesos!=NULL? $accesos->editar: 0;

    // $datos['insertar']=$activoi;
    // $datos['editar']=$activoe;
    return $activoc;
  }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traslado extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(40)){redirect(base_url()."inicio");}

    $this->layout->setLayout("contraido");
    $this->load->model("tmovimiento_model");
    $this->load->model("kardex_model");
    $this->load->model("kardexl_model");
    $this->load->model("lote_model");
    $this->load->model("traslado_model");
    $this->load->model("trasladod_model");
    $this->load->library("mytcpdf");
  }

  public function index()
  {
    $controlip=$this->controlip('traslado');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
    $fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d");

    $listas=$this->traslado_model->mostrarTotal($this->session->userdata("predeterminado"),$inicio,$fin);
    $this->layout->setTitle("Traslado Producto");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function traslados()
  {
    $controlip=$this->controlip('traslado/traslados');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $nestablecimientos=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
    $motivos=$this->tmovimiento_model->mostrarTotal(array("id"=>11));
    $this->layout->setTitle("Salida Producto");
    $this->layout->view("traslados",compact("anexos","nestablecimiento",'empresa',"motivos","nestablecimientos"));
  }

  public function trasladoi($id)
  {
    $controlip=$this->controlip('traslado/trasladoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $motivos=$this->tmovimiento_model->mostrarTotal(array("id"=>21));
    $datos=$this->traslado_model->mostrar($id);
    $listas=$this->trasladod_model->mostrarTotal($id);
    $this->layout->setTitle("Ingreso Producto");
    $this->layout->view("trasladoi",compact("anexos","nestablecimiento",'empresa',"motivos","listas","datos","id"));
  }

  public function salidag()
  {
    $controlip=$this->controlip('traslado/salidag');
    if ($this->input->post())
    {
      $url='';
      if ($this->input->post('idproducto',true)==null) {
        $mensaje='No envio productos en el movimiento!';
      } else {
        $data=array
        (
          "idestablecimiento"  =>$this->session->userdata("predeterminado"),
          "iduser"             =>$this->session->userdata('id'),
          "femision"           =>date("Y-m-d"),
          "idestablecimientod" =>$this->input->post("destino",true),
        );
        $insertar=$this->traslado_model->insert($data);

        $importe=0;
        for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
          $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
          $inicalf=$saldos==null ? 0: $saldos->saldof;
          $inicalv=$saldos==null ? 0: $saldos->saldov;
          //costos promedio
          $costo=round($inicalv/$inicalf,2);
          $salidav=$this->input->post('almacenc',true)[$i]*$costo;

          $saldof=$inicalf-$this->input->post('almacenc',true)[$i];
          $saldov=$inicalv-$salidav;
          $mtraslado=explode('-',$this->input->post('motivo',true));
          $datak=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            "iduser"            =>$this->session->userdata('id'),
            'fecha'             =>date("Y-m-d"),
            'idtmovimiento'     =>$mtraslado[0],
            'concepto'          =>$mtraslado[1],
            'idproducto'        =>$this->input->post('idproducto',true)[$i],
            'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
            'salidaf'           =>$this->input->post('almacenc',true)[$i],
            'saldof'            =>$saldof,
            'costo'             =>$costo,
            'salidav'           =>$salidav,
            'saldov'            =>$saldov,
            "documento"         =>'TI-'.$insertar,
          );
          $insertark=$this->kardex_model->insert($datak);

          $datas=array('stock'=>$saldof);
          $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

          $nlotes='';
          $clotes='';
          $flotes='';
          if ($this->input->post('lote',true)[$i]!='') {
            $cantidad=$this->input->post('almacenc',true)[$i];
            $clotes=array();
            $flotes=array();
            $nlotes=array();
            $nlote1=explode(',',$this->input->post('lote',true)[$i]);
            for ($l=0; $l < count($nlote1) ; $l++) {
              $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i],"nlote"=>$nlote1[$l]));
              $ncantidad=$cantidad-$consultal->stock; //nueva cantidad
              $saldoc=$consultal->stock-$cantidad;  //saldo a guardar

              if ($saldoc>0) {
                $datal=array('stock'=>$saldoc);
                $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$nlote1[$l]);
              } else {
                $elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i],"nlote"=>$nlote1[$l]));
              }

              if ($consultal->stock<$cantidad) {
                $inicialf=$consultal->stock;
              } else {
                $inicialf=$cantidad;
              }

              $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$nlote1[$l]);
              $inicial=$saldos==null ? 0: $saldos->saldof;
              $saldosl=$inicial-$inicialf;
              $datac=array
              (
                'idestablecimiento' =>$this->session->userdata('predeterminado'),
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date("Y-m-d"),
                'idtmovimiento'     =>$mtraslado[0],
                'concepto'          =>$mtraslado[1],
                'idproducto'        =>$this->input->post('idproducto',true)[$i],
                'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
                'nlote'             =>$nlote1[$l],
                'salidaf'           =>$inicialf,
                'saldof'            =>$saldosl,
                'documento'         =>'TI-'.$insertar,
              );
              $insertarc=$this->kardexl_model->insert($datac);

              array_push($nlotes,$consultal->nlote);
              array_push($clotes,$inicialf);
              array_push($flotes,$consultal->fvencimiento);
              $cantidad=$ncantidad;
              if ($cantidad<=0) {break;}
            }

            $nlotes=implode('|', $nlotes);
            $clotes=implode('|', $clotes);
            $flotes=implode('|', $flotes);
          }

          $datad=array
          (
            "idtraslado"  =>$insertar,
            "idproducto"  =>$this->input->post("idproducto",true)[$i],
            "descripcion" =>trim($this->input->post("descripcion",true)[$i]),
            "unidad"      =>$this->input->post("unidad",true)[$i],
            "cantidad"    =>$this->input->post("cantidad",true)[$i],
            "precio"      =>$salidav/$this->input->post("cantidad",true)[$i],
            "importe"     =>$salidav,
            "calmacen"    =>$this->input->post("almacenc",true)[$i],
            "palmacen"    =>$costo,
            "lote"        =>$nlotes,
            "clote"       =>$clotes,
            "fvencimiento"=>$flotes,
          );
          $insertard=$this->trasladod_model->insert($datad);
          $importe+=$salidav;
        }

        $datai=array("importe"=>$importe);
        $actualizari=$this->traslado_model->update($datai,$insertar);
        $control_movimiento=$this->movimientos('traslado/salidag','Registro salida traslado nro '.$insertar);

        $mensaje='Los datos se han guardado exitosamente!';
        $url=base_url().'traslado';
      }

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
    }
  }

  public function ingresog($id)
  {
    $controlip=$this->controlip('traslado/ingresog');
    if ($this->input->post())
    {
      $url='';
      if ($this->input->post('idproducto',true)==null) {
        $mensaje='No envio productos en el movimiento!';
      } else {
        $consulta=$this->traslado_model->mostrar($id);
        if ($consulta->frecepcion==null) {
          $data=array
          (
            "frecepcion"    =>date("Y-m-d"),
            "urecepcion"    =>$this->session->userdata('id'),
          );
          $actualizar=$this->traslado_model->update($data,$id);

          for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
            $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
            $inicalf=$saldos==null ? 0: $saldos->saldof;
            $inicalv=$saldos==null ? 0: $saldos->saldov;

            $saldof=$inicalf+$this->input->post("almacenc",true)[$i];
            $saldov=$inicalv+$this->input->post("importe",true)[$i];
            $mtraslado=explode('-',$this->input->post('motivo',true));
            $datak=array
            (
              "idestablecimiento" =>$this->session->userdata("predeterminado"),
              "iduser"            =>$this->session->userdata('id'),
              "fecha"             =>date("Y-m-d"),
              "idtmovimiento"     =>$mtraslado[0],
              "concepto"          =>$mtraslado[1],
              "idproducto"        =>$this->input->post("idproducto",true)[$i],
              "descripcion"       =>trim($this->input->post("descripcion",true)[$i]),
              "entradaf"          =>$this->input->post("almacenc",true)[$i],
              "saldof"            =>$saldof,
              "costo"             =>$this->input->post("almacenp",true)[$i],
              "entradav"          =>$this->input->post("importe",true)[$i],
              "saldov"            =>$saldov,
              "documento"         =>'TI-'.$id,
            );
            $insertark=$this->kardex_model->insert($datak);

            $datas=array('stock'=>$saldof);
            $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

            if ($this->input->post("lote",true)[$i]!="") {
              $nlotes=explode('|',$this->input->post('lote',true)[$i]);
              $clotes=explode('|',$this->input->post('clote',true)[$i]);
              $flotes=explode('|',$this->input->post('fvencimiento',true)[$i]);

              for ($l=0; $l < count($nlotes) ; $l++) {
                $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i],"nlote"=>$nlotes[$l]));

                if ($consultal==null) {
                  $datal=array
                  (
                    "idestablecimiento" =>$this->session->userdata("predeterminado"),
                    'idproducto'        =>$this->input->post('idproducto',true)[$i],
                    'nlote'             =>$nlotes[$l],
                    'fvencimiento'      =>valor_fecha($flotes[$l]),
                    'inicial'           =>$clotes[$l],
                    'stock'             =>$clotes[$l],
                  );
                  $insertarl=$this->lote_model->insert($datal);
                } else {
                  $datal=array("stock"=>$consultal->stock+$clotes[$l]);
                  $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$nlotes[$l]);
                }

                $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlotes[$l]);
                $inicial=$saldos==null ? 0: $saldos->saldof;
                $saldosl=$inicial+$clotes[$l];
                $datac=array
                (
                  'idestablecimiento' =>$this->session->userdata('predeterminado'),
                  'iduser'            =>$this->session->userdata('id'),
                  'fecha'             =>date('Y-m-d'),
                  'idtmovimiento'     =>$mtraslado[0],
                  'concepto'          =>$mtraslado[1],
                  'idproducto'        =>$this->input->post('idproducto',true)[$i],
                  'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
                  'nlote'             =>$nlotes[$l],
                  'entradaf'          =>$clotes[$l],
                  'saldof'            =>$saldosl,
                  'documento'         =>'TI-'.$id,
                );
                $insertarc=$this->kardexl_model->insert($datac);
              }
            }
          }
          $control_movimiento=$this->movimientos('traslado/ingresog','Registro ingreso traslado nro '.$id);

          $mensaje='El ingreso se ha guardado exitosamente!';
          $url=base_url()."traslado";
        }else{
          $mensaje='El traslado ya fue ingresado!';
        }
      }

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
    }
  }

  public function trasladoa($id)
  {
    $controlip=$this->controlip('traslado/trasladoa');
    $datos=$this->traslado_model->mostrar($id);
    if ($datos->nulo==0) {
      $detalles=$this->trasladod_model->mostrarTotal($id);
      foreach ($detalles as $detalle) {
        $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));
        $inicalf=$saldos==null ? 0: $saldos->saldof;
        $inicalv=$saldos==null ? 0: $saldos->saldov;
        //costos promedio
        $salidav=$detalle->calmacen*$detalle->palmacen;

        $saldof=$inicalf+$detalle->calmacen;
        $saldov=$inicalv+$salidav;
        $datak=array
        (
          "idestablecimiento" =>$datos->idestablecimiento,
          "iduser"            =>$this->session->userdata('id'),
          "fecha"             =>date("Y-m-d"),
          "idtmovimiento"       =>11,
          "concepto"          =>'Anulacion Salida por transferencia entre almacenes',
          "idproducto"        =>$detalle->idproducto,
          "descripcion"       =>$detalle->descripcion,
          "entradaf"          =>$detalle->calmacen,
          "saldof"            =>$saldof,
          "costo"             =>$detalle->palmacen,
          "entradav"          =>$salidav,
          "saldov"            =>$saldov,
          "documento"         =>'TI-'.$datos->id,
        );
        $insertark=$this->kardex_model->insert($datak);

        $datas=array('stock'=>$saldof);
        $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

        if ($detalle->lote!='') {
          $nlote=explode("|",$detalle->lote);
          $flote=explode("|",$detalle->fvencimiento);
          $clote=explode("|",$detalle->clote);

          for ($l=0; $l < count($nlote) ; $l++) {
            $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));

            if ($consultal==null) {
              $datal=array
              (
                "idestablecimiento" =>$datos->idestablecimiento,
                "idproducto"        =>$detalle->idproducto,
                "nlote"             =>$nlote[$l],
                "fvencimiento"      =>valor_fecha($flote[$l]),
                "inicial"           =>$clote[$l],
                "stock"             =>$clote[$l],
              );
              $insertarl=$this->lote_model->insert($datal);
            } else {
              $datal=array("stock"=>$consultal->stock+$clote[$l]);
              $actualizar=$this->lote_model->update($datal,$datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
            }

            $saldos=$this->kardexl_model->ultimo($datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
            $inicial=$saldos==null ? 0: $saldos->saldof;
            $saldosl=$inicial-$clote[$l];
            $datac=array
            (
              'idestablecimiento' =>$datos->idestablecimiento,
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date("Y-m-d"),
              'idtmovimiento'       =>11,
              'concepto'          =>'Anulacion Salida por transferencia entre almacenes',
              'idproducto'        =>$detalle->idproducto,
              'descripcion'       =>$detalle->descripcion,
              'nlote'             =>$nlote[$l],
              'salidaf'           =>$clote[$l],
              'saldof'            =>$saldosl,
              'documento'         =>'TI-'.$insertar,
            );
            $insertarc=$this->kardexl_model->insert($datac);
          }
        }
      }
      $data=array
      (
        "nulo"    =>1,
        "importe" =>"0.00",
      );
      $actualizar=$this->traslado_model->update($data,$id);
      $control_movimiento=$this->movimientos('traslado/trasladoa','Anulo traslado nro '.$id);

      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
    }else{
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', 'El registro ya fue anulado previamente!');
    }
    redirect(base_url()."traslado");
  }

  public function consulta($id)
  {
    $datos=$this->traslado_model->mostrar($id);
    $detalles=$this->trasladod_model->mostrarTotal($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("consulta",compact("datos","detalles"));
  }

  public function pdftraslado($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $datos=$this->traslado_model->mostrar($id);
    $origen=$this->establecimiento_model->mostrar($datos->idestablecimiento);
    $destino=$this->establecimiento_model->mostrar($datos->idestablecimientod);
    $detalles=$this->trasladod_model->mostrarTotal($id);
    $nombre= $this->usuario_model->mostrar($datos->iduser);
    $this->layout->setLayout("blanco");
    $this->layout->view("pdftraslado",compact("nestablecimiento","empresa","datos","origen","destino","detalles","nombre"));
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

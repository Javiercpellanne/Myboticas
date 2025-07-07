<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Despacho extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata('login')){redirect(base_url().'login');}
    if (!is_numeric($this->session->userdata('codigo'))){redirect(base_url().'inicio');}
    if (!$this->acciones(34)){redirect(base_url()."inicio");}

    $this->layout->setLayout('principal');
    $this->load->model("testado_model");
    $this->load->model('serie_model');
    $this->load->model('ttransporte_model');
    $this->load->model('ttraslado_model');
    $this->load->model('departamento_model');
    $this->load->model('provincia_model');
    $this->load->model('distrito_model');
    $this->load->model('tidentidad_model');
    $this->load->model('cliente_model');
    $this->load->model('transporte_model');
    $this->load->model('venta_model');
    $this->load->model('ventad_model');
    $this->load->model('traslado_model');
    $this->load->model('trasladod_model');
    $this->load->model('despacho_model');
    $this->load->model('despachod_model');

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
    $controlip=$this->controlip('despacho');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date('Y-m-d')) ;
    $fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->despacho_model->mostrarTotal($filtros,"desc");
    $this->layout->setLayout('contraido');
    $this->layout->setTitle('Guia de Remision');
    $this->layout->view('index',compact("anexos","nestablecimiento",'listas','inicio','fin','empresa'));
  }

  public function despachov($id)
  {
    $controlip=$this->controlip('despacho/despachov');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'09'));
    $modost=$this->ttransporte_model->mostrarTotal();
    $motivost=$this->ttraslado_model->mostrarTotal();
    $didentidades=$this->tidentidad_model->mostrarTotal();
    $departamentos=$this->departamento_model->mostrarTotal();
    $provincias=$this->provincia_model->mostrarTotal($nestablecimiento->iddepartamento);
    $distritos=$this->distrito_model->mostrarTotal($nestablecimiento->idprovincia);

    $venta=$this->venta_model->mostrar($id);
    $detalles=$this->ventad_model->mostrarTotal($id);
    $clientes=$this->cliente_model->mostrar($venta->idcliente);
    $dprovincias=$this->provincia_model->mostrarTotal($clientes->iddepartamento);
    $ddistritos=$this->distrito_model->mostrarTotal($clientes->idprovincia);
    $conductores=$this->transporte_model->mostrarTotal('02');
    $transportistas=$this->transporte_model->mostrarTotal('01');
    $this->layout->setTitle('Guia de Remision');
    $this->layout->view('despachov',compact("anexos","nestablecimiento","nserie","clientes","modost","motivost","departamentos","provincias","distritos","didentidades","empresa","venta","detalles","dprovincias","ddistritos","conductores","transportistas"));
  }

  public function despachot($id)
  {
    $controlip=$this->controlip('despacho/despachot');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'09'));
    $modost=$this->ttransporte_model->mostrarTotal();
    $motivost=$this->ttraslado_model->mostrar('04');
    $didentidades=$this->tidentidad_model->mostrarTotal();
    $departamentos=$this->departamento_model->mostrarTotal();
    $provincias=$this->provincia_model->mostrarTotal($nestablecimiento->iddepartamento);
    $distritos=$this->distrito_model->mostrarTotal($nestablecimiento->idprovincia);

    $traslado=$this->traslado_model->mostrar($id);
    $detalles=$this->trasladod_model->mostrarTotal($id);
    $destino=$this->establecimiento_model->mostrar($traslado->idestablecimientod);
    $dprovincias=$this->provincia_model->mostrarTotal($destino->iddepartamento);
    $ddistritos=$this->distrito_model->mostrarTotal($destino->idprovincia);
    $conductores=$this->transporte_model->mostrarTotal('02');
    $transportistas=$this->transporte_model->mostrarTotal('01');
    $this->layout->setTitle('Guia de Remision');
    $this->layout->view('despachot',compact("anexos","nestablecimiento","nserie","modost","motivost","departamentos","provincias","distritos","didentidades","empresa","destino","traslado","detalles","dprovincias","ddistritos","conductores","transportistas","id"));
  }

  public function despachoi()
  {
    $controlip=$this->controlip('despacho/despachoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'09'));
    $modost=$this->ttransporte_model->mostrarTotal();
    $motivost=$this->ttraslado_model->mostrarTotal();
    $didentidades=$this->tidentidad_model->mostrarTotal();
    $departamentos=$this->departamento_model->mostrarTotal();
    $provincias=$this->provincia_model->mostrarTotal($nestablecimiento->iddepartamento);
    $distritos=$this->distrito_model->mostrarTotal($nestablecimiento->idprovincia);
    $conductores=$this->transporte_model->mostrarTotal('02');
    $transportistas=$this->transporte_model->mostrarTotal('01');
    $this->layout->setTitle('Guia de Remision');
    $this->layout->view('despachoi',compact("anexos","nestablecimiento","nserie","modost","motivost","departamentos","provincias","distritos","didentidades","empresa","conductores","transportistas"));
  }

  public function guardar($id=NULL)
  {
    $controlip=$this->controlip('despacho/guardar');
    if ($this->input->post())
    {
      if ($this->input->post('idproducto',true)==null) {
        $this->session->set_flashdata('css', 'danger');
        $this->session->set_flashdata('mensaje', 'No envio productos en la venta!');
      } else {
        $empresa=$this->empresa_model->mostrar();
        $numero=$this->despacho_model->maximo($this->input->post('serie',true));
        $ninicio= $numero==null ? '' : $numero->numero;
        $numeracion=$ninicio+1;

        $comprobante=array
        (
          'idestablecimiento'   =>$this->session->userdata("predeterminado"),
          'iduser'              =>$this->session->userdata('id'),
          'femision'            =>date('Y-m-d'),
          'hemision'            =>date('H:i:s'),
          'tcomprobante'        =>'09',
          'serie'               =>$this->input->post('serie',true),
          'numero'              =>$numeracion,
          'idcliente'           =>$this->input->post('idcliente',true),
          'cliente'             =>$this->input->post('cliente',true),
          'observaciones'       =>$this->input->post('observaciones',true),
          'idttransporte'       =>$this->input->post('modot',true),
          'idttraslado'         =>$this->input->post('motivot',true),
          'descripcion_traslado'=>$this->input->post('descripciont',true),
          'fenvio'              =>$this->input->post('fenvio',true),
          'unidad_peso'         =>"KGM",
          'peso_total'          =>$this->input->post('peso_total',true),
          'paquetes'            =>$this->input->post('paquetes',true),
          'codigo_origen'       =>$this->input->post('codigop',true),
          'ubigeo_origen'       =>$this->input->post('distritop',true),
          'direccion_origen'    =>$this->input->post('direccionp',true),
          'codigo_entrega'      =>$this->input->post('codigoe',true),
          'ubigeo_entrega'      =>$this->input->post('distritoe',true),
          'direccion_entrega'   =>$this->input->post('direccione',true),
          'tipo_estado'         =>'01',
          'm1l'                 =>valor_check($this->input->post('m1l',true)),
        );

        if (valor_check($this->input->post('m1l',true))==0) {
          if ($this->input->post('modot',true)=='01') {
            $comprobante['tdocumento_transporte']=$this->input->post('documentot',true);
            $comprobante['ndocumento_transporte']=$this->input->post('ndocumentot',true);
            $comprobante['nombres_transporte']   =$this->input->post('nombrest',true);
          } else {
            $comprobante['tdocumento_transporte']=$this->input->post('documentoc',true);
            $comprobante['ndocumento_transporte']=$this->input->post('ndocumentoc',true);
            $comprobante['nombres_transporte']=$this->input->post('nombresc',true);
            $comprobante['licencia_conducir']=$this->input->post('licencia',true);
            $comprobante['placa']=$this->input->post('placa',true);
          }
        }
        $insertar=$this->despacho_model->insert($comprobante);

        for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
          $itemx=array
          (
            'iddespacho'  =>$insertar,
            'idproducto'  =>$this->input->post('idproducto',true)[$i],
            'descripcion' =>trim($this->input->post('descripcion',true)[$i]),
            'unidad'      =>$this->input->post("unidad",true)[$i],
            'cantidad'    =>$this->input->post('cantidad',true)[$i],
            'lote'        =>$this->input->post('lote',true)[$i],
            'fvencimiento'=>$this->input->post('fvencimiento',true)[$i],
          );
          $insertard=$this->despachod_model->insert($itemx);
        }

        $nombrexml = $empresa->ruc.'-'.$comprobante['tcomprobante'].'-'.$comprobante['serie'].'-'.$comprobante['numero'];
        $mensaje=$this->generadogXml($insertar);

        self::pdfguia($insertar);
        $control_movimiento=$this->movimientos('despacho/guardar','Emitio guia de remision '.$this->input->post('serie',true).'-'.$numeracion);

        if ($empresa->envio_guia==1 && $empresa->id_gre!='') {
          $mensaje_envios=$this->enviarGuia($insertar);

          $this->session->set_flashdata('css', $mensaje_envios['color']??'');
          $this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
        }else{
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', $mensaje);
        }

        echo "<script>window.open('".base_url()."downloads/pdf/".$nombrexml.".pdf','_blank');</script>";
      }
      echo "<script>location.href ='".base_url()."despacho'; </script>";
    }
  }

  public function pdfguia($id)
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->despacho_model->mostrar($id);
    $clientes=$nestablecimiento;
    if ($datos->idcliente>0) {
      $clientes=$this->cliente_model->mostrar($datos->idcliente);
    } else {
      $clientes->nombres=$empresa->nombres;
      $clientes->documento=$empresa->ruc;
    }
    $detalles=$this->despachod_model->mostrarTotal($id);
    $pdepartamentos=$this->departamento_model->mostrar(substr($datos->ubigeo_origen,0,2));
    $pprovincias=$this->provincia_model->mostrar(substr($datos->ubigeo_origen,0,4));
    $pdistritos=$this->distrito_model->mostrar($datos->ubigeo_origen);
    $edepartamentos=$this->departamento_model->mostrar(substr($datos->ubigeo_entrega,0,2));
    $eprovincias=$this->provincia_model->mostrar(substr($datos->ubigeo_entrega,0,4));
    $edistritos=$this->distrito_model->mostrar($datos->ubigeo_entrega);
    $this->layout->setLayout("blanco");
    $this->layout->view("pdfguia",compact("empresa","nestablecimiento","datos","clientes","detalles","pdepartamentos","pprovincias","pdistritos","edepartamentos","eprovincias","edistritos"));
  }

  public function emisor()
  {
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $datos=array
    (
      'ruc'           =>$empresa->ruc,
      'nombres'       =>$empresa->nombres,
      'ncomercial'    =>$empresa->ncomercial,
      'codigo'        =>$nestablecimiento->codigo,
      'ndepartamento' =>$nestablecimiento->ndepartamento,
      'nprovincia'    =>$nestablecimiento->nprovincia,
      'ndistrito'     =>$nestablecimiento->ndistrito,
      'distrito'      =>$nestablecimiento->iddistrito,
      'direccion'     =>$nestablecimiento->direccion,
    );
    return $datos;
  }

  public function itemsGuia($id)
  {
    $detalles=$this->despachod_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      $itemx=array
      (
        'iddespacho'      =>$id,
        'idproducto'      =>$detalle->idproducto,
        'descripcion'     =>$detalle->descripcion,
        'unidad'          =>$detalle->unidad,
        'cantidad'        =>$detalle->cantidad,
      );
      $datos[]=$itemx;
    }
    return $datos;
  }

  public function generadogXml($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->despacho_model->mostrar($id);
    $comprobante=array
    (
      'femision'              =>$datos->femision,
      'hemision'              =>$datos->hemision,
      'tcomprobante'          =>$datos->tcomprobante,
      'serie'                 =>$datos->serie,
      'numero'                =>$datos->numero,
      'codigo_motivo_traslado'=>$datos->idttraslado,
      'motivo_traslado'       =>$datos->descripcion_traslado,
      'unidad_peso'           =>$datos->unidad_peso,
      'peso'                  =>$datos->peso_total,
      'paquetes'              =>$datos->paquetes,
      'modo_transporte'       =>$datos->idttransporte,
      'fecha_envio'           =>$datos->fenvio,
      'destino_codigo'        =>$datos->codigo_entrega,
      'destino_ubigeo'        =>$datos->ubigeo_entrega,
      'destino_direccion'     =>$datos->direccion_entrega,
      'partida_codigo'        =>$datos->codigo_origen,
      'partida_ubigeo'        =>$datos->ubigeo_origen,
      'partida_direccion'     =>$datos->direccion_origen,
      'observaciones'         =>$datos->observaciones,
      'm1l'                   =>$datos->m1l,
    );

    if ($datos->idttransporte=='01') {
      $comprobante['transporte_tipo_doc']=$datos->tdocumento_transporte;
      $comprobante['transporte_nro_doc']=$datos->ndocumento_transporte;
      $comprobante['transporte_nombres']=$datos->nombres_transporte;
    } else {
      $comprobante['transporte_tipo_doc']=$datos->tdocumento_transporte;
      $comprobante['transporte_nro_doc']=$datos->ndocumento_transporte;
      $comprobante['transporte_nombres']=$datos->nombres_transporte;
      $comprobante['licencia']=$datos->licencia_conducir;
      $comprobante['placa']=str_replace('-','',$datos->placa);
    }

    $nombrexml = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $ruta_xml = "downloads/xml/".$nombrexml;
    $emisor=$this->emisor();
    $cliente=(object) array();
    if ($datos->idcliente>0) {
      $cliente=$this->cliente_model->mostrar($datos->idcliente);
    } else {
      $cliente->tdocumento=6;
      $cliente->documento=$empresa->ruc;
      $cliente->nombres=$empresa->nombres;
    }
    $detalle=$this->itemsGuia($id);
    $this->generadoXML->CrearXMLGuia($ruta_xml, $emisor, $cliente, $comprobante, $detalle);
    $ruta_certificado = "downloads/certificado/".$empresa->certificado;
    $hash = $this->firmadoXML->FirmarDocumento($ruta_xml,$ruta_certificado,$empresa->certificado_clave);

    $datav=array(
      'filename'  =>$nombrexml,
      //'hash'      =>$hash,
      'has_xml'   =>1,
      'has_pdf'   =>1,
    );
    $actualizar=$this->despacho_model->update($datav,$id);
    return 'Se genero comprobante '.$datos->serie.'-'.$datos->numero;
  }

  public function consulta($id)
  {
    $datos=$this->despacho_model->mostrar($id);
    $detalles=$this->despachod_model->mostrarTotal($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("consulta",compact("datos","detalles"));
  }

  public function guiaToken()
  {
    $empresa=$this->empresa_model->mostrar();
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/'.$empresa->id_gre.'/oauth2/token/',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'grant_type=password&scope=https://api-cpe.sunat.gob.pe&client_id='.$empresa->id_gre.'&client_secret='.urlencode($empresa->secret_gre).'&username='.$empresa->usuario_soap.'&password='.urlencode($empresa->clave_soap).'',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response,true);
  }

  public function enviarGuia($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $limite=fechaHoraria('+ '.$empresa->expires_gre.' second ',$empresa->fecha_gre);
    if ($limite<date("Y-m-d H:i:s")) {
      $respuesta_json=self::guiaToken();

      $data=array
      (
        'token_gre' =>$respuesta_json["access_token"],
        'fecha_gre' =>date("Y-m-d H:i:s"),
        'expires_gre' =>$respuesta_json["expires_in"],
      );
      $actualizar=$this->empresa_model->update($data);
    }

    $datos=$this->despacho_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $resultado = $this->apiFacturacion->EnviarGuiaRemision($empresa,$nombre,$rutazip);

    if ($resultado['ticket']!='') {
      $datav=array
      (
        'ticket'      =>$resultado['ticket'],
        'frecepcion'  =>$resultado['frecepcion'],
        'tipo_estado' =>'03',
      );
      $actualizar=$this->despacho_model->update($datav,$id);

      $mensaje['color']='info';
    }else{
      $mensaje['color']='danger';
    }

    $mensaje['mensaje']=$resultado['mensaje'];
    return $mensaje;
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

<?php
defined("BASEPATH") OR exit('No direct script access allowed');

class Gasto extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    $this->layout->setLayout("contraido");
    $this->load->model("tidentidad_model");
    $this->load->model("departamento_model");
    $this->load->model("provincia_model");
    $this->load->model("distrito_model");
    $this->load->model("tpago_model");
    $this->load->model("tcomprobante_model");
    $this->load->model("proveedor_model");
    $this->load->model("compra_model");
    $this->load->model("comprad_model");
    $this->load->model("pago_model");
    $this->load->library("mytcpdf");
  }

  public function index()
  {
    $controlip=$this->controlip('gasto');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
    $fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d") ;

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tipo"=>'G',"femision>="=>$inicio,"femision<="=>$fin);
    if ($this->session->userdata("tipo")!='admin') {$filtrop['iduser']=$this->session->userdata("id");}
    $listas=$this->compra_model->mostrarTotal($filtros);
    $this->layout->setTitle("Compra Producto");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function gastoi($id=null)
  {
    $controlip=$this->controlip('gasto/gastoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $mpagos=$this->tpago_model->mostrarTotal();
    $datos=$id!=null?$this->compra_model->mostrar($id):(object) array("comprobante"=>'',"femision"=>date("Y-m-d"),"serie"=>'',"numero"=>'',"idproveedor"=>'',"proveedor"=>'',"moneda"=>'PEN',"tcambio"=>'1.000',"dadicional"=>'',"total"=>'');
    $this->layout->setLayout("principal");
    $this->layout->setTitle("Gasto Soles");
    $this->layout->view("gastoi",compact("anexos","nestablecimiento",'empresa',"mpagos","datos","id"));
  }

  public function guardar($id=null)
  {
    $controlip=$this->controlip('gasto/guardar');
    $empresa=$this->empresa_model->mostrar();
    if ($this->input->post())
    {
      $url='';
      if ($id!=null) {
        $data=array
        (
          "femision"          =>$this->input->post("fecha",true),
          "moneda"            =>$this->input->post("moneda",true),
          "comprobante"       =>$this->input->post("comprobante",true),
          "serie"             =>$this->input->post("serie",true),
          "numero"            =>$this->input->post("numero",true),
          "idproveedor"       =>$this->input->post("idproveedor",true),
          "proveedor"         =>$this->input->post("proveedor",true),
          'tgravado'          =>round($this->input->post("total",true)/1.18,2),
          "subtotal"          =>round($this->input->post("total",true)/1.18,2),
          "igv"               =>round(($this->input->post("total",true)/1.18)*0.18,2),
          "total"             =>$this->input->post("total",true),
          "tcambio"           =>$this->input->post("tcambio",true),
          "condicion"         =>$this->input->post("tpago",true),
          "dadicional"        =>$this->input->post("dadicional",true),
        );
        $actualizar=$this->compra_model->update($data,$id);
        $control_movimiento=$this->movimientos('compra/guardar','Actualizo compra con documento '.$this->input->post("serie",true).'-'.$this->input->post("numero",true));

        $mensaje='Los datos se han actualizado exitosamente!';
        $url=base_url().'gasto';
      } else {
        $consulta=$this->compra_model->contador(array("nulo"=>0,"idproveedor"=>$this->input->post("idproveedor",true),"serie"=>$this->input->post("serie",true),"numero"=>$this->input->post("numero",true)));
        if ($consulta==0) {
          $data=array
          (
            "idestablecimiento" =>$this->session->userdata("predeterminado"),
            "iduser"            =>$this->session->userdata('id'),
            "tipo"              =>'G',
            "femision"          =>$this->input->post("fecha",true),
            "moneda"            =>$this->input->post("moneda",true),
            "comprobante"       =>$this->input->post("comprobante",true),
            "serie"             =>$this->input->post("serie",true),
            "numero"            =>$this->input->post("numero",true),
            "idproveedor"       =>$this->input->post("idproveedor",true),
            "proveedor"         =>$this->input->post("proveedor",true),
            "incluye"           =>1,
            'tgravado'          =>round($this->input->post("total",true)/1.18,2),
            'tinafecto'         =>0,
            'texonerado'        =>0,
            'tgratuito'         =>0,
            "subtotal"          =>round($this->input->post("total",true)/1.18,2),
            "igv"               =>round(($this->input->post("total",true)/1.18)*0.18,2),
            "total"             =>$this->input->post("total",true),
            "tcambio"           =>$this->input->post("tcambio",true),
            "condicion"         =>$this->input->post("tpago",true),
            "dadicional"        =>$this->input->post("dadicional",true),
          );
          if ($this->input->post("tpago",true)==1) {
            $data["cancelado"]=1;
          }
          $insertar=$this->compra_model->insert($data);

          if ($this->input->post('tpago',true)==1) {
            $datap=array
            (
              "idestablecimiento" =>$this->session->userdata("predeterminado"),
              "iduser"            =>$this->session->userdata("id"),
              "idcompra"          =>$insertar,
              "femision"          =>date("Y-m-d"),
              "total"             =>$this->input->post("total",true),
              "tcambio"           =>$this->input->post("tcambio",true),
              "idtpago"           =>$this->input->post("mpago",true),
            );
            $insertarp=$this->pago_model->insert($datap);
          }
          $control_movimiento=$this->movimientos('compra/guardar','Registro compra con documento '.$this->input->post("serie",true).'-'.$this->input->post("numero",true));

          $mensaje='Los datos se han guardado exitosamente!';
          $url=base_url().'gasto';
        } else {
          $mensaje='El comprobante ya existe';
        }
      }

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
    }
  }

  public function gastoa($id)
  {
    $controlip=$this->controlip('gasto/gastoa');
    $datos=$this->compra_model->mostrar($id);
    if ($datos->nulo==0) {
      $datap=array
      (
        "nulo"    =>1,
        "total" =>"0.00",
      );
      $actualizap=$this->pago_model->update($datap,array("idcompra"=>$id));

      $data=array
      (
        "nulo"        =>1,
        "subtotal"    =>"0.00",
        "igv"         =>"0.00",
        "total"       =>"0.00",
      );
      $actualiza=$this->compra_model->update($data,$id);
      $control_movimiento=$this->movimientos('gasto/gastoa','Anulo gasto con documento '.$datos->serie.'-'.$datos->numero);
      echo "borrado";
    }else{
      echo "no borrado";
    }
  }

  public function pagos($id)
  {
    $datos=$this->compra_model->mostrar($id);
    $listas=$this->pago_model->mostrarTotal(array("idcompra"=>$id));
    $this->layout->setLayout("blanco");
    $this->layout->view("pagos",compact("datos","listas"));
  }

  public function consulta($id)
  {
    $datos=$this->compra_model->mostrar($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("consulta",compact("datos"));
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






}

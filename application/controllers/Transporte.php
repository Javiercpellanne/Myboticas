<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporte extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(43)){redirect(base_url()."inicio");}
    if (!is_numeric($this->session->userdata('codigo'))){redirect(base_url().'inicio');}
    $this->layout->setLayout("principal");
    $this->load->model("tidentidad_model");
    $this->load->model("transporte_model");
  }

  public function index()
  {
    $controlip=$this->controlip('transporte');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->transporte_model->mostrarTotal('02');
    $this->layout->setTitle("Transporte Privado");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function privadoi($id=null)
  {
    $controlip=$this->controlip('transporte/privadoi');
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array
        (
          "tdocumento"  =>$this->input->post("tdocumento",true),
          "documento"   =>$this->input->post("documento",true),
          "nombres"     =>$this->input->post("nombres",true),
          "placa"       =>$this->input->post("placa",true),
          "licencia"    =>$this->input->post("licencia",true),
        );

        $guardar=$this->transporte_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $data=array
        (
          "idttransporte" =>'02',
          "tdocumento"    =>$this->input->post("tdocumento",true),
          "documento"     =>$this->input->post("documento",true),
          "nombres"       =>$this->input->post("nombres",true),
          "placa"         =>$this->input->post("placa",true),
          "licencia"      =>$this->input->post("licencia",true),
        );

        $insertar=$this->transporte_model->insert($data);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      }
      echo base_url()."transporte";
      exit();
    }

    $datos=$id!=null?$this->transporte_model->mostrar($id):(object) array("tdocumento"=>1,"documento"=>'',"nombres"=>'',"placa"=>'',"licencia"=>'');
    $identidades=$this->tidentidad_model->mostrarTotal();
    $this->layout->setLayout("blanco");
    $this->layout->view("privadoi",compact('datos',"identidades"));
  }

  public function publico()
  {
    $controlip=$this->controlip('transporte/publico');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->transporte_model->mostrarTotal('01');
    $this->layout->setTitle("Transporte Publico");
    $this->layout->view("publico",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function publicoi($id=null)
  {
    $controlip=$this->controlip('transporte/publicoi');
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array
        (
          "tdocumento"  =>$this->input->post("tdocumento",true),
          "documento"   =>$this->input->post("documento",true),
          "nombres"     =>$this->input->post("nombres",true),
        );

        $guardar=$this->transporte_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $data=array
        (
          "idttransporte" =>'01',
          "tdocumento"    =>$this->input->post("tdocumento",true),
          "documento"     =>$this->input->post("documento",true),
          "nombres"       =>$this->input->post("nombres",true),
        );

        $insertar=$this->transporte_model->insert($data);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      }
      echo base_url()."transporte/publico";
      exit();
    }

    $datos=$id!=null?$this->transporte_model->mostrar($id):(object) array("tdocumento"=>6,"documento"=>'',"nombres"=>'');
    $identidades=$this->tidentidad_model->mostrarTotal();
    $this->layout->setLayout("blanco");
    $this->layout->view("publicoi",compact('datos',"identidades"));
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

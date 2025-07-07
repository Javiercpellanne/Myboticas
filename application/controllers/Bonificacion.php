<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bonificacion extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(35)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("mes_model");
    $this->load->model("bonificado_model");
  }

  public function index()
  {
    $controlip=$this->controlip('bonificacion');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $anua=$this->input->post('canuo',true)!=null ? $this->input->post('canuo',true) : date("Y");
    $anuos=$this->periodo_model->mostrarTotal();
    $listas=$this->bonificado_model->mostrarLimite($anua);
    $this->layout->setTitle("Producto Bonificados");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"anua","anuos","listas"));
  }

  public function busBonificacion()
  {
    if ($this->input->post())
    {
      $datos=$this->bonificado_model->contador(array("anuo"=>$this->input->post("a",true),"mes"=>$this->input->post("m",true)));
      echo json_encode($datos);
    }
    else
    {
      show_404();
    }
  }

  public function bonificacioni($year=null,$month=null)
  {
    $controlip=$this->controlip('bonificacion/bonificacioni');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    if ($this->input->post())
    {
      if ($this->input->post("idproducto",true)==null) {
        $this->session->set_flashdata("css", "danger");
        $this->session->set_flashdata("mensaje", "No envio productos en la solicitud compra!");
      } else {
        for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
          $datad=array
          (
            "iduser"            =>$this->session->userdata('id'),
            "anuo"              =>$this->input->post("canuo",true),
            "mes"               =>$this->input->post("cmes",true),
            "idproducto"        =>$this->input->post("idproducto",true)[$i],
            "descripcion"       =>$this->input->post("descripcion",true)[$i],
            "monto"             =>$this->input->post("monto",true)[$i],
          );
          $insertard=$this->bonificado_model->insert($datad);
        }

        $this->session->set_flashdata("css", "success");
        $this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
        $control_movimiento=$this->movimientos('bonificacion/bonificacioni','Registro bonificacion de '.$this->input->post("canuo",true).'-'.$this->input->post("cmes",true));
      }
      redirect(base_url()."bonificacion/bonificacioni/".$this->input->post("canuo",true)."/".$this->input->post("cmes",true));
    }

    $anuos=$this->periodo_model->mostrarTotal();
    $meses=$this->mes_model->mostrarTotal();
    $productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
    $datos=$this->mes_model->mostrar($month);
    $listas=$this->bonificado_model->mostrarTotal(array("anuo"=>$year,"mes"=>$month));
    $this->layout->setLayout("contraido");
    $this->layout->setTitle("Producto Bonificados");
    $this->layout->view("bonificacioni",compact("anexos","nestablecimiento",'empresa',"anuos","meses","productos","datos","listas","year","month"));
  }

  public function bonificacionc($year,$month)
  {
    $controlip=$this->controlip('bonificacion/bonificacionc');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    if ($this->input->post())
    {
      if ($this->input->post("idproducto",true)==null) {
        $this->session->set_flashdata("css", "danger");
        $this->session->set_flashdata("mensaje", "No envio productos en la solicitud compra!");
      } else {
        for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
          $datad=array
          (
            "iduser"            =>$this->session->userdata('id'),
            "anuo"              =>$this->input->post("canuo",true),
            "mes"               =>$this->input->post("cmes",true),
            "idproducto"        =>$this->input->post("idproducto",true)[$i],
            "descripcion"       =>$this->input->post("descripcion",true)[$i],
            "monto"             =>$this->input->post("monto",true)[$i],
          );
          $insertard=$this->bonificado_model->insert($datad);
        }

        $this->session->set_flashdata("css", "success");
        $this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
        $control_movimiento=$this->movimientos('bonificacion/bonificacionc','Registro bonificacion de '.$this->input->post("canuo",true).'-'.$this->input->post("cmes",true));
      }
      redirect(base_url()."bonificacion/bonificacioni/".$this->input->post("canuo",true)."/".$this->input->post("cmes",true));
    }

    $anuos=$this->periodo_model->mostrarTotal();
    $meses=$this->mes_model->mostrarTotal();
    $productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
    $datos=$this->mes_model->mostrar($month);
    $listas=$this->bonificado_model->mostrarTotal(array("anuo"=>$year,"mes"=>$month));
    $this->layout->setLayout("contraido");
    $this->layout->setTitle("Producto Bonificados");
    $this->layout->view("bonificacionc",compact("anexos","nestablecimiento",'empresa',"anuos","meses","productos","datos","listas","year","month"));
  }

  public function bonificaciond($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('bonificacion/bonificaciond');
    $datos=$this->bonificado_model->mostrar(array("id"=>$id));
    if ($datos==NULL) {show_404();}
    $eliminar=$this->bonificado_model->delete($id);

    $this->session->set_flashdata("css", "danger");
    $this->session->set_flashdata("mensaje", "Los datos fueron eliminados exitosamente!");
    $control_movimiento=$this->movimientos('bonificacion/bonificaciond','Elimino bonificacion de '.$datos->anuo.'-'.$datos->mes);
    redirect(base_url()."bonificacion/bonificacioni/".$datos->anuo."/".$datos->mes);
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

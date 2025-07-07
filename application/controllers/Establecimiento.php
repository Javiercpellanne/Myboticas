<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Establecimiento extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    $this->layout->setLayout("principal");
    $this->load->model("departamento_model");
    $this->load->model("provincia_model");
    $this->load->model("distrito_model");
    $this->load->model("serie_model");
  }

  public function index()
  {
    if (!$this->acciones(23)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('establecimiento');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
    $this->layout->setTitle("Establecimientos");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function inactivos()
  {
    if (!$this->acciones(23)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('establecimiento');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->establecimiento_model->mostrarTotal(array('estado'=>0));
    $this->layout->setTitle("Establecimientos");
    $this->layout->view("inactivos",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function establecimientoi($id=null)
  {
    $controlip=$this->controlip('establecimiento/establecimientoi');
    $empresa=$this->empresa_model->mostrar();
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array
        (
          "codigo"      =>$this->input->post("codigo",true),
          "descripcion"   =>$this->input->post("descripcion",true),
          "iddepartamento"  =>$this->input->post("departamento",true),
          "idprovincia"   =>$this->input->post("provincia",true),
          "iddistrito"    =>$this->input->post("distrito",true),
          "direccion"     =>$this->input->post("direccion",true),
          "telefono"      =>$this->input->post("telefono",true),
          "email"       =>$this->input->post("email",true),
          "cdigemid"      =>$this->input->post("cdigemid",true),
        );
        $guardar=$this->establecimiento_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        $control_movimiento=$this->movimientos('establecimiento/establecimientoi','Edito el establecimiento nro '.$id);
        echo base_url()."establecimiento";
      } else {
        $data=array
        (
          "codigo"      =>$this->input->post("codigo",true),
          "descripcion"   =>$this->input->post("descripcion",true),
          "iddepartamento"  =>$this->input->post("departamento",true),
          "idprovincia"   =>$this->input->post("provincia",true),
          "iddistrito"    =>$this->input->post("distrito",true),
          "direccion"     =>$this->input->post("direccion",true),
          "telefono"      =>$this->input->post("telefono",true),
          "email"       =>$this->input->post("email",true),
          "cdigemid"      =>$this->input->post("cdigemid",true),
        );
        $insertar=$this->establecimiento_model->insert($data);

        $catalogos=$this->producto_model->mostrarCatalogo();
        foreach ($catalogos as $catalogo) {
          $datac=array
          (
            'idestablecimiento' =>$insertar,
            'idproducto'        =>$catalogo->id,
            'stock'             =>0,
          );
          $insertark=$this->inventario_model->insert($datac);
        }

        $this->session->set_flashdata("css", "success");
        $this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
        echo base_url()."serie/index/".$insertar;
      }

      $nro=$id==null ? $insertar: $id;
      if (isset($_FILES['logo']) && $_FILES['logo']['tmp_name']!='') {
        $nombreCompleto=$_FILES['logo']['name'];

        $config['upload_path']   = './public/logo/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2000;
        $config['max_width']     = 0;
        $config['max_height']    = 0;
        $config['file_name']     = $nombreCompleto;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('logo')) {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', $this->upload->display_errors());
        } else {
          $ruta= addslashes(base_url()."public/logo/".$nombreCompleto);
          $datai=array('logoe'=>$ruta);
          $imagen=$this->upload->data();
          $guardar=$this->establecimiento_model->update($datai,$nro);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Se subio con exito la portada '.$imagen["file_name"]);
        }
      }

      if (isset($_FILES['lticket']) && $_FILES['lticket']['tmp_name']!='') {
        $nombreCompleto=$_FILES['lticket']['name'];

        $config['upload_path']   = './public/logo/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size']      = 2000;
        $config['max_width']     = 0;
        $config['max_height']    = 0;
        $config['file_name']     = $nombreCompleto;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('lticket')) {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', $this->upload->display_errors());
        } else {
          $ruta= addslashes(base_url()."public/logo/".$nombreCompleto);
          $datai=array('logot'=>$ruta,);
          $imagen=$this->upload->data();
          $guardar=$this->establecimiento_model->update($datai,$nro);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Se subio con exito logo '.$imagen["file_name"]);
        }
      }
      exit();
    }

    $datos=$id!=null?$this->establecimiento_model->mostrar($id):(object) array("codigo"=>'',"descripcion"=>'',"iddepartamento"=>'',"idprovincia"=>'',"iddistrito"=>'',"direccion"=>'',"email"=>'',"telefono"=>'',"cdigemid"=>'');
    $departamentos=$this->departamento_model->mostrarTotal();
    $provincias=$this->provincia_model->mostrarTotal($datos->iddepartamento);
    $distritos=$this->distrito_model->mostrarTotal($datos->idprovincia);
    $this->layout->setLayout("blanco");
    $this->layout->view("establecimientoi",compact('empresa',"datos","departamentos","provincias","distritos"));
  }

  public function busProvincia()
  {
    if ($this->input->post())
    {
      $datos=$this->provincia_model->mostrarTotal($this->input->post("id",true));
      echo json_encode($datos);
    }
    else
    {
      show_404();
    }
  }

  public function busDistrito()
  {
    if ($this->input->post())
    {
      $datos=$this->distrito_model->mostrarTotal($this->input->post("id",true));
      echo json_encode($datos);
    }
    else
    {
      show_404();
    }
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

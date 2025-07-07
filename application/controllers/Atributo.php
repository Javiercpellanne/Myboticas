<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Atributo extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(20)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("categoria_model");
    $this->load->model("laboratorio_model");
    $this->load->model("pactivo_model");
    $this->load->model("aterapeutica_model");
    $this->load->model("ubicacion_model");
  }

  public function index()
  {
    $controlip=$this->controlip('atributo');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->categoria_model->mostrarTotal('F');
    $this->layout->setTitle("Categoria");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function categoriai($id=null)
  {
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array('descripcion' =>$this->input->post('descripcion',true));
        $guardar=$this->categoria_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->categoria_model->contador($this->input->post('descripcion',true));
        if ($consulta==0) {
          $data=array(
            'tipo'       =>'F',
            'descripcion'=>$this->input->post('descripcion',true)
          );
          $insertar=$this->categoria_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'La categoria ya EXISTE!!!!!!');
        }
      }
      echo base_url()."atributo";
      exit();
    }

    $datos=$id!=null?$this->categoria_model->mostrar($id):(object) array("descripcion"=>'');
    $this->layout->setLayout("blanco");
    $this->layout->view("atributoi",compact('datos'));
  }

  public function categoriad($id)
  {
    if (!$id) {show_404();}
    $datos=$this->categoria_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $contador=$this->producto_model->contador(array("idcategoria"=>$id));
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->categoria_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'atributo';
    echo json_encode($proceso);
    exit();
  }

  public function laboratorio()
  {
    $controlip=$this->controlip('laboratorio');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->laboratorio_model->mostrarTotal();
    $this->layout->setTitle("Laboratorio");
    $this->layout->view("laboratorio",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function laboratorioi($id=null)
  {
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array('descripcion'=>trim(mb_strtoupper($this->input->post('descripcion',true), 'UTF-8')));
        $guardar=$this->laboratorio_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->laboratorio_model->contador($this->input->post('descripcion',true));
        if ($consulta==0) {
          $data=array('descripcion'=>trim(mb_strtoupper($this->input->post('descripcion',true), 'UTF-8')));
          $insertar=$this->laboratorio_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'El laboratorio ya EXISTE!!!!!!');
        }
      }
      echo base_url()."atributo/laboratorio";
      exit();
    }

    $datos=$id!=null?$this->laboratorio_model->mostrar($id):(object) array("descripcion"=>'');
    $this->layout->setLayout("blanco");
    $this->layout->view("atributoi",compact('datos'));
  }

  public function laboratoriod($id)
  {
    if (!$id) {show_404();}
    $datos=$this->laboratorio_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $contador=$this->producto_model->contador(array("idlaboratorio"=>$id));
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->laboratorio_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'atributo/laboratorio';
    echo json_encode($proceso);
    exit();
  }

  public function pactivo()
  {
    $controlip=$this->controlip('pactivo');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->pactivo_model->mostrarTotal();
    $this->layout->setTitle("Principio Activo");
    $this->layout->view("pactivo",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function pactivoi($id=null)
  {
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array('descripcion'=>$this->input->post('descripcion',true));
        $guardar=$this->pactivo_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->pactivo_model->contador($this->input->post('descripcion',true));
        if ($consulta==0) {
          $data=array('descripcion'=>$this->input->post('descripcion',true));
          $insertar=$this->pactivo_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'El principo activo ya EXISTE!!!!!!');
        }
      }
      echo base_url()."atributo/pactivo";
      exit();
    }

    $datos=$id!=null?$this->pactivo_model->mostrar($id):(object) array("descripcion"=>'');
    $this->layout->setLayout("blanco");
    $this->layout->view("atributoi",compact('datos'));
  }

  public function pactivod($id)
  {
    if (!$id) {show_404();}
    $datos=$this->pactivo_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $contador=$this->producto_model->contador(array("idpactivo"=>$id));
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->pactivo_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'atributo/pactivo';
    echo json_encode($proceso);
    exit();
  }

  public function aterapeutica()
  {
    $controlip=$this->controlip('aterapeutica');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->aterapeutica_model->mostrarTotal();
    $this->layout->setTitle("Accion Terapeutica");
    $this->layout->view("aterapeutica",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function aterapeuticai($id=null)
  {
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array('descripcion'=>$this->input->post('descripcion',true));
        $guardar=$this->aterapeutica_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->aterapeutica_model->contador($this->input->post('descripcion',true));
        if ($consulta==0) {
          $data=array('descripcion'=>$this->input->post('descripcion',true));
          $insertar=$this->aterapeutica_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'La accion terapeutica ya EXISTE!!!!!!');
        }
      }
      echo base_url()."atributo/aterapeutica";
      exit();
    }

    $datos=$id!=null?$this->aterapeutica_model->mostrar($id):(object) array("descripcion"=>'');
    $this->layout->setLayout("blanco");
    $this->layout->view("atributoi",compact('datos'));
  }

  public function aterapeuticad($id)
  {
    if (!$id) {show_404();}
    $datos=$this->aterapeutica_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $contador=$this->producto_model->contador(array("idaterapeutica"=>$id));
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->aterapeutica_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'atributo/aterapeutica';
    echo json_encode($proceso);
    exit();
  }

  public function ubicacion()
  {
    $controlip=$this->controlip('ubicacion');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->ubicacion_model->mostrarTotal();
    $this->layout->setTitle("Ubicacion");
    $this->layout->view("ubicacion",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function ubicacioni($id=null)
  {
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array('descripcion'=>$this->input->post('descripcion',true));
        $guardar=$this->ubicacion_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->ubicacion_model->contador($this->input->post('descripcion',true));
        if ($consulta==0) {
          $data=array('descripcion'=>$this->input->post('descripcion',true));
          $insertar=$this->ubicacion_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'La accion terapeutica ya EXISTE!!!!!!');
        }
      }
      echo base_url()."atributo/ubicacion";
      exit();
    }

    $datos=$id!=null?$this->ubicacion_model->mostrar($id):(object) array("descripcion"=>'');
    $this->layout->setLayout("blanco");
    $this->layout->view("atributoi",compact('datos'));
  }

  public function ubicaciond($id)
  {
    if (!$id) {show_404();}
    $datos=$this->ubicacion_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $contador=$this->producto_model->contador(array("idubicacion"=>$id));
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->ubicacion_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'atributo/ubicacion';
    echo json_encode($proceso);
    exit();
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

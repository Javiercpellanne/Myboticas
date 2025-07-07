<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Servicio extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(42)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("tafectacion_model");
    $this->load->model("categoria_model");
    $this->load->model("punto_model");
    $this->load->model("nventa_model");
    $this->load->model("venta_model");
  }

  public function index()
  {
    $controlip=$this->controlip('servicio');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $listas=$this->producto_model->mostrarLimite(array("tipo"=>'S',"estado"=>1));
    $detalles=$this->producto_model->mostrarTotal(array("tipo"=>'S',"estado"=>1));
    $this->layout->setTitle("Servicio Activo");
    $this->layout->view("index",compact("anexos","nestablecimiento","empresa","listas","detalles"));
  }

  public function inactivos()
  {
    $controlip=$this->controlip('servicio/inactivos');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $listas=$this->producto_model->mostrarLimite(array("tipo"=>'S',"estado"=>0));
    $detalles=$this->producto_model->mostrarTotal(array("tipo"=>'S',"estado"=>0));
    $this->layout->setTitle("Servicio Inactivo");
    $this->layout->view("inactivos",compact("anexos","nestablecimiento","empresa","listas","detalles"));
  }

  public function servicioi($id=null)
  {
    $controlip=$this->controlip('servicio/servicioi');
    $empresa=$this->empresa_model->mostrar();
    if ($this->input->post())
    {
      if ($id!=null) {
        $data=array
        (
          'idcategoria'   =>$this->input->post('categoria',true),
          'descripcion'   =>trim(mb_strtoupper($this->input->post('descripcion',true), 'UTF-8')),
          'tafectacion'   =>$this->input->post('tafectacion',true),
        );
        if ($empresa->pestablecimiento==0) {
          $data['pventa']=$this->input->post('pventa',true);
        }
        $guardar=$this->producto_model->update($data,$id);

        if ($empresa->pestablecimiento==1) {
          $datae=array('pventa'=>$this->input->post('pventa',true));
          $actualizarp=$this->inventario_model->update($datae,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id));
        }
        $control_movimiento=$this->movimientos('servicio/servicioi','Edito servicio '.$this->input->post('descripcion',true));
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      } else {
        $consulta=$this->producto_model->contador(array("descripcion"=>$this->input->post('descripcion',true)));
        if ($consulta==0) {
          $data=array
          (
            'tipo'          =>'S',
            'idcategoria'   =>$this->input->post('categoria',true),
            'descripcion'   =>trim(mb_strtoupper($this->input->post('descripcion',true), 'UTF-8')),
            'umedidav'      =>'ZZ',
            'tafectacion'   =>$this->input->post('tafectacion',true),
            'factor'        =>1,
            'estado'        =>1,
          );
          if ($empresa->pestablecimiento==0) {
            $data['pventa']=$this->input->post('pventa',true);
          }
          $insertar=$this->producto_model->insert($data);

          $investablecimientos=$this->establecimiento_model->mostrarTotal();
          foreach ($investablecimientos as $investablecimiento) {
            $datae=array
            (
              'idestablecimiento' =>$investablecimiento->id,
              'idproducto'        =>$insertar,
              'stock'             =>0,
            );
            if ($empresa->pestablecimiento==1) {
              $datae['pventa']=$this->input->post('pventa',true);
            }
            $insertark=$this->inventario_model->insert($datae);
          }
          $control_movimiento=$this->movimientos('servicio/servicioi','Registro servicio '.$this->input->post('descripcion',true));
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'El servicio ya EXISTE!!!!!!');
        }
      }
      echo base_url()."servicio";
      exit();
    }

    $datos=$id!=null?$this->producto_model->mostrar(array("p.id"=>$id)):(object) array("idcategoria"=>'',"descripcion"=>'',"tafectacion"=>10,"pventa"=>'');
    $cantidad=$id!=null?$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$id):(object) array("pventa"=>'');
    $categorias=$this->categoria_model->mostrarTotal('F');
    $tafectaciones=$this->tafectacion_model->mostrarTotal();
    $this->layout->setLayout("blanco");
    $this->layout->view("servicioi",compact('empresa','datos','categorias','tafectaciones','cantidad'));
  }

  public function busventas($id)
  {
    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    $this->layout->setLayout("blanco");
    $this->layout->view("bventas",compact('datos',"id"));
  }

  public function habilitar($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('servicio/habilitar');
    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    if ($datos==NULL) {show_404();}

    $data=array('estado'=>1);
    $guardar=$this->producto_model->update($data,$id);
    $control_movimiento=$this->movimientos('servicio/habilitar','Habilito servicio '.$datos->descripcion);
    $this->session->set_flashdata('css', 'success');
    $this->session->set_flashdata('mensaje', 'El servicio fue habilitado!');
    redirect(base_url()."servicio");
  }

  public function deshabilitar($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('servicio/deshabilitar');
    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    if ($datos==NULL) {show_404();}

    $data=array('estado'=>0);
    $guardar=$this->producto_model->update($data,$id);
    $control_movimiento=$this->movimientos('servicio/deshabilitar','Deshabilito servicio '.$datos->descripcion);
    $this->session->set_flashdata('css', 'success');
    $this->session->set_flashdata('mensaje', 'El servicio fue deshabilitado!');
    redirect(base_url()."servicio");
  }

  public function busListado() //listado general de productos
  {
    if ($this->input->post())
    {
      $empresa=$this->empresa_model->mostrar();
      if (strlen($this->input->post('id',true))>2) {
        $productos=$this->producto_model->buscador($this->input->post('id',true),array("tipo"=>'S',"estado"=>$this->input->post('estado',true)));
      }else {
        $productos=$this->producto_model->mostrarLimite(array("tipo"=>'S',"estado"=>$this->input->post('estado',true)));
      }

      $datos=array();
      foreach ($productos as $producto) {
        $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);

        $detalle['id']=$producto->id;
        $detalle['descripcion']=$producto->descripcion;
        $detalle['pcompra']=$producto->pcompra;
        $detalle['pventa']=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
        $detalle['tipo']=$producto->tipo;
        $detalle['estado']=$producto->estado;
        array_push($datos,$detalle);
      }
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

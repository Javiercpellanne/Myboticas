<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utilitario extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(24)){redirect(base_url()."inicio");}

		$this->layout->setLayout("principal");
		$this->load->model("tpago_model");
		$this->load->model("tingreso_model");
		$this->load->model("tegreso_model");
		$this->load->model("ingreso_model");
		$this->load->model("egreso_model");
	}

	public function index()
	{
    $controlip=$this->controlip('utilitario');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->periodo_model->mostrarTotal();
		$this->layout->setTitle("Periodos");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function periodoi($id=null)
	{
    $controlip=$this->controlip('utilitario/periodoi');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array("descripcion"=>$this->input->post("descripcion",true));
				$guardar=$this->periodo_model->update($data,$id);
				$this->session->set_flashdata("css", "success");
				$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
			} else {
				$consulta=$this->periodo_model->contador($this->input->post("descripcion",true));
				if ($consulta==0) {
					$data=array("descripcion"=>$this->input->post("descripcion",true));
					$insertar=$this->periodo_model->insert($data);
					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
				} else {
					$this->session->set_flashdata("css", "danger");
					$this->session->set_flashdata("mensaje", "La periodo ya EXISTE!!!!!!");
				}
			}
			echo base_url()."utilitario";
			exit();
		}

		$datos=$id!=null?$this->periodo_model->mostrar($id):(object) array("descripcion"=>"");
		$this->layout->setLayout("blanco");
		$this->layout->view("utilitarioi",compact("datos"));
	}

	public function medio()
	{
    $controlip=$this->controlip('utilitario/medio');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->tpago_model->mostrarTotal();
		$this->layout->setTitle("Medio Pago");
		$this->layout->view("medio",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function medioi($id=null)
	{
    $controlip=$this->controlip('utilitario/medioi');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array('descripcion'=>$this->input->post('descripcion',true));
				$guardar=$this->tpago_model->update($data,$id);
				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
			} else {
				$consulta=$this->tpago_model->contador($this->input->post('descripcion',true));
				if ($consulta==0) {
					$data=array('descripcion'=>$this->input->post('descripcion',true));
					$insertar=$this->tpago_model->insert($data);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
				} else {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', 'El medio pago ya EXISTE!!!!!!');
				}
			}
			echo base_url()."utilitario/medio";
			exit();
		}

		$datos=$id!=null?$this->tpago_model->mostrar($id):(object) array("descripcion"=>'');
		$this->layout->setLayout("blanco");
		$this->layout->view("utilitarioi",compact('datos'));
	}

	public function tingreso()
	{
    $controlip=$this->controlip('utilitario/tingreso');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->tingreso_model->mostrarTotal();
		$this->layout->setTitle("Tipo Ingreso");
		$this->layout->view("tingreso",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function tingresoi($id=null)
	{
    $controlip=$this->controlip('utilitario/tingresoi');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array('descripcion'=>$this->input->post('descripcion',true));
				$guardar=$this->tingreso_model->update($data,$id);
				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
			} else {
				$consulta=$this->tingreso_model->contador($this->input->post('descripcion',true));
				if ($consulta==0) {
					$data=array('descripcion'=>$this->input->post('descripcion',true));
					$insertar=$this->tingreso_model->insert($data);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
				} else {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', 'El medio pago ya EXISTE!!!!!!');
				}
			}
			echo base_url()."utilitario/tingreso";
			exit();
		}

		$datos=$id!=null?$this->tingreso_model->mostrar($id):(object) array("descripcion"=>'');
		$this->layout->setLayout("blanco");
		$this->layout->view("utilitarioi",compact('datos'));
	}

	public function tingresod($id)
	{
    $controlip=$this->controlip('utilitario/tingresod');
		if (!$id) {show_404();}
		$datos=$this->tingreso_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$contador=$this->ingreso_model->contador('comprobante',$id);
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
			$eliminar=$this->tingreso_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'utilitario/tingreso';
    echo json_encode($proceso);
    exit();
	}

	public function tegreso()
	{
    $controlip=$this->controlip('utilitario/tegreso');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
		$establecimientos=$this->establecimiento_model->mostrarAcceso($anexos);
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->tegreso_model->mostrarTotal();
		$this->layout->setTitle("Tipo egreso");
		$this->layout->view("tegreso",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function tegresoi($id=null)
	{
    $controlip=$this->controlip('utilitario/tegresoi');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array('descripcion'=>$this->input->post('descripcion',true));
				$guardar=$this->tegreso_model->update($data,$id);
				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
			} else {
				$consulta=$this->tegreso_model->contador($this->input->post('descripcion',true));
				if ($consulta==0) {
					$data=array('descripcion'=>$this->input->post('descripcion',true));
					$insertar=$this->tegreso_model->insert($data);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
				} else {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', 'El medio pago ya EXISTE!!!!!!');
				}
			}
			echo base_url()."utilitario/tegreso";
			exit();
		}

		$datos=$id!=null?$this->tegreso_model->mostrar($id):(object) array("descripcion"=>'');
		$this->layout->setLayout("blanco");
		$this->layout->view("utilitarioi",compact('datos'));
	}

	public function tegresod($id)
	{
    $controlip=$this->controlip('utilitario/tegresod');
		if (!$id) {show_404();}
		$datos=$this->tegreso_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$contador=$this->egreso_model->contador('comprobante',$id);
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
			$eliminar=$this->tegreso_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'utilitario/tegreso';
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
				'ip'	=>$ip,
				'fecha'	=>time(),
				'tiempo'=>$tiempo,
				'nombre'=>$nomcpu,
				'soperativo'=>$info["os"],
				'navegador'=>$info["browser"],
				'dispositivo'=>$info["device"],
				'pagina'=>$pagina,
				'user'	=>$this->session->userdata('user'),
			);
			$insertar=$this->controlip_model->insertar($data);
		}else{
			$data=array
			(
				'ip'	=>$ip,
				'fecha'	=>time(),
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

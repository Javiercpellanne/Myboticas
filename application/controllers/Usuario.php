<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(22)){redirect(base_url()."inicio");}

		$this->layout->setLayout("principal");
		$this->load->model("acceso_model");
		$this->load->model("anivel_model");
		$this->load->model("controll_model");
	}

	public function index()
	{
    $controlip=$this->controlip('usuario');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Usuarios");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function inactivos()
	{
    $controlip=$this->controlip('usuario/inactivos');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->usuario_model->mostrarTotal(array('estado'=>0));
		$this->layout->setTitle("Usuarios");
		$this->layout->view("inactivos",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function conectados()
	{
    $controlip=$this->controlip('usuario/conectados');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$tiempo=date(fechaHoraria('-7 day'),time());
		$listas=$this->controlip_model->mostrarTotal($tiempo);
		$this->layout->setTitle("Usuarios Conectados");
		$this->layout->view("conectados",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function movimientos()
	{
    $controlip=$this->controlip('usuario/movimientos');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$fecha=date("Y-m-d").' 00:00:00';
		//$limpiar=$this->controlm_model->delete($fecha);
		$listas=$this->controlm_model->mostrarTotal();
		$this->layout->setTitle("Usuarios Movimientos");
		$this->layout->view("movimientos",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function logines()
	{
    $controlip=$this->controlip('usuario/logines');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$tiempo=date(fechaHoraria('-7 day'),time());
		$listas=$this->controll_model->mostrarTotal($tiempo);
		$this->layout->setTitle("Usuarios Login");
		$this->layout->view("logines",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function usuariosi($id=null)
	{
    $controlip=$this->controlip('usuario/usuariosi');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array
				(
					'nombres'						=>$this->input->post('nombres',true),
					'idestablecimiento'	=>implode(',',$this->input->post('establecimiento',true)),
					'perfil'							=>$this->input->post('perfil',true),
					'anulacion'					=>$this->input->post('anulacion',true),
				);

				$guardar=$this->usuario_model->update($data,$id);
				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
			} else {
				$consulta=$this->usuario_model->contador($this->input->post('usuario',true));
				if ($consulta==0) {
					if ($this->input->post('clave',true)==$this->input->post('clavn',true)) {
						$data=array
						(
							'nombres'						=>$this->input->post('nombres',true),
							'idestablecimiento'	=>implode(',',$this->input->post('establecimiento',true)),
							'perfil'						=>$this->input->post('perfil',true),
							'usuario'						=>$this->input->post('usuario',true),
							'clave'							=>$this->input->post('clave',true),
							'anulacion'					=>$this->input->post('anulacion',true),
							'tipo'							=>'F',
							'estado'						=>1,
						);

						$insertar=$this->usuario_model->insert($data);
						$this->session->set_flashdata('css', 'success');
						$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
					} else {
						$this->session->set_flashdata('css', 'danger');
						$this->session->set_flashdata('mensaje', 'No se repitio bien la contraseña!');
					}
				} else {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', 'El usuario ya EXISTE!!!!!!');
				}
			}
			echo base_url()."usuario";
			exit();
		}

		$establecimientos=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
		$datos=$id!=null?$this->usuario_model->mostrar($id):(object) array("nombres"=>'', "idestablecimiento"=>1, "perfil"=>'','anulacion'=>0);
		$this->layout->setLayout("blanco");
		$this->layout->view("usuariosi",compact("datos","establecimientos","id"));
	}

	public function usuariosr($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('usuario/usuariosr');
		$datos=$this->usuario_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$data=array('clave'=>$datos->usuario);
		$guardar=$this->usuario_model->update($data,$id);
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'La contraseña fue restaurada!');

		redirect(base_url()."usuario");
	}

	public function habilitar($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('usuario/habilitar');
		$datos=$this->usuario_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$data=array('estado'=>1);
		$actualizar=$this->usuario_model->update($data,$id);
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'El usuario fue habilitado!');
		redirect(base_url()."usuario");
	}

	public function deshabilitar($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('usuario/deshabilitar');
		$datos=$this->usuario_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$data=array('estado'=>0);
		$actualizar=$this->usuario_model->update($data,$id);
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'El usuario fue deshabilitado!');
		redirect(base_url()."usuario");
	}

	public function usuariosc()
	{
    $controlip=$this->controlip('usuario/usuariosc');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		if ($this->input->post())
		{
			$datos=$this->usuario_model->mostrar($this->session->userdata("id"));
			if ($datos->clave==$this->input->post('anterior',true)) {
				$data=array('clave'=>$this->input->post('posterior',true));

				$guardar=$this->usuario_model->update($data,$this->session->userdata("id"));
				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'La contraseña fue cambiada!');
			} else {
				$this->session->set_flashdata('css', 'danger');
				$this->session->set_flashdata('mensaje', 'No se ingreso bien la contraseña actual!');
			}
		}

		$this->layout->setTitle("Usuarios");
		$this->layout->view("usuariosc",compact("anexos","nestablecimiento",'empresa'));
	}

	public function acceso($id)
	{
    $controlip=$this->controlip('usuario/acceso');
		if ($this->input->post())
		{
			$eliminarm=$this->ausuario_model->delete($id);
			for($i = 0; $i < count($this->input->post('menu',true)); $i++) {
				$data=array
				(
					'idacceso'	=>$this->input->post('menu',true)[$i],
					'iduser'	=>$id,
				);
				//var_dump($data); echo "<br>";
				$insertar=$this->ausuario_model->insert($data);
			}

			$eliminars=$this->anusuario_model->delete($id);
			for($i = 0; $i < count($this->input->post('submenu',true)); $i++) {
				$datas=array
				(
					'idacceson'	=>$this->input->post('submenu',true)[$i],
					'iduser'	=>$id,
				);
				//var_dump($datas); echo "<br>";
				$insertars=$this->anusuario_model->insert($datas);
			}

			$this->session->set_flashdata('css', 'success');
			$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
			echo base_url()."usuario";
			exit();
		}

		$empresa=$this->empresa_model->mostrar();
		if ($empresa->facturacion==1) {
			$datos=$this->acceso_model->mostrarTotal();
		} else {
			$datos=$this->acceso_model->mostrarLimite();
		}
		$establecimientos=$this->establecimiento_model->contador();
		$this->layout->setLayout("blanco");
		$this->layout->view("acceso",compact('datos','id','empresa','establecimientos'));
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

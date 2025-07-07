<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->layout->setLayout('login');
		$this->load->model("controll_model");
	}

	public function index()
	{
		if ($this->session->userdata('login')) {
			redirect(base_url('inicio'));
		}
		else{
			$this->layout->view('index');
		}
	}

	public function acceso()
	{
		if($this->input->post())
		{
			$datos=$this->usuario_model->login($this->input->post('usuario',true),$this->input->post('clave',true));
			if($datos==NULL)
			{
				$this->session->set_flashdata('css', 'danger');
				$this->session->set_flashdata('mensaje', 'El usuario y/o contraseña son incorrectos');
				redirect(base_url());
			}else{
				$empresa=$this->empresa_model->mostrar();
				$establecimientos=explode(',', $datos->idestablecimiento);
				$idestablecimiento=$establecimientos[0];
				$nestablecimiento=$this->establecimiento_model->mostrar($idestablecimiento);

				if ($empresa->time!='') {
					//echo $empresa->time; exit();
					if ($empresa->time>=time()) {
						$data = array(
							'id' 								=> $datos->id,
							'nombre' 						=> $datos->nombres,
							'user' 							=> $datos->usuario,
							'tipo' 							=> $datos->perfil,
							'establecimientos'	=> $datos->idestablecimiento,
							'predeterminado' 		=> $idestablecimiento,
							'codigo' 						=> $nestablecimiento->codigo,
							'login' 						=> TRUE
						);
						$this->session->set_userdata($data);
						$controlip=$this->controlip('login');
						redirect(base_url().'inicio');
					} else {
						$this->session->set_flashdata('css', 'danger');
						$this->session->set_flashdata('mensaje', 'La vigencia del sistema ya caduco');
						redirect(base_url());
					}
				}else{
					$data = array(
						'id' 								=> $datos->id,
						'nombre' 						=> $datos->nombres,
						'user' 							=> $datos->usuario,
						'tipo' 							=> $datos->perfil,
						'establecimientos'	=> $datos->idestablecimiento,
						'predeterminado' 		=> $idestablecimiento,
						'codigo' 						=> $nestablecimiento->codigo,
						'login' 						=> TRUE
					);
					$this->session->set_userdata($data);
					$controlip=$this->controlip('login');
					redirect(base_url().'inicio');
				}
			}
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url());
	}

	public function controlip($pagina)
	{
		$nomcpu=gethostbyaddr($_SERVER["REMOTE_ADDR"]);
		$ip = $_SERVER["REMOTE_ADDR"];
  	$info=$this->detectar();
  	$tiempo = date('Y-m-d H:i:s',time());
  	$limite = time()-5*60;  //borrando los registros de las ip inactivas (5 minutos)

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
		$insertar=$this->controll_model->insertar($data);
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

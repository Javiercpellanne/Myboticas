<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(5)){redirect(base_url()."inicio");}

		$this->layout->setLayout("contraido");
		$this->load->model("departamento_model");
		$this->load->model("provincia_model");
		$this->load->model("distrito_model");
		$this->load->model("proveedor_model");
		$this->load->model("compra_model");
		$this->load->model("egreso_model");
	}

	public function index()
	{
    $controlip=$this->controlip('proveedor');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->proveedor_model->mostrarTotal();
		$this->layout->setTitle("Proveedor");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function proveedori($id=null)
	{
    $controlip=$this->controlip('proveedor/proveedori');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array
				(
					"tdocumento"	=>$this->input->post("tipo",true),
					"documento"		=>$this->input->post("documento",true),
					"nombres"		=>trim(mb_strtoupper($this->input->post('nombres',true), 'UTF-8')),
					"iddepartamento"	=>$this->input->post("departamento",true),
					"idprovincia"		=>$this->input->post("provincia",true),
					"iddistrito"		=>$this->input->post("distrito",true),
					"direccion"		=>$this->input->post("direccion",true),
					"telefono"		=>$this->input->post("telefono",true),
					"email"			=>$this->input->post("email",true),
				);

				$guardar=$this->proveedor_model->update($data,$id);
				$this->session->set_flashdata("css", "success");
				$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
				$control_movimiento=$this->movimientos('proveedor/proveedori','Edito al proveedor '.$this->input->post('nombres',true));
			} else {
				$consulta=$this->proveedor_model->contador($this->input->post("dni",true));
				if ($consulta==0) {
					$data=array
					(
						"tdocumento"	=>$this->input->post("tipo",true),
						"documento"		=>$this->input->post("documento",true),
						"nombres"		=>trim(mb_strtoupper($this->input->post('nombres',true), 'UTF-8')),
						"iddepartamento"	=>$this->input->post("departamento",true),
						"idprovincia"		=>$this->input->post("provincia",true),
						"iddistrito"		=>$this->input->post("distrito",true),
						"direccion"		=>$this->input->post("direccion",true),
						"telefono"		=>$this->input->post("telefono",true),
						"email"			=>$this->input->post("email",true),
					);

					$insertar=$this->proveedor_model->insert($data);
					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
					$control_movimiento=$this->movimientos('proveedor/proveedori','Registro al proveedor '.$this->input->post('nombres',true));
				} else {
					$this->session->set_flashdata("css", "danger");
					$this->session->set_flashdata("mensaje", "El proveedor ya EXISTE!!!!!!");
				}
			}
			echo base_url()."proveedor";
			exit();
		}

		$datos=$id!=null?$this->proveedor_model->mostrar(array("p.id"=>$id)):(object) array("tdocumento"=>"", "nombres"=>"", "documento"=>"", "iddepartamento"=>"", "idprovincia"=>"", "iddistrito"=>"", "direccion"=>"", "telefono"=>"", "email"=>"");
		$departamentos=$this->departamento_model->mostrarTotal();
		$provincias=$id!=null?$this->provincia_model->mostrarTotal($datos->iddepartamento):null;
		$distritos=$id!=null?$this->distrito_model->mostrarTotal($datos->idprovincia):null;
		$this->layout->setLayout("blanco");
		$this->layout->view("proveedori",compact("datos","departamentos","provincias","distritos"));
	}

	public function proveedord($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('proveedor/proveedord');
		$datos=$this->proveedor_model->mostrar(array("p.id"=>$id));
		if ($datos==NULL) {show_404();}

		$contadorn=$this->compra_model->contador(array('idproveedor'=>$id));
		$contadorg=$this->egreso_model->contador('idproveedor',$id);
		$contador=$contadorn+$contadorg;
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
			$eliminar=$this->proveedor_model->delete($id);
			$control_movimiento=$this->movimientos('proveedor/proveedord','Elimino al proveedor '.$datos->nombres);

      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'proveedor';
    echo json_encode($proceso);
    exit();
	}

  public function busProveedor()
  {
    if ($this->input->post())
    {
      if (strlen($this->input->post('id',true))>2) {
        $datos=$this->proveedor_model->buscador($this->input->post('id',true));
      } else {
        $datos=$this->proveedor_model->mostrarLimite();
      }
      echo json_encode($datos);
    }
    else
    {
      show_404();
    }
  }

  public function buscador()
  {
    $datos=$this->proveedor_model->mostrarLimite();
    $this->layout->setLayout("blanco");
    $this->layout->view("buscador",compact("datos"));
  }

	public function movimientos($pagina,$descripcion)
	{
		$tiempo = date('Y-m-d H:i:s',time());
		$data=array
		(
			'user'				=>$this->session->userdata('user'),
			'descripcion'	=>$descripcion,
			'tiempo'			=>$tiempo,
			'pagina'			=>$pagina,
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

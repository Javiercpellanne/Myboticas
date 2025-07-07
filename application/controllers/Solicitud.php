<?php
defined("BASEPATH") OR exit('No direct script access allowed');

class Solicitud extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(3)){redirect(base_url()."inicio");}

		$this->layout->setLayout("principal");
		$this->load->model("proveedor_model");
		$this->load->model("solicitud_model");
		$this->load->model("solicitudd_model");
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    $controlip=$this->controlip('solicitud');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
		$fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d") ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$listas=$this->solicitud_model->mostrarTotal($filtros);
		$this->layout->setLayout("contraido");
		$this->layout->setTitle("Solicitud Compra Producto");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
	}

	public function solicitudi()
	{
    $controlip=$this->controlip('solicitud/solicitudi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		if ($this->input->post())
		{
			if ($this->input->post("idproducto",true)==null) {
				$this->session->set_flashdata("css", "danger");
				$this->session->set_flashdata("mensaje", "No envio productos en la solicitud compra!");
			} else {
				$data=array
				(
					"idestablecimiento"	=>$this->session->userdata("predeterminado"),
					"iduser"						=>$this->session->userdata('id'),
					"femision"					=>$this->input->post("fecha",true),
					"idproveedor"				=>$this->input->post("idproveedor",true),
					"proveedor"					=>$this->input->post("proveedor",true),
					"estado"						=>1,
				);
				$insertar=$this->solicitud_model->insert($data);

				for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
					$datad=array
					(
						"idsolicitud"		=>$insertar,
						"idproducto"		=>$this->input->post("idproducto",true)[$i],
						"descripcion"		=>trim($this->input->post("descripcion",true)[$i]),
						"unidad"				=>$this->input->post("unidad",true)[$i],
						"factor"				=>$this->input->post("factor",true)[$i],
						"cantidad"			=>$this->input->post("cantidad",true)[$i],
					);
					$insertard=$this->solicitudd_model->insert($datad);
				}
				$control_movimiento=$this->movimientos('solicitud/solicitudi','Registro Solicitud nro '.$insertar);
				$this->session->set_flashdata("css", "success");
				$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
			}
			redirect(base_url()."solicitud");
		}

		$this->layout->setTitle("Solicitud Compra Producto");
		$this->layout->view("solicitudi",compact("anexos","nestablecimiento",'empresa'));
	}

	public function solicituda($id)
	{
    $controlip=$this->controlip('solicitud/solicituda');
		$datos=$this->solicitud_model->mostrar($id);
		$data=array("estado"=>3);
		$actualiza=$this->solicitud_model->update($data,$id);
		$control_movimiento=$this->movimientos('solicitud/solicituda','Anulo Solicitud nro '.$id);

    $success=true;
    $titulo='Anulado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'solicitud';
    echo json_encode($proceso);
    exit();
	}

	public function pdfsolicitud($id)
  {
		$empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$datos=$this->solicitud_model->mostrar($id);
		$detalles=$this->solicitudd_model->mostrarTotal($id);
		$proveedor=$this->proveedor_model->mostrar(array("p.id"=>$datos->idproveedor));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfsolicitud",compact("empresa","nestablecimiento","datos","detalles","proveedor","id"));
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

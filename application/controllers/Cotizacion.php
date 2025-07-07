<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizacion extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login')){redirect(base_url().'login');}
    if (!$this->acciones(1)){redirect(base_url()."inicio");}

		$this->layout->setLayout('contraido');
		$this->load->model("cliente_model");
		$this->load->model('cotizacion_model');
		$this->load->model('cotizaciond_model');
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    $controlip=$this->controlip('cotizacion');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
		$fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d") ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$listas=$this->cotizacion_model->mostrarTotal($filtros);
		$this->layout->setTitle("Cotizacion Venta");
		$this->layout->view("index",compact("anexos","nestablecimiento","empresa","listas","inicio","fin"));
	}

	public function cotizacioni($id=null)
	{
    $controlip=$this->controlip('cotizacion/cotizacioni');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		if ($this->input->post())
		{
			if ($this->input->post("idproducto",true)==null) {
				$this->session->set_flashdata("css", "danger");
				$this->session->set_flashdata("mensaje", "No envio productos en la cotizacion venta!");
			} else {
				if ($id!=null) {
					$data=array
					(
						"idcliente"		=>$this->input->post("idcliente",true),
						"cliente"			=>$this->input->post("cliente",true),
						"tvalidez"		=>$this->input->post("tvalidez",true),
						"tentrega"		=>$this->input->post("tentrega",true),
						'total'				=>$this->input->post('totalg',true),
						'condicion'		=>1,
						'dadicional'	=>$this->input->post('dadicional',true),
					);
					$actualizar=$this->cotizacion_model->update($data,array('id'=>$id));

					if (valor_check($this->input->post('pcredito',true))==1) {
						$datac=array('condicion'=>2);
						$actualizac=$this->cotizacion_model->update($datac,array('id'=>$id));
					}

					for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
						if (isset($this->input->post('id',true)[$i])) {
							$datad=array
							(
								"idproducto"	=>$this->input->post("idproducto",true)[$i],
								"descripcion"	=>trim($this->input->post("descripcion",true)[$i]),
								"unidad"			=>$this->input->post("unidad",true)[$i],
								"cantidad"		=>$this->input->post("cantidad",true)[$i],
								"precio"			=>$this->input->post("precio",true)[$i],
								"importe"			=>$this->input->post("importe",true)[$i],
							);
							$actualizad=$this->cotizaciond_model->update($datad,$this->input->post('id',true)[$i]);
						} else {
							$datad=array
							(
								"idcotizacion"	=>$id,
								"idproducto"		=>$this->input->post("idproducto",true)[$i],
								"descripcion"		=>trim($this->input->post("descripcion",true)[$i]),
								"unidad"				=>$this->input->post("unidad",true)[$i],
								"factor"				=>$this->input->post("factor",true)[$i],
								"cantidad"			=>$this->input->post("cantidad",true)[$i],
								"precio"				=>$this->input->post("precio",true)[$i],
								"importe"				=>$this->input->post("importe",true)[$i],
							);
							$insertard=$this->cotizaciond_model->insert($datad);
						}
					}
					$control_movimiento=$this->movimientos('cotizacion/cotizacioni','Edito cotizacion nro '.$id);

					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Los datos se han actualizado exitosamente!");
				} else {
					$data=array
					(
						"idestablecimiento"	=>$this->session->userdata("predeterminado"),
						"iduser"						=>$this->session->userdata('id'),
						"tipo"							=>"S",
						"femision"					=>date("Y-m-d"),
						"idcliente"					=>$this->input->post("idcliente",true),
						"cliente"						=>$this->input->post("cliente",true),
						"tvalidez"					=>$this->input->post("tvalidez",true),
						"tentrega"					=>$this->input->post("tentrega",true),
						'total'							=>$this->input->post('totalg',true),
						"estado"						=>1,
						'condicion'					=>1,
						'dadicional'				=>$this->input->post('dadicional',true),
					);
					$insertar=$this->cotizacion_model->insert($data);

					if (valor_check($this->input->post('pcredito',true))==1) {
						$datac=array('condicion'=>2);
						$actualizac=$this->cotizacion_model->update($datac,array('id'=>$insertar));
					}

					for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
						$datad=array
						(
							"idcotizacion"	=>$insertar,
							"idproducto"		=>$this->input->post("idproducto",true)[$i],
							"descripcion"		=>trim($this->input->post("descripcion",true)[$i]),
							"unidad"				=>$this->input->post("unidad",true)[$i],
							"factor"				=>$this->input->post("factor",true)[$i],
							"cantidad"			=>$this->input->post("cantidad",true)[$i],
							"precio"				=>$this->input->post("precio",true)[$i],
							"importe"				=>$this->input->post("importe",true)[$i],
						);
						$insertard=$this->cotizaciond_model->insert($datad);
					}

					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
					$control_movimiento=$this->movimientos('cotizacion/cotizacioni','Registro cotizacion nro '.$insertar);
				}
			}
			redirect(base_url()."cotizacion");
		}

		$empresa=$this->empresa_model->mostrar();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));

		$datos=$id!=null?$this->cotizacion_model->mostrar($id):(object) array("idcliente"=>'1',"cliente"=>'CLIENTES VARIOS',"tvalidez"=>'',"tentrega"=>'',"total"=>'',"dadicional"=>'', "condicion"=>0);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$this->layout->setTitle("Cotizacion Venta");
		$this->layout->view("cotizacioni",compact("anexos","nestablecimiento","empresa","productos",'datos','detalles'));
	}

	public function cotizaciond($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('cotizacion/cotizaciond');
		$datos=$this->cotizaciond_model->mostrar($id);
		if ($datos==NULL) {show_404();}
		$eliminar=$this->cotizaciond_model->delete($id);
		$control_movimiento=$this->movimientos('compra/eliminar','Elimino de la cotizacion producto '.$datos->descripcion);
		echo 1;
	}

	public function cotizaciona($id)
	{
    $controlip=$this->controlip('cotizacion/cotizaciona');
		$datos=$this->cotizacion_model->mostrar($id);
		$data=array("estado"=>3);
		$actualiza=$this->cotizacion_model->update($data,array("id"=>$id));
		$control_movimiento=$this->movimientos('cotizacion/cotizaciona','Anulo cotizacion '.$id);

    $success=true;
    $titulo='Anulado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'cotizacion';
    echo json_encode($proceso);
    exit();
	}

	public function opciones($id)
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("opciones",compact("id"));
	}

	public function pdfa4($id)
 {
 		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa4",compact("empresa","nestablecimiento","datos","detalles","cliente","id","usuario"));
	}

	public function pdf80($id)
 {
 		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf80",compact("empresa","nestablecimiento","datos","detalles","cliente","id","usuario"));
	}

	public function pdf58($id)
 {
 		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf58",compact("empresa","nestablecimiento","datos","detalles","cliente","id","usuario"));
	}

	public function pdfa5($id)
 {
 		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa5",compact("empresa","nestablecimiento","datos","detalles","cliente","id","usuario"));
	}

  public function copias($id)
  {
    $controlip=$this->controlip('cotizacion/copias');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
    $cotizacion=$this->cotizacion_model->mostrar($id);
    $detalles=$this->cotizaciond_model->mostrarTotal($id);
    $this->layout->setTitle("Cotizacion");
    $this->layout->view("copias",compact("anexos","nestablecimiento","empresa","productos","cotizacion","detalles","id"));
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

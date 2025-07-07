<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(26)){redirect(base_url()."inicio");}

		$this->layout->setLayout("contraido");
		$this->load->model("tidentidad_model");
		$this->load->model("departamento_model");
		$this->load->model("provincia_model");
		$this->load->model("distrito_model");
		$this->load->model("cliente_model");
		$this->load->model("nventa_model");
		$this->load->model("venta_model");
		$this->load->model("punto_model");
		$this->load->model("clientep_model");
		$this->load->model("vale_model");
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    $controlip=$this->controlip('cliente');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->cliente_model->mostrarLimite();
		$this->layout->setTitle("Cliente");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function busDatos()
	{
		if ($this->input->post())
		{
			$tipo=$this->input->post('tipo',true);
			$numero=$this->input->post('numero',true);

      if ($tipo==1) {
				$ruta="https://api.apis.net.pe/v1/dni?numero=".$numero;
			}else{
				$ruta="https://api.apis.net.pe/v1/ruc?numero=".$numero;
			}

			$curl = curl_init();
			curl_setopt_array($curl, array(
			  // para user api versión 1
			  CURLOPT_URL => $ruta,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 2,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'GET',
			));
			$response = curl_exec($curl);
			curl_close($curl);
			echo $response;
		}
		else
		{
			show_404();
		}
	}

	public function clientei($id=null)
	{
    $controlip=$this->controlip('cliente/clientei');
		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array
				(
					"tdocumento"			=>$this->input->post("tipo",true),
					"documento"				=>$this->input->post("documento",true),
					"nombres"					=>trim(mb_strtoupper($this->input->post('nombres',true), 'UTF-8')),
					"ncomercial"			=>$this->input->post("ncomercial",true),
					"iddepartamento"	=>$this->input->post("departamento",true),
					"idprovincia"			=>$this->input->post("provincia",true),
					"iddistrito"			=>$this->input->post("distrito",true),
					"direccion"				=>$this->input->post("direccion",true),
					"telefono"				=>$this->input->post("telefono",true),
					"email"						=>$this->input->post("email",true),
				);

				$guardar=$this->cliente_model->update($data,$id);
				$this->session->set_flashdata("css", "success");
				$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
				$control_movimiento=$this->movimientos('cliente/clientei','Edito al cliente '.$this->input->post('nombres',true));
			} else {
				$consulta= $this->input->post("tipo",true)==0 ? 0: $this->cliente_model->contador($this->input->post("documento",true));
				if ($consulta==0) {
					if ($this->input->post("tipo",true)==1 && strlen($this->input->post("documento",true))==8 || $this->input->post("tipo",true)==6 && strlen($this->input->post("documento",true))==11 || $this->input->post("tipo",true)==4 || $this->input->post("tipo",true)==7 || $this->input->post("tipo",true)==0) {
						$data=array
						(
							"tdocumento"			=>$this->input->post("tipo",true),
							"documento"				=>$this->input->post("documento",true),
							"nombres"					=>trim(mb_strtoupper($this->input->post('nombres',true), 'UTF-8')),
							"ncomercial"			=>$this->input->post("ncomercial",true),
							"idpais"					=>"PE",
							"iddepartamento"	=>$this->input->post("departamento",true),
							"idprovincia"			=>$this->input->post("provincia",true),
							"iddistrito"			=>$this->input->post("distrito",true),
							"direccion"				=>$this->input->post("direccion",true),
							"telefono"				=>$this->input->post("telefono",true),
							"email"						=>$this->input->post("email",true),
						);

						$insertar=$this->cliente_model->insert($data);
						$this->session->set_flashdata("css", "success");
						$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
						$control_movimiento=$this->movimientos('cliente/clientei','Registro al cliente '.$this->input->post('nombres',true));
					} else {
						$this->session->set_flashdata("css", "danger");
						$this->session->set_flashdata("mensaje", "El numero de documento en incoherente con el tipo de documento");
					}
				} else {
					$this->session->set_flashdata("css", "danger");
					$this->session->set_flashdata("mensaje", "El cliente ya EXISTE!!!!!!");
				}
			}
			echo base_url()."cliente";
			exit();
		}

		$datos=$id!=null?$this->cliente_model->mostrar($id):(object) array("tdocumento"=>1, "nombres"=>"", "ncomercial"=>"", "documento"=>"", "iddepartamento"=>"", "idprovincia"=>"", "iddistrito"=>"", "direccion"=>"", "telefono"=>"", "email"=>"");
		$departamentos=$this->departamento_model->mostrarTotal();
		$provincias=$id!=null?$this->provincia_model->mostrarTotal($datos->iddepartamento):null;
		$distritos=$id!=null?$this->distrito_model->mostrarTotal($datos->idprovincia):null;
		$identidades=$this->tidentidad_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("clientei",compact("datos","departamentos","provincias","distritos","identidades"));
	}

	public function cliented($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('cliente/cliented');
		$datos=$this->cliente_model->mostrar($id);
		if ($datos==NULL) {show_404();}

		$contadorn=$this->nventa_model->contador(array('idcliente'=>$id));
		$contadorv=$this->venta_model->contador(array('idcliente'=>$id));
		$contador=$contadorn+$contadorv;
    if ($contador>0) {
      $success=false;
      $titulo='No se puede borrar!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
    }else{
      $eliminar=$this->cliente_model->delete($id);
      $success=true;
      $titulo='Borrado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
    	$control_movimiento=$this->movimientos('cliente/cliented','Elimino al cliente '.$datos->nombres);
    }

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'cliente';
    echo json_encode($proceso);
    exit();
	}

	public function busVale()
	{
		$codigo=$this->input->post("id",true);
		$datos=$this->vale_model->mostrar(array("concat(id,'&',femision,'&',dni)"=>$codigo,"estado"=>1));
		if ($datos==null) {
			$resultado['importe']=0;
			$resultado['mensaje']='El codigo de vale no existe activo';
			$resultado['tipo']=1;
		} else {
			$resultado['importe']=$datos->importe;
			$resultado['mensaje']='El codigo de vale si existe';
			$resultado['tipo']=2;
		}
		echo json_encode($resultado);
	}

	public function pacumulados($id)
	{
    $controlip=$this->controlip('cliente/pacumulados');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$datos=$this->cliente_model->mostrar($id);
		$listas=$this->clientep_model->mostrarTotal($id);
		$vcanje=$this->punto_model->mostrar();
		$vales=$this->vale_model->mostrarTotal($datos->documento);
		$this->layout->setTitle("Puntos Acumulados");
		$this->layout->view("pacumulados",compact("anexos","nestablecimiento",'empresa',"listas","datos","vcanje","vales","id"));
	}

	public function canjerv($id)
	{
    $controlip=$this->controlip('cliente/canjerv');
		$valor_canje=$this->punto_model->mostrar();
		if ($valor_canje->canjep>0 && $valor_canje->canjev>0) {
			$listas=$this->clientep_model->mostrarCliente($id);

			$cantidad=$valor_canje->canjep;
			foreach ($listas as $lista) {
				if ($cantidad>0) {
					$ncantidad=$cantidad-$lista->cantidad;	//nueva cantidad
					$saldoc=$lista->cantidad-$cantidad;

					if ($saldoc>0) {
						$datac=array('cantidad'=>$saldoc);
						$actualizar=$this->clientep_model->update($datac,array("id"=>$lista->id));
					} else {
						$eliminar=$this->clientep_model->delete(array("id"=>$lista->id));
					}

					$cantidad=$ncantidad;
				}else{
					break;
				}
			}

			$datos=$this->cliente_model->mostrar($id);
			$data=array
			(
				"femision"	=>date("Y-m-d"),
				"dni"				=>$datos->documento,
				"importe"		=>$valor_canje->canjev,
				"estado"		=>1,
			);

			$insertar=$this->vale_model->insert($data);
			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "El vale se ha generado exitosamente!");
			$control_movimiento=$this->movimientos('cliente/canjerv','Canjeo vale del cliente'.$datos->nombres);

			echo "<script>window.open('".base_url()."cliente/pdfcanje/".$insertar."','_blank'); location.href ='".base_url()."cliente/pacumulados/".$id."'; </script>";
		} else {
			$this->session->set_flashdata('css', 'danger');
			$this->session->set_flashdata('mensaje', 'Tiene que configurar los montos de canje');
		}
	}

	public function pdfcanje($id)
  {
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->vale_model->mostrar(array("id"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfcanje",compact("empresa","datos"));
  }

	public function busCliente()
	{
		if ($this->input->post())
		{
    	$empresa=$this->empresa_model->mostrar();
			$datos=array();
			if (strlen($this->input->post('id',true))>2) {
				$clientes=$this->cliente_model->buscador($this->input->post('id',true));
			} else {
				$clientes=$this->cliente_model->mostrarLimite();
			}

			foreach ($clientes as $cliente) {
				$tpuntos=$this->clientep_model->cantidadTotal($cliente->id);
				$puntos=$tpuntos->cantidad==null ? 0 : $tpuntos->cantidad ;

				$detalle['id']=$cliente->id;
				$detalle['tdocumento']=$cliente->tdocumento;
				$detalle['documento']=$cliente->documento;
				$detalle['nombres']=$cliente->nombres;
				$detalle['iddepartamento']=$cliente->iddepartamento;
				$detalle['idprovincia']=$cliente->idprovincia;
				$detalle['iddistrito']=$cliente->iddistrito;
				$detalle['direccion']=$cliente->direccion;
				$detalle['telefono']=$cliente->telefono;
				$detalle['email']=$cliente->email;
				$detalle['spuntos']=$empresa->spuntos;
				$detalle['puntos']='Puntos Acumulados : '.$puntos;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function buscador($envio=null)
	{
		$datos=$this->cliente_model->mostrarLimite();
		$this->layout->setLayout("blanco");
		$this->layout->view("buscador",compact("datos","envio"));
	}

	public function destinatario()
	{
		$datos=$this->cliente_model->mostrarLimite();
		$this->layout->setLayout("blanco");
		$this->layout->view("destinatario",compact("datos"));
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

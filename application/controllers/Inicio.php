<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
		$this->layout->setLayout("contraido");
		$this->load->model("tpago_model");
		$this->load->model("compra_model");
		$this->load->model("nventa_model");
		$this->load->model("venta_model");
		$this->load->model("nota_model");
		$this->load->model("resumen_model");
		$this->load->model("anulado_model");
		$this->load->model("testado_model");
		$this->load->model("cobro_model");
		$this->load->model("cobroe_model");
		$this->load->model("cobron_model");
		$this->load->model("arqueo_model");
	}

	public function index()
	{
    $controlip=$this->controlip('inicio');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$anuo=date("Y"); $mes=date("m");

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),'year(femision)'=>$anuo,'month(femision)'=>$mes);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$boletas=$this->venta_model->contador($filtros);
		$ncreditos=$this->nota_model->contador($filtros);
		$comprobantes=$boletas+$ncreditos;

		$ctotal=$this->compra_model->montoTotal($filtros);
    $diasmes=date("t");
    $arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));

    $fecvencimientos=$this->lote_model->fechasVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fvencimiento<="=>SumarFecha('+3 month'),"stock>"=>0));
    $mpagos=$this->tpago_model->mostrarTotal();
		$this->layout->setTitle('Inicio');
		$this->layout->view('index',compact("anexos","nestablecimiento","empresa","ctotal","comprobantes","diasmes","arqueoc","fecvencimientos","mpagos"));
	}

	public function minimo()
	{
		$listas=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1));
		$this->layout->setLayout("blanco");
		$this->layout->view("minimo",compact("listas"));
	}

	public function vencido()
	{
    $listas=$this->lote_model->productosVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fvencimiento<="=>SumarFecha('+3 month'),"estado"=>1,"stock>"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("vencido",compact("listas"));
	}

	public function digemid()
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("digemid");
	}

	public function vigilancia()
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("vigilancia");
	}

	public function ventasm()
	{
		$diasmes=date("t");
		$finicio='01-'.date("m").'-'.date("Y");
		$datos=array();
		for ($i=0; $i < $diasmes ; $i++) {
			$fecha=SumarFecha('+'.$i.' day',$finicio);
			$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
			if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
      $vtotal=$this->venta_model->montoTotal($filtros);

      $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
      if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
      $ntotal=$this->nventa_model->montoTotal($filtros);

			$anexo['fechas']=date("d",strtotime($fecha));
			$anexo['ventas']=($vtotal->total??0);
			$anexo['nventas']=($ntotal->total??0);
			array_push($datos,$anexo);
		}
		echo json_encode($datos);
		//var_dump($datos);
	}

	public function comprasa()
	{
		$diasmes=date("t");
		$finicio='01-'.date("m").'-'.date("Y");
		$datos=array();
		for ($i=0; $i < $diasmes ; $i++) {
			$fecha=SumarFecha('+'.$i.' day',$finicio);
			$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'femision'=>$fecha);
			if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
      $ctotal=$this->compra_model->montoTotal($filtros);

			$anexo['fechas']=date("d",strtotime($fecha));
			$anexo['compras']=($ctotal->total??0);
			array_push($datos,$anexo);
		}
		echo json_encode($datos);
		//var_dump($datos);
	}

	public function nventac()
	{
		$mes=date("m"); $anuo=date("Y");
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>1);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$contado=$this->nventa_model->montoTotal($filtros);

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>2);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$credito=$this->nventa_model->montoTotal($filtros);
		$datos['contado']=($contado->total??0);
		$datos['credito']=($credito->total??0);
		echo json_encode($datos);
	}

	public function comprobantec()
	{
		$mes=date("m"); $anuo=date("Y");
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>1);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$contado=$this->venta_model->montoTotal($filtros);

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>2);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$credito=$this->venta_model->montoTotal($filtros);
		$datos['contado']=($contado->total??0);
		$datos['credito']=($credito->total??0);
		echo json_encode($datos);
	}

	public function comprac()
	{
		$mes=date("m"); $anuo=date("Y");
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>1);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$contado=$this->compra_model->montoTotal($filtros);

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,'year(femision)'=>$anuo,'month(femision)'=>$mes,"condicion"=>2);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$credito=$this->compra_model->montoTotal($filtros);
		$datos['contado']=($contado->total??0);
		$datos['credito']=($credito->total??0);
		echo json_encode($datos);
	}

  public function establecimientos()
  {
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $establecimientos=$this->establecimiento_model->mostrarAcceso($anexos);
		$this->layout->setLayout("blanco");
    $this->layout->view('establecimientos',compact("establecimientos"));
  }

  public function asignacion($id,$codigo)
  {
    $data = array(
      'predeterminado'=> $id,
      'codigo'        => $codigo,
    );
    $this->session->set_userdata($data);
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



}

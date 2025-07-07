<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Caja extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
		$this->layout->setLayout("principal");
		$this->load->model("tpago_model");
    $this->load->model("compra_model");
		$this->load->model("pago_model");
    $this->load->model("nventa_model");
    $this->load->model("venta_model");
		$this->load->model("cobro_model");
		$this->load->model("cobroe_model");
    $this->load->model("nota_model");
		$this->load->model("cobron_model");
		$this->load->model("ingreso_model");
		$this->load->model("egreso_model");
		$this->load->model("arqueo_model");
		$this->load->model("arqueod_model");
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    if (!$this->acciones(6)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('caja');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-7 day',date("Y-m-d")) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$listas=$this->arqueo_model->mostrarTotal($filtros);
		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		$this->layout->setTitle("Arqueo Caja");
		$this->layout->view("index",compact("anexos","nestablecimiento","empresa","listas","inicio","fin","arqueoc"));
	}

	public function arqueoi()
	{
    $controlip=$this->controlip('caja/arqueoi');
		if ($this->input->post())
		{
			$data=array
			(
				'idestablecimiento'	=>$this->session->userdata("predeterminado"),
				'iduser'						=>$this->session->userdata('id'),
				'femision'					=>date("Y-m-d"),
				'finicial'					=>date("Y-m-d H:i:s"),
				'minicial'					=>$this->input->post('minicial',true),
				'estado'						=>1,
			);
			$insertar=$this->arqueo_model->insert($data);
			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
			$control_movimiento=$this->movimientos('caja/arqueoi','Registro arqueo nro '.$insertar);

			echo base_url()."caja";
			exit();
		}

		$this->layout->setLayout("blanco");
		$this->layout->view("arqueoi");
	}

	public function arqueoc($id)
	{
    $controlip=$this->controlip('caja/arqueoc');
		$datos=$this->arqueo_model->mostrar($id);
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idtpago"=>1,"idarqueoc"=>NULL,"iduser"=>$datos->iduser);
		//cobros comprobante
		$mcobrosc=$this->cobroe_model->montoTotal($filtros);
		$mcobrosn=$this->cobron_model->montoTotal($filtros);
		$totalComprobante=$mcobrosc->total+$mcobrosn->total;

		//cobros
		$mcobros=$this->cobro_model->montoTotal($filtros);
		$totalNventas=$mcobros->total;

		//ingresos
		$mingresos=$this->ingreso_model->montoTotal($filtros);
		$totalIngresos=$mingresos->total;

		//pagos
		$mpagos=$this->pago_model->montoTotal($filtros);
		$totalCompras=$mpagos->total;

		//egresos
		$megresos=$this->egreso_model->montoTotal($filtros);
		$totalegresos=$megresos->total;

		$saldo=$datos->minicial+$totalComprobante+$totalNventas+$totalIngresos-$totalCompras-$totalegresos;
		$data=array
		(
			'ffinal'		=>date("Y-m-d H:i:s"),
			'ventas'		=>$totalComprobante+$totalNventas,
			'compras'		=>$totalCompras,
			'ingresos'	=>$totalIngresos,
			'egresos'		=>$totalegresos,
			'mfinal'		=>$saldo,
			'estado'		=>0,
		);
		$actualizar=$this->arqueo_model->update($data,$id);

		$filtrosm=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idarqueoc"=>NULL,"iduser"=>$datos->iduser);
		$medios=$this->cobro_model->mediosPagos($filtrosm);
		foreach ($medios as $medio) {
			$filtron=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idarqueoc"=>NULL,"iduser"=>$datos->iduser,"idtpago"=>$medio->idtpago);
			$notas=$this->cobron_model->montoTotal($filtron);
			$datad=array
			(
				'idarqueo'	=>$id,
				'idtpago'		=>$medio->idtpago,
				'importe'		=>$medio->total+$notas->total,
			);
			$insertard=$this->arqueod_model->insert($datad);
		}

		$datac=array("idarqueoc"=>$id);
		$actualiza=$this->cobro_model->update($datac,$filtrosm);
		$actualizac=$this->cobroe_model->update($datac,$filtrosm);
		$actualizan=$this->cobron_model->update($datac,$filtrosm);
		$actualizap=$this->pago_model->update($datac,$filtros);
		$actualizai=$this->ingreso_model->update($datac,$filtros);
		$actualizag=$this->egreso_model->update($datac,$filtros);

		$this->session->set_flashdata("css", "success");
		$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
		$control_movimiento=$this->movimientos('caja/arqueoc','Cerro arqueo nro '.$id);

		redirect(base_url()."caja");
	}

	public function cerrar($id)
	{
    $controlip=$this->controlip('caja/arqueoc');
		if ($this->input->post())
		{
			$datos=$this->arqueo_model->mostrar($id);
			$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idtpago"=>1,"idarqueoc"=>NULL,"iduser"=>$datos->iduser);
			//cobros comprobante
			$mcobrosc=$this->cobroe_model->montoTotal($filtros);
			$mcobrosn=$this->cobron_model->montoTotal($filtros);
			$totalComprobante=$mcobrosc->total+$mcobrosn->total;

			//cobros
			$mcobros=$this->cobro_model->montoTotal($filtros);
			$totalNventas=$mcobros->total;

			//ingresos
			$mingresos=$this->ingreso_model->montoTotal($filtros);
			$totalIngresos=$mingresos->total;

			//pagos
			$mpagos=$this->pago_model->montoTotal($filtros);
			$totalCompras=$mpagos->total;

			//egresos
			$megresos=$this->egreso_model->montoTotal($filtros);
			$totalegresos=$megresos->total;

			$saldo=$datos->minicial+$totalComprobante+$totalNventas+$totalIngresos-$totalCompras-$totalegresos;
			$data=array
			(
				'ffinal'		=>date("Y-m-d H:i:s"),
				'ventas'		=>$totalComprobante+$totalNventas,
				'compras'		=>$totalCompras,
				'ingresos'	=>$totalIngresos,
				'egresos'		=>$totalegresos,
				'mfinal'		=>$this->input->post('mfinal',true),
				'estado'		=>0,
			);
			$actualizar=$this->arqueo_model->update($data,$id);

			$filtrosm=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idarqueoc"=>NULL,"iduser"=>$datos->iduser);
			$medios=$this->cobro_model->mediosPagos($filtrosm);
			foreach ($medios as $medio) {
				$filtron=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"idarqueoc"=>NULL,"iduser"=>$datos->iduser,"idtpago"=>$medio->idtpago);
				$notas=$this->cobron_model->montoTotal($filtron);
				$datad=array
				(
					'idarqueo'	=>$id,
					'idtpago'		=>$medio->idtpago,
					'importe'		=>$medio->total+$notas->total,
				);
				$insertard=$this->arqueod_model->insert($datad);
			}

			$datac=array("idarqueoc"=>$id);
			$actualiza=$this->cobro_model->update($datac,$filtrosm);
			$actualizac=$this->cobroe_model->update($datac,$filtrosm);
			$actualizan=$this->cobron_model->update($datac,$filtrosm);
			$actualizap=$this->pago_model->update($datac,$filtros);
			$actualizai=$this->ingreso_model->update($datac,$filtros);
			$actualizag=$this->egreso_model->update($datac,$filtros);

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
			$control_movimiento=$this->movimientos('caja/cerrar','Cerro arqueo nro '.$id);
			echo base_url()."caja";
			exit();
		}

		$this->layout->setLayout("blanco");
		$this->layout->view("arqueoc");
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
		$datos=$this->arqueo_model->mostrar($id);
		$detalles=$this->arqueod_model->mostrarTotal($id);
		$nombre= $this->usuario_model->mostrar($datos->iduser);

		$cobros=$this->cobro_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrose=$this->cobroe_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrosn=$this->cobron_model->mostrarTotal(array("idarqueoc"=>$id));
		$ingresos=$this->ingreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$pagos=$this->pago_model->mostrarTotal(array("idarqueoc"=>$id));
		$egresos=$this->egreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa4",compact("empresa","nestablecimiento","datos","detalles","nombre","id","cobros","cobrose","cobrosn","ingresos","pagos","egresos"));
	}

	public function pdf80($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->arqueo_model->mostrar($id);
		$detalles=$this->arqueod_model->mostrarTotal($id);
		$nombre= $this->usuario_model->mostrar($datos->iduser);

		$cobros=$this->cobro_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrose=$this->cobroe_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrosn=$this->cobron_model->mostrarTotal(array("idarqueoc"=>$id));
		$ingresos=$this->ingreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$pagos=$this->pago_model->mostrarTotal(array("idarqueoc"=>$id));
		$egresos=$this->egreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf80",compact("empresa","nestablecimiento","datos","detalles","nombre","id","cobros","cobrose","cobrosn","ingresos","pagos","egresos"));
	}

	public function pdf58($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->arqueo_model->mostrar($id);
		$detalles=$this->arqueod_model->mostrarTotal($id);
		$nombre= $this->usuario_model->mostrar($datos->iduser);

		$cobros=$this->cobro_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrose=$this->cobroe_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrosn=$this->cobron_model->mostrarTotal(array("idarqueoc"=>$id));
		$ingresos=$this->ingreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$pagos=$this->pago_model->mostrarTotal(array("idarqueoc"=>$id));
		$egresos=$this->egreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf58",compact("empresa","nestablecimiento","datos","detalles","nombre","id","cobros","cobrose","cobrosn","ingresos","pagos","egresos"));
	}

	public function pdfa5($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->arqueo_model->mostrar($id);
		$detalles=$this->arqueod_model->mostrarTotal($id);
		$nombre= $this->usuario_model->mostrar($datos->iduser);

		$cobros=$this->cobro_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrose=$this->cobroe_model->mostrarTotal(array("idarqueoc"=>$id));
		$cobrosn=$this->cobron_model->mostrarTotal(array("idarqueoc"=>$id));
		$ingresos=$this->ingreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$pagos=$this->pago_model->mostrarTotal(array("idarqueoc"=>$id));
		$egresos=$this->egreso_model->mostrarTotal(array("idarqueoc"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa5",compact("empresa","nestablecimiento","datos","detalles","nombre","id","cobros","cobrose","cobrosn","ingresos","pagos","egresos"));
	}

	public function mpago()
	{
    if (!$this->acciones(7)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('mpago');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : date("Y-m-d") ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;

		$medios=$this->tpago_model->mostrarTotal();
		$this->layout->setTitle("Medio Pago");
		$this->layout->view("mpago",compact("anexos","nestablecimiento","empresa","inicio","fin","medios"));
	}

	public function pdfcaja($inicio,$fin)
  {
  	$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
    $medios=$this->tpago_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfcaja",compact("empresa","nestablecimiento","medios","inicio","fin"));
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

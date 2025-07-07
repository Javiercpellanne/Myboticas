<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Consulta extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
		$this->layout->setLayout("contraido");
		$this->load->model("mes_model");
		$this->load->model("compra_model");
		$this->load->model("nventa_model");
		$this->load->model("venta_model");
		$this->load->model("nota_model");
		$this->load->model("bonificado_model");
		$this->load->model("kardex_model");
		$this->load->model("kardexl_model");
		$this->load->model("pactivo_model");
		$this->load->model("egenerico_model");
		$this->load->library("mytcpdf");
	}

  public function index()
	{
    if (!$this->acciones(14)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : date("Y-m-d") ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;
		$user=$this->input->post('usuario',true)!=null ? $this->input->post('usuario',true) : '' ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin);
		if ($user!='') {$filtros['iduser']=$user;}
		$listas=$this->nventa_model->ganancia($filtros);
		$usuarios=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Ventas Valorizado");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas",'usuarios',"inicio","fin",'user'));
	}

	public function pdfventav($inicio,$fin,$user='')
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin);
		if ($user!='') {$filtros['iduser']=$user;}
		$listas=$this->nventa_model->ganancia($filtros);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfventav",compact("empresa","nestablecimiento","listas","inicio","fin"));
  }

  public function excelventav($inicio,$fin,$user='')
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin);
		if ($user!='') {$filtros['iduser']=$user;}
		$listas=$this->nventa_model->ganancia($filtros);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Venta Valorizada");

		$sheet->mergeCells("A1:H1");
		$sheet->mergeCells("A2:H2");

		$sheet->getStyle("A1:H3")->getFont()->setBold(true);
		$sheet->getStyle("A1:H3")->getAlignment()->setHorizontal("center");

		$titulo="VENTAS VALORIZADAS DEL ".$inicio." AL ".$fin;
		if ($user!='') {
			$usuarios=$this->usuario_model->mostrar($user);
			$titulo.=" DEL USUARIO ".$usuarios->nombres;
		}
		$sheet->setCellValueByColumnAndRow(1, 1, $titulo);

		$styleArray = [
		    'borders' => [
		        'top' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'bottom' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'left' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'right' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		];

		foreach(range("A","H") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."3")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 3,"#");
		$sheet->setCellValueByColumnAndRow(2, 3,"Producto");
		$sheet->setCellValueByColumnAndRow(3, 3,"Cantidad");
		$sheet->setCellValueByColumnAndRow(4, 3,"(Dscto)");
		$sheet->setCellValueByColumnAndRow(5, 3,"Ventas");
		$sheet->setCellValueByColumnAndRow(6, 3,"Costo Prom.");
		$sheet->setCellValueByColumnAndRow(7, 3,"Utilidad");
		$sheet->setCellValueByColumnAndRow(8, 3,"Margen (%)");

		$i=4;
		$n=1;
    $tcompra=0;
    $tventa=0;
    $tutilidad=0;
    foreach ($listas as $lista) {
    	$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin,"nulo"=>0,"idproducto"=>$lista->idproducto);
			if ($user!='') {$filtros['iduser']=$user;}
	    $notas=$this->nota_model->ganancia($filtros);
	    $cantidad=$lista->cantidad-$notas->cantidad;
	    $venta=$lista->importe-$notas->importe;

	    $compra=$lista->costo-$notas->costo;//
	    $utilidad=$venta-$compra;
      $margen=gananciav($venta,$compra,1);

			$sheet->getStyle("A".$i)->applyFromArray($styleArray);
			$sheet->getStyle("B".$i)->applyFromArray($styleArray);
			$sheet->getStyle("C".$i)->applyFromArray($styleArray);
			$sheet->getStyle("D".$i)->applyFromArray($styleArray);
			$sheet->getStyle("E".$i)->applyFromArray($styleArray);
			$sheet->getStyle("F".$i)->applyFromArray($styleArray);
			$sheet->getStyle("G".$i)->applyFromArray($styleArray);
			$sheet->getStyle("H".$i)->applyFromArray($styleArray);

			$sheet->setCellValue("A".$i,$n);
			$sheet->setCellValue("B".$i,$lista->descripcion);
			$sheet->setCellValue("C".$i,$cantidad);
			$sheet->setCellValue("D".$i,$lista->dscto);
			$sheet->setCellValue("E".$i,$venta);
			$sheet->setCellValue("F".$i,$compra);
			$sheet->setCellValue("G".$i,$utilidad);
			$sheet->setCellValue("H".$i,$margen);

			$tcompra+=$compra;
		  $tventa+=$venta;
		  $tutilidad+=$utilidad;
			$i++; $n++;
		}

		$sheet->setCellValue("A".$i,'Totales');
		$sheet->setCellValue("E".$i,$tventa);
		$sheet->setCellValue("F".$i,$tcompra);
		$sheet->setCellValue("G".$i,$tutilidad);

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="VENTAS_VALORIZADAS'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function vhorario()
	{
    if (!$this->acciones(16)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/vhorario');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-7 day',date("Y-m-d")) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;
		$this->layout->setTitle("Ventas Horario");
		$this->layout->view("vhorario",compact("anexos","nestablecimiento",'empresa',"inicio","fin"));
	}

	public function ventash()
	{
		$hinicial="07:00";
		$datos=array();
		for ($i=0; $i < 16; $i++) {
			$j=$i+1;
			$horarioi=SumarHora('+'.$i.' hour',$hinicial);
			$horariof=SumarHora('+'.$j.' hour',$hinicial);
			$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"hemision>="=>$horarioi,"hemision<="=>$horariof,"femision>="=>$this->input->post('inicio',true),"femision<="=>$this->input->post('fin',true));
			$cantidadn=$this->nventa_model->contador($filtros);
			$cantidadv=$this->venta_model->contador($filtros);
			$cantidad=$cantidadn+$cantidadv;

			$horas['horas']=date('H:i',strtotime($horarioi));
			$horas['ventas']=$cantidad;
			array_push($datos,$horas);
		}
		echo json_encode($datos);
		//var_dump($datos);
	}

  public function stockv()
	{
    if (!$this->acciones(15)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/stockv');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));
		$this->layout->setTitle("Stock Valorizado");
		$this->layout->view("stockv",compact("anexos","nestablecimiento","empresa","listas"));
	}

	public function pdfstockv()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfstockv",compact("empresa","nestablecimiento","listas"));
  }

 	public function excelstockv()
	{
		$empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Stock Valorizado");

		$sheet->mergeCells("A1:I1");
		$sheet->mergeCells("A2:I2");

		$sheet->getStyle("A1:I3")->getFont()->setBold(true);
		$sheet->getStyle("A1:I3")->getAlignment()->setHorizontal("center");

		$sheet->setCellValueByColumnAndRow(1, 1,"STOCK VALORIZADO");

		$styleArray = [
		    'borders' => [
		        'top' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'bottom' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'left' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		        'right' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		        ],
		    ],
		];

		foreach(range("A","I") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."3")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 3,"#");
		$sheet->setCellValueByColumnAndRow(2, 3,"Producto");
		$sheet->setCellValueByColumnAndRow(3, 3,"Precio Venta");
		$sheet->setCellValueByColumnAndRow(4, 3,"Costo Prom");
		$sheet->setCellValueByColumnAndRow(5, 3,"Cantidad Actual");
		$sheet->setCellValueByColumnAndRow(6, 3,"Total Venta");
		$sheet->setCellValueByColumnAndRow(7, 3,"Total Costo Prom");
		$sheet->setCellValueByColumnAndRow(8, 3,"Utilidad");
		$sheet->setCellValueByColumnAndRow(9, 3,"Margen (%)");

		$i=4;

		$n=1;
    $tcompra=0;
    $tventa=0;
    $tutilidad=0;
    foreach ($listas as $lista) {
    	$nproducto=$lista->descripcion;
      if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}
      $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
      $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa;

      $cantidad=$lista->stock;
      $venta=$cantidad*$pventa;

      $kardex=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));
      $costo=round($kardex->saldov/$kardex->saldof,2);
      $compra=$cantidad*($kardex!=NULL ? $costo : $lista->pcompra);
      $utilidad=$venta-$compra;
      $margen=gananciav($venta,$compra,1);

			$sheet->getStyle("A".$i)->applyFromArray($styleArray);
			$sheet->getStyle("B".$i)->applyFromArray($styleArray);
			$sheet->getStyle("C".$i)->applyFromArray($styleArray);
			$sheet->getStyle("D".$i)->applyFromArray($styleArray);
			$sheet->getStyle("E".$i)->applyFromArray($styleArray);
			$sheet->getStyle("F".$i)->applyFromArray($styleArray);
			$sheet->getStyle("G".$i)->applyFromArray($styleArray);
			$sheet->getStyle("H".$i)->applyFromArray($styleArray);
			$sheet->getStyle("I".$i)->applyFromArray($styleArray);

			$sheet->setCellValue("A".$i,$n);
			$sheet->setCellValue("B".$i,$nproducto);
			$sheet->setCellValue("C".$i,$pventa);
			$sheet->setCellValue("D".$i,($kardex!=NULL ? $costo : $lista->pcompra));
			$sheet->setCellValue("E".$i,$cantidad);
			$sheet->setCellValue("F".$i,$venta);
			$sheet->setCellValue("G".$i,$compra);
			$sheet->setCellValue("H".$i,$utilidad);
			$sheet->setCellValue("I".$i,$margen);

	    $tventa+=$venta;
			$tcompra+=$compra;
	    $tutilidad+=$utilidad;
			$i++;
			$n++;
		}

		$sheet->setCellValue("A".$i,'Totales');
		$sheet->setCellValue("F".$i,$tventa);
		$sheet->setCellValue("G".$i,$tcompra);
		$sheet->setCellValue("H".$i,$tutilidad);

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="STOCK_VALORIZADO_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function kardex()
	{
    if (!$this->acciones(18)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/kardex');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$finicio = $this->input->post('finicio',true)!=null ? $this->input->post('finicio',true) : date("Y-m-d") ;
		$ffinal = $this->input->post('ffinal',true)!=null ? $this->input->post('ffinal',true) : date("Y-m-d") ;
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fecha>="=>$finicio,"fecha<="=>$ffinal);
		$listas=$this->kardex_model->mostrarTotal($filtros);
		$this->layout->setLayout("contraido");
		$this->layout->setTitle("Kardex");
		$this->layout->view("kardex",compact("anexos","nestablecimiento",'empresa',"listas","finicio","ffinal"));
	}

	public function producto()
	{
    if (!$this->acciones(18)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/producto');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$idproducto = $this->input->post('idproducto',true)!=null ? $this->input->post('idproducto',true) : '' ;
		$descripcion = $this->input->post('descripcion',true)!=null ? $this->input->post('descripcion',true) : "" ;
		$finicio = $this->input->post('finicio',true)!=null ? $this->input->post('finicio',true) : date("Y-m-d") ;
		$ffinal = $this->input->post('ffinal',true)!=null ? $this->input->post('ffinal',true) : date("Y-m-d") ;
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fecha>="=>$finicio,"fecha<="=>$ffinal,"idproducto"=>$idproducto);
		$listas=$this->kardex_model->mostrarTotal($filtros);
		$this->layout->setLayout("contraido");
		$this->layout->setTitle("Kardex");
		$this->layout->view("producto",compact("anexos","nestablecimiento",'empresa',"listas","idproducto","descripcion","finicio","ffinal"));
	}

	public function lote()
	{
    if (!$this->acciones(18)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/lote');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$idproducto = $this->input->post('idproducto',true)!=null ? $this->input->post('idproducto',true) : '' ;
    $descripcion = $this->input->post('descripcion',true)!=null ? $this->input->post('descripcion',true) : "" ;
    $finicio = $this->input->post('finicio',true)!=null ? $this->input->post('finicio',true) : date("Y-m-d") ;
    $ffinal = $this->input->post('ffinal',true)!=null ? $this->input->post('ffinal',true) : date("Y-m-d") ;
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fecha>="=>$finicio,"fecha<="=>$ffinal,"idproducto"=>$idproducto);
    $listas=$this->kardexl_model->mostrarTotal($filtros);
    $this->layout->setTitle("Kardex Lote");
    $this->layout->view("lote",compact("anexos","nestablecimiento",'empresa',"listas","idproducto","descripcion","finicio","ffinal"));
	}

  public function bvendedor()
	{
    if (!$this->acciones(36)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/bvendedor');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$canuo=$this->input->post('canuo',true)!=null ? $this->input->post('canuo',true) : '';
		$cmes=$this->input->post('cmes',true)!=null ? $this->input->post('cmes',true) : '';
		$nusuario=$this->input->post('nusuario',true)!=null ? $this->input->post('nusuario',true) : '';
		$anuos=$this->periodo_model->mostrarTotal();
		$meses=$this->mes_model->mostrarTotal();
		$usuarios=$this->usuario_model->mostrarTotal(array('estado'=>1));

		$listas=$this->nventa_model->ganancia(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>$canuo,"month(femision)"=>$cmes,"iduser"=>$nusuario));
		$this->layout->setTitle("Bonificaciones Vendedor");
		$this->layout->view("bvendedor",compact("anexos","nestablecimiento",'empresa',"canuo","cmes","nusuario","anuos","meses","usuarios","listas"));
	}

	public function pclasificacion()
	{
    if (!$this->acciones(36)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('consulta/bvendedor');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $listas=$this->pactivo_model->mostrarLimite();
		$this->layout->setTitle("Productos Clasificacion");
		$this->layout->view("pclasificacion",compact("anexos","nestablecimiento",'empresa','listas'));
	}

	public function productos()
	{
		$generico=$this->producto_model->contador(array('estado'=>1,'clasificacion'=>1));
		$marca=$this->producto_model->contador(array('estado'=>1,'clasificacion'=>2));

		$datos['generico']=$generico;
		$datos['marca']=$marca;
		echo json_encode($datos);
	}

	public function clasificacion()
	{
		$generico=$this->producto_model->contador(array('estado'=>1,'clasificacion'=>1));
		$marca=$this->producto_model->contador(array('estado'=>1,'clasificacion'=>2));
		$otro=$this->producto_model->contador(array('estado'=>1,'clasificacion'=>0));

		$datos['generico']=$generico;
		$datos['marca']=$marca;
		$datos['otro']=$otro;
		echo json_encode($datos);
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

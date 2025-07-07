<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Reporte extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
		$this->layout->setLayout("principal");
		$this->load->model("mes_model");
		$this->load->model("categoria_model");
		$this->load->model("laboratorio_model");
		$this->load->model("pactivo_model");
		$this->load->model("aterapeutica_model");
		$this->load->model("ubicacion_model");
		$this->load->model("tmovimiento_model");
		$this->load->model("egenerico_model");
		$this->load->model("lote_model");
		$this->load->model("kardex_model");
		$this->load->model("proveedor_model");
		$this->load->model("compra_model");
		$this->load->model("comprad_model");
		$this->load->model("pago_model");
		$this->load->model("cliente_model");
		$this->load->model("nventa_model");
		$this->load->model("nventad_model");
		$this->load->model("cobro_model");
		$this->load->model("venta_model");
		$this->load->model("ventad_model");
		$this->load->model("cobroe_model");
		$this->load->model("nota_model");
		$this->load->model("notad_model");
		$this->load->model("cobron_model");
		$this->load->model("resumen_model");
		$this->load->model("ingreso_model");
		$this->load->model("egreso_model");
		$this->load->model("tpago_model");
		$this->load->library("mytcpdf");
	}

  public function index()
	{
    if (!$this->acciones(13)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('reporte');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$categorias=$this->categoria_model->mostrarTotal('F');
		$salidas=$this->tmovimiento_model->mostrarTotal(array("tipo"=>"S","estado"=>1));
		$ingresos=$this->tmovimiento_model->mostrarTotal(array("tipo"=>"I","estado"=>1));
		$this->layout->setTitle("Reporte Producto");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"categorias","salidas","ingresos"));
	}

  public function busFiltros()
	{
		if ($this->input->post())
		{
			$buscar=$this->input->post('id',true);
			if ($buscar=='Lab') {
				//lista de laboratorios
				$datos=$this->laboratorio_model->mostrarTotal();
			}elseif ($buscar=='Cat') {
				//lista de laboratorios
				$datos=$this->categoria_model->mostrarTotal('F');
			}elseif ($buscar=='Pac') {
				//lista de principio activo
				$datos=$this->pactivo_model->mostrarTotal();
			}elseif ($buscar=='Ate') {
				//lista de accion terapeutica
				$datos=$this->aterapeutica_model->mostrarTotal();
			}elseif ($buscar=='Ubi') {
				//lista de ubicacion
				$datos=$this->ubicacion_model->mostrarTotal();
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function pdfminimo()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->inventario_model->productosMinimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"mstock>"=>0,"(stock-mstock)<"=>1));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfminimo",compact("empresa","nestablecimiento","datos"));
	}

	public function pdfstock()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfstock",compact("empresa","nestablecimiento"));
	}

	public function excelstock()
	{
		$empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$datos=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>1));
		$spreadsheet = IOFactory::load("./downloads/excel/productosc.xlsx");

		$i = 5;
		foreach ($datos as $dato) {
			$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->id);
    	$pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $dato->pventa;

			$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$i,  $dato->id)
					->setCellValue('B'.$i,  $dato->descripcion)
					->setCellValue('C'.$i,  $dato->nlaboratorio)
					->setCellValue('D'.$i,  $cantidad->stock)
					->setCellValue('E'.$i,  $pventa)
					->setCellValue('F'.$i,  $dato->pcompra);
			$i++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="STOCK_PRODUCTOS_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfpstock()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfpstock",compact("empresa","nestablecimiento","datos"));
	}

	public function excelpstock()
	{
		$empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$datos=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));
		$spreadsheet = IOFactory::load("./downloads/excel/productosl.xlsx");

		$i = 5;
		foreach ($datos as $dato) {
	    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->id);
	    $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $dato->pventa;

			$lotes=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$dato->id);
			if ($lotes==null) {
				$spreadsheet->setActiveSheetIndex(0)
							->setCellValue('A'.$i,  $dato->id)
							->setCellValue('B'.$i,  $dato->descripcion)
							->setCellValue('C'.$i,  $dato->nlaboratorio)
							->setCellValue('D'.$i,  '')
							->setCellValue('E'.$i,  '')
							->setCellValue('F'.$i,  '')
							->setCellValue('G'.$i,  $cantidad->stock)
							->setCellValue('H'.$i,  $pventa);
				$i++;
			}else{
				$nl=0;
				foreach ($lotes as $lote) {
					if ($nl==0) {
						$spreadsheet->setActiveSheetIndex(0)
								->setCellValue('A'.$i,  $dato->id)
								->setCellValue('B'.$i,  $dato->descripcion)
								->setCellValue('C'.$i,  $dato->nlaboratorio)
								->setCellValue('D'.$i,  $lote->stock)
								->setCellValue('E'.$i,  $lote->nlote)
								->setCellValue('F'.$i,  $lote->fvencimiento)
								->setCellValue('G'.$i,  $cantidad->stock)
								->setCellValue('H'.$i,  $pventa);
					} else {
						$spreadsheet->setActiveSheetIndex(0)
								->setCellValue('A'.$i,  '')
								->setCellValue('B'.$i,  '')
								->setCellValue('C'.$i,  '')
								->setCellValue('D'.$i,  $lote->stock)
								->setCellValue('E'.$i,  $lote->nlote)
								->setCellValue('F'.$i,  $lote->fvencimiento)
								->setCellValue('G'.$i,  '')
								->setCellValue('H'.$i,  '');
					}
					$nl++;
					$i++;
				}
			}
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="STOCK_LOTES_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfcinventario()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>1));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfcinventario",compact("empresa","nestablecimiento","datos"));
	}

	public function excelprecios()
	{
		$empresa=$this->empresa_model->mostrar();
		$listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Precios y Lotes");

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
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Producto");
		$sheet->setCellValueByColumnAndRow(3, 1,"Laboratorio");
		$sheet->setCellValueByColumnAndRow(4, 1,"R. Sanitario");
		$sheet->setCellValueByColumnAndRow(5, 1,"Precio Unidad");
		$sheet->setCellValueByColumnAndRow(6, 1,"Precio Blister");
		$sheet->setCellValueByColumnAndRow(7, 1,"Precio Caja");
		$sheet->setCellValueByColumnAndRow(8, 1,"Stock");
		$sheet->setCellValueByColumnAndRow(9, 1,"Lotes y Vcto");

		$i=2; $j=1;
		foreach ($listas as $lista) {
	    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
	    $pventa=$empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa;
	    $venta=$empresa->pestablecimiento==1 ? $cantidad->venta: $lista->venta;
	    $pblister=$empresa->pestablecimiento==1 ? $cantidad->pblister: $lista->pblister;

	    $lotes=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$lista->id);
	    $lotesProducto='';
	    $n=1;
	    $salto='';
	    foreach ($lotes as $lote) {
	        if ($n>1) {
	            $lotesProducto.="\n".'Nº: '.$lote->nlote.' - Vcto: '.FormatoFecha($lote->fvencimiento).' ('.$lote->stock.')';
	        }else{
	            $lotesProducto.='Nº: '.$lote->nlote.' - Vcto: '.FormatoFecha($lote->fvencimiento).' ('.$lote->stock.')';
	        }
	        $n++;
	    }

			$sheet->getStyle("A".$i)->applyFromArray($styleArray);
			$sheet->getStyle("B".$i)->applyFromArray($styleArray);
			$sheet->getStyle("C".$i)->applyFromArray($styleArray);
			$sheet->getStyle("D".$i)->applyFromArray($styleArray);
			$sheet->getStyle("E".$i)->applyFromArray($styleArray);
			$sheet->getStyle("F".$i)->applyFromArray($styleArray);
			$sheet->getStyle("G".$i)->applyFromArray($styleArray);
			$sheet->getStyle("H".$i)->applyFromArray($styleArray);
			$sheet->getStyle("I".$i)->applyFromArray($styleArray);

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$lista->descripcion);
			$sheet->setCellValue("C".$i,$lista->nlaboratorio);
			$sheet->setCellValue("D".$i,$lista->rsanitario);
			$sheet->setCellValue("E".$i,$pventa);
			$sheet->setCellValue("F".$i,$pblister);
			$sheet->setCellValue("G".$i,$venta);
			$sheet->setCellValue("H".$i,$cantidad->stock);
			$sheet->setCellValue("I".$i,$lotesProducto);
			$i++; $j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_PRECIOS_LOTES.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function exceldigemid()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->producto_model->productosDgmi();

		$spreadsheet = IOFactory::load("./downloads/excel/formato.xlsx");
		$i = 2;
		foreach ($datos as $dato) {
			$nproducto=$dato->descripcion;
    	if ($dato->nlaboratorio!='') {$nproducto.=' ['.$dato->nlaboratorio.']';}

			$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->id);
			$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('A'.$i,  $nestablecimiento->cdigemid)
					->setCellValue('B'.$i,  $dato->cdigemid)
					->setCellValue('C'.$i,  $empresa->pestablecimiento==1 ? $cantidad->venta: $dato->venta)
					->setCellValue('D'.$i,  $empresa->pestablecimiento==1 ? $cantidad->pventa: $dato->pventa);
			$i++;
		}

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="PRECIOS_DIGEMID_'.$nestablecimiento->descripcion.'.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}

	public function excelescencial()
	{
		$empresa=$this->empresa_model->mostrar();
		$listas=$this->pactivo_model->mostrarLimite();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Esenciales Genéricos");

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

		foreach(range("A","G") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(2, 1,"Principio Activo");
		$sheet->setCellValueByColumnAndRow(3, 1,"Medicamento Resolucion");
		$sheet->setCellValueByColumnAndRow(4, 1,"Producto");
		$sheet->setCellValueByColumnAndRow(5, 1,"Stock Marca");
		$sheet->setCellValueByColumnAndRow(6, 1,"Stock Generico");
		$sheet->setCellValueByColumnAndRow(7, 1,"Stock Minimo (".$empresa->pesencial."%)");

		$i=2; $j=1;
		foreach ($listas as $lista) {
			$egenericos=$this->egenerico_model->mostrarTotal($lista->id);
			foreach ($egenericos as $egenerico) {
				$productos=$this->producto_model->mostrarTotal(array("estado"=>1,'idpactivo'=>$lista->id,'idegenerico'=>$egenerico->id,"factor>"=>0));
				$marca=0; $generico=0;
				foreach ($productos as $producto) {
			    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);

			    foreach(range("A","G") as $columnID) {
				    $sheet->getColumnDimension($columnID)->setAutoSize(true);
				    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
					}

					// $sheet->setCellValue("A".$i,$j);
					$sheet->setCellValue("B".$i,$lista->descripcion);
					$sheet->setCellValue("C".$i,$egenerico->descripcion);
					$sheet->setCellValue("D".$i,$producto->descripcion.' '.$producto->nlaboratorio);
					if ($producto->clasificacion==2) {
						$sheet->setCellValue("E".$i,$cantidad->stock);
						$marca+=$cantidad->stock;
					} else {
						$sheet->setCellValue("F".$i,$cantidad->stock);
						$generico+=$cantidad->stock;
					}
					$i++;
				}
				if ($marca>0 || $generico>0) {
					$sheet->setCellValue("E".$i,$marca);
					$sheet->setCellValue("F".$i,$generico);
					$sheet->setCellValue("G".$i, round($marca*($empresa->pesencial/100)).' UND');
					$i++;
				}
			}
			$j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_MEDICAMENTOS_ESCENCIALES.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfatributos()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		if ($this->input->post('buscar',true)=='Lab') {
			$buscador='Laboratorio';
			$nombres=$this->laboratorio_model->mostrar($this->input->post('nombres',true));
			$datos=$this->producto_model->mostrarTotal(array("idlaboratorio"=>$this->input->post('nombres',true),'estado'=>1,"factor>"=>0));
		} elseif ($this->input->post('buscar',true)=='Cat') {
			$buscador='Categoria';
			$nombres=$this->categoria_model->mostrar($this->input->post('nombres',true));
			$datos=$this->producto_model->mostrarTotal(array("idcategoria"=>$this->input->post('nombres',true),'estado'=>1,"factor>"=>0));
		} elseif ($this->input->post('buscar',true)=='Pac') {
			$buscador='Principio Activo';
			$nombres=$this->pactivo_model->mostrar($this->input->post('nombres',true));
			$datos=$this->producto_model->mostrarTotal(array("idpactivo"=>$this->input->post('nombres',true),'estado'=>1,"factor>"=>0));
		} elseif ($this->input->post('buscar',true)=='Ate') {
			$buscador='Accion Terapeutica';
			$nombres=$this->aterapeutica_model->mostrar($this->input->post('nombres',true));
			$datos=$this->producto_model->mostrarTotal(array("idaterapeutica"=>$this->input->post('nombres',true),'estado'=>1,"factor>"=>0));
		} elseif ($this->input->post('buscar',true)=='Ubi') {
			$buscador='Ubicacion';
			$nombres=$this->ubicacion_model->mostrar($this->input->post('nombres',true));
			$datos=$this->producto_model->mostrarTotal(array("idubicacion"=>$this->input->post('nombres',true),'estado'=>1,"factor>"=>0));
		}
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfatributos",compact("empresa","nestablecimiento","datos","buscador","nombres"));
	}

	public function pdfingreso()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$mtraslado=explode('-',$this->input->post('motivoi',true));
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idtmovimiento"=>$mtraslado[0],"fecha>="=>$this->input->post('iinicio',true),"fecha<="=>$this->input->post('ifin',true));
		$datos=$this->kardex_model->mostrarTotal($filtros);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfingreso",compact("empresa","nestablecimiento","datos","mtraslado"));
	}

	public function pdfsalida()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$mtraslado=explode('-',$this->input->post('motivos',true));
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idtmovimiento"=>$mtraslado[0],"fecha>="=>$this->input->post('sinicio',true),"fecha<="=>$this->input->post('sfin',true));
		$datos=$this->kardex_model->mostrarTotal($filtros);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfsalida",compact("empresa","nestablecimiento","datos","mtraslado"));
	}

	public function pdfpsicotropico()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->producto_model->mostrar(array("p.id"=>$this->input->post('idproducto',true)));

		$fecha=explode('-', $this->input->post('minicia',true));
		$meses=$this->mes_model->mostrar($fecha[1]);
		$listas=$this->compra_model->psicotropicos($fecha[0],$fecha[1],$this->input->post('idproducto',true));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfpsicotropico",compact("empresa","nestablecimiento","datos",'fecha','meses','listas'));
	}

	public function pdfvencido()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->lote_model->productosVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fvencimiento<="=>SumarFecha($this->input->post('fvencer',true)),"estado"=>1,"stock>"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfvencido",compact("empresa","nestablecimiento","datos","fecha"));
	}

	public function excelvencido()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$listas=$this->lote_model->productosVencer(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"fvencimiento<="=>SumarFecha($this->input->post('fvencer',true)),"estado"=>1,"stock>"=>0));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Precios");

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

		foreach(range("A","E") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Descripcion");
		$sheet->setCellValueByColumnAndRow(3, 1,"Lote");
		$sheet->setCellValueByColumnAndRow(4, 1,"F. Vcto");
		$sheet->setCellValueByColumnAndRow(5, 1,"Cantidad");

		$i=2; $j=1;
		foreach ($listas as $lista) {
			$sheet->getStyle("A".$i)->applyFromArray($styleArray);
			$sheet->getStyle("B".$i)->applyFromArray($styleArray);
			$sheet->getStyle("C".$i)->applyFromArray($styleArray);
			$sheet->getStyle("D".$i)->applyFromArray($styleArray);
			$sheet->getStyle("E".$i)->applyFromArray($styleArray);

			$nproducto=$lista->descripcion;
    	if ($lista->nlaboratorio!='') {$nproducto.=' ['.$lista->nlaboratorio.']';}

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$nproducto);
			$sheet->setCellValue("C".$i,$lista->lote);
			$sheet->setCellValue("D".$i,$lista->fvencimiento);
			$sheet->setCellValue("E".$i,$lista->stock);

			$i++; $j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="PRODUCTOS_VENCIDOS_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfclasificacion()
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		if ($this->input->post('clasificacion',true)==1) {
			$buscador='Generico';
		} elseif ($this->input->post('clasificacion',true)==2) {
			$buscador='Marca';
		} else {
			$buscador='Otros';
		}
		$datos=$this->producto_model->mostrarTotal(array("clasificacion"=>$this->input->post('clasificacion',true),'estado'=>1,"factor>"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfclasificacion",compact("empresa","nestablecimiento","datos","buscador"));
	}

	public function compras()
	{
    if (!$this->acciones(11)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('reporte/compras');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$this->layout->setTitle("Reporte Compra");
		$this->layout->view("compras",compact("anexos","nestablecimiento",'empresa'));
	}

	public function pdfcompra()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true));
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$datos=$this->compra_model->mostrarTotal($filtros);
		$detallado=valor_check($this->input->post('detallado',true));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfcompra",compact("empresa","nestablecimiento","datos","detallado"));
  }

	public function excelcompra()
	{
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true));
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$compras=$this->compra_model->mostrarTotal($filtros,"asc");
		$detallado=valor_check($this->input->post('detallado',true));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Compras");

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
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 1,"Tipo");
		$sheet->setCellValueByColumnAndRow(4, 1,"Numero");
		$sheet->setCellValueByColumnAndRow(5, 1,"Proveedor");
		$sheet->setCellValueByColumnAndRow(6, 1,"Subtotal");
		$sheet->setCellValueByColumnAndRow(7, 1,"IGV");
		$sheet->setCellValueByColumnAndRow(8, 1,"Total");
		$sheet->setCellValueByColumnAndRow(9, 1,"Percepcion");

		if ($detallado==1) {
			$sheet->setCellValueByColumnAndRow(10, 1,"Descripcion");
			$sheet->setCellValueByColumnAndRow(11, 1,"Unidad");
			$sheet->setCellValueByColumnAndRow(12, 1,"Cantidad");
			$sheet->setCellValueByColumnAndRow(13, 1,"Lote");
			$sheet->setCellValueByColumnAndRow(14, 1,"Fecha Vcto");
			$sheet->setCellValueByColumnAndRow(15, 1,"Precio Unit.");
		}

		$i=2; $j=1;
		foreach ($compras as $dato) {
			foreach(range("A","I") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->ncomprobante);
			$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("E".$i,$dato->proveedor);
			$sheet->setCellValue("F".$i,$dato->subtotal);
			$sheet->setCellValue("G".$i,$dato->igv);
			$sheet->setCellValue("H".$i,$dato->total);
			$sheet->setCellValue("I".$i,$dato->percepcion);

			if ($detallado==1) {
				$detalles=$this->comprad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle)
		    {
		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('J'.$i,$detalle->descripcion)
						->setCellValue('K'.$i,$detalle->unidad)
						->setCellValue('L'.$i,$detalle->cantidad)
						->setCellValue('M'.$i,$detalle->lote)
						->setCellValue('N'.$i,$detalle->fvencimiento)
						->setCellValue('O'.$i,$detalle->precio);
					$i++;
		    }
			}
      if ($detallado==0) {
				$i++;
      }
      $j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_COMPRAS.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfproductoc()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->compra_model->cproducto($this->session->userdata("predeterminado"),$this->input->post('finicio',true),$this->input->post('ffin',true),$this->input->post('idproducto',true));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfproductoc",compact("empresa","nestablecimiento","datos"));
  }

	public function excelproductoc()
	{
		$empresa=$this->empresa_model->mostrar();
		$compras=$this->compra_model->cproducto($this->session->userdata("predeterminado"),$this->input->post('finicio',true),$this->input->post('ffin',true),$this->input->post('idproducto',true));
		$notas=$this->cnota_model->cproducto($this->session->userdata("predeterminado"),$this->input->post('finicio',true),$this->input->post('ffin',true),$this->input->post('idproducto',true));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Compras");

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

		foreach(range("A","K") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 1,"Tipo");
		$sheet->setCellValueByColumnAndRow(4, 1,"Numero");
		$sheet->setCellValueByColumnAndRow(5, 1,"Proveedor");
		$sheet->setCellValueByColumnAndRow(6, 1,"Moneda");
		$sheet->setCellValueByColumnAndRow(7, 1,"Cantidad");
		$sheet->setCellValueByColumnAndRow(8, 1,"Unidad");
		$sheet->setCellValueByColumnAndRow(9, 1,"Impuesto");
		$sheet->setCellValueByColumnAndRow(10, 1,"Precio");
		$sheet->setCellValueByColumnAndRow(11, 1,"Total");

		$i=2; $j=1;
		foreach ($compras as $dato) {
			foreach(range("A","K") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			$impuesto='';
      if ($dato->tafectacion=='10') {
          $impuesto=$dato->incluye==0 ? 's/igv' : 'c/igv' ;
      }

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->ncomprobante);
			$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("E".$i,$dato->proveedor);
			$sheet->setCellValue("F".$i,$dato->moneda);
			$sheet->setCellValue("G".$i,$dato->cantidad);
			$sheet->setCellValue("H".$i,$dato->unidad);
			$sheet->setCellValue("I".$i,$impuesto);
			$sheet->setCellValue("J".$i,$dato->precio);
			$sheet->setCellValue("K".$i,$dato->importe);
			$i++; $j++;
		}

		foreach ($notas as $dato) {
			foreach(range("A","K") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			$impuesto='';
      if ($dato->tafectacion=='10') {
          $impuesto=$dato->incluye==0 ? 's/igv' : 'c/igv' ;
      }

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->ncomprobante);
			$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("E".$i,$dato->proveedor);
			$sheet->setCellValue("F".$i,$dato->moneda);
			$sheet->setCellValue("G".$i,$dato->cantidad);
			$sheet->setCellValue("H".$i,$dato->unidad);
			$sheet->setCellValue("I".$i,$impuesto);
			$sheet->setCellValue("J".$i,$dato->precio);
			$sheet->setCellValue("K".$i,$dato->importe);
			$i++; $j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_COMPRAS_PRODUCTOS.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function pdfproveedor()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
  	$empresa=$this->empresa_model->mostrar();
  	$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('pinicio',true),"femision<="=>$this->input->post('pfin',true),"idproveedor"=>$this->input->post('idproveedor',true));
  	$datos=$this->compra_model->mostrarTotal($filtros);
  	$detallado=valor_check($this->input->post('detalladop',true));
  	$this->layout->setLayout("blanco");
  	$this->layout->view("pdfproveedor",compact("empresa","nestablecimiento","datos","detallado"));
  }

	public function excelproveedor()
	{
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('pinicio',true),"femision<="=>$this->input->post('pfin',true),"idproveedor"=>$this->input->post('idproveedor',true));
  	$compras=$this->compra_model->mostrarTotal($filtros);
		$notas=$this->cnota_model->mostrarTotal($filtros);
  	$detallado=valor_check($this->input->post('detalladop',true));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Compras");

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

		foreach(range("A","J") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 1,"Tipo");
		$sheet->setCellValueByColumnAndRow(4, 1,"Numero");
		$sheet->setCellValueByColumnAndRow(5, 1,"Proveedor");
		$sheet->setCellValueByColumnAndRow(6, 1,"Moneda");
		$sheet->setCellValueByColumnAndRow(7, 1,"Subtotal");
		$sheet->setCellValueByColumnAndRow(8, 1,"IGV");
		$sheet->setCellValueByColumnAndRow(9, 1,"Total");
		$sheet->setCellValueByColumnAndRow(10, 1,"Percepcion");

		if ($detallado==1) {
			$sheet->setCellValueByColumnAndRow(11, 1,"Descripcion");
			$sheet->setCellValueByColumnAndRow(12, 1,"Unidad");
			$sheet->setCellValueByColumnAndRow(13, 1,"Cantidad");
			$sheet->setCellValueByColumnAndRow(14, 1,"Lote");
			$sheet->setCellValueByColumnAndRow(15, 1,"Fecha Vcto");
			$sheet->setCellValueByColumnAndRow(16, 1,"Precio Unit.");
		}

		$i=2; $j=1;
		foreach ($compras as $dato) {
			foreach(range("A","J") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->ncomprobante);
			$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("E".$i,$dato->proveedor);
			$sheet->setCellValue("F".$i,$dato->moneda);
			$sheet->setCellValue("G".$i,$dato->subtotal);
			$sheet->setCellValue("H".$i,$dato->igv);
			$sheet->setCellValue("I".$i,$dato->total);
			$sheet->setCellValue("J".$i,$dato->percepcion);

			if ($detallado==1) {
				$detalles=$this->comprad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle)
		    {
		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('K'.$i,$detalle->descripcion)
						->setCellValue('L'.$i,$detalle->unidad)
						->setCellValue('M'.$i,$detalle->cantidad)
						->setCellValue('N'.$i,$detalle->lote)
						->setCellValue('O'.$i,$detalle->fvencimiento)
						->setCellValue('P'.$i,$detalle->precio);
					$i++;
		    }
			}
			$i++; $j++;
		}

		foreach ($notas as $dato) {
			foreach(range("A","J") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}
      $compras=$this->compra_model->mostrar($dato->idcompra);

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->ncomprobante);
			$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("E".$i,$dato->proveedor);
			$sheet->setCellValue("F".$i,$dato->moneda);
			$sheet->setCellValue("G".$i,$dato->subtotal);
			$sheet->setCellValue("H".$i,$dato->igv);
			$sheet->setCellValue("I".$i,$dato->total);
			$sheet->setCellValue("J".$i,$compras->serie.'-'.$compras->numero);

			if ($detallado==1) {
				$detalles=$this->cnotad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle)
		    {
		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('K'.$i,$detalle->descripcion)
						->setCellValue('L'.$i,$detalle->unidad)
						->setCellValue('M'.$i,$detalle->cantidad)
						->setCellValue('N'.$i,$detalle->lote)
						->setCellValue('O'.$i,$detalle->fvencimiento)
						->setCellValue('P'.$i,$detalle->precio);
					$i++;
		    }
			}
			$i++; $j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_COMPRAS_PROVEEDOR.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function ventas()
	{
    if (!$this->acciones(39)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('reporte/ventas');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$usuarios=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$anuos=$this->periodo_model->mostrarTotal();
		$meses=$this->mes_model->mostrarTotal();
		$this->layout->setTitle("Reporte Ventas");
		$this->layout->view("ventas",compact("anexos","nestablecimiento","empresa","usuarios","anuos","meses"));
	}

	public function pdfventa()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true));
		if ($this->input->post('cusuario',true)!='') {$filtros['iduser']=$this->input->post('cusuario',true);}
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$ventas=$this->venta_model->mostrarTotal($filtros,"asc");
		$notas=$this->nota_model->mostrarTotal($filtros,"asc");
  	$nventas=$this->nventa_model->mostrarTotal($filtros,"asc");
		$detallado=valor_check($this->input->post('detallado',true));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfventa",compact("empresa","nestablecimiento","ventas","notas","nventas","detallado"));
  }

	public function excelventa()
	{
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true));
		if ($this->input->post('cusuario',true)!='') {$filtros['iduser']=$this->input->post('cusuario',true);}
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$ventas=$this->venta_model->mostrarTotal($filtros,"asc");
		$notas=$this->nota_model->mostrarTotal($filtros,"asc");
  	$nventas=$this->nventa_model->mostrarTotal($filtros,"asc");
		$detallado=valor_check($this->input->post('detallado',true));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Ventas");

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

		foreach(range("A","K") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 1,"Hora");
		$sheet->setCellValueByColumnAndRow(4, 1,"Tipo");
		$sheet->setCellValueByColumnAndRow(5, 1,"Numero");
		$sheet->setCellValueByColumnAndRow(6, 1,"Cliente");
		$sheet->setCellValueByColumnAndRow(7, 1,"Subtotal");
		$sheet->setCellValueByColumnAndRow(8, 1,"IGV");
		$sheet->setCellValueByColumnAndRow(9, 1,"Total");
		$sheet->setCellValueByColumnAndRow(10, 1,"Izipay");
		$sheet->setCellValueByColumnAndRow(11, 1,"Usuario");

		if ($detallado==1) {
			$sheet->setCellValueByColumnAndRow(12, 1,"Descripcion");
			$sheet->setCellValueByColumnAndRow(13, 1,"Unidad");
			$sheet->setCellValueByColumnAndRow(14, 1,"Cantidad");
			$sheet->setCellValueByColumnAndRow(15, 1,"Lote");
			$sheet->setCellValueByColumnAndRow(16, 1,"Fecha Vcto");
			$sheet->setCellValueByColumnAndRow(17, 1,"Precio Unit.");
		}

		$i=2; $j=1;
		foreach ($ventas as $dato) {
			foreach(range("A","K") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
      	if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

					$sheet->setCellValue("A".$i,$j);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->hemision);
					$sheet->setCellValue("D".$i,$dato->ncomprobante);
					$sheet->setCellValue("E".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("F".$i,$nestado);
					$sheet->setCellValue("G".$i,0);
					$sheet->setCellValue("H".$i,0);
					$sheet->setCellValue("I".$i,0);
					$sheet->setCellValue("J".$i,0);
					$sheet->setCellValue("K".$i,'');
      } else {
          $nombre= $this->usuario_model->mostrar($dato->iduser);
          $nusuario=$nombre->nombres??'';

					$sheet->setCellValue("A".$i,$j);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->hemision);
					$sheet->setCellValue("D".$i,$dato->ncomprobante);
					$sheet->setCellValue("E".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("F".$i,$dato->cliente);
					$sheet->setCellValue("G".$i,$dato->subtotal);
					$sheet->setCellValue("H".$i,$dato->tigv);
					$sheet->setCellValue("I".$i,$dato->total);
					$sheet->setCellValue("J".$i,$dato->izipay);
					$sheet->setCellValue("K".$i,$nusuario);

					if ($detallado==1) {
						$detalles=$this->ventad_model->mostrarTotal($dato->id);
						foreach($detalles as $detalle)
				    {
				      $spreadsheet->setActiveSheetIndex(0)
								->setCellValue('L'.$i,$detalle->descripcion)
								->setCellValue('M'.$i,$detalle->unidad)
								->setCellValue('N'.$i,$detalle->cantidad)
								->setCellValue('O'.$i,$detalle->lote)
								->setCellValue('P'.$i,$detalle->fvencimiento)
								->setCellValue('Q'.$i,$detalle->precio);
							$i++;
				    }
					}
      }
      if ($detallado==0) {
				$i++;
      }
      $j++;
		}

		foreach ($notas as $dato) {
			foreach(range("A","K") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
      	if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

					$sheet->setCellValue("A".$i,$j);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->hemision);
					$sheet->setCellValue("D".$i,$dato->ncomprobante);
					$sheet->setCellValue("E".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("F".$i,$nestado);
					$sheet->setCellValue("G".$i,'');
					$sheet->setCellValue("H".$i,'');
					$sheet->setCellValue("I".$i,'');
					$sheet->setCellValue("J".$i,'');
					$sheet->setCellValue("K".$i,'');
      } else {
          $nombre= $this->usuario_model->mostrar($dato->iduser);
          $nusuario=$nombre->nombres??'';

					$sheet->setCellValue("A".$i,$j);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->hemision);
					$sheet->setCellValue("D".$i,$dato->ncomprobante);
					$sheet->setCellValue("E".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("F".$i,$dato->cliente);
					$sheet->setCellValue("G".$i,$dato->subtotal*-1);
					$sheet->setCellValue("H".$i,$dato->tigv*-1);
					$sheet->setCellValue("I".$i,$dato->total*-1);
					$sheet->setCellValue("J".$i,'');
					$sheet->setCellValue("K".$i,$nusuario);

					if ($detallado==1) {
						$detalles=$this->notad_model->mostrarTotal($dato->id);
						foreach($detalles as $detalle)
				    {
				      $spreadsheet->setActiveSheetIndex(0)
								->setCellValue('L'.$i,$detalle->descripcion)
								->setCellValue('M'.$i,$detalle->unidad)
								->setCellValue('N'.$i,$detalle->cantidad)
								->setCellValue('O'.$i,$detalle->lote)
								->setCellValue('P'.$i,$detalle->fvencimiento)
								->setCellValue('Q'.$i,$detalle->precio);
							$i++;
				    }
					}
      }
      if ($detallado==0) {
				$i++;
      }
      $j++;
		}

		foreach ($nventas as $dato) {
			foreach(range("A","K") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

			$nombre= $this->usuario_model->mostrar($dato->iduser);
      $nusuario=$nombre->nombres??'';

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$dato->hemision);
			$sheet->setCellValue("D".$i,'Nota de Venta');
			$sheet->setCellValue("E".$i,$dato->serie.'-'.$dato->numero);
			$sheet->setCellValue("F".$i,$dato->cliente);
			$sheet->setCellValue("G".$i,'');
			$sheet->setCellValue("H".$i,'');
			$sheet->setCellValue("I".$i,$dato->total);
			$sheet->setCellValue("J".$i,$dato->izipay);
			$sheet->setCellValue("K".$i,$nusuario);

			if ($detallado==1 && $dato->nulo==0) {
				$detalles=$this->nventad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle)
		    {
		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('L'.$i,$detalle->descripcion)
						->setCellValue('M'.$i,$detalle->unidad)
						->setCellValue('N'.$i,$detalle->cantidad)
						->setCellValue('O'.$i,$detalle->lote)
						->setCellValue('P'.$i,$detalle->fvencimiento)
						->setCellValue('Q'.$i,$detalle->precio);
					$i++;
		    }
			}
      if ($detallado==0) {
				$i++;
      }
			$j++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_VENTAS.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfproductov()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
  	if ($this->input->post('nusuario',true)=='') {
  		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('finicio',true),"femision<="=>$this->input->post('ffin',true),"idproducto"=>$this->input->post('idproducto',true));
  	}else{
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"iduser"=>$this->input->post('nusuario',true),"femision>="=>$this->input->post('finicio',true),"femision<="=>$this->input->post('ffin',true),"idproducto"=>$this->input->post('idproducto',true));
  	}
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$nventas=$this->nventa_model->vproducto($filtros);
		$ventas=$this->venta_model->vproducto($filtros);
		$notas=$this->nota_model->vproducto($filtros);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfproductov",compact("empresa","nestablecimiento","nventas","ventas","notas"));
  }

  public function pdfcliente()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
  	$empresa=$this->empresa_model->mostrar();
  	$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('pinicio',true),"femision<="=>$this->input->post('pfin',true),"idcliente"=>$this->input->post('idcliente',true));

  	$comprobantes=$this->venta_model->mostrarTotal($filtros,"asc");
  	$notas=$this->nota_model->mostrarTotal($filtros,"asc");
  	$datos=$this->nventa_model->mostrarTotal($filtros,"asc");
  	$detallado=valor_check($this->input->post('detalladop',true));
  	$this->layout->setLayout("blanco");
  	$this->layout->view("pdfcliente",compact("empresa","nestablecimiento","datos","notas","comprobantes","detallado"));
  }

  public function pdfvendible()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nventa_model->ganancia(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('vinicio',true),"femision<="=>$this->input->post('vfin',true),"nulo"=>0));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfvendible",compact("empresa","nestablecimiento","datos"));
  }

	public function pdfusuario()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
    $listas=$this->usuario_model->mostrarTotal(array('estado'=>1));
    $ncreditos=$this->nota_model->montoTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('uinicio',true),"femision<="=>$this->input->post('ufin',true)));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfusuario",compact("empresa","nestablecimiento","listas",'ncreditos'));
  }

	public function pdfhoras()
	{
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('finicio',true),"femision<="=>$this->input->post('ffin',true),"hemision>="=>$this->input->post('hinicio',true),"hemision<="=>$this->input->post('hfin',true));
		$filtrosn=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('finicio',true),"femision<="=>$this->input->post('ffin',true),"hemision>="=>$this->input->post('hinicio',true),"hemision<="=>$this->input->post('hfin',true));

		$ventas=$this->venta_model->mostrarTotal($filtros,"asc");
		$notas=$this->nota_model->mostrarTotal($filtrosn,"asc");
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfhoras",compact("empresa","nestablecimiento","ventas","notas"));
	}

	public function excelmensual()
	{
  	$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
  	$meses=$this->input->post('tmeses',true);
  	$anuos=$this->input->post('tanuos',true);
    //1 mes anterior
  	$anteriorm=$meses-1; $anteriora=$anuos;
  	if ($anteriorm<1) {$anteriorm=$anteriorm+12; $anteriora-=1;}
    //2 meses anterior
  	$tanteriorm=$anteriorm-1; $tanteriora=$anteriora;
  	if ($tanteriorm<1) {$tanteriorm=$tanteriorm+12; $tanteriora-=1;}

		$listas=$this->producto_model->mostrarTotal(array("estado"=>1,"tipo"=>"B"));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Venta Trimestral");

		$sheet->mergeCells("A1:F1");
		$sheet->mergeCells("A2:F2");

		$sheet->getStyle("A1:F3")->getFont()->setBold(true);
		$sheet->getStyle("A1:F3")->getAlignment()->setHorizontal("center");

		$sheet->setCellValueByColumnAndRow(1, 1,"PRODUCTOS VENDIDOS TRIMESTRALMENTE");

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

		foreach(range("A","F") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."3")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 3,"#");
		$sheet->setCellValueByColumnAndRow(2, 3,"Producto");
		$sheet->setCellValueByColumnAndRow(3, 3,"Stock");
		$sheet->setCellValueByColumnAndRow(4, 3,zerofill($meses,2).'/'.$anuos);
		$sheet->setCellValueByColumnAndRow(5, 3,zerofill($anteriorm,2).'/'.$anteriora);
		$sheet->setCellValueByColumnAndRow(6, 3,zerofill($tanteriorm,2).'/'.$tanteriora);

    $i=4; $j=1;
    foreach ($listas as $dato) {
			foreach(range("A","F") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}

      $stock=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$dato->id);
      //1 mes
      $cantidadn=$this->nventa_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$anuos,"month(v.femision)"=>$meses,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $cantidadv=$this->venta_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$anuos,"month(v.femision)"=>$meses,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $vendidos=$cantidadn->calmacen+$cantidadv->calmacen;
      //2 mes
      $acantidadn=$this->nventa_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$anteriora,"month(v.femision)"=>$anteriorm,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $acantidadv=$this->venta_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$anteriora,"month(v.femision)"=>$anteriorm,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $avendidos=$acantidadn->calmacen+$acantidadv->calmacen;
      //3 meses
      $tcantidadn=$this->nventa_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$tanteriora,"month(v.femision)"=>$tanteriorm,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $tcantidadv=$this->venta_model->productoTotal(array("v.idestablecimiento"=>$this->session->userdata("predeterminado"),"year(v.femision)"=>$tanteriora,"month(v.femision)"=>$tanteriorm,"v.nulo"=>0,"d.idproducto"=>$dato->id));
      $tvendidos=$tcantidadn->calmacen+$tcantidadv->calmacen;

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->descripcion.' '.$dato->nlaboratorio);
			$sheet->setCellValue("C".$i,$stock->stock??0);
			$sheet->setCellValue("D".$i,$vendidos);
			$sheet->setCellValue("E".$i,$avendidos);
			$sheet->setCellValue("F".$i,$tvendidos);
			$i++; $j++;
		}

		$writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="VENTA_TRIMESTRAL_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function pdfanual()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$listas=$this->mes_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfanual",compact("empresa","nestablecimiento","listas"));
  }

	public function caja()
	{
    if (!$this->acciones(39)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('reporte/caja');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$usuarios=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Reporte Caja");
		$this->layout->view("caja",compact("anexos","nestablecimiento",'empresa',"usuarios"));
	}

	public function excelcobros()
	{
		$empresa=$this->empresa_model->mostrar();
		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision>="=>$this->input->post('ginicio',true),"femision<="=>$this->input->post('gfin',true));
		if ($this->input->post('gusuario',true)!='') {
			$filtros['iduser']=$this->input->post('gusuario',true);
		}
    $nventas=$this->cobro_model->mostrarTotal($filtros);
    $ventas=$this->cobroe_model->mostrarTotal($filtros);
    //$notas=$this->cobron_model->mostrarTotal($filtros);

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Cobros");

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

		foreach(range("A","G") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"#");
		$sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 1,"Numero Origen");
		$sheet->setCellValueByColumnAndRow(4, 1,"Cliente");
		$sheet->setCellValueByColumnAndRow(5, 1,"Total");
		$sheet->setCellValueByColumnAndRow(6, 1,"Medio Pago");
		$sheet->setCellValueByColumnAndRow(7, 1,"Doc Sustenta");

		$i=2; $j=1;
		foreach ($nventas as $dato) {
			foreach(range("A","G") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}
      $venta=$this->nventa_model->mostrar($dato->idnventa);
      $clientes=$this->cliente_model->mostrar($venta->idcliente);

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$venta->serie.'-'.$venta->numero);
			$sheet->setCellValue("D".$i,$venta->cliente);
			$sheet->setCellValue("E".$i,$dato->total);
			$sheet->setCellValue("F".$i,$dato->ntpago);
			$sheet->setCellValue("G".$i,$dato->documento);
			$i++; $j++;
		}

		foreach ($ventas as $dato) {
			foreach(range("A","G") as $columnID) {
		    $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
			}
      $venta=$this->venta_model->mostrar($dato->idventa);
      $clientes=$this->cliente_model->mostrar($venta->idcliente);

			$sheet->setCellValue("A".$i,$j);
			$sheet->setCellValue("B".$i,$dato->femision);
			$sheet->setCellValue("C".$i,$venta->serie.'-'.$venta->numero);
			$sheet->setCellValue("D".$i,$venta->cliente);
			$sheet->setCellValue("E".$i,$dato->total);
			$sheet->setCellValue("F".$i,$dato->ntpago);
			$sheet->setCellValue("G".$i,$dato->documento);
			$i++; $j++;
		}

		// foreach ($notas as $dato) {
		// 	foreach(range("A","G") as $columnID) {
		//     $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
		// 	}
    //   $venta=$this->nota_model->mostrar($dato->idnota);
    //   $clientes=$this->cliente_model->mostrar($venta->idcliente);

		// 	$sheet->setCellValue("A".$i,$j);
		// 	$sheet->setCellValue("B".$i,$dato->femision);
		// 	$sheet->setCellValue("C".$i,$venta->serie.'-'.$venta->numero);
		// 	$sheet->setCellValue("D".$i,$venta->cliente);
		// 	$sheet->setCellValue("E".$i,$dato->total*-1);
		// 	$sheet->setCellValue("F".$i,$dato->ntpago);
		// 	$i++; $j++;
		// }

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_COBROS.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function pdfpagar()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
  	$empresa=$this->empresa_model->mostrar();
  	$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('pinicio',true),"femision<="=>$this->input->post('pfin',true),"idproveedor"=>$this->input->post('idproveedor',true),"nulo"=>0,"cancelado"=>0);
  	$datos=$this->compra_model->mostrarTotal($filtros);
  	$this->layout->setLayout("blanco");
  	$this->layout->view("pdfpagar",compact("empresa","nestablecimiento","datos"));
  }

  public function pdfcobrar()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
  	$empresa=$this->empresa_model->mostrar();
  	$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true),"idcliente"=>$this->input->post('idcliente',true),"nulo"=>0,"cancelado"=>0);

  	$datos=$this->nventa_model->mostrarTotal($filtros,"asc");
  	$comprobantes=$this->venta_model->mostrarTotal($filtros,"asc");
  	$this->layout->setLayout("blanco");
  	$this->layout->view("pdfcobrar",compact("empresa","nestablecimiento","datos","comprobantes"));
  }

	public function pdfmedios()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
    $mpagos=$this->tpago_model->mostrarTotal();
		$detallado=valor_check($this->input->post('detallado',true));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfmedios",compact("empresa","nestablecimiento","mpagos","detallado"));
  }

  public function contable()
	{
    if (!$this->acciones(33)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('reporte/contable');
    if (!is_numeric($this->session->userdata('codigo'))){redirect(base_url().'inicio');}
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$anuos=$this->periodo_model->mostrarTotal();
		$meses=$this->mes_model->mostrarTotal();
		$this->layout->setTitle("Registros Contables");
		$this->layout->view("contable",compact("anexos","nestablecimiento",'empresa',"anuos","meses"));
	}

	public function cexcel()
	{
		$empresa=$this->empresa_model->mostrar();
		$year=$this->input->post('canuo',true);
		$month=$this->input->post('cmes',true);
		$datos=$this->compra_model->mostrarRegistro(array("nulo"=>0,"year(femision)"=>$year,"month(femision)"=>$month));

		$spreadsheet = IOFactory::load("./downloads/excel/formato81.xlsx");

		$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B3',  $year.'-'.$month)
					->setCellValue('B4',  $empresa->ruc)
					->setCellValue('E5',  $empresa->nombres);

		$i = 14;
		foreach ($datos as $dato) {
			$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B'.$i,  $dato->femision)
					->setCellValue('D'.$i,  $dato->comprobante)
					->setCellValue('E'.$i,  $dato->serie)
					->setCellValue('G'.$i,  $dato->numero)
					->setCellValue('H'.$i,  $dato->tdocumento)
					->setCellValue('I'.$i,  $dato->documento)
					->setCellValue('J'.$i,  $dato->proveedor)
					->setCellValue('K'.$i,  $dato->tgravado)
					->setCellValue('L'.$i,  $dato->igv)
					->setCellValue('Q'.$i,  $dato->tinafecto+$dato->texonerado)
					->setCellValue('T'.$i,  $dato->total);
			$i++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="REGISTRO_COMPRAS_'.$year.$month.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function vexcel()
	{
		$empresa=$this->empresa_model->mostrar();
		$year=$this->input->post('vanuo',true);
		$month=$this->input->post('vmes',true);
		$datos=$this->venta_model->mostrarRegistro(array("year(femision)"=>$year,"month(femision)"=>$month));
		$notas=$this->nota_model->mostrarRegistro(array("year(femision)"=>$year,"month(femision)"=>$month));

		$spreadsheet = IOFactory::load("./downloads/excel/formato141.xlsx");

		$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B3',  $year.'-'.$month)
					->setCellValue('B4',  $empresa->ruc)
					->setCellValue('D5',  $empresa->nombres);

		$i = 12;
		foreach ($datos as $dato) {
			if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
        if ($dato->tipo_estado=='09') {$nestado='Rechazado';} else {$nestado='Anulado';}

				$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B'.$i,  $dato->femision)
					->setCellValue('D'.$i,  $dato->tcomprobante)
					->setCellValue('E'.$i,  $dato->serie)
					->setCellValue('F'.$i,  $dato->numero)
					->setCellValue('G'.$i,  0)
					->setCellValue('H'.$i,  0)
					->setCellValue('I'.$i,  $nestado)
					->setCellValue('K'.$i,  0)
					->setCellValue('O'.$i,  0)
					->setCellValue('Q'.$i,  0);
			}else{
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue('B'.$i,  $dato->femision)
						->setCellValue('D'.$i,  $dato->tcomprobante)
						->setCellValue('E'.$i,  $dato->serie)
						->setCellValue('F'.$i,  $dato->numero)
						->setCellValue('G'.$i,  $dato->tdocumento)
						->setCellValue('H'.$i,  $dato->documento)
						->setCellValue('I'.$i,  $dato->cliente)
						->setCellValue('K'.$i,  $dato->tgravado)
						->setCellValue('L'.$i,  $dato->texonerado)
						->setCellValue('M'.$i,  $dato->tinafecto)
						->setCellValue('O'.$i,  $dato->tigv)
						->setCellValue('Q'.$i,  $dato->total);
			}
			$i++;
		}

		foreach ($notas as $dato) {
			$ventas=$this->venta_model->mostrar($dato->idventa);
			$spreadsheet->setActiveSheetIndex(0)
					->setCellValue('B'.$i,  $dato->femision)
					->setCellValue('D'.$i,  $dato->tcomprobante)
					->setCellValue('E'.$i,  $dato->serie)
					->setCellValue('F'.$i,  $dato->numero)
					->setCellValue('G'.$i,  $dato->tdocumento)
					->setCellValue('H'.$i,  $dato->documento)
					->setCellValue('I'.$i,  $dato->cliente)
					->setCellValue('K'.$i,  $dato->tgravado)
					->setCellValue('L'.$i,  $dato->texonerado)
					->setCellValue('M'.$i,  $dato->tinafecto)
					->setCellValue('O'.$i,  $dato->tigv)
					->setCellValue('Q'.$i,  $dato->total)
					->setCellValue('S'.$i,  $ventas->femision)
					->setCellValue('T'.$i,  $ventas->tcomprobante)
					->setCellValue('U'.$i,  $ventas->serie)
					->setCellValue('V'.$i,  $ventas->numero);
			$i++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="REGISTRO_VENTAS_'.$year.$month.'.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function directorio()
	{
		$empresa=$this->empresa_model->mostrar();
		$fecha=$empresa->ruc.'-'.$this->input->post('danuo',true).'-'.$this->input->post('dmes',true);
		$this->load->library('zip');

		$facturas=$this->venta_model->mostrarTotal(array("tcomprobante"=>'01'),"asc");
		$notasf=$this->nota_model->mostrarTotal(array("v.tcomprobante"=>'01'),"asc");

		foreach ($facturas as $dato) {
			$nombre=$empresa->ruc.'-'.$dato->tcomprobante.'-'.$dato->serie.'-'.$dato->numero;
			$path = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/signed/'.$nombre.'.xml';
			$new_path = $fecha.'/factura/'.$nombre.'.xml';
			$this->zip->read_file($path, $new_path);

			$patr = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/cdr/R-'.$nombre.'.zip';
			$new_patr = $fecha.'/factura/R-'.$nombre.'.zip';
			$this->zip->read_file($patr, $new_patr);
		}

		foreach ($notasf as $dato) {
			$nombre=$empresa->ruc.'-'.$dato->tcomprobante.'-'.$dato->serie.'-'.$dato->numero;
			$path = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/signed/'.$nombre.'.xml';
			$new_path = $fecha.'/factura/'.$nombre.'.xml';
			$this->zip->read_file($path, $new_path);

			$patr = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/cdr/R-'.$nombre.'.zip';
			$new_patr = $fecha.'/factura/R-'.$nombre.'.zip';
			$this->zip->read_file($patr, $new_patr);
		}

		$inicio=primer_dia_mes($this->input->post('dmes',true),$this->input->post('danuo',true));
		$fin=ultimo_dia_mes($this->input->post('dmes',true),$this->input->post('danuo',true));
		$resumenes=$this->resumen_model->mostrarTotal(array('fdocumento>='=>$inicio,'fdocumento<='=>$fin,"tproceso"=>1));

		foreach ($resumenes as $dato) {
			$nombre=$empresa->ruc.'-'.$dato->identificador;
			$path = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/signed/'.$nombre.'.xml';
			$new_path = $fecha.'/boleta/'.$nombre.'.xml';
			$this->zip->read_file($path, $new_path);

			$patr = 'C:/laragon/www/apifacturalo/storage/app/tenancy/tenants/tenancy_farmacia/cdr/R-'.$nombre.'.zip';
			$new_patr = $fecha.'/boleta/R-'.$nombre.'.zip';
			$this->zip->read_file($patr, $new_patr);
		}

		$this->zip->download($fecha.'.zip');
	}

  public function consolidado()
	{
    if (!$this->acciones(46)){redirect(base_url()."inicio");}
		$controlip=$this->controlip('reporte/consolidado');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$this->layout->setTitle("Reporte Ventas");
		$this->layout->view("consolidado",compact("anexos","nestablecimiento",'empresa'));
	}

	public function excelcompras()
	{
		$datos=$this->compra_model->mostrarTotal(array("nulo"=>0,"femision>="=>$this->input->post('cinicio',true),"femision<="=>$this->input->post('cfin',true)));

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Compras");

		$sheet->mergeCells("A1:Q1");
		$sheet->mergeCells("A2:Q2");

		$sheet->getStyle("A1:Q3")->getFont()->setBold(true);
		$sheet->getStyle("A1:Q3")->getAlignment()->setHorizontal("center");

		$sheet->setCellValueByColumnAndRow(1, 1,'Compras del '.FormatoFecha($this->input->post('cinicio',true)).' al '.FormatoFecha($this->input->post('cfin',true)));

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

		foreach(range("A","Q") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."3")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 3,"Establecimiento");
		$sheet->setCellValueByColumnAndRow(2, 3,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 3,"Tipo");
		$sheet->setCellValueByColumnAndRow(4, 3,"Numero");
		$sheet->setCellValueByColumnAndRow(5, 3,"Cliente");
		$sheet->setCellValueByColumnAndRow(6, 3,"Subtotal");
		$sheet->setCellValueByColumnAndRow(7, 3,"IGV");
		$sheet->setCellValueByColumnAndRow(8, 3,"Total");
		$sheet->setCellValueByColumnAndRow(9, 3,"Categoria");
		$sheet->setCellValueByColumnAndRow(10, 3,"Descripcion");
		$sheet->setCellValueByColumnAndRow(11, 3,"Laboratorio");
		$sheet->setCellValueByColumnAndRow(12, 3,"Principio Actico");
		$sheet->setCellValueByColumnAndRow(13, 3,"Lote");
		$sheet->setCellValueByColumnAndRow(14, 3,"Fecha Vcto");
		$sheet->setCellValueByColumnAndRow(15, 3,"Cantidad");
		$sheet->setCellValueByColumnAndRow(16, 3,"Precio Unit.");
		$sheet->setCellValueByColumnAndRow(17, 3,"Precio Total");

		$i=4;
		foreach ($datos as $dato) {
			$nestablecimiento=$this->establecimiento_model->mostrar($dato->idestablecimiento);

			$spreadsheet->setActiveSheetIndex(0)
			->setCellValue('A'.$i,  $nestablecimiento->descripcion)
			->setCellValue('B'.$i,  $dato->femision)
			->setCellValue('C'.$i,  $dato->ncomprobante)
			->setCellValue('D'.$i,  $dato->serie.'-'.$dato->numero)
			->setCellValue('E'.$i,  $dato->proveedor)
			->setCellValue('F'.$i,  $dato->subtotal)
			->setCellValue('G'.$i,  $dato->igv)
			->setCellValue('H'.$i,  $dato->total);

			$detalles=$this->comprad_model->mostrarTotal($dato->id);
			foreach($detalles as $detalle){
				$producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
				$categoria=$this->categoria_model->mostrar($producto->idcategoria);
				$pactivo=$this->pactivo_model->mostrar($producto->idpactivo);

				$spreadsheet->setActiveSheetIndex(0)
				->setCellValue('I'.$i,$categoria->descripcion)
				->setCellValue('J'.$i,$producto->descripcion)
				->setCellValue('K'.$i,$producto->nlaboratorio)
				->setCellValue('L'.$i,($pactivo->descripcion??''))
				->setCellValue('M'.$i,$detalle->lote)
				->setCellValue('N'.$i,$detalle->fvencimiento)
				->setCellValue('O'.$i,$detalle->cantidad)
				->setCellValue('P'.$i,$detalle->precio)
				->setCellValue('Q'.$i,$detalle->importe);
				$i++;
			}
      if (count($detalles)==1) {
				$i++;
      }
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="REPORTE_COMPRAS_CONSOLIDADO.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function excelventas()
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$filtros=array("femision>="=>$this->input->post('vinicio',true),"femision<="=>$this->input->post('vfin',true));
		$ventas=$this->venta_model->mostrarTotal($filtros,'asc');
		$notas=$this->nota_model->mostrarTotal($filtros,'asc');

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle("Ventas");

		$sheet->mergeCells("A1:Q1");
		$sheet->mergeCells("A2:Q2");

		$sheet->getStyle("A1:Q3")->getFont()->setBold(true);
		$sheet->getStyle("A1:Q3")->getAlignment()->setHorizontal("center");

		$sheet->setCellValueByColumnAndRow(1, 1,'Comprobantes Electronicos del '.FormatoFecha($this->input->post('vinicio',true)).' al '.FormatoFecha($this->input->post('vfin',true)));

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

		foreach(range("A","Q") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."3")->applyFromArray($styleArray);
		}

		$sheet->setCellValueByColumnAndRow(1, 3,"Establecimiento");
		$sheet->setCellValueByColumnAndRow(2, 3,"Fecha");
		$sheet->setCellValueByColumnAndRow(3, 3,"Tipo");
		$sheet->setCellValueByColumnAndRow(4, 3,"Numero");
		$sheet->setCellValueByColumnAndRow(5, 3,"Cliente");
		$sheet->setCellValueByColumnAndRow(6, 3,"Subtotal");
		$sheet->setCellValueByColumnAndRow(7, 3,"IGV");
		$sheet->setCellValueByColumnAndRow(8, 3,"Total");
		$sheet->setCellValueByColumnAndRow(9, 3,"Categoria");
		$sheet->setCellValueByColumnAndRow(10, 3,"Descripcion");
		$sheet->setCellValueByColumnAndRow(11, 3,"Laboratorio");
		$sheet->setCellValueByColumnAndRow(12, 3,"Principio Actico");
		$sheet->setCellValueByColumnAndRow(13, 3,"Lote");
		$sheet->setCellValueByColumnAndRow(14, 3,"Fecha Vcto");
		$sheet->setCellValueByColumnAndRow(15, 3,"Cantidad");
		$sheet->setCellValueByColumnAndRow(16, 3,"Precio Unit.");
		$sheet->setCellValueByColumnAndRow(17, 3,"Precio Total");

		$i=4;
		foreach ($ventas as $dato) {
			$nestablecimiento=$this->establecimiento_model->mostrar($dato->idestablecimiento);

			if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
      	if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

					$sheet->setCellValue("A".$i,$nestablecimiento->descripcion);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->ncomprobante);
					$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("E".$i,$nestado);
					$sheet->setCellValue("F".$i,0);
					$sheet->setCellValue("G".$i,0);
					$sheet->setCellValue("H".$i,0);

				$detalles=array();
      } else {
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue('A'.$i,  $nestablecimiento->descripcion)
						->setCellValue('B'.$i,  $dato->femision)
						->setCellValue('C'.$i,  $dato->ncomprobante)
						->setCellValue('D'.$i,  $dato->serie.'-'.$dato->numero)
						->setCellValue('E'.$i,  $dato->cliente)
						->setCellValue('F'.$i,  $dato->subtotal)
						->setCellValue('G'.$i,  $dato->tigv)
						->setCellValue('H'.$i,  $dato->total);

				$detalles=$this->ventad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle){
					$producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
					$categoria=$this->categoria_model->mostrar($producto->idcategoria);
					$pactivo=$this->pactivo_model->mostrar($producto->idpactivo);

		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('I'.$i,$categoria->descripcion)
						->setCellValue('J'.$i,$detalle->descripcion)
						->setCellValue('K'.$i,$producto->nlaboratorio)
						->setCellValue('L'.$i,($pactivo->descripcion??''))
						->setCellValue('M'.$i,$detalle->lote)
						->setCellValue('N'.$i,$detalle->fvencimiento)
						->setCellValue('O'.$i,$detalle->cantidad)
						->setCellValue('P'.$i,$detalle->precio)
						->setCellValue('Q'.$i,$detalle->importe);
					$i++;
		    }
		  }

      if (count($detalles)<1) {
				$i++;
      }
		}

		foreach ($notas as $dato) {
			$nestablecimiento=$this->establecimiento_model->mostrar($dato->idestablecimiento);

			if ($dato->tipo_estado=='09' || $dato->tipo_estado=='11') {
      	if ($dato->tipo_estado=='09') {$nestado='RECHAZADO';} else {$nestado='ANULADO';}

					$sheet->setCellValue("A".$i,$nestablecimiento->descripcion);
					$sheet->setCellValue("B".$i,$dato->femision);
					$sheet->setCellValue("C".$i,$dato->ncomprobante);
					$sheet->setCellValue("D".$i,$dato->serie.'-'.$dato->numero);
					$sheet->setCellValue("E".$i,$nestado);
					$sheet->setCellValue("F".$i,0);
					$sheet->setCellValue("G".$i,0);
					$sheet->setCellValue("H".$i,0);

				$detalles=array();
      } else {
				$spreadsheet->setActiveSheetIndex(0)
						->setCellValue('A'.$i,  $nestablecimiento->descripcion)
						->setCellValue('B'.$i,  $dato->femision)
						->setCellValue('C'.$i,  $dato->ncomprobante)
						->setCellValue('D'.$i,  $dato->serie.'-'.$dato->numero)
						->setCellValue('E'.$i,  $dato->cliente)
						->setCellValue('F'.$i,  '-'.$dato->subtotal)
						->setCellValue('G'.$i,  '-'.$dato->tigv)
						->setCellValue('H'.$i,  '-'.$dato->total);

				$detalles=$this->notad_model->mostrarTotal($dato->id);
				foreach($detalles as $detalle)
		    {
		    	$producto=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));
					$categoria=$this->categoria_model->mostrar($producto->idcategoria);
					$pactivo=$this->pactivo_model->mostrar($producto->idpactivo);

		      $spreadsheet->setActiveSheetIndex(0)
						->setCellValue('I'.$i,$categoria->pactivo)
						->setCellValue('J'.$i,$detalle->descripcion)
						->setCellValue('K'.$i,$producto->nlaboratorio)
						->setCellValue('L'.$i,($pactivo->descripcion??''))
						->setCellValue('M'.$i,$detalle->lote)
						->setCellValue('N'.$i,$detalle->fvencimiento)
						->setCellValue('O'.$i,$detalle->cantidad)
						->setCellValue('P'.$i,$detalle->precio)
						->setCellValue('Q'.$i,$detalle->importe);
					$i++;
		    }
		  }

      if (count($detalles)<1) {
				$i++;
      }
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="REPORTE_VENTAS_CONSOLIDADO.xlsx"');
    $writer->save('php://output');	// download file
	}

  public function pdfvendidos()
  {
    $empresa=$this->empresa_model->mostrar();
  	$establecimientos=$this->establecimiento_model->mostrarTotal(array("estado"=>1));
  	$inicio=$this->input->post('pinicio',true);
  	$fin=$this->input->post('pfin',true);
		$listas=$this->producto_model->mostrarTotal(array("estado"=>1,"tipo"=>"B"));
  	$this->layout->setLayout("blanco");
  	$this->layout->view("pdfvendidos",compact("empresa","establecimientos","listas","inicio","fin"));
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

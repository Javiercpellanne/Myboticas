<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Producto extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
		$this->layout->setLayout("contraido");
		$this->load->model("tafectacion_model");
		$this->load->model("categoria_model");
		$this->load->model("laboratorio_model");
		$this->load->model("pactivo_model");
		$this->load->model("aterapeutica_model");
		$this->load->model("ubicacion_model");
		$this->load->model("egenerico_model");
		$this->load->model("kardex_model");
		$this->load->model("kardexl_model");
		$this->load->model("lote_model");
    $this->load->model("bonificado_model");
		$this->load->model("compra_model");
		$this->load->model("comprad_model");
		$this->load->model("nventa_model");
		$this->load->model("nventad_model");
		$this->load->model("venta_model");
		$this->load->model("ventad_model");
		$this->load->model("punto_model");
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$listas=$this->producto_model->mostrarLimite(array("tipo"=>'B',"estado"=>1));
		$detalles=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>1));
		$canexos=$this->establecimiento_model->contador();
		$this->layout->setTitle("Producto Activo");
		$this->layout->view("index",compact("anexos","nestablecimiento","empresa","listas","detalles","canexos"));
	}

	public function inactivos()
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto/inactivos');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$listas=$this->producto_model->mostrarLimite(array("tipo"=>'B',"estado"=>0));
		$detalles=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>0));
		$canexos=$this->establecimiento_model->contador();
		$this->layout->setTitle("Producto Inactivo");
		$this->layout->view("inactivos",compact("anexos","nestablecimiento","empresa","listas","detalles","canexos"));
	}

	public function deficit()
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto/deficit');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$listas=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>1,"lote"=>1));
		$this->layout->setTitle("Producto Activo");
		$this->layout->view("deficit",compact("anexos","nestablecimiento",'empresa',"listas"));
	}

	public function catalogo()
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto/catalogo');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$activos=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>1),"factivo");
		$inactivos=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>0),"finactivo");
		$retirados=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>2),"fretiro");

		$contador1=$this->producto_model->contador(array("tipo"=>'B',"estado"=>1));
		$contador2=$this->producto_model->contador(array("tipo"=>'B',"estado"=>0));
		$contador3=$this->producto_model->contador(array("tipo"=>'B',"estado"=>2));
		$this->layout->setTitle("Catalogo Producto");
		$this->layout->view("catalogo",compact("anexos","nestablecimiento",'empresa','activos',"inactivos",'retirados','contador1','contador2','contador3'));
	}

	public function gestores()
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto/gestores');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$this->layout->setTitle("Gestor Precios");
		$this->layout->view("gestores",compact("anexos","nestablecimiento",'empresa'));
	}

	public function busConsultas()
	{
		if ($this->input->post())
		{
			$nombre=$this->input->post('id',true);
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'http://consultas.comerciosoft.com/api/productos',
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => 'descripcion='.$nombre,
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/x-www-form-urlencoded',
			    'Cookie: ci_session=1i0qlt5tsq62qr7rekt5i02nl5k4nmol'
			  ),
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

	public function productoi($id=null)
	{
    if (!$this->acciones(19)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('producto/productoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		if ($this->input->post())
		{
			if ($id!=null) {
				$data=array
				(
					'idcategoria'		=>$this->input->post('categoria',true),
					'descripcion'		=>trim(mb_strtoupper(reemplazarComillas($this->input->post('descripcion',true)), 'UTF-8')),
					'idlaboratorio'	=>$this->input->post('laboratorio',true),
					'clasificacion'	=>$this->input->post('clasificacion',true),
					'idpactivo'			=>$this->input->post('pactivo',true),
					'idaterapeutica'=>$this->input->post('aterapeutica',true),
					'cdigemid'			=>$this->input->post('cdigemid',true),
					'umedidav'			=>$this->input->post('umedidav',true),
					'codbarra'			=>$this->input->post('codbarra',true),
					'rsanitario'		=>$this->input->post('rsanitario',true),
					'mstock'				=>$this->input->post('mstock',true),
					'tafectacion'		=>$this->input->post('tafectacion',true),
					'pcompra'				=>$this->input->post('pcompra',true),
					'factor'				=>$this->input->post('factor',true),
					'compra'				=>$this->input->post('compra',true),
					'umedidac'			=>$this->input->post('factor',true)>1 ? $this->input->post('umedidac',true) : NULL,
					'umedidab'			=>$this->input->post('factorb',true)>1 ? $this->input->post('umedidab',true) : NULL,
					'factorb'				=>$this->input->post('factorb',true)>1 ? $this->input->post('factorb',true) : NULL,
					'vsujeta'				=>$this->input->post('vsujeta',true),
					'informacion'		=>$this->input->post('informacion',true),
					'idubicacion'		=>$this->input->post('ubicacion',true),
				);
				if ($this->input->post('clasificacion',true)!=0 && $this->input->post('pactivo',true)>0) {
					$data['idegenerico']=valor_fecha($this->input->post('egenerico',true));
				}
				if ($this->input->post('stock',true)==0)
				{
					$data['lote']=valor_check($this->input->post('lote',true));
				}
				if ($empresa->pestablecimiento==0) {
					$data['pventa']=$this->input->post('pventa',true);
					$data['venta']=$this->input->post('factor',true)>1 ? $this->input->post('venta',true) : 0;
					$data['pblister']=$this->input->post('factorb',true)>1 ? $this->input->post('pblister',true) : 0;
				}
				$guardar=$this->producto_model->update($data,$id);

				if ($empresa->pestablecimiento==1) {
					$datae=array
					(
						'venta'			=>$this->input->post('venta',true),
						'pventa'		=>$this->input->post('factor',true)>1 ? $this->input->post('pventa',true) : 0,
						'pblister'	=>$this->input->post('factorb',true)>1 ? $this->input->post('pblister',true) : 0,
					);
					$actualizarp=$this->inventario_model->update($datae,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id));
				}

				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        $control_movimiento=$this->movimientos('producto/productoi','Edito el producto '.$this->input->post('descripcion',true).' con codigo '.$id);
			} else {
				$consulta=$this->producto_model->contador(array("descripcion"=>reemplazarComillas($this->input->post('descripcion',true)),"idlaboratorio"=>$this->input->post('laboratorio',true)));
				if ($consulta==0 && $this->input->post('descripcion',true)!='') {
					$data=array
					(
						'tipo'					=>'B',
						'idcategoria'		=>$this->input->post('categoria',true),
						'descripcion'		=>trim(mb_strtoupper(reemplazarComillas($this->input->post('descripcion',true)), 'UTF-8')),
						'idlaboratorio'	=>$this->input->post('laboratorio',true),
						'clasificacion'	=>$this->input->post('clasificacion',true),
						'idpactivo'			=>$this->input->post('pactivo',true),
						'idaterapeutica'=>$this->input->post('aterapeutica',true),
						'cdigemid'			=>$this->input->post('cdigemid',true),
						'umedidav'			=>$this->input->post('umedidav',true),
						'codbarra'			=>$this->input->post('codbarra',true),
						'rsanitario'		=>$this->input->post('rsanitario',true),
						'mstock'				=>$this->input->post('mstock',true),
						'tafectacion'		=>$this->input->post('tafectacion',true),
						'pcompra'				=>$this->input->post('pcompra',true),
						'factor'				=>$this->input->post('factor',true),
						'compra'				=>$this->input->post('compra',true),
						'umedidac'			=>$this->input->post('factor',true)>1 ? $this->input->post('umedidac',true) : NULL,
						'umedidab'			=>$this->input->post('factorb',true)>1 ? $this->input->post('umedidab',true) : NULL,
						'factorb'				=>$this->input->post('factorb',true)>1 ? $this->input->post('factorb',true) : NULL,
						'vsujeta'				=>$this->input->post('vsujeta',true),
						'estado'				=>1,
						'lote'					=>valor_check($this->input->post('lote',true)),
						'informacion'		=>$this->input->post('informacion',true),
						'idubicacion'		=>$this->input->post('ubicacion',true),
					);
					if ($this->input->post('clasificacion',true)!=0 && $this->input->post('pactivo',true)>0) {
						$data['idegenerico']=valor_fecha($this->input->post('egenerico',true));
					}
					if ($empresa->pestablecimiento==0) {
						$data['pventa']=$this->input->post('pventa',true);
						$data['venta']=$this->input->post('factor',true)>1 ? $this->input->post('venta',true) : 0;
						$data['pblister']=$this->input->post('factorb',true)>1 ? $this->input->post('pblister',true) : 0;
					}
					$insertar=$this->producto_model->insert($data);

					$investablecimientos=$this->establecimiento_model->mostrarTotal();
					foreach ($investablecimientos as $investablecimiento) {
						$datae=array
						(
							'idestablecimiento'	=>$investablecimiento->id,
							'idproducto'				=>$insertar,
							'stock'							=>0,
						);
						if ($empresa->pestablecimiento==1) {
							$datae['pventa']=$this->input->post('pventa',true);
							$datae['venta']=$this->input->post('factor',true)>1 ? $this->input->post('venta',true) : 0;
							$datae['pblister']=$this->input->post('factorb',true)>1 ? $this->input->post('pblister',true) : 0;
						}
						$insertark=$this->inventario_model->insert($datae);
					}

					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        	$control_movimiento=$this->movimientos('producto/productoi','Registro el producto '.$this->input->post('descripcion',true).' con codigo '.$insertar);
				} else {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', 'El producto ya EXISTE!!!!!!');
				}
			}

			if (isset($_FILES['foto']) && $_FILES['foto']['tmp_name']!='') {
        $nombreCompleto= $id==null ? 'P_'.$insertar.'.jpg': 'P_'.$id.'.jpg';
        $nro=$id==null ? $insertar: $id;

        $config['upload_path']   = './downloads/productos/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = 'jpg|jpeg';
        $config['max_size']      = 1000;
        $config['max_width']     = 0;
        $config['max_height']    = 0;
        $config['file_name']     = $nombreCompleto;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('foto')) {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', $this->upload->display_errors());
        } else {
          $ruta= addslashes(base_url()."downloads/productos/".$nombreCompleto);
          $data=array('ruta'=>$ruta);
          $guardar=$this->producto_model->update($data,$nro);
          $imagen=$this->upload->data();
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Se subio con exito la portada '.$imagen["file_name"]);
        }
      }

			if ($this->input->post('codbarra',true)!='') {
				barcode("./downloads/codigos/".$this->input->post('codbarra',true).'.png', $this->input->post('codbarra',true));
			}

			redirect(base_url()."producto");
		}

		$datos=$id!=null?$this->producto_model->mostrar(array("p.id"=>$id)):(object) array("idcategoria"=>'',"descripcion"=>'',"idlaboratorio"=>'',"idpactivo"=>'',"idaterapeutica"=>'',"clasificacion"=>0,"codbarra"=>'',"cdigemid"=>'',"mstock"=>0,"tafectacion"=>10,"compra"=>'', "venta"=>'',"factor"=>1,"pcompra"=>'', "pventa"=>'',"factorb"=>'', "pblister"=>'',"rsanitario"=>'',"vsujeta"=>'', "lote"=>'',"informacion"=>'',"idubicacion"=>'','idegenerico'=>'');
		$cantidades=$id!=null?$this->inventario_model->cantidadTotal(array("idproducto"=>$id)):(object) array("stock"=>0);
		$cantidad=$id!=null?$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$id):(object) array("venta"=>'', "pventa"=>'', "pblister"=>'');
		$egenericos=$this->egenerico_model->mostrarTotal($datos->idpactivo);

		$categorias=$this->categoria_model->mostrarTotal('F');
		$laboratorios=$this->laboratorio_model->mostrarTotal();
		$tafectaciones=$this->tafectacion_model->mostrarTotal();
		$ubicaciones=$this->ubicacion_model->mostrarTotal();
		$pactivos=$this->pactivo_model->mostrarTotal();
		$aterapeuticas=$this->aterapeutica_model->mostrarTotal();
		$this->layout->setTitle("Producto");
		$this->layout->view("productoi",compact("anexos","nestablecimiento","empresa","datos","cantidades","laboratorios","categorias","pactivos","aterapeuticas","ubicaciones","id","tafectaciones","cantidad",'egenericos'));
	}

	public function precios()
	{
    $controlip=$this->controlip('producto/precios');
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			$file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			if(isset($_FILES['archivo']['name']) && in_array($_FILES['archivo']['type'], $file_mimes)) {
				$arr_file = explode('.', $_FILES['archivo']['name']);
				$extension = end($arr_file);
				if('csv' == $extension){
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
				} else {
					$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
				}
				$spreadsheet = $reader->load($_FILES['archivo']['tmp_name']);
				// $sheetData = $spreadsheet->getActiveSheet()->toArray();
				// echo "<pre>";
				// print_r($sheetData);

				$sheet_data = $spreadsheet->getActiveSheet()->toArray();
				//$lista 			= [];
				foreach($sheet_data as $key => $val) {
					if($key != 0) {
						$data=array(
							'codbarra'=>$val[2],
							'factor'=>$val[4],
							'factorb'=>$val[6],
						);
						if ($empresa->pestablecimiento==0) {
							$data['pventa']=$val[3];
							$data['venta']=$val[5];
							$data['pblister']=$val[7];
						}
						$guardar=$this->producto_model->update($data,$val[0]);

						if ($empresa->pestablecimiento==1) {
							$datas=array
							(
								'pventa'		=>$val[3],
								'venta'			=>$val[5],
								'pblister'	=>$val[7],
							);
							$actualizarp=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$val[0]));
						}
					}
				}
        $control_movimiento=$this->movimientos('producto/precios','Edito precios de producto por excel');

				$this->session->set_flashdata('css', 'success');
				$this->session->set_flashdata('mensaje', 'Los precios se han actualizado exitosamente!');
			}else {
				$this->session->set_flashdata('css', 'danger');
				$this->session->set_flashdata('mensaje', 'No existe archivo o no corresponde el tipo!');
			}

			echo base_url()."producto";
			exit();
		}

		$this->layout->setLayout("blanco");
		$this->layout->view("precios");
	}

	public function preciosexcel()
	{
		$empresa=$this->empresa_model->mostrar();
    $listas=$this->producto_model->mostrarTotal(array("estado"=>1,"factor>"=>0));

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

		foreach(range("A","H") as $columnID) {
		    $sheet->getColumnDimension($columnID)->setAutoSize(true);
		    $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
		}

		foreach(range("C","H") as $columnID) {
				$sheet->getStyle($columnID."1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
		}

		$sheet->setCellValueByColumnAndRow(1, 1,"Id");
		$sheet->setCellValueByColumnAndRow(2, 1,"Nombre");
		$sheet->setCellValueByColumnAndRow(3, 1,"Codigo Barra");
		$sheet->setCellValueByColumnAndRow(4, 1,"Precio Unidad");
		$sheet->setCellValueByColumnAndRow(5, 1,"Factor Caja");
		$sheet->setCellValueByColumnAndRow(6, 1,"Precio Caja");
		$sheet->setCellValueByColumnAndRow(7, 1,"Factor Blister");
		$sheet->setCellValueByColumnAndRow(8, 1,"Precio Blister");

		$i=2;
		foreach ($listas as $lista) {
			$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$lista->id);
			$sheet->getStyle("A".$i)->applyFromArray($styleArray);
			$sheet->getStyle("B".$i)->applyFromArray($styleArray);
			$sheet->getStyle("C".$i)->applyFromArray($styleArray);
			$sheet->getStyle("D".$i)->applyFromArray($styleArray);
			$sheet->getStyle("E".$i)->applyFromArray($styleArray);
			$sheet->getStyle("F".$i)->applyFromArray($styleArray);
			$sheet->getStyle("G".$i)->applyFromArray($styleArray);
			$sheet->getStyle("H".$i)->applyFromArray($styleArray);

			$sheet->setCellValue("A".$i,$lista->id);
			$sheet->setCellValue("B".$i,$lista->descripcion.' '.$lista->nlaboratorio);
			$sheet->setCellValue("C".$i,$lista->codbarra);
			$sheet->setCellValue("D".$i,$empresa->pestablecimiento==1 ? $cantidad->pventa: $lista->pventa);
			$sheet->setCellValue("E".$i,$lista->factor);
			$sheet->setCellValue("F".$i,$empresa->pestablecimiento==1 ? $cantidad->venta: $lista->venta);
			$sheet->setCellValue("G".$i,$lista->factorb);
			$sheet->setCellValue("H".$i,$empresa->pestablecimiento==1 ? $cantidad->pblister: $lista->pblister);

			$i++;
		}

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_PRECIOS.xlsx"');
    $writer->save('php://output');	// download file
	}

	public function establecimiento($id)
	{
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$listas=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
		$this->layout->setLayout("blanco");
		$this->layout->view("establecimiento",compact("empresa",'datos',"listas","id"));
	}

	public function inventario($id)
	{
    $controlip=$this->controlip('producto/inventario');
		if ($this->input->post())
		{
			$datak=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        'iduser'            =>$this->session->userdata('id'),
        'fecha'             =>date("Y-m-d"),
        'idtmovimiento'     =>16,
        'concepto'          =>'Stock Actualizado',
        'idproducto'        =>$id,
        'descripcion'       =>trim($this->input->post('descripcion',true)),
        'entradaf'          =>$this->input->post('cantidad',true),
        'saldof'            =>$this->input->post('cantidad',true),
        'costo'             =>$this->input->post('precio',true),
        'entradav'          =>$this->input->post('cantidad',true)*$this->input->post('precio',true),
        'saldov'            =>$this->input->post('cantidad',true)*$this->input->post('precio',true),
      );
      $insertark=$this->kardex_model->insert($datak);

      //actualizar stock
      $datas=array('stock'=>$this->input->post('cantidad',true));
      $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id));

      if ($this->input->post('lote',true)!="") {
      	$detalles=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
      	foreach ($detalles as $detalle) {
	        $datac=array
	        (
	          'idestablecimiento' =>$this->session->userdata('predeterminado'),
	          'iduser'            =>$this->session->userdata('id'),
	          'fecha'             =>date('Y-m-d'),
	          'idtmovimiento'     =>16,
	          'concepto'          =>'Stock Actualizado',
	          'idproducto'        =>$id,
	          'descripcion'       =>trim($this->input->post('descripcion',true)),
	          'nlote'             =>$detalle->nlote,
	          'entradaf'          =>0,
	          'saldof'            =>0,
	        );
	        $insertarc=$this->kardexl_model->insert($datac);
      	}
      	$eliminar=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id));

      	if ($this->input->post('cantidad',true)>0) {
	        $datal=array
	        (
	          'idestablecimiento' =>$this->session->userdata("predeterminado"),
	          'idproducto'        =>$id,
	          'nlote'             =>$this->input->post('lote',true),
	          'fvencimiento'      =>valor_fecha($this->input->post('fvencimiento',true)),
	          'inicial'           =>$this->input->post('cantidad',true),
	          'stock'             =>$this->input->post('cantidad',true),
	        );
	        $insertarl=$this->lote_model->insert($datal);

	        $datac=array
	        (
	          'idestablecimiento' =>$this->session->userdata('predeterminado'),
	          'iduser'            =>$this->session->userdata('id'),
	          'fecha'             =>date('Y-m-d'),
	          'idtmovimiento'     =>16,
	          'concepto'          =>'Stock Actualizado',
	          'idproducto'        =>$id,
	          'descripcion'       =>trim($this->input->post('descripcion',true)),
	          'nlote'             =>$this->input->post('lote',true),
	          'entradaf'          =>$this->input->post('cantidad',true),
	          'saldof'            =>$this->input->post('cantidad',true),
	        );
	        $insertarc=$this->kardexl_model->insert($datac);
      	}
      }
      $control_movimiento=$this->movimientos('producto/inventario','Se modifico el stock producto '.$this->input->post('descripcion',true).' desde 0');

      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', 'El inventario se actualizo exitosamentei!');

			echo base_url()."producto";
			exit();
		}

		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("inventario",compact('datos'));
	}

	public function lotes($id)
	{
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$listas=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
		$this->layout->setLayout("blanco");
		$this->layout->view("lotes",compact('datos',"listas"));
	}

	public function buscompras($id)
	{
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$compras=$this->compra_model->ultimas($this->session->userdata("predeterminado"),$id);
		$ventas=$this->venta_model->ultimas($this->session->userdata("predeterminado"),$id);
		$nventas=$this->nventa_model->ultimas($this->session->userdata("predeterminado"),$id);
		$this->layout->setLayout("blanco");
		$this->layout->view("bcompras",compact('datos',"compras","id",'ventas','nventas'));
	}

	public function deshabilitar($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('producto/deshabilitar');
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		if ($datos==NULL) {show_404();}

		// $contador=$this->inventario_model->cantidadTotal(array("idproducto"=>$id));
		// if ($contador->stock>0) {
		// 	$this->session->set_flashdata('css', 'danger');
		// 	$this->session->set_flashdata('mensaje', 'El producto tiene stock en alguno establecimiento!');
		// } else {
			$data=array('estado'=>0);
			$guardar=$this->producto_model->update($data,$id);
			$this->session->set_flashdata('css', 'success');
			$this->session->set_flashdata('mensaje', 'El producto fue deshabilitado!');
      $control_movimiento=$this->movimientos('producto/deshabilitar','Se deshabilito producto '.$datos->descripcion.' '.$datos->nlaboratorio);
		// }
		redirect(base_url()."producto");
	}

	public function habilitar($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('producto/habilitar');
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		if ($datos==NULL) {show_404();}

		$data=array('estado'=>1);
		$guardar=$this->producto_model->update($data,$id);
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'El producto fue habilitado!');
    $control_movimiento=$this->movimientos('producto/habilitar','Se habilito producto '.$datos->descripcion.' '.$datos->nlaboratorio);
		redirect(base_url()."producto");
	}

	public function busListado() //listado general de productos
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if (strlen($this->input->post('id',true))>2) {
				$productos=$this->producto_model->buscador($this->input->post('id',true),array("tipo"=>'B',"estado"=>$this->input->post('estado',true)));
			}else {
				$productos=$this->producto_model->mostrarLimite(array("tipo"=>'B',"estado"=>$this->input->post('estado',true)));
			}

			$datos=array();
			foreach ($productos as $producto) {
				$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
				$canexos=$this->establecimiento_model->contador();
				$detalle['id']=$producto->id;
				$detalle['descripcion']=$producto->descripcion;
				$detalle['lote']=$producto->lote;
				$detalle['compra']=$producto->compra;
				$detalle['venta']=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;
				$detalle['factor']=$producto->factor;
				$detalle['pcompra']=$producto->pcompra;
				$detalle['pventa']=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
				$detalle['factorb']=$producto->factorb;
				$detalle['pblister']=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
				$detalle['nlaboratorio']=$producto->nlaboratorio;
				$detalle['stock']=$cantidad->stock;
				$detalle['mstock']=$producto->mstock;
				$detalle['rsanitario']=$producto->rsanitario;
				$detalle['tipo']=$producto->tipo;
				$detalle['estado']=$producto->estado;
				$detalle['canexos']=$canexos;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function inactivar()
	{
    $controlip=$this->controlip('producto/inactivar');
    // $contador=$this->inventario_model->cantidadTotal(array("idproducto"=>$this->input->post('id',true)));
		// if ($contador->stock>0) {
		// 	$mensaje='El producto tiene stock en alguno establecimiento!';
		// 	$success=false;
		// } else {
			$data=array('estado'=>0,'finactivo'=>date('Y-m-d H:i:s'));
			$guardar=$this->producto_model->update($data,$this->input->post('id',true));
    	$control_movimiento=$this->movimientos('producto/inactivar','Se inactivo producto codigo '.$this->input->post('id',true));

			$mensaje='El producto fue inactivado!';
			$success=true;
			$contador1=$this->producto_model->contador(array("tipo"=>'B',"estado"=>1));
			$contador2=$this->producto_model->contador(array("tipo"=>'B',"estado"=>0));
			$contador3=$this->producto_model->contador(array("tipo"=>'B',"estado"=>2));
		// }

		$datos['mensaje']=$mensaje;
		$datos['success']=$success;
		$datos['contador1']=$contador1;
		$datos['contador2']=$contador2;
		$datos['contador3']=$contador3;
		echo json_encode($datos);
	}

	public function activar()
	{
    $controlip=$this->controlip('producto/activar');
		$data=array('estado'=>1,'factivo'=>date('Y-m-d H:i:s'));
		$guardar=$this->producto_model->update($data,$this->input->post('id',true));
    	$control_movimiento=$this->movimientos('producto/activar','Se activo producto codigo '.$this->input->post('id',true));

		$mensaje='El producto fue activado!';
		$success=true;
		$contador1=$this->producto_model->contador(array("tipo"=>'B',"estado"=>1));
		$contador2=$this->producto_model->contador(array("tipo"=>'B',"estado"=>0));
		$contador3=$this->producto_model->contador(array("tipo"=>'B',"estado"=>2));

		$datos['mensaje']=$mensaje;
		$datos['success']=$success;
		$datos['contador1']=$contador1;
		$datos['contador2']=$contador2;
		$datos['contador3']=$contador3;
		echo json_encode($datos);
	}

	public function retirar()
	{
    $controlip=$this->controlip('producto/retirar');
    $contador=$this->inventario_model->cantidadTotal(array("idproducto"=>$this->input->post('id',true)));
		if ($contador->stock>0) {
			$mensaje='El producto tiene stock en alguno establecimiento!';
			$success=false;
		} else {
			$data=array('estado'=>2,'fretiro'=>date('Y-m-d H:i:s'));
			$guardar=$this->producto_model->update($data,$this->input->post('id',true));
    	$control_movimiento=$this->movimientos('producto/retirar','Se retiro producto codigo '.$this->input->post('id',true));

			$mensaje='El producto fue retirado!';
			$success=true;
			$contador1=$this->producto_model->contador(array("tipo"=>'B',"estado"=>1));
			$contador2=$this->producto_model->contador(array("tipo"=>'B',"estado"=>0));
			$contador3=$this->producto_model->contador(array("tipo"=>'B',"estado"=>2));
		}

		$datos['mensaje']=$mensaje;
		$datos['success']=$success;
		$datos['contador1']=$contador1;
		$datos['contador2']=$contador2;
		$datos['contador3']=$contador3;
		echo json_encode($datos);
	}

	public function busActivos()
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if (strlen($this->input->post('id',true))>2) {
				$productos=$this->producto_model->buscador($this->input->post('id',true),array("tipo"=>'B',"estado"=>1));
			}else {
				$productos=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>1),"factivo");
			}

			$datos=array();
			foreach ($productos as $producto) {
				$nproducto=$producto->descripcion;
        if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
				$detalle['id']=$producto->id;
				$detalle['descripcion']=$nproducto;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function busInactivos()
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if (strlen($this->input->post('id',true))>2) {
				$productos=$this->producto_model->buscador($this->input->post('id',true),array("tipo"=>'B',"estado"=>0));
			}else {
				$productos=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>0),"finactivo");
			}

			$datos=array();
			foreach ($productos as $producto) {
				$nproducto=$producto->descripcion;
        if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
				$detalle['id']=$producto->id;
				$detalle['descripcion']=$nproducto;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function busRetirados()
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if (strlen($this->input->post('id',true))>2) {
				$productos=$this->producto_model->buscador($this->input->post('id',true),array("tipo"=>'B',"estado"=>2));
			}else {
				$productos=$this->producto_model->mostrarEstado(array("tipo"=>'B',"estado"=>2),"fretiro");
			}

			$datos=array();
			foreach ($productos as $producto) {
				$nproducto=$producto->descripcion;
        if ($producto->nlaboratorio!='') {$nproducto.=' ['.$producto->nlaboratorio.']';}
				$detalle['id']=$producto->id;
				$detalle['descripcion']=$nproducto;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

/*===========================================================================================================================
=                                                         buscadores                                                        =
===========================================================================================================================*/
	public function busCodigo()
	{
		if ($this->input->post())
		{
			$codigo=$this->producto_model->mostrar(array("codbarra"=>$this->input->post('id',true)));
			if ($codigo==NULL) {
				$datos['success'] = true;
				$datos['data'] = "El codigo de barras esta disponible";
			} else {
				$datos['success'] = false;
				$datos['data'] = "El codigo de barras ya se asigno";
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function generarCodigo()
	{
		$numero=$this->producto_model->codigo();
		$codigo=($numero->codbarra ?? 100000)+1;

		$contador=$this->producto_model->contador(array("codbarra"=>$codigo));
		if ($contador>0) {
			$codigo='';
		}
		echo $codigo;
	}

  public function busCategoria() //buscador de productos por categoria
  {
    if ($this->input->post())
    {
			$empresa=$this->empresa_model->mostrar();
      if ($this->input->post('id',true)>0) {
        $productos=$this->producto_model->mostrarTotal(array('idcategoria'=>$this->input->post('id',true),'estado'=>1,"factor>"=>0));
      } else {
        $productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
      }

      $datos=array();
      foreach ($productos as $producto) {
				$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);

        $detalle['id']=$producto->id;
        $detalle['tipo']=$producto->tipo;
        $detalle['descripcion']=$producto->descripcion;
				$detalle['nlaboratorio']=$producto->nlaboratorio;
				$detalle['umedidav']=$producto->umedidav;
        $detalle['tafectacion']=$producto->tafectacion;
				$detalle['lote']=$producto->lote;
				$detalle['factor']=$producto->factor;
				$detalle['pventa']=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
				$detalle['stock']=$cantidad->stock;
        $detalle['ruta']=$producto->ruta!=NULL ? $producto->ruta:base_url().'downloads/productos/default.jpg';
				$detalle['edicion']=$empresa->pventa;
				$detalle['imagen']=base_url().'downloads/productos/sinstock.png';
        array_push($datos,$detalle);
      }
      echo json_encode($datos);
    }
  }

	public function busProductos() //buscador de productos por nombres
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if (strlen($this->input->post('id',true))>2) {
				$productos=$this->producto_model->buscador($this->input->post('id',true),array("estado"=>1,"factor>"=>0));
			} else {
				$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
			}

			$datos=array();
			foreach ($productos as $producto) {
				$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
				$bonificacion=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id));
				$valor=$bonificacion==NULL ? 0 : $bonificacion->monto;

				$detalle['id']=$producto->id;
				$detalle['tipo']=$producto->tipo;
				$detalle['descripcion']=$producto->descripcion;
				$detalle['nlaboratorio']=$producto->nlaboratorio;
				$detalle['umedidav']=$producto->umedidav;
				$detalle['umedidac']=$producto->umedidac;
				$detalle['umedidab']=$producto->umedidab;
				$detalle['tafectacion']=$producto->tafectacion;
				$detalle['lote']=$producto->lote;
				$detalle['factor']=$producto->factor;
				$detalle['factorb']=$producto->factorb;
				$detalle['compra']=$producto->compra;
				$detalle['pcompra']=$producto->pcompra;
				$detalle['venta']=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;
				$detalle['pventa']=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
				$detalle['pblister']=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
				$detalle['stock']=$cantidad->stock;
				$detalle['bonificacion']=$valor;
        $detalle['ruta']=$producto->ruta!=NULL ? $producto->ruta:base_url().'downloads/productos/default.jpg';
				$detalle['edicion']=$empresa->pventa;
				$detalle['vsujeta']=$producto->vsujeta;
				$detalle['imagen']=base_url().'downloads/productos/sinstock.png';

				$detalle['descuento']=$empresa->dscto;
				$detalle['vbonificar']=$empresa->vbonificar;
				$detalle['lstock']=$empresa->lstock;
				array_push($datos,$detalle);
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function busCodigobarra()
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			$producto=$this->producto_model->mostrar(array("codbarra"=>$this->input->post('id',true),"estado"=>1,"factor>"=>0));
      if ($producto!=null) {
				$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$producto->id);
				$bonificacion=$this->bonificado_model->mostrar(array("anuo"=>date("Y"),"mes"=>date("n"),"idproducto"=>$producto->id));
				$valor=$bonificacion==NULL ? 0 : $bonificacion->monto;

        $datos['id']=$producto->id;
				$datos['tipo']=$producto->tipo;
				$datos['descripcion']=$producto->descripcion;
				$datos['nlaboratorio']=$producto->nlaboratorio;
				$datos['umedidav']=$producto->umedidav;
				$datos['umedidac']=$producto->umedidac;
				$datos['umedidab']=$producto->umedidab;
				$datos['tafectacion']=$producto->tafectacion;
				$datos['lote']=$producto->lote;
				$datos['factor']=$producto->factor;
				$datos['factorb']=$producto->factorb;
				$datos['compra']=$producto->compra;
				$datos['pcompra']=$producto->pcompra;
				$datos['venta']=$empresa->pestablecimiento==1 ? $cantidad->venta: $producto->venta;
				$datos['pventa']=$empresa->pestablecimiento==1 ? $cantidad->pventa: $producto->pventa;
				$datos['pblister']=$empresa->pestablecimiento==1 ? $cantidad->pblister: $producto->pblister;
				$datos['stock']=$cantidad->stock;
				$datos['bonificacion']=$valor;
				$datos['vsujeta']=$producto->vsujeta;
				$datos['edicion']=$empresa->pventa;

				$datos['descuento']=$empresa->dscto;
				$datos['vbonificar']=$empresa->vbonificar;
				$datos['lstock']=$empresa->lstock;
      }else{
      	$datos=null;
      }
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function busPrecios($id=null)
	{
		$empresa=$this->empresa_model->mostrar();
		if ($this->input->post())
		{
			if ($empresa->pestablecimiento==1) {
				$datos=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$this->input->post('id',true));
			} else {
				$datos=$this->producto_model->mostrar(array("p.id"=>$this->input->post('id',true)));
			}
			echo json_encode($datos);
		}
		else
		{
			$datos=$this->producto_model->mostrar(array("p.id"=>$id));
			$cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$id);
			$this->layout->setLayout("blanco");
			$this->layout->view("bprecios",compact('empresa','datos','cantidad'));
		}
	}

	public function busInformacion($id,$opcion=null)
	{
    $empresa=$this->empresa_model->mostrar();
    $canexos=$this->establecimiento_model->contador();
    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    $principios=$this->producto_model->mostrarTotal(array("idpactivo"=>$datos->idpactivo,"estado"=>1,"factor>"=>0));
    $terapeuticos=$this->producto_model->mostrarTotal(array("idaterapeutica"=>$datos->idaterapeutica,"estado"=>1,"factor>"=>0));

		$pactivo=$this->pactivo_model->mostrar($datos->idpactivo);
		$aterapeutica=$this->aterapeutica_model->mostrar($datos->idaterapeutica);
		$ubicacion=$this->ubicacion_model->mostrar($datos->idubicacion);
		$categoria=$this->categoria_model->mostrar($datos->idcategoria);
		$tafectacion=$this->tafectacion_model->mostrar($datos->tafectacion);

		$establecimientos=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
		$this->layout->setLayout("blanco");
		$this->layout->view("binformacion",compact('empresa','canexos','datos','principios','terapeuticos',"pactivo","aterapeutica","ubicacion","categoria","tafectacion",'establecimientos','opcion','id'));
	}

	public function busProductoLotes($id)
	{
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$lotes=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
		$cantidades=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$id);
		$this->layout->setLayout("blanco");
		$this->layout->view("bproductolotes",compact('empresa','datos','lotes','cantidades'));
	}

	public function busLotes($id=null,$nro=null)
	{
		if ($this->input->post())
		{
			$datos=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$this->input->post('id',true));
			echo json_encode($datos);
		}
		else
		{
			$lotes=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
			$this->layout->setLayout("blanco");
			$this->layout->view("blotes",compact('lotes','nro'));
		}
	}

	public function consulta($id)
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("consulta",compact("id"));
	}

	public function pdfcodigobarra($id,$nro=1)
	{
		$datos=$this->producto_model->mostrar(array("p.id"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfcodigobarra",compact("datos","nro"));
	}

	public function busGenericos()
	{
		if ($this->input->post())
		{
			$datos=array();
			if ($this->input->post('clasificacion',true)!=0 && $this->input->post('pactivo',true)>0) {
				$datos=$this->egenerico_model->mostrarTotal($this->input->post('pactivo',true));
			}
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

/*===========================================================================================================================
=                                                         acciones                                                        =
===========================================================================================================================*/
  public function resetear()
  {
    $controlip=$this->controlip('producto/resetear');
    $listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0));
    foreach ($listas as $lista)
    {
      $datak=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        'iduser'            =>$this->session->userdata('id'),
        'fecha'             =>date("Y-m-d"),
        'idtmovimiento'     =>16,
        'concepto'          =>'Stock Reseteado',
        'idproducto'        =>$lista->id,
        'descripcion'       =>$lista->descripcion,
        'entradaf'          =>0,
        'saldof'            =>0,
        'costo'             =>$lista->pcompra,
        'entradav'          =>0*$lista->pcompra,
        'saldov'            =>0*$lista->pcompra,
      );
      $insertark=$this->kardex_model->insert($datak);

      //actualizar stock
      $datas=array('stock'=>0);
      $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));

      if ($lista->lote==1) {
        $detalles=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$lista->id);
        foreach ($detalles as $detalle) {
          $datac=array
          (
            'idestablecimiento' =>$this->session->userdata('predeterminado'),
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date('Y-m-d'),
            'idtmovimiento'     =>16,
            'concepto'          =>'Stock Reseteado',
            'idproducto'        =>$lista->id,
            'descripcion'       =>$lista->descripcion,
            'nlote'             =>$detalle->nlote,
            'entradaf'          =>0,
            'saldof'            =>0,
          );
          $insertarc=$this->kardexl_model->insert($datac);
        }

        $eliminar=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));
      }
    }

    $control_movimiento=$this->movimientos('producto/resetear','Reseteo el stock de productos activos a 0');

    $success=true;
    $titulo='Stock resetado a 0!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'empresa/configuracion';
    echo json_encode($proceso);
    exit();
  }

	public function laboratorios()
	{
    $controlip=$this->controlip('producto/laboratorios');
		$listas=$this->laboratorio_model->mostrarTotal();
		$buscador='idlaboratorio';
		$this->layout->setLayout("blanco");
		$this->layout->view("atributos",compact('listas','buscador'));
	}

	public function categorias()
	{
    $controlip=$this->controlip('producto/categorias');
		$listas=$this->categoria_model->mostrarTotal('F');
		$buscador='idcategoria';
		$this->layout->setLayout("blanco");
		$this->layout->view("atributos",compact('listas','buscador'));
	}

	public function resetearLaboratorio($id)
  {
    $controlip=$this->controlip('producto/resetearLaboratorio');
    $listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0,"idlaboratorio"=>$id));
    foreach ($listas as $lista)
    {
      $datak=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        'iduser'            =>$this->session->userdata('id'),
        'fecha'             =>date("Y-m-d"),
        'idtmovimiento'     =>16,
        'concepto'          =>'Stock Reseteado',
        'idproducto'        =>$lista->id,
        'descripcion'       =>$lista->descripcion,
        'entradaf'          =>0,
        'saldof'            =>0,
        'costo'             =>$lista->pcompra,
        'entradav'          =>0*$lista->pcompra,
        'saldov'            =>0*$lista->pcompra,
      );
      $insertark=$this->kardex_model->insert($datak);

      //actualizar stock
      $datas=array('stock'=>0);
      $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));

      if ($lista->lote==1) {
        $detalles=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$lista->id);
        foreach ($detalles as $detalle) {
          $datac=array
          (
            'idestablecimiento' =>$this->session->userdata('predeterminado'),
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date('Y-m-d'),
            'idtmovimiento'     =>16,
            'concepto'          =>'Stock Reseteado',
            'idproducto'        =>$lista->id,
            'descripcion'       =>$lista->descripcion,
            'nlote'             =>$detalle->nlote,
            'entradaf'          =>0,
            'saldof'            =>0,
          );
          $insertarc=$this->kardexl_model->insert($datac);
        }

        $eliminar=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));
      }
    }

    $control_movimiento=$this->movimientos('producto/resetearLaboratorio','Reseteo el stock de productos por laboratorio a 0');

    $success=true;
    $titulo='Stock resetado a 0!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'empresa/configuracion';
    echo json_encode($proceso);
    exit();
  }

	public function resetearCategoria($id)
  {
    $controlip=$this->controlip('producto/resetearCategoria');
    $listas=$this->inventario_model->productosStock(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"estado"=>1,"stock>"=>0,"idcategoria"=>$id));
    foreach ($listas as $lista)
    {
      $datak=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        'iduser'            =>$this->session->userdata('id'),
        'fecha'             =>date("Y-m-d"),
        'idtmovimiento'     =>16,
        'concepto'          =>'Stock Reseteado',
        'idproducto'        =>$lista->id,
        'descripcion'       =>$lista->descripcion,
        'entradaf'          =>0,
        'saldof'            =>0,
        'costo'             =>$lista->pcompra,
        'entradav'          =>0*$lista->pcompra,
        'saldov'            =>0*$lista->pcompra,
      );
      $insertark=$this->kardex_model->insert($datak);

      //actualizar stock
      $datas=array('stock'=>0);
      $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));

      if ($lista->lote==1) {
        $detalles=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$lista->id);
        foreach ($detalles as $detalle) {
          $datac=array
          (
            'idestablecimiento' =>$this->session->userdata('predeterminado'),
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date('Y-m-d'),
            'idtmovimiento'     =>16,
            'concepto'          =>'Stock Reseteado',
            'idproducto'        =>$lista->id,
            'descripcion'       =>$lista->descripcion,
            'nlote'             =>$detalle->nlote,
            'entradaf'          =>0,
            'saldof'            =>0,
          );
          $insertarc=$this->kardexl_model->insert($datac);
        }

        $eliminar=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$lista->id));
      }
    }

    $control_movimiento=$this->movimientos('producto/resetearCategoria','Reseteo el stock de productos por categoria a 0');

    $success=true;
    $titulo='Stock resetado a 0!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'empresa/configuracion';
    echo json_encode($proceso);
    exit();
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

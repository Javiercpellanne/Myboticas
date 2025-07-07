<?php
defined("BASEPATH") OR exit('No direct script access allowed');

class Compra extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(4)){redirect(base_url()."inicio");}

		$this->layout->setLayout("contraido");
		$this->load->model("tafectacion_model");
		$this->load->model("departamento_model");
		$this->load->model("provincia_model");
		$this->load->model("distrito_model");
		$this->load->model("tpago_model");
		$this->load->model("proveedor_model");
		$this->load->model("solicitud_model");
		$this->load->model("solicitudd_model");
		$this->load->model("compra_model");
		$this->load->model("comprad_model");
		$this->load->model("kardex_model");
		$this->load->model("kardexl_model");
		$this->load->model("lote_model");
		$this->load->model("pago_model");
		$this->load->library("mytcpdf");
	}

	public function index()
	{
    $controlip=$this->controlip('compra');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
		$fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d") ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tipo!="=>'G',"femision>="=>$inicio,"femision<="=>$fin);
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		$listas=$this->compra_model->mostrarTotal($filtros);
		$this->layout->setTitle("Compra Producto");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
	}

	public function proveedori()
	{
    $controlip=$this->controlip('compra/proveedori');
		if ($this->input->post("documento",true))
		{
			$consulta=$this->proveedor_model->contador($this->input->post("documento",true));
			if ($consulta==0) {
				$data=array
				(
					"tdocumento"			=>$this->input->post("tipo",true),
					"documento"				=>$this->input->post("documento",true),
					"nombres"					=>trim(mb_strtoupper($this->input->post('nombres',true), 'UTF-8')),
					"iddepartamento"	=>$this->input->post("departamento",true),
					"idprovincia"			=>$this->input->post("provincia",true),
					"iddistrito"			=>$this->input->post("distrito",true),
					"direccion"				=>$this->input->post("direccion",true),
					"telefono"				=>$this->input->post("telefono",true),
					"email"						=>$this->input->post("email",true),
				);

				$insertar=$this->proveedor_model->insert($data);
				$datos['success'] = true;
				$datos['data'] = array("idproveedor"=>$insertar,"proveedor"=>$this->input->post("nombres",true));
				$control_movimiento=$this->movimientos('compra/proveedori','Registro al proveedor '.$this->input->post('nombres',true));
			} else {
				$datos['success'] = false;
				$datos['data'] = "El numero de documento ya fue ingresado";
			}
			echo json_encode($datos);
			exit();
		}

    $departamentos=$this->departamento_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("proveedori",compact("departamentos"));
	}

	public function ordeni($id)
	{
    $controlip=$this->controlip('compra/ordeni');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$solicitud=$this->solicitud_model->mostrar($id);
		$detalles=$this->solicitudd_model->mostrarTotal($id);
		$mpagos=$this->tpago_model->mostrarTotal();
		$tafectaciones=$this->tafectacion_model->mostrarTotal();
		$this->layout->setTitle("Compra Producto");
		$this->layout->view("ordeni",compact("anexos","nestablecimiento",'empresa',"mpagos","solicitud","detalles",'tafectaciones',"id"));
	}

	public function comprai()
	{
    $controlip=$this->controlip('compra/comprai');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$mpagos=$this->tpago_model->mostrarTotal();
		$tafectaciones=$this->tafectacion_model->mostrarTotal();
		$this->layout->setTitle("Compra Producto");
		$this->layout->view("comprai",compact("anexos","nestablecimiento",'empresa',"mpagos",'tafectaciones'));
	}

	public function guardar($id=null)
	{
    $controlip=$this->controlip('compra/guardar');
		$empresa=$this->empresa_model->mostrar();
		if ($this->input->post())
		{
			$url='';
			if ($this->input->post("idproducto",true)==null) {
				$mensaje='No envio productos en la compra!';
			} else {
				$consulta=$this->compra_model->contador(array("nulo"=>0,"idproveedor"=>$this->input->post("idproveedor",true),"serie"=>$this->input->post("serie",true),"numero"=>$this->input->post("numero",true)));
				if ($consulta==0) {
					$data=array
			    (
			    	"idestablecimiento"	=>$this->session->userdata("predeterminado"),
			      "iduser"    				=>$this->session->userdata('id'),
            "tipo"              =>'B',
			      "femision"    			=>$this->input->post("fecha",true),
			      "comprobante" 			=>$this->input->post("comprobante",true),
			      "serie"     				=>$this->input->post("serie",true),
			      "numero"    				=>$this->input->post("numero",true),
			      "idproveedor" 			=>$this->input->post("idproveedor",true),
			      "proveedor"   			=>$this->input->post("proveedor",true),
						"incluye"						=>valor_check($this->input->post("incluye",true)),
			      'tgravado'					=>$this->input->post('gravado',true),
						'tinafecto'					=>$this->input->post('inafecto',true),
						'texonerado'				=>$this->input->post('exonerado',true),
						'tgratuito'					=>$this->input->post('gratuito',true),
			      "subtotal"    			=>$this->input->post("subtotal",true),
			      "igv"      					=>$this->input->post("igv",true),
			      "total"   					=>$this->input->post("total",true),
			      "percepcion"  			=>$this->input->post("mpercepcion",true),
			      "condicion"   			=>$this->input->post("tpago",true),
            "dadicional"        =>$this->input->post("dadicional",true),
			    );
					if ($empresa->compra==0) {
						$data["almacen"]=1;
					}
			    if ($this->input->post("tpago",true)==1) {
						$data["cancelado"]=1;
			    }
			    $insertar=$this->compra_model->insert($data);

			    if ($this->input->post('tpago',true)==1) {
				    $datap=array
		        (
		        	'idestablecimiento'	=>$this->session->userdata("predeterminado"),
		          'iduser'      			=>$this->session->userdata('id'),
		          'idcompra'    			=>$insertar,
		          'femision'    			=>date("Y-m-d"),
		          'total'     				=>$this->input->post('total',true),
		          'idtpago'     			=>$this->input->post('mpago',true),
		        );
						$insertarp=$this->pago_model->insert($datap);
					}

			    for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
			      $datad=array
			      (
			        "idcompra"    =>$insertar,
			        "idproducto"  =>$this->input->post("idproducto",true)[$i],
			        "descripcion" =>trim($this->input->post("descripcion",true)[$i]),
			        "tafectacion" =>$this->input->post("tafectacion",true)[$i],
			        "unidad"      =>$this->input->post("unidad",true)[$i],
			        "factor"      =>$this->input->post("factor",true)[$i],
			        "cantidad"    =>$this->input->post("cantidad",true)[$i],
			        "precio"      =>$this->input->post("precio",true)[$i],
			        "importe"     =>$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("importe",true)[$i],
			        "calmacen"    =>$this->input->post("almacenc",true)[$i],
			        "palmacen"    =>$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("almacenp",true)[$i],
			        "lote"        =>$this->input->post("lote",true)[$i],
			        "fvencimiento"=>valor_fecha($this->input->post("fvencimiento",true)[$i]),
			      );

            if ($this->input->post("pventa",true)[$i]>0) {
			      	$precioventa["pventa"]=$this->input->post("pventa",true)[$i];
			      	$precioventa["venta"]=$this->input->post("venta",true)[$i];
			      	$precioventa["pblister"]=$this->input->post("blister",true)[$i];
              $datad["pventas"]=json_encode($precioventa);
            }
			      $insertard=$this->comprad_model->insert($datad);

			      if ($empresa->compra==0) {
							if (valor_check($this->input->post("incluye",true))==0 && $this->input->post("tafectacion",true)[$i]==10) {
			          $totalu=round($this->input->post("importe",true)[$i]+($this->input->post("importe",true)[$i]*0.18),2);
			          $preciou=round($totalu/$this->input->post("almacenc",true)[$i],2);
			          $preciom=round($this->input->post("precio",true)[$i]+($this->input->post("precio",true)[$i]*0.18),2);
			        } else {
			          $totalu=$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("importe",true)[$i];
			          $preciou=$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("almacenp",true)[$i];
			          $preciom=$this->input->post("precio",true)[$i];
			        }

			        $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
			        $inicalf=$saldos==null ? 0: $saldos->saldof;
			        $inicalv=$saldos==null ? 0: $saldos->saldov;

			        $saldof=$inicalf+$this->input->post("almacenc",true)[$i];
			        $saldov=$inicalv+$totalu;
			        $datak=array
			        (
			        	'idestablecimiento'	=>$this->session->userdata('predeterminado'),
			          'iduser'    				=>$this->session->userdata('id'),
			          'fecha'     				=>date('Y-m-d'),
			          'idtmovimiento' 		=>2,
			          'concepto'    			=>'Compra',
			          'idproducto'  			=>$this->input->post('idproducto',true)[$i],
			          'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
			          'entradaf'    			=>$this->input->post('almacenc',true)[$i],
			          'saldof'    				=>$saldof,
			          'costo'     				=>$preciou,
			          'entradav'    			=>$totalu,
			          'saldov'    				=>$saldov,
			          'documento'   			=>$this->input->post("serie",true)."-".$this->input->post("numero",true),
			        );
			        $insertark=$this->kardex_model->insert($datak);

			        if ($this->input->post("tafectacion",true)[$i]!=15) {
				        if ($this->input->post("almacenc",true)[$i]==$this->input->post("cantidad",true)[$i]) {
				          $producto=$this->producto_model->mostrar(array("p.id"=>$this->input->post("idproducto",true)[$i]));
				          $preciom=$preciou*$producto->factor;
				        }
				        $datap=array
				        (
				          "compra"  =>$preciom,
				          "pcompra" =>$preciou,
				        );
				        $actualizar=$this->producto_model->update($datap,$this->input->post("idproducto",true)[$i]);
			        }

			        $datas=array('stock'=>$saldof);
							$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));

			        if ($this->input->post("lote",true)[$i]!="") {
			          $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$this->input->post("lote",true)[$i]));

			          if ($consultal==null) {
			            $datal=array
			            (
			            	'idestablecimiento'	=>$this->session->userdata("predeterminado"),
			              'idproducto'  			=>$this->input->post("idproducto",true)[$i],
			              'nlote'     				=>$this->input->post("lote",true)[$i],
			              'fvencimiento'  		=>valor_fecha($this->input->post("fvencimiento",true)[$i]),
			              'inicial'   				=>$this->input->post("almacenc",true)[$i],
			              'stock'     				=>$this->input->post("almacenc",true)[$i],
			            );
			            $insertarl=$this->lote_model->insert($datal);
			          } else {
			            $datal=array("stock"=>$consultal->stock+$this->input->post("almacenc",true)[$i]);
			            $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$this->input->post("lote",true)[$i]);
			          }

			          $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$this->input->post("lote",true)[$i]);
								$inicial=$saldos==null ? 0: $saldos->saldof;
								$saldosl=$inicial+$this->input->post('almacenc',true)[$i];
								$datac=array
								(
									'idestablecimiento'	=>$this->session->userdata("predeterminado"),
									'iduser'    				=>$this->session->userdata('id'),
									'fecha'							=>date('Y-m-d'),
									'idtmovimiento' 		=>2,
									'concepto'					=>'Compra',
									'idproducto'  			=>$this->input->post('idproducto',true)[$i],
			          	'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
									'nlote'							=>$this->input->post('lote',true)[$i],
									'entradaf'					=>$this->input->post('almacenc',true)[$i],
									'saldof'						=>$saldosl,
									'documento'					=>$this->input->post('serie',true)."-".$this->input->post('numero',true),
								);
								$insertarc=$this->kardexl_model->insert($datac);
			        }

				      //modificar precios de venta
				      if ($this->input->post("pventa",true)[$i]>0) {
					      $dataa=array();
					      if ($this->input->post("pventa",true)[$i]>0)
					      {
					      	$dataa["pventa"]=$this->input->post("pventa",true)[$i];
					      }
					      if ($this->input->post("venta",true)[$i]>0)
					      {
					      	$dataa["venta"]=$this->input->post("venta",true)[$i];
					      }
					      if ($this->input->post("blister",true)[$i]>0)
					      {
					      	$dataa["pblister"]=$this->input->post("blister",true)[$i];
					      }
					      if ($empresa->pestablecimiento==0) {
					      	$actualizara=$this->producto_model->update($dataa,$this->input->post("idproducto",true)[$i]);
					      } else {
					      	$actualizara=$this->inventario_model->update($dataa,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
					      }
				      }
			      }
			    }

			    if ($id!=null) {
						$datat=array("estado"=>2);
						$actualizar=$this->solicitud_model->update($datat,$id);
			    }
					$control_movimiento=$this->movimientos('compra/guardar','Registro compra con documento '.$this->input->post("serie",true).'-'.$this->input->post("numero",true));

					$mensaje='Los datos se han guardado exitosamente!';
	        $url=base_url().'compra';
				} else {
					$mensaje='El comprobante ya existe';
				}
			}

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
		}
	}

	public function comprae($id)
	{
    $controlip=$this->controlip('compra/comprae');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$tafectaciones=$this->tafectacion_model->mostrarTotal();
		$this->layout->setTitle("Compra Producto");
		$this->layout->view("comprae",compact("anexos","nestablecimiento",'empresa',"datos","detalles",'tafectaciones',"id"));
	}

	public function actualizar($id)
	{
    $controlip=$this->controlip('compra/actualizar');
		if ($this->input->post())
		{
			$url='';
			if ($this->input->post("idproducto",true)==null) {
				$mensaje='No envio productos en la compra!';
			} else {
				$data=array
				(
					"femision"				=>$this->input->post("fecha",true),
					"comprobante"			=>$this->input->post("comprobante",true),
					"serie"						=>$this->input->post("serie",true),
					"numero"					=>$this->input->post("numero",true),
					"idproveedor"			=>$this->input->post("idproveedor",true),
					"proveedor"				=>$this->input->post("proveedor",true),
					"incluye"					=>valor_check($this->input->post("incluye",true)),
		      'tgravado'				=>$this->input->post('gravado',true),
					'tinafecto'				=>$this->input->post('inafecto',true),
					'texonerado'			=>$this->input->post('exonerado',true),
					'tgratuito'				=>$this->input->post('gratuito',true),
					"subtotal"				=>$this->input->post("subtotal",true),
					"igv"							=>$this->input->post("igv",true),
					"total"						=>$this->input->post("total",true),
					"percepcion"			=>$this->input->post("mpercepcion",true),
				);
				$actualizar=$this->compra_model->update($data,$id);

				$datap=array('total'=>$this->input->post('total',true));
				$actualizarp=$this->pago_model->update($datap,array("idcompra"=>$id));

				for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
					if (isset($this->input->post("id",true)[$i])) {
						$datad=array
						(
							"idcompra"		=>$id,
							"idproducto"	=>$this->input->post("idproducto",true)[$i],
							"descripcion"	=>trim($this->input->post("descripcion",true)[$i]),
							"unidad"			=>$this->input->post("unidad",true)[$i],
			        "tafectacion" =>$this->input->post("tafectacion",true)[$i],
							"factor"      =>$this->input->post("factor",true)[$i],
							"cantidad"		=>$this->input->post("cantidad",true)[$i],
							"precio"			=>$this->input->post("precio",true)[$i],
							"importe"			=>$this->input->post("importe",true)[$i],
							"calmacen"		=>$this->input->post("almacenc",true)[$i],
							"palmacen"		=>$this->input->post("almacenp",true)[$i],
							"lote"				=>$this->input->post("lote",true)[$i],
							"fvencimiento"=>valor_fecha($this->input->post("fvencimiento",true)[$i]),
						);
						$actualizard=$this->comprad_model->update($datad,$this->input->post("id",true)[$i]);
					} else {
						$datad=array
						(
							"idcompra"		=>$id,
							"idproducto"	=>$this->input->post("idproducto",true)[$i],
							"descripcion"	=>trim($this->input->post("descripcion",true)[$i]),
							"unidad"			=>$this->input->post("unidad",true)[$i],
			        "tafectacion" =>$this->input->post("tafectacion",true)[$i],
							"factor"      =>$this->input->post("factor",true)[$i],
							"cantidad"		=>$this->input->post("cantidad",true)[$i],
							"precio"			=>$this->input->post("precio",true)[$i],
							"importe"			=>$this->input->post("importe",true)[$i],
							"calmacen"		=>$this->input->post("almacenc",true)[$i],
							"palmacen"		=>$this->input->post("almacenp",true)[$i],
							"lote"				=>$this->input->post("lote",true)[$i],
							"fvencimiento"=>valor_fecha($this->input->post("fvencimiento",true)[$i]),
						);

            if ($this->input->post("pventa",true)[$i]>0) {
			      	$precioventa["pventa"]=$this->input->post("pventa",true)[$i];
			      	$precioventa["venta"]=$this->input->post("venta",true)[$i];
			      	$precioventa["pblister"]=$this->input->post("blister",true)[$i];
              $datad["pventas"]=json_encode($precioventa);
            }
						$insertard=$this->comprad_model->insert($datad);
					}
				}
				$control_movimiento=$this->movimientos('compra/actualizar','Edito compra con documento '.$this->input->post("serie",true).'-'.$this->input->post("numero",true));

				$mensaje='Los datos se han guardado exitosamente!';
	      $url=base_url().'compra';
			}

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
		}
	}

	public function almacen($id)
	{
    $controlip=$this->controlip('compra/almacen');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		if ($this->input->post())
		{
			$url='';
			$consulta=$this->compra_model->mostrar($id);
			if ($consulta->almacen==0) {
				for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
					if ($this->input->post("incluye",true)==0 && $this->input->post("tafectacion",true)[$i]==10) {
						$totalu=round($this->input->post("importe",true)[$i]+($this->input->post("importe",true)[$i]*0.18),2);
						$preciou=round($totalu/$this->input->post("almacenc",true)[$i],2);
						$preciom=round($this->input->post("precio",true)[$i]+($this->input->post("precio",true)[$i]*0.18),2);
					} else {
						$totalu=$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("importe",true)[$i];
						$preciou=$this->input->post("tafectacion",true)[$i]==15 ? 0: $this->input->post("almacenp",true)[$i];
						$preciom=$this->input->post("precio",true)[$i];
					}

					$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
					$inicalf=$saldos==null ? 0: $saldos->saldof;
					$inicalv=$saldos==null ? 0: $saldos->saldov;

					$saldof=$inicalf+$this->input->post("almacenc",true)[$i];
					$saldov=$inicalv+$totalu;
					$datak=array
					(
						"idestablecimiento"	=>$this->session->userdata("predeterminado"),
						'iduser'						=>$this->session->userdata('id'),
						"fecha"							=>date("Y-m-d"),
						"idtmovimiento"			=>2,
						"concepto"					=>"Compra",
						"idproducto"				=>$this->input->post("idproducto",true)[$i],
						"descripcion"				=>trim($this->input->post("descripcion",true)[$i]),
						"entradaf"					=>$this->input->post("almacenc",true)[$i],
						"saldof"						=>$saldof,
						"costo"							=>$preciou,
						"entradav"					=>$totalu,
						"saldov"						=>$saldov,
						"documento"					=>$this->input->post("documento",true),
					);
					$insertark=$this->kardex_model->insert($datak);

					if ($this->input->post("tafectacion",true)[$i]!=15) {
						if ($this->input->post("almacenc",true)[$i]==$this->input->post("cantidad",true)[$i]) {
		          $producto=$this->producto_model->mostrar(array("p.id"=>$this->input->post("idproducto",true)[$i]));
		          $preciom=$preciou*$producto->factor;
		        }
		        $datap=array
		        (
		          "compra"  =>$preciom,
		          "pcompra" =>$preciou,
		        );
		        $actualizar=$this->producto_model->update($datap,$this->input->post("idproducto",true)[$i]);
					}

	        $datas=array('stock'=>$saldof);
					$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));

					if ($this->input->post("lote",true)[$i]!="") {
	          $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$this->input->post("lote",true)[$i]));

	          if ($consultal==null) {
	            $datal=array
	            (
	            	'idestablecimiento'	=>$this->session->userdata("predeterminado"),
	              'idproducto'  			=>$this->input->post("idproducto",true)[$i],
	              'nlote'     				=>$this->input->post("lote",true)[$i],
	              'fvencimiento'  		=>valor_fecha($this->input->post("fvencimiento",true)[$i]),
	              'inicial'   				=>$this->input->post("almacenc",true)[$i],
	              'stock'     				=>$this->input->post("almacenc",true)[$i],
	            );
	            $insertarl=$this->lote_model->insert($datal);
	          } else {
	            $datal=array("stock"=>$consultal->stock+$this->input->post("almacenc",true)[$i]);
	            $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$this->input->post("lote",true)[$i]);
	          }

	          $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$this->input->post("lote",true)[$i]);
						$inicial=$saldos==null ? 0: $saldos->saldof;
						$saldosl=$inicial+$this->input->post('almacenc',true)[$i];
						$datac=array
						(
							'idestablecimiento'	=>$this->session->userdata('predeterminado'),
							'iduser'    				=>$this->session->userdata('id'),
							'fecha'							=>date('Y-m-d'),
							'idtmovimiento' 		=>2,
							'concepto'					=>'Compra',
							'idproducto'  			=>$this->input->post('idproducto',true)[$i],
	          	'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
							'nlote'							=>$this->input->post('lote',true)[$i],
							'entradaf'					=>$this->input->post('almacenc',true)[$i],
							'saldof'						=>$saldosl,
							'documento'					=>$this->input->post("documento",true),
						);
						$insertarc=$this->kardexl_model->insert($datac);
	        }

		      //modificar precios de venta
		      if ($this->input->post("pventa",true)[$i]>0) {
			      $dataa=array();
			      if ($this->input->post("pventa",true)[$i]>0)
			      {
			      	$dataa["pventa"]=$this->input->post("pventa",true)[$i];
			      }
			      if ($this->input->post("venta",true)[$i]>0)
			      {
			      	$dataa["venta"]=$this->input->post("venta",true)[$i];
			      }
			      if ($this->input->post("blister",true)[$i]>0)
			      {
			      	$dataa["pblister"]=$this->input->post("blister",true)[$i];
			      }
			      if ($empresa->pestablecimiento==0) {
			      	$actualizara=$this->producto_model->update($dataa,$this->input->post("idproducto",true)[$i]);
			      } else {
			      	$actualizara=$this->inventario_model->update($dataa,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
			      }
		      }
				}

				$data=array("almacen"=>1);
				$actualizar=$this->compra_model->update($data,$id);
				$control_movimiento=$this->movimientos('compra/almacen','Ingreso almacen compra con documento '.$consulta->serie.'-'.$consulta->numero);

				$mensaje='Se ingreso al almacen exitosamente!';
				$url=base_url().'compra';
			} else {
				$mensaje='No se puede realizar el proceso ya se envio anteriormente almacen!';
			}

			$datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
		}

		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$this->layout->setTitle("Ingreso Compra");
		$this->layout->view("almacen",compact("anexos","nestablecimiento",'empresa',"datos","detalles","id"));
	}

	public function compraa($id)
	{
    $controlip=$this->controlip('compra/compraa');
		$datos=$this->compra_model->mostrar($id);
		if ($datos->nulo==0) {
			if ($datos->almacen==1) {
				$detalles=$this->comprad_model->mostrarTotal($id);
				foreach ($detalles as $detalle) {
					if ($datos->incluye==0 && $detalle->tafectacion==10) {
						$total=round($detalle->importe+($detalle->importe*0.18),2);
						$costo=round($total/$detalle->calmacen,2);
					} else {
						$total=$detalle->importe;
						$costo=$detalle->palmacen;
					}

					$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));
					$inicalf=$saldos==null ? 0: $saldos->saldof;
					$inicalv=$saldos==null ? 0: $saldos->saldov;

					$saldof=$inicalf-$detalle->calmacen;
					$saldov=$inicalv-$total;
					$datak=array
					(
						"idestablecimiento"	=>$datos->idestablecimiento,
						"iduser"						=>$this->session->userdata('id'),
						"fecha"							=>date("Y-m-d"),
						"idtmovimiento"			=>2,
						"concepto"					=>"Anulacion Compra",
						"idproducto"				=>$detalle->idproducto,
						"descripcion"				=>$detalle->descripcion,
						"salidaf"						=>$detalle->calmacen,
						"saldof"						=>$saldof,
						"costo"							=>$costo,
						"salidav"						=>$total,
						"saldov"						=>$saldov,
						"documento"					=>$datos->serie."-".$datos->numero,
					);
					//var_dump($datak);
					$insertark=$this->kardex_model->insert($datak);

					$datas=array("stock"=>$saldof);
					$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

					if ($detalle->lote!='') {
						$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$detalle->lote));
						if ($consultal->stock>$detalle->calmacen) {
							$datal=array("stock"=>$consultal->stock-$detalle->calmacen);
							$actualizar=$this->lote_model->update($datal,$datos->idestablecimiento,$detalle->idproducto,$detalle->lote);
						} else {
							$elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$detalle->lote));
						}

						$saldos=$this->kardexl_model->ultimo($datos->idestablecimiento,$detalle->idproducto,$detalle->lote);
						$inicial=$saldos==null ? 0: $saldos->saldof;
						$saldosl=$inicial-$detalle->calmacen;
						$datac=array
						(
							'idestablecimiento'	=>$datos->idestablecimiento,
							"iduser"    				=>$this->session->userdata('id'),
							"fecha"							=>date("Y-m-d"),
							"idtmovimiento" 		=>2,
	          	"concepto"    			=>"Anulacion Compra",
							"idproducto"				=>$detalle->idproducto,
							"descripcion"				=>$detalle->descripcion,
							"nlote"							=>$detalle->lote,
							"salidaf"						=>$detalle->calmacen,
							"saldof"						=>$saldosl,
	          	"documento"   			=>$datos->serie."-".$datos->numero,
						);
						$insertarc=$this->kardexl_model->insert($datac);
					}
				}
			}

			$datap=array
			(
				"nulo"	 	=>1,
				"total"	=>"0.00",
			);
			$actualizap=$this->pago_model->update($datap,array("idcompra"=>$id));

			$data=array
			(
				"nulo"	 			=>1,
				"subtotal"		=>"0.00",
				"igv"					=>"0.00",
				"total"				=>"0.00",
			);
			$actualiza=$this->compra_model->update($data,$id);
			$control_movimiento=$this->movimientos('compra/compraa','Anulo compra con documento '.$datos->serie.'-'.$datos->numero);

      $success=true;
      $titulo='Anulado!';
      $mensaje='El proceso se realizo con exito';
      $color='success';
		}else{
      $success=false;
      $titulo='No se puede anular!';
      $mensaje='El proceso no se realizo por que esta siendo usado en otro registro';
      $color='error';
		}

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'compra';
    echo json_encode($proceso);
    exit();
	}

  public function eliminar($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('compra/eliminar');
    $datos=$this->comprad_model->mostrar($id);
    if ($datos==NULL) {show_404();}
    $eliminar=$this->comprad_model->delete($id);
		$control_movimiento=$this->movimientos('compra/eliminar','Elimino de la compra producto '.$datos->descripcion);
    echo 1;
  }

	public function pagos($id)
	{
		$datos=$this->compra_model->mostrar($id);
		$listas=$this->pago_model->mostrarTotal(array("idcompra"=>$id));
		$this->layout->setLayout("blanco");
		$this->layout->view("pagos",compact("datos","listas"));
	}

	public function consulta($id)
	{
		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$this->layout->setLayout("blanco");
		$this->layout->view("consulta",compact("datos","detalles"));
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
		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$proveedor=$this->proveedor_model->mostrar(array("p.id"=>$datos->idproveedor));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa4",compact("empresa","nestablecimiento","datos","detalles","proveedor","id"));
  }

	public function pdf80($id)
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$proveedor=$this->proveedor_model->mostrar(array("p.id"=>$datos->idproveedor));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf80",compact("empresa","nestablecimiento","datos","detalles","proveedor","id"));
  }

	public function pdf58($id)
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$proveedor=$this->proveedor_model->mostrar(array("p.id"=>$datos->idproveedor));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf58",compact("empresa","nestablecimiento","datos","detalles","proveedor","id"));
  }

	public function pdfa5($id)
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->compra_model->mostrar($id);
		$detalles=$this->comprad_model->mostrarTotal($id);
		$proveedor=$this->proveedor_model->mostrar(array("p.id"=>$datos->idproveedor));
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa5",compact("empresa","nestablecimiento","datos","detalles","proveedor","id"));
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

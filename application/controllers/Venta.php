<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login')){redirect(base_url().'login');}
    if (!$this->acciones(2)){redirect(base_url()."inicio");}
    if (!is_numeric($this->session->userdata('codigo'))){redirect(base_url().'inicio');}

		$this->layout->setLayout('contraido');
		$this->load->model("testado_model");
		$this->load->model('tidentidad_model');
		$this->load->model('tcomprobante_model');
		$this->load->model('serie_model');
		$this->load->model('tcredito_model');
		$this->load->model('tdebito_model');
		$this->load->model('tpago_model');
		$this->load->model('departamento_model');
		$this->load->model("provincia_model");
		$this->load->model("distrito_model");
		$this->load->model('cliente_model');
		$this->load->model("tafectacion_model");
		$this->load->model("categoria_model");
		$this->load->model("laboratorio_model");
		$this->load->model("pactivo_model");
		$this->load->model("aterapeutica_model");
		$this->load->model('producto_model');
		$this->load->model('lote_model');
		$this->load->model('kardex_model');
		$this->load->model('kardexl_model');
		$this->load->model("bonificado_model");
    $this->load->model("toperacion_model");
    $this->load->model("tdetraccion_model");
    $this->load->model("tmedio_model");
		$this->load->model('cotizacion_model');
		$this->load->model('cotizaciond_model');
		$this->load->model("nventa_model");
		$this->load->model("nventad_model");
		$this->load->model("cobro_model");
		$this->load->model('venta_model');
		$this->load->model('ventad_model');
		$this->load->model('cobroe_model');
		$this->load->model('nota_model');
		$this->load->model('notad_model');
		$this->load->model('cobron_model');
		$this->load->model("arqueo_model");
		$this->load->model("punto_model");
		$this->load->model("clientep_model");
		$this->load->model("vale_model");
		$this->load->model("anulado_model");
    $this->load->model("anuladod_model");
		$this->load->model("resumen_model");
		$this->load->model("resumend_model");

		$this->load->library("mytcpdf");
    $this->load->library("generadorXML");
    $this->load->library("firmarXML");
    $this->load->library("apiFacturacion");
    $this->generadoXML = new GeneradorXML();
    $this->firmadoXML = new FirmarXML();
    $this->apiFacturacion = new ApiFacturacion();
	}

	public function index()
	{
    $controlip=$this->controlip('venta');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-7 day',date('Y-m-d')) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"));
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		if ($empresa->lventa==1) {$filtros["femision>="]=$inicio; $filtros["femision<="]=$fin;}
		$listas=$empresa->lventa==1 ? $this->venta_model->mostrarTotal($filtros,"desc"): $this->venta_model->mostrarLimite($filtros,"desc");

		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		$anulacionv=$this->usuario_model->mostrar($this->session->userdata("id"));
		$this->layout->setTitle('Venta Producto');
		$this->layout->view('index',compact("anexos","nestablecimiento",'listas','inicio','fin','empresa','arqueoc','anulacionv'));
	}

	public function arqueoi()
	{
    $controlip=$this->controlip('venta/arqueoi');
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
      $control_movimiento=$this->movimientos('venta/arqueoi','Registro arqueo nro '.$insertar);

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");

			echo base_url()."venta/ventai";
			exit();
		}

		$this->layout->setLayout("blanco");
		$this->layout->view("arqueoi");
	}

	public function busSerie()
	{
		if ($this->input->post())
		{
			$datos=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>$this->input->post('nro',true)));
			echo $datos->serie;
		}
		else
		{
			show_404();
		}
	}

  public function busDetraccion()
  {
    if ($this->input->post())
    {
      $datos=$this->tdetraccion_model->mostrar($this->input->post('nro',true));
      echo $datos->porcentaje;
    }
    else
    {
      show_404();
    }
  }

	public function presupuestoi($id)
	{
    $controlip=$this->controlip('venta/presupuestoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$canexos=$this->establecimiento_model->contador();
		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		if ($arqueoc==0) {redirect(base_url().'venta');}

		$mpagos=$this->tpago_model->mostrarTotal();
		$comprobantes=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'03'));
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$cotizacion=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($cotizacion->idcliente);
    $toperaciones=$this->toperacion_model->mostrarTotal();
    $codigos=$this->tdetraccion_model->mostrarTotal();
    $medios=$this->tmedio_model->mostrarTotal();
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle('Venta Producto');
		$this->layout->view('presupuestoi',compact("anexos","nestablecimiento",'empresa',"canexos",'mpagos','comprobantes','nserie',"productos","cotizacion","detalles","cliente","toperaciones","codigos","medios","vendedores","id"));
	}

	public function nventai($id)
	{
    $controlip=$this->controlip('venta/nventai');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$canexos=$this->establecimiento_model->contador();
		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		if ($arqueoc==0) {redirect(base_url().'venta');}

		$comprobantes=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'03'));
		$nventa=$this->nventa_model->mostrar($id);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($nventa->idcliente);
    $toperaciones=$this->toperacion_model->mostrarTotal();
    $codigos=$this->tdetraccion_model->mostrarTotal();
    $medios=$this->tmedio_model->mostrarTotal();
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle('Venta Producto');
		$this->layout->view('nventai',compact("anexos","nestablecimiento",'empresa',"canexos",'comprobantes','nserie',"nventa","detalles","cliente","toperaciones","codigos","medios","vendedores","id"));
	}

	public function guardav($id)
	{
    $controlip=$this->controlip('venta/guardav');
		if ($this->input->post())
		{
			$impresion='';
			$empresa=$this->empresa_model->mostrar();
			if ($this->input->post('idproducto',true)==null) {
				$mensaje='No envio productos en la venta!';
			} else {
				if ($this->input->post('totalg',true)>0) {
					$tcomprobante=$this->input->post('comprobante',true);
					$serie=SUBSTR($this->input->post('serie',true),0,1);
					$cliente=$this->cliente_model->mostrar($this->input->post('idcliente',true));

					if ($tcomprobante=='01' && $serie=='F' && $cliente->tdocumento==6 || $tcomprobante=='03' && $serie=='B') {
						if ($serie=="B" && $this->input->post('totalg',true)>700 && $cliente->tdocumento==0) {
							$mensaje='El monto a emitir es mayor a S/.700 requiere DNI del cliente';
						}else{
							$numero=$this->venta_model->maximo($this->input->post('serie',true));
							$ninicio= $numero==null ? '' : $numero->numero;
							$numeracion=$ninicio+1;

							$consulta=$this->venta_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
							if ($consulta==0) {
								$comprobante=array
								(
									'idestablecimiento'	=>$this->session->userdata("predeterminado"),
									'iduser'						=>$this->session->userdata('id'),
									"grupo"        		 	=>$this->input->post('comprobante',true)=='01' ? '01' : '02',
									'tipo_soap'         =>$empresa->tipo_soap,
									'femision'					=>date("Y-m-d"),
									'hemision'					=>date('H:i:s'),
									'fvencimiento'			=>date("Y-m-d"),
									'tcomprobante'			=>$this->input->post('comprobante',true),
									'serie'							=>$this->input->post('serie',true),
									'numero'						=>$numeracion,
									'toperacion'				=>$this->input->post('toperacion',true),
									'moneda'						=>'PEN',
									'idcliente'					=>$this->input->post('idcliente',true),
									'cliente'						=>$this->input->post('cliente',true),
									'tgravado'					=>$this->input->post('gravado',true),
									'tinafecto'					=>$this->input->post('inafecto',true),
									'texonerado'				=>$this->input->post('exonerado',true),
									'tgratuito'					=>$this->input->post('gratuito',true),
									'subtotal'					=>$this->input->post('gravado',true)+$this->input->post('inafecto',true)+$this->input->post('exonerado',true),
									'tigv'							=>$this->input->post('igv',true),
									'total'							=>$this->input->post('totalg',true),
									'izipay'						=>valor_fecha($this->input->post('mizipay',true)),
									'lote'							=>valor_check($this->input->post('impresion',true)),
									'dadicional'				=>$this->input->post('dadicional',true),
									'ocompra'						=>$this->input->post('ocompra',true),
									'idvendedor'				=>$this->input->post('vendedor',true),
									'condicion'					=>1,
									'cancelado'					=>1,
									'tipo_estado'				=>'01',
								);

								if ($this->input->post('mdsctog',true)!='') {
									$msubtotal=$this->input->post('bimponible',true);
									$mdescuento=round($this->input->post('mdsctog',true)/1.18,2);
									$fdescuento=round(($mdescuento*100)/($msubtotal*100),4);

									$descuentos["codigo"]="02";
                  $descuentos["descripcion"]="Descuentos globales que afectan la base imponible del IGV/IVAP";
                  $descuentos["factor"]=$fdescuento;
                  $descuentos["monto"]=$mdescuento;
                  $descuentos["base"]=$msubtotal;
						      $comprobante["descuentos"]=json_encode($descuentos);
						    }

                if ($this->input->post('pdetraccion',true)>0) {
                  $descuentos["codigo"]=$this->input->post('codigo',true);
                  $descuentos["ncuenta"]=$this->input->post('ncuenta',true);
                  $descuentos["medio"]=$this->input->post('medio',true);
                  $descuentos["factor"]=$this->input->post('pdetraccion',true);
                  $descuentos["monto"]=$this->input->post('mdetraccion',true);

                  $comprobante["detraccion"]=json_encode($descuentos);
                }

								if ($this->input->post('pretencion',true)>0) {
									$descuentos["codigo"]="62";
			            $descuentos["descripcion"]="Retencion del IGV";
			            $descuentos["factor"]=round($this->input->post('pretencion',true)/100,4);
			            $descuentos["monto"]=$this->input->post('mretencion',true);
			            $descuentos["base"]=$this->input->post('totalg',true);

			            $comprobante["retencion"]=json_encode($descuentos);
								}
								$insertar=$this->venta_model->insert($comprobante);

								for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
									if ($this->input->post('tipo',true)[$i]=='B') {
										$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
										$inicalf=$saldos==null ? 0: $saldos->saldof;
										$inicalv=$saldos==null ? 0: $saldos->saldov;

										$saldof=$inicalf-$this->input->post("calmacen",true)[$i];
										$saldov=$inicalv-$this->input->post("palmacen",true)[$i];
										$datak=array
										(
											'idestablecimiento'	=>$this->session->userdata("predeterminado"),
											'iduser'						=>$this->session->userdata('id'),
											'fecha'							=>date('Y-m-d'),
											"idtmovimiento"			=>1,
											'concepto'					=>'Venta',
											'idproducto'				=>$this->input->post("idproducto",true)[$i],
											'descripcion'				=>trim($this->input->post("descripcion",true)[$i]),
											'salidaf'						=>$this->input->post("calmacen",true)[$i],
											'saldof'						=>$saldof,
											'costo'							=>0,
											'salidav'						=>$this->input->post("palmacen",true)[$i],
											'saldov'						=>$saldov,
											'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
										);
										$insertark=$this->kardex_model->insert($datak);

										if ($this->input->post("lote",true)[$i]==1 && $this->input->post("nlote",true)[$i]!='') {
											$clotes=explode("|",$this->input->post("clote",true)[$i]);
											$nlotes=explode("|",$this->input->post("nlote",true)[$i]);
											$flotes=explode("|",$this->input->post("flote",true)[$i]);
											for ($l=0; $l < count($nlotes) ; $l++) {
												$saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlotes[$l]);
												$inicial=$saldos==null ? 0: $saldos->saldof;
												$saldosl=$inicial-$clotes[$l];
												$datac=array
												(
													'idestablecimiento'	=>$this->session->userdata("predeterminado"),
													'iduser'    				=>$this->session->userdata('id'),
													'fecha'							=>date('Y-m-d'),
													'idtmovimiento'			=>1,
													'concepto'					=>'Venta',
													'idproducto'  			=>$this->input->post('idproducto',true)[$i],
						        			'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
													'nlote'							=>$nlotes[$l],
													'salidaf'						=>$clotes[$l],
													'saldof'						=>$saldosl,
													'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
												);
												$insertarc=$this->kardexl_model->insert($datac);
											}
										}
									}

				  					/*----------  conversion tipo afectacion  ----------*/
									if ($this->input->post('tafectacion',true)[$i]==10) {
										$tprecio='01';
										$valoru=round($this->input->post('precio',true)[$i]/1.18,6);
										$valor=round($this->input->post('importe',true)[$i]/1.18,2);
										$igv=round(($this->input->post('importe',true)[$i]*0.18)/1.18,2);
										$importe=$this->input->post('importe',true)[$i];
									} elseif ($this->input->post('tafectacion',true)[$i]==20 || $this->input->post('tafectacion',true)[$i]==30) {
										$tprecio='01';
										$valoru=$this->input->post("precio",true)[$i];
										$valor=$this->input->post('importe',true)[$i];
										$importe=$this->input->post('importe',true)[$i];
										$igv=0;
									} else {
										$tprecio='02';
										$valoru=0;
										$valor=$this->input->post('importe',true)[$i];
										$igv=round($this->input->post('importe',true)[$i]*0.18,2);
										$importe=0;
									}

									$itemx=array
									(
										'idventa'			=>$insertar,
										'idproducto'	=>$this->input->post('idproducto',true)[$i],
										'descripcion'	=>trim($this->input->post('descripcion',true)[$i]),
										'unidad'			=>$this->input->post("unidad",true)[$i],
										'tafectacion'	=>$this->input->post('tafectacion',true)[$i],
										'cantidad'		=>$this->input->post('cantidad',true)[$i],
										'valor'				=>$valoru,
										'tprecio'			=>$tprecio,
										'total'				=>$valor,
										'igv'					=>$igv,
										'importe'			=>$importe,
										'controlado'  =>$this->input->post('controlado',true)[$i],
									);

									if ($this->input->post('tipo',true)[$i]=='B') {
										$itemx["calmacen"]		=$this->input->post("calmacen",true)[$i];
										$itemx["palmacen"]		=$this->input->post("palmacen",true)[$i];
										$itemx["lote"]				=$this->input->post("nlote",true)[$i];
										$itemx["clote"]				=$this->input->post("clote",true)[$i];
										$itemx["fvencimiento"]=$this->input->post("flote",true)[$i];
									}

									if ($this->input->post('dscto',true)[$i]>0) {
										$msubtotal=$this->input->post('cantidad',true)[$i]*$this->input->post('precio',true)[$i];
										if ($this->input->post('tafectacion',true)[$i]==10) {
											$msubtotal=round($msubtotal/1.18,2);
										}

										if ($this->input->post('tdscto',true)[$i]==0) { //descuento porcentaje
											$fdescuento=$this->input->post('dscto',true)[$i]/100;
											$mdescuento=round($msubtotal*$fdescuento,2);
										} else { //descuento en monto
											if ($this->input->post('tafectacion',true)[$i]==10) {
												$mdescuento=round($this->input->post('dscto',true)[$i]/1.18,2);
											} else {
												$mdescuento=$this->input->post('dscto',true)[$i];
											}
											$fdescuento=round(($mdescuento*100)/($msubtotal*100),4);
										}

                    $descuentos["codigo"]="00";
                    $descuentos["descripcion"]="Descuento Lineal";
                    $descuentos["factor"]=$fdescuento;
                    $descuentos["monto"]=$mdescuento;
                    $descuentos["base"]=$msubtotal;
										$itemx['precio']=$this->input->post('precio',true)[$i]-round($this->input->post('precio',true)[$i]*$fdescuento,2);
                    $itemx["descuentos"]=json_encode($descuentos);
									}else{
										$itemx['precio']=$this->input->post('precio',true)[$i];
									}
									$insertard=$this->ventad_model->insert($itemx);
								}

						    $datat=array("emitido" =>$this->input->post('serie',true).'-'.$numeracion);
								$actualizar=$this->nventa_model->update($datat,array("id"=>$id));

	              $nombrexml = $empresa->ruc.'-'.$comprobante['tcomprobante'].'-'.$comprobante['serie'].'-'.$comprobante['numero'];
						    $mensaje=$this->generadorXml($insertar);

                if (valor_check($this->input->post('formato',true))==1) {
									self::pdfa4($insertar,'I');
								}else{
									$empresa->ticket==80 ? self::pdf80($insertar,'I'): self::pdf58($insertar,'I');
								}
                $impresion=base_url()."downloads/pdf/".$nombrexml.'.pdf';
      					$control_movimiento=$this->movimientos('venta/guardav','Emitio Venta '.$this->input->post("serie",true).'-'.$numeracion);

                if ($empresa->envio_automatico==1) {
                	if ($this->input->post('comprobante',true)=='01') {
                		$mensaje_envios=$this->enviarFactura($insertar);
                	}else{
                		if ($empresa->envio_boleta==1) {
                			$mensaje_envios=$this->enviarFactura($insertar);
                		}
                	}

                	$this->session->set_flashdata('css', $mensaje_envios['color']??'');
  								$this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
                }else{
			          	$this->session->set_flashdata('css', 'success');
									$this->session->set_flashdata('mensaje', $mensaje);
			          }
							} else {
								$mensaje='El comprobante ya existe';
							}
						}
					} else {
						$mensaje='El tipo de comprobante no corresponde con la serie o cliente';
					}
				} else {
					$mensaje='No se puede realizar una venta en negativo!';
				}
			}

			$datos['mensaje']=$mensaje;
			$datos['impresion']=$impresion;
			$datos['url']=base_url().'venta';
			echo json_encode($datos);
			exit();
		}
	}

	public function ventai()
	{
    $controlip=$this->controlip('venta/ventai');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$canexos=$this->establecimiento_model->contador();
		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		if ($arqueoc==0) {redirect(base_url().'venta');}

		$mpagos=$this->tpago_model->mostrarTotal();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$comprobantes=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'03'));
    $toperaciones=$this->toperacion_model->mostrarTotal();
    $codigos=$this->tdetraccion_model->mostrarTotal();
    $medios=$this->tmedio_model->mostrarTotal();
		$bonodiario=$this->nventa_model->bonos(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision"=>date('Y-m-d'),"v.iduser"=>$this->session->userdata("id")));
		$bonomensual=$this->nventa_model->bonos(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>date("Y"),"month(femision)"=>date("n"),"v.iduser"=>$this->session->userdata("id")));
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle('Venta Producto');
		$this->layout->view('ventai',compact("anexos","nestablecimiento",'empresa',"canexos",'mpagos','comprobantes','nserie',"productos","toperaciones","codigos","medios",'bonodiario','bonomensual',"vendedores"));
	}

	public function copias($id)
	{
    $controlip=$this->controlip('venta/copias');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$mpagos=$this->tpago_model->mostrarTotal();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$comprobantes=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'03'));
    $toperaciones=$this->toperacion_model->mostrarTotal();
    $codigos=$this->tdetraccion_model->mostrarTotal();
    $medios=$this->tmedio_model->mostrarTotal();
		$nventa=$this->venta_model->mostrar($id);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($nventa->idcliente);
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Venta Producto");
		$this->layout->view("copias",compact("anexos","nestablecimiento","empresa","mpagos",'comprobantes',"nserie","productos","toperaciones","codigos","medios","nventa","detalles","cliente","vendedores","id"));
	}

	public function guardar($id=NULL)
	{
    $controlip=$this->controlip('venta/guardar');
		if ($this->input->post())
		{
			$impresion='';
			$empresa=$this->empresa_model->mostrar();
			if ($this->input->post('idproducto',true)==null) {
				$mensaje='No envio productos en la venta!';
			} else {
				if ($this->input->post('totalg',true)>0) {
					$pagado=round(array_sum($this->input->post('monto',true)),2);
					if (valor_check($this->input->post('pcredito',true))==0 && $pagado!=floatval($this->input->post('totalg',true))) {
						$mensaje='El monto cobrado es diferente al comprobante';
					}else{
						$tcomprobante=$this->input->post('comprobante',true);
						$serie=SUBSTR($this->input->post('serie',true),0,1);
						$cliente=$this->cliente_model->mostrar($this->input->post('idcliente',true));

						if ($tcomprobante=='01' && $serie=='F' && $cliente->tdocumento==6 || $tcomprobante=='03' && $serie=='B') {
							if ($serie=="B" && $this->input->post('totalg',true)>700 && $cliente->tdocumento==0) {
								$mensaje='El monto a emitir es mayor a S/.700 requiere DNI del cliente';
							}else{
								$numero=$this->venta_model->maximo($this->input->post('serie',true));
								$ninicio= $numero==null ? '' : $numero->numero;
								$numeracion=$ninicio+1;

								$consulta=$this->venta_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
								if ($consulta==0) {
									$comprobante=array
									(
										'idestablecimiento'	=>$this->session->userdata("predeterminado"),
										'iduser'						=>$this->session->userdata('id'),
										"grupo"        		 	=>$this->input->post('comprobante',true)=='01' ? '01' : '02',
										'tipo_soap'         =>$empresa->tipo_soap,
										'femision'					=>date("Y-m-d"),
										'hemision'					=>date('H:i:s'),
										'fvencimiento'			=>date("Y-m-d"),
										'tcomprobante'			=>$this->input->post('comprobante',true),
										'serie'							=>$this->input->post('serie',true),
										'numero'						=>$numeracion,
										'toperacion'				=>$this->input->post('toperacion',true),
										'moneda'						=>'PEN',
										'idcliente'					=>$this->input->post('idcliente',true),
										'cliente'						=>$this->input->post('cliente',true),
										'tgravado'					=>$this->input->post('gravado',true),
										'tinafecto'					=>$this->input->post('inafecto',true),
										'texonerado'				=>$this->input->post('exonerado',true),
										'tgratuito'					=>$this->input->post('gratuito',true),
										'subtotal'					=>$this->input->post('gravado',true)+$this->input->post('inafecto',true)+$this->input->post('exonerado',true),
										'tigv'							=>$this->input->post('igv',true),
										'total'							=>$this->input->post('totalg',true),
										'izipay'						=>valor_fecha($this->input->post('mizipay',true)),
										'lote'							=>valor_check($this->input->post('impresion',true)),
										'dadicional'				=>$this->input->post('dadicional',true),
										'ocompra'						=>$this->input->post('ocompra',true),
										'idvendedor'				=>$this->input->post('vendedor',true),
										'condicion'					=>1,
										'cancelado'					=>1,
										'tipo_estado'				=>'01',
										'efectivo'					=>valor_fecha($this->input->post('efectivo',true)),
										'vuelto'						=>valor_fecha($this->input->post('vuelto',true)),
									);

									if ($this->input->post('mdsctog',true)!='') {
										$msubtotal=$this->input->post('bimponible',true);
										$mdescuento=round($this->input->post('mdsctog',true)/1.18,2);
										$fdescuento=round(($mdescuento*100)/($msubtotal*100),4);

										$descuentos["codigo"]="02";
	                  $descuentos["descripcion"]="Descuentos globales que afectan la base imponible del IGV/IVAP";
	                  $descuentos["factor"]=$fdescuento;
	                  $descuentos["monto"]=$mdescuento;
	                  $descuentos["base"]=$msubtotal;
							      $comprobante["descuentos"]=json_encode($descuentos);
							    }

	                if ($this->input->post('pdetraccion',true)>0) {
	                  $descuentos["codigo"]=$this->input->post('codigo',true);
	                  $descuentos["ncuenta"]=$this->input->post('ncuenta',true);
	                  $descuentos["medio"]=$this->input->post('medio',true);
	                  $descuentos["factor"]=$this->input->post('pdetraccion',true);
	                  $descuentos["monto"]=$this->input->post('mdetraccion',true);

	                  $comprobante["detraccion"]=json_encode($descuentos);
	                }

									if ($this->input->post('pretencion',true)>0) {
										$descuentos["codigo"]="62";
				            $descuentos["descripcion"]="Retencion del IGV";
				            $descuentos["factor"]=round($this->input->post('pretencion',true)/100,4);
				            $descuentos["monto"]=$this->input->post('mretencion',true);
				            $descuentos["base"]=$this->input->post('totalg',true);

				            $comprobante["retencion"]=json_encode($descuentos);
									}
									$insertar=$this->venta_model->insert($comprobante);

									if (valor_check($this->input->post('pcredito',true))==1) {
										$suma=tiempoCuota($this->input->post('pcuota',true));
										$posterior=SumarFecha($suma,date("Y-m-d"));

										$fvencimiento=$posterior;
			      				if ($this->input->post('cuotas',true)>1) {
				      				for ($i=1; $i <= $this->input->post('cuotas',true) ; $i++) {
				      					$suma=tiempoCuota($datos->pcuota);
			      						$fvencimiento=SumarFecha($suma,$fvencimiento);
				      				}
			      				}

										$datac=array
										(
											'condicion'			=>2,
											'cancelado'			=>0,
											'pcuota'				=>$this->input->post('pcuota',true),
											'cuotas'				=>$this->input->post('cuotas',true),
											'mcuota'				=>$this->input->post('mcuota',true),
											'fpago'					=>$posterior,
											'fvencimiento'	=>$fvencimiento,
										);
										$actualizac=$this->venta_model->update($datac,$insertar);
									} else {
										for ($i=0; $i < count($this->input->post('mpago',true)) ; $i++) {
											$datap=array
											(
												'idestablecimiento'	=>$this->session->userdata("predeterminado"),
												'iduser'						=>$this->session->userdata('id'),
												'idventa'						=>$insertar,
												'femision'					=>date("Y-m-d"),
												'idtpago'						=>$this->input->post('mpago',true)[$i],
												'total'							=>$this->input->post('monto',true)[$i],
          							'documento'         =>$this->input->post("documento",true)[$i],
											);
											$insertarp=$this->cobroe_model->insert($datap);
										}
									}

									for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
										if ($this->input->post('tipo',true)[$i]=='B') {
											$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
											$inicalf=$saldos==null ? 0: $saldos->saldof;
											$inicalv=$saldos==null ? 0: $saldos->saldov;
											//costos promedio
											$costo=$inicalf==0 ? 0 : round($inicalv/$inicalf,4);
											$salidav=$this->input->post("almacenc",true)[$i]*$costo;

											$saldof=$inicalf-$this->input->post("almacenc",true)[$i];
											$saldov=$inicalv-$salidav;
											$datak=array
											(
												'idestablecimiento'	=>$this->session->userdata("predeterminado"),
												'iduser'						=>$this->session->userdata('id'),
												'fecha'							=>date('Y-m-d'),
												"idtmovimiento"			=>1,
												'concepto'					=>'Venta',
												'idproducto'				=>$this->input->post("idproducto",true)[$i],
												'descripcion'				=>trim($this->input->post("descripcion",true)[$i]),
												'salidaf'						=>$this->input->post("almacenc",true)[$i],
												'saldof'						=>$saldof,
												'costo'							=>$costo,
												'salidav'						=>$salidav,
												'saldov'						=>$saldov,
												'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
											);
											$insertark=$this->kardex_model->insert($datak);

											$datas=array('stock'=>$saldof);
											$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

											$nlotes='';
											$clotes='';
											$flotes='';
											if ($this->input->post("lote",true)[$i]==1) {
												$cantidad=$this->input->post("almacenc",true)[$i];

												$nlotes=array();
												$clotes=array();
												$flotes=array();
												if ($this->input->post("nlote",true)[$i]!='') {
													$nlote1=explode(",",$this->input->post("nlote",true)[$i]);
													for ($l=0; $l < count($nlote1) ; $l++) {
														$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$nlote1[$l]));
														$ncantidad=$cantidad-$consultal->stock;	//nueva cantidad
														$saldoc=$consultal->stock-$cantidad;	//saldo a guardar

														if ($saldoc>0) {
															$datal=array('stock'=>$saldoc);
															$actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlote1[$l]);
														} else {
															$elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$nlote1[$l]));
														}

														if ($consultal->stock<$cantidad) {
															$inicialf=$consultal->stock;
														} else {
															$inicialf=$cantidad;
														}

														$saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlote1[$l]);
														$inicial=$saldos==null ? 0: $saldos->saldof;
														$saldosl=$inicial-$inicialf;
														$datac=array
														(
															'idestablecimiento'	=>$this->session->userdata("predeterminado"),
															'iduser'    				=>$this->session->userdata('id'),
															'fecha'							=>date('Y-m-d'),
															'idtmovimiento'			=>1,
															'concepto'					=>'Venta',
															'idproducto'  			=>$this->input->post('idproducto',true)[$i],
								        			'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
															'nlote'							=>$nlote1[$l],
															'salidaf'						=>$inicialf,
															'saldof'						=>$saldosl,
															'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
														);
														$insertarc=$this->kardexl_model->insert($datac);

									          array_push($nlotes,$consultal->nlote);
														array_push($clotes,$inicialf);
														array_push($flotes,$consultal->fvencimiento);

														$cantidad=$ncantidad;
														if ($cantidad<=0) {break;}
													}
												} else {
													for ($l=0; $l < 3 ; $l++) {
									          $consultal=$this->lote_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i]);

									          if ($cantidad>=$consultal->stock) {
									            $elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$consultal->nlote));
									            $inicialf=$consultal->stock;
									          } else {
									            $datal=array('stock'=>$consultal->stock-$cantidad);
									            $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
									            $inicialf=$cantidad;
									          }

														$saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$consultal->nlote);
														$inicial=$saldos==null ? 0: $saldos->saldof;
														$saldosl=$inicial-$inicialf;
														$datac=array
														(
															'idestablecimiento'	=>$this->session->userdata("predeterminado"),
															'iduser'    				=>$this->session->userdata('id'),
															'fecha'							=>date('Y-m-d'),
															'idtmovimiento'			=>1,
															'concepto'					=>'Venta',
															'idproducto'  			=>$this->input->post('idproducto',true)[$i],
								        			'descripcion' 			=>trim($this->input->post('descripcion',true)[$i]),
															'nlote'							=>$consultal->nlote,
															'salidaf'						=>$inicialf,
															'saldof'						=>$saldosl,
															'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
														);
														$insertarc=$this->kardexl_model->insert($datac);

									          array_push($nlotes,$consultal->nlote);
									          array_push($clotes,$inicialf);
									          array_push($flotes,$consultal->fvencimiento);

									          $cantidad-=$consultal->stock;
														if ($cantidad<=0) {break;}
									        }
												}

												$nlotes=implode('|', $nlotes);
												$clotes=implode('|', $clotes);
												$flotes=implode('|', $flotes);
											}
										}

					  					/*----------  conversion tipo afectacion  ----------*/
										if ($this->input->post('tafectacion',true)[$i]==10) {
											$tprecio='01';
											$valoru=round($this->input->post('precio',true)[$i]/1.18,6);
											$valor=round($this->input->post('importe',true)[$i]/1.18,2);
											$igv=round(($this->input->post('importe',true)[$i]*0.18)/1.18,2);
											$importe=$this->input->post('importe',true)[$i];
										} elseif ($this->input->post('tafectacion',true)[$i]==20 || $this->input->post('tafectacion',true)[$i]==30) {
											$tprecio='01';
											$valoru=$this->input->post("precio",true)[$i];
											$valor=$this->input->post('importe',true)[$i];
											$igv=0;
											$importe=$this->input->post('importe',true)[$i];
										} else {
											$tprecio='02';
											$valoru=0;
											$valor=$this->input->post('importe',true)[$i];
											$igv=round($this->input->post('importe',true)[$i]*0.18,2);
											$importe=0;
										}

										$itemx=array
										(
											'idventa'			=>$insertar,
											'idproducto'	=>$this->input->post('idproducto',true)[$i],
											'descripcion'	=>trim($this->input->post('descripcion',true)[$i]),
											'unidad'			=>$this->input->post("unidad",true)[$i],
											'tafectacion'	=>$this->input->post('tafectacion',true)[$i],
											'cantidad'		=>$this->input->post('cantidad',true)[$i],
											'valor'				=>$valoru,
											'tprecio'			=>$tprecio,
											'total'				=>$valor,
											'igv'					=>$igv,
											'importe'			=>$importe,
										);

										if ($this->input->post('tipo',true)[$i]=='B') {
											$itemx["calmacen"]		=$this->input->post("almacenc",true)[$i];
											$itemx["palmacen"]		=$costo;
											$itemx["lote"]				=$nlotes;
											$itemx["clote"]				=$clotes;
											$itemx["fvencimiento"]=$flotes;
										}

										if ($this->input->post('dscto',true)[$i]>0) {
											$msubtotal=$this->input->post('cantidad',true)[$i]*$this->input->post('precio',true)[$i];
											if ($this->input->post('tafectacion',true)[$i]==10) {
												$msubtotal=round($msubtotal/1.18,2);
											}

											if ($this->input->post('tdscto',true)[$i]==0) { //descuento porcentaje
												$fdescuento=$this->input->post('dscto',true)[$i]/100;
												$mdescuento=round($msubtotal*$fdescuento,2);
											} else { //descuento en monto
												if ($this->input->post('tafectacion',true)[$i]==10) {
													$mdescuento=round($this->input->post('dscto',true)[$i]/1.18,2);
												} else {
													$mdescuento=$this->input->post('dscto',true)[$i];
												}
												$fdescuento=round(($mdescuento*100)/($msubtotal*100),4);
											}

	                    $descuentos["codigo"]="00";
	                    $descuentos["descripcion"]="Descuento Lineal";
	                    $descuentos["factor"]=$fdescuento;
	                    $descuentos["monto"]=$mdescuento;
	                    $descuentos["base"]=$msubtotal;
											$itemx['precio']=$this->input->post('precio',true)[$i]-round($this->input->post('precio',true)[$i]*$fdescuento,2);
	                    $itemx["descuentos"]=json_encode($descuentos);
										}else{
											$itemx['precio']=$this->input->post('precio',true)[$i];
										}

										if ($this->input->post('doctor',true)[$i]!='') {
											$receta["paciente"]=$this->input->post("paciente",true)[$i];
								      $receta["colegiatura"]=$this->input->post("colegiatura",true)[$i];
								      $receta["doctor"]=$this->input->post("doctor",true)[$i];
								      $itemx["controlado"]=json_encode($receta);
								    }
										$insertard=$this->ventad_model->insert($itemx);
									}

									if ($empresa->spuntos==1 && $this->input->post("idcliente",true)>1 && $this->input->post("tdocumento",true)!=6) {
										$vpuntos=$this->punto_model->mostrar();
							      $punto_acumulado = intval($this->input->post("totalg",true)/$vpuntos->valorp);

										$datap=array
										(
											"idventa"		=>$insertar,
											"idcliente"	=>$this->input->post("idcliente",true),
											"femision"	=>date("Y-m-d"),
											"inicial"		=>$punto_acumulado,
											"cantidad"	=>$punto_acumulado,
										);
										$insertarp=$this->clientep_model->insert($datap);
									}

									if ($this->input->post('mdsctog',true)!='' && $this->input->post('nvale',true)!='') {
										$idvale=explode("&",$this->input->post("nvale",true));
										$datal=array(
											"estado"=>0,
							        "fcanje"=>date("Y-m-d"),
							      );
										$actualizarl=$this->vale_model->update($datal,$idvale[0]);
									}

							    if ($id!=NULL) {
										$datat=array(
					            "estado"  =>2,
					            "emitido" =>$this->input->post('serie',true).'-'.$numeracion,
					          );
										$actualizar=$this->cotizacion_model->update($datat,array("id"=>$id));
							    }

		              $nombrexml = $empresa->ruc.'-'.$comprobante['tcomprobante'].'-'.$comprobante['serie'].'-'.$comprobante['numero'];
							    $mensaje=$this->generadorXml($insertar);

	                if (valor_check($this->input->post('formato',true))==1) {
										self::pdfa4($insertar,'I');
									}else{
										$empresa->ticket==80 ? self::pdf80($insertar,'I'): self::pdf58($insertar,'I');
									}
	                $impresion=base_url()."downloads/pdf/".$nombrexml.'.pdf';
      						$control_movimiento=$this->movimientos('venta/guardar','Emitio Venta '.$this->input->post("serie",true).'-'.$numeracion);

	                if ($empresa->envio_automatico==1) {
	                	if ($this->input->post('comprobante',true)=='01') {
	                		$mensaje_envios=$this->enviarFactura($insertar);
	                	}else{
	                		if ($empresa->envio_boleta==1) {
	                			$mensaje_envios=$this->enviarFactura($insertar);
	                		}
	                	}

	                	$this->session->set_flashdata('css', $mensaje_envios['color']??'');
    								$this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
	                }else{
				          	$this->session->set_flashdata('css', 'success');
										$this->session->set_flashdata('mensaje', $mensaje);
				          }
								} else {
									$mensaje='El comprobante ya existe';
								}
							}
						} else {
							$mensaje='El tipo de comprobante no corresponde con la serie o cliente';
						}
					}
				} else {
					$mensaje='No se puede realizar una venta en negativo!';
				}
			}

			$datos['mensaje']=$mensaje;
			$datos['impresion']=$impresion;
			$datos['url']=base_url().'venta/ventai';
			echo json_encode($datos);
			exit();
		}
	}

	public function generar($id)
	{
    $empresa=$this->empresa_model->mostrar();
    $mensaje=$this->generadorXml($id);
    $empresa->ticket==80 ? self::pdf80($id,'I'): self::pdf58($id,'I');

    $comprobante=$this->venta_model->mostrar($id);
		$nombrexml = $empresa->ruc.'-'.$comprobante->tcomprobante.'-'.$comprobante->serie.'-'.$comprobante->numero;
    $impresion=base_url()."downloads/pdf/".$nombrexml.'.pdf';

    if ($empresa->envio_automatico==1) {
    	if ($this->input->post('comprobante',true)=='01') {
    		$mensaje_envios=$this->enviarFactura($id);
    	}else{
    		if ($empresa->envio_boleta==1) {
    			$mensaje_envios=$this->enviarFactura($id);
    		}
    	}

    	$this->session->set_flashdata('css', $mensaje_envios['color']??'');
			$this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
    }else{
    	$this->session->set_flashdata('css', 'success');
			$this->session->set_flashdata('mensaje', $mensaje);
    }

		$this->layout->setLayout("blanco");
		$this->layout->view("generar",compact("impresion"));
	}

  public function emisor()
  {
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $datos=array
    (
      'ruc'           =>$empresa->ruc,
      'nombres'       =>$empresa->nombres,
      'ncomercial'    =>$empresa->ncomercial,
      'codigo'        =>$nestablecimiento->codigo,
      'ndepartamento' =>$nestablecimiento->ndepartamento,
      'nprovincia'    =>$nestablecimiento->nprovincia,
      'ndistrito'     =>$nestablecimiento->ndistrito,
      'distrito'      =>$nestablecimiento->iddistrito,
      'direccion'     =>$nestablecimiento->direccion,
    );
    return $datos;
  }

  public function itemsVenta($id)
  {
    $detalles=$this->ventad_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      $itemx=array
      (
        'idventa'         =>$id,
        'idproducto'      =>$detalle->idproducto,
        'descripcion'     =>$detalle->descripcion,
        'unidad'          =>$detalle->unidad,
        'cantidad'        =>$detalle->cantidad,
        'valor'           =>$detalle->valor,
        'tafectacion'     =>$detalle->tafectacion,
        'tprecio'         =>$detalle->tprecio,
        'precio'          =>$detalle->precio,
        'total'           =>$detalle->total,
        'igv'             =>$detalle->igv,
        'importe'         =>$detalle->importe,
        'descuentos'      =>$detalle->descuentos,
      );

      if ($detalle->tafectacion == 10) {
        $itemx['codigo_tributo']=array("S","1000","IGV","VAT");
      }
      elseif ($detalle->tafectacion == 20) {
        $itemx['codigo_tributo']=array("E","9997","EXO","VAT");
      }
      elseif ($detalle->tafectacion == 30) {
        $itemx['codigo_tributo']=array("O","9998","INA","FRE");
      }
      elseif ($detalle->tafectacion == 40) {
        $itemx['codigo_tributo']=array("G","9995","EXP","FRE");
      }
      else{
        $itemx['codigo_tributo']=array("Z","9996","GRA","FRE");
      }
      $datos[]=$itemx;
    }
    return $datos;
  }

  public function generadorXml($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->venta_model->mostrar($id);
    $comprobante=array
    (
      'femision'          =>$datos->femision,
      'hemision'          =>$datos->hemision,
      'fvencimiento'      =>$datos->fvencimiento,
      'tcomprobante'      =>$datos->tcomprobante,
      'serie'             =>$datos->serie,
      'numero'            =>$datos->numero,
      'toperacion'        =>$datos->toperacion,
      'moneda'            =>$datos->moneda,
      'tgravado'          =>$datos->tgravado,
      'texonerado'        =>$datos->texonerado,
      'tinafecto'         =>$datos->tinafecto,
      'tgratuito'					=>$datos->tgratuito,
      'subtotal'          =>$datos->subtotal,
      'tigv'              =>$datos->tigv,
      'total'           	=>$datos->total,
      'condicion'    			=>$datos->condicion,
      'descuentos'      	=>$datos->descuentos,
      'detraccion'        =>$datos->detraccion,
      'retencion'        	=>$datos->retencion,
    );

    if ($datos->condicion==2) {
			$comprobante['cuotas']=$datos->cuotas;
			$comprobante['pcuota']=$datos->pcuota;
			$comprobante['mcuota']=$datos->mcuota;
			$comprobante['fpago']=$datos->fpago;
    }

    $nombrexml = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $ruta_xml = "downloads/xml/".$nombrexml;
    $emisor=$this->emisor();
    $cliente=$this->cliente_model->mostrar($datos->idcliente);
    $detalle=$this->itemsVenta($id);
    $this->generadoXML->CrearXMLFactura($ruta_xml, $emisor, $cliente, $comprobante, $detalle);

    $ruta_certificado = "downloads/certificado/".$empresa->certificado;
    $hash = $this->firmadoXML->FirmarDocumento($ruta_xml,$ruta_certificado,$empresa->certificado_clave);

    $datav=array
    (
      'filename'  =>$nombrexml,
      'hash'      =>$hash,
      'has_xml'   =>1,
      'has_pdf'   =>1,
    );
    $actualizar=$this->venta_model->update($datav,$id);
    return 'Se genero comprobante '.$datos->serie.'-'.$datos->numero;
  }

	public function consulta($id)
	{
		$datos=$this->venta_model->mostrar($id);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$this->layout->setLayout("blanco");
		$this->layout->view("consulta",compact("datos","detalles"));
	}

	public function opciones($id)
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("opciones",compact("id"));
	}

	public function pdfa4($id,$tipo=null)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->venta_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$cobros=$this->cobroe_model->mostrarTotal(array("idventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$vendedor=$this->usuario_model->mostrar($datos->idvendedor);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa4",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","vendedor","tipo"));
	}

	public function pdf80($id,$tipo=null)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->venta_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$cobros=$this->cobroe_model->mostrarTotal(array("idventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$vendedor=$this->usuario_model->mostrar($datos->idvendedor);
		$tpuntos=$this->clientep_model->cantidadTotal($datos->idcliente);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf80",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","vendedor","tipo","tpuntos"));
	}

	public function pdf58($id,$tipo=null)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->venta_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$cobros=$this->cobroe_model->mostrarTotal(array("idventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$vendedor=$this->usuario_model->mostrar($datos->idvendedor);
		$tpuntos=$this->clientep_model->cantidadTotal($datos->idcliente);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf58",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","vendedor","tipo","tpuntos"));
	}

	public function pdfa5($id)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->venta_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->ventad_model->mostrarTotal($id);
		$cobros=$this->cobroe_model->mostrarTotal(array("idventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$vendedor=$this->usuario_model->mostrar($datos->idvendedor);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa5",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","vendedor"));
	}

	public function enviarFactura($id)
  {
    $controlip=$this->controlip('venta/enviarFactura');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->venta_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->EnviarComprobanteElectronico($empresa,$nombre,$rutazip,$rutacdr);
    if (!is_numeric($resultado['codigo'])) {
      $mensaje['color']='danger';
    } elseif (obtenerNumero($resultado['codigo'])===0) {
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"05",
        "respuesta_sunat" =>$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $mensaje['color']='success';
    } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
      $data=array
      (
        "rectificar"   =>1,
        "respuesta_rectificar" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $mensaje['color']='info';
    } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"09",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $devolver=$this->devolucionv($id);
      $mensaje['color']='info';
    } else {// 4000 en adelante Observaciones
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"07",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $mensaje['color']='success';
    }

    $mensaje['mensaje']=$resultado['mensaje'];
    return $mensaje;
  }

	/*===================================================================================================================
	=                                                    notas comprobantes                                             =
	===================================================================================================================*/
	public function ncredito()
	{
    $controlip=$this->controlip('venta/ncredito');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date('Y-m-d')) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"));
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		if ($empresa->lventa==1) {$filtros["femision>="]=$inicio; $filtros["femision<="]=$fin;}
		$listas=$empresa->lventa==1 ? $this->nota_model->mostrarTotal($filtros,"desc"): $this->nota_model->mostrarLimite($filtros,"desc");

		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		$this->layout->setTitle('Notas Credito');
		$this->layout->view('ncredito',compact("anexos","nestablecimiento",'empresa','listas','inicio','fin','empresa','arqueoc'));
	}

  public function ncreditoi($id)
  {
    $controlip=$this->controlip('venta/ncreditoi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

  	$empresa=$this->empresa_model->mostrar();
  	$datos=$this->venta_model->mostrar($id);
  	$detalles=$this->ventad_model->mostrarTotal($id);
  	$cobros=$this->cobroe_model->mostrarTotal(array("idventa"=>$id));

  	$tcreditos=$this->tcredito_model->mostrarTotal();
    $documento=substr($datos->serie,0,1);
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),'tcomprobante'=>'07','SUBSTR(serie,1,1)'=>$documento));
		$existentes=$this->nota_model->mostrarTotal(array('idventa'=>$id,'tcomprobante'=>'07'),"desc");
		$this->layout->setTitle('Nota Credito');
		$this->layout->view('ncreditoi',compact("anexos","nestablecimiento",'empresa','nserie','tcreditos','datos','detalles',"cobros",'existentes','id'));
  }

  public function guardan($id)
  {
    $controlip=$this->controlip('venta/guardan');
  	if ($this->input->post())
		{
			$impresion='';
			$empresa=$this->empresa_model->mostrar();
			if ($this->input->post('idproducto',true)==null) {
				$mensaje='No envio productos en la nota!';
			} else {
				$numero=$this->nota_model->maximo($this->input->post('serie',true));
				$ninicio= $numero==null ? '' : $numero->numero;
				$numeracion=$ninicio+1;

				$consulta=$this->nota_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
				if ($consulta==0) {
					$comprobante=array
					(
						'idestablecimiento'	=>$this->session->userdata("predeterminado"),
						'iduser'						=>$this->session->userdata('id'),
						"grupo"         		=>$this->input->post('vcomprobante',true)=='01' ? '01' : '02',
						'tipo_soap'     		=>$empresa->tipo_soap,
						'femision'					=>$this->input->post('fecha',true),
						'hemision'					=>date('H:i:s'),
						'tcomprobante'			=>'07',
						'serie'							=>$this->input->post('serie',true),
						'numero'						=>$numeracion,
						'moneda'						=>'PEN',
						'tnota'							=>$this->input->post('tnota',true),
						'motivo'						=>$this->input->post('motivo',true),
						'idcliente'					=>$this->input->post('idcliente',true),
						'cliente'						=>$this->input->post('cliente',true),
						'tgravado'					=>$this->input->post('gravado',true),
						'tinafecto'					=>$this->input->post('inafecto',true),
						'texonerado'				=>$this->input->post('exonerado',true),
						'tgratuito'					=>$this->input->post('gratuito',true),
						'subtotal'					=>$this->input->post('gravado',true)+$this->input->post('inafecto',true)+$this->input->post('exonerado',true),
						'tigv'							=>$this->input->post('igv',true),
						'total'							=>$this->input->post('totalg',true),
						'idventa'						=>$id,
						'tipo_estado'				=>'01',
					);
					$insertar=$this->nota_model->insert($comprobante);

					if ($this->input->post('tpago',true)==1) {
						for ($i=0; $i < count($this->input->post('mpago',true)) ; $i++) {
							$datap=array
							(
								'idestablecimiento'	=>$this->session->userdata("predeterminado"),
								'iduser'						=>$this->session->userdata('id'),
								'idnota'						=>$insertar,
								'femision'					=>date('Y-m-d'),
								'idtpago'						=>$this->input->post('mpago',true)[$i],
								'total'							=>'-'.$this->input->post('monto',true)[$i],
							);
							$insertarp=$this->cobron_model->insert($datap);
						}
					}else{
						$datap=array('cancelado'=>1);
						$actualizac=$this->venta_model->update($datap,$id);
					}

					for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
						if ($this->input->post('tipo',true)[$i]=='B') {
							$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
							$inicalf=$saldos==null ? 0: $saldos->saldof;
							$inicalv=$saldos==null ? 0: $saldos->saldov;
							//costos promedio
							$salidav=$this->input->post('almacenc',true)[$i]*$this->input->post('almacenp',true)[$i];

							$saldof=$inicalf+$this->input->post('almacenc',true)[$i];
							$saldov=$inicalv+$salidav;
							$datak=array
							(
								'idestablecimiento'	=>$this->session->userdata("predeterminado"),
								'iduser'						=>$this->session->userdata('id'),
								'fecha'							=>date('Y-m-d'),
								"idtmovimiento"			=>1,
								'concepto'					=>'Nota de Credito',
								'idproducto'				=>$this->input->post('idproducto',true)[$i],
								'descripcion'				=>trim($this->input->post('descripcion',true)[$i]),
								'entradaf'					=>$this->input->post('almacenc',true)[$i],
								'saldof'						=>$saldof,
								'costo'							=>$this->input->post('almacenp',true)[$i],
								'entradav'					=>$salidav,
								'saldov'						=>$saldov,
								'documento'					=>$this->input->post('serie',true).'-'.$numeracion,
							);
							$insertark=$this->kardex_model->insert($datak);

							$datas=array('stock'=>$saldof);
							$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

							$nlotes='';
							$flotes='';
							$clotes='';
							if ($this->input->post('lote',true)[$i]!='') {
								$cantidad=$this->input->post('almacenc',true)[$i];

								$nlotes=array();
								$flotes=array();
								$clotes=array();

								$nlote1=explode('|',$this->input->post('lote',true)[$i]);
								$flote1=explode('|',$this->input->post('fvencimiento',true)[$i]);
								$clote1=explode('|',$this->input->post('clote',true)[$i]);

								for ($l=0; $l < count($nlote1) ; $l++) {
									$ncantidad=$cantidad-$clote1[$l]; //nueva cantidad

									if ($clote1[$l]<$cantidad) {
										$inicialf=$clote1[$l];
									} else {
										$inicialf=$cantidad;
									}

									$consultal=$this->lote_model->mostrar(array('idestablecimiento'=>$this->session->userdata("predeterminado"),'idproducto'=>$this->input->post('idproducto',true)[$i],'nlote'=>$nlote1[$l]));
									if ($consultal==null) {
	                  $datal=array
	                  (
											'idestablecimiento'	=>$this->session->userdata("predeterminado"),
	                    'idproducto'  			=>$this->input->post('idproducto',true)[$i],
	                    'nlote'       			=>$nlote1[$l],
	                    'fvencimiento'			=>valor_fecha($flote1[$l]),
	                    'inicial'     			=>$inicialf,
	                    'stock'       			=>$inicialf,
	                  );
	                  $insertarl=$this->lote_model->insert($datal);
	                } else {
	                  $datal=array('stock'=>$consultal->stock+$inicialf);
	                  $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$nlote1[$l]);
	                }

									$saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlote1[$l]);
									$inicial=$saldos==null ? 0: $saldos->saldof;
									$saldosl=$inicial+$inicialf;
									$datac=array
									(
										'idestablecimiento'	=>$this->session->userdata("predeterminado"),
										"iduser"    				=>$this->session->userdata('id'),
										"fecha"							=>date("Y-m-d"),
										"idtmovimiento"				=>1,
										"concepto"					=>"Nota de Credito",
										"idproducto"				=>$this->input->post("idproducto",true)[$i],
										"descripcion"				=>trim($this->input->post("descripcion",true)[$i]),
										"nlote"							=>$nlote1[$l],
										"entradaf"					=>$inicialf,
										"saldof"						=>$saldosl,
										"documento"					=>$this->input->post("serie",true)."-".$numeracion,
									);
									$insertarc=$this->kardexl_model->insert($datac);

									array_push($nlotes,$nlote1[$l]);
									array_push($flotes,$flote1[$l]);
									array_push($clotes,$inicialf);
									$cantidad=$ncantidad;

									if ($ncantidad==0) {break;}
								}

								$nlotes=implode('|', $nlotes);
								$clotes=implode('|', $clotes);
								$flotes=implode('|', $flotes);
							}
						}

						if ($this->input->post('tafectacion',true)[$i]==10) {
							$valoru=round($this->input->post('precio',true)[$i]/1.18,6);
              $valor=round($this->input->post('importe',true)[$i]/1.18,2);
              $igv=round(($this->input->post('importe',true)[$i]*0.18)/1.18,2);
						} elseif ($this->input->post('tafectacion',true)[$i]==20 || $this->input->post('tafectacion',true)[$i]==30) {
							$valoru=$this->input->post('precio',true)[$i];
              $valor=$this->input->post('importe',true)[$i];
              $igv=0;
						} else {
							$valoru=0;
              $valor=$this->input->post('cantidad',true)[$i]*$this->input->post('precio',true)[$i];
              $igv=round($valor*0.18,2);
						}

						$itemx=array
						(
							'idnota'			=>$insertar,
							'idproducto'	=>$this->input->post('idproducto',true)[$i],
							'descripcion'	=>trim($this->input->post('descripcion',true)[$i]),
							'unidad'			=>$this->input->post('unidad',true)[$i],
							'tafectacion'	=>$this->input->post('tafectacion',true)[$i],
							'cantidad'		=>$this->input->post('cantidad',true)[$i],
							'valor'				=>$valoru,
							'tprecio'			=>$this->input->post('tprecio',true)[$i],
							'precio'			=>$this->input->post('precio',true)[$i],
							'total'				=>$valor,
							'igv'					=>$igv,
							'importe'			=>$this->input->post('importe',true)[$i],
						);

						if ($this->input->post('tipo',true)[$i]=='B') {
							$itemx['calmacen']		=$this->input->post('almacenc',true)[$i];
							$itemx['palmacen']		=$this->input->post('almacenp',true)[$i];
							$itemx['lote']				=valor_fecha($nlotes);
							$itemx['fvencimiento']=valor_fecha($flotes);
							$itemx['clote']				=$clotes;
						}
						$insertard=$this->notad_model->insert($itemx);
					}

					$datat=array("estado" =>1,"emitido" =>NULL);
          $actualizar=$this->cotizacion_model->update($datat,array("emitido"=>$this->input->post('vnumero',true)));

					$nombrexml = $empresa->ruc.'-'.$comprobante['tcomprobante'].'-'.$comprobante['serie'].'-'.$comprobante['numero'];
			    $mensaje=$this->generadonXml($insertar);

	        self::pdfnticket($insertar);
	        $impresion=base_url()."downloads/pdf/".$nombrexml.'.pdf';
	        $control_movimiento=$this->movimientos('venta/guardan','Emitio Nota Credito '.$this->input->post("serie",true).'-'.$numeracion);

			    if ($empresa->envio_automatico==1) {
          	if ($this->input->post('vcomprobante',true)=='01') {
          		$mensaje_envios=$this->enviarNota($insertar);
          	}else{
          		if ($empresa->envio_boleta==1) {
          			$mensaje_envios=$this->enviarNota($insertar);
          		}
          	}

          	$this->session->set_flashdata('css', $mensaje_envios['color']??'');
						$this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
          }else{
          	$this->session->set_flashdata('css', 'success');
						$this->session->set_flashdata('mensaje', $mensaje);
          }
				} else {
					$mensaje='El comprobante ya existe';
				}
			}

			$datos['mensaje']=$mensaje;
			$datos['impresion']=$impresion;
			$datos['url']=base_url().'venta/ncredito';
			echo json_encode($datos);
			exit();
		}
  }

  public function itemsNota($id)
  {
    $detalles=$this->notad_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      $itemx=array
      (
        'idnota'         	=>$id,
        'idproducto'      =>$detalle->idproducto,
        'descripcion'     =>$detalle->descripcion,
        'unidad'          =>$detalle->unidad,
        'cantidad'        =>$detalle->cantidad,
        'valor'           =>$detalle->valor,
        'tafectacion'     =>$detalle->tafectacion,
        'tprecio'         =>$detalle->tprecio,
        'precio'          =>$detalle->precio,
        'total'           =>$detalle->total,
        'igv'             =>$detalle->igv,
        'importe'         =>$detalle->importe,
      );

      if ($detalle->tafectacion == 10) {
        $itemx['codigo_tributo']=array("S","1000","IGV","VAT");
      }
      elseif ($detalle->tafectacion == 20) {
        $itemx['codigo_tributo']=array("E","9997","EXO","VAT");
      }
      elseif ($detalle->tafectacion == 30) {
        $itemx['codigo_tributo']=array("O","9998","INA","FRE");
      }
      elseif ($detalle->tafectacion == 40) {
        $itemx['codigo_tributo']=array("G","9995","EXP","FRE");
      }
      else{
        $itemx['codigo_tributo']=array("Z","9996","GRA","FRE");
      }
      $datos[]=$itemx;
    }
    return $datos;
  }

  public function generadonXml($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->nota_model->mostrar($id);
    $venta=$this->venta_model->mostrar($datos->idventa);
    $comprobante=array
    (
      'femision'          =>$datos->femision,
      'hemision'          =>$datos->hemision,
      'tcomprobante'      =>$datos->tcomprobante,
      'serie'             =>$datos->serie,
      'numero'            =>$datos->numero,
      'moneda'            =>$datos->moneda,
      'tnota'        			=>$datos->tnota,
      'motivo'    				=>$datos->motivo,
      'tgravado'          =>$datos->tgravado,
      'texonerado'        =>$datos->texonerado,
      'tinafecto'         =>$datos->tinafecto,
      'tgratuito'         =>$datos->tgratuito,
      'subtotal'          =>$datos->subtotal,
      'tigv'              =>$datos->tigv,
      'total'           	=>$datos->total,
      'treferencia'   		=>$venta->tcomprobante,
      'referencia'        =>$venta->serie.'-'.$venta->numero,
    );

    $nombrexml = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $ruta_xml = "downloads/xml/".$nombrexml;
    $emisor=$this->emisor();
    $cliente=$this->cliente_model->mostrar($datos->idcliente);
    $detalle=$this->itemsNota($id);
    $this->generadoXML->CrearXMLNotaCredito($ruta_xml, $emisor, $cliente, $comprobante, $detalle);

    $ruta_certificado = "downloads/certificado/".$empresa->certificado;
    $hash = $this->firmadoXML->FirmarDocumento($ruta_xml,$ruta_certificado,$empresa->certificado_clave);

    $datav=array
    (
      'filename'  =>$nombrexml,
      'hash'      =>$hash,
      'has_xml'   =>1,
      'has_pdf'   =>1,
    );
    $actualizar=$this->nota_model->update($datav,$id);
    return 'Se genero comprobante '.$datos->serie.'-'.$datos->numero;
  }

	public function pdfnticket($id)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nota_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->notad_model->mostrarTotal($id);
		$docafectado=$this->venta_model->mostrar($datos->idventa);
		$tiponota=$datos->tcomprobante=='07' ? $this->tcredito_model->mostrar($datos->tnota) : $this->tdebito_model->mostrar($datos->tnota) ;
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfnticket",compact("empresa","nestablecimiento","datos","cliente","detalles","docafectado","tiponota","usuario"));
	}

	public function pdfnformato($id)
	{
		$nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nota_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->notad_model->mostrarTotal($id);
		$docafectado=$this->venta_model->mostrar($datos->idventa);
		$tiponota=$datos->tcomprobante=='07' ? $this->tcredito_model->mostrar($datos->tnota) : $this->tdebito_model->mostrar($datos->tnota) ;
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfnformato",compact("empresa","nestablecimiento","datos","cliente","detalles","docafectado","tiponota","usuario"));
	}

	public function consultan($id)
	{
		$datos=$this->nota_model->mostrar($id);
		$detalles=$this->notad_model->mostrarTotal($id);
		$this->layout->setLayout("blanco");
		$this->layout->view("consultan",compact("datos","detalles"));
	}

	public function enviarNota($id)
  {
    $controlip=$this->controlip('venta/enviarNota');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->nota_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->EnviarComprobanteElectronico($empresa,$nombre,$rutazip,$rutacdr);
    if (!is_numeric($resultado['codigo'])) {
      $mensaje['color']='danger';
    } elseif (obtenerNumero($resultado['codigo'])===0) {
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"05",
        "respuesta_sunat" =>$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $mensaje['color']='success';
    } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
      $data=array
      (
        "rectificar"   =>1,
        "respuesta_rectificar" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $mensaje['color']='info';
    } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"09",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $devolver=$this->devolucionn($id);
      $mensaje['color']='info';
    } else {// 4000 en adelante Observaciones
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"07",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $mensaje['color']='success';
    }

    $mensaje['mensaje']=$resultado['mensaje'];
    return $mensaje;
  }

	/*====================================================================================================================
	=                                              anulaciones de documentos                                             =
	====================================================================================================================*/
  public function anulacion($id)
  {
    $controlip=$this->controlip('venta/anulacion');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->venta_model->mostrar($id);
    if ($datos->tipo_estado<'09' && $datos->nulo==0) {
      $datav=array("nulo"=>1);
      $actualizar=$this->venta_model->update($datav,$id);
      $elimnarp=$this->clientep_model->delete(array("idventa"=>$id));

      $datat=array("emitido"=>NULL);
      $actualizar=$this->nventa_model->update($datat,array("emitido"=>$datos->serie.'-'.$datos->numero));

      $datat=array("estado"	=>1,"emitido"	=>NULL);
      $actualizar=$this->cotizacion_model->update($datat,array("emitido"=>$datos->serie.'-'.$datos->numero));

      $devolver=$this->devolucionv($id);
	    $control_movimiento=$this->movimientos('venta/anulacion','Anulo venta '.$datos->serie.'-'.$datos->numero);

      if ($empresa->envio_automatico==1 && $datos->tipo_estado=='05') {
      	if ($datos->grupo=='01') {
      		$mensaje_envios=$this->enviarAnulado($id,$datos);
      	} else {
      		$mensaje_envios=$this->enviarAnuladob($id,$datos);
      	}

      	$this->session->set_flashdata('css', $mensaje_envios['color']??'');
				$this->session->set_flashdata('mensaje', $mensaje_envios['mensaje']??'');
      }

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
    $proceso['url']=base_url().'venta';
    echo json_encode($proceso);
    exit();
  }

	public function devolucionv($id)
	{
		/*----------  devolucion de venta  ----------*/
		$datos=$this->venta_model->mostrar($id);
		$detalles=$this->ventad_model->mostrarTotal($id);
		foreach ($detalles as $detalle) {
			$productos=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));

			if ($productos->tipo=='B') {
				$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));
				$inicalf=$saldos==null ? 0: $saldos->saldof;
				$inicalv=$saldos==null ? 0: $saldos->saldov;
				//costos promedio
				$salidav=$detalle->calmacen*$detalle->palmacen;

				$saldof=$inicalf+$detalle->calmacen;
				$saldov=$inicalv+$salidav;
				$datak=array
				(
					"idestablecimiento"	=>$datos->idestablecimiento,
					"iduser"						=>$this->session->userdata('id'),
					"fecha"							=>date("Y-m-d"),
					"idtmovimiento"			=>1,
					"concepto"					=>"Anulacion Venta",
					"idproducto"				=>$detalle->idproducto,
					"descripcion"				=>$detalle->descripcion,
					"entradaf"					=>$detalle->calmacen,
					"saldof"						=>$saldof,
					"costo"							=>$detalle->palmacen,
					"entradav"					=>$salidav,
					"saldov"						=>$saldov,
					"documento"					=>$datos->serie."-".$datos->numero,
				);
				//var_dump($datak);
				$insertark=$this->kardex_model->insert($datak);

				$datas=array("stock"=>$saldof);
				$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

				//devolucion de lotes
				if ($detalle->lote!='') {
					$nlote=explode("|",$detalle->lote);
					$flote=explode("|",$detalle->fvencimiento);
					$clote=explode("|",$detalle->clote);

					for ($l=0; $l < count($nlote) ; $l++) {
						$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));

						if ($consultal==null) {
							$datal=array
							(
								"idestablecimiento"	=>$datos->idestablecimiento,
								"idproducto"				=>$detalle->idproducto,
								"nlote"							=>$nlote[$l],
								"fvencimiento"			=>valor_fecha($flote[$l]),
								"inicial"						=>$clote[$l],
								"stock"							=>$clote[$l],
							);
							$insertarl=$this->lote_model->insert($datal);
						} else {
							$datal=array("stock"=>$consultal->stock+$clote[$l]);
							$actualizar=$this->lote_model->update($datal,$datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
						}

            $saldos=$this->kardexl_model->ultimo($datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
            $inicial=$saldos==null ? 0: $saldos->saldof;
            $saldosl=$inicial+$clote[$l];
            $datac=array
            (
              'idestablecimiento' =>$datos->idestablecimiento,
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date('Y-m-d'),
              'idtmovimiento'     =>1,
              'concepto'          =>'Venta Anulada',
              'idproducto'        =>$detalle->idproducto,
              'descripcion'       =>$detalle->descripcion,
              'nlote'             =>$nlote[$l],
              'entradaf'          =>$clote[$l],
              'saldof'            =>$saldosl,
              'documento'         =>$datos->serie."-".$datos->numero,
            );
            $insertarc=$this->kardexl_model->insert($datac);
					}
				}
			}
		}

    $datap=array("nulo"=>1,"total"=>"0.00");
    $actualizap=$this->cobroe_model->update($datap,array("idventa"=>$id));
	}

	public function anulacionn($id)
  {
    $controlip=$this->controlip('venta/anulacionn');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->nota_model->mostrar($id);
    if ($datos->tipo_estado<'09' && $datos->nulo==0) {
      $datav=array("nulo"=>1);
      $actualizar=$this->nota_model->update($datav,$id);
      $devolver=$this->devolucionn($id);
	    $control_movimiento=$this->movimientos('venta/anulacionn','Anulo nota credito '.$datos->serie.'-'.$datos->numero);

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
    $proceso['url']=base_url().'venta';
    echo json_encode($proceso);
    exit();
  }

	public function devolucionn($id)
	{
		/*----------  devolucion de nota  ----------*/
		$datos=$this->nota_model->mostrar($id);
		if ($datos->tcomprobante=="07") {
			$detalles=$this->notad_model->mostrarTotal($id);
			foreach ($detalles as $detalle) {
				$productos=$this->producto_model->mostrar(array("p.id"=>$detalle->idproducto));

				if ($productos->tipo=='B') {
					$saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));
					$inicalf=$saldos==null ? 0: $saldos->saldof;
					$inicalv=$saldos==null ? 0: $saldos->saldov;
					//costos promedio
					$salidav=$detalle->calmacen*$detalle->palmacen;

					$saldof=$inicalf-$detalle->calmacen;
					$saldov=$inicalv-$salidav;
					$datak=array
					(
						"idestablecimiento"	=>$datos->idestablecimiento,
						"iduser"						=>$this->session->userdata('id'),
						"fecha"							=>date("Y-m-d"),
						"idtmovimiento"			=>1,
						"concepto"					=>"Anulacion ".$datos->ncomprobante,
						"idproducto"				=>$detalle->idproducto,
						"descripcion"				=>$detalle->descripcion,
						"salidaf"						=>$detalle->calmacen,
						"saldof"						=>$saldof,
						"costo"							=>$detalle->palmacen,
						"salidav"						=>$salidav,
						"saldov"						=>$saldov,
						"documento"					=>$datos->serie."-".$datos->numero,
					);
					//var_dump($datak);
					$insertark=$this->kardex_model->insert($datak);

					$datas=array("stock"=>$saldof);
					$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

					//devolucion de lotes
					if ($detalle->lote!='') {
						$nlotes=explode("|",$detalle->lote);
						$clotes=explode("|",$detalle->clote);
						for ($l=0; $l < count($nlotes) ; $l++) {
							$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlotes[$l]));
							$saldoc=$consultal->stock-$clotes[$l];	//saldo a guardar

							if ($saldoc>0) {
								$datal=array('stock'=>$saldoc);
								$actualizar=$this->lote_model->update($datal,$datos->idestablecimiento,$detalle->idproducto,$nlotes[$l]);
							} else {
								$elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlotes[$l]));
							}

              $saldos=$this->kardexl_model->ultimo($datos->idestablecimiento,$detalle->idproducto,$nlotes[$l]);
              $inicial=$saldos==null ? 0: $saldos->saldof;
              $saldosl=$inicial-$clotes[$l];
              $datac=array
              (
                'idestablecimiento' =>$datos->idestablecimiento,
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date('Y-m-d'),
                'idtmovimiento'     =>1,
                'concepto'          =>$datos->ncomprobante.' Rechazada',
                'idproducto'        =>$detalle->idproducto,
                'descripcion'       =>$detalle->descripcion,
                'nlote'             =>$nlotes[$l],
                'salidaf'           =>$clotes[$l],
                'saldof'            =>$saldosl,
                'documento'         =>$datos->serie."-".$datos->numero,
              );
              //var_dump($datak);
              $insertarc=$this->kardexl_model->insert($datac);
						}
					}
				}
			}
		}

    $datap=array("nulo"=>1,"total"=>"0.00");
    $actualizap=$this->cobron_model->update($datap,array("idnota"=>$id));
	}

  public function enviarAnulado($id,$documento)
  {
    $controlip=$this->controlip('venta/enviarAnulado');
    $empresa=$this->empresa_model->mostrar();

    $numeracion = $this->anulado_model->mostrar(array("femision"=>date("Y-m-d")));
    $separador = $numeracion==null ? '' : explode('-',$numeracion->identificador);
    $numero = $numeracion==null ? 1: $separador[2]+1;
    $codigo = str_replace('-','',date("Y-m-d"));

    $comprobante=array
    (
      'idestablecimiento' =>$this->session->userdata("predeterminado"),
      'tipo_soap'         =>$empresa->tipo_soap,
      'iduser'            =>$this->session->userdata('id'),
      'femision'          =>date("Y-m-d"),
      'fdocumento'        =>$documento->femision,
      'identificador'     =>'RA-'.$codigo.'-'.$numero,
      "tipo_estado"       =>"01",
    );
    $insertar=$this->anulado_model->insert($comprobante);

    $datad=array
    (
      'idanulado' =>$insertar,
      'idventa'   =>$id,
      'motivo'    =>'Anulacion de Operacion'
    );
    $insertard=$this->anuladod_model->insert($datad);

    //actualizar estado de comprobante
    $datav=array("tipo_estado"=>"13");
    $actualizarv=$this->venta_model->update($datav,$id);

    $nombrexml = $empresa->ruc.'-RA-'.$codigo.'-'.$numero;
    $ruta_xml = "downloads/xml/".$nombrexml;
    $itemx=array
    (
      'tcomprobante'    =>$documento->tcomprobante,
      'serie'           =>$documento->serie,
      'numero'          =>$documento->numero,
      'motivo'          =>'Anulacion de Operacion'
    );
    $detalle[]=$itemx;
    $this->generadoXML->CrearXmlBajaDocumentos($ruta_xml, $empresa, $comprobante, $detalle);

    $ruta_certificado = "downloads/certificado/".$empresa->certificado;
    $hash = $this->firmadoXML->FirmarDocumento($ruta_xml,$ruta_certificado,$empresa->certificado_clave);
    $resultado = $this->apiFacturacion->EnviarResumenComprobantes($empresa,$nombrexml,$ruta_xml);

    if ($resultado['ticket']!='') {
      $datav=array
      (
        'filename'  =>$nombrexml,
        'ticket'    =>$resultado['ticket'],
        'has_xml'   =>1,
      );
      $actualizar=$this->anulado_model->update($datav,$insertar);

      $mensaje['color']='success';
    }else{
      $mensaje['color']='danger';
    }

    $mensaje['mensaje']=$resultado['mensaje'];
    return $mensaje;
  }

  public function enviarAnuladob($id,$documento)
  {
    $controlip=$this->controlip('venta/enviarAnuladob');
    $empresa=$this->empresa_model->mostrar();

    $numeracion = $this->resumen_model->mostrar(array("femision"=>date("Y-m-d")));
    $separador = $numeracion==null ? '' : explode('-',$numeracion->identificador);
    $numero = $numeracion==null ? 1: $separador[2]+1;
    $codigo = str_replace('-','',date("Y-m-d"));

    $comprobante=array
    (
      'idestablecimiento' =>$this->session->userdata("predeterminado"),
      'tipo_soap'         =>$empresa->tipo_soap,
      'iduser'            =>$this->session->userdata('id'),
      'femision'          =>date("Y-m-d"),
      'fdocumento'        =>$documento->femision,
      'tproceso'          =>3,
      'identificador'     =>'RC-'.$codigo.'-'.$numero,
      "tipo_estado"       =>"01",
    );
    $insertar=$this->resumen_model->insert($comprobante);

    $datad=array
    (
      'idresumen' =>$insertar,
      'idventa'   =>$id,
      'condicion' =>3
    );
    $insertard=$this->resumend_model->insert($datad);

    //actualizar estado de comprobante
    $datav=array("tipo_estado"=>"13");
    $actualizarv=$this->venta_model->update($datav,$id);

    $nombrexml = $empresa->ruc.'-RC-'.$codigo.'-'.$numero;
    $ruta_xml = "downloads/xml/".$nombrexml;
    $cliente=$this->cliente_model->mostrar($documento->idcliente);
    $itemx=array
    (
      'idresumen'       =>$id,
      'tipo_comprobante'=>$documento->tcomprobante,
      'serie'           =>$documento->serie,
      'numero'          =>$documento->numero,
      'tipo_documento'  =>$cliente->tdocumento,
      'documento'       =>$cliente->documento,
      'condicion'       =>3,
      'moneda'          =>$documento->moneda,
      'total'           =>$documento->total,
      'total_gravado'   =>$documento->tgravado,
      'total_exonerado' =>$documento->texonerado,
      'total_inafecto'  =>$documento->tinafecto,
      'total_gratuito'  =>$documento->tgratuito,
      'total_impuesto'  =>$documento->tigv,
      'total_igv'       =>$documento->tigv,
      'tipo_afectado'   =>'',
      'afectado'        =>'',
    );
    $detalle[]=$itemx;
    $this->generadoXML->CrearXMLResumenDocumentos($ruta_xml, $empresa, $comprobante, $detalle);

    $ruta_certificado = "downloads/certificado/".$empresa->certificado;
    $hash = $this->firmadoXML->FirmarDocumento($ruta_xml,$ruta_certificado,$empresa->certificado_clave);
    $resultado = $this->apiFacturacion->EnviarResumenComprobantes($empresa,$nombrexml,$ruta_xml);

    if ($resultado['ticket']!='') {
      $datav=array
      (
        'filename'  =>$nombrexml,
        'ticket'    =>$resultado['ticket'],
        'has_xml'   =>1,
      );
      $actualizar=$this->resumen_model->update($datav,$insertar);

      $mensaje['color']='success';
    }else{
      $mensaje['color']='danger';
    }

    $mensaje['mensaje']=$resultado['mensaje'];
    return $mensaje;
  }

  public function descarga($archivo)
  {
    $img = './downloads/xml/'.$archivo.'.xml';
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($img));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($img));
    ob_clean();
    flush();
    readfile($img);
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

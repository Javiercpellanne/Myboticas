<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nventa extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(41)){redirect(base_url()."inicio");}

		$this->layout->setLayout("contraido");
		$this->load->model("serie_model");
		$this->load->model("tpago_model");
		$this->load->model("tidentidad_model");
		$this->load->model("departamento_model");
		$this->load->model("provincia_model");
		$this->load->model("distrito_model");
		$this->load->model("cliente_model");
		$this->load->model("tafectacion_model");
		$this->load->model("categoria_model");
		$this->load->model("laboratorio_model");
		$this->load->model("pactivo_model");
		$this->load->model("aterapeutica_model");
		$this->load->model("lote_model");
		$this->load->model("kardex_model");
		$this->load->model("kardexl_model");
		$this->load->model("bonificado_model");
		$this->load->model("arqueo_model");
		$this->load->model("nventa_model");
		$this->load->model("nventad_model");
		$this->load->model("cobro_model");
		$this->load->model('cotizacion_model');
		$this->load->model('cotizaciond_model');
		$this->load->model("punto_model");
		$this->load->model("clientep_model");
		$this->load->model("vale_model");
		$this->load->library('mytcpdf');
	}

	public function index()
	{
    $controlip=$this->controlip('nventa');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date("Y-m-d")) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;

		$filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"));
		if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
		if ($empresa->lventa==1) {$filtros["femision>="]=$inicio; $filtros["femision<="]=$fin;}
		$listas=$empresa->lventa==1 ? $this->nventa_model->mostrarTotal($filtros,"desc"): $this->nventa_model->mostrarLimite($filtros,"desc");

		$arqueoc=$this->arqueo_model->contador($this->session->userdata("predeterminado"),$this->session->userdata("id"));
		$anulacionv=$this->usuario_model->mostrar($this->session->userdata("id"));
		$this->layout->setTitle("Nota de Venta");
		$this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin","arqueoc",'anulacionv'));
	}

	public function arqueoi()
	{
    $controlip=$this->controlip('nventa/arqueoi');
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
			$control_movimiento=$this->movimientos('nventa/arqueoi','Registro arqueo nro '.$insertar);

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");

			echo base_url()."nventa/nventai";
			exit();
		}

		$this->layout->setLayout("blanco");
		$this->layout->view("arqueoi");
	}

  public function busDatos($tipo,$numero)
  {
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
    return $response;
  }

	public function clientei()
	{
    $controlip=$this->controlip('nventa/clientei');
		if ($this->input->post("documento",true))
		{
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
					$control_movimiento=$this->movimientos('nventa/clientei','Registro cliente '.$this->input->post('nombres',true));

					$datos['success'] = true;
					$datos['data'] = array("idcliente"=>$insertar,"tdocumento"=>$this->input->post("tipo",true),"cliente"=>$this->input->post("nombres",true),"puntos"=>'Puntos Acumulados : 0');
				} else {
					$datos['success'] = false;
					$datos['data'] = "El numero de documento en incoherente con el tipo de documento";
				}
			} else {
				$datos['success'] = false;
				$datos['data'] = "El numero de documento ya fue ingresado";
			}
			echo json_encode($datos);
			exit();
		}

		$identidades=$this->tidentidad_model->mostrarTotal();
    $departamentos=$this->departamento_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("clientei",compact("departamentos","identidades"));
	}

	public function productoi()
	{
    $controlip=$this->controlip('nventa/productoi');
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			$consulta=$this->producto_model->contador(array("descripcion"=>$this->input->post('productos',true),"idlaboratorio"=>$this->input->post('laboratorio',true)));
			if ($consulta==0 && $this->input->post('productos',true)!='') {
				$data=array
				(
					'tipo'					=>'B',
					'idcategoria'		=>$this->input->post('categoria',true),
					'descripcion'		=>trim(mb_strtoupper($this->input->post('productos',true), 'UTF-8')),
					'idlaboratorio'	=>$this->input->post('laboratorio',true),
					'idpactivo'			=>$this->input->post('pactivo',true),
					'idaterapeutica'=>$this->input->post('aterapeutica',true),
					'umedidav'			=>$this->input->post('umedidav',true),
					'codbarra'			=>$this->input->post('codbarra',true),
					'rsanitario'		=>$this->input->post('rsanitario',true),
					'mstock'				=>$this->input->post('mstock',true),
					'tafectacion'		=>$this->input->post('tafectacion',true),
					'pcompra'				=>$this->input->post('pcompra',true),
					'factor'				=>$this->input->post('factor',true),
					'compra'				=>$this->input->post('compra',true),
					'estado'				=>1,
					'lote'					=>valor_check($this->input->post('lote',true)),
				);
				if ($this->input->post('factor',true)>1) {
					$data['umedidac']=$this->input->post('umedidac',true);
				}
				if ($this->input->post('factorb',true)>1) {
					$data['umedidab']=$this->input->post('umedidab',true);
					$data['factorb']=$this->input->post('factorb',true);
				}
				if ($empresa->pestablecimiento==0) {
					$data['pventa']=$this->input->post('pventa',true);
					$data['venta']=$this->input->post('venta',true);
					$data['pblister']=$this->input->post('pblister',true);
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
						$datae['venta']=$this->input->post('venta',true);
						$datae['pblister']=$this->input->post('pblister',true);
					}
					$insertark=$this->inventario_model->insert($datae);
				}

				if ($this->input->post('stock',true)>0) {
					$datak=array
					(
						'idestablecimiento'	=>$this->session->userdata('predeterminado'),
						'iduser'						=>$this->session->userdata('id'),
						'fecha'							=>date('Y-m-d'),
						'idtmovimiento'			=>16,
						'concepto'					=>"Stock inicial",
						'idproducto'				=>$insertar,
						'descripcion'				=>trim(mb_strtoupper($this->input->post('productos',true), 'UTF-8')),
						'entradaf'					=>$this->input->post('stock',true),
						'saldof'						=>$this->input->post('stock',true),
						'costo'							=>$this->input->post('pcompra',true),
						'entradav'					=>$this->input->post('stock',true)*$this->input->post('pcompra',true),
						'saldov'						=>$this->input->post('stock',true)*$this->input->post('pcompra',true),
					);
					$insertark=$this->kardex_model->insert($datak);

					$datas=array('stock'=>$this->input->post('stock',true));
					$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$insertar));

					if (valor_check($this->input->post('lote',true))==1 && $this->input->post('clote',true)!="") {
						$datal=array
						(
							'idestablecimiento'	=>$this->session->userdata("predeterminado"),
							'idproducto'				=>$insertar,
							'nlote'							=>$this->input->post('clote',true),
							'fvencimiento'			=>valor_fecha($this->input->post('fvencimiento',true)),
							'inicial'						=>$this->input->post('stock',true),
							'stock'							=>$this->input->post('stock',true),
						);
						$insertarl=$this->lote_model->insert($datal);

            $datac=array
            (
              'idestablecimiento' =>$this->session->userdata('predeterminado'),
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date('Y-m-d'),
              'idtmovimiento'			=>16,
							'concepto'					=>"Stock inicial",
              'idproducto'        =>$insertar,
              'descripcion'       =>trim($this->input->post('productos',true)),
              'nlote'             =>$this->input->post('clote',true),
              'entradaf'          =>$this->input->post('stock',true),
              'saldof'            =>$this->input->post('stock',true),
            );
            $insertarc=$this->kardexl_model->insert($datac);
					}
				}
				$control_movimiento=$this->movimientos('nventa/productoi','Registro producto '.$this->input->post('productos',true));

				$datos['success'] = true;
				$datos['mensaje'] = "El registro del producto se genero con exito";
			} else {
				$datos['success'] = false;
				$datos['mensaje'] = "El producto ya fue registrado";
			}

			echo json_encode($datos);
			exit();
		}

		$categorias=$this->categoria_model->mostrarTotal('F');
		$laboratorios=$this->laboratorio_model->mostrarTotal();
		$pactivos=$this->pactivo_model->mostrarTotal();
		$aterapeuticas=$this->aterapeutica_model->mostrarTotal();
		$tafectaciones=$this->tafectacion_model->mostrarTotal();
		$this->layout->setLayout("blanco");
		$this->layout->view("productoi",compact("categorias","laboratorios","pactivos","aterapeuticas","tafectaciones"));
	}

	public function atajos()
	{
		$this->layout->setLayout("blanco");
		$this->layout->view("atajos");
	}

	public function cotizacioni($id)
	{
    $controlip=$this->controlip('nventa/cotizacioni');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$mpagos=$this->tpago_model->mostrarTotal();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tcomprobante"=>"99"));
		$cotizacion=$this->cotizacion_model->mostrar($id);
		$detalles=$this->cotizaciond_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($cotizacion->idcliente);
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Nota de Venta");
		$this->layout->view("cotizacioni",compact("anexos","nestablecimiento","empresa","mpagos","nserie","productos","cotizacion","detalles","cliente","vendedores","id"));
	}

	public function nventai()
	{
    $controlip=$this->controlip('nventa/nventai');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$canexos=$this->establecimiento_model->contador();
		$mpagos=$this->tpago_model->mostrarTotal();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tcomprobante"=>"99"));
		$bonodiario=$this->nventa_model->bonos(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"femision"=>date('Y-m-d'),"v.iduser"=>$this->session->userdata("id")));
		$bonomensual=$this->nventa_model->bonos(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"year(femision)"=>date("Y"),"month(femision)"=>date("n"),"v.iduser"=>$this->session->userdata("id")));
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Nota de Venta");
		$this->layout->view("nventai",compact("anexos","nestablecimiento","empresa","canexos","mpagos","nserie","productos",'bonodiario','bonomensual',"vendedores"));
	}

	public function copias($id)
	{
    $controlip=$this->controlip('nventa/copias');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$mpagos=$this->tpago_model->mostrarTotal();
		$productos=$this->producto_model->mostrarLimite(array("estado"=>1,"factor>"=>0));
		$nserie=$this->serie_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"tcomprobante"=>"99"));
		$nventa=$this->nventa_model->mostrar($id);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cliente=$this->cliente_model->mostrar($nventa->idcliente);
    $vendedores=$this->usuario_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Nota de Venta");
		$this->layout->view("copias",compact("anexos","nestablecimiento","empresa","mpagos","nserie","productos","nventa","detalles","cliente","vendedores","id"));
	}

	public function guardar($id=NULL)
	{
    $controlip=$this->controlip('nventa/guardar');
		if ($this->input->post())
		{
			$impresion='';
			$empresa=$this->empresa_model->mostrar();
			if ($this->input->post('idproducto',true)==null) {
				$mensaje='No envio productos en la venta!';
			} else {
				$pagado=round(array_sum($this->input->post('monto',true)),2);
				if (valor_check($this->input->post('pcredito',true))==0 && $pagado!=floatval($this->input->post('totalg',true))) {
					$mensaje='El monto cobrado es diferente al comprobante';
				}else{
					$numero=$this->nventa_model->maximo($this->input->post('serie',true));
					$ninicio= $numero==null ? '' : $numero->numero;
					$numeracion=$ninicio+1;

					$consulta=$this->nventa_model->contador(array("serie"=>$this->input->post("serie",true),"numero"=>$numeracion));
					if ($consulta==0) {
						$data=array
						(
							'idestablecimiento'	=>$this->session->userdata("predeterminado"),
							'iduser'						=>$this->session->userdata('id'),
							'femision'					=>date("Y-m-d"),
							'hemision'					=>date('H:i:s'),
							'fvencimiento'			=>date("Y-m-d"),
							'serie'							=>$this->input->post('serie',true),
							'numero'						=>$numeracion,
							'formato'						=>valor_check($this->input->post('formato',true)),
							'idcliente'					=>$this->input->post('idcliente',true),
							'cliente'						=>$this->input->post('cliente',true),
							'total'							=>$this->input->post('totalg',true),
							'izipay'						=>valor_fecha($this->input->post('mizipay',true)),
							'lote'							=>valor_check($this->input->post('impresion',true)),
							'dadicional'				=>$this->input->post('dadicional',true),
							'condicion'					=>1,
							'cancelado'					=>1,
							'efectivo'					=>valor_fecha($this->input->post('efectivo',true)),
							'vuelto'						=>valor_fecha($this->input->post('vuelto',true)),
							'idvendedor'				=>$this->input->post('vendedor',true),
						);

						if ($this->input->post('mdsctog',true)!='') {
				      $data["dscto"]=$this->input->post("mdsctog",true);
				    }
						$insertar=$this->nventa_model->insert($data);

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
								'condicion'		=>2,
								'cancelado'		=>0,
								'pcuota'			=>$this->input->post('pcuota',true),
								'cuotas'			=>$this->input->post('cuotas',true),
								'mcuota'			=>$this->input->post('mcuota',true),
								'fpago'				=>$posterior,
								'fvencimiento'=>$fvencimiento,
							);
							$actualizac=$this->nventa_model->update($datac,array("id"=>$insertar));
						} else {
							for ($i=0; $i < count($this->input->post('mpago',true)) ; $i++) {
				        $datap=array
				        (
				        	'idestablecimiento'	=>$this->session->userdata("predeterminado"),
				          'iduser'    				=>$this->session->userdata('id'),
				          'idnventa'  				=>$insertar,
				          'femision'  				=>date("Y-m-d"),
				          'total'   					=>$this->input->post('monto',true)[$i],
				          'idtpago'   				=>$this->input->post('mpago',true)[$i],
          				'documento'         =>$this->input->post("documento",true)[$i],
				        );
				        $insertarp=$this->cobro_model->insert($datap);
				      }
						}

				    for ($i=0; $i < count($this->input->post("idproducto",true)) ; $i++) {
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
					      	"idestablecimiento"	=>$this->session->userdata("predeterminado"),
					      	"iduser"						=>$this->session->userdata('id'),
					        "fecha"     				=>date("Y-m-d"),
					        "idtmovimiento" 		=>1,
					        "concepto"    			=>"Nota de venta",
					        "idproducto"  			=>$this->input->post("idproducto",true)[$i],
					        "descripcion" 			=>trim($this->input->post("descripcion",true)[$i]),
					        "salidaf"   				=>$this->input->post("almacenc",true)[$i],
					        "saldof"    				=>$saldof,
					        "costo"     				=>$costo,
					        "salidav"   				=>$salidav,
					        "saldov"    				=>$saldov,
					        "documento"   			=>$this->input->post("serie",true)."-".$numeracion,
					      );
					      $insertark=$this->kardex_model->insert($datak);

					      $datas=array('stock'=>$saldof);
					      $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));

					      $nlotes="";
					      $clotes="";
					      $flotes="";
					      if ($this->input->post("lote",true)[$i]==1) {
					        $cantidad=$this->input->post("almacenc",true)[$i];
					        $clotes=array();
					        $flotes=array();
					      	$nlotes=array();
					      	if ($this->input->post("nlote",true)[$i]!='') {
					      		$nlote1=explode(",",$this->input->post("nlote",true)[$i]);
										for ($l=0; $l < count($nlote1) ; $l++) {
											$consultal=$this->lote_model->mostrar(array("idestablecimiento"	=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$nlote1[$l]));
											$ncantidad=$cantidad-$consultal->stock;	//nueva cantidad
											$saldoc=$consultal->stock-$cantidad;	//saldo a guardar

											if ($saldoc>0) {
												$datal=array('stock'=>$saldoc);
												$actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$nlote1[$l]);
											} else {
												$elimnarl=$this->lote_model->delete(array("idestablecimiento"	=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$nlote1[$l]));
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
												'concepto'					=>'Nota de venta',
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
						            $elimnarl=$this->lote_model->delete(array("idestablecimiento"	=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$consultal->nlote));
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
												'concepto'					=>'Nota de venta',
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

				      $datad=array
				      (
				        'idnventa'    =>$insertar,
				        'idproducto'  =>$this->input->post("idproducto",true)[$i],
				        'descripcion' =>trim($this->input->post('descripcion',true)[$i]),
				        'cantidad'    =>$this->input->post("cantidad",true)[$i],
				        'unidad'      =>$this->input->post("unidad",true)[$i],
				        'precio'      =>$this->input->post("precio",true)[$i],
				        'importe'     =>$this->input->post("importe",true)[$i],
				      );

							if ($this->input->post('tipo',true)[$i]=='B') {
								$datad["calmacen"]		=$this->input->post("almacenc",true)[$i];
								$datad["palmacen"]		=$costo;
								$datad["lote"]				=$nlotes;
								$datad["clote"]				=$clotes;
								$datad["fvencimiento"]=$flotes;
							}

							if ($this->input->post('dscto',true)[$i]>0) {
								if ($this->input->post('tdscto',true)[$i]==0) { //descuento porcentaje
									$mimporte=$this->input->post('cantidad',true)[$i]*$this->input->post('precio',true)[$i];
									$mdscto=$mimporte*$this->input->post('dscto',true)[$i]/100;
								} else { //descuento en monto
									$mdscto=$this->input->post("dscto",true)[$i];
								}
								$datad["dscto"]=$mdscto;
							}

							if ($this->input->post('doctor',true)[$i]!='') {
								$receta["paciente"]=$this->input->post("paciente",true)[$i];
					      $receta["colegiatura"]=$this->input->post("colegiatura",true)[$i];
					      $receta["doctor"]=$this->input->post("doctor",true)[$i];
					      $datad["controlado"]=json_encode($receta);
					    }
				      //var_dump($datad);
				      $insertard=$this->nventad_model->insert($datad);
				    }

						if ($empresa->spuntos==1 && $this->input->post("idcliente",true)>1 && $this->input->post("tdocumento",true)!=6) {
							$vpuntos=$this->punto_model->mostrar();
				      $punto_acumulado = intval($this->input->post("totalg",true)/$vpuntos->valorp);
							$datap=array
							(
								"idnventa"		=>$insertar,
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

						if (valor_check($this->input->post('formato',true))==1) {
							$impresion=base_url()."nventa/pdfa4/".$insertar;
						}else{
							$impresion=base_url()."nventa/pdf".$empresa->ticket."/".$insertar;
						}
      			$control_movimiento=$this->movimientos('nventa/guardar','Emitio Nota Venta '.$this->input->post("serie",true).'-'.$numeracion);

						$mensaje='Se genero la nota de venta exitosamente!';
					} else {
						$mensaje='El comprobante ya existe';
					}
				}
			}

			$datos['mensaje']=$mensaje;
			$datos['impresion']=$impresion;
			$datos['url']=base_url().'nventa/nventai';
			echo json_encode($datos);
			exit();
		}
	}

	public function generar($id)
	{
    $empresa=$this->empresa_model->mostrar();
		$impresion=base_url()."nventa/pdf".$empresa->ticket."/".$id;

		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'Se genero la nota de venta exitosamente!');

		$this->layout->setLayout("blanco");
		$this->layout->view("generar",compact("impresion"));
	}

	public function nventaa($id)
	{
    $controlip=$this->controlip('nventa/nventaa');
		$datos=$this->nventa_model->mostrar($id);
		if ($datos->nulo==0 && $datos->emitido=='') {
			$detalles=$this->nventad_model->mostrarTotal($id);
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
						"concepto"					=>"Anulacion Nota de venta",
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
							$consultal=$this->lote_model->mostrar(array("idestablecimiento"	=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));
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
	    					'idestablecimiento'	=>$datos->idestablecimiento,
	    					"iduser"    				=>$this->session->userdata('id'),
	    					"fecha"							=>date("Y-m-d"),
								"idtmovimiento"			=>1,
								"concepto"					=>"Anulacion Nota de venta",
	    					"idproducto"				=>$detalle->idproducto,
	    					"descripcion"				=>$detalle->descripcion,
	    					"nlote"							=>$nlote[$l],
	    					"entradaf"					=>$clote[$l],
	    					"saldof"						=>$saldosl,
	    					"documento"					=>$datos->serie."-".$datos->numero,
	    				);
	    				$insertarc=$this->kardexl_model->insert($datac);
						}
					}
				}
			}

			$data=array
			(
				"nulo"	 	=>1,
				"total"	=>"0.00",
				"izipay"	=>NULL,
			);
			$actualiza=$this->nventa_model->update($data,array("id"=>$id));

			$datac=array
			(
				"nulo"	 	=>1,
				"total"	=>"0.00",
			);
			$actualizac=$this->cobro_model->update($datac,array("idnventa"=>$id));

			$elimnarp=$this->clientep_model->delete(array("idnventa"=>$id));

			$datat=array("estado"	=>1,"emitido"	=>NULL);
      $actualizar=$this->cotizacion_model->update($datat,array("emitido"=>$datos->serie.'-'.$datos->numero));
      $control_movimiento=$this->movimientos('nventa/nventaa','Anulo Nota Venta '.$datos->serie.'-'.$datos->numero);

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
    $proceso['url']=base_url().'nventa';
    echo json_encode($proceso);
    exit();
	}

	public function consulta($id)
	{
		$datos=$this->nventa_model->mostrar($id);
		$detalles=$this->nventad_model->mostrarTotal($id);
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
		$datos=$this->nventa_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cobros=$this->cobro_model->mostrarTotal(array("idnventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa4",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario"));
	}

	public function pdf80($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nventa_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cobros=$this->cobro_model->mostrarTotal(array("idnventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$tpuntos=$this->clientep_model->cantidadTotal($datos->idcliente);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf80",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","tpuntos"));
	}

	public function pdf58($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nventa_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cobros=$this->cobro_model->mostrarTotal(array("idnventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$tpuntos=$this->clientep_model->cantidadTotal($datos->idcliente);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdf58",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario","tpuntos"));
	}

	public function pdfa5($id)
	{
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();
		$datos=$this->nventa_model->mostrar($id);
		$cliente=$this->cliente_model->mostrar($datos->idcliente);
		$detalles=$this->nventad_model->mostrarTotal($id);
		$cobros=$this->cobro_model->mostrarTotal(array("idnventa"=>$id));
		$usuario= $this->usuario_model->mostrar($datos->iduser);
		$this->layout->setLayout("blanco");
		$this->layout->view("pdfa5",compact("empresa","nestablecimiento","datos","cliente","detalles",'cobros',"usuario"));
	}

	public function metodos()
	{
		$datos=$this->tpago_model->mostrarTotal();
		echo json_encode($datos);
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

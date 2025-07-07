<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facturacion extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!is_numeric($this->session->userdata('codigo'))){redirect(base_url().'inicio');}

		$this->layout->setLayout("contraido");
		$this->load->model("testado_model");
		$this->load->model("kardex_model");
    $this->load->model("kardexl_model");
		$this->load->model("lote_model");
		$this->load->model("cliente_model");
		$this->load->model("serie_model");
		$this->load->model("tvalidacion_model");
		$this->load->model("tcomprobante_model");
		$this->load->model("nventa_model");
		$this->load->model("venta_model");
		$this->load->model("ventad_model");
		$this->load->model("cobroe_model");
		$this->load->model("nota_model");
		$this->load->model("notad_model");
		$this->load->model("cobron_model");
		$this->load->model("despacho_model");
		$this->load->model("despachod_model");
		$this->load->model("resumen_model");
		$this->load->model("resumend_model");
		$this->load->model("anulado_model");
    $this->load->model("anuladod_model");

    $this->load->library("generadorXML");
    $this->load->library("firmarXML");
    $this->load->library("apiFacturacion");
    $this->generadoXML = new GeneradorXML();
    $this->firmadoXML = new FirmarXML();
    $this->apiFacturacion = new ApiFacturacion();
	}

	/*=====================================================================================================================
	=                                               comprobantes no enviados                                              =
	=====================================================================================================================*/
	public function index()
	{
    if (!$this->acciones(27)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$filtros=array("tipo_estado"=>'01',"has_cdr"=>0,"rectificar"=>0);
    if ($empresa->envio_boleta==0){$filtros["grupo"]="01";}
		$listas=$this->venta_model->mostrarTotal($filtros,"asc");
		$listasn=$this->nota_model->mostrarTotal($filtros,"asc");

		$filtros=array("has_cdr"=>0,"tipo_estado"=>"01");
		$listasd=$this->despacho_model->mostrarTotal($filtros,"asc");
		$this->layout->setTitle('Comprobante no Enviado');
		$this->layout->view('index',compact("anexos","nestablecimiento",'empresa','listas','listasn','listasd'));
	}

  public function enviarFactura($id)
  {
    $controlip=$this->controlip('facturacion/enviarFactura');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->venta_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->EnviarComprobanteElectronico($empresa,$nombre,$rutazip,$rutacdr);
    if ($resultado['estado']==1) {
      if (!is_numeric($resultado['codigo'])) {
        $data=array
        (
          "rectificar"   =>1,
          "respuesta_rectificar" =>$resultado['mensaje'],
        );
        $actualizar=$this->venta_model->update($data,$id);
        $this->session->set_flashdata('css', 'danger');
      } elseif (obtenerNumero($resultado['codigo'])===0) {
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"05",
          "respuesta_sunat" =>$resultado['mensaje'],
        );
        $actualizar=$this->venta_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
      } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
        $data=array
        (
          "rectificar"   =>1,
          "respuesta_rectificar" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->venta_model->update($data,$id);
        $this->session->set_flashdata('css', 'info');
      } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"09",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->venta_model->update($data,$id);
        $devolver=$this->devolucionv($id);
        $this->session->set_flashdata('css', 'info');
      } else {// 4000 en adelante Observaciones
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"07",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->venta_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
      }
    }

    $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    redirect(base_url().'facturacion');
  }

  public function enviarNota($id)
  {
    $controlip=$this->controlip('facturacion/enviarNota');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->nota_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->EnviarComprobanteElectronico($empresa,$nombre,$rutazip,$rutacdr);
    if ($resultado['estado']==1) {
      if (!is_numeric($resultado['codigo'])) {
        $data=array
        (
          "rectificar"   =>1,
          "respuesta_rectificar" =>$resultado['mensaje'],
        );
        $actualizar=$this->nota_model->update($data,$id);
        $this->session->set_flashdata('css', 'danger');
      } elseif (obtenerNumero($resultado['codigo'])===0) {
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"05",
          "respuesta_sunat" =>$resultado['mensaje'],
        );
        $actualizar=$this->nota_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
      } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
        $data=array
        (
          "rectificar"   =>1,
          "respuesta_rectificar" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->nota_model->update($data,$id);
        $this->session->set_flashdata('css', 'info');
      } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"09",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->nota_model->update($data,$id);
        $devolver=$this->devolucionn($id);
        $this->session->set_flashdata('css', 'info');
      } else {// 4000 en adelante Observaciones
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"07",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->nota_model->update($data,$id);
        $this->session->set_flashdata('css', 'success');
      }
    }

    $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    redirect(base_url().'facturacion');
  }

	public function devolucionv($id)
	{
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
          'idestablecimiento' =>$datos->idestablecimiento,
					'iduser'		        =>$this->session->userdata('id'),
					'fecha'			        =>date('Y-m-d'),
					'idtmovimiento'	    =>1,
					'concepto'		      =>'Venta Rechazada',
					'idproducto'	      =>$detalle->idproducto,
					'descripcion'	      =>$detalle->descripcion,
					'entradaf'		      =>$detalle->calmacen,
					'saldof'		        =>$saldof,
					'costo'			        =>$detalle->palmacen,
					'entradav'		      =>$salidav,
					'saldov'		        =>$saldov,
					'documento'		      =>$datos->serie."-".$datos->numero,
				);
				$insertark=$this->kardex_model->insert($datak);

				$datas=array("stock"=>$saldof);
				$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

				//---------------  devolucion de lotes  ------------------
				if ($detalle->lote!='') {
					$nlote=explode("|",$detalle->lote);
					$flote=explode("|",$detalle->fvencimiento);
					$clote=explode("|",$detalle->clote);

					for ($l=0; $l < count($nlote) ; $l++) {
						$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));

						if ($consultal==null) {
							$datal=array
							(
                "idestablecimiento"=>$datos->idestablecimiento,
								"idproducto"	     =>$detalle->idproducto,
								"nlote"				     =>$nlote[$l],
								"fvencimiento"     =>valor_fecha($flote[$l]),
								"inicial"			     =>$clote[$l],
								"stock"				     =>$clote[$l],
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
              'concepto'          =>'Venta Rechazada',
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

		$datac=array
		(
			"nulo"	 	=>1,
			"total"	=>"0.00",
		);
		$actualizac=$this->cobroe_model->update($datac,array("idventa"=>$id));
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'Los producto fueron devueltos a almacen');
	}

	public function devolucionn($id)
	{
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
            'idestablecimiento'=>$datos->idestablecimiento,
						'iduser'		       =>$this->session->userdata('id'),
						'fecha'			       =>date('Y-m-d'),
						'idtmovimiento'	   =>1,
						'concepto'         =>$datos->ncomprobante.' Rechazada',
						'idproducto'	     =>$detalle->idproducto,
						'descripcion'	     =>$detalle->descripcion,
						'salidaf'		       =>$detalle->calmacen,
						'saldof'		       =>$saldof,
						'costo'			       =>$detalle->palmacen,
						'salidav'		       =>$salidav,
						'saldov'		       =>$saldov,
						'documento'		     =>$datos->serie."-".$datos->numero,
					);
					//var_dump($datak);
					$insertark=$this->kardex_model->insert($datak);

					$datas=array("stock"=>$saldof);
					$actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));

					//-------------  devolucion de lotes  -----------------
					if ($detalle->lote!='') {
						$nlote=explode("|",$detalle->lote);
						$clote=explode("|",$detalle->clote);
						for ($l=0; $l < count($nlote) ; $l++) {
							$consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));
							$saldoc=$consultal->stock-$clote[$l];	//saldo a guardar

							if ($saldoc>0) {
								$datal=array('stock'=>$saldoc);
								$actualizar=$this->lote_model->update($datal,$datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
							} else {
								$elimnarl=$this->lote_model->delete(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto,"nlote"=>$nlote[$l]));
							}

              $saldos=$this->kardexl_model->ultimo($datos->idestablecimiento,$detalle->idproducto,$nlote[$l]);
              $inicial=$saldos==null ? 0: $saldos->saldof;
              $saldosl=$inicial-$clote[$l];
              $datac=array
              (
                'idestablecimiento' =>$datos->idestablecimiento,
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date('Y-m-d'),
                'idtmovimiento'     =>1,
                'concepto'          =>$datos->ncomprobante.' Rechazada',
                'idproducto'        =>$detalle->idproducto,
                'descripcion'       =>$detalle->descripcion,
                'nlote'             =>$nlote[$l],
                'salidaf'           =>$clote[$l],
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

		$datac=array
		(
			"nulo"	 	=>1,
			"total"	=>"0.00",
		);
		$actualizac=$this->cobron_model->update($datac,array("idnota"=>$id));
		$this->session->set_flashdata('css', 'success');
		$this->session->set_flashdata('mensaje', 'Los producto fueron devueltos a almacen');
	}

	public function guiaToken()
	{
		$empresa=$this->empresa_model->mostrar();
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientessol/'.$empresa->id_gre.'/oauth2/token/',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'grant_type=password&scope=https://api-cpe.sunat.gob.pe&client_id='.$empresa->id_gre.'&client_secret='.urlencode($empresa->secret_gre).'&username='.$empresa->usuario_soap.'&password='.urlencode($empresa->clave_soap).'',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/x-www-form-urlencoded'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response,true);
	}

	public function enviarGuia($id)
	{
    $controlip=$this->controlip('facturacion/enviarGuia');
		$empresa=$this->empresa_model->mostrar();
		$limite=fechaHoraria('+ '.$empresa->expires_gre.' second ',$empresa->fecha_gre);
		if ($limite<date("Y-m-d H:i:s")) {
			$respuesta_json=self::guiaToken();

			$data=array
			(
				'token_gre'	=>$respuesta_json["access_token"],
				'fecha_gre'	=>date("Y-m-d H:i:s"),
				'expires_gre'	=>$respuesta_json["expires_in"],
			);
			$actualizar=$this->empresa_model->update($data);
		}

    $datos=$this->despacho_model->mostrar($id);
    $nombre = $empresa->ruc.'-'.$datos->tcomprobante.'-'.$datos->serie.'-'.$datos->numero;
    $rutazip="downloads/xml/".$nombre;
    $resultado = $this->apiFacturacion->EnviarGuiaRemision($empresa,$nombre,$rutazip);

    if ($resultado['ticket']!='') {
      $datav=array
      (
        'ticket'    	=>$resultado['ticket'],
        'frecepcion'	=>$resultado['frecepcion'],
        'tipo_estado'	=>'03',
      );
      $actualizar=$this->despacho_model->update($datav,$id);

      sleep(5);
      $mensaje=$this->consultarGuia($id);

      $this->session->set_flashdata('css', 'info');
      $this->session->set_flashdata('mensaje', $mensaje);
    }else{
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    }
    redirect(base_url().'facturacion');
	}

	public function consultarGuia($id,$tipo=null)
	{
    $empresa=$this->empresa_model->mostrar();
    $limite=fechaHoraria('+ '.$empresa->expires_gre.' second ',$empresa->fecha_gre);
    if ($limite<date("Y-m-d H:i:s")) {
      $respuesta_json=self::guiaToken();

      $data=array
      (
        'token_gre' =>$respuesta_json["access_token"],
        'fecha_gre' =>date("Y-m-d H:i:s"),
        'expires_gre' =>$respuesta_json["expires_in"],
      );
      $actualizar=$this->empresa_model->update($data);
    }

    $datos=$this->despacho_model->mostrar($id);
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->ConsultarTicketGuia($empresa,$datos->filename,$datos->ticket,$rutacdr);

    if ($resultado['estado']==3) {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    }elseif ($resultado['estado']==2) {
    	$data=array(
    		"tipo_estado"=>'01',
    		//"ticket"=>NULL,
    	);
      $actualizar=$this->despacho_model->update($data,$id);

      $this->session->set_flashdata('css', 'info');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    } else {
      if (obtenerNumero($resultado['codigo'])===0) {
        $data=array
        (
          "has_cdr"       	=>1,
          "tipo_estado"   	=>"05",
          "respuesta_sunat" =>$resultado['mensaje'],
          "qr"							=>$resultado['qr'],
        );
        $actualizar=$this->despacho_model->update($data,$id);

        //$reimpresion=base_url()."venta/pdfguia/".$id;
      }else{
        $data=array
        (
          "has_cdr"       	=>1,
          "tipo_estado"   	=>"09",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->despacho_model->update($data,$id);
      }

      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    }

    if ($tipo!=null) {
      redirect(base_url().'despacho');
    }else{
      return $resultado['mensaje'];
    }
	}

  public function resumenesi()
  {
    if (!$this->acciones(27)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/resumenesi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $listas=$this->venta_model->mostrarFechas(array("grupo"=>'02',"has_cdr"=>0,"tipo_estado"=>"01","rectificar"=>0));
    $this->layout->setTitle('Resumenes no Enviado');
    $this->layout->view('resumenesi',compact("anexos","nestablecimiento",'empresa','listas'));
  }

  public function itemsResumen($id)
  {
    $detalles=$this->resumend_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      if ($detalle->idventa!='') {
        $documento=$this->venta_model->mostrar($detalle->idventa);
        $cliente=$this->cliente_model->mostrar($documento->idcliente);
        $itemx=array
        (
          'idresumen'       =>$id,
          'tipo_comprobante'=>$documento->tcomprobante,
          'serie'           =>$documento->serie,
          'numero'          =>$documento->numero,
          'tipo_documento'  =>$cliente->tdocumento,
          'documento'       =>$cliente->documento,
          'condicion'       =>$detalle->condicion,
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
      } else {
        $documento=$this->nota_model->mostrar($detalle->idnota);
        $cliente=$this->cliente_model->mostrar($documento->idcliente);
        $venta=$this->venta_model->mostrar($documento->idventa);
        $itemx=array
        (
          'idresumen'       =>$id,
          'tipo_comprobante'=>$documento->tcomprobante,
          'serie'           =>$documento->serie,
          'numero'          =>$documento->numero,
          'tipo_documento'  =>$cliente->tdocumento,
          'documento'       =>$cliente->documento,
          'condicion'       =>$detalle->condicion,
          'moneda'          =>$documento->moneda,
          'total'           =>$documento->total,
          'total_gravado'   =>$documento->tgravado,
          'total_exonerado' =>$documento->texonerado,
          'total_inafecto'  =>$documento->tinafecto,
          'total_gratuito'  =>$documento->tgratuito,
          'total_impuesto'  =>$documento->tigv,
          'total_igv'       =>$documento->tigv,
          'tipo_afectado'   =>$venta->tcomprobante,
          'afectado'        =>$venta->serie.'-'.$venta->numero,
        );
      }

      $datos[]=$itemx;
    }
    return $datos;
  }

  public function enviarResumen($fecha)
  {
    $controlip=$this->controlip('facturacion/enviarResumen');
    $empresa=$this->empresa_model->mostrar();
    $ventas=$this->venta_model->mostrarTotal(array("grupo"=>'02',"tipo_estado"=>"01",'femision'=>$fecha),"asc");
    $notas=$this->nota_model->mostrarTotal(array("grupo"=>'02',"tipo_estado"=>"01",'femision'=>$fecha),"asc");
    if ($ventas!=NULL || $notas!=NULL) {
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
        'fdocumento'        =>$fecha,
        'tproceso'          =>1,
        'identificador'     =>'RC-'.$codigo.'-'.$numero,
        "tipo_estado"       =>"01",
      );
      $insertar=$this->resumen_model->insert($comprobante);

      $limiteRegistrosTotal = 500;
      $contadorTotal = 0;
      foreach ($ventas as $interno) {
        if ($contadorTotal >= $limiteRegistrosTotal) {
            break; // Salir del bucle cuando se alcance el límite de 500 registros en total
        }

        $datad=array
        (
          'idresumen' =>$insertar,
          'idventa'   =>$interno->id,
          'condicion' =>1
        );
        $insertard=$this->resumend_model->insert($datad);

        $datav=array("tipo_estado"=>"03");
        $actualizarv=$this->venta_model->update($datav,$interno->id);
        $contadorTotal++;
      }

      foreach ($notas as $interno) {
        if ($contadorTotal >= $limiteRegistrosTotal) {
            break; // Salir del bucle cuando se alcance el límite de 500 registros en total
        }

        $datad=array
        (
          'idresumen' =>$insertar,
          'idnota'    =>$interno->id,
          'condicion' =>1
        );
        $insertard=$this->resumend_model->insert($datad);

        $datav=array("tipo_estado"=>"03");
        $actualizarv=$this->nota_model->update($datav,$interno->id);
        $contadorTotal++;
      }

      $nombrexml = $empresa->ruc.'-RC-'.$codigo.'-'.$numero;
      $ruta_xml = "downloads/xml/".$nombrexml;
      $detalle=$this->itemsResumen($insertar);
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

        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }else{
        $this->session->set_flashdata('css', 'danger');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }
    } else {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', 'No hay documentos para la fecha '.$fecha);
    }

    redirect(base_url().'facturacion/resumenesi');
  }

  public function anulacionesi()
  {
    if (!$this->acciones(27)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/anulacionesi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $empresa=$this->empresa_model->mostrar();
    $listaf=$this->venta_model->mostrarFechas(array("grupo"=>'01',"tipo_estado<"=>'11',"nulo"=>1));
    $listab=$this->venta_model->mostrarFechas(array("grupo"=>'02',"tipo_estado<"=>'11',"nulo"=>1));
    $this->layout->setTitle('Anulaciones no Enviado');
    $this->layout->view('anulacionesi',compact("anexos","nestablecimiento",'empresa','listaf','listab'));
  }

  public function itemsAnulado($id)
  {
    $detalles=$this->anuladod_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      if ($detalle->idventa!='') {
        $documento=$this->venta_model->mostrar($detalle->idventa);
        $itemx=array
        (
          'idanulado'       =>$id,
          'tcomprobante'    =>$documento->tcomprobante,
          'serie'           =>$documento->serie,
          'numero'          =>$documento->numero,
          'motivo'          =>$detalle->motivo,
        );
      } else {
        $documento=$this->nota_model->mostrar($detalle->idnota);
        $itemx=array
        (
          'idanulado'       =>$id,
          'tcomprobante'    =>$documento->tcomprobante,
          'serie'           =>$documento->serie,
          'numero'          =>$documento->numero,
          'motivo'          =>$detalle->motivo,
        );
      }

      $datos[]=$itemx;
    }
    return $datos;
  }

  public function enviarAnulado($fecha)
  {
    $controlip=$this->controlip('facturacion/enviarAnulado');
    $empresa=$this->empresa_model->mostrar();
    $ventas=$this->venta_model->mostrarTotal(array("grupo"=>'01','femision'=>$fecha,"tipo_estado<"=>'11',"nulo"=>1),"asc");
    $notas=$this->nota_model->mostrarTotal(array("grupo"=>'01','femision'=>$fecha,"tipo_estado<"=>'11',"nulo"=>1),"asc");
    if ($ventas!=NULL || $notas!=NULL) {
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
        'fdocumento'        =>$fecha,
        'identificador'     =>'RA-'.$codigo.'-'.$numero,
        "tipo_estado"       =>"01",
      );
      $insertar=$this->anulado_model->insert($comprobante);

      foreach ($ventas as $interno) {
        $datad=array
        (
          'idanulado' =>$insertar,
          'idventa'   =>$interno->id,
          'motivo'    =>'Anulacion de Operacion'
        );
        $insertard=$this->anuladod_model->insert($datad);

        $datav=array("tipo_estado"=>"13");
        $actualizarv=$this->venta_model->update($datav,$interno->id);
      }

      foreach ($notas as $interno) {
        $datad=array
        (
          'idanulado' =>$insertar,
          'idnota'    =>$interno->id,
          'motivo'    =>'Anulacion de Operacion'
        );
        $insertard=$this->anuladod_model->insert($datad);

        $datav=array("tipo_estado"=>"13");
        $actualizarv=$this->nota_model->update($datav,$interno->id);
      }

      $nombrexml = $empresa->ruc.'-RA-'.$codigo.'-'.$numero;
      $ruta_xml = "downloads/xml/".$nombrexml;
      $detalle=$this->itemsAnulado($insertar);
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

        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }else{
        $this->session->set_flashdata('css', 'danger');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }
    } else {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', 'No hay documentos para la fecha '.$fecha);
    }
    redirect(base_url().'facturacion/anulacionesi');
  }

  public function enviarAnuladob($fecha)
  {
    $controlip=$this->controlip('facturacion/enviarAnuladob');
    $empresa=$this->empresa_model->mostrar();
    $ventas=$this->venta_model->mostrarTotal(array("grupo"=>'02',"nulo"=>1,"tipo_estado<"=>'11','femision'=>$fecha),"asc");
    $notas=$this->nota_model->mostrarTotal(array("grupo"=>'02',"nulo"=>1,"tipo_estado<"=>'11','femision'=>$fecha),"asc");
    if ($ventas!=NULL || $notas!=NULL) {
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
        'fdocumento'        =>$fecha,
        'tproceso'          =>3,
        'identificador'     =>'RC-'.$codigo.'-'.$numero,
        "tipo_estado"       =>"01",
      );
      $insertar=$this->resumen_model->insert($comprobante);

      foreach ($ventas as $interno) {
        $datad=array
        (
          'idresumen' =>$insertar,
          'idventa'   =>$interno->id,
          'condicion' =>3
        );
        $insertard=$this->resumend_model->insert($datad);

        $datav=array("tipo_estado"=>"13");
        $actualizarv=$this->venta_model->update($datav,$interno->id);
      }

      foreach ($notas as $interno) {
        $datad=array
        (
          'idresumen' =>$insertar,
          'idnota'    =>$interno->id,
          'condicion' =>3
        );
        $insertard=$this->resumend_model->insert($datad);

        $datav=array("tipo_estado"=>"13");
        $actualizarv=$this->nota_model->update($datav,$interno->id);
      }

      $nombrexml = $empresa->ruc.'-RC-'.$codigo.'-'.$numero;
      $ruta_xml = "downloads/xml/".$nombrexml;
      $detalle=$this->itemsResumen($insertar);
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

        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }else{
        $this->session->set_flashdata('css', 'danger');
        $this->session->set_flashdata('mensaje', $resultado['mensaje']);
      }
    } else {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', 'No hay documentos para la fecha '.$fecha);
    }
    redirect(base_url().'facturacion/anulacionesb');
  }

	/*=========================================================================================================================
	=                           rectificaciones                          =
	=========================================================================================================================*/
	public function rectificaciones()
	{
    if (!$this->acciones(28)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/rectificaciones');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$filtros=array("tipo_estado"=>"01","rectificar"=>1);
		$listas=$this->venta_model->mostrarTotal($filtros,"desc");
		$listasn=$this->nota_model->mostrarTotal($filtros,"desc");
		$this->layout->setTitle('Comprobante por rectificar');
		$this->layout->view('rectificaciones',compact("anexos","nestablecimiento",'listas','listasn','empresa'));
	}

  public function consultacdr($id)
  {
    $controlip=$this->controlip('facturacion/consultacdr');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->venta_model->mostrar($id);
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->consultarCdr($empresa,$datos->tcomprobante,$datos->serie,$datos->numero,$rutacdr);
    if (!is_numeric($resultado['codigo'])) {
      $this->session->set_flashdata('css', 'danger');
    } elseif (obtenerNumero($resultado['codigo'])===0) {
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"05",
        "respuesta_sunat" =>$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
    } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
      $data=array
      (
        "rectificar"   =>1,
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $this->session->set_flashdata('css', 'info');
    } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"09",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $this->session->set_flashdata('css', 'info');
    } else {// 4000 en adelante Observaciones
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"07",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->venta_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
    }

    $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    redirect(base_url().'facturacion/rectificaciones');
  }

	public function consultancdr($id)
	{
    $controlip=$this->controlip('facturacion/consultancdr');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->nota_model->mostrar($id);
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->consultarCdr($empresa,$datos->tcomprobante,$datos->serie,$datos->numero,$rutacdr);
    if (!is_numeric($resultado['codigo'])) {
      $this->session->set_flashdata('css', 'danger');
    } elseif (obtenerNumero($resultado['codigo'])===0) {
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"05",
        "respuesta_sunat" =>$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
    } elseif (obtenerNumero($resultado['codigo'])<2000) { //Del 0100 al 1999 Excepciones
      $data=array
      (
        "rectificar"   =>1,
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $this->session->set_flashdata('css', 'info');
    } elseif (obtenerNumero($resultado['codigo'])<'4000') { //Del 2000 al 3999 Errores que generan rechazo
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"09",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $this->session->set_flashdata('css', 'info');
    } else {// 4000 en adelante Observaciones
      $data=array
      (
        "has_cdr"       =>1,
        "tipo_estado"   =>"07",
        "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
      );
      $actualizar=$this->nota_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
    }

    $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    redirect(base_url().'facturacion/rectificaciones');
	}

	/*=========================================================================================================================
	=                    resumen diario de boletas                 =
	=========================================================================================================================*/
	public function resumenes()
	{
    if (!$this->acciones(29)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/resumenes');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date('Y-m-d')) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

		$filtros=array('femision>='=>$inicio,'femision<='=>$fin,"tproceso"=>1);
		$listas=$this->resumen_model->mostrarTotal($filtros);
		$this->layout->setTitle('Resumenes Boleta');
		$this->layout->view('resumenes',compact("anexos","nestablecimiento",'empresa','listas','inicio','fin'));
	}

  public function pendienter()
  {
    if (!$this->acciones(29)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/pendienter');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $filtros=array("tproceso"=>1,"tipo_estado"=>'01');
    $listas=$this->resumen_model->mostrarTotal($filtros);
    $this->layout->setTitle('Resumenes Boleta');
    $this->layout->view('pendienter',compact("anexos","nestablecimiento",'empresa','listas'));
  }

  public function consultarResumen($id)
  {
    $controlip=$this->controlip('facturacion/consultarResumen');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->resumen_model->mostrar(array("id"=>$id));
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->ConsultarTicket($empresa,$datos->filename,$datos->ticket,$rutacdr);

    if ($resultado['estado']!=2) {
      if (obtenerNumero($resultado['codigo'])===0) {
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"05",
          "respuesta_sunat" =>$resultado['mensaje'],
        );
        $actualizar=$this->resumen_model->update($data,$id);

        if ($datos->validado==0) {
          $consultas=$this->resumend_model->mostrarTotal($id);
          foreach ($consultas as $consulta) {
            if ($datos->tproceso=='01') {
            $datav=array("tipo_estado"=>'05');
            } else {
            $datav=array("tipo_estado"=>'11');
            }
            if ($consulta->idventa!='') {
              $actualizar=$this->venta_model->update($datav,$consulta->idventa);
            } else {
              $actualizar=$this->nota_model->update($datav,$consulta->idnota);
            }
          }
        }
      }else{
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"09",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->resumen_model->update($data,$id);

        //liberar ventas
        $detalles=$this->resumend_model->mostrarTotal($id);
        foreach ($detalles as $detalle) {
          if ($datos->tproceso=='01') {
            $datav=array("tipo_estado"=>"01");
          } else {
            $datav=array("tipo_estado"=>"05");
          }

          if ($detalle->idventa!="") {
            $actualizarv=$this->venta_model->update($datav,$detalle->idventa);
          } else {
            $actualizarv=$this->nota_model->update($datav,$detalle->idnota);
          }
        }
      }

      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    } else {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    }

    if ($datos->tproceso=='01') {
      redirect(base_url().'facturacion/resumenes');
    }else{
      redirect(base_url().'facturacion/anulaciones');
    }
  }

	public function eliminarr($id)
	{
		if (!$id) {show_404();}
    $controlip=$this->controlip('facturacion/eliminarr');
		$datos=$this->resumen_model->mostrar(array("id"=>$id));
		if ($datos==NULL) {show_404();}

		$empresa=$this->empresa_model->mostrar();
		$detalles=$this->resumend_model->mostrarTotal($id);
		foreach ($detalles as $detalle) {
			$datav=array("tipo_estado"=>"01");
			if ($detalle->idventa!="") {
				$actualizarv=$this->venta_model->update($datav,$detalle->idventa);
			} else {
				$actualizarv=$this->nota_model->update($datav,$detalle->idnota);
			}
		}

		$eliminarr=$this->resumen_model->delete($id);
		$eliminard=$this->resumend_model->delete($id);
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'facturacion/resumenes';
    echo json_encode($proceso);
    exit();
	}

	/*===================================================================================================================
	=                                              anulaciones de documentos                                            =
	===================================================================================================================*/
	public function anulaciones()
	{
    if (!$this->acciones(30)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/anulaciones');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date('Y-m-d')) ;
		$fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

		$filtrosf=array('femision>='=>$inicio,'femision<='=>$fin);
		$listasf=$this->anulado_model->mostrarTotal($filtrosf);

		$filtrosb=array('femision>='=>$inicio,'femision<='=>$fin,"tproceso"=>3);
		$listasb=$this->resumen_model->mostrarTotal($filtrosb);
		$this->layout->setTitle('Anulaciones Venta');
		$this->layout->view('anulaciones',compact("anexos","nestablecimiento",'listasf','listasb','inicio','fin','empresa'));
	}

  public function pendientea()
  {
    if (!$this->acciones(30)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/pendientea');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $filtrosf=array("tipo_estado"=>'01');
    $listasf=$this->anulado_model->mostrarTotal($filtrosf);

    $filtrosb=array("tproceso"=>3,"tipo_estado"=>'01');
    $listasb=$this->resumen_model->mostrarTotal($filtrosb);
    $this->layout->setTitle('Anulaciones Venta');
    $this->layout->view('pendientea',compact("anexos","nestablecimiento",'empresa','listasf','listasb'));
  }

  public function consultarBaja($id)
  {
    $controlip=$this->controlip('facturacion/consultarBaja');
    $empresa=$this->empresa_model->mostrar();
    $datos=$this->anulado_model->mostrar(array("id"=>$id));
    $rutacdr="downloads/cdr/";
    $resultado = $this->apiFacturacion->ConsultarTicket($empresa,$datos->filename,$datos->ticket,$rutacdr);

    if ($resultado['estado']!=2) {
      if (obtenerNumero($resultado['codigo'])===0) {
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"05",
          "respuesta_sunat" =>$resultado['mensaje'],
        );
        $actualizar=$this->anulado_model->update($data,$id);

        if ($datos->validado==0) {
          $consultas=$this->anuladod_model->mostrarTotal($id);
          foreach ($consultas as $consulta) {
            $datav=array("tipo_estado"=>11);
            if ($consulta->idventa!='') {
              $actualizar=$this->venta_model->update($datav,$consulta->idventa);
            } else {
              $actualizarv=$this->nota_model->update($datav,$consulta->idnota);
            }
          }
        }
      }else{
        $data=array
        (
          "has_cdr"       =>1,
          "tipo_estado"   =>"09",
          "respuesta_sunat" =>$resultado['codigo'].' '.$resultado['mensaje'],
        );
        $actualizar=$this->anulado_model->update($data,$id);

        //liberar ventas
        $detalles=$this->anuladod_model->mostrarTotal($id);
        foreach ($detalles as $detalle) {
          $datav=array("tipo_estado"=>"05");
          if ($detalle->idventa!='') {
            $actualizarv=$this->venta_model->update($datav,$detalle->idventa);
          } else {
            $actualizarv=$this->nota_model->update($datav,$detalle->idnota);
          }
        }
      }

      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    } else {
      $this->session->set_flashdata('css', 'danger');
      $this->session->set_flashdata('mensaje', $resultado['mensaje']);
    }
    redirect(base_url().'facturacion/anulaciones');
  }

  public function eliminara($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('facturacion/eliminara');
    $datos=$this->anulado_model->mostrar(array("id"=>$id));
    if ($datos==NULL) {show_404();}

    $detalles=$this->anuladod_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      $datav=array("tipo_estado"=>"05");
      if ($detalle->idventa!='') {
        $actualizarv=$this->venta_model->update($datav,$detalle->idventa);
      } else {
        $actualizarv=$this->nota_model->update($datav,$detalle->idnota);
      }
    }

    $eliminarr=$this->anulado_model->delete($id);
    $eliminard=$this->anuladod_model->delete($id);
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'facturacion/anulaciones';
    echo json_encode($proceso);
    exit();
  }

  public function eliminarb($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('facturacion/eliminarb');
    $datos=$this->resumen_model->mostrar(array("id"=>$id));
    if ($datos==NULL) {show_404();}

    $detalles=$this->resumend_model->mostrarTotal($id);
    foreach ($detalles as $detalle) {
      $datav=array("tipo_estado"=>"05");
      if ($detalle->idventa!='') {
        $actualizarv=$this->venta_model->update($datav,$detalle->idventa);
      } else {
        $actualizarv=$this->nota_model->update($datav,$detalle->idnota);
      }
    }

    $eliminarr=$this->resumen_model->delete($id);
    $eliminard=$this->resumend_model->delete($id);
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'facturacion/anulaciones';
    echo json_encode($proceso);
    exit();
  }

	/*===================================================================================================================
	=                                              consistencia de documentos                                           =
	===================================================================================================================*/
	public function consistencia()
	{
    if (!$this->acciones(31)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/consistencia');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$listas=$this->serie_model->mostrarTotal(array("idestablecimiento"=>$this->session->userdata("predeterminado")));
    $estados=$this->testado_model->mostrarTotal();
		$this->layout->setTitle('Comprobante Emitido');
		$this->layout->view('consistencia',compact("anexos","nestablecimiento",'empresa','listas',"estados"));
	}

	/*===================================================================================================================
	=                                                    validaciones                                                   =
	===================================================================================================================*/
	public function busSerie()
	{
		if ($this->input->post())
		{
			$datos=$this->serie_model->mostrarTotal(array('tcomprobante'=>$this->input->post('nro',true)));
			echo json_encode($datos);
		}
		else
		{
			show_404();
		}
	}

	public function generarToken()
	{
		$empresa=$this->empresa_model->mostrar();
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://api-seguridad.sunat.gob.pe/v1/clientesextranet/'.$empresa->id_validador.'/oauth2/token/',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => 'grant_type=client_credentials&scope=https://api.sunat.gob.pe/v1/contribuyente/contribuyentes&client_id='.$empresa->id_validador.'&client_secret='.$empresa->secret_validador.'',
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/x-www-form-urlencoded'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response,true);
	}

	public function consultarValidez($tcomprobante,$serie,$numero,$femision,$monto)
	{
		$empresa=$this->empresa_model->mostrar();
		$data_json=array(
			"numRuc"=> $empresa->ruc,
			"codComp"=> $tcomprobante,
			"numeroSerie"=> $serie,
			"numero"=> $numero,
			"fechaEmision"=> $femision,
			"monto"=> $monto
		);
		$data_json=json_encode($data_json);

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.sunat.gob.pe/v1/contribuyente/contribuyentes/".$empresa->ruc."/validarcomprobante",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>$data_json,
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "Authorization: Bearer ".$empresa->token_validador
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return json_decode($response,true);
	}

	public function validador()
	{
    if (!$this->acciones(32)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/validador');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$limite=fechaHoraria('+ '.$empresa->expires_validador.' second ',$empresa->fecha_validador);
		if (date("Y-m-d H:i:s")>=$limite) {
			$respuesta_json=self::generarToken();

			$data=array
			(
				'token_validador'	=>$respuesta_json["access_token"],
				'fecha_validador'	=>date("Y-m-d H:i:s"),
				'expires_validador'	=>$respuesta_json["expires_in"],
			);
			$actualizar=$this->empresa_model->update($data);
		}

		$comprobantec=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));
		$comprobanten=$this->tcomprobante_model->mostrarLimite(array("formulario"=>2));
		$nseries=$this->serie_model->mostrarTotal(array('tcomprobante'=>'03'));

		$listas=array();
		if ($this->input->post())
		{
			if ($this->input->post('comprobante',true)=='01' || $this->input->post('comprobante',true)=='03') {
				if ($this->input->post('fin',true)) {
					$filtros=array('serie'=>$this->input->post('serie',true),'numero>='=>$this->input->post('inicio',true),'numero<='=>$this->input->post('fin',true));
				} else {
					$filtros=array('serie'=>$this->input->post('serie',true),'numero'=>$this->input->post('inicio',true));
				}
				$datos=$this->venta_model->mostrarTotal($filtros,"asc");
			}else{
				if ($this->input->post('fin',true)) {
					$filtros=array('serie'=>$this->input->post('serie',true),'numero>='=>$this->input->post('inicio',true),'numero<='=>$this->input->post('fin',true));
				} else {
					$filtros=array('serie'=>$this->input->post('serie',true),'numero'=>$this->input->post('inicio',true));
				}
				$datos=$this->nota_model->mostrarTotal($filtros,"asc");
			}

			foreach ($datos as $dato) {
				$respuesta_json=self::consultarValidez($dato->tcomprobante,$dato->serie,$dato->numero,FormatoFecha($dato->femision),$dato->total);
				//var_dump($respuesta_json);
				if ($respuesta_json["data"]==NULL) {
					break;
				}

				$estados=$this->tvalidacion_model->mostrar(array("tipo"=>"E","id"=>$respuesta_json["data"]["estadoCp"]));
				if($respuesta_json["data"]["estadoCp"] == 1){
					$badges='badge-success';
				}else if($respuesta_json["data"]["estadoCp"] == 2){
					$badges='badge-danger';
				}else{
					$badges='badge-primary';
				}
				$estadod=$this->testado_model->mostrar($dato->tipo_estado);

				$anexos['id']=$dato->id;
				$anexos['ncomprobante']=$dato->ncomprobante;
				$anexos['serie']=$dato->serie;
				$anexos['numero']=$dato->numero;
				$anexos['femision']=$dato->femision;
				$anexos['cliente']=$dato->cliente;
				$anexos['badged']=$estadod->badge;
				$anexos['estadod']=$estadod->descripcion;
				$anexos['badges']=$badges;
				$anexos['estados']=$estados->descripcion;
				$anexos['estadocp']=$respuesta_json["data"]["estadoCp"];
				array_push($listas,$anexos);
			}
		}
		$this->layout->setTitle('Validador Documentos');
		$this->layout->view('validador',compact("anexos","nestablecimiento",'empresa','comprobantec','comprobanten','nseries','listas'));
	}

  public function regularizar()
  {
    if ($this->input->post())
    {
      for ($i=0; $i < count($this->input->post('id',true)) ; $i++) {
        if ($this->input->post('estadocp',true)[$i]==1) {
          if ($this->input->post('tipo',true)=='01' || $this->input->post('tipo',true)=='03') {
            $data=array('tipo_estado'=>'05');
            $actualizar=$this->venta_model->update($data,$this->input->post('id',true)[$i]);
          }elseif($this->input->post('tipo',true)=='07' || $this->input->post('tipo',true)=='08'){
            $datos=$this->nota_model->mostrar($this->input->post('id',true)[$i]);
            $data=array('tipo_estado'=>'05');
            $actualizar=$this->nota_model->update($data,$this->input->post('id',true)[$i]);
          }
        }elseif ($this->input->post('estadocp',true)[$i]==2) {
          if ($this->input->post('tipo',true)=='01' || $this->input->post('tipo',true)=='03') {
            $data=array("tipo_estado"=>"11");
            $actualizar=$this->venta_model->update($data,$this->input->post('id',true)[$i]);
          }elseif($this->input->post('tipo',true)=='07' || $this->input->post('tipo',true)=='08'){
            $data=array("tipo_estado"=>"11");
            $actualizar=$this->nota_model->update($data,$this->input->post('id',true)[$i]);
          }
        }
      }
      redirect(base_url()."facturacion/validador");
    }
  }

	public function validadorb()
	{
    if (!$this->acciones(32)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/validadorb');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$limite=fechaHoraria('+ '.$empresa->expires_validador.' second ',$empresa->fecha_validador);
		if (date("Y-m-d H:i:s")>=$limite) {
			$respuesta_json=self::generarToken();

			$data=array
			(
				'token_validador'	=>$respuesta_json["access_token"],
				'fecha_validador'	=>date("Y-m-d H:i:s"),
				'expires_validador'	=>$respuesta_json["expires_in"],
			);
			$actualizar=$this->empresa_model->mostrarUpdate($data);
		}

		$listas=array();
		$datos=array();
		if ($this->input->post())
		{
			$datos=$this->resumen_model->mostrar(array("ticket"=>$this->input->post('ticket',true)));
			$detalles=$this->resumend_model->mostrarTotal($datos->id);
			foreach ($detalles as $detalle) {
				if ($detalle->idventa!="") {
					$dato=$this->venta_model->mostrar($detalle->idventa);
				} else {
					$dato=$this->nota_model->mostrar($detalle->idnota);
				}
				$respuesta_json=self::consultarValidez($dato->tcomprobante,$dato->serie,$dato->numero,FormatoFecha($dato->femision),$dato->total);
				//var_dump($respuesta_json);
				if ($respuesta_json["data"]==NULL) {
					break;
				}
				$estados=$this->tvalidacion_model->mostrar(array("tipo"=>"E","id"=>$respuesta_json["data"]["estadoCp"]));

				if($respuesta_json["data"]["estadoCp"] == 1){
					$badges='badge-success';
				}elseif($respuesta_json["data"]["estadoCp"] == 2){
					$badges='badge-danger';
				}else{
					$badges='badge-primary';
				}
				$estadod=$this->testado_model->mostrar($dato->tipo_estado);

				$anexos['id']=$dato->id;
				$anexos['tipo']=$dato->tcomprobante;
				$anexos['ncomprobante']=$dato->ncomprobante;
				$anexos['serie']=$dato->serie;
				$anexos['numero']=$dato->numero;
				$anexos['femision']=$dato->femision;
				$anexos['cliente']=$dato->cliente;
				$anexos['estadod']=$estadod->descripcion;
				$anexos['badges']=$badges;
				$anexos['estados']=$estados->descripcion;
				$anexos['estadocp']=$respuesta_json["data"]["estadoCp"];
				array_push($listas,$anexos);
			}
		}
		$this->layout->setTitle('Validador Resumenes');
		$this->layout->view('validadorb',compact("anexos","nestablecimiento",'empresa','listas',"datos"));
	}

  public function regularizarb()
  {
    if ($this->input->post())
    {
      $nro=0; $nra=0;
      for ($i=0; $i < count($this->input->post('id',true)) ; $i++) {
        if ($this->input->post('estadocp',true)[$i]==1) {
          $data=array('tipo_estado' =>'05');
          if ($this->input->post('tipo',true)[$i]=='03') {
            $actualizar=$this->venta_model->update($data,$this->input->post('id',true)[$i]);
          }elseif($this->input->post('tipo',true)[$i]=='07' || $this->input->post('tipo',true)[$i]=='08'){
            $actualizar=$this->nota_model->update($data,$this->input->post('id',true)[$i]);
          }
          $nro++;
        }elseif ($this->input->post('estadocp',true)[$i]==2) {
          $data=array("tipo_estado"  =>"11");
          if ($this->input->post('tipo',true)[$i]=='03') {
            $actualizar=$this->venta_model->update($data,$this->input->post('id',true)[$i]);
          }elseif($this->input->post('tipo',true)[$i]=='07' || $this->input->post('tipo',true)[$i]=='08'){
            $actualizar=$this->nota_model->update($data,$this->input->post('id',true)[$i]);
          }
          $nra++;
        }
      }

      if ($nro>0 || $nra>0) {
        $empresa=$this->empresa_model->mostrar();
        $datos=$this->resumen_model->mostrar(array("id"=>$this->input->post('idresumen',true)));
        $datac=array('validado'=>1);
        $actualizarr=$this->resumen_model->update($datac,$this->input->post('idresumen',true));
      }
      redirect(base_url()."facturacion/validadorb");
    }
  }

	public function validadora()
	{
    if (!$this->acciones(32)){redirect(base_url()."inicio");}
    $controlip=$this->controlip('facturacion/validadora');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		$empresa=$this->empresa_model->mostrar();
		$limite=fechaHoraria('+ '.$empresa->expires_validador.' second ',$empresa->fecha_validador);
		if (date("Y-m-d H:i:s")>=$limite) {
			$respuesta_json=self::generarToken();

			$data=array
			(
				'token_validador'	=>$respuesta_json["access_token"],
				'fecha_validador'	=>date("Y-m-d H:i:s"),
				'expires_validador'	=>$respuesta_json["expires_in"],
			);
			$actualizar=$this->empresa_model->mostrarUpdate($data);
		}

		$listas=array();
		$datos=array();
		if ($this->input->post())
		{
			$datos=$this->anulado_model->mostrar(array("ticket"=>$this->input->post('ticket',true)));
      $detalles=$this->anuladod_model->mostrarTotal($datos->id);
      foreach ($detalles as $detalle) {
  			if ($detalle->idventa!="") {
  				$dato=$this->venta_model->mostrar($detalle->idventa);
  			} else {
  				$dato=$this->nota_model->mostrar($detalle->idnota);
  			}

  			$respuesta_json=self::consultarValidez($dato->tcomprobante,$dato->serie,$dato->numero,FormatoFecha($dato->femision),$dato->total);
  			//var_dump($respuesta_json);

        if ($respuesta_json["data"]==NULL) {
          break;
        }
				$estados=$this->tvalidacion_model->mostrar(array("tipo"=>"E","id"=>$respuesta_json["data"]["estadoCp"]));

				if($respuesta_json["data"]["estadoCp"] == 1){
					$badges='badge-success';
				}elseif($respuesta_json["data"]["estadoCp"] == 2){
					$badges='badge-danger';
				}else{
					$badges='badge-primary';
				}
				$estadod=$this->testado_model->mostrar($dato->tipo_estado);

				$anexos['id']=$dato->id;
				$anexos['tipo']=$dato->tcomprobante;
				$anexos['ncomprobante']=$dato->ncomprobante;
				$anexos['serie']=$dato->serie;
				$anexos['numero']=$dato->numero;
				$anexos['femision']=$dato->femision;
				$anexos['cliente']=$dato->cliente;
				$anexos['estadod']=$estadod->descripcion;
				$anexos['badges']=$badges;
				$anexos['estados']=$estados->descripcion;
				$anexos['estadocp']=$respuesta_json["data"]["estadoCp"];
				array_push($listas,$anexos);
      }
		}
		$this->layout->setTitle('Validador Anulaciones');
		$this->layout->view('validadora',compact("anexos","nestablecimiento",'empresa','listas',"datos"));
	}

  public function regularizara()
  {
    if ($this->input->post())
    {
      $nra=0;
      for ($i=0; $i < count($this->input->post('id',true)) ; $i++) {
        if ($this->input->post('estadocp',true)[$i]==2) {
          $data=array('nulo'=>1, "tipo_estado"=>"11");
          if ($this->input->post('tipo',true)[$i]=='01') {
            $actualizar=$this->venta_model->update($data,$this->input->post('id',true)[$i]);
          }elseif($this->input->post('tipo',true)[$i]=='07' || $this->input->post('tipo',true)[$i]=='08'){
            $actualizar=$this->nota_model->update($data,$this->input->post('id',true)[$i]);
          }
          $nra++;
        }
      }

      if ($nra>0) {
        $empresa=$this->empresa_model->mostrar();
        $datos=$this->anulado_model->mostrar(array("id"=>$this->input->post('idresumen',true)));
        $datac=array('validado'=>1);
        $actualizarr=$this->anulado_model->update($datac,$this->input->post('idresumen',true));
      }
      redirect(base_url()."facturacion/validadora");
    }
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
        'ip'  =>$ip,
        'fecha' =>time(),
        'tiempo'=>$tiempo,
        'nombre'=>$nomcpu,
        'soperativo'=>$info["os"],
        'navegador'=>$info["browser"],
        'dispositivo'=>$info["device"],
        'pagina'=>$pagina,
        'user'  =>$this->session->userdata('user'),
      );
      $insertar=$this->controlip_model->insertar($data);
    }else{
      $data=array
      (
        'ip'  =>$ip,
        'fecha' =>time(),
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(21)){redirect(base_url()."inicio");}

		$this->layout->setLayout("principal");
	}

	public function index()
	{
    $controlip=$this->controlip('empresa');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		if ($this->input->post())
		{
			$data=array
			(
				"ruc"								=>$this->input->post("ruc",true),
				"nombres"						=>$this->input->post("nombres",true),
				"ncomercial"				=>$this->input->post("ncomercial",true),
				"producto"					=>$this->input->post("producto",true),
				"dscto"							=>$this->input->post("dscto",true),
				"ticket"						=>$this->input->post("ticket",true),
				"pie"								=>$this->input->post("pie",true),
				"piec"							=>$this->input->post("piec",true),
				"detraccion"				=>$this->input->post("detraccion",true),
				"lventa"						=>$this->input->post("lventa",true),
				"pesencial"					=>$this->input->post("pesencial",true),
			);
			$guardar=$this->empresa_model->update($data);

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");

			if (isset($_FILES['logo']) && $_FILES['logo']['tmp_name']!='') {
				$nombreCompleto=$_FILES['logo']['name'];

				$config['upload_path']   = './public/logo/';
				$config['overwrite'] = TRUE;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size']      = 2000;
				$config['max_width']     = 0;
				$config['max_height']    = 0;
				$config['file_name']     = $nombreCompleto;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('logo')) {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', $this->upload->display_errors());
				} else {
					$ruta= addslashes(base_url()."public/logo/".$nombreCompleto);
					$datai=array('logo'=>$ruta);
					$imagen=$this->upload->data();
					$guardar=$this->empresa_model->update($datai);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Se subio con exito la portada '.$imagen["file_name"]);
				}
			}

			if (isset($_FILES['lticket']) && $_FILES['lticket']['tmp_name']!='') {
				$nombreCompleto=$_FILES['lticket']['name'];

				$config['upload_path']   = './public/logo/';
				$config['overwrite'] = TRUE;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['max_size']      = 2000;
				$config['max_width']     = 0;
				$config['max_height']    = 0;
				$config['file_name']     = $nombreCompleto;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('lticket')) {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', $this->upload->display_errors());
				} else {
					$ruta= addslashes(base_url()."public/logo/".$nombreCompleto);
					$datai=array('lticket'=>$ruta,);
					$imagen=$this->upload->data();
					$guardar=$this->empresa_model->update($datai);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Se subio con exito logo '.$imagen["file_name"]);
				}
			}
      $control_movimiento=$this->movimientos('empresa','Edito datos generales de la empresa');
		}

		$empresa=$this->empresa_model->mostrar();
		$this->layout->setTitle("Empresa");
		$this->layout->view("index",compact("anexos","nestablecimiento","empresa"));
	}

	public function facturacion()
	{
    $controlip=$this->controlip('empresa/facturacion');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		if ($this->input->post())
		{
			$data=array
			(
				'envio_automatico'	=>valor_check($this->input->post('envio',true)),
				'envio_boleta'			=>valor_check($this->input->post('boleta',true)),
				'envio_guia'				=>valor_check($this->input->post('guia',true)),
			);
			if ($this->input->post('edicion',true)==1) {
				$data['tipo_soap']=$this->input->post('soap',true);
				$data['usuario_soap']=$this->input->post('usuario',true);
				$data['clave_soap']=$this->input->post('secundario',true);
				$data['certificado_clave']=$this->input->post('clave',true);
				$data['certificado_vence']=valor_fecha($this->input->post('vencimiento',true));
				$data['id_gre']=$this->input->post('idguia',true);
				$data['secret_gre']=$this->input->post('secretg',true);
			}
			$guardar=$this->empresa_model->update($data);

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
      $control_movimiento=$this->movimientos('empresa/facturacion','Edito datos facturacion de la empresa');

			if (isset($_FILES['certificado']) && $_FILES['certificado']['tmp_name']!='') {
				$nombreCompleto=$_FILES['certificado']['name'];

				$config['upload_path']   = './downloads/certificado/';
				$config['overwrite'] = TRUE;
				$config['allowed_types'] = '*';
				$config['max_size']      = 2000;
				$config['max_width']     = 0;
				$config['max_height']    = 0;
				$config['file_name']     = $nombreCompleto;
				$this->load->library('upload', $config);

				if (!$this->upload->do_upload('certificado')) {
					$this->session->set_flashdata('css', 'danger');
					$this->session->set_flashdata('mensaje', $this->upload->display_errors());
				} else {
					//$ruta= addslashes(base_url()."downloads/certificado/".$nombreCompleto);
					$datai=array('certificado'=>$nombreCompleto);
					$imagen=$this->upload->data();
					$guardar=$this->empresa_model->update($datai);
					$this->session->set_flashdata('css', 'success');
					$this->session->set_flashdata('mensaje', 'Se subio con exito certificado '.$imagen["file_name"]);
				}
			}
		}

		$empresa=$this->empresa_model->mostrar();
		$this->layout->setTitle("Empresa");
		$this->layout->view("facturacion",compact("anexos","nestablecimiento","empresa"));
	}

	public function certificadod()
	{
    $controlip=$this->controlip('empresa/certificadod');
		$datos=$this->empresa_model->mostrar();
		if ($datos==NULL) {show_404();}

		$filename="./downloads/certificado/".$datos->certificado;
		if (file_exists($filename)) {
		    $success = unlink($filename);
		}
		$eliminar=$this->empresa_model->update(array('certificado'=>NULL));
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'empresa';
    echo json_encode($proceso);
    exit();
	}

	public function avanzado()
	{
    $controlip=$this->controlip('empresa/avanzado');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

		if ($this->input->post())
		{
			$data=array
			(
				"compra"					=>valor_check($this->input->post("compra",true)),
				"pventa"					=>valor_check($this->input->post("pventa",true)),
				"arqueo"					=>valor_check($this->input->post("arqueo",true)),
				"spuntos"					=>valor_check($this->input->post("spuntos",true)),
				"pcompra"					=>valor_check($this->input->post("pcompra",true)),
				"vbonificar"			=>valor_check($this->input->post("vbonificar",true)),
				"lstock"					=>valor_check($this->input->post("lstock",true)),

				"gunidad"					=>$this->input->post("gunidad",true),
				"gblister"				=>$this->input->post("gblister",true),
				"gcaja"						=>$this->input->post("gcaja",true),
			);
			$guardar=$this->empresa_model->update($data);
      $control_movimiento=$this->movimientos('empresa','Edito datos avanzados de la empresa');

			$this->session->set_flashdata("css", "success");
			$this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
		}

		$empresa=$this->empresa_model->mostrar();
		$contador=$this->establecimiento_model->contador();
		$establecimientos=$this->establecimiento_model->mostrarTotal(array('estado'=>1));
		$this->layout->setTitle("Empresa");
		$this->layout->view("avanzado",compact("anexos","nestablecimiento","empresa","contador","establecimientos"));
	}

	public function precio()
	{
		if ($this->input->post())
		{
			$empresa=$this->empresa_model->mostrar();
			if ($empresa->pestablecimiento!=valor_check($this->input->post("pestablecimiento",true))) {
				$data=array("pestablecimiento"=>valor_check($this->input->post("pestablecimiento",true)));
				$guardar=$this->empresa_model->update($data);

				if (valor_check($this->input->post("pestablecimiento",true))==1) {
		      $catalogos=$this->producto_model->mostrarCatalogo();
		      foreach ($catalogos as $catalogo) {
		        $datas=array
		        (
		          'venta'       =>$catalogo->venta,
		          'pventa'      =>$catalogo->pventa,
		          'pblister'    =>$catalogo->pblister,
		        );
		        $actualizari=$this->inventario_model->update($datas,array("idproducto"=>$catalogo->id));
		      }
		      $control_movimiento=$this->movimientos('empresa','Se separo los precios por establecimientos de la empresa');
					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Se separo los precios por establecimientos de la empresa!");
		    }

		    if (valor_check($this->input->post("pestablecimiento",true))==0) {
		    	$catalogos=$this->inventario_model->mostrarTotal(array("idestablecimiento"=>$this->input->post("establecimiento",true)));
		      foreach ($catalogos as $catalogo) {
		        $datas=array
		        (
		          'venta'       =>$catalogo->venta,
		          'pventa'      =>$catalogo->pventa,
		          'pblister'    =>$catalogo->pblister,
		        );
		        $actualizar=$this->producto_model->update($datas,$catalogo->idproducto);
		      }

		      $control_movimiento=$this->movimientos('empresa','Se unifico los precios por establecimientos de la empresa');
					$this->session->set_flashdata("css", "success");
					$this->session->set_flashdata("mensaje", "Se unifico los precios por establecimientos de la empresa!");
		    }
			}else{
					$this->session->set_flashdata("css", "danger");
					$this->session->set_flashdata("mensaje", "No se puede realizar el mismo proceso para el precio!");
			}
	    redirect(base_url()."empresa/avanzado");
	  }
	}

	public function configuracion()
	{
    $controlip=$this->controlip('empresa/configuracion');
		$anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
		$empresa=$this->empresa_model->mostrar();

		$this->layout->setTitle("Empresa");
		$this->layout->view("configuracion",compact("anexos","nestablecimiento","empresa"));
	}

	public function backup()
  {
    $this->load->dbutil();
    //Hacemos el backup de los datos que nos interesan
    $prefs = array(
      'tables'        => array(),                     // Listado de tablas. 'tabla1', 'tabla2'
      'ignore'        => array(),                     // Listado de tablas a omitir
      'format'        => 'zip',                       // gzip, zip, txt
      'filename'      => 'backup.zip',                // Nombre del fichero - SOLAMENTE PARA FICHEROS ZIP
      'add_drop'      => TRUE,                        // Si agregar la sentencia DROP TABLE al backup
      'add_insert'    => TRUE,                        // Si agregar la sentencia INSERT al backup
      'newline'       => "\n"                         // Salto de línea
    );
    $backup = $this->dbutil->backup($prefs);

    //Cargamos el helper file y generamos un fichero
    //Esta parte la usamos solo si deseamos guardarlo en servidor
    // $this->load->helper('file');
    // write_file('downloads/backup/backup.zip', $backup);

    //Cargamos el helper download y forzamos la descarga
    $this->load->helper('download');
    force_download('backup.zip', $backup);
  }

  public function directorioPdf()
  {
    $this->load->library('zip');
    $this->zip->read_dir('downloads/pdf', FALSE);
    $this->zip->download('pdf.zip');
  }

  public function directorioXml()
  {
    $this->load->library('zip');
    $this->zip->read_dir('downloads/xml', FALSE);
    $this->zip->download('xml.zip');
  }

  public function directorioCdr()
  {
    $this->load->library('zip');
    $this->zip->read_dir('downloads/cdr', FALSE);
    $this->zip->download('cdr.zip');
  }

  public function busDetraccion()
  {
		if ($this->input->post())
		{
	    $datos=$this->empresa_model->mostrar();
	    echo $datos->detraccion;
		}
		else
		{
			show_404();
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

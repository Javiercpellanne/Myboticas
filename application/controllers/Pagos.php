<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Pagos extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(9)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("tpago_model");
    $this->load->model("compra_model");
    $this->load->model("pago_model");
  }

  public function index()
  {
    $controlip=$this->controlip('pagos');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-7 day',date('Y-m-d')) ;
    $fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date('Y-m-d') ;

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin,"nulo"=>0);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->pago_model->mostrarTotal($filtros,"desc");
    $this->layout->setTitle("Cobros");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function pagar()
  {
    $controlip=$this->controlip('pagar');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"cancelado"=>0);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->compra_model->mostrarTotal($filtros);
    $this->layout->setTitle("Cuentas por pagar");
    $this->layout->view("pagar",compact("anexos","nestablecimiento",'empresa',"listas"));
  }

  public function pagari($id)
  {
    $controlip=$this->controlip('pagar/pagari');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $datos=$this->compra_model->mostrar($id);
    if ($this->input->post())
    {
      if ($this->input->post('importe',true)>$this->input->post('saldo',true)) {
        $this->session->set_flashdata("css", "danger");
        $this->session->set_flashdata("mensaje", "El monto es mucho mayor al que tiene que pagar");
      } else {
        $data=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          "iduser"            =>$this->session->userdata('id'),
          "idcompra"          =>$id,
          "femision"          =>date("Y-m-d"),
          "total"             =>$this->input->post("importe",true),
          "idtpago"           =>$this->input->post("mpago",true),
          "documento"         =>$this->input->post("documento",true),
        );
        $insertar=$this->pago_model->insert($data);

        $pagos=$this->pago_model->montoTotal(array("nulo"=>0,"idcompra"=>$id));
        $saldo=$datos->total-$pagos->total;
        if ($saldo==0) {
          $datac=array("cancelado"=>1);
          $actualizar=$this->compra_model->update($datac,$id);
        }
        $control_movimiento=$this->movimientos('pagar/pagari','Registro pago de documento '.$datos->serie.'-'.$datos->numero);

        $this->session->set_flashdata("css", "success");
        $this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
      }
      redirect(base_url()."pagos");
    }

    $pagos=$this->pago_model->mostrarTotal(array("p.nulo"=>0,"p.idcompra"=>$id));
    $mpagos=$this->tpago_model->mostrarTotal();
    $this->layout->setTitle("Cuentas por pagar");
    $this->layout->view("pagari",compact("anexos","nestablecimiento",'empresa',"datos","pagos","mpagos"));
  }

  public function excelpagar()
  {
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"nulo"=>0,"cancelado"=>0);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->compra_model->mostrarTotal($filtros,"desc");

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("ingreso");

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

    $sheet->setCellValueByColumnAndRow(1, 1,"Id");
    $sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
    $sheet->setCellValueByColumnAndRow(3, 1,"Comprobante");
    $sheet->setCellValueByColumnAndRow(4, 1,"Proveedor");
    $sheet->setCellValueByColumnAndRow(5, 1,"Importe");
    $sheet->setCellValueByColumnAndRow(6, 1,"Cobrado");
    $sheet->setCellValueByColumnAndRow(7, 1,"Saldo");

    $i=2; $j=1;
    foreach ($listas as $lista) {
      foreach(range("A","G") as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
        $sheet->getStyle($columnID.$i)->applyFromArray($styleArray);
      }
      $pagado=$this->pago_model->montoTotal(array("idcompra"=>$lista->id));

      $sheet->setCellValue("A".$i,$j);
      $sheet->setCellValue("B".$i,$lista->femision);
      $sheet->setCellValue("C".$i,$lista->serie.'-'.$lista->numero);
      $sheet->setCellValue("D".$i,$lista->proveedor);
      $sheet->setCellValue("E".$i,$lista->total);
      $sheet->setCellValue("F".$i,$pagado->total);
      $sheet->setCellValue("G".$i,$lista->total-$pagado->total);
      $i++; $j++;
    }

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_PAGAR.xlsx"');
    $writer->save('php://output');  // download file
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

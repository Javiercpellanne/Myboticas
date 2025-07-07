<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Ingreso extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(25)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("tpago_model");
    $this->load->model("tingreso_model");
    $this->load->model("ingreso_model");
    $this->load->library("mytcpdf");
  }

  public function index()
  {
    $controlip=$this->controlip('ingreso');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post('inicio',true)!=null ? $this->input->post('inicio',true) : SumarFecha('-15 day',date("Y-m-d")) ;
    $fin=$this->input->post('fin',true)!=null ? $this->input->post('fin',true) : date("Y-m-d") ;

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->ingreso_model->mostrarTotal($filtros);
    $this->layout->setTitle("Ingresos");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function ingresoi()
  {
    $controlip=$this->controlip('ingreso/ingresoi');
    if ($this->input->post())
    {
      $data=array
      (
        'idestablecimiento' =>$this->session->userdata("predeterminado"),
        "iduser"            =>$this->session->userdata('id'),
        "femision"          =>$this->input->post("fecha",true),
        "comprobante"       =>$this->input->post("comprobante",true),
        "numero"            =>$this->input->post("numero",true),
        "motivo"            =>$this->input->post("motivo",true),
        "total"             =>$this->input->post("importe",true),
        "idtpago"           =>$this->input->post("mpago",true),
      );
      $insertar=$this->ingreso_model->insert($data);
      $this->session->set_flashdata("css", "success");
      $this->session->set_flashdata("mensaje", "Los datos se han guardado exitosamente!");
      $control_movimiento=$this->movimientos('ingreso/ingresoi','Registro ingreso nro '.$insertar);
      echo base_url()."ingreso";
    }

    $comprobantes=$this->tingreso_model->mostrarTotal();
    $mpagos=$this->tpago_model->mostrarTotal();
    $this->layout->setLayout("blanco");
    $this->layout->view("ingresoi",compact("mpagos","comprobantes"));
  }

  public function ingresod($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('ingreso/ingresod');
    $datos=$this->ingreso_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $eliminar=$this->ingreso_model->delete($id);
    $control_movimiento=$this->movimientos('ingreso/ingresod','Elimino ingreso nro '.$id);
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'ingreso';
    echo json_encode($proceso);
    exit();
  }

  public function pdfingreso($inicio,$fin)
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->ingreso_model->mostrarTotal($filtros);
    $this->layout->setLayout("blanco");
    $this->layout->view("pdfingreso",compact("empresa","nestablecimiento","listas","inicio","fin"));
  }

  public function excelingreso($inicio,$fin)
  {
    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
    if ($this->session->userdata("tipo")!='admin') {$filtros['iduser']=$this->session->userdata("id");}
    $listas=$this->ingreso_model->mostrarTotal($filtros);

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

    foreach(range("A","F") as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
        $sheet->getStyle($columnID."1")->applyFromArray($styleArray);
    }

    $sheet->setCellValueByColumnAndRow(1, 1,"Id");
    $sheet->setCellValueByColumnAndRow(2, 1,"Fecha");
    $sheet->setCellValueByColumnAndRow(3, 1,"Numero");
    $sheet->setCellValueByColumnAndRow(4, 1,"Motivo");
    $sheet->setCellValueByColumnAndRow(5, 1,"Importe");

    $i=2; $j=1;
    foreach ($listas as $lista) {
      $sheet->getStyle("A".$i)->applyFromArray($styleArray);
      $sheet->getStyle("B".$i)->applyFromArray($styleArray);
      $sheet->getStyle("C".$i)->applyFromArray($styleArray);
      $sheet->getStyle("D".$i)->applyFromArray($styleArray);
      $sheet->getStyle("E".$i)->applyFromArray($styleArray);

      $sheet->setCellValue("A".$i,$j);
      $sheet->setCellValue("B".$i,$lista->femision);
      $sheet->setCellValue("C".$i,$lista->numero);
      $sheet->setCellValue("D".$i,$lista->motivo);
      $sheet->setCellValue("E".$i,$lista->total);
      $i++; $j++;
    }

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_INGRESOS.xlsx"');
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

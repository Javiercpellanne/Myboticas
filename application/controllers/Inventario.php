<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Inventario extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(38)){redirect(base_url()."inicio");}

    $this->layout->setLayout("principal");
    $this->load->model("kardex_model");
    $this->load->model("kardexl_model");
    $this->load->model("lote_model");
    $this->load->model("iinicial_model");
    $this->load->library("mytcpdf");
  }

  public function index()
  {
    $controlip=$this->controlip('inventario');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
    $fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d");

    $listas=$this->iinicial_model->mostrarLimite($this->session->userdata("predeterminado"),$inicio,$fin);
    $this->layout->setTitle("Actualizar Inventario");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function inventarioi($id=null)
  {
    $controlip=$this->controlip('inventario/inventarioi');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    if ($this->input->post())
    {
      if ($id!=null) {
        $consulta=$this->iinicial_model->contador($this->session->userdata("predeterminado"),$id,$this->input->post('mcodigo',true));
        if ($consulta==0) {
          $data=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            'iduser'            =>$this->session->userdata('id'),
            'numero'            =>$id,
            'femision'          =>date("Y-m-d"),
            'idproducto'        =>$this->input->post('mcodigo',true),
            'descripcion'       =>$this->input->post('mdescripcion',true),
            'cantidad'          =>$this->input->post('munidades',true),
          );

          if ($this->input->post('mactivar',true)==1) {
            $data['lote']=$this->input->post('mlote',true);
            $data['fvencimiento']=$this->input->post('mfecha',true);
          }
          $insertar=$this->iinicial_model->insert($data);
          $this->session->set_flashdata('css', 'success');
          $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
          $control_movimiento=$this->movimientos('inventario/inventarioi','Edito inventario nro '.$id);
        } else {
          $this->session->set_flashdata('css', 'danger');
          $this->session->set_flashdata('mensaje', 'El producto ya fue agregado al inventario!!!!!!');
        }
        echo base_url()."inventario/inventarioi/".$id;
      } else {
        $numero=$this->iinicial_model->maximo($this->session->userdata("predeterminado"));
        $ninicio= $numero==null ? '' : $numero->numero;
        $numeracion=$ninicio+1;
        $data=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          'iduser'            =>$this->session->userdata('id'),
          'numero'            =>$numeracion,
          'femision'          =>date("Y-m-d"),
          'idproducto'        =>$this->input->post('mcodigo',true),
          'descripcion'       =>$this->input->post('mdescripcion',true),
          'cantidad'          =>$this->input->post('munidades',true),
        );

        if ($this->input->post('mactivar',true)==1) {
          $data['lote']=$this->input->post('mlote',true);
          $data['fvencimiento']=$this->input->post('mfecha',true);
        }
        $insertar=$this->iinicial_model->insert($data);
        $this->session->set_flashdata('css', 'success');
        $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
        $control_movimiento=$this->movimientos('inventario/inventarioi','Registro inventario nro '.$numeracion);
        echo base_url()."inventario/inventarioi/".$numeracion;
      }
      exit();
    }

    $listas=$this->iinicial_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
    $this->layout->setLayout("contraido");
    $this->layout->setTitle("Actualizar Inventario");
    $this->layout->view("inventarioi",compact("anexos","nestablecimiento",'empresa',"listas","id"));
  }

  public function inventarioe($nro,$id)
  {
    $controlip=$this->controlip('inventario/inventarioe');
    if ($this->input->post())
    {
      $data=array('cantidad'=>$this->input->post('munidades',true));
      if ($this->input->post('mactivar',true)==1) {
        $data['lote']=$this->input->post('mlote',true);
        $data['fvencimiento']=$this->input->post('mfecha',true);
      }
      $guardar=$this->iinicial_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      $control_movimiento=$this->movimientos('inventario/inventarioe','Edito inventario nro '.$nro);
      echo base_url()."inventario/inventarioi/".$nro;
      exit();
    }

    $datos=$this->iinicial_model->mostrar($id);
    $producto=$this->producto_model->mostrar(array("p.id"=>$datos->idproducto));
    $this->layout->setLayout("blanco");
    $this->layout->view("inventarioe",compact("datos","producto"));
  }

  public function inventarioItemd($id)
  {
    if (!$id) {show_404();}
    $controlip=$this->controlip('inventario/inventarioItemd');
    $datos=$this->iinicial_model->mostrar($id);
    if ($datos==NULL) {show_404();}

    $eliminar=$this->iinicial_model->delete($id);
    $control_movimiento=$this->movimientos('inventario/inventarioItemd','Elimino del inventario producto '.$datos->descripcion);
    $success=true;
    $titulo='Borrado!';
    $mensaje='El proceso se realizo con exito';
    $color='success';

    $proceso['success']=$success;
    $proceso['titulo']=$titulo;
    $proceso['mensaje']=$mensaje;
    $proceso['color']=$color;
    $proceso['url']=base_url().'inventario/inventarioi/'.$datos->numero;
    echo json_encode($proceso);
    exit();
  }

  public function inventariog()
  {
    $controlip=$this->controlip('inventario/inventariog');
    if ($this->input->post())
    {
      $url='';
      for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
        $datak=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          'iduser'            =>$this->session->userdata('id'),
          'fecha'             =>date("Y-m-d"),
          'idtmovimiento'     =>16,
          'concepto'          =>'Stock Actualizado',
          'idproducto'        =>$this->input->post('idproducto',true)[$i],
          'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
          'entradaf'          =>$this->input->post('cantidad',true)[$i],
          'saldof'            =>$this->input->post('cantidad',true)[$i],
          'costo'             =>$this->input->post('precio',true)[$i],
          'entradav'          =>$this->input->post('importe',true)[$i],
          'saldov'            =>$this->input->post('importe',true)[$i],
        );
        $insertark=$this->kardex_model->insert($datak);

        //actualizar stock
        $datas=array('stock'=>$this->input->post('cantidad',true)[$i]);
        $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

        if ($this->input->post('lote',true)[$i]!="") {
          $detalles=$this->lote_model->mostrarTotal($this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i]);
          foreach ($detalles as $detalle) {
            $datac=array
            (
              'idestablecimiento' =>$this->session->userdata('predeterminado'),
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date('Y-m-d'),
              'idtmovimiento'     =>16,
              'concepto'          =>'Stock Actualizado',
              'idproducto'        =>$this->input->post('idproducto',true)[$i],
              'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
              'nlote'             =>$detalle->nlote,
              'entradaf'          =>0,
              'saldof'            =>0,
            );
            $insertarc=$this->kardexl_model->insert($datac);
          }

          $eliminar=$this->lote_model->delete(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

          if ($this->input->post('cantidad',true)[$i]>0) {
            $datal=array
            (
              'idestablecimiento' =>$this->session->userdata("predeterminado"),
              'idproducto'        =>$this->input->post('idproducto',true)[$i],
              'nlote'             =>$this->input->post('lote',true)[$i],
              'fvencimiento'      =>valor_fecha($this->input->post('fvencimiento',true)[$i]),
              'inicial'           =>$this->input->post('cantidad',true)[$i],
              'stock'             =>$this->input->post('cantidad',true)[$i],
            );
            $insertarl=$this->lote_model->insert($datal);

            $datac=array
            (
              'idestablecimiento' =>$this->session->userdata('predeterminado'),
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date('Y-m-d'),
              'idtmovimiento'     =>16,
              'concepto'          =>'Stock Actualizado',
              'idproducto'        =>$this->input->post('idproducto',true)[$i],
              'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
              'nlote'             =>$this->input->post('lote',true)[$i],
              'entradaf'          =>$this->input->post('cantidad',true)[$i],
              'saldof'            =>$this->input->post('cantidad',true)[$i],
            );
            $insertarc=$this->kardexl_model->insert($datac);
          }
        }

        //actualizar inventario
        $data=array(
          'precio'        =>$this->input->post('precio',true)[$i],
          'importe'       =>$this->input->post('importe',true)[$i],
          'estado'        =>1,
        );
        $actualizar=$this->iinicial_model->update($data,$this->input->post('id',true)[$i]);
      }
      $control_movimiento=$this->movimientos('inventario/inventariog','Ingreso almacen el inventario '.$this->input->post('numero',true));

      $datos['mensaje']='Los datos se han guardado exitosamente!';
      $datos['url']=base_url().'inventario';
      echo json_encode($datos);
      exit();
    }
  }

  public function inventariox()
  {
   if ($this->input->post())
   {
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

       //numeracion del inventario
      $numero=$this->iinicial_model->maximo($this->session->userdata("predeterminado"));
      $ninicio= $numero==null ? '' : $numero->numero;
      $numeracion=$ninicio+1;

      //llenado de informacion
       $sheet_data = $spreadsheet->getActiveSheet()->toArray();
       foreach($sheet_data as $key => $val) {
         if($key != 0 && $val[0]>0 && $val[3]>0) {
          $data=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            'iduser'            =>$this->session->userdata('id'),
            'numero'            =>$numeracion,
            'femision'          =>date("Y-m-d"),
            'idproducto'        =>$val[0],
            'descripcion'       =>trim($val[1]),
            'cantidad'          =>$val[3],
            'lote'              =>valor_fecha($val[4]),
            'fvencimiento'      =>valor_fecha(formatoVcto($val[5])),
          );
          $insertar=$this->iinicial_model->insert($data);
         }
       }

       $this->session->set_flashdata('css', 'success');
       $this->session->set_flashdata('mensaje', 'La informacion fue subida exitosamente!');
       $control_movimiento=$this->movimientos('inventario/inventariox','Subio archivo excel inventario');
     }else {
       $this->session->set_flashdata('css', 'danger');
       $this->session->set_flashdata('mensaje', 'No existe archivo o no corresponde el tipo!');
     }
     echo base_url()."inventario";
     exit();
   }

   $this->layout->setLayout("blanco");
   $this->layout->view("inventariox");
  }

  public function inventarioexcel()
  {
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $listas=$this->producto_model->mostrarTotal(array("tipo"=>'B',"estado"=>1));

   $spreadsheet = new Spreadsheet();
   $sheet = $spreadsheet->getActiveSheet();
   $sheet->setTitle("Stock");

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
   $sheet->setCellValueByColumnAndRow(2, 1,"Nombre");
   $sheet->setCellValueByColumnAndRow(3, 1,"Control Lote");
   $sheet->setCellValueByColumnAndRow(4, 1,"Nuevo Stock");
   $sheet->setCellValueByColumnAndRow(5, 1,"Codigo Lote");
   $sheet->setCellValueByColumnAndRow(6, 1,"F.Vcto");
   $sheet->setCellValueByColumnAndRow(7, 1,"Anterior Stock");

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

     $sheet->setCellValue("A".$i,$lista->id);
     $sheet->setCellValue("B".$i,$lista->descripcion.' '.$lista->nlaboratorio);
     $sheet->setCellValue("C".$i,$lista->lote==1?'lote/f.vcto':'');
     $sheet->setCellValue("G".$i,$cantidad->stock);
     $i++;
   }

    $writer = new Xlsx($spreadsheet); // instantiate Xlsx
    header('Content-Type: application/vnd.ms-excel'); // generate excel file
    header('Content-Disposition: attachment;filename="LISTA_STOCK_'.$nestablecimiento->descripcion.'.xlsx"');
    $writer->save('php://output'); // download file
  }

  public function consulta($id)
  {
    $detalles=$this->iinicial_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
    $this->layout->setLayout("blanco");
    $this->layout->view("consulta",compact("detalles"));
  }

  public function pdfinventario($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $detalles=$this->iinicial_model->mostrarTotal($this->session->userdata("predeterminado"),$id);
    $this->layout->setLayout("blanco");
    $this->layout->view("pdfinventario",compact("empresa","nestablecimiento","detalles","id"));
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

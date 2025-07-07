<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    if (!$this->acciones(17)){redirect(base_url()."inicio");}

    $this->layout->setLayout("contraido");
    $this->load->model("tmovimiento_model");
    $this->load->model("kardex_model");
    $this->load->model("kardexl_model");
    $this->load->model("lote_model");
    $this->load->model("movimiento_model");
    $this->load->model("movimientod_model");
    $this->load->library("mytcpdf");
  }

  public function index()
  {
    $controlip=$this->controlip('movimiento');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $inicio=$this->input->post("inicio",true)!=null ? $this->input->post("inicio",true) : SumarFecha("-15 day",date("Y-m-d")) ;
    $fin=$this->input->post("fin",true)!=null ? $this->input->post("fin",true) : date("Y-m-d");

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"femision>="=>$inicio,"femision<="=>$fin);
    $listas=$this->movimiento_model->mostrarTotal($filtros);
    $this->layout->setTitle("Movimiento Producto");
    $this->layout->view("index",compact("anexos","nestablecimiento",'empresa',"listas","inicio","fin"));
  }

  public function ingreso()
  {
    $controlip=$this->controlip('movimiento/ingreso');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $motivos=$this->tmovimiento_model->mostrarTotal(array("tipo"=>"I","estado"=>1));
    $this->layout->setTitle("Ingreso Producto");
    $this->layout->view("ingreso",compact("anexos","nestablecimiento",'empresa',"motivos"));
  }

  public function ingresog()
  {
    $controlip=$this->controlip('movimiento/ingresog');
    if ($this->input->post())
    {
      $url='';
      if ($this->input->post('idproducto',true)==null) {
        $mensaje='No envio productos en el movimiento!';
      } else {
        $mtraslado=explode('-',$this->input->post('motivo',true));
        $data=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          "iduser"            =>$this->session->userdata('id'),
          "femision"          =>date("Y-m-d"),
          "idtmovimiento"     =>$mtraslado[0],
          "observaciones"     =>$this->input->post('observaciones',true),
        );
        $insertar=$this->movimiento_model->insert($data);

        $importe=0;
        for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
          $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
          $inicalf=$saldos==null ? 0: $saldos->saldof;
          $inicalv=$saldos==null ? 0: $saldos->saldov;

          $saldof=$inicalf+$this->input->post('almacenc',true)[$i];
          $saldov=$inicalv+$this->input->post('importe',true)[$i];
          $datak=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date("Y-m-d"),
            'idtmovimiento'     =>$mtraslado[0],
            'concepto'          =>$mtraslado[1],
            'idproducto'        =>$this->input->post('idproducto',true)[$i],
            'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
            'entradaf'          =>$this->input->post('almacenc',true)[$i],
            'saldof'            =>$saldof,
            'costo'             =>$this->input->post('almacenp',true)[$i],
            'entradav'          =>$this->input->post('importe',true)[$i],
            'saldov'            =>$saldov,
            'documento'         =>'MV-'.$insertar,
          );
          $insertark=$this->kardex_model->insert($datak);

          $datas=array('stock'=>$saldof);
          $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

          if ($this->input->post('lote',true)[$i]!="") {
            $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i],"nlote"=>$this->input->post('lote',true)[$i]));

            if ($consultal==null) {
              $datal=array
              (
                'idestablecimiento' =>$this->session->userdata("predeterminado"),
                'idproducto'        =>$this->input->post('idproducto',true)[$i],
                'nlote'             =>$this->input->post('lote',true)[$i],
                'fvencimiento'      =>$this->input->post('fvencimiento',true)[$i],
                'inicial'           =>$this->input->post('almacenc',true)[$i],
                'stock'             =>$this->input->post('almacenc',true)[$i],
              );
              $insertarl=$this->lote_model->insert($datal);
            } else {
              $datal=array("stock"=>$consultal->stock+$this->input->post('almacenc',true)[$i]);
              $actualizar=$this->lote_model->update($datal,$this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$this->input->post('lote',true)[$i]);
            }

            $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post("idproducto",true)[$i],$this->input->post("lote",true)[$i]);
            $inicial=$saldos==null ? 0: $saldos->saldof;
            $saldosl=$inicial+$this->input->post('almacenc',true)[$i];
            $datac=array
            (
              'idestablecimiento' =>$this->session->userdata('predeterminado'),
              'iduser'            =>$this->session->userdata('id'),
              'fecha'             =>date('Y-m-d'),
              'idtmovimiento'     =>$mtraslado[0],
              'concepto'          =>$mtraslado[1],
              'idproducto'        =>$this->input->post('idproducto',true)[$i],
              'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
              'nlote'             =>$this->input->post('lote',true)[$i],
              'entradaf'          =>$this->input->post('almacenc',true)[$i],
              'saldof'            =>$saldosl,
              'documento'         =>'MV-'.$insertar,
            );
            $insertarc=$this->kardexl_model->insert($datac);
          }

          $datad=array
          (
            "idmovimiento"  =>$insertar,
            "idproducto"    =>$this->input->post("idproducto",true)[$i],
            "descripcion"   =>trim($this->input->post("descripcion",true)[$i]),
            "unidad"        =>$this->input->post("unidad",true)[$i],
            "cantidad"      =>$this->input->post("cantidad",true)[$i],
            "precio"        =>$this->input->post("precio",true)[$i],
            "importe"       =>$this->input->post("importe",true)[$i],
            "calmacen"      =>$this->input->post("almacenc",true)[$i],
            "palmacen"      =>$this->input->post("almacenp",true)[$i],
            "lote"          =>$this->input->post("lote",true)[$i],
            "fvencimiento"  =>valor_fecha($this->input->post("fvencimiento",true)[$i]),
          );
          $insertard=$this->movimientod_model->insert($datad);
          $importe+=$this->input->post('importe',true)[$i];
        }

        $datas=array("importe"=>$importe);
        $actualizars=$this->movimiento_model->update($datas,$insertar);
        $control_movimiento=$this->movimientos('movimiento/ingresog','Registro ingreso MV-'.$insertar);

        $mensaje='Los datos se han guardado exitosamente!';
        $url=base_url().'movimiento';
      }

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
    }
  }

  public function ingresoa($id)
  {
    $controlip=$this->controlip('movimiento/ingresoa');
    $datos=$this->movimiento_model->mostrar($id);
    if ($datos->nulo==0) {
      $detalles=$this->movimientod_model->mostrarTotal($id);
      foreach ($detalles as $detalle) {
        $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$datos->idestablecimiento,"idproducto"=>$detalle->idproducto));
        $inicalf=$saldos==null ? 0: $saldos->saldof;
        $inicalv=$saldos==null ? 0: $saldos->saldov;

        $saldof=$inicalf-$detalle->calmacen;
        $saldov=$inicalv-$detalle->importe;
        $datak=array
        (
          'idestablecimiento' =>$datos->idestablecimiento,
          'iduser'            =>$this->session->userdata('id'),
          "fecha"             =>date("Y-m-d"),
          "idtmovimiento"     =>$datos->idtmovimiento,
          "concepto"          =>"Anulacion ".$datos->nmtraslado,
          "idproducto"        =>$detalle->idproducto,
          "descripcion"       =>$detalle->descripcion,
          "salidaf"           =>$detalle->calmacen,
          "saldof"            =>$saldof,
          "costo"             =>$detalle->palmacen,
          "salidav"           =>$detalle->importe,
          "saldov"            =>$saldov,
          "documento"         =>'MV-'.$datos->id,
        );
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
            'idestablecimiento' =>$datos->idestablecimiento,
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date('Y-m-d'),
            'idtmovimiento'     =>6,
            "concepto"          =>"Anulacion ".$datos->nmtraslado,
            'idproducto'        =>$detalle->idproducto,
            'descripcion'       =>$detalle->descripcion,
            'nlote'             =>$detalle->lote,
            'salidaf'           =>$detalle->calmacen,
            'saldof'            =>$saldosl,
            'documento'         =>'MV-'.$datos->id,
          );
          $insertarc=$this->kardexl_model->insert($datac);
        }
      }

      $data=array
      (
        "nulo"    =>1,
        "importe" =>"0.00",
      );
      $actualiza=$this->movimiento_model->update($data,$id);
      $control_movimiento=$this->movimientos('movimiento/ingresog','Anulo ingreso MV-'.$id);

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
    $proceso['url']=base_url().'movimiento';
    echo json_encode($proceso);
    exit();
  }

  public function salida()
  {
    $controlip=$this->controlip('movimiento/salida');
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $motivos=$this->tmovimiento_model->mostrarTotal(array("tipo"=>"S","estado"=>1));
    $this->layout->setTitle("Salida Producto");
    $this->layout->view("salida",compact("anexos","nestablecimiento",'empresa',"motivos"));
  }

  public function salidag()
  {
    $controlip=$this->controlip('movimiento/salidag');
    if ($this->input->post())
    {
      $url='';
      if ($this->input->post('idproducto',true)==null) {
        $mensaje='No envio productos en el movimiento!';
      } else {
        $mtraslado=explode('-',$this->input->post('motivo',true));
        $data=array
        (
          'idestablecimiento' =>$this->session->userdata("predeterminado"),
          "iduser"            =>$this->session->userdata('id'),
          "femision"          =>date("Y-m-d"),
          "idtmovimiento"     =>$mtraslado[0],
          "observaciones"     =>$this->input->post('observaciones',true),
        );
        $insertar=$this->movimiento_model->insert($data);

        $importe=0;
        for ($i=0; $i < count($this->input->post('idproducto',true)) ; $i++) {
          $saldos=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i]));
          $inicalf=$saldos==null ? 0: $saldos->saldof;
          $inicalv=$saldos==null ? 0: $saldos->saldov;
          //costos promedio
          $costo=round($inicalv/$inicalf,4);
          $salidav=$this->input->post('almacenc',true)[$i]*$costo;

          $saldof=$inicalf-$this->input->post('almacenc',true)[$i];
          $saldov=$inicalv-$salidav;
          $datak=array
          (
            'idestablecimiento' =>$this->session->userdata("predeterminado"),
            'iduser'            =>$this->session->userdata('id'),
            'fecha'             =>date("Y-m-d"),
            'idtmovimiento'     =>$mtraslado[0],
            'concepto'          =>$mtraslado[1],
            'idproducto'        =>$this->input->post('idproducto',true)[$i],
            'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
            'salidaf'           =>$this->input->post('almacenc',true)[$i],
            'saldof'            =>$saldof,
            'costo'             =>$costo,
            'salidav'           =>$salidav,
            'saldov'            =>$saldov,
            'documento'         =>'MV-'.$insertar,
          );
          $insertark=$this->kardex_model->insert($datak);

          $datas=array('stock'=>$saldof);
          $actualizar=$this->inventario_model->update($datas,array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post('idproducto',true)[$i]));

          $nlotes='';
          $clotes='';
          $flotes='';
          if ($this->input->post('lote',true)[$i]!="") {
            $cantidad=$this->input->post('almacenc',true)[$i];
            $clotes=array();
            $flotes=array();
            $nlotes=array();
            $nlote1=explode(",",$this->input->post("lote",true)[$i]);
            for ($l=0; $l < count($nlote1) ; $l++) {
              $consultal=$this->lote_model->mostrar(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$this->input->post("idproducto",true)[$i],"nlote"=>$nlote1[$l]));
              $ncantidad=$cantidad-$consultal->stock; //nueva cantidad
              $saldoc=$consultal->stock-$cantidad;  //saldo a guardar

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

              $saldos=$this->kardexl_model->ultimo($this->session->userdata("predeterminado"),$this->input->post('idproducto',true)[$i],$nlote1[$l]);
              $inicial=$saldos==null ? 0: $saldos->saldof;
              $saldosl=$inicial-$inicialf;
              $datac=array
              (
                'idestablecimiento' =>$this->session->userdata('predeterminado'),
                'iduser'            =>$this->session->userdata('id'),
                'fecha'             =>date("Y-m-d"),
                'idtmovimiento'     =>$mtraslado[0],
                'concepto'          =>$mtraslado[1],
                'idproducto'        =>$this->input->post('idproducto',true)[$i],
                'descripcion'       =>trim($this->input->post('descripcion',true)[$i]),
                'nlote'             =>$nlote1[$l],
                'salidaf'           =>$inicialf,
                'saldof'            =>$saldosl,
                'documento'         =>'MV-'.$insertar,
              );
              $insertarc=$this->kardexl_model->insert($datac);

              array_push($nlotes,$consultal->nlote);
              array_push($clotes,$inicialf);
              array_push($flotes,$consultal->fvencimiento);
              $cantidad=$ncantidad;
              if ($cantidad<=0) {break;}
            }

            $nlotes=implode("|", $nlotes);
            $clotes=implode("|", $clotes);
            $flotes=implode("|", $flotes);
          }

          $datad=array
          (
            "idmovimiento"=>$insertar,
            "idproducto"  =>$this->input->post("idproducto",true)[$i],
            "descripcion" =>trim($this->input->post("descripcion",true)[$i]),
            "unidad"      =>$this->input->post("unidad",true)[$i],
            "cantidad"    =>$this->input->post("cantidad",true)[$i],
            "precio"      =>$salidav/$this->input->post("cantidad",true)[$i],
            "importe"     =>$salidav,
            "calmacen"    =>$this->input->post("almacenc",true)[$i],
            "palmacen"    =>$costo,
            "lote"        =>$nlotes,
            "clote"       =>$clotes,
            "fvencimiento"=>$flotes,
          );
          $insertard=$this->movimientod_model->insert($datad);
          $importe+=$salidav;
        }

        $datai=array("importe"=>$importe);
        $actualizari=$this->movimiento_model->update($datai,$insertar);
        $control_movimiento=$this->movimientos('movimiento/salidag','Registro salida MV-'.$insertar);

        $mensaje='Los datos se han guardado exitosamente!';
        $url=base_url().'movimiento';
      }

      $datos['mensaje']=$mensaje;
      $datos['url']=$url;
      echo json_encode($datos);
      exit();
    }
  }

  public function salidaa($id)
  {
    $controlip=$this->controlip('movimiento/salidaa');
    $datos=$this->movimiento_model->mostrar($id);
    if ($datos->nulo==0) {
      $detalles=$this->movimientod_model->mostrarTotal($id);
      foreach ($detalles as $detalle) {
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
          'iduser'            =>$this->session->userdata('id'),
          "fecha"             =>date("Y-m-d"),
          "idtmovimiento"     =>$datos->idtmovimiento,
          "concepto"          =>"Anulacion ".$datos->nmtraslado,
          "idproducto"        =>$detalle->idproducto,
          "descripcion"       =>$detalle->descripcion,
          "entradaf"          =>$detalle->calmacen,
          "saldof"            =>$saldof,
          "costo"             =>$detalle->palmacen,
          "entradav"          =>$salidav,
          "saldov"            =>$saldov,
          "documento"         =>'MV-'.$datos->id,
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
                'idestablecimiento' =>$datos->idestablecimiento,
                "idproducto"        =>$detalle->idproducto,
                "nlote"             =>$nlote[$l],
                "fvencimiento"      =>valor_fecha($flote[$l]),
                "inicial"           =>$clote[$l],
                "stock"             =>$clote[$l],
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
              'fecha'             =>date("Y-m-d"),
              'idtmovimiento'     =>$datos->idtmovimiento,
              'concepto'          =>"Anulacion ".$datos->nmtraslado,
              'idproducto'        =>$detalle->idproducto,
              'descripcion'       =>$detalle->descripcion,
              'nlote'             =>$nlote[$l],
              'entradaf'          =>$clote[$l],
              'saldof'            =>$saldosl,
              'documento'         =>'MV-'.$datos->id,
            );
            $insertarc=$this->kardexl_model->insert($datac);
          }
        }
      }

      $data=array
      (
        "nulo"    =>1,
        "importe" =>"0.00",
      );
      $actualiza=$this->movimiento_model->update($data,$id);
      $control_movimiento=$this->movimientos('movimiento/salidaa','Anulo salida MV-'.$id);

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
    $proceso['url']=base_url().'movimiento';
    echo json_encode($proceso);
    exit();
  }

  public function consulta($id)
  {
    $datos=$this->movimiento_model->mostrar($id);
    $detalles=$this->movimientod_model->mostrarTotal($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("consulta",compact("datos","detalles"));
  }

  public function pdfmovimiento($id)
  {
    $empresa=$this->empresa_model->mostrar();
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));

    $datos=$this->movimiento_model->mostrar($id);
    $detalles=$this->movimientod_model->mostrarTotal($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("pdfmovimiento",compact("empresa","nestablecimiento","datos","detalles"));
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

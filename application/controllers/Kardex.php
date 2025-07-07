<?php
class Kardex extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    if (!$this->session->userdata("login")){redirect(base_url()."login");}
    $this->layout->setLayout("contraido");
    $this->load->model("producto_model");
    $this->load->model("inventario_model");
    $this->load->model("kardex_model");
    $this->load->model("kardexl_model");

    $this->load->model("movimiento_model");
    $this->load->model("traslado_model");
    $this->load->model("nventa_model");
    $this->load->model("venta_model");
    $this->load->model("movimientod_model");
    $this->load->model("trasladod_model");
    $this->load->model("nventad_model");
    $this->load->model("ventad_model");

    $this->load->library('pagination');
  }

  public function index()
  {
    // Obtener datos de la sesión y los modelos necesarios
    $anexos = explode(",", $this->session->userdata("establecimientos"));
    $nestablecimiento = $this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa = $this->empresa_model->mostrar();
    $search = $this->input->get('search');

    // Filtros para los productos
    $filtros=array("estado"=>1);
    $total_rows = $this->producto_model->contadorProductos($filtros,$search);
    // Configurar y obtener la paginación
    $pagination = $this->paginacion('kardex/index', $total_rows, 3);

    // Obtener registros de productos paginados
    $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
    $listas = $this->producto_model->totalProductos($filtros, 15, $page, $search);

    // Cargar la vista
    $this->layout->setTitle("Productos");
    $this->layout->view("index", compact("anexos", "nestablecimiento", "empresa", "listas", "pagination", "search"));
  }

  public function kardex($id,$anio=null,$mes=null)
  {
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    $cantidad=$this->inventario_model->mostrar($this->session->userdata("predeterminado"),$id);

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$id);
    $meses=$this->kardex_model->agrupacionMensual($filtros);

    if (!empty($meses) && $anio==null && $mes==null) {
        $primer_mes = $meses[0]; // Obtiene el primer resultado
        $anio = $primer_mes->anio; // Si se agrupa por anio
        $mes = $primer_mes->mes;   // Si se agrupa por mes

    }
    $ultimo_mes = end($meses);; // Obtiene el primer resultado

    $filtros['YEAR(fecha)']=$anio; $filtros['MONTH(fecha)']=$mes;
    $listas=$this->kardex_model->mostrarTotal($filtros);
    $this->layout->setTitle("Kardex");
    $this->layout->view("kardex",compact('empresa',"anexos","nestablecimiento","datos",'cantidad',"meses","listas","id",'anio','mes','ultimo_mes'));
  }

  public function recalcular($producto,$id)
  {
    // $anexos=explode(",",$this->session->userdata("establecimientos"));
    // $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    // $empresa=$this->empresa_model->mostrar();

    $filtros=array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$producto,"id>="=>$id);
    $listas=$this->kardex_model->mostrarTotal($filtros);
    $inicial=$this->kardex_model->ultimo(array("idestablecimiento"=>$this->session->userdata("predeterminado"),"idproducto"=>$producto,"id<"=>$id));

    $iniciof = $inicial->saldof ?? 0;
    $iniciov = $inicial->saldov ?? 0;

    foreach ($listas as $lista) {
        // Consulta inicial
        if (substr($lista->documento,0,2)=='MV') {
          $consulta=$this->movimiento_model->productoTotal(array("concat('MV-',v.id)"=>$lista->documento,"idproducto"=>$producto));
        } elseif (substr($lista->documento,0,2)=='TI') {
          $consulta=$this->traslado_model->productoTotal(array("concat('TI-',v.id)"=>$lista->documento,"idproducto"=>$producto));
        } elseif (substr($lista->documento,0,2)=='NV') {
          $consulta=$this->nventa_model->productoTotal(array("concat(serie,'-',numero)"=>$lista->documento,"idproducto"=>$producto));
        } elseif (substr($lista->documento,0,1)=='B' || substr($lista->documento,0,1)=='F') {
          $consulta=$this->venta_model->productoTotal(array("concat(serie,'-',numero)"=>$lista->documento,"idproducto"=>$producto));
        } else {
          $iniciof=0; $iniciov=0;
          $consulta=(object) array("calmacen"=>$lista->entradaf,"palmacen"=>$lista->costo);
        }

        if ($lista->salidaf !== NULL) {
            // Procesar salida
            $salida = $consulta->calmacen;
            $saldof = $iniciof - $salida;
            $costo = round($iniciov / $iniciof, 4);
            $salidav = $salida * $costo;
            $saldov = round($iniciov - $salidav, 4);

            $datas = ["palmacen" => $costo];
            if (substr($lista->documento,0,2)=='MV') {
              $this->movimientod_model->update($datas, $consulta->id);
            }  elseif (substr($lista->documento,0,2)=='TI') {
              $this->trasladod_model->update($datas, $consulta->id);
            } elseif (substr($lista->documento, 0, 2) == 'NV') {
              $this->nventad_model->update($datas, ["id" => $consulta->id]);
            } elseif (substr($lista->documento, 0, 1) == 'B' || substr($lista->documento, 0, 1) == 'F') {
              $this->ventad_model->update($datas, ["id" => $consulta->id]);
            }

            $data = [
                "costo" => $costo,
                "salidav" => $salidav,
                "saldov" => $saldov,
            ];
            $this->kardex_model->update($data, $lista->id);
        } else {
            // Procesar entrada
            $ingreso = $consulta->calmacen;
            $saldof = $iniciof + $ingreso;
            $costo = $consulta->palmacen;
            $ingresov = $ingreso * $costo;
            $saldov = round($iniciov + $ingresov, 4);

            $data = [
                "costo" => $costo,
                "entradav" => $ingresov,
                "saldov" => $saldov,
            ];
            $this->kardex_model->update($data, $lista->id);
        }

        // Actualizar los saldos iniciales
        $iniciof = $saldof;
        $iniciov = $saldov;

        // Esperar un breve período antes de la siguiente iteración
        usleep(500000); // 0.5 segundos (ajustar según sea necesario)

        $fecha=$lista->fregistro;
    }
    $mes=date("n", strtotime($fecha));
    $anio=date("Y", strtotime($fecha));
    redirect(base_url()."kardex/kardex/".$producto."/".$anio."/".$mes);

    // $this->layout->setTitle("Kardex");
    // $this->layout->view("recalcular",compact('empresa',"anexos","nestablecimiento","listas",'inicial','producto'));
  }

  public function kardexe($nro,$id)
  {
    if ($this->input->post("valor",true))
    {
      $data=array
      (
        "entradaf"    =>valor_fecha($this->input->post("entradaf",true)),
        "salidaf"     =>valor_fecha($this->input->post("salidaf",true)),
        "saldof"      =>$this->input->post("saldof",true),
        "costo"       =>$this->input->post("costo",true),
        "entradav"    =>valor_fecha($this->input->post("entradav",true)),
        "salidav"     =>valor_fecha($this->input->post("salidav",true)),
        "saldov"      =>$this->input->post("saldov",true),
      );

      $guardar=$this->kardex_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      echo base_url()."kardex/kardex/".$nro;
      exit();
    }

    $datos=$this->kardex_model->mostrar($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("kardexe",compact("datos"));
  }

  public function kardexl($id)
  {
    $anexos=explode(",",$this->session->userdata("establecimientos"));
    $nestablecimiento=$this->establecimiento_model->mostrar($this->session->userdata("predeterminado"));
    $empresa=$this->empresa_model->mostrar();

    $datos=$this->producto_model->mostrar(array("p.id"=>$id));
    $listas=$this->kardexl_model->unicos($this->session->userdata("predeterminado"),$id);
    $this->layout->setTitle("Kardex Lote");
    $this->layout->view("kardexl",compact('empresa',"anexos","nestablecimiento","datos","listas","id"));
  }

  public function kardexle($nro,$id)
  {
    if ($this->input->post("valor",true))
    {
      $data=array
      (
        "entradaf"    =>valor_fecha($this->input->post("entradaf",true)),
        "salidaf"     =>valor_fecha($this->input->post("salidaf",true)),
        "saldof"      =>$this->input->post("saldof",true),
      );

      $guardar=$this->kardexl_model->update($data,$id);
      $this->session->set_flashdata('css', 'success');
      $this->session->set_flashdata('mensaje', 'Los datos se han guardado exitosamente!');
      echo base_url()."kardex/kardexl/".$nro;
      exit();
    }

    $datos=$this->kardexl_model->mostrar($id);
    $this->layout->setLayout("blanco");
    $this->layout->view("kardexle",compact("datos"));
  }

  protected function paginacion($base_url, $total_rows, $uri_segment)
  {
    // Configurar la paginación
    $this->load->library('pagination');
    $config = [
        'base_url' => base_url($base_url),
        'total_rows' => $total_rows,
        'per_page' => 10,
        'uri_segment' => $uri_segment,
        'reuse_query_string' => TRUE,
        'full_tag_open' => '<nav><ul class="pagination justify-content-center">',
        'full_tag_close' => '</ul></nav>',
        'first_link' => 'Primero',
        'last_link' => 'Último',
        'next_link' => '&raquo;',
        'prev_link' => '&laquo;',
        'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
        'cur_tag_close' => '</a></li>',
        'num_tag_open' => '<li class="page-item">',
        'num_tag_close' => '</li>',
        'attributes' => ['class' => 'page-link']
    ];

    $this->pagination->initialize($config);
    return $this->pagination->create_links();
  }


}

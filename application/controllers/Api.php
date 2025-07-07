<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
require APPPATH.'/libraries/RestController.php';
require APPPATH.'/libraries/Format.php';

class Api extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->model("producto_model");
        $this->load->model("inventario_model");
    }

    public function productos_post()
    {
        if (strlen($this->input->post('descripcion',true))>2) {
            $productos=$this->producto_model->buscador($this->input->post('descripcion',true),array("tipo"=>'B',"estado"=>1));
        } else {
            $productos=$this->producto_model->mostrarLimite(array("tipo"=>'B',"estado"=>1));
        }
        $datos=array();
        foreach ($productos as $producto) {
            $stock1=$this->inventario_model->mostrar(1,$producto->id);
            $stock2=$this->inventario_model->mostrar(2,$producto->id);

            $detalles['descripcion']=$producto->descripcion;
            $detalles['laboratorio']=$producto->nlaboratorio;
            $detalles['stock1']=$stock1==null ? 0 : $stock1->stock;
            $detalles['stock2']=$stock2==null ? 0 : $stock2->stock;
            array_push($datos,$detalles);
        }
        //echo json_encode($datos);
        $this->response($datos,201);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buscar extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->layout->setLayout('blanco');
    $this->load->model('tcomprobante_model');
    $this->load->model('venta_model');
  }

  public function index()
  {
    $comprobantec=$this->tcomprobante_model->mostrarLimite(array("formulario"=>1));

    $tcomprobante = $this->input->post('tcomprobante',true)?? '' ;
    $femision = $this->input->post('femision',true)?? '' ;
    $serie = $this->input->post('serie',true)?? '' ;
    $numero = $this->input->post('numero',true)?? '' ;
    $documento = $this->input->post('documento',true)?? '' ;
    $total = $this->input->post('total',true)?? '' ;
    $dato=$this->venta_model->consulta(array('tcomprobante'=>$tcomprobante,'femision'=>$femision,'serie'=>$serie,'numero'=>$numero,'documento'=>$documento,'total'=>$total));
    $this->layout->view('index',compact('comprobantec','dato','tcomprobante','femision','serie','numero','total','documento'));
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

}

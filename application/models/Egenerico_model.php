<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Egenerico_model extends CI_Model
{
	public $table = "escenciales_genericos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->where(array("idpactivo"=>$id))
				->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("descripcion")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		return $query->row();
	}


}

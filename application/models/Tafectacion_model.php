<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tafectacion_model extends CI_Model
{
	public $table = "tipo_afectaciones";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->order_by("id")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		return $query->row();
	}


}

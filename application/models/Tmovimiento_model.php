<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tmovimiento_model extends CI_Model
{
	public $table = "tipo_movimientos";
	public $table_id = "id";
	
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{		
		$query=$this->db
				->select("id, descripcion, tipo")
				->from($this->table)
				->where($filtros)
				->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("descripcion")
				->from($this->table)
				->where(array($this->table_id=>$id))
				->get();
		return $query->row();
	}


}

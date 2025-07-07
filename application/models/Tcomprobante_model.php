<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tcomprobante_model extends CI_Model
{
	public $table = "tipo_comprobantes";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{		
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				//->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrarLimite($filtros)
	{
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->order_by("descripcion")
				->where($filtros)
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Departamento_model extends CI_Model
{
	public $table = "departamentos";
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
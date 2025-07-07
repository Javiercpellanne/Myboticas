<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mes_model extends CI_Model
{
	public $table = "meses";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{		
		$query=$this->db
				->select("id, descripcion, sigla")
				->from($this->table)
				// ->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("descripcion, sigla")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		return $query->row();
	}


}

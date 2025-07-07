<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tdebito_model extends CI_Model
{
	public $table = "tipo_debito";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{		
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->where(array("estado"=>1))
				->order_by("descripcion")
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
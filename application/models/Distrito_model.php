<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distrito_model extends CI_Model
{
	public $table = "distritos";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{		
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->where(array("idprovincia"=>$id))
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

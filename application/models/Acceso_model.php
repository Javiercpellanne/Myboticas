<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Acceso_model extends CI_Model
{
	public $table = "acceso";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("id, valor, menu")
				->from($this->table)
				->order_by("orden","asc")
				->get();
		return $query->result();
	}

	public function mostrarLimite()
	{
		$query=$this->db
				->select("id, valor, menu")
				->from($this->table)
				->where(array("estado"=>0))
				->order_by("orden","asc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("menu")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		return $query->row();
	}

	public function update($data=array(),$id)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table, $data);
	}



}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anivel_model extends CI_Model
{
	public $table = "acceso_niveles";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, valor, submenu")
				->from($this->table)
				->where(array("idacceso"=>$id))
				->get();
		return $query->result();
	}

	public function mostrarLimite($id)
	{
		$query=$this->db
				->select("id, valor, submenu")
				->from($this->table)
				->where(array("idacceso"=>$id,"estado"=>0))
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("valor, submenu, idacceso")
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

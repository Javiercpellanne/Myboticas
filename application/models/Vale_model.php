<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vale_model extends CI_Model
{
	public $table = "vales";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($dni)
	{		
		$query=$this->db
				->select("id, femision, dni, importe")
				->from($this->table)
				->where(array("dni"=>$dni,"estado"=>1)) //
				->order_by("id","desc")
				->limit(3)
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{		
		$query=$this->db
				->select("id, femision, dni, importe")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->delete($this->table);
	}


}

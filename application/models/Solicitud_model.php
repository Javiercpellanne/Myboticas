<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitud_model extends CI_Model
{
	public $table = "solicitudes";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("id, nulo, iduser, idproveedor, proveedor, femision, estado")
				->from($this->table)
				->where($filtros)//,"nulo"=>0
				->order_by("id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, iduser, idproveedor, proveedor, femision, estado")
				->from($this->table)
				->where(array("id"=>$id))
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

	public function contador($columna,$dato)
	{
		$this->db->from($this->table)->where($columna,$dato);
		return $this->db->count_all_results();
	}

}

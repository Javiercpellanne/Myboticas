<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pactivo_model extends CI_Model
{
	public $table = "principio_activos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("id, descripcion, escenciales")
				->from($this->table)
				->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrarLimite()
	{
		$query=$this->db
				->select("id, descripcion, escenciales")
				->from($this->table)
				->where(array("escenciales"=>1))
				->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("descripcion, escenciales")
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
		$this->db->where("id",$id);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where("id",$id);
		$this->db->delete($this->table);
	}

	public function contador($dato)
	{
		$this->db->from($this->table)->where("descripcion", $dato);
		return $this->db->count_all_results();
	}


}

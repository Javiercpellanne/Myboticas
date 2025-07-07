<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_model extends CI_Model
{
	public $table = "categorias";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($tipo)
	{		
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->where(array("tipo"=>$tipo))
				->order_by("descripcion")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("descripcion")
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

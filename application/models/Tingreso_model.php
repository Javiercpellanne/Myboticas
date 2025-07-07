<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tingreso_model extends CI_Model
{	
	public $table = "tipo_ingresos";
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

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$id)
	{
		$this->db->where($this->table_id,$id);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where($this->table_id,$id);
		$this->db->delete($this->table);
	}

	public function contador($dato)
	{
		$this->db->from($this->table)->where("descripcion", $dato); 
		return $this->db->count_all_results();
	}






}

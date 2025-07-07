<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bonificado_model extends CI_Model
{
	public $table = "bonificados";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("id, idproducto, descripcion, monto")
				->from($this->table)
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrarLimite($anuo)
	{
		$query=$this->db
				->select("anuo, mes, count(idproducto) as cantidad")
				->from($this->table)
				->where(array("anuo"=>$anuo))
				->group_by("mes")
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("id, iduser, anuo, mes, idproducto, descripcion, monto")
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
		$this->db->where($this->table_id,$id);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where($this->table_id,$id);
		$this->db->delete($this->table);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}


}

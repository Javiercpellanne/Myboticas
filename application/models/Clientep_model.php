<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clientep_model extends CI_Model
{
	public $table = "clientes_puntos";
	public $table_id = "id";
	
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($idcliente)
	{		
		$query=$this->db
				->select("year(femision) as year, month(femision) as month, sum(cantidad) as cantidad")
				->from($this->table)
				->where(array("idcliente"=>$idcliente))
				->group_by("year(femision),month(femision)")
				->get();
		return $query->result();
	}

	public function mostrarCliente($idcliente)
	{		
		$query=$this->db
				->select("id, idcliente, femision, inicial, cantidad")
				->from($this->table)
				->where(array("idcliente"=>$idcliente))
				->get();
		return $query->result();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$filtros)
	{
		$this->db->where($filtros);
		$this->db->update($this->table, $data);
	}

	public function delete($filtros)
	{
		$this->db->where($filtros);
		$this->db->delete($this->table);
	}

	public function cantidadTotal($idcliente)
	{		
		$query=$this->db
				->select_sum("cantidad")
				->from($this->table)
				->where(array("idcliente"=>$idcliente))
				->get();
		return $query->row();
	}


}

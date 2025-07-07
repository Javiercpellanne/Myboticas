<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model
{
	public $table = "usuarios";
	public function __construct()
	{
		parent::__construct();
	}

	public function login($user,$pass)
	{		
		$query=$this->db
				->select("id, nombres, usuario, clave, perfil, idestablecimiento")
				->from($this->table)
				->where(array("usuario"=>$user,"clave"=>$pass,"estado"=>1))
				->where_in('tipo',array('', 'F'))
				->get();
		return $query->row();
	}

	public function mostrarTotal($filtros)
	{		
		$query=$this->db
				->select("id, nombres, estado, perfil, usuario, idestablecimiento")
				->from($this->table)
				->where($filtros)
				->where_in('tipo',array('', 'F'))
				->order_by("nombres","asc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("id, nombres, usuario, clave, perfil, estado, idestablecimiento, anulacion")
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
		$this->db->from($this->table)->where("usuario", $dato);
		return $this->db->count_all_results();
	}

}

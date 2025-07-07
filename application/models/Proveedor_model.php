<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor_model extends CI_Model
{
	public $table = "proveedores";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("id, tdocumento, nombres, documento, direccion, telefono, email")
				->from($this->table)
				->order_by("nombres")
				->get();
		return $query->result();
	}

	public function mostrarLimite()
	{
		$query=$this->db
				->select("id, tdocumento, nombres, documento, direccion, telefono, email")
				->from($this->table)
				->limit(50)
				->order_by("id", "desc")
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("p.id, p.tdocumento, p.nombres, p.documento, p.iddepartamento, p.idprovincia, p.iddistrito, p.direccion, p.telefono, p.email, d.descripcion")
				->from($this->table." p")
				->join("tipo_identidades d","p.tdocumento=d.id")
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
		$this->db->from($this->table)->where("documento", $dato);
		return $this->db->count_all_results();
	}

	public function contadorTotal()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function buscador($nombre)
	{
		$query=$this->db
				->select("id, tdocumento, nombres, documento, direccion")
				->from($this->table)
				->like("nombres", $nombre)
				->or_like("documento", $nombre, "after")
				->limit(50)
				->get();
		return $query->result();
	}


}

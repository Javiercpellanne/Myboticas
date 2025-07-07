<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends CI_Model
{
	public $table = "clientes";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{		
		$query=$this->db
				->select("c.id, c.tdocumento, c.nombres, c.documento, c.iddepartamento, c.idprovincia, c.iddistrito, c.direccion, c.telefono, c.email, t.descripcion as ndistrito")
				->from($this->table." c")
				->join("distritos t","c.iddistrito=t.id","left")
				->order_by("nombres")
				->get();
		return $query->result();
	}

	public function mostrarLimite()
	{		
		$query=$this->db
				->select("c.id, c.tdocumento, c.nombres, c.documento, c.iddepartamento, c.idprovincia, c.iddistrito, c.direccion, c.telefono, c.email, t.descripcion as ndistrito")
				->from($this->table." c")
				->join("distritos t","c.iddistrito=t.id","left")
				->limit(50)
				->order_by("id", "desc") 
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("c.id, c.tdocumento, c.nombres, c.ncomercial, c.documento, c.idpais, c.iddepartamento, c.idprovincia, c.iddistrito, c.direccion, c.telefono, c.email, d.descripcion, m.descripcion as ndepartamento, p.descripcion as nprovincia, t.descripcion as ndistrito")
				->from($this->table." c")
				->join("tipo_identidades d","c.tdocumento=d.id")
				->join("departamentos m","c.iddepartamento=m.id","left")
				->join("provincias p","c.idprovincia=p.id","left")
				->join("distritos t","c.iddistrito=t.id","left")
				->where(array("c.id"=>$id))
				->get();
		//echo $this->db->last_query();exit;
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
				->select("id, tdocumento, nombres, documento, iddepartamento, idprovincia, iddistrito, direccion, telefono, email")
				->from($this->table)
				->like("nombres", $nombre)
				->or_like("documento", $nombre, "after")
				->limit(50)
				->get();
		return $query->result();
	}


}

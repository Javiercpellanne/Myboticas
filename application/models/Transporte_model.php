<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporte_model extends CI_Model
{
	public $table = "transportes";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($tipo)
	{		
		$query=$this->db
				->select("id, tdocumento, documento, nombres, placa, licencia")
				->from($this->table)
				->where(array("idttransporte"=>$tipo))
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("tdocumento, documento, nombres, placa, licencia")
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

	public function delete($id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->delete($this->table);
	}


}

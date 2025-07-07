<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Egreso_model extends CI_Model
{	
	public $table = "egresos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{		
		$query=$this->db
				->select("p.id, p.iduser, p.nulo, p.comprobante, p.numero, p.proveedor, p.femision, p.motivo, p.total, t.descripcion as negreso, m.descripcion as ntpago")
				->from($this->table." p")
				->join("tipo_egresos t","p.comprobante=t.id")
				->join("tipo_pagos m","p.idtpago=m.id","left")
				->where($filtros)//,"nulo"=>0
				->order_by("p.id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("nulo, comprobante, numero, proveedor, femision, motivo, total, idtpago")
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

	public function update($data=array(),$filtros)
	{
		$this->db->where($filtros);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where("id",$id);
		$this->db->delete($this->table);
	}

	public function contador($columna,$dato)
	{
		$this->db->from($this->table)->where($columna,$dato); 
		return $this->db->count_all_results();
	}

	public function montoTotal($filtros)
	{		
		$query=$this->db
				->select_sum("total")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}


}

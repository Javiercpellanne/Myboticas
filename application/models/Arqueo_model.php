<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Arqueo_model extends CI_Model
{	
	public $table = "arqueos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{		
		$query=$this->db
				->select("id, iduser, femision, finicial, ffinal, minicial, mfinal, estado")
				->from($this->table)
				->where($filtros)
				->order_by("id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("iduser, finicial, ffinal, minicial, mfinal, ventas, compras, ingresos, egresos")
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

	public function contador($anexo,$id)
	{
		$this->db->from($this->table)->where(array("idestablecimiento"=>$anexo,"iduser"=>$id,"estado"=>1));
		return $this->db->count_all_results();
	}






}

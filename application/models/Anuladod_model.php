<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anuladod_model extends CI_Model
{
	public $table = "anulados_detalle";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, idventa, idnota, motivo")
				->from($this->table)
				->where(array("idanulado"=>$id))
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("motivo")
				->from($this->table." d")
				->join("anulados a","d.idanulado=a.id")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function delete($id)
	{
		$this->db->where(array("idanulado"=>$id));
		$this->db->delete($this->table);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}

	public function mostrarVentas($id)
	{
		$query=$this->db
				->select("concat(v.serie,'-',v.numero) as venta")
				->from($this->table." a")
				->join("ventas v","a.idventa=v.id")
				->where(array("idanulado"=>$id))
				->get();
		return $query->result();
	}



}

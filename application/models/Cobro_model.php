<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cobro_model extends CI_Model
{
	public $table = "cobros";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("p.id, p.iduser, p.idnventa, p.femision, p.total, p.idtpago, p.documento, p.idarqueoc, m.descripcion as ntpago")
				->from($this->table." p")
				->join("tipo_pagos m","p.idtpago=m.id")
				->where($filtros)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("p.id, p.iduser, p.idnventa, p.femision, p.total, p.idtpago, p.documento, p.pago, p.saldo, m.descripcion as ntpago")
				->from($this->table." p")
				->join("tipo_pagos m","p.idtpago=m.id")
				->where(array("p.id"=>$id))
				->get();
		return $query->row();
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

	public function mediosPagos($filtros)
	{
		$query1=$this->db
				->select("idtpago, SUM(total) as total")
				->from($this->table)
				->where($filtros)
				->group_by("idtpago")
				->get_compiled_select();

		$query2=$this->db
				->select("idtpago, SUM(total) as total")
				->from("cobros_cpe")
				->where($filtros)
				->group_by("idtpago")
				->get_compiled_select();

		$query=$this->db->select("SUM(total) as total, idtpago")
				->from("($query1 UNION ALL $query2) medios")
				->group_by("idtpago")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}






}

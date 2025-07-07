<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cobroe_model extends CI_Model
{
	public $table = "cobros_cpe";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("p.id, p.iduser, p.idventa, p.femision, p.total, p.idtpago, p.documento, p.idarqueoc, p.pago, p.saldo, m.descripcion as ntpago")
				->from($this->table." p")
				->join("tipo_pagos m","p.idtpago=m.id")
				->where($filtros)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("p.id, p.iduser, p.idventa, p.femision, p.total, p.idtpago, p.documento, p.pago, p.saldo, m.descripcion as ntpago")
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






}

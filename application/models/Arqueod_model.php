<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Arqueod_model extends CI_Model
{
	public $table = "arqueos_detalle";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("d.id, d.idarqueo, d.idtpago, d.importe, m.descripcion as ntpago")
				->from($this->table." d")
				->join("tipo_pagos m","d.idtpago=m.id")
				->where(array("idarqueo"=>$id))
				->get();
		return $query->result();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}






}

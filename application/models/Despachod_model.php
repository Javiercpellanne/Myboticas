<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Despachod_model extends CI_Model
{
	public $table = "despachos_detalle";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{		
		$query=$this->db
				->select("id, iddespacho, idproducto, descripcion, unidad, cantidad, lote, fvencimiento")
				->from($this->table)
				->where(array("iddespacho"=>$id))
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{		
		$query=$this->db
				->select("iddespacho, idproducto, descripcion, unidad, cantidad, lote, fvencimiento")
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



}

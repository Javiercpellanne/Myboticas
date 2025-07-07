<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trasladod_model extends CI_Model
{
	public $table = "traslados_detalle";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, idproducto, descripcion, unidad, cantidad, precio, importe, calmacen, palmacen, lote, clote, fvencimiento")
				->from($this->table)
				->where(array("idtraslado"=>$id))
				->get();
		return $query->result();
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

	public function contador($id)
	{
		$this->db->from($this->table)->where(array("idtraslado"=>$id));
		return $this->db->count_all_results();
	}




}

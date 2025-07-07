<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nventad_model extends CI_Model
{
	public $table = "nventas_detalle";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("idnventa, idproducto, descripcion, unidad, cantidad, precio, dscto, importe, calmacen, palmacen, lote, fvencimiento, clote, controlado")
				->from($this->table)
				->where(array("idnventa"=>$id))
				->get();
		return $query->result();
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

	public function contador($dato)
	{
		$this->db->from($this->table)->where("idproducto",$dato);
		return $this->db->count_all_results();
	}



}

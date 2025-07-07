<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cotizaciond_model extends CI_Model
{
	public $table = "cotizaciones_detalle";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, idproducto, descripcion, unidad, factor, cantidad, precio, importe, descuentos")
				->from($this->table)
				->where(array("idcotizacion"=>$id))
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, idproducto, descripcion, unidad, factor, cantidad, precio, importe, descuentos")
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
		$this->db->where("id",$id);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where("id",$id);
		$this->db->delete($this->table);
	}

	public function contador($dato)
	{
		$this->db->from($this->table)->where("idproducto",$dato);
		return $this->db->count_all_results();
	}




}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ventad_model extends CI_Model
{
	public $table = "ventas_detalle";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, idventa, idproducto, descripcion, unidad, cantidad, valor, tprecio, precio, tafectacion, dscto, total, igv, importe, calmacen, palmacen, lote, fvencimiento, clote, descuentos, controlado")
				->from($this->table)
				->where(array("idventa"=>$id))
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("idventa, idproducto, descripcion, unidad, cantidad, valor, tprecio, precio, tafectacion, dscto, total, igv, importe, calmacen, palmacen, lote, fvencimiento, clote, descuentos, controlado")
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

	public function contador($dato)
	{
		$this->db->from($this->table)->where("idproducto",$dato);
		return $this->db->count_all_results();
	}



}

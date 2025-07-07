<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notad_model extends CI_Model
{
	public $table = "notas_detalle";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{		
		$query=$this->db
				->select("idnota, idproducto, descripcion, unidad, cantidad, valor, tprecio, precio, tafectacion, total, igv, importe, calmacen, palmacen, lote, fvencimiento, clote")
				->from($this->table)
				->where(array("idnota"=>$id))
				->get();
		return $query->result();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function contador($dato)
	{
		$this->db->from($this->table)->where(array("idproducto"=>$dato));
		return $this->db->count_all_results();
	}



}

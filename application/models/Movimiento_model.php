<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Movimiento_model extends CI_Model
{
	public $table = "movimientos";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("m.id, m.nulo, m.iduser, m.femision, m.importe, t.descripcion as nmtraslado, t.tipo")
				->from($this->table." m")
				->join("tipo_movimientos t","m.idtmovimiento=t.id")
				->where($filtros)
				->order_by("m.id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("m.id, m.idestablecimiento, m.nulo, m.idtmovimiento, m.femision, m.observaciones, m.importe, t.descripcion as nmtraslado, t.tipo")
				->from($this->table." m")
				->join("tipo_movimientos t","m.idtmovimiento=t.id")
				->where(array("m.id"=>$id))
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
		$this->db->where(array("id"=>$id));
		$this->db->update($this->table, $data);
	}

	public function montoTotal($filtros)
	{
		$query=$this->db
				->select_sum("importe")
				->from($this->table)
				->where($filtros)
				->order_by("id","desc")
				->get();
		return $query->row();
	}

	public function productoTotal($filtros)
	{
		$query=$this->db
				->select("d.id, calmacen, palmacen")
				->from($this->table." v")
				->join("movimientos_detalle d", "v.id = d.idmovimiento")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}



}

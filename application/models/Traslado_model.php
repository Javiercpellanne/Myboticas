<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Traslado_model extends CI_Model
{
	public $table = "traslados";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($anexo,$inicio,$fin)
	{
		$query=$this->db
				->select("id, iduser, idestablecimiento, nulo, femision, idestablecimientod, frecepcion, importe")
				->from($this->table)
				->where(array("femision>="=>$inicio,"femision<="=>$fin))
				->group_start()
				->where(array("idestablecimiento"=>$anexo))
				->or_where(array("idestablecimientod"=>$anexo))
				->group_end()
				->order_by("id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, iduser, idestablecimiento, nulo, femision, idestablecimientod, frecepcion, urecepcion, importe")
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
		$this->db->where(array("id"=>$id));
		$this->db->update($this->table, $data);
	}

	public function productoTotal($filtros)
	{
		$query=$this->db
				->select("d.id, calmacen, palmacen")
				->from($this->table." v")
				->join("traslados_detalle d", "v.id = d.idtraslado")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}



}

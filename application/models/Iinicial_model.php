<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Iinicial_model extends CI_Model
{
	public $table = "inventario_inicial";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($anexo,$id)
	{
		$query=$this->db
				->select("id, idestablecimiento, iduser, femision, idproducto, descripcion, cantidad, precio, importe, lote, fvencimiento")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"numero"=>$id))
				->get();
		return $query->result();
	}

	public function mostrarLimite($anexo,$inicio,$fin)
	{
		$query=$this->db
				->select("numero, sum(importe) as importe, max(femision) as femision, max(estado) as estado")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"femision>="=>$inicio,"femision<="=>$fin))
				->group_by("numero")
				->order_by("numero","desc")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, idestablecimiento, iduser, femision, idproducto, descripcion, cantidad, precio, importe, lote, fvencimiento")
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

	public function delete($id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->delete($this->table);
	}

	public function contador($anexo,$numero,$idproducto)
	{
		$this->db->from($this->table)->where(array("idestablecimiento"=>$anexo,"numero"=>$numero,"idproducto"=>$idproducto));
		return $this->db->count_all_results();
	}

	public function maximo($anexo)
	{
		$query=$this->db
				->select_max("numero")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo))
				->get();
		return $query->row();
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



}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kardexl_model extends CI_Model
{
	public $table = "kardex_lotes";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("id, iduser, fecha, concepto, idproducto, descripcion, nlote, entradaf, salidaf, saldof, documento, fregistro")
				->from($this->table)
				->where($filtros)
				->order_by("nlote, id")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, idestablecimiento, fecha, concepto, idproducto, descripcion, nlote, entradaf, salidaf, saldof, documento, fregistro")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		//echo $this->db->last_query();exit;
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

	public function delete($anexo,$idproducto)
	{
		$this->db->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto));
		$this->db->delete($this->table);
	}

	public function ultimo($anexo,$idproducto,$lote)
	{
		$query=$this->db
				->select("saldof, saldov")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto,"nlote"=>$lote))
				->limit(1)
				->order_by("id","desc")
				->get();
		return $query->row();
	}

	public function unicos($anexo,$idproducto)
	{
		$query=$this->db
				->select("nlote, max(id) as id")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto))
				->group_by("nlote")
				->order_by("id")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}


}

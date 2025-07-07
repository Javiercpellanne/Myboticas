<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lote_model extends CI_Model
{
	public $table = "lotes";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($anexo,$idproducto)
	{
		$query=$this->db
				->select("id, nlote, IFNULL(fvencimiento,'') as fvencimiento, inicial, stock, idproducto")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto,"stock>"=>0))
				->order_by("fvencimiento")
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("id, nlote, fvencimiento, inicial, stock, idproducto")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$anexo,$idproducto,$nlote)
	{
		$this->db->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto,"nlote"=>$nlote));
		$this->db->update($this->table, $data);
	}

	public function delete($filtros)
	{
		$this->db->where($filtros); //array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto,"nlote"=>$nlote)
		$this->db->delete($this->table);
	}

	public function stock($anexo,$idproducto)
	{
		$query=$this->db
				->select_sum("stock")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto,"stock>"=>0))
				->get();
		return $query->row();
	}

	public function ultimo($anexo,$idproducto)
	{
		$query=$this->db
				->select("id, nlote, fvencimiento, stock")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto))
				->limit(1)
				->order_by("fvencimiento")
				->get();
		return $query->row();
	}

	public function productosVencer($filtros)
	{
		$query=$this->db->select("p.id, p.descripcion, b.descripcion as nlaboratorio, l.fvencimiento, p.pventa, p.estado, l.nlote as lote, l.stock")
				->from($this->table." l")
				->join("productos p", "p.id=l.idproducto")
				->join("laboratorios b", "b.id = p.idlaboratorio", "left")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function fechasVencer($filtros)
	{
		$query=$this->db->select("DATE_FORMAT(fvencimiento, '%Y-%m') as fecha")
				->from($this->table)
				->where($filtros)
				->group_by("fecha")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}



}

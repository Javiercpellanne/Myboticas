<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kardex_model extends CI_Model
{
	public $table = "kardex";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("id, iduser, fecha, concepto, idproducto, descripcion, entradaf, salidaf, saldof, costo, entradav, salidav, saldov, documento, fregistro")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("id, idestablecimiento, fecha, concepto, idproducto, descripcion, entradaf, salidaf, saldof, costo, entradav, salidav, saldov, documento, fregistro")
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

	public function ultimo($filtros)
	{
		$query=$this->db
				->select("saldof, costo, saldov")
				->from($this->table)
				->where($filtros)
				->limit(1)
				->order_by("id","desc")
				->get();
		return $query->row();
	}

	public function agrupacionMensual($filtros)
	{
    $query = $this->db
        ->select("YEAR(fecha) as anio, MONTH(fecha) as mes")
        ->from($this->table)
        ->where($filtros)
        ->group_by(["YEAR(fecha)", "MONTH(fecha)"])  // Agrupación por año y mes
        ->get();
    return $query->result();
	}



}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serie_model extends CI_Model
{
	public $table = "series";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("s.id, s.serie, c.descripcion as ncomprobante")
				->from($this->table." s")
				->join("tipo_comprobantes c","s.tcomprobante=c.id")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrar($filtros)
	{		
		$query=$this->db
				->select("s.serie, s.tcomprobante, s.numero, c.descripcion as ncomprobante")
				->from($this->table." s")
				->join("tipo_comprobantes c","s.tcomprobante=c.id")
				->where($filtros)
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
		$this->db->where("id",$id);
		$this->db->update($this->table, $data);
	}

	public function contador($anexo)
	{
		$this->db->from($this->table)->where(array("idestablecimiento"=>$anexo));
		return $this->db->count_all_results();
	}


}

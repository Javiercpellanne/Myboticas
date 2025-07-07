<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_model extends CI_Model
{
	public $table = "resumenes";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("id, femision, fdocumento, tproceso, identificador, ticket, filename, has_xml, has_cdr, tipo_estado, respuesta_sunat, validado")
				->from($this->table)
				->where($filtros)
				->order_by("id","desc")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("id, femision, fdocumento, tproceso, identificador, ticket, filename, has_xml, has_cdr, respuesta_sunat, validado")
				->from($this->table)
				->where($filtros)
				->order_by("id","desc")
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

	public function delete($id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->delete($this->table);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}



}

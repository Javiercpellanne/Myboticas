<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudd_model extends CI_Model
{	
	public $table = "solicitudes_detalle";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{		
		$query=$this->db
				->select("idproducto, descripcion, unidad, factor, cantidad")
				->from($this->table)
				->where(array("idsolicitud"=>$id))
				->get();
		return $query->result();
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

	public function contador($dato)
	{
		$this->db->from($this->table)->where("idproducto",$dato); 
		return $this->db->count_all_results();
	}




}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumend_model extends CI_Model
{
	public $table = "resumenes_detalle";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("id, condicion, idventa, idnota, motivo")
				->from($this->table)
				->where(array("idresumen"=>$id))
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("condicion, motivo")
				->from($this->table." d")
				->join("resumenes r","d.idresumen=r.id")
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

	public function delete($id)
	{
		$this->db->where(array("idresumen"=>$id));
		$this->db->delete($this->table);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}

	public function mostrarVentas($id)
	{
		$query=$this->db
				->select("concat(v.serie,'-',v.numero) as venta")
				->from($this->table." r")
				->join("ventas v","r.idventa=v.id")
				->where(array("idresumen"=>$id))
				->get();
		return $query->result();
	}

	public function mostrarNotas($id)
	{
		$query=$this->db
				->select("concat(n.serie,'-',n.numero) as nota")
				->from($this->table." r")
				->join("notas n","r.idnota=n.id")
				->where(array("idresumen"=>$id))
				->get();
		return $query->result();
	}



}

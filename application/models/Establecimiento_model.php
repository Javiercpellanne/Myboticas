<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Establecimiento_model extends CI_Model
{
	public $table = "establecimientos";
	public $table_id = "id";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros=array())
	{
		$this->db->select("id, descripcion, iddepartamento, idprovincia, iddistrito, direccion, email, telefono, codigo, logoe, logot")
             ->from($this->table);
	    if (!empty($filtros)) {
	        $this->db->where($filtros); // Aplicar los filtros si están presentes
	    }
	    $query = $this->db->get();
	    return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("e.codigo, e.descripcion, e.iddepartamento, e.idprovincia, e.iddistrito, e.direccion, e.email, e.telefono, e.cdigemid, e.logoe, e.logot, d.descripcion as ndepartamento, p.descripcion as nprovincia, t.descripcion as ndistrito")
				->from($this->table." e")
				->join("departamentos d","e.iddepartamento=d.id")
				->join("provincias p","e.idprovincia=p.id")
				->join("distritos t","e.iddistrito=t.id")
				->where(array("e.id"=>$id))
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

	public function contador()
	{
		$this->db->from($this->table)->where(array("estado"=>1));
		return $this->db->count_all_results();
	}

	public function mostrarAcceso($acceso)
	{
		$query=$this->db
				->select("id, descripcion, codigo, direccion")
				->from($this->table)
				->where_in("id",$acceso)
				//->order_by("codigo")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}


}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Anusuario_model extends CI_Model
{
	public $table = "acceso_niveles_usuario";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($id)
	{
		$query=$this->db
				->select("a.valor")
				->from($this->table." u")
				->join("acceso_niveles a","u.idacceson=a.id")
				->where(array("iduser"=>$id))
				->get();

		$datos=array();
		foreach ($query->result() as $row)
		{
		        array_push($datos,$row->valor);
		}
		return $datos;
	}

	public function mostrar($filtro)
	{
		$query=$this->db
				->select("idacceson, iduser")
				->from($this->table)
				->where($filtro)
				->get();
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function delete($id)
	{
		$this->db->where("iduser",$id);
		$this->db->delete($this->table);
	}




}
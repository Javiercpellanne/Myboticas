<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controlm_model extends CI_Model
{
	public $table = "control_movimientos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("user, descripcion, pagina, tiempo")
				->from($this->table)
				//->where(array("tiempo>="=>$tiempo))
				->order_by("tiempo","desc")
				->get();
		return $query->result();
	}

	public function insertar($data=array())
	{
		$this->db->insert($this->table,$data);
		return $this->db->insert_id();
	}

	public function delete($tiempo)
	{
		$this->db->where('tiempo<=',$tiempo);
		$this->db->delete($this->table);
	}




}

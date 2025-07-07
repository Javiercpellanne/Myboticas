<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testado_model extends CI_Model
{
	public $table = "tipo_estado";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{
		$query=$this->db
				->select("id, descripcion, badge")
				->from($this->table)
				// ->order_by("mes")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("descripcion, badge")
				->from($this->table)
				->where(array("id"=>$id))
				->get();
		return $query->row();
	}


}
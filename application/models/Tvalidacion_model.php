<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tvalidacion_model extends CI_Model
{
	public $table = "tipo_validaciones";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("descripcion")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}


}

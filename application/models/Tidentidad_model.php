<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tidentidad_model extends CI_Model
{
	public $table = "tipo_identidades";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal()
	{		
		$query=$this->db
				->select("id, descripcion")
				->from($this->table)
				->get();
		return $query->result();
	}

}

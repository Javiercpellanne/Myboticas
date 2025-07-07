<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ttransporte_model extends CI_Model
{
	public $table = "tipo_transportes";
	public $table_id = "id";

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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Punto_model extends CI_Model
{
	public $table = "puntos";
	public $table_id = "id";
	
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrar()
	{		
		$query=$this->db
				->select("valorp, caducidad, canjep, canjev")
				->from($this->table)
				->where(array($this->table_id=>1))
				->get();
		return $query->row();
	}

	public function update($data=array())
	{
		$this->db->where($this->table_id,1);
		$this->db->update($this->table, $data);
	}


}

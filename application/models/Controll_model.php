<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controll_model extends CI_Model
{
	public $table = "control_logins";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($tiempo)
	{
		$query=$this->db
				->select("user, ip, nombre, navegador, soperativo, dispositivo, pagina, tiempo")
				->from($this->table)
				->where(array("tiempo>="=>$tiempo))
				->order_by("tiempo","desc")
				->get();
		return $query->result();
	}

	public function insertar($data=array())
	{
		$this->db->insert($this->table,$data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$user)
	{
		$this->db->where('user',$user);
		$this->db->update($this->table,$data);
	}

	public function delete($fecha)
	{
		$this->db->where('fecha<=',$tiempo);
		$this->db->delete($this->table);
	}

	public function contador($user)
	{
		$this->db->from($this->table)->where("user",$user);
		return $this->db->count_all_results();
	}




}

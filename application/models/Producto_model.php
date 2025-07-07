<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model
{
	public $table = "productos";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarLimite($filtros)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.descripcion, p.codbarra, p.idlaboratorio, p.mstock, p.tafectacion, p.lote, p.compra, p.venta, p.factor, p.pcompra, p.pventa, p.factorb, p.pblister, p.umedidav, p.umedidac, p.umedidab, p.estado, p.ruta, p.rsanitario, p.vsujeta, p.ruta, IFNULL(l.descripcion,'') as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where($filtros)
				->limit(100)
				->order_by("id", "desc")
				->get();
		return $query->result();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.descripcion, p.codbarra, p.idlaboratorio, p.clasificacion, p.mstock, p.tafectacion, p.lote, p.compra, p.venta, p.factor, p.pventa, p.pcompra, p.factorb, p.pblister, p.umedidav, p.umedidac, p.umedidab, p.estado, p.ruta, p.rsanitario, p.vsujeta, IFNULL(l.descripcion,'') as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where($filtros)
				->order_by("descripcion", "asc")
				->get();
		return $query->result();
	}

	public function mostrar($filtros)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.idcategoria, p.descripcion, p.idlaboratorio, p.clasificacion, p.codbarra, p.mstock, p.digemid, p.cdigemid, p.lote, p.tafectacion, p.compra, p.venta, p.factor, p.pcompra, p.pventa, p.factorb, p.pblister, p.umedidav, p.umedidac, p.umedidab, p.idpactivo, p.idegenerico, p.idaterapeutica, p.rsanitario, p.estado, p.ruta, p.idubicacion, p.informacion, p.vsujeta, p.ruta, IFNULL(l.descripcion,'') as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
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

	public function update($data=array(),$id)
	{
		$this->db->where("id",$id);
		$this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where("id",$id);
		$this->db->delete($this->table);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}

	public function buscador($nombre,$filtros)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.descripcion, p.idlaboratorio, p.lote, p.mstock, p.tafectacion, p.compra, p.venta, p.factor, p.pcompra, p.pventa, p.factorb, p.pblister, p.umedidav, p.umedidac, p.umedidab, p.estado, p.rsanitario, p.vsujeta, p.ruta, IFNULL(l.descripcion,'') as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where($filtros)
				->group_start()
				->like("p.descripcion", $nombre)//, "after"
				->or_like("l.descripcion", $nombre)
				->group_end()
				->limit(100)
				->order_by("p.descripcion")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function productosDgmi()
	{
		$query=$this->db
				->select("p.id, p.cdigemid, p.descripcion, p.factor, p.rsanitario, p.venta, p.pventa, l.descripcion as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where(array("cdigemid!="=>'',"estado"=>1))
				->order_by("cdigemid", 'asc')
				->get();
		return $query->result();
	}

	public function mostrarCatalogo()
	{
		$query=$this->db
				->select("id, descripcion, venta, pventa, pblister")
				->from($this->table)
				->order_by("id")
				->get();
		return $query->result();
	}

	public function codigo()
	{
		$query=$this->db
				->select("max(codbarra) as codbarra")
				->from($this->table)
				->where(array("LENGTH(codbarra)"=>6))
				->get();
		return $query->row();
	}

	public function mostrarEstado($filtros,$columna)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.descripcion, p.idlaboratorio, IFNULL(l.descripcion,'') as nlaboratorio")
				->from($this->table." p")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where($filtros)
				->limit(100)
				->order_by($columna, "desc")
				->get();
		return $query->result();
	}




	// Obtener productos con paginación
  public function totalProductos($filtros, $limit, $offset, $search = NULL)
  {
    $query = $this->db
		    ->select('p.id, p.descripcion, p.lote, l.descripcion as nlaboratorio')
		    ->from($this->table . " p")
		    ->join("laboratorios l", "l.id = p.idlaboratorio", "left")
		    ->where($filtros)
		    ->limit($limit, $offset);
				if ($search) {
			    $query->group_start()
			          ->like("p.descripcion", $search)
			          ->or_like("l.descripcion", $search)
			          ->group_end();
				}
		return $query->get()->result();

  }

  // Obtener el total de productos
  public function contadorProductos($filtros, $search = NULL)
  {
    $query = $this->db
		    ->from($this->table . " p")
		    ->join("laboratorios l", "l.id = p.idlaboratorio", "left")
		    ->where($filtros);
				if ($search) {
			    $query->group_start()
			          ->like('p.descripcion', $search)
			          ->or_like('l.descripcion', $search)  // Puedes agregar más campos a filtrar
			          ->group_end();
				}
		return $query->count_all_results();
  }



}

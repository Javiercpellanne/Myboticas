<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inventario_model extends CI_Model
{
	public $table = "inventarios";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("i.id, i.idestablecimiento, i.idproducto, i.stock, i.venta, i.pventa, i.pblister, e.descripcion as nestablecimiento")
				->from($this->table." i")
				->join("establecimientos e","i.idestablecimiento=e.id")
				->where($filtros)
				->get();
		return $query->result();
	}

	public function mostrar($anexo,$idproducto)
	{
		$query=$this->db
				->select("id, idestablecimiento, idproducto, stock, venta, pventa, pblister")
				->from($this->table)
				->where(array("idestablecimiento"=>$anexo,"idproducto"=>$idproducto))
				->get();
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$filtros)
	{
		$this->db->where($filtros);
		$this->db->update($this->table, $data);
	}

	public function cantidadTotal($filtros)
	{
		$query=$this->db
				->select_sum("stock")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}

	public function productosMinimo($filtros)
	{
		$query=$this->db->select("p.id, p.descripcion, p.mstock, (stock-mstock) as actual, b.descripcion as nlaboratorio, i.stock")
				->from($this->table." i")
				->join("productos p", "p.id = i.idproducto")
				->join("laboratorios b", "b.id = p.idlaboratorio", "left")
				->where($filtros)
				->limit(200)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function productosStock($filtros)
	{
		$query=$this->db->select("p.id, p.descripcion, p.rsanitario, p.lote, p.pcompra, p.pventa, p.venta, p.pblister, b.descripcion as nlaboratorio, i.stock")
				->from($this->table." i")
				->join("productos p", "p.id = i.idproducto")
				->join("laboratorios b", "b.id = p.idlaboratorio", "left")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	// public function productosStock($anexo)
	// {
	// 	$query=$this->db->select("p.id, p.descripcion, p.rsanitario, p.lote, p.pcompra, p.pventa, p.venta, p.pblister, b.descripcion as nlaboratorio, i.stock, i.pventa as pventai, i.venta as ventai, k.saldov,
  //           k.saldof")
	// 			->from($this->table." i")
	// 			->join("productos p", "p.id = i.idproducto")
	// 			->join("laboratorios b", "b.id = p.idlaboratorio", "left")
	// 			->join("(SELECT saldof, saldov, idproducto FROM kardex ORDER BY id DESC LIMIT 1) k", "k.idproducto = p.id", "left")
	// 			->where(array("idestablecimiento"=>$anexo,"estado"=>1,"stock>"=>0))
	// 			->get();
	// 	//echo $this->db->last_query();exit;
	// 	return $query->result();
	// }

	public function mostrarStock($filtros = array(), $limit = null, $offset = null)
	{
		$query=$this->db
				->select("p.id, p.tipo, p.descripcion, p.factor, p.pcompra, p.compra, p.pventa, p.venta, i.pventa as pventa2, i.venta as venta2, l.descripcion as nlaboratorio, i.stock")
				->from($this->table." i")
				->join("productos p", "p.id = i.idproducto")
				->join("laboratorios l", "l.id = p.idlaboratorio", "left")
				->where($filtros)
				->order_by("descripcion", "asc");

	    if ($limit !== null && $offset !== null) {
	        $this->db->limit($limit, $offset);
	    }

	    $query = $this->db->get();
	    return $query->result();
	}


}

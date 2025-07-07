<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compra_model extends CI_Model
{
	public $table = "compras";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.comprobante, v.serie, v.numero, v.proveedor, v.idproveedor, v.femision, v.subtotal, v.igv, v.total, v.percepcion, v.condicion, v.cancelado, v.almacen, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.comprobante=c.id")
				->where($filtros)//,"nulo"=>0
				->order_by("id","desc")
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.nulo, v.comprobante, v.serie, v.numero, v.proveedor, v.idproveedor, v.femision, v.incluye, v.dscto, v.tgravado, v.tinafecto, v.texonerado, v.tgratuito, v.subtotal, v.igv, v.total, v.percepcion, v.condicion, v.cancelado, v.almacen, v.dadicional, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.comprobante=c.id")
				->where(array("v.id"=>$id))
				->get();
		return $query->row();
	}

	public function insert($data=array())
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function update($data=array(),$id)
	{
		$this->db->where(array("id"=>$id));
		$this->db->update($this->table, $data);
	}

	public function contador($filtros)
	{
		$this->db->from($this->table)->where($filtros);
		return $this->db->count_all_results();
	}

	public function montoTotal($filtros)
	{
		$query=$this->db
				->select_sum("total")
				->from($this->table)
				->where($filtros)
				->get();
		return $query->row();
	}

	public function cproducto($anexo,$inicio,$fin,$idproducto)
	{
		$query=$this->db
				->select("v.comprobante, v.serie, v.numero, v.proveedor, v.femision, v.incluye, c.descripcion as ncomprobante, d.unidad, d.cantidad, d.precio, d.tafectacion, d.importe, d.lote, d.fvencimiento")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.comprobante=c.id")
				->join("compras_detalle d", "v.id = d.idcompra")
				->where(array("idestablecimiento"=>$anexo,"nulo"=>0,"femision>="=>$inicio,"femision<="=>$fin,"idproducto"=>$idproducto))
				->order_by("femision","desc")
				->get();
		return $query->result();
	}

	public function ultimas($anexo,$idproducto)
	{
		$query=$this->db
				->select("v.serie, v.numero, v.proveedor, v.femision, v.incluye, d.tafectacion, d.unidad, d.factor, d.cantidad, d.precio, d.importe, d.lote, d.fvencimiento")
				->from($this->table." v")
				->join("compras_detalle d", "v.id = d.idcompra")
				->where(array("idestablecimiento"=>$anexo,"nulo"=>0,"idproducto"=>$idproducto))
				->order_by("femision","desc")
				->limit(20)
				->get();
		return $query->result();
	}

	public function mostrarRegistro($filtros)
	{
		$query=$this->db
				->select("c.femision, c.comprobante, c.serie, c.numero, c.proveedor, c.idproveedor, c.incluye, c.tgravado, c.tinafecto, c.texonerado, c.tgratuito, c.subtotal, c.igv, c.total, c.condicion, c.cancelado, p.tdocumento, p.documento")
				->from($this->table." c")
				->join("proveedores p","c.idproveedor=p.id")
				->where($filtros)//,"nulo"=>0
				->order_by("c.id")
				->get();
		return $query->result();
	}

	public function psicotropicos($anuo,$mes,$idproducto)
	{
		$query1=$this->db
				->select("'S' as tipo, femision, controlado as detalle, calmacen")
				->from("nventas v")
				->join("nventas_detalle d", "v.id = d.idnventa")
				->where(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"idproducto"=>$idproducto,"nulo"=>0))
				->get_compiled_select();

		$query2=$this->db
				->select("'S' as tipo, femision, controlado as detalle, calmacen")
				->from("ventas v")
				->join("ventas_detalle d", "v.id = d.idventa")
				->where(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"idproducto"=>$idproducto,"nulo"=>0))
				->get_compiled_select();

		$query3 = $this->db
		    ->select("'I' as tipo, femision, CONCAT(c.descripcion, ' ', serie, '-', numero) as detalle, calmacen")
		    ->from("compras v")
		    ->join("compras_detalle d", "v.id = d.idcompra")
		    ->join("tipo_comprobantes c", "v.comprobante = c.id")
		    ->where(array('year(femision)' => $anuo, 'month(femision)' => $mes, "idproducto" => $idproducto,"nulo"=>0))
		    ->get_compiled_select();

		$query=$this->db->select("tipo, femision, detalle, calmacen")
				->from("($query1 UNION ALL $query2 UNION ALL $query3) ganancias")
				->order_by('femision')
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

}

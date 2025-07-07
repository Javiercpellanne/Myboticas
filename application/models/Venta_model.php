<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model
{
	public $table = "ventas";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarLimite($filtros,$orden)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, v.hemision, v.subtotal, v.tigv, v.total, v.izipay, v.fpago, v.condicion, v.has_xml, v.has_pdf, v.has_cdr, v.filename, v.tipo_estado, v.respuesta_rectificar, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->where($filtros)
				->limit(3)
				->order_by("v.id",$orden)
				->get();
		return $query->result();
	}

	public function mostrarTotal($filtros,$orden)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, v.hemision, v.subtotal, v.tigv, v.total, v.izipay, v.fpago, v.condicion, v.has_xml, v.has_pdf, v.has_cdr, v.filename, v.tipo_estado, v.respuesta_rectificar, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->where($filtros)
				->order_by("v.id",$orden)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tipo_soap, v.grupo, v.tcomprobante, v.serie, v.numero, v.femision, v.hemision, v.fvencimiento, v.toperacion, v.moneda, v.cliente, v.idcliente, v.tdscto, v.tcargo, v.tgravado, v.tinafecto, v.texonerado, v.tgratuito, v.subtotal, v.tigv, v.total, v.dadicional, v.ocompra, v.descuentos, v.retencion, v.detraccion, v.izipay, v.efectivo, v.vuelto, v.condicion, v.cuotas, v.mcuota, v.pcuota, v.fpago, v.cancelado, v.idvendedor, v.hash, v.filename, v.tipo_estado, v.respuesta_sunat, v.lote, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
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

	public function maximo($serie)
	{
		$query=$this->db
				->select_max("numero")
				->from($this->table)
				->where("serie",$serie)
				->get();
		return $query->row();
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

	public function rangoSerie($serie)
	{
		$query=$this->db
				->select("min(numero) as minimo, max(numero) as maximo")
				->from($this->table)
				->where(array("serie"=>$serie))
				->get();
		return $query->row();
	}

	public function mostrarFechas($filtros)
	{
		$query1=$this->db
				->select("femision")
				->from($this->table)
				->where($filtros)
				->group_by("femision")
				->get_compiled_select();

		$query2=$this->db
				->select("femision")
				->from("notas")
				->where($filtros)
				->group_by("femision")
				->get_compiled_select();

		$query=$this->db->select("femision")
				->from("($query1 UNION $query2) fechas")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function vproducto($filtros)
	{
		$query=$this->db
				->select("v.iduser, v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, c.descripcion as ncomprobante, d.unidad, d.cantidad, d.precio, d.importe")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->join("ventas_detalle d", "v.id = d.idventa")
				->where($filtros)
				->order_by("femision","desc")
				->get();
		return $query->result();
	}

	public function mostrarRegistro($filtros)
	{
		$query=$this->db
				->select("v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, v.tgravado, v.tinafecto, v.texonerado, v.subtotal, v.tigv, v.total, v.condicion, v.cancelado, v.tipo_estado, c.tdocumento, c.documento")
				->from($this->table." v")
				->join("clientes c","v.idcliente=c.id")
				->where($filtros)//,"nulo"=>0
				->order_by("v.id")
				->get();
		return $query->result();
	}

	public function consulta($filtros)
	{
		$query=$this->db
				->select("v.serie, v.numero, v.femision, v.cliente, v.total, v.filename, v.has_xml, v.has_pdf, v.has_cdr, c.documento")
				->from($this->table." v")
				->join("clientes c","v.idcliente=c.id")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}

	public function ultimas($anexo,$idproducto)
	{
		$query=$this->db
				->select("v.iduser, v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, c.descripcion as ncomprobante, d.unidad, d.cantidad, d.precio, d.importe, d.calmacen, d.palmacen, d.lote, d.fvencimiento")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->join("ventas_detalle d", "v.id = d.idventa")
				->where(array("idestablecimiento"=>$anexo,"nulo"=>0,"idproducto"=>$idproducto,"calmacen>"=>0))
				->order_by("femision","desc")
				->limit(20)
				->get();
		return $query->result();
	}

	public function productoTotal($filtros)
	{
		$query=$this->db
				->select("MAX(d.id) as id, SUM(calmacen) as calmacen, MAX(palmacen) as palmacen")
				->from($this->table." v")
				->join("ventas_detalle d", "v.id = d.idventa")
				->where($filtros)
				->get();
		return $query->row();
	}



}

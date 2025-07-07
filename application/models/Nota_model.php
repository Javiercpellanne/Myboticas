<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nota_model extends CI_Model
{
	public $table = "notas";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarLimite($filtros,$orden)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tcomprobante, v.serie, v.numero, v.tnota, v.motivo, v.idcliente, v.cliente, v.femision, v.hemision, v.tgravado, v.tinafecto, v.texonerado, v.subtotal, v.tigv, v.total, v.idventa, v.has_xml, v.has_pdf, v.has_cdr, v.filename, v.tipo_estado, v.respuesta_rectificar, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->where($filtros)
				->limit(3)
				->order_by("v.id",$orden)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrarTotal($filtros,$orden)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tcomprobante, v.serie, v.numero, v.tnota, v.motivo, v.idcliente, v.cliente, v.femision, v.hemision, v.tgravado, v.tinafecto, v.texonerado, v.subtotal, v.tigv, v.total, v.idventa, v.has_xml, v.has_pdf, v.has_cdr, v.filename, v.tipo_estado, v.respuesta_rectificar, c.descripcion as ncomprobante")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->where($filtros)
				->order_by("v.id",$orden)
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("v.id, v.idestablecimiento, v.iduser, v.nulo, v.tipo_soap, v.tcomprobante, v.serie, v.numero, v.moneda, v.tnota, v.motivo, v.idcliente, v.cliente, v.femision, v.hemision, v.tgravado, v.tinafecto, v.texonerado, v.tgratuito, v.subtotal, v.tigv, v.total, v.hash, v.filename, v.idventa, v.tipo_estado, v.respuesta_sunat, c.descripcion as ncomprobante")
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

	public function vproducto($filtros)
	{
		$query=$this->db
				->select("v.iduser, v.tcomprobante, v.serie, v.numero, v.cliente, v.idcliente, v.femision, c.descripcion as ncomprobante, d.unidad, d.cantidad, d.precio, d.importe")
				->from($this->table." v")
				->join("tipo_comprobantes c","v.tcomprobante=c.id")
				->join("notas_detalle d", "v.id = d.idnota")
				->where($filtros)
				->order_by("femision","desc")
				->get();
		return $query->result();
	}

	public function ganancia($filtros)
	{
		$query=$this->db
				->select("d.idproducto, MAX(d.descripcion) as descripcion, SUM(if(p.tipo = 'B', d.calmacen, d.cantidad)) as cantidad, SUM(d.importe) as importe, SUM(d.calmacen*d.palmacen) as costo")
				->from($this->table." v")
				->join("notas_detalle d", "v.id = d.idnota")
				->join("productos p", "d.idproducto = p.id")
				->where($filtros)
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}

	public function mostrarRegistro($filtros)
	{
		$query=$this->db
				->select("v.tcomprobante, v.serie, v.numero, v.idcliente, v.cliente, v.femision, v.tgravado, v.tinafecto, v.texonerado, v.subtotal, v.tigv, v.total, v.tipo_estado, v.idventa, c.tdocumento, c.documento")
				->from($this->table." v")
				->join("clientes c","v.idcliente=c.id")
				->where($filtros)//,"nulo"=>0
				->order_by("v.id")
				->get();
		return $query->result();
	}

	public function productoTotal($filtros)
	{
		$query=$this->db
				->select_sum("calmacen")
				->from($this->table." v")
				->join("notas_detalle d", "v.id = d.idnota")
				->where($filtros)
				->get();
		return $query->row();
	}



}

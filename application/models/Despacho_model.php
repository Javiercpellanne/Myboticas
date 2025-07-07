<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Despacho_model extends CI_Model
{
	public $table = "despachos";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarTotal($filtros,$orden)
	{
		$query=$this->db
				->select("d.id, d.idestablecimiento, d.iduser, d.tcomprobante, d.serie, d.numero, d.cliente, d.idcliente, d.femision, d.fenvio, d.has_xml, d.has_pdf, d.has_cdr, d.filename, d.tipo_estado, d.ticket, c.descripcion as ncomprobante")
				->from($this->table." d")
				->join("tipo_comprobantes c","d.tcomprobante=c.id")
				->where($filtros)
				->order_by("d.id",$orden)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("d.idestablecimiento, d.iduser, d.tcomprobante, d.serie, d.numero, d.femision, d.hemision, d.cliente, d.idcliente, d.observaciones, d.idttransporte, d.idttraslado, d.descripcion_traslado, d.fenvio, d.unidad_peso, d.peso_total, d.paquetes, d.codigo_origen, d.ubigeo_origen, d.direccion_origen, d.codigo_entrega, d.ubigeo_entrega, d.direccion_entrega, d.m1l, d.tdocumento_transporte, d.ndocumento_transporte, d.nombres_transporte, d.licencia_conducir, d.placa, d.filename, d.ticket, d.qr, d.tipo_estado, d.respuesta_sunat, c.descripcion as ncomprobante, m.descripcion as nmodot, t.descripcion as motivot")
				->from($this->table." d")
				->join("tipo_comprobantes c","d.tcomprobante=c.id")
				->join("tipo_transportes m","d.idttransporte=m.id")
				->join("tipo_traslados t","d.idttraslado=t.id")
				->where(array("d.id"=>$id))
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

	public function rangoSerie($serie)
	{
		$query=$this->db
				->select("min(numero) as minimo, max(numero) as maximo")
				->from($this->table)
				->where(array("serie"=>$serie))
				->get();
		return $query->row();
	}




}

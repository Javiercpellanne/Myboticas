<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Nventa_model extends CI_Model
{
	public $table = "nventas";

	public function __construct()
	{
		parent::__construct();
	}

	public function mostrarLimite($filtros,$orden)
	{
		$query=$this->db
				->select("id, idestablecimiento, iduser, nulo, serie, numero, formato, cliente, idcliente, femision, hemision, total, izipay, fpago, condicion, cancelado, emitido")
				->from($this->table)
				->where($filtros)
				->limit(3)
				->order_by("id",$orden)
				->get();
		return $query->result();
	}

	public function mostrarTotal($filtros,$orden)
	{
		$query=$this->db
				->select("id, idestablecimiento, iduser, nulo, serie, numero, formato, cliente, idcliente, femision, hemision, total, izipay, fpago, condicion, cancelado, emitido")
				->from($this->table)
				->where($filtros)
				->order_by("id",$orden)
				->get();
		return $query->result();
	}

	public function mostrar($id)
	{
		$query=$this->db
				->select("idestablecimiento, nulo, iduser, serie, numero, cliente, idcliente, femision, hemision, dscto, cargo, total, dadicional, izipay, efectivo, vuelto, condicion, cuotas, mcuota, pcuota, fpago, cancelado, lote, emitido, idvendedor")
				->from($this->table)
				->where(array("id"=>$id))
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

	public function vproducto($filtros)
	{
		$query=$this->db
				->select("v.iduser, v.serie, v.numero, v.cliente, v.idcliente, v.femision, d.unidad, d.cantidad, d.precio, d.importe")
				->from($this->table." v")
				->join("nventas_detalle d", "v.id = d.idnventa")
				->where($filtros)
				->order_by("femision","desc")
				->get();
		return $query->result();
	}

	public function ganancia($filtros)
	{
		$query1=$this->db
				->select("d.idproducto, MAX(d.descripcion) as descripcion, SUM(d.dscto) as dscto, SUM(if(p.tipo = 'B', d.calmacen, d.cantidad)) as cantidad, SUM(d.importe) as importe, SUM(d.calmacen*d.palmacen) as costo")
				->from("nventas v")
				->join("nventas_detalle d", "v.id = d.idnventa")
				->join("productos p", "d.idproducto = p.id")
				->where($filtros)
				->group_by("d.idproducto")
				->get_compiled_select();

		$query2=$this->db
				->select("d.idproducto, MAX(d.descripcion) as descripcion, SUM(d.dscto+(d.dscto*0.18)) as dscto, SUM(if(p.tipo = 'B', d.calmacen, d.cantidad)) as cantidad, SUM(d.importe) as importe, SUM(d.calmacen*d.palmacen) as costo")
				->from("ventas v")
				->join("ventas_detalle d", "v.id = d.idventa")
				->join("productos p", "d.idproducto = p.id")
				->where($filtros)
				->group_by("d.idproducto")
				->get_compiled_select();

		$query=$this->db->select("idproducto, max(descripcion) as descripcion, sum(dscto) as dscto, sum(cantidad) as cantidad, sum(importe) as importe, sum(costo) as costo")
				->from("($query1 UNION ALL $query2) ganancias")
				->where('importe!=',null)
				->group_by("idproducto")
				->order_by("descripcion asc")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
	}

	public function bonos($filtros)
	{
		$query1=$this->db
				->select("SUM(if(p.tipo = 'B', d.calmacen, d.cantidad))*MAX(b.monto) as monto")
				->from("nventas v")
				->join("nventas_detalle d", "v.id = d.idnventa")
				->join("bonificados b", "b.anuo=year(v.femision) AND b.mes=month(v.femision)")
				->join("productos p", "d.idproducto = p.id")
				->where($filtros)
				->get_compiled_select();

		$query2=$this->db
				->select("SUM(if(p.tipo = 'B', d.calmacen, d.cantidad))*MAX(b.monto) as monto")
				->from("ventas v")
				->join("ventas_detalle d", "v.id = d.idventa")
				->join("bonificados b", "b.anuo=year(v.femision) AND b.mes=month(v.femision)")
				->join("productos p", "d.idproducto = p.id")
				->where($filtros)
				->get_compiled_select();

		$query=$this->db->select("sum(monto) as monto")
				->from("($query1 UNION ALL $query2) bonos")
				->get();
		//echo $this->db->last_query();exit;
		return $query->row();
	}

	public function mostrarPrecios($anuo,$mes,$idproducto)
	{
		$query1=$this->db
				->select("precio")
				->from("nventas v")
				->join("nventas_detalle d", "v.id = d.idnventa")
				->where(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"idproducto"=>$idproducto))
				->group_by("precio")
				->get_compiled_select();

		$query2=$this->db
				->select("precio")
				->from("ventas v")
				->join("ventas_detalle d", "v.id = d.idventa")
				->where(array('year(femision)'=>$anuo,'month(femision)'=>$mes,"idproducto"=>$idproducto))
				->group_by("precio")
				->get_compiled_select();

		$query=$this->db->select("precio")
				->from("($query1 UNION ALL $query2) ganancias")
				//->group_by("precio")
				->get();
		//echo $this->db->last_query();exit;
		return $query->result();
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

	public function ultimas($anexo,$idproducto)
	{
		$query=$this->db
				->select("v.iduser, v.serie, v.numero, v.cliente, v.idcliente, v.femision, d.unidad, d.cantidad, d.precio, d.importe, d.calmacen, d.palmacen, d.lote, d.fvencimiento")
				->from($this->table." v")
				->join("nventas_detalle d", "v.id = d.idnventa")
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
				->join("nventas_detalle d", "v.id = d.idnventa")
				->where($filtros)
				->get();
		return $query->row();
	}



}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa_model extends CI_Model
{
	public $table = "empresa";
	public function __construct()
	{
		parent::__construct();
	}

	public function mostrar()
	{		
		$query=$this->db
				->select("id, ruc, nombres, ncomercial, producto, dscto, lventa, lstock, pesencial, logo, lticket, lestablecimiento, ticket, pie, piec, time, compra, arqueo, pventa, pestablecimiento, spuntos, pcompra, gunidad, gblister, gcaja, vbonificar, facturacion, tipo_soap, envio_automatico, envio_boleta, envio_guia, usuario_soap, clave_soap, certificado, certificado_clave, certificado_vence, edicion, id_validador, secret_validador, token_validador, fecha_validador, expires_validador, id_gre, secret_gre, token_gre, fecha_gre, expires_gre")
				->from($this->table)
				->get();
		return $query->row();
	}

	public function update($data=array())
	{
		$this->db->where("id",1);
		$this->db->update($this->table, $data);
	}

}

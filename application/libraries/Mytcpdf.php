<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require('tcpdf/tcpdf.php');

class Mytcpdf extends TCPDF
{
	function __construct()
	{
		parent::__construct();
		//$this->$ci =& get_instance();
	}
}


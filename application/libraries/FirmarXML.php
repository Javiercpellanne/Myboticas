<?php
require_once("Signature.php");

class FirmarXML
{
  public function FirmarDocumento($nombrexml,$certificado,$clave)
  {
    //firma del documento
    $objSignature = new Signature();
    $flg_firma = "0";
    $ruta = $nombrexml.'.xml';
    $resp = $objSignature->signature_xml($flg_firma, $ruta, $certificado, $clave);
    //print_r($resp);
    return $resp['hash_cpe'];
  }


}
?>

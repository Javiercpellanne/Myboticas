<?php
class ApiFacturacion
{
	function EnviarComprobanteElectronico($emisor, $nombre, $ruta_archivo_zip, $ruta_archivo_cdr) // envio de comprobante
	{
		//Generar el .zip  FE_PRODUCCION_ALTERNATE = 'https://www.sunat.gob.pe/ol-ti-itcpfegem/billService';
		$ruta = $ruta_archivo_zip.'.xml';
		$zip = new ZipArchive();
		$nombrezip = $nombre.".zip";
		$rutazip = $ruta_archivo_zip.".zip";
		if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre.'.xml');
			$zip->close();
		}

		//Enviamos el archivo a sunat
		if ($emisor->tipo_soap=='01') {
			$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
		} else {
			$ws = "https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl";
		}

		$contenido_del_zip = base64_encode(file_get_contents($rutazip));
		$xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
		 	<soapenv:Header>
		 		<wsse:Security>
		 			<wsse:UsernameToken>
		 				<wsse:Username>'.$emisor->usuario_soap.'</wsse:Username>
		 				<wsse:Password>'.$emisor->clave_soap.'</wsse:Password>
		 			</wsse:UsernameToken>
		 		</wsse:Security>
		 	</soapenv:Header>
		 	<soapenv:Body>
		 		<ser:sendBill>
		 			<fileName>'.$nombrezip.'</fileName>
		 			<contentFile>'.$contenido_del_zip.'</contentFile>
		 		</ser:sendBill>
		 	</soapenv:Body>
		</soapenv:Envelope>';

		$header = array(
			"Content-type: text/xml; charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: ",
			"Content-lenght: ".strlen($xml_envio)
		);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$response = curl_exec($ch);
		unlink($rutazip);

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$estado = 0;
		$mensaje = "";
		$codigo = "";

		if ($httpcode == 200 || $httpcode == 500) {
			$estado = 1;
			$doc = new DOMDocument();
			$doc->loadXML($response);

			if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
				$cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
				$cdr = base64_decode($cdr);
				file_put_contents($ruta_archivo_cdr."R-".$nombrezip, $cdr);

				$zip = new ZipArchive;
				if($zip->open($ruta_archivo_cdr."R-".$nombrezip)===true){
					$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre.'.xml');
					$zip->close();
				}

				$doc_cdr = new DOMDocument();
				$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre.'.xml');
				$doc_cdr->loadXML($infocdr);
				$codigo = $doc_cdr->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
				$mensaje = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;
				unlink($ruta_archivo_cdr.'R-'.$nombre.'.xml');
			}else{
				$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
				$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
			}
		} else {
			$httpMessage = curl_getinfo($ch, CURLINFO_HTTP_CONNECTCODE);
			$codigo = "";
			$mensaje = $httpMessage;
		}

		$resultado = array(
			"estado" 	=>$estado,
			"mensaje"	=>$mensaje,
			"codigo"	=>$codigo,
		);
		curl_close($ch);
		return $resultado;
	}

	function EnviarResumenComprobantes($emisor, $nombre, $ruta_archivo_zip) // envio de resumen y anulaciones
	{
		$ruta = $ruta_archivo_zip.'.xml';
		$zip = new ZipArchive();
		$nombrezip = $nombre.".zip";
		$rutazip = $ruta_archivo_zip.".zip";

		if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre.'.xml');
			$zip->close();
		}

		//Enviamos el archivo a sunat
		if ($emisor->tipo_soap=='01') {
			$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
		} else {
			$ws = "https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl";
		}

		$contenido_del_zip = base64_encode(file_get_contents($rutazip));
		$xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
		 <soapenv:Header>
		 	<wsse:Security>
		 		<wsse:UsernameToken>
		 			<wsse:Username>'.$emisor->usuario_soap.'</wsse:Username>
		 			<wsse:Password>'.$emisor->clave_soap.'</wsse:Password>
		 		</wsse:UsernameToken>
		 	</wsse:Security>
		 </soapenv:Header>
		 <soapenv:Body>
		 	<ser:sendSummary>
		 		<fileName>'.$nombrezip.'</fileName>
		 		<contentFile>'.$contenido_del_zip.'</contentFile>
		 	</ser:sendSummary>
		 </soapenv:Body>
		</soapenv:Envelope>';


		$header = array(
			"Content-type: text/xml; charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: ",
			"Content-lenght: ".strlen($xml_envio)
		);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$response = curl_exec($ch);
		unlink($rutazip);

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$resultado['ticket'] = '';
		$doc = new DOMDocument();
		$doc->loadXML($response);

		if (isset($doc->getElementsByTagName('ticket')->item(0)->nodeValue)) {
      $ticket = $doc->getElementsByTagName('ticket')->item(0)->nodeValue;
			$resultado['ticket'] = $ticket;
			$resultado['mensaje'] = 'Se genero el ticket '.$ticket;
		}else{
			$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
			$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
			$resultado['mensaje'] = $codigo.": ".$mensaje;
		}
		curl_close($ch);
		return $resultado;
	}

	function ConsultarTicket($emisor, $nombre, $ticket, $ruta_archivo_cdr) //estado de resumen por ticket
	{
		if ($emisor->tipo_soap=='01') {
			$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";
		} else {
			$ws = "https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService?wsdl";
		}

		$xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
      <soapenv:Header>
        <wsse:Security>
	        <wsse:UsernameToken>
	        <wsse:Username>'.$emisor->usuario_soap.'</wsse:Username>
	        <wsse:Password>'.$emisor->clave_soap.'</wsse:Password>
	        </wsse:UsernameToken>
        </wsse:Security>
      </soapenv:Header>
      <soapenv:Body>
        <ser:getStatus>
        	<ticket>'.$ticket.'</ticket>
        </ser:getStatus>
      </soapenv:Body>
    </soapenv:Envelope>';

		$header = array(
			"Content-type: text/xml; charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: ",
			"Content-lenght: ".strlen($xml_envio)
		);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$response = curl_exec($ch);

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$estado = 0;
		$mensaje = "";
		$codigo = "";

		$doc = new DOMDocument();
		$doc->loadXML($response);
		$respuesta=$doc->getElementsByTagName('statusCode')->item(0)->nodeValue;
		if ('98' == $respuesta) {
			$estado = 2;
			$codigo = '98';
			$mensaje = "El procesamiento del comprobante aún no ha terminado";
		} else {
			if(isset($doc->getElementsByTagName('content')->item(0)->nodeValue)){
				$cdr = $doc->getElementsByTagName('content')->item(0)->nodeValue;
				$cdr = base64_decode($cdr);
				file_put_contents($ruta_archivo_cdr."R-".$nombre.".zip", $cdr);

				$zip = new ZipArchive;
				if($zip->open($ruta_archivo_cdr."R-".$nombre.".zip")===true){
					$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre.'.xml');
					$zip->close();
				}

				$doc_cdr = new DOMDocument();
				$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre.'.xml');
				$doc_cdr->loadXML($infocdr);
				$estado = 1;
				$codigo = $doc_cdr->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
				$mensaje = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;
				unlink($ruta_archivo_cdr.'R-'.$nombre.'.xml');
			}else{
				$estado = 2;
				$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
				$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
			}
		}
		curl_close($ch);

		$resultado = array(
			"estado"				=>$estado,
			"mensaje"	=>$mensaje,
			"codigo"	=>$codigo,
		);
		return $resultado;
	}

	function consultarCdr($emisor,$comprobante,$serie,$numero, $ruta_archivo_cdr) //estado cdr de comprobante
	{
		$url = "https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl";
		$nombre = $emisor->ruc.'-'.$comprobante.'-'.$serie.'-'.$numero;
		$nombrezip = $nombre.".zip";

		// xml post structure
		$xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
		xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe"
		xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<soapenv:Header>
				<wsse:Security>
					<wsse:UsernameToken>
						<wsse:Username>'.$emisor->usuario_soap.'</wsse:Username>
						<wsse:Password>'.$emisor->clave_soap.'</wsse:Password>
					</wsse:UsernameToken>
				</wsse:Security>
			</soapenv:Header>
			<soapenv:Body>
				<ser:getStatusCdr>
					<rucComprobante>'.$emisor->ruc.'</rucComprobante>
					<tipoComprobante>'.$comprobante.'</tipoComprobante>
					<serieComprobante>'.$serie.'</serieComprobante>
					<numeroComprobante>'.$numero.'</numeroComprobante>
				</ser:getStatusCdr>
			</soapenv:Body>
		</soapenv:Envelope>';

		$headers = array(
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"SOAPAction: ",
			"Content-length: " . strlen($xml_post_string),
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$mensaje = "";
		$codigo = "";

		$doc = new DOMDocument();
		$doc->loadXML($response);
		if(isset($doc->getElementsByTagName("content")->item(0)->nodeValue)){
			$cdr = $doc->getElementsByTagName("content")->item(0)->nodeValue;
			$cdr = base64_decode($cdr);
			file_put_contents($ruta_archivo_cdr."R-".$nombrezip, $cdr);

			$zip = new ZipArchive;
			if($zip->open($ruta_archivo_cdr."R-".$nombrezip)===true){
				$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre.'.xml');
				$zip->close();
			}

			$doc_cdr = new DOMDocument();
			$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre.'.xml');
			$doc_cdr->loadXML($infocdr);
			$codigo = $doc_cdr->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
			$mensaje = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;
			unlink($ruta_archivo_cdr.'R-'.$nombre.'.xml');
		}else{
			$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
			$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
		}

		$resultado = array(
			"mensaje"	=>$mensaje,
			"codigo"	=>$codigo,
		);
		curl_close($ch);
		return $resultado;
	}

	function EnviarGuiaRemision($emisor, $nombre, $ruta_archivo_zip) // envio de guia remision
	{
		$ruta = $ruta_archivo_zip.'.xml';
		$zip = new ZipArchive();
		$nombrezip = $nombre.".zip";
		$rutazip = $ruta_archivo_zip.".zip";

		if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
			$zip->addFile($ruta, $nombre.'.xml');
			$zip->close();
		}

		//Enviamos el archivo a sunat
		$ws = "https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/".$nombre;

		$contenido_del_zip = base64_encode(file_get_contents($rutazip));
		$hash_del_zip = hash('sha256',file_get_contents($rutazip));
		$xml_envio =array(
			"archivo"=>array(
				"nomArchivo"=>$nombrezip,
				"arcGreZip"=>$contenido_del_zip,
				"hashZip"=>$hash_del_zip
			)
		);
		$xml_envio=json_encode($xml_envio);

		$header = array(
			"Content-type: application/json",
			"Authorization: Bearer ".$emisor->token_gre
		);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$response = curl_exec($ch);
		unlink($rutazip);

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$resultado['ticket'] = '';
		$resultado['frecepcion'] = '';
		if($httpcode == 200){
			$response=json_decode($response);

			$ticket = $response->numTicket;
			$resultado['ticket'] = $response->numTicket;
			$resultado['frecepcion'] = $response->fecRecepcion;
			$resultado['mensaje'] = 'Se genero el ticket '.$response->numTicket;
		}else{
			//echo curl_error($ch);
			$resultado['mensaje']="ERROR EN CONEXION";
		}
		curl_close($ch);
		return $resultado;
	}

	function ConsultarTicketGuia($emisor, $nombre, $ticket, $ruta_archivo_cdr) //estado de resumen por ticket
	{
		$ws = "https://api-cpe.sunat.gob.pe/v1/contribuyente/gem/comprobantes/envios/".$ticket;

		$header = array(
			"Authorization: Bearer ".$emisor->token_gre
		);

		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_CUSTOMREQUEST,'GET');
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		$response = curl_exec($ch);

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$estado = 0;
		$mensaje = "";
		$codigo = "";
		$qr = '';
		if($httpcode == 200){
			$response=json_decode($response);

			if ($response->codRespuesta=='98') {
				$estado = 3;
				$codigo = '98';
				$mensaje = "El procesamiento del comprobante aún no ha terminado";
			} else {
				if(isset($response->arcCdr)){
					$cdr = $response->arcCdr;
					$cdr = base64_decode($cdr);
					file_put_contents($ruta_archivo_cdr."R-".$nombre.".zip", $cdr);

					$zip = new ZipArchive;
					if($zip->open($ruta_archivo_cdr."R-".$nombre.".zip")===true){
						$zip->extractTo($ruta_archivo_cdr,'R-'.$nombre.'.xml');
						$zip->close();
					}

					$doc_cdr = new DOMDocument();
					$infocdr = file_get_contents($ruta_archivo_cdr.'R-'.$nombre.'.xml');
					$doc_cdr->loadXML($infocdr);

					$estado = 1;
					$codigo = $doc_cdr->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
					$mensaje = $doc_cdr->getElementsByTagName("Description")->item(0)->nodeValue;
					if ($response->codRespuesta==0) {
						$qr = $doc_cdr->getElementsByTagName("DocumentDescription")->item(0)->nodeValue;
					}
					unlink($ruta_archivo_cdr.'R-'.$nombre.'.xml');
				}else{
					$estado = 2;
					$codigo = $response->codRespuesta;
					$errores = $response->error;
					$mensaje = $errores->numError.' - '.$errores->desError;
				}
			}
		}else{
			$estado = 3;
			$codigo = '00';
			$mensaje = "ERROR EN CONEXION ".$response;
		}
		curl_close($ch);

		$resultado = array(
			"estado"				=>$estado,
			"mensaje"	=>$mensaje,
			"codigo"	=>$codigo,
			"qr"						=>$qr,
		);
		return $resultado;
	}


}

?>

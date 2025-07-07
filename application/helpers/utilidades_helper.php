<?php
/*===================================================================================================================
=                                                         formateo                                                  =
===================================================================================================================*/
if(!function_exists('zerofill'))
{
	function zerofill($valor, $longitud){
		$res='';
		if ($valor!='') {
	 		$res = str_pad($valor, $longitud, '0', STR_PAD_LEFT);
		}
	 return $res;
	}
}

if(!function_exists('formatoPrecio'))
{
	function formatoPrecio($monto)
	{
		if($monto==''){$monto='';}
		else{$monto=number_format($monto,2,'.','');}
		return $monto;
	}
}

if(!function_exists('formatoMonto'))
{
	function formatoMonto($monto)
	{
		if(!isset($monto)){$monto='';}
		else{$monto=number_format($monto,2,'.','');}
		return $monto;
	}
}

if(!function_exists('formatoFecha'))
{
	function formatoFecha($fecha)
	{
		if($fecha!=''){
		$fechita=date("d/m/Y", strtotime($fecha));
		}
		else{$fechita='';}
		return $fechita;
	}
}

if(!function_exists('formatoVcto'))
{
	function formatoVcto($fecha)
	{
		if($fecha!=''){
		$fechita=date("Y-m-d", strtotime($fecha));
		}
		else{$fechita='';}
		return $fechita;
	}
}

if(!function_exists('formatoHora'))
{
	function formatoHora($hora)
	{
		if($hora!=''){
		$horita=date("H:i", strtotime($hora));
		}
		else{$horita='';}
		return $horita;
	}
}

/*===================================================================================================================
=                                                       validaciones                                                =
===================================================================================================================*/
if(!function_exists('reemplazarComillas'))
{
	function reemplazarComillas($cadena) {
	    $cadenaSinComillas = str_replace('"', "''", $cadena);
	    return $cadenaSinComillas;
	}
}

if(!function_exists('obtenerNumero'))
{
	function obtenerNumero($valor)
	{
		return intval(preg_replace('/[^0-9]+/', '', $valor), 10);
	}
}

if(!function_exists('set_value_select'))
{
	function set_value_select($result=array(),$post,$campo,$valor)
	{
		if($result==NULL)
		{
			if(isset($_POST[$post]) and $_POST[$post]==$valor)
			{
				return 'selected="true"';
			}else
			{
				return '';
			}
		}else
		{
			if($campo==$valor)
			{
				return 'selected="true"';
			}else
			{
				return '';
			}
		}
	}
}

if(!function_exists('set_value_smultiple'))
{
    function set_value_smultiple($campo,$result,$limitador)
    {
        $arrGroups = explode($limitador, $result);
        if (in_array($campo,$arrGroups)) {
           return 'selected="true"';
        }else
        {
            return '';
         }
        //return var_dump($arrGroups);
    }
}

if(!function_exists('set_value_input'))
{
	function set_value_input($result=array(),$post,$campo)
	{
		if($result==NULL)
		{
			if(isset($_POST[$post]))
			{
				return $_POST[$post];
			}else
			{
				return '';
			}
		}else
		{
			if($campo)
			{
				return $campo;
			}else
			{
				return '';
			}
		}
	}
}

if(!function_exists('set_value_check'))
{
    function set_value_check($result=array(),$post,$campo,$valor)
    {
        if($result==NULL)
        {
            if(isset($_POST[$post]) and $_POST[$post]==$valor)
            {
                 return 'checked="true"';
            }else
            {
                return '';
            }
        }else
        {
            if($campo==$valor)
            {
                 return 'checked="true"';
            }else
            {
                return '';
            }
        }
    }
}

if (!function_exists('valor_check')) {
	function valor_check($input)
	{
		return $input == NULL ? 0 : $input ;
	}
}

if (!function_exists('valor_fecha')) {
	function valor_fecha($input)
	{
		return $input == NULL ? NULL : $input ;
	}
}

/*===================================================================================================================
=                                                         calculos                                                  =
===================================================================================================================*/
if(!function_exists('sumarHora'))
{
	function sumarHora($suma,$horaInicial = false)
	{
	  $hora = !empty($horaInicial) ? $horaInicial : date('H:i:s');
	  $nuevaHora = strtotime($suma, strtotime ( $hora));
	  $nuevaHora = date ('H:i:s',$nuevaHora);
	  return $nuevaHora;
	}
}

if(!function_exists('sumarFecha'))
{
	function sumarFecha($suma,$fechaInicial = false)
	{
	  $fecha = !empty($fechaInicial) ? $fechaInicial : date('Y-m-d'); 
	  $nuevaFecha = strtotime ($suma , strtotime ( $fecha ) ) ;
	  $nuevaFecha = date ( 'Y-m-d' , $nuevaFecha );
	  return $nuevaFecha;
	}
}

if(!function_exists('fechaHoraria'))
{
	function fechaHoraria($suma,$fechaInicial = false)
	{
	  $fecha = !empty($fechaInicial) ? $fechaInicial : date('Y-m-d H:i:s');
	  $nuevaFecha = strtotime ($suma , strtotime ( $fecha ) ) ;
	  $nuevaFecha = date ( 'Y-m-d H:i:s' , $nuevaFecha );
	  return $nuevaFecha;
	}
}

if(!function_exists('diferenciaFecha'))
{
	function diferenciaFecha($fechaInicial,$fechaFinal,$tipo=null)
	{
		$date1 = new DateTime($fechaInicial);
		$date2 = new DateTime($fechaFinal);

		$diff = $date1->diff($date2);

		if ($fechaInicial>=$fechaFinal) {
			return $diff->days;
		} else {
			if ($tipo=='envio') {
				return '<h5 class="badge bg-danger text-white">El plazo de envío caducó</h5>';
			} else {
				return '<h5 class="badge bg-danger text-white">El producto caducó</h5>';
			}
		}

	}
}

if(!function_exists('gananciav'))
{
	function gananciav($venta,$compra,$factor)
	{
		$resultado = $venta>0 && $compra>0 && $factor>0 ? round((($venta/$factor-$compra)*100)/$venta,2) : 0 ;
		return $resultado;
	}
}

/*===================================================================================================================
=                                                         valores                                                  =
===================================================================================================================*/
if(!function_exists('ultimo_dia_mes'))
{
	function ultimo_dia_mes($month,$year) { 
		$day = date("d", mktime(0,0,0, $month+1, 0, $year));
		return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
	};
}

if(!function_exists('primer_dia_mes'))
{
	function primer_dia_mes($month,$year) {
		return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
	}
}

if(!function_exists('tiempoCuota'))
{
	function tiempoCuota($tipo)
	{
		switch($tipo) {
		case 'Semanal':
			return '+7 day';
		break;
		case 'Quincenal':
			return '+15 day';
		break;
		case 'Mensual':
			return '+1 month';
		break;
		default:
			return '';
		}
	}
}

if(!function_exists('estadoUsuario'))
{
	function estadoUsuario($tipo)
	{
		if ($tipo==0) {
			return 'Inactivo';
		} else {
			return 'Activo';
		}
	}
}

if(!function_exists('estadoPago'))
{
	function estadoPago($tipo)
	{
		if ($tipo==1) {
			return '<span class="badge badge-success">Pagado</span>';
		} else {
			return '<span class="badge badge-secondary">Pendiente</span>';
		}
	}
}

if(!function_exists('NumerosALetras'))
{
    function NumerosALetras($monto){
        $maximo = pow(10,9);
            $unidad            = array(1=>"UNO", 2=>"DOS", 3=>"TRES", 4=>"CUATRO", 5=>"CINCO", 6=>"SEIS", 7=>"SIETE", 8=>"OCHO", 9=>"NUEVE");
            $decena            = array(10=>"DIEZ", 11=>"ONCE", 12=>"DOCE", 13=>"TRECE", 14=>"CATORCE", 15=>"QUINCE", 20=>"VEINTE", 30=>"TREINTA", 40=>"CUARENTA", 50=>"CINCUENTA", 60=>"SESENTA", 70=>"SETENTA", 80=>"OCHENTA", 90=>"NOVENTA");
            $prefijo_decena    = array(10=>"DIECI", 20=>"VEINTI", 30=>"TREINTA Y ", 40=>"CUARENTA Y ", 50=>"CINCUENTA Y ", 60=>"SESENTA Y ", 70=>"SETENTA Y ", 80=>"OCHENTA Y ", 90=>"NOVENTA Y ");
            $centena           = array(100=>"CIEN", 200=>"DOSCIENTOS", 300=>"TRESCIENTOS", 400=>"CUATROCIENTOS", 500=>"QUINIENTOS", 600=>"SEISCIENTOS", 700=>"SETECIENTOS", 800=>"OCHOCIENTOS", 900=>"NOVECIENTOS");      
            $prefijo_centena   = array(100=>"CIENTO ", 200=>"DOSCIENTOS ", 300=>"TRESCIENTOS ", 400=>"CUATROCIENTOS ", 500=>"QUINIENTOS ", 600=>"SEISCIENTOS ", 700=>"SETECIENTOS ", 800=>"OCHOCIENTOS ", 900=>"NOVECIENTOS ");
            $sufijo_mil      = "UN MIL";
            $sufijo_miles      = "MIL";
            $sufijo_millon     = "UN MILLON";
            $sufijo_millones   = "MILLONES";   
            //echo var_dump($monto); die;       
            $base         = strlen(strval($monto));
            $pren         = intval(floor($monto/pow(10,$base-1)));
            $prencentena  = intval(floor($monto/pow(10,3)));
            $prenmillar   = intval(floor($monto/pow(10,6)));
            $resto        = $monto%pow(10,$base-1);
            $restocentena = $monto%pow(10,3);
            $restomillar  = $monto%pow(10,6);       
            if (!$monto) return "";       
        if (is_int($monto) && $monto>0 && $monto < abs($maximo))   {            
                    switch ($base) {
                            case 1: return $unidad[$monto];
                            case 2: return array_key_exists($monto, $decena)  ? $decena[$monto]  : $prefijo_decena[$pren*10]   . NumerosALetras($resto);
                            case 3: return array_key_exists($monto, $centena) ? $centena[$monto] : $prefijo_centena[$pren*100] . NumerosALetras($resto);
                            case 4: case 5: case 6: return ($prencentena>1) ? NumerosALetras($prencentena). " ". $sufijo_miles . " " . NumerosALetras($restocentena) : $sufijo_mil. " " . NumerosALetras($restocentena);
                            case 7: case 8: case 9: return ($prenmillar>1)  ? NumerosALetras($prenmillar). " ". $sufijo_millones . " " . NumerosALetras($restomillar)  : $sufijo_millon. " " . NumerosALetras($restomillar);
                    }
        } else {
            echo "ERROR con el numero - $monto<br/> Debe ser un numero entero menor que " . number_format($maximo, 0, ".", ",") . ".";
        }
                   //return $texto;       
    } 
}

if(!function_exists('MontoMonetarioEnLetras'))
{
    function MontoMonetarioEnLetras($monto){ 
            $monto = str_replace(',','',$monto); //ELIMINA LA COMA 
            $pos = strpos($monto, '.');               
            if ($pos == false)      {
                    $monto_entero = $monto;
                    $monto_decimal = '00';
            }else{
                    $monto_entero = substr($monto,0,$pos);
                    $monto_decimal = substr($monto,$pos,strlen($monto)-$pos);
                    $monto_decimal = $monto_decimal * 100;
                    if ($monto_decimal==0) {
                        $monto_decimal = '00';
                    }
            } 

            $monto = (int)($monto_entero); 
            $texto_con = " CON $monto_decimal/100 SOLES";               
            return NumerosALetras($monto).$texto_con; 
    }
}

if(!function_exists('barcode'))
{
	function barcode( $filepath="", $text="0", $size="20", $orientation="horizontal", $code_type="code128", $print=false, $SizeFactor=1 ) {
		$code_string = "";
		// Translate the $text into barcode the correct $code_type
		if ( in_array(strtolower($code_type), array("code128", "code128b")) ) {
			$chksum = 104;
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","\`"=>"111422","a"=>"121124","b"=>"121421","c"=>"141122","d"=>"141221","e"=>"112214","f"=>"112412","g"=>"122114","h"=>"122411","i"=>"142112","j"=>"142211","k"=>"241211","l"=>"221114","m"=>"413111","n"=>"241112","o"=>"134111","p"=>"111242","q"=>"121142","r"=>"121241","s"=>"114212","t"=>"124112","u"=>"124211","v"=>"411212","w"=>"421112","x"=>"421211","y"=>"212141","z"=>"214121","{"=>"412121","|"=>"111143","}"=>"111341","~"=>"131141","DEL"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","FNC 4"=>"114131","CODE A"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ( $X = 1; $X <= strlen($text); $X++ ) {
				$activeKey = substr( $text, ($X-1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum=($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211214" . $code_string . "2331112";
		} elseif ( strtolower($code_type) == "code128a" ) {
			$chksum = 103;
			$text = strtoupper($text); // Code 128A doesn't support lower case
			// Must not change order of array elements as the checksum depends on the array's key to validate final code
			$code_array = array(" "=>"212222","!"=>"222122","\""=>"222221","#"=>"121223","$"=>"121322","%"=>"131222","&"=>"122213","'"=>"122312","("=>"132212",")"=>"221213","*"=>"221312","+"=>"231212",","=>"112232","-"=>"122132","."=>"122231","/"=>"113222","0"=>"123122","1"=>"123221","2"=>"223211","3"=>"221132","4"=>"221231","5"=>"213212","6"=>"223112","7"=>"312131","8"=>"311222","9"=>"321122",":"=>"321221",";"=>"312212","<"=>"322112","="=>"322211",">"=>"212123","?"=>"212321","@"=>"232121","A"=>"111323","B"=>"131123","C"=>"131321","D"=>"112313","E"=>"132113","F"=>"132311","G"=>"211313","H"=>"231113","I"=>"231311","J"=>"112133","K"=>"112331","L"=>"132131","M"=>"113123","N"=>"113321","O"=>"133121","P"=>"313121","Q"=>"211331","R"=>"231131","S"=>"213113","T"=>"213311","U"=>"213131","V"=>"311123","W"=>"311321","X"=>"331121","Y"=>"312113","Z"=>"312311","["=>"332111","\\"=>"314111","]"=>"221411","^"=>"431111","_"=>"111224","NUL"=>"111422","SOH"=>"121124","STX"=>"121421","ETX"=>"141122","EOT"=>"141221","ENQ"=>"112214","ACK"=>"112412","BEL"=>"122114","BS"=>"122411","HT"=>"142112","LF"=>"142211","VT"=>"241211","FF"=>"221114","CR"=>"413111","SO"=>"241112","SI"=>"134111","DLE"=>"111242","DC1"=>"121142","DC2"=>"121241","DC3"=>"114212","DC4"=>"124112","NAK"=>"124211","SYN"=>"411212","ETB"=>"421112","CAN"=>"421211","EM"=>"212141","SUB"=>"214121","ESC"=>"412121","FS"=>"111143","GS"=>"111341","RS"=>"131141","US"=>"114113","FNC 3"=>"114311","FNC 2"=>"411113","SHIFT"=>"411311","CODE C"=>"113141","CODE B"=>"114131","FNC 4"=>"311141","FNC 1"=>"411131","Start A"=>"211412","Start B"=>"211214","Start C"=>"211232","Stop"=>"2331112");
			$code_keys = array_keys($code_array);
			$code_values = array_flip($code_keys);
			for ( $X = 1; $X <= strlen($text); $X++ ) {
				$activeKey = substr( $text, ($X-1), 1);
				$code_string .= $code_array[$activeKey];
				$chksum=($chksum + ($code_values[$activeKey] * $X));
			}
			$code_string .= $code_array[$code_keys[($chksum - (intval($chksum / 103) * 103))]];

			$code_string = "211412" . $code_string . "2331112";
		} elseif ( strtolower($code_type) == "code39" ) {
			$code_array = array("0"=>"111221211","1"=>"211211112","2"=>"112211112","3"=>"212211111","4"=>"111221112","5"=>"211221111","6"=>"112221111","7"=>"111211212","8"=>"211211211","9"=>"112211211","A"=>"211112112","B"=>"112112112","C"=>"212112111","D"=>"111122112","E"=>"211122111","F"=>"112122111","G"=>"111112212","H"=>"211112211","I"=>"112112211","J"=>"111122211","K"=>"211111122","L"=>"112111122","M"=>"212111121","N"=>"111121122","O"=>"211121121","P"=>"112121121","Q"=>"111111222","R"=>"211111221","S"=>"112111221","T"=>"111121221","U"=>"221111112","V"=>"122111112","W"=>"222111111","X"=>"121121112","Y"=>"221121111","Z"=>"122121111","-"=>"121111212","."=>"221111211"," "=>"122111211","$"=>"121212111","/"=>"121211121","+"=>"121112121","%"=>"111212121","*"=>"121121211");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
				$code_string .= $code_array[substr( $upper_text, ($X-1), 1)] . "1";
			}

			$code_string = "1211212111" . $code_string . "121121211";
		} elseif ( strtolower($code_type) == "code25" ) {
			$code_array1 = array("1","2","3","4","5","6","7","8","9","0");
			$code_array2 = array("3-1-1-1-3","1-3-1-1-3","3-3-1-1-1","1-1-3-1-3","3-1-3-1-1","1-3-3-1-1","1-1-1-3-3","3-1-1-3-1","1-3-1-3-1","1-1-3-3-1");

			for ( $X = 1; $X <= strlen($text); $X++ ) {
				for ( $Y = 0; $Y < count($code_array1); $Y++ ) {
					if ( substr($text, ($X-1), 1) == $code_array1[$Y] )
						$temp[$X] = $code_array2[$Y];
				}
			}

			for ( $X=1; $X<=strlen($text); $X+=2 ) {
				if ( isset($temp[$X]) && isset($temp[($X + 1)]) ) {
					$temp1 = explode( "-", $temp[$X] );
					$temp2 = explode( "-", $temp[($X + 1)] );
					for ( $Y = 0; $Y < count($temp1); $Y++ )
						$code_string .= $temp1[$Y] . $temp2[$Y];
				}
			}

			$code_string = "1111" . $code_string . "311";
		} elseif ( strtolower($code_type) == "codabar" ) {
			$code_array1 = array("1","2","3","4","5","6","7","8","9","0","-","$",":","/",".","+","A","B","C","D");
			$code_array2 = array("1111221","1112112","2211111","1121121","2111121","1211112","1211211","1221111","2112111","1111122","1112211","1122111","2111212","2121112","2121211","1121212","1122121","1212112","1112122","1112221");

			// Convert to uppercase
			$upper_text = strtoupper($text);

			for ( $X = 1; $X<=strlen($upper_text); $X++ ) {
				for ( $Y = 0; $Y<count($code_array1); $Y++ ) {
					if ( substr($upper_text, ($X-1), 1) == $code_array1[$Y] )
						$code_string .= $code_array2[$Y] . "1";
				}
			}
			$code_string = "11221211" . $code_string . "1122121";
		}

		// Pad the edges of the barcode
		$code_length = 20;
		if ($print) {
			$text_height = 30;
		} else {
			$text_height = 0;
		}

		for ( $i=1; $i <= strlen($code_string); $i++ ){
			$code_length = $code_length + (integer)(substr($code_string,($i-1),1));
	        }

		if ( strtolower($orientation) == "horizontal" ) {
			$img_width = $code_length*$SizeFactor;
			$img_height = $size;
		} else {
			$img_width = $size;
			$img_height = $code_length*$SizeFactor;
		}

		$image = imagecreate($img_width, $img_height + $text_height);
		$black = imagecolorallocate ($image, 0, 0, 0);
		$white = imagecolorallocate ($image, 255, 255, 255);

		imagefill( $image, 0, 0, $white );
		if ( $print ) {
			imagestring($image, 5, 31, $img_height, $text, $black );
		}

		$location = 10;
		for ( $position = 1 ; $position <= strlen($code_string); $position++ ) {
			$cur_size = $location + ( substr($code_string, ($position-1), 1) );
			if ( strtolower($orientation) == "horizontal" )
				imagefilledrectangle( $image, $location*$SizeFactor, 0, $cur_size*$SizeFactor, $img_height, ($position % 2 == 0 ? $white : $black) );
			else
				imagefilledrectangle( $image, 0, $location*$SizeFactor, $img_width, $cur_size*$SizeFactor, ($position % 2 == 0 ? $white : $black) );
			$location = $cur_size;
		}

		// Draw barcode to the screen or save in a file
		if ( $filepath=="" ) {
			header ('Content-type: image/png');
			imagepng($image);
			imagedestroy($image);
		} else {
			imagepng($image,$filepath);
			imagedestroy($image);
		}
	}
}

if(!function_exists('obtenerDireccionMAC'))
{
	function obtenerDireccionMAC() {
	    $output = [];
	    exec('ipconfig /all', $output);

	    foreach ($output as $line) {
	        if (preg_match('/.*Dirección física.+: (.+)/', $line, $matches)) {
	            return $matches[1];
	        }
	    }

	    return null;
	}
}

if(!function_exists('generarClaveLicencia'))
{
	function generarClaveLicencia() {
	    $macAddress = obtenerDireccionMAC(); // Obtener la dirección MAC de la computadora
	    $clave = md5($macAddress); // Generar una clave basada en la dirección MAC

	    // Puedes agregar lógica adicional para personalizar la generación de la clave de licencia

	    return $clave;
	}
}

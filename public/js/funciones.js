/*===========================================================================================================================
=                                                    funciones generales                                                    =
===========================================================================================================================*/
function mostrarModal(url,div,title)
{
	$("#modalTitle").html(title);
	$('#busdatos').modal({
        backdrop: 'static',
        keyboard: false,
        show: true // Muestra el modal inmediatamente
    });

	$.post(url,function(resp){
		$("#"+div+"").html(resp);
	});
}

function consultar(url,id,div)
{
	$.post(url,{id},function(resp){
		$("#"+div+"").html(resp);
	});
}

function kardexActualizar(url,nro,div)
{
	var saldof=$("#saldof"+nro+"").val();
	var costo=$("#costo"+nro+"").val();
	var saldov=$("#saldov"+nro+"").val();
	$('#busdatos').modal('show');
	$.post(url,{saldof,costo,saldov},function(resp){
		$("#"+div+"").html(resp);
	});
}

function kardexlActualizar(url,nro,div)
{
	var saldof=$("#saldof"+nro+"").val();
	$('#busdatos').modal('show');
	$.post(url,{saldof},function(resp){
		$("#"+div+"").html(resp);
	});
}

function enviodatos(url)
{
	event.preventDefault();
	var datos= new FormData($('#formdatos')[0]);
	$.ajax({
		url: url,
		type: 'POST',
		data: datos,
		contentType: false,
		processData: false,
		success: function(responder) {
			window.location.href=responder;
		},
		error: function(error) {
			console.log(error);
		}
	});
	$('#busdatos').modal('hide');
}

function dcliente(url)
{
	var tipo= $("#tipo").val();
	var numero= $("#documento").val();
	$.post(url,{tipo,numero},function(resp){
		if (resp=='') {
			toastr.error('No se pudo conectar con la pagina de consulta');
		} else {
			var c = JSON.parse(resp);
			//console.log(c);
			if (c.error) {
				toastr.error(c.error);
			} else {
				if (tipo==1) {
					document.getElementById('nombres').value = c.nombre;
				} else {
					let purl=url.replace('cliente/busDatos','establecimiento/busProvincia');
					let durl=url.replace('cliente/busDatos','establecimiento/busDistrito');
					document.getElementById('nombres').value = c.nombre;
					document.getElementById('direccion').value = c.direccion;

					if (c.ubigeo != '-') {
						let distrito=c.ubigeo;
						let departamento=distrito.substr(0,2);
						let provincia=distrito.substr(0,4);
						document.getElementById('departamento').value = departamento;
						bubicaciones(purl,departamento,'provincia',provincia);
						bubicaciones(durl,provincia,'distrito',distrito);
					}
				}
			}
		}
	});
}

function bubicacion(url,id,mos)
{
	$('#'+mos+'').html('');
	$.post(url,{id},function(data){
		var c = JSON.parse(data);
		$('#'+mos+'').append('<option value="">::Seleccione</option>');
		$.each(c, function(i,item){
			$('#'+mos+'').append('<option value="'+item.id+'">'+item.descripcion+'</option>');
		});
	});
}

function bubicaciones(url,id,mos,dato)
{
	$('#'+mos+'').html('');
	$.post(url,{id},function(data){
		var c = JSON.parse(data);
		$('#'+mos+'').append('<option value="">::Seleccione</option>');
		$.each(c, function(i,item){
			if (item.id==dato) {
				$('#'+mos+'').append('<option value="'+item.id+'" selected="true">'+item.descripcion+'</option>');
			} else {
				$('#'+mos+'').append('<option value="'+item.id+'">'+item.descripcion+'</option>');
			}
		});
	});
}

function borrar(url,titulo)
{
	Swal.fire({
		title: titulo,
		text: "No podras revertir esto!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: "Si, realizar",
	  	cancelButtonText: "No, cancelar",
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
	}).then((result) => {
		if (result.value) {
			$.post(url,function(response){
				//console.log(response);
				var dato = JSON.parse(response);
				if (dato.success==true) {
					Swal.fire(dato.titulo, dato.mensaje, dato.color);
				    window.location=dato.url; // direccionar a una pagina especifica
				}
				else {
					Swal.fire(dato.titulo, dato.mensaje, dato.color);
				}
			});
		}
	})
}

function saltar(e,ant,pos)
{
  // Obtenemos la tecla pulsada
  (e.keyCode)?k=e.keyCode:k=e.which;

  // Si la tecla pulsada es enter (codigo ascii 38 up y 40 down)
  if(k==38)
  {
    if (ant=='input-1') {
  		document.getElementById('bproducto').focus();
  	} else {
  		document.getElementById(ant).focus();
  	}
  }

  if(k==40)
  {
    document.getElementById(pos).focus();
  }
}

function mostrarLotes(valor)
{
  if (valor.checked) {
      document.getElementById('mostrarlote').style.display='block';
      $("#clote").prop('required',true);
  }
  else {
      document.getElementById('mostrarlote').style.display='none';
      $("#clote").val('');
      $("#clote").removeAttr("required");
  }
}

function accesocheck(valor,clase)
{
	if (valor.checked) {
        $('.'+clase+'').attr("checked",true);
    }
    else {
        $('.'+clase+'').attr("checked",false);
    }
}

function conversion(valor)
{
	var medida=valor.split('|'); //BX|100|50.00
	document.getElementById('mfactor').value= medida[1];
	document.getElementById('munidades').value= 1;
	document.getElementById('mcosto').value=medida[2];
	document.getElementById('mtotal').value=medida[2];

	document.getElementById('mcantidad').value= 1*medida[1];
	document.getElementById('mmonto').value=decimales(medida[2]/medida[1],2);
	document.getElementById('munidades').select();
}

function mostrarDato(url,nro,div)
{
	$.post(url,{nro},function(resp){
		document.getElementById(div).value=resp;
	});
}

function tieneNumeroEnURL(url)
{
  // Expresión regular para verificar si hay un número en la URL
  var regex = /\d/;

  // Aplicar la expresión regular a la URL
  return regex.test(url);
}

$(document).ready(function() {
	fceliminar();
	fveliminar();
	fteliminar();
	calcularc();
	calcularv();
	calculart();
	calcularn();
});

function establecimiento(url)
{
	$.post(url,function(resp){
		if (resp==1) {
			toastr.success('Se actualizo el establecimiento');
		} else {
			toastr.error('No se pudo actualizar');
		}
	});
	location.reload();
}

function mostrarGenerico(url)
{
	var clasificacion=$('#clasificacion').val();
	var pactivo=$('#pactivo').val();
	$('#egenerico').html('');
	$.post(url,{pactivo,clasificacion},function(data){
		var c = JSON.parse(data);
		$('#egenerico').append('<option value="">::Seleccione</option>');
		$.each(c, function(i,item){
			$('#egenerico').append('<option value="'+item.id+'">'+item.descripcion+'</option>');
		});
	});
}

function mostrarGanancia(valor)
{
  if (valor.checked) {
      document.getElementById('mganancia').style.display='block';
      $("#gunidad").prop('required',true);
      $("#gblister").prop('required',true);
      $("#gcaja").prop('required',true);
  }
  else {
      document.getElementById('mganancia').style.display='none';
      $("#gunidad").val('');
      $("#gunidad").removeAttr("required");
      $("#gblister").val('');
      $("#gblister").removeAttr("required");
      $("#gcaja").val('');
      $("#gcaja").removeAttr("required");
  }
}

function  calcularPrecios()
{
	factor=document.getElementById('mfactor').value;
	if (factor>1) {
		precios('mcosto','mutilidadc','mventa','mfactoru');

		if ($('#mutilidadu').val()>0) {
			//hallar precio unidad
			var costo=parseFloat($('#mmonto').val());
			var porcentaje=$('#mutilidadu').val()/100;
			document.getElementById('mpventa').value=decimales((costo/(1-porcentaje))*$('#mfactoru').val(),2);
		}

		if ($('#mutilidadb').val()>0) {
			//hallar precio blister
			var costob=parseFloat($('#mmonto').val());
			var porcentajeb=$('#mutilidadb').val()/100;
			document.getElementById('mblister').value=decimales((costob/(1-porcentajeb))*$('#mfactorb').val(),2);
		}
	} else {
		precios('mcosto','mutilidadu','mpventa','mfactoru');

		if ($('#mutilidadb').val()>0) {
			precios('mcosto','mutilidadb','mblister','mfactorb');
		}

		if ($('#mutilidadc').val()>0) {
			//hallar precio unidad
			var costo=parseFloat($('#mcosto').val()*document.getElementById('mfactorc').value);
			var porcentaje=$('#mutilidadc').val()/100;
			document.getElementById('mventa').value=decimales((costo/(1-porcentaje))*$('#mfactoru').val(),2);
		}
	}
}

/*===========================================================================================================================
=                                                     operaciones basicas                                                   =
===========================================================================================================================*/
function decimales(num, decimals)
{
	return parseFloat(num).toFixed(decimals);
}

function suma(total,sumando,resultado)
{
	var unidad=parseFloat($('#'+total+'').val());
	var unidac=parseFloat($('#'+sumando+'').val());
	var monto=unidad+unidac;
	if (!isNaN(monto)) {
		document.getElementById(resultado).value=decimales(monto,2);
	}else{
		document.getElementById(resultado).value='';
	}
}

function diferencia(total,minuendo,resultado)
{
	var unidad=parseFloat($('#'+total+'').val());
	var unidac=parseFloat($('#'+minuendo+'').val());
	var monto=unidad-unidac;
	if (!isNaN(monto)) {
		document.getElementById(resultado).value=decimales(monto,2);
	}else{
		document.getElementById(resultado).value='';
	}
}

function factores(cantidad,factor,resultado)
{
	var unidad=parseFloat($('#'+cantidad+'').val());
	var unidac=parseFloat($('#'+factor+'').val());
	var monto=unidad*unidac;
	if (!isNaN(monto)) {
		document.getElementById(resultado).value=decimales(monto,2);
	}else{
		document.getElementById(resultado).value='';
	}
}

function divisores(costo,factor,resultado)
{
	var unidad=parseFloat($('#'+costo+'').val());
	var unidac=parseFloat($('#'+factor+'').val());
	var monto=unidad/unidac;
	if (!isNaN(monto)) {
		document.getElementById(resultado).value=decimales(monto,2);
	}else{
		document.getElementById(resultado).value='';
	}
}

function porcentajes(cantidad,factor,resultado)
{
	var unidad=parseFloat($('#'+cantidad+'').val());
	var unidac=parseFloat($('#'+factor+'').val()/100);
	var monto=unidad*unidac;
	if (!isNaN(monto)) {
		document.getElementById(resultado).value=decimales(monto,2);
	}else{
		document.getElementById(resultado).value='';
	}
}

function margen(costo,precio,factor,gananacia)
{
	var costo=parseFloat($('#'+costo+'').val());
	var venta=parseFloat($('#'+precio+'').val()/$('#'+factor+'').val());
	var resultado=decimales(((venta-costo)*100)/venta,2);

	if (!isNaN(resultado)) {
		document.getElementById(gananacia).value=resultado;
	}else{
		document.getElementById(gananacia).value='';
	}
}

function precios(costo,gananacia,precio,factor)
{
	var costo=parseFloat($('#'+costo+'').val());
	var porcentaje=$('#'+gananacia+'').val()/100;
	var resultado=decimales((costo/(1-porcentaje))*$('#'+factor+'').val(),2);

	if (!isNaN(resultado)) {
		document.getElementById(precio).value=resultado;
	}else{
		document.getElementById(precio).value='';
	}
}

function validarGanancia(control)
{
    var numer = parseFloat(control.value);  // Intenta convertir el valor del control a un número
    if (isNaN(numer) || numer > 99) {  // Verifica si el número es no válido (NaN) o mayor que 20
        control.value = '';  // Limpia el valor si no es válido o es mayor que 20
    } else {
        control.value = numer;  // Asigna el número convertido si es válido y menor o igual a 20
    }
    control.focus();  // Mantiene el foco en el elemento control, independientemente del resultado
}

function unidades(factor)
{
	if (factor>1) {
		document.getElementById('precios').style.display = 'block';
		document.getElementById('factorc').value=factor;

		$('#venta').val('');
		$('#utilidadc').val('');

		$('#pblister').val('');
		$('#factorb').val('');
		$('#utilidadb').val('');
	} else {
		document.getElementById('precios').style.display = 'none';
	}
}

function calcularCompra()
{
	var incluye=$('#incluye').prop("checked");
	var igravada=0;
	var exonerada=0;
	var inafecta=0;
	var gratuita=0;
	var contador = document.getElementsByName("importe[]").length;
	for(i=0;i<contador;i++){
		let afectacion=document.getElementsByName('tafectacion[]')[i].value;
		if (afectacion==30) {
			inafecta += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==20) {
			exonerada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==10) {
			igravada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else {
			gratuita += parseFloat(document.getElementsByName('importe[]')[i].value);
		}
	}

	if (incluye) {
		var gravada=igravada/1.18;
		document.getElementById('gravado').value=decimales(gravada,2);
		document.getElementById('exonerado').value=decimales(exonerada,2);
		document.getElementById('inafecto').value=decimales(inafecta,2);
		document.getElementById('gratuito').value=decimales(gratuita,2);
		var subtotal=inafecta+exonerada+gravada;
		document.getElementById('subtotal').value=decimales(subtotal,2);
		var igv=gravada*0.18;
		document.getElementById('igv').value=decimales(igv,2);
		var total=inafecta+exonerada+gravada+igv;
		document.getElementById('total').value=decimales(total,2);
	} else {
		document.getElementById('gravado').value=decimales(igravada,2);
		document.getElementById('exonerado').value=decimales(exonerada,2);
		document.getElementById('inafecto').value=decimales(inafecta,2);
		document.getElementById('gratuito').value=decimales(gratuita,2);
		var subtotal=inafecta+exonerada+igravada;
		document.getElementById('subtotal').value=decimales(subtotal,2);
		var igv=igravada*0.18;
		document.getElementById('igv').value=decimales(igv,2);
		var total=inafecta+exonerada+igravada+igv;
		document.getElementById('total').value=decimales(total,2);
	}
}

function calcularCotizacion()
{
	var totales=0;
	var contador = document.getElementsByName("importe[]").length;
	for(i=0;i<contador;i++){
		totales += parseFloat(document.getElementsByName('importe[]')[i].value);
	}

	document.getElementById('totalg').value=decimales(totales,2);
}

function calcularVenta()
{
	var igravada=0;
	var exonerada=0;
	var inafecta=0;
	var gratuita=0;
	var contador = document.getElementsByName("importe[]").length;
	for(i=0;i<contador;i++){
		let afectacion=document.getElementsByName('tafectacion[]')[i].value;
		if (afectacion==30) {
			inafecta += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==20) {
			exonerada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==10) {
			igravada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else {
			gratuita += parseFloat(document.getElementsByName('importe[]')[i].value);
		}
	}

	var gravada=igravada/1.18;
	document.getElementById('bimponible').value=decimales(gravada,2);
	document.getElementById('gravado').value=decimales(gravada,2);
	document.getElementById('exonerado').value=decimales(exonerada,2);
	document.getElementById('inafecto').value=decimales(inafecta,2);
	document.getElementById('gratuito').value=decimales(gratuita,2);
	var igv=gravada*0.18;
	document.getElementById('igv').value=decimales(igv,2);
	var total=inafecta+exonerada+gravada+igv;
	document.getElementById('totalg').value=decimales(total,2);

	document.getElementById('monto1').value=decimales(total,2);
}

function calcularDescuento(dscto)
{
	var totales=$('#totalg').val()-dscto;
	var subtotal=totales/1.18;
	document.getElementById('gravado').value=decimales(subtotal,2);
	var igv=subtotal*0.18;
	document.getElementById('igv').value=decimales(igv,2);
	document.getElementById('totalg').value=decimales(totales,2);
	document.getElementById('monto0').value=decimales(totales,2);
}

function calcularNota()
{
	var igravada=0;
	var exonerada=0;
	var inafecta=0;
	var gratuita=0;
	var contador = document.getElementsByName("importe[]").length;
	for(i=0;i<contador;i++){
		let afectacion=document.getElementsByName('tafectacion[]')[i].value;
		if (afectacion==30) {
			inafecta += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==20) {
			exonerada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else if(afectacion==10) {
			igravada += parseFloat(document.getElementsByName('importe[]')[i].value);
		} else {
			gratuita += parseFloat(document.getElementsByName('importe[]')[i].value);
		}
	}

	var gravada=igravada/1.18;
	document.getElementById('bimponible').value=decimales(gravada,2);
	document.getElementById('gravado').value=decimales(gravada,2);
	document.getElementById('exonerado').value=decimales(exonerada,2);
	document.getElementById('inafecto').value=decimales(inafecta,2);
	document.getElementById('gratuito').value=decimales(gratuita,2);
	var igv=gravada*0.18;
	document.getElementById('igv').value=decimales(igv,2);
	var total=inafecta+exonerada+gravada+igv;
	document.getElementById('totalg').value=decimales(total,2);
}

function inventario(valor,control)
{
  if (control==1 && valor>0) {
      $("#lote").prop('required',true);
  }
  else {
      $("#lote").removeAttr("required");
  }
}

function feliminar()
{
    $("a.elimina").click(function(){
    	$(this).parents("tr").fadeOut("normal", function(){
        	$(this).remove();
        	toastr.error('El item fue eliminado');
        })
   });
};

/*=========================================================================================================================
=                                                    funciones catalogo                                                    =
=========================================================================================================================*/
function activos(url,id)
{
	$('#grcatalogo').html('');
	const data = new FormData();
	data.append('id', id);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#grcatalogo').html('<tr><td colspan="10"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var cadena=''; nro=1
			var badge,badgee,estado;
			$.each(datos, function(i,item){
				let aurl=url.replace('busActivos','inactivar');
			  cadena += '<tr id="items'+nro+'">';
				cadena +='<td>'+item.descripcion+'</td>';
				cadena +='<td><a href="javascript:void(0)" class="btn btn-sm btn-warning py-0" onclick="asignarInactivo(\''+aurl+'\',\''+item.id+'\',\'items'+nro+'\')" ><i class="fa fa-arrow-right"></i></a></td>';
			  cadena +='</tr>';
				$('#grcatalogo').html(cadena);
			  nro++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

function asignarInactivo(url,id,div)
{
	$.post(url,{id},function(data){
		var dato = JSON.parse(data);
		if (dato.success) {
			$("#"+div+"").remove();
			toastr.warning(dato.mensaje,{"positionClass" : "toast-top-center"});

			purl=url.replace('inactivar','busInactivos');
			$('#bproducta').val('');
			inactivos(purl,'a');
			$('#contador1').html(dato.contador1);
			$('#contador2').html(dato.contador2);
			$('#contador3').html(dato.contador3);
		} else {
			toastr.error(dato.mensaje,{"positionClass" : "toast-top-center"});
		}
	});
}

function inactivos(url,id)
{
	$('#grsistema').html('');
	const data = new FormData();
	data.append('id', id);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#grsistema').html('<tr><td colspan="10"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var cadena=''; nro=1
			var badge,badgee,estado;
			$.each(datos, function(i,item){
				let aurl=url.replace('busInactivos','activar');
				let rurl=url.replace('busInactivos','retirar');
			  cadena += '<tr id="itemi'+nro+'">';
				cadena +='<td><a href="javascript:void(0)" class="btn btn-sm btn-success py-0" onclick="asignarActivo(\''+aurl+'\',\''+item.id+'\',\'itemi'+nro+'\')" ><i class="fa fa-arrow-left"></i></a></td>';
				cadena +='<td>'+item.descripcion+'</td>';
				cadena +='<td><a href="javascript:void(0)" class="btn btn-sm btn-danger py-0" onclick="asignarRetiro(\''+rurl+'\',\''+item.id+'\',\'itemi'+nro+'\')" ><i class="fa fa-arrow-right"></i></a></td>';
			  cadena +='</tr>';
				$('#grsistema').html(cadena);
			  nro++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

function asignarActivo(url,id,div)
{
	$.post(url,{id},function(data){
		var dato = JSON.parse(data);
		if (dato.success) {
			$("#"+div+"").remove();
			toastr.success(dato.mensaje,{"positionClass" : "toast-top-center"});

			purl=url.replace('activar','busActivos');
			$('#bproducto').val('');
			activos(purl,'a');
			$('#contador1').html(dato.contador1);
			$('#contador2').html(dato.contador2);
			$('#contador3').html(dato.contador3);
		} else {

		}
	});
}

function asignarRetiro(url,id,div)
{
	$.post(url,{id},function(data){
		var dato = JSON.parse(data);
		if (dato.success) {
			$("#"+div+"").remove();
			toastr.error(dato.mensaje,{"positionClass" : "toast-top-center"});

			purl=url.replace('retirar','busRetirados');
			$('#bproductr').val('');
			retirados(purl,'a');
			$('#contador1').html(dato.contador1);
			$('#contador2').html(dato.contador2);
			$('#contador3').html(dato.contador3);
		} else {

		}
	});
}

function retirados(url,id)
{
	$('#grprueba').html('');
	const data = new FormData();
	data.append('id', id);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#grprueba').html('<tr><td colspan="10"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var cadena=''; nro=1
			var badge,badgee,estado;
			$.each(datos, function(i,item){
				let aurl=url.replace('busRetirados','activar');
			  cadena += '<tr id="itemr'+nro+'">';
				cadena +='<td><a href="javascript:void(0)" class="btn btn-sm btn-success py-0" onclick="asignarActivo(\''+aurl+'\',\''+item.id+'\',\'itemr'+nro+'\')" ><i class="fa fa-arrow-left"></i></a></td>';
				cadena +='<td>'+item.descripcion+'</td>';
			  cadena +='</tr>';
				$('#grprueba').html(cadena);
			  nro++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

/*=========================================================================================================================
=                                                    funciones listado                                                    =
=========================================================================================================================*/
function consultaCodigo(event,valor,div,url)
{
    if(event.which === 13){
    	event.preventDefault();
    	if (valor!='') {
      		consultar(url,valor,div);
    	}
    }
}

function generarCodigo(url)
{
  $.post(url,function(codigo){
		document.getElementById('codbarra').value=codigo;
	});
}

function productoListado(url,id,estado)
{
	$('#tblproducto').html('');
	const data = new FormData();
	data.append('id', id);
	data.append('estado', estado);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#tblproducto').html('<tr><td colspan="10"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var j=1;
			var cadena='';
			var badge,badgee,estado;
			$.each(datos, function(i,item){
				if (parseFloat(item.stock)<=parseFloat(item.mstock)) {badge='danger';} else {badge='success';}
				let surl=url.replace('busListado','establecimiento');
				let eurl=url.replace('busListado','productoi');
				let burl=url.replace('busListado','consulta');
				let iurl=url.replace('busListado','inventario');
				let curl=url.replace('busListado','buscompras');
				let lurl=url.replace('busListado','lotes');
				let ourl=url.replace('busListado','deshabilitar');
				let aurl=url.replace('busListado','habilitar');
			    cadena += '<tr>';
				cadena +='<td>'+j+'</td>';
				cadena +='<td>'+item.descripcion+'</td>';
				cadena +='<td>'+item.nlaboratorio+'</td>';
				cadena +='<td>'+item.factor+'</td>';
				cadena +='<td>'+item.rsanitario+'</td>';
				cadena +='<td align="right">'+decimales(item.pcompra,2)+'</td>';
				cadena +='<td align="center">';
				if (item.canexos>1) {
				cadena +='<button type="button" class="btn btn-'+badge+' btn-sm py-0" onclick="mostrarModal(\''+surl+'/'+item.id+'\',\'bdatos\',\'Stock Establecimiento\')">'+item.stock+'</button>';
				} else {
				cadena +='<h5 class="my-0"><span class="badge badge-'+badge+'">'+item.stock+'</span></h5>';
				}
				cadena +='</td>';
				cadena +='<td align="right">'+decimales(item.pventa,2)+'</td>';
				cadena +='<td><div class="btn-group">';
				cadena +='<a href="'+eurl+'/'+item.id+'" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>';
				if (item.estado==1) {
					if (item.tipo=='B') {
					cadena +='<button type="button" class="btn bg-teal btn-sm py-0" onclick="mostrarModal(\''+burl+'/'+item.id+'\',\'bdatos\',\'Impresion Codigo Barra\')" data-toggle="tooltip" data-placement="bottom" title="Codigo Barra"><i class="fa fa-barcode"></i> </button>';

					cadena +='<button type="button" class="btn bg-secondary btn-sm py-0" onclick="mostrarModal(\''+iurl+'/'+item.id+'\',\'bdatos\',\'Actualizar Inventario\')" title="Actualizar Inventario" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-server"></i> </button>';
					}
					if (item.lote==1) {
					cadena +='<button type="button" class="btn bg-purple btn-sm py-0" onclick="mostrarModal(\''+lurl+'/'+item.id+'\',\'bdatos\',\'Datos Lotes\')" title="Datos Lotes" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-cubes"></i> </button>';
					}
					cadena +='<button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal(\''+curl+'/'+item.id+'\',\'bdatos\',\'Datos Ultimos Movimientos\')"><i class="fa fa-sync-alt"></i> </button>';
					cadena +='<a href="'+ourl+'/'+item.id+'" class="btn btn-outline-danger btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Inactivar"><i class="fa fa-thumbs-down"></i></a>';
				} else {
					cadena +='<a href="'+aurl+'/'+item.id+'" class="btn btn-outline-success btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Activar"><i class="fa fa-thumbs-up"></i></a>';
				}
				cadena +='</div></td>';
			  cadena +='</tr>';
				$('#tblproducto').html(cadena);
				j++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

function servicioListado(url,id,estado)
{
	$('#tblservicio').html('');
	const data = new FormData();
	data.append('id', id);
	data.append('estado', estado);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#tblservicio').html('<tr><td colspan="10"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var j=1;
			var cadena='';
			var badge,badgee,estado;
			$.each(datos, function(i,item){
				let eurl=url.replace('busListado','servicioi');
				let ourl=url.replace('busListado','deshabilitar');
				let aurl=url.replace('busListado','habilitar');
			    cadena += '<tr>';
				cadena +='<td>'+j+'</td>';
				cadena +='<td>'+item.descripcion+'</td>';
				cadena +='<td align="right">'+decimales(item.pventa,2)+'</td>';
				cadena +='<td><div class="btn-group">';
				if (item.estado==1) {
					cadena +='<a href="'+eurl+'/'+item.id+'" class="btn btn-warning btn-sm py-0" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></a>';
					cadena +='<button type="button" class="btn btn-info btn-sm py-0" onclick="mostrarModal(\''+eurl+'/'+item.id+'\',\'bdatos\',\'Datos Ultimos Ventas\')"><i class="fa fa-sync-alt"></i> </button>';
					cadena +='<a href="'+ourl+'/'+item.id+'" class="btn btn-outline-danger btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Inactivar"><i class="fa fa-thumbs-down"></i></a>';
				} else {
					cadena +='<a href="'+aurl+'/'+item.id+'" class="btn btn-outline-success btn-sm py-0" data-toggle="tooltip" data-placement="bottom" title="Activar"><i class="fa fa-thumbs-up"></i></a>';
				}
				cadena +='</div></td>';
			  cadena +='</tr>';
				$('#tblservicio').html(cadena);
				j++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

function envioFormulario(url)
{
	event.preventDefault();

	Swal.fire({
		title: "Desea guardar la informacion?",
		text: "No podras revertir esto!",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: "Si, guardar esto!",
		cancelButtonText: "No, cancelar!",
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
	}).then((result) => {
		if (result.value) {
			document.getElementById("btsubmit").value = "Enviando...";
			document.getElementById("btsubmit").disabled = true;

			$.ajax({
				url: url,
				type: 'POST',
				async:true,
				data: $('#form1').serialize(),
				success: function(responder) {
					console.log(responder);
					var dato = JSON.parse(responder);
					if (dato.url!='') {
						window.location.href=dato.url;
						toastr.success(dato.mensaje);
					} else {
						toastr.error(dato.mensaje);
						setTimeout(function () {
							document.getElementById("btsubmit").value = "Guardar";
							document.getElementById("btsubmit").disabled = false;
					    }, 800);
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
	})
}

$('#busconsulta').on('shown.bs.modal', function () {
  $('#bconsulta').focus();
})

$('#busconsulta').on('hidden.bs.modal', function (e){
	$('#bconsulta').val('');
	$('#tblconsulta').html('');
})

function busConsulta(url,id)
{
	const data = new FormData();
	data.append('id', id);

	if (id.length > 2) {
		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				document.getElementById("tblconsulta").innerHTML = '<strong>No hay datos de la busqueda</strong>';
			}
			else
			{
				var cadena='';
	      $.each(datos, function(i,item){
		      cadena += '<tr>';
					cadena +='<td>'+item.codigo+'</td>';
					cadena +='<td>'+item.nombre+'</td>';
					cadena +='<td>'+item.concentracion+'</td>';
					cadena +='<td>'+item.forma+'</td>';
					cadena +='<td>'+item.fabricante+'</td>';
					cadena +='<td>'+item.fraccion+'</td>';
					cadena +='<td>'+item.rsanitario+'</td>';
					cadena +='<td align="right">'+item.media+'</td>';
					cadena +='<td align="right">'+item.mediana+'</td>';
					cadena +='<td align="right">'+item.moda+'</td>';
				  cadena +='</tr>';
	        $('#tblconsulta').html(cadena);
	      });
			}
		})
		.catch(function(err) {
		   console.log(err);
		});
	}
}

/*=========================================================================================================================
=                                                 funciones bonificaciones                                                =
=========================================================================================================================*/
function consultarb(url)
{
	var a=$('#canuo').val();
	var m=$('#cmes').val();
	$('#grilla').html('');
	$.post(url,{a,m},function(data){
		if (data==0) {
			document.getElementById('tblbonificacion').style.display = 'block';
		} else {
			toastr.error('Ya se ingreso productos bonificados para ese año y mes');
			document.getElementById('tblbonificacion').style.display = 'none';
		}
	});
}

function mostrarb(url)
{
	var a=$('#canuo').val();
	var m=$('#cmes').val();
	$.post(url,{a,m},function(data){
		if (data>0) {
			toastr.error('Ya se ingreso productos bonificados para ese año y mes', '',{"positionClass" : "toast-top-center"});
			$('#cmes').val('');
		}
	});
}

function productoNombreb(url,id)
{
	$('#tblproducto').html('');
	const data = new FormData();
	data.append('id', id);

	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#tblproducto').html('<strong>No hay datos de la busqueda</strong>');
		}
		else
		{
			var cadena='';
			$.each(datos, function(i,item){
				nproducto=item.descripcion;
				if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				cadena += '<tr>';
				cadena +='<td>'+item.id+'</td>';
				cadena +='<td>'+nproducto+'</td>';
				cadena +='<td align="center">'+item.bonificacion+'</td>';
				cadena +='<td align="center">'+item.stock+'</td>';
				cadena +='<td align="right"><a href="javascript:void(0)" class="btn btn-info btn-sm py-0" onclick="appbonificacion(\''+item.id+'\', \`'+nproducto+'\`);">'+item.pventa+'</a></td>';
				cadena +='</tr>';
				$('#tblproducto').html(cadena);
			});
		}
  })
	.catch(function(err) {
	   console.log(err);
	});
}

function appbonificacion(id,nombres)
{
	var nmes=$('#cmes').val();
	if (nmes!='') {
		cadena = '<tr>';
		cadena += '<td><input type="hidden" name="idproducto[]" value="'+id+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
		cadena += '<td><input type="number" min="0.01" step="0.01" name="monto[]" value="" class="form-control form-control-sm"/></td>';
		cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" data-toggle="tooltip" data-placement="bottom" title="Eliminar" data-toggle="tooltip" data-placement="bottom" data-original-title="Eliminar"><i class="fa fa-trash"></i></a></td>';
		cadena += "</tr>";
	  var tr = $(cadena).prependTo("#grilla");

	  // Ajustar la altura del textarea recién agregado
	  var textarea = tr.find('textarea[name="descripcion[]"]');
  	textarea.height(textarea[0].scrollHeight - 4);
		feliminar();
	} else {
		toastr.error('No selecciono año y mes para la bonificacion');
	}
};

function borrarb(id)
{
	$('#'+id+'').remove();
}

/*=========================================================================================================================
=                                                    funciones ingreso                                                    =
=========================================================================================================================*/
$('#busingreso').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#busingreso').on('hidden.bs.modal', function (e){
	reset_ingreso();
})

function productoNombrei(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

		        	var nproducto=item.descripcion;
		        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				    cadena +='<a href="javascript:void(0)" onclick="mingreso(\''+item.id+'\', \`'+nproducto+'\`,\''+item.compra+'\',\''+item.factor+'\',\''+item.lote+'\',\''+item.umedidac+'\',\''+item.umedidav+'\',\''+url+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt></a>';
				    cadena +='<hr class="m-0">';
		        	$('#grdescripcion').html(cadena);
				});
			}
	    })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function mingreso(cod,nom,pre,fac,lote,mec,mev,url)
{
	reset_ingreso();
	$("#munidades").removeClass("is-invalid");
	$("#mcosto").removeClass("is-invalid");
	$("#mlote").removeClass("is-invalid");
	$("#mfecha").removeClass("is-invalid");

	document.getElementById('mcodigo').value = cod;
	document.getElementById('mdescripcion').value = nom;
	document.getElementById('mfactor').value = fac;
	document.getElementById('mactivar').value = lote;

	document.getElementById('munidades').value = 1;
	document.getElementById('mcosto').value = pre;
	document.getElementById('mtotal').value = pre;
	document.getElementById('mcantidad').value = fac*1;
	document.getElementById('mmonto').value = pre/fac;
	if (lote==1) {
		document.getElementById('mdetalle').style.display = 'block';
	}else{
		document.getElementById('mdetalle').style.display = 'none';
	}

	$('#mmedida').html('');
	if (fac>1) {
		$("#mmedida").append('<option value="'+mec+'|'+fac+'|'+pre+'">Precio Caja</option>');
		$("#mmedida").append('<option value="'+mev+'|1|'+pre/fac+'">Precio Unidad</option>');
	}else{
		$("#mmedida").append('<option value="'+mev+'|1|'+pre+'">Precio Unidad</option>');
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('munidades').select();
}

function appingreso()
{
	var codigo=$('#mcodigo').val();
	var nombres=$('#mdescripcion').val();
	var cantidad=$('#munidades').val();
	var precio=$('#mcosto').val();
	var total=$('#mtotal').val();
	var nlote=$('#mlote').val();
	var fechal=$('#mfecha').val();
	var restringir=$('#mactivar').val();
	var factor=$('#mfactor').val();
	var medida=$('#mmedida').val().split('|');

	var almacenc=$('#mcantidad').val();
	var almacenp=$('#mmonto').val();
	if (restringir==1) {
		if(codigo!='' && nombres!='' && cantidad!='' && precio!='' && nlote!=''){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="fvencimiento[]" value="'+fechal+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="almacenc[]" value="'+almacenc+'"/><input type="hidden" name="almacenp[]" value="'+almacenp+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
			reset_ingreso();
			feliminar();
    		$('#busingreso').modal('hide');
		}else{
			$('#mensajeerror').html('<strong class="text-danger">Falta ingresar datos del producto</strong>');
			if (cantidad=='') {document.getElementById("munidades").className += " is-invalid";}
			if (precio=='') {document.getElementById("mcosto").className += " is-invalid";}
			if (nlote=='') {document.getElementById("mlote").className += " is-invalid";}
		}
	}else{
		if(codigo!='' && nombres!='' && cantidad!='' && precio!=''){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="fvencimiento[]" value="'+fechal+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="almacenc[]" value="'+almacenc+'"/><input type="hidden" name="almacenp[]" value="'+almacenp+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
			reset_ingreso();
			feliminar();
    		$('#busingreso').modal('hide');
		}
	}
};

function reset_ingreso()
{
	$('#codbarra').val(''); // formulario principal

	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#mfactor').val(1);
	$('#munidades').val('');
	$('#mcosto').val('');
	$('#mtotal').val('');
	$('#mcantidad').val('');
	$('#mmonto').val('');

	$('#mlote').val('');
	$('#mfecha').val('');
	$("#mdescripcion").removeAttr("readonly");
	document.getElementById('mdetalle').style.display = 'none';
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

function productoBarrai(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo de barra no existe');
				} else {
      		//console.log(responder);
					var item=JSON.parse(responder);
					$('#busingreso').modal('show');
					$("#mdescripcion").prop('readonly',true);

					var nproducto=item.descripcion;
					if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
					document.getElementById('mcodigo').value=item.id;
					document.getElementById('mdescripcion').value=nproducto;
					document.getElementById('mfactor').value = item.factor;
					document.getElementById('mactivar').value = item.lote;

					document.getElementById('munidades').value = 1;
					document.getElementById('mcosto').value = item.compra;
					document.getElementById('mtotal').value = item.compra;
					document.getElementById('mcantidad').value = 1*item.factor;
					document.getElementById('mmonto').value = item.compra/item.factor;
					if (item.lote==1) {
						document.getElementById('mdetalle').style.display = 'block';
					}else{
						document.getElementById('mdetalle').style.display = 'none';
					}

					$('#mmedida').html('');
					if (item.factor>1) {
						$("#mmedida").append('<option value="'+item.umedidac+'|'+item.factor+'|'+item.compra+'">Precio Caja</option>');
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.compra/item.factor+'">Precio Unidad</option>');
					}else{
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.compra+'">Precio Unidad</option>');
					}

					document.getElementById('munidades').select();
				}
			});
		}
		$('#codbarra').val('');
		$('#codbarra').focus();
	}
}

/*=========================================================================================================================
=                                                    funciones salida                                                    =
=========================================================================================================================*/
$('#bussalida').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#bussalida').on('hidden.bs.modal', function (e){
	reset_salida();
})

function productoNombres(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

		        	var nproducto=item.descripcion;
		        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				    cadena +='<a href="javascript:void(0)" onclick="msalida(\''+item.id+'\', \`'+nproducto+'\`,\''+item.venta+'\',\''+item.factor+'\',\''+item.pventa+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.umedidac+'\',\''+item.umedidav+'\',\''+url+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt><dd class="mx-2">Precio Unidad : '+decimales(item.pventa,2)+' -- Precio Caja : '+decimales(item.venta,2)+' -- Stock : '+item.stock+'</dd></a>';
				    cadena +='<hr class="m-0">';
		        	$('#grdescripcion').html(cadena);
				});
			}
	    })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function msalida(id,nom,pre,fac,und,lote,cant,mec,mev,url)
{
	reset_salida();
	$("#munidades").removeClass("is-invalid");
	$("#mcosto").removeClass("is-invalid");
	$("#mlote").removeClass("is-invalid");

	document.getElementById('mcodigo').value = id;
	document.getElementById('mdescripcion').value = nom;
	document.getElementById('mstock').value = cant;
	document.getElementById('mfactor').value = 1;
	document.getElementById('mactivar').value = lote;

	document.getElementById('mcosto').value = und;
	document.getElementById('munidades').value = 1;
	document.getElementById('mtotal').value = und;
	document.getElementById('mcantidad').value = 1;
	document.getElementById('munidades').select();
	if (lote==1) {
		$('#tbLotes').html('');
		document.getElementById('mdetalle').style.display = 'block';

		var lurl=url.replace('busProductos','busLotes');
		var cadena='';
		$.post(lurl,{id},function(data){
			var c = JSON.parse(data);
			$.each(c, function(i,item){
				var valores=item.nlote+'|'+item.stock;
				cadena += '<tr>';
				cadena += '<td><div class="form-check"><label class="form-check-label"><input class="form-check-input nlote" type="checkbox" value="'+valores+'" onclick="marcados(this)">'+item.nlote+'</label><div></td>';
				cadena += '<td>'+item.stock+'</td>';
				cadena += '<td>'+item.fvencimiento+'</td>';
				cadena += "</tr>";
				$('#tbLotes').html(cadena);
			});
		});
	}else{
		document.getElementById('mdetalle').style.display = 'none';
	}

	$('#mmedida').html('');
	if (fac>1) {
		$("#mmedida").append('<option value="'+mev+'|1|'+und+'">Precio Unidad</option>');
		$("#mmedida").append('<option value="'+mec+'|'+fac+'|'+pre+'">Precio Caja</option>');
	}else{
		$("#mmedida").append('<option value="'+mev+'|1|'+und+'">Precio Unidad</option>');
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('munidades').select();
}

function appsalida()
{
	var codigo=$('#mcodigo').val();
	var nombres=$('#mdescripcion').val();
	var cantidad=$('#munidades').val();
	var precio=$('#mcosto').val();
	var total=$('#mtotal').val();
	var nlote=$('#clote').val();
	var cstock=parseFloat($('#mstock').val());
	var restringir=$('#mactivar').val();
	var factor=$('#mfactor').val();
	var medida=$('#mmedida').val().split('|');

	var almacenc=$('#mcantidad').val();
	var centregar=parseFloat($('#centregar').val());
	if (restringir==1) {
		if(codigo!='' && nombres!='' && cantidad!='' && precio!='' && nlote!='' && almacenc<=cstock && centregar>=almacenc){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="almacenc[]" value="'+almacenc+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
			reset_salida();
			feliminar();
    		$('#bussalida').modal('hide');
		}else{
			$('#mensajeerror').html('<strong class="text-danger">Falta ingresar datos del producto</strong>');
			if (cantidad=='') {document.getElementById("munidades").className += " is-invalid";}
			if (precio=='') {document.getElementById("mcosto").className += " is-invalid";}
			if (nlote=='') {$('#mensajeerror').append('<strong class="text-danger"><br>Seleccione un lote</strong>');}
			if (almacenc>cstock) {$('#mensajeerror').append('<strong class="text-danger"><br>EL stock actual en mucho menor al que desea vender</strong>');}
			if (centregar<almacenc) {$('#mensajeerror').append('<strong class="text-danger"><br>La cantidad de lote seleccionada es mucho menor</strong>');}
		}
	}else{
		if(codigo!='' && nombres!='' && cantidad!='' && precio!='' && almacenc<=cstock){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="almacenc[]" value="'+almacenc+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo" onkeydown="return false"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
			reset_salida();
			feliminar();
    		$('#bussalida').modal('hide');
		}else{
			if (almacenc>cstock) {$('#mensajeerror').html('<strong class="text-danger">EL stock actual en mucho menor al que desea vender</strong>');}
		}
	}
};

function reset_salida()
{
	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#mfactor').val(1);
	$('#mstock').val('');
	$('#munidades').val('');
	$('#mcosto').val('');
	$('#mtotal').val('');
	$('#mcantidad').val('');

	$('#mdescuento').val('');
	$('#centregar').val(0);
	$('#clote').val('');
	$("#mdescripcion").removeAttr("readonly");
	$('#tbLotes').html('');
	document.getElementById('mdetalle').style.display = 'none';
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

function marcados(nvalor)
{
	let numerosl = [];
	let cantidades=0;
	let cpedido=parseFloat($('#mcantidad').val());
	let cactual=parseFloat($('#centregar').val());

	if (nvalor.checked) {
		if (cpedido>=cactual) {
			$(".nlote:checked").each(function(){
				var valor=this.value.split('|');
			    numerosl.push(valor[0]);
			    cantidades+=parseFloat(valor[1]);
			});

			document.getElementById('clote').value=numerosl;
			document.getElementById('centregar').value=cantidades;
		}
		else {
			nvalor.checked=0;
		}
	}else{
		$(".nlote:checked").each(function(){
			var valor=this.value.split('|');
		    numerosl.push(valor[0]);
		    cantidades+=parseFloat(valor[1]);
		});

		document.getElementById('clote').value=numerosl;
		document.getElementById('centregar').value=cantidades;
	}
}

function productoBarras(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo de barra no existe');
				} else {
					var item=JSON.parse(responder);
					$('#bussalida').modal('show');
				  $("#munidades").removeClass("is-invalid");
				  $("#mcosto").removeClass("is-invalid");
				  $("#mlote").removeClass("is-invalid");
					$("#mdescripcion").prop('readonly',true);

					var nproducto=item.descripcion;
					if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
					document.getElementById('mcodigo').value=item.id;
					document.getElementById('mdescripcion').value=nproducto;
  				document.getElementById('mstock').value = item.stock;
  				document.getElementById('mfactor').value = 1;

					document.getElementById('mcosto').value = item.pventa;
					document.getElementById('munidades').value = 1;
					document.getElementById('mtotal').value = 1*item.pventa;
					document.getElementById('mcantidad').value = 1;
					document.getElementById('mactivar').value = item.lote;
					document.getElementById('munidades').select();
					if (item.lote==1 || item.serie==1) {
						$('#tbLotes').html('');
						document.getElementById('mdetalle').style.display = 'block';

						var lurl=url.replace('busCodigobarra','busLotes');
						var cadena='';
						$.post(lurl,{id:item.id},function(data){
							var c = JSON.parse(data);
							$.each(c, function(i,item){
								var valores=item.nlote+'|'+item.stock;
								cadena += '<tr>';
								cadena += '<td><div class="form-check"><label class="form-check-label"><input class="form-check-input nlote" type="checkbox" value="'+valores+'" onclick="marcados(this)">'+item.nlote+'</label><div></td>';
								cadena += '<td>'+item.stock+'</td>';
								cadena += '<td>'+item.fvencimiento+'</td>';
								cadena += "</tr>";
								$('#tbLotes').html(cadena);
							});
						});
					}else{
						document.getElementById('mdetalle').style.display = 'none';
					}

					$('#mmedida').html('');
					if (item.factor>1) {
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.pventa+'">Precio Unitario</option>');
						$("#mmedida").append('<option value="'+item.umedidac+'|'+item.factor+'|'+item.venta+'">Precio Mayorista</option>');
					}else{
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.pventa+'">Precio Unitario</option>');
					}
				}
			});
		}
		$('#codbarra').val('');
		$('#codbarra').focus();
	}
}

/*=========================================================================================================================
=                                                   funciones inventario                                                  =
=========================================================================================================================*/
$('#busproductosu').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#busproductosu').on('hidden.bs.modal', function (e){
	reset_inventario();
})

function productoNombreu(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

		        	var nproducto=item.descripcion;
		        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				    cadena +='<a href="javascript:void(0)" onclick="minventario(\''+item.id+'\', \`'+nproducto+'\`,\''+item.stock+'\',\''+item.lote+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+' -- Stock : '+item.stock+'</dt></a>';
				    cadena +='<hr class="m-0">';
		        	$('#grdescripcion').html(cadena);
				});
			}
	    })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function minventario(cod,nom,stock,lote)
{
	reset_inventario();
	$("#munidades").removeClass("is-invalid");

	document.getElementById('mcodigo').value = cod;
	document.getElementById('mdescripcion').value = nom;
	document.getElementById('munidades').value = 1;
	document.getElementById('mactivar').value = lote;
	document.getElementById('mstock').value = stock;

	if (lote==1) {
		document.getElementById('mdetalle').style.display = 'block';
		$("#mlote").prop('required',true);
	}else{
		document.getElementById('mdetalle').style.display = 'none';
        $("#mlote").removeAttr("required");
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('munidades').select();
}

function productoBarrau(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo no existe');
				} else {
	  			//console.log(responder);
					var item=JSON.parse(responder);
					$('#busproductosu').modal('show');
					$("#mdescripcion").prop('readonly',true);
					var nproducto=item.descripcion;
					if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
					document.getElementById('mcodigo').value=item.id;
					document.getElementById('mdescripcion').value=nproducto;
					document.getElementById('munidades').value = 1;
					document.getElementById('mactivar').value = item.lote;
					document.getElementById('mstock').value = item.stock;
					document.getElementById('munidades').select();
					if (item.lote==1) {
						document.getElementById('mdetalle').style.display = 'block';
					}else{
						document.getElementById('mdetalle').style.display = 'none';
					}
				}
			});
		}
	}
}

function reset_inventario()
{
	$('#codbarra').val(''); // formulario principal
	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#munidades').val('');
	$('#mlote').val('');
	$('#mfecha').val('');
	$("#mdescripcion").removeAttr("readonly");
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

/*=======================================================================================================================
=                                                    funciones proveedor                                                =
=======================================================================================================================*/
function envioProveedor(url)
{
	event.preventDefault();
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: $('#fdatos').serialize(),
		success: function(responder) {
			var c = JSON.parse(responder);
			if (c.success) {
				var item = c.data;
				document.getElementById('idproveedor').value=item.idproveedor;
				document.getElementById('proveedor').value=item.proveedor;
			} else {
				toastr.error(c.data);
			}
		},
		error: function(error) {
			console.log(error);
		}
	});
	$('#busdatos').modal('hide');
}

function proveedorNombre(url,id)
{
	$('#grproveedor').html('');
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: { id : id },
		success: function(responder) {
			var datos=JSON.parse(responder);
			if (datos.length==0)
			{
				$('#grproveedor').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
					cadena += '<tr>';
					cadena += '<td>'+item.nombres+'</td>';
					cadena += '<td>'+item.documento+'</td>';
					cadena += '<td>'+item.direccion+' - <i>'+item.ndistrito+'</i></td>';
					cadena += '<td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosProveedor(\''+item.id+'\', \`'+item.nombres+'\`);" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>';
					cadena += '</tr>';
					$('#tblbuscador').html(cadena);
				});
			}
		},

		error: function(error) {
			console.log(error);
		}
	});
}

function datosProveedor(cod,nom)
{
	document.getElementById('idproveedor').value = cod;
	document.getElementById('proveedor').value = nom;
	$('#busdatos').modal('hide');
}

/*=======================================================================================================================
=                                                    funciones solicitud                                                =
=======================================================================================================================*/
$('#bussolicitud').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#bussolicitud').on('hidden.bs.modal', function (e){
	reset_solicitud();
})

function productoNombret(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

        	var nproducto=item.descripcion;
        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
        	cadena +='<a href="javascript:void(0)" onclick="msolicitud(\''+item.id+'\', \`'+nproducto+'\`,\''+item.factor+'\',\''+item.umedidac+'\',\''+item.umedidav+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt></a>';
        	$('#grdescripcion').html(cadena);
				});
			}
	  })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function msolicitud(cod,nom,fac,mec,mev)
{
	reset_solicitud();
	document.getElementById('mcodigo').value = cod;
	document.getElementById('mdescripcion').value = nom;
	$('#mmedida').html('');
	if (fac>1) {
		$("#mmedida").append('<option value="'+mev+'|1">Precio Unidad</option>');
		$("#mmedida").append('<option value="'+mec+'|'+fac+'">Precio Caja</option>');
	}else{
		$("#mmedida").append('<option value="'+mev+'|1">Precio Unidad</option>');
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('munidades').select();
}

function appsolicitud()
{
	var codigo=$('#mcodigo').val();
	var nombres=$('#mdescripcion').val();
	var cantidad=$('#munidades').val();
	var medida=$('#mmedida').val().split('|');

	if(codigo!='' && nombres!='' && cantidad!=''){
		cadena = '<tr>';
		cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
		cadena += '<td><input type="hidden" name="factor[]" value="'+medida[1]+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
		cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
		cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
		cadena += "</tr>";
	  var tr = $(cadena).prependTo("#grilla");

	  // Ajustar la altura del textarea recién agregado
	  var textarea = tr.find('textarea[name="descripcion[]"]');
  	textarea.height(textarea[0].scrollHeight - 4);
		reset_solicitud();
		feliminar();
		$('#bussolicitud').modal('hide');
	}
};

function reset_solicitud()
{
	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#munidades').val('');
	$('#mmedida').val('');
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

/*=======================================================================================================================
=                                                     funciones compras                                                 =
=======================================================================================================================*/
$('#buscompra').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#buscompra').on('hidden.bs.modal', function (e){
	reset_compra();
})

function productoNombrec(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

		        	var nproducto=item.descripcion;
		        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
		        	cadena +='<a href="javascript:void(0)" onclick="mcompra(\''+item.id+'\', \`'+nproducto+'\`,\''+item.compra+'\',\''+item.factor+'\',\''+item.lote+'\',\''+item.umedidac+'\',\''+item.umedidav+'\',\''+item.tafectacion+'\',\''+item.pventa+'\',\''+item.venta+'\',\''+item.pblister+'\',\''+item.factorb+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt></a>';
				    cadena +='<hr class="m-0">';
		        	$('#grdescripcion').html(cadena);
				});
			}
	    })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function mcompra(cod,nom,pre,fac,lote,mec,mev,afec,unid,caja,blis,fab)
{
	reset_compra();
	$("#munidades").removeClass("is-invalid");
	$("#mcosto").removeClass("is-invalid");
	$("#mlote").removeClass("is-invalid");
	$("#mfecha").removeClass("is-invalid");

	document.getElementById('mcodigo').value = cod;
	document.getElementById('mdescripcion').value = nom;
	document.getElementById('mfactor').value = fac;
	document.getElementById('mtafectacion').value = afec;
	document.getElementById('mactivar').value = lote;

	document.getElementById('munidades').value = 1;
	document.getElementById('mcosto').value = pre;
	document.getElementById('mtotal').value = pre;
	document.getElementById('mcantidad').value = fac*1;
	document.getElementById('mmonto').value = pre/fac;
	if (lote==1) {
		document.getElementById('mdetalle').style.display = 'block';
	}else{
		document.getElementById('mdetalle').style.display = 'none';
	}

	$('#mmedida').html('');
	if (fac>1) {
		$("#mmedida").append('<option value="BX|'+fac+'|'+pre+'">Caja</option>');
		$("#mmedida").append('<option value="NIU|1|'+pre/fac+'">Unidad</option>');
	}else{
		$("#mmedida").append('<option value="NIU|1|'+pre+'">Unidad</option>');
	}

	var precios=$('#pactualizar').val();
	if (precios==1)
	{
		document.getElementById('mutilidadu').value = $('#gunidad').val();
		if (caja>0){
			$("#mventa").removeAttr("readonly");
			$("#mutilidadc").removeAttr("readonly");
			document.getElementById('mfactorc').value = fac;
			document.getElementById('mutilidadc').value = $('#gcaja').val();
		}else{
			$("#mventa").prop('readonly',true);
			$('#mventa').val('');
			$("#mutilidadc").prop('readonly',true);
			$('#mutilidadc').val('');
		}
		if (blis>0){
			$("#mblister").removeAttr("readonly");
			$("#mutilidadb").removeAttr("readonly");
			document.getElementById('mfactorb').value = fab;
			document.getElementById('mutilidadb').value = $('#gblister').val();
		}else{
			$("#mblister").prop('readonly',true);
			$('#mblister').val('');
			$("#mutilidadb").prop('readonly',true);
			$('#mutilidadb').val('');
		}
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('munidades').select();
}

function appcompra()
{
	var codigo=$('#mcodigo').val();
	var nombres=$('#mdescripcion').val();
	var afectacion=$('#mtafectacion').val();
	var cantidad=$('#munidades').val();
	var precio=$('#mcosto').val();
	var total=$('#mtotal').val();
	var nlote=$('#mlote').val();
	var fechal=$('#mfecha').val();
	var restringir=$('#mactivar').val();
	var factor=$('#mfactor').val();
	var medida=$('#mmedida').val().split('|');

	var almacenc=$('#mcantidad').val();
	var almacenp=$('#mmonto').val();

	var pventa=$('#mpventa').val();
	var venta=$('#mventa').val();
	var blister=$('#mblister').val();
	var color= afectacion==15 ? ' class="table-primary"' : '';
	if (restringir==1) {
		if(codigo!='' && nombres!='' && cantidad!='' && precio!='' && nlote!=''){
			cadena = '<tr'+color+'>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="fvencimiento[]" value="'+fechal+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="factor[]" value="'+factor+'" class="factores"/><input type="hidden" name="almacenc[]" value="'+almacenc+'" class="calmacens"/><input type="hidden" name="almacenp[]" value="'+almacenp+'" class="palmacenes"/><input type="hidden" name="tafectacion[]" value="'+afectacion+'"/><div class="input-group"><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo unidades"><span class="badge-precio">'+factor+'</span></div></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo cantidades"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo precios"/><input type="hidden" name="pventa[]" value="'+pventa+'"/><input type="hidden" name="venta[]" value="'+venta+'"/><input type="hidden" name="blister[]" value="'+blister+'"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo text-right importes"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 eliminac" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += '</tr>';
		  var tr = $(cadena).appendTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);

			//suamr todos los totales
			calcularCompra();
			reset_compra();
			fceliminar();
    		$('#buscompra').modal('hide');
		}else{
			$('#mensajeerror').html('<strong class="text-danger">Falta ingresar datos del producto</strong>');
			if (cantidad=='') {document.getElementById("munidades").className += " is-invalid";}
			if (precio=='') {document.getElementById("mcosto").className += " is-invalid";}
			if (nlote=='') {document.getElementById("mlote").className += " is-invalid";}
		}
	}else{
		if(codigo!='' && nombres!='' && cantidad!='' && precio!=''){
			cadena = '<tr'+color+'>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
			cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
			cadena += '<td><input type="text" name="fvencimiento[]" value="'+fechal+'" class="campo"/></td>';
			cadena += '<td><input type="hidden" name="factor[]" value="'+medida[1]+'" class="factores"/><input type="hidden" name="almacenc[]" value="'+almacenc+'" class="calmacens"/><input type="hidden" name="almacenp[]" value="'+almacenp+'" class="palmacenes"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo unidades"/><input type="hidden" name="tafectacion[]" value="'+afectacion+'"/></td>';
			cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo cantidades"/></td>';
			cadena += '<td><input type="text" name="precio[]" value="'+decimales(precio,2)+'" class="campo precios"/><input type="hidden" name="pventa[]" value="'+pventa+'"/><input type="hidden" name="venta[]" value="'+venta+'"/><input type="hidden" name="blister[]" value="'+blister+'"/></td>';
			cadena += '<td><input type="text"  name="importe[]" value="'+decimales(total,2)+'" class="campo text-right importes"/></td>';
			cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 eliminac" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
			cadena += '</tr>';
		  var tr = $(cadena).appendTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);

			//suamr todos los totales
			calcularCompra();
			reset_compra();
			fceliminar();
    		$('#buscompra').modal('hide');
		}
	}
};

function reset_compra()
{
	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#munidades').val('');
	$('#mcosto').val('');
	$('#mtotal').val('');
	$('#mmedida').val('');
	$('#mtafectacion').val('');

	$('#mlote').val('');
	$('#mfecha').val('');

	$('#mpventa').val('');
	$('#mventa').val('');
	$('#mblister').val('');
	$("#mventa").prop('readonly',true);
	$("#mblister").prop('readonly',true);
	$("#utilidadc").prop('readonly',true);
	$("#utilidadb").prop('readonly',true);

	$("#mdescripcion").removeAttr("readonly");
	document.getElementById('mdetalle').style.display = 'none';
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

function productoBarrac(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo de barra no existe');
				} else {
      		//console.log(responder);
					var item=JSON.parse(responder);
					$('#buscompra').modal('show');
					$("#mdescripcion").prop('readonly',true);

					var nproducto=item.descripcion;
					if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
					document.getElementById('mcodigo').value=item.id;
					document.getElementById('mdescripcion').value=nproducto;
					document.getElementById('mfactor').value = item.factor;
					document.getElementById('mtafectacion').value = item.tafectacion;
					document.getElementById('mactivar').value = item.lote;

					document.getElementById('munidades').value = 1;
					document.getElementById('mcosto').value = item.compra;
					document.getElementById('mtotal').value = item.compra;
					document.getElementById('mcantidad').value = 1*item.factor;
					document.getElementById('mmonto').value = item.compra/item.factor;
					if (item.lote==1) {
						document.getElementById('mdetalle').style.display = 'block';
					}else{
						document.getElementById('mdetalle').style.display = 'none';
					}

					$('#mmedida').html('');
					if (item.factor>1) {
						$("#mmedida").append('<option value="'+item.umedidac+'|'+item.factor+'|'+item.compra+'">Precio Caja</option>');
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.compra/item.factor+'">Precio Unidad</option>');
					}else{
						$("#mmedida").append('<option value="'+item.umedidav+'|1|'+item.compra+'">Precio Unidad</option>');
					}

					var precios=$('#pactualizar').val();
					if (precios==1)
					{
						document.getElementById('mpventa').value = item.pventa;
						if (item.venta>0){
							$("#mventa").removeAttr("readonly");
							document.getElementById('mventa').value = item.venta;
						}else{
							$("#mventa").prop('readonly',true);
							$('#mventa').val('');
						}
						if (item.pblister>0){
							$("#mblister").removeAttr("readonly");
							document.getElementById('mblister').value = item.pblister;
						}else{
							$("#mblister").prop('readonly',true);
							$('#mblister').val('');
						}
					}

					document.getElementById('munidades').select();
				}
			});
		}
		$('#codbarra').val('');
		$('#codbarra').focus();
	}
}

function fceliminar()
{
	$("a.eliminac").click(function(){
		$(this).parents("tr").fadeOut("normal", function(){
			$(this).remove();
			calcularCompra();
			toastr.error('El producto fue eliminado');
		})
	});
};

function calcularc()
{
	$('input.cantidades').keyup(function(){
		unidad = $(this).parents('tr').find('.unidades').val();
		factor = $(this).parents('tr').find('.factores').val();
		cantidad = $(this).val();
		precio = $(this).parents('tr').find('.precios').val();
		importe = cantidad*precio;
		almacenc=cantidad*factor;
		$(this).parents('tr').find('.calmacenes').val(almacenc);
		$(this).parents('tr').find('.importes').val(decimales(importe,2));

		calcularCompra();
	});

	$('input.precios').keyup(function(){
		unidad = $(this).parents('tr').find('.unidades').val();
		factor = $(this).parents('tr').find('.factores').val();
		cantidad = $(this).parents('tr').find('.cantidades').val();
		precio = $(this).val();
		importe = cantidad*precio;
		almacenp=precio/factor;
		$(this).parents('tr').find('.palmacenes').val(decimales(almacenp,2));
		$(this).parents("tr").find('.importes').val(decimales(importe,2));

		calcularCompra();
	});
}

function pagoCreditoc(valor)
{
	if (valor==1) {
		document.getElementById('contado').style.display = 'block';
	}else{
		document.getElementById('contado').style.display = 'none';
	}
}

function borrarCompra(url,div)
{
	$.post(url,function(data){
		$("#"+div+"").remove();
		calcularCompra();
		toastr.error('El item fue eliminado');
	});
}

/*======================================================================================================================
=                                                     funciones producto                                                 =
======================================================================================================================*/
function envioProducto(url)
{
	event.preventDefault();
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: $('#fdatos').serialize(),
		success: function(responder) {
			var c = JSON.parse(responder);
			if (c.success) {
				toastr.success(c.mensaje);
			} else {
				toastr.error(c.mensaje);
			}
		},
		error: function(error) {
			console.log(error);
		}
	});
	$('#busdatos').modal('hide');
}

/*=======================================================================================================================
=                                                    funciones clientes                                                 =
=======================================================================================================================*/
function clienteListado(url,id)
{
	$('#grcliente').html('');
	const data = new FormData();
	data.append('id', id);
	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#grcliente').html('<tr><td colspan="7"><strong>No hay datos de la busqueda</strong></td></tr>');
		}
		else
		{
			var j=1;
			var cadena='';
			$.each(datos, function(i,item){
				let eurl=url.replace('busCliente','clientei');
				let aurl=url.replace('busCliente','anexos');
				let purl=url.replace('busCliente','pacumulados');
				let durl=url.replace('busCliente','cliented');
			    cadena += '<tr>';
				cadena +='<td>'+item.id+'</td>';
				cadena +='<td>'+item.nombres+'</td>';
				cadena +='<td>'+item.documento+'</td>';
				cadena +='<td>'+item.telefono+'</td>';
				cadena +='<td>'+item.email+'</td>';
				cadena +='<td>'+item.direccion+'</td>';
				cadena +='<td><div class="btn-group">';
				if (item.id>1) {
				cadena +='<button type="button" class="btn btn-warning btn-sm py-0" onclick="mostrarModal(\''+eurl+'/'+item.id+'\',\'bdatos\')" title="Editar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-edit"></i></button>';
				if (item.spuntos==1) {
				cadena +='<a href="'+purl+'/'+item.id+'" class="btn btn-info btn-sm py-0" title="Puntos Acumulados" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-plus-circle"></i></a>';
				}
				cadena +='<a href="javascript:void(0)" onclick="borrar(\''+durl+'/'+item.id+'\',\'Desea borrar '+item.nombres+'?\')" class="btn btn-danger btn-sm py-0" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
				}
				cadena +='</div></td>';
			  cadena +='</tr>';
				$('#grcliente').html(cadena);
				j++;
			});
		}
	})
	.catch(function(err) {
	   console.log(err);
	});
}

function envioCliente(url)
{
	event.preventDefault();
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: $('#fdatos').serialize(),
		success: function(responder) {
			var c = JSON.parse(responder);
			if (c.success) {
				var item = c.data;
				document.getElementById('idcliente').value=item.idcliente;
				document.getElementById('tdocumento').value = item.tdocumento;
				document.getElementById('cliente').value=item.cliente;
			} else {
				toastr.error(c.data);
			}
		},
		error: function(error) {
			console.log(error);
		}
	});
	$('#busdatos').modal('hide');
}

function clienteNombre(url,id,envio)
{
	$('#tblbuscador').html('');
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: { id : id },
		success: function(responder) {
			var datos=JSON.parse(responder);
			if (datos.length==0)
			{
				$('#tblbuscador').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		    	cadena += '<tr>';
					cadena += '<td>'+item.nombres+'</td>';
					cadena += '<td>'+item.documento+'</td>';
					cadena += '<td>'+item.direccion+' - <i>'+item.ndistrito+'</i></td>';
					cadena += '<td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosCliente(\''+item.id+'\', \`'+item.nombres+'\`, \''+item.tdocumento+'\', \''+item.puntos+'\', \''+envio+'\');" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>';
					cadena += '</tr>';
					$('#tblbuscador').html(cadena);
				});
			}
		},

		error: function(error) {
			console.log(error);
		}
	});
}

function datosCliente(cod,nom,tipo,punto,envio)
{
	document.getElementById('idcliente').value = cod;
	document.getElementById('cliente').value = nom;
	if (envio!='') {
		document.getElementById('tdocumento').value = tipo;
	}
	if (envio=='V') {
		$('#puntaje').html(punto);
	}
	$('#busdatos').modal('hide');
}

function mostrarDescuento(valor)
{
	$('#nvale').val('');
	$('#mdscto').val('');
	$("#validador").val(0);
	if (valor) {
		var cliente=$('#idcliente').val();
		var tidentidad=$('#tdocumento').val();
		var totales=0;
		var contador = document.getElementsByName("dscto[]").length;
	  for(i=0;i<contador;i++){
			if (!isNaN(parseFloat(document.getElementsByName("dscto[]")[i].value))) totales += parseFloat(document.getElementsByName("dscto[]")[i].value);
	  }

		if (totales==0 && cliente>1 && tidentidad!=6) {
			document.getElementById('cvale').style.display = 'block';
		} else {
			document.getElementById('cvale').style.display = 'none';
		}
	} else {
		var elemento = document.getElementById('cvale');
		if (elemento) {
			elemento.style.display = 'none';
		}
	}
}

function validarVale(url)
{
	var con= $("#validador").val();
	var id= $("#nvale").val();
	$.post(url,{id},function(data){
		var c = JSON.parse(data);

		if (c.tipo==1) {
			toastr.error(c.mensaje);
		} else {
			let descuento=c.importe;
			if (descuento>0) {
				if (con==0) {
					document.getElementById('validador').value=1;
					document.getElementById('mdsctog').value=descuento;
					calcularDescuento(descuento);
					toastr.success(c.mensaje);
				}
			} else {
				$('#mdsctog').val('');
				$("#validador").val(0);
			}
		}
	});
}

function limpiarVale()
{
	$("#validador").val(0);
	$('#nvale').val('');
	$('#mdsctog').val('');
	calcularVenta();
}

/*=========================================================================================================================
=                                                      funciones cotizacion                                                   =
=========================================================================================================================*/
function productoNombren(url,id)
{
	$('#bcodigo').val('');
	$('#tblproducto').html('');
	const data = new FormData();
	data.append('id', id);

	fetch(url, {
		method: 'POST',
		body: data
	})
	.then(function(response) {
		if(response.ok) {
			return response.json();
		} else {
			throw "Error en la llamada Ajax";
		}
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#tblproducto').html('<strong>No hay datos de la busqueda</strong>');
		}
		else
		{
			var cadena='';
			$.each(datos, function(i,item){
				let durl=url.replace('busProductos','busInformacion');
				var nproducto=item.descripcion;
		        if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				if (item.stock>0) {
					cadena += '<tr>';
				}
				else {
					cadena += '<tr style="color: red;">';
				}
				cadena +='<td class="priority">'+item.id+'</td>';
				cadena +='<td><a href="javascript:void(0)" onclick="mostrarModal(\''+durl+'/'+item.id+'\',\'bdatos\',\'Informacion Producto\')">'+nproducto+'</a></td>';
				cadena +='<td align="center">'+item.stock+'</td>';
				cadena +='<td align="center"><a href="javascript:void(0)" class="btn btn-info btn-sm py-0" onclick="appcotizacion(\''+item.id+'\', \`'+nproducto+'\`,\''+item.umedidav+'\',\'1\',\''+item.tafectacion+'\',\''+item.pventa+'\',\''+item.stock+'\',\''+item.edicion+'\');">'+item.pventa+'</a></td>';
				cadena +='<td align="center">';
				if (item.umedidab!='' && item.factorb>1 && item.pblister>0) {
				cadena +='<a href="javascript:void(0)" class="btn btn-primary btn-sm py-0" onclick="appcotizacion(\''+item.id+'\', \`'+nproducto+' BLISTER X '+item.factorb+'\`,\''+item.umedidab+'\','+item.factorb+',\''+item.tafectacion+'\',\''+item.pblister+'\',\''+item.stock+'\',\''+item.edicion+'\');" style="position: relative;">'+item.pblister+'<span class="badge-precio">'+item.factorb+'</span></a>';
				}
				cadena +='</td>';
				cadena +='<td align="center">';
				if (item.umedidac!='' && item.factor>1 && item.venta>0) {
				cadena +='<a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="appcotizacion(\''+item.id+'\', \`'+nproducto+' CJ X '+item.factor+'\`,\''+item.umedidac+'\','+item.factor+',\''+item.tafectacion+'\',\''+item.venta+'\',\''+item.stock+'\',\''+item.edicion+'\');" style="position: relative;">'+item.venta+'<span class="badge-precio">'+item.factor+'</span></a>';
				}
				cadena +='</td>';
				cadena +='</tr>';
				$('#tblproducto').html(cadena);
			});
		}
	})
	.catch(function(err) {
		console.log(err);
	});
}

function appcotizacion(id,producto,unidad,factor,afectacion,precio,stock,edicion)
{
		let mtotal=decimales(precio*1,2);
		let estilo=edicion==0 ? 'campo' : 'form-control form-control-sm';
		let bloquear=edicion==0 ? 'onkeydown="return false"' : '';
		cadena = '<tr>';
		cadena += '<td><input type="hidden" name="idproducto[]" value="'+id+'" class="productoc"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea>';
		cadena += '<div class="row"><div class="col-sm-3"><input type="hidden" name="factor[]" id="factor[]" value="'+factor+'" class="factorc"><input type="text" name="unidad[]" value="'+unidad+'" class="campo"/></div>';
		cadena += '<div class="col-sm-3"><input type="number" name="cantidad[]" value="1" min="1" class="form-control form-control-sm cantidadc"/></div>';
		cadena += '<div class="col-sm-3"><input type="text" name="precio[]" value="'+precio+'" class="'+estilo+' text-right precioc" '+bloquear+'/></div>';
		cadena += '<div class="col-sm-3"><input type="number" min="0.01" step="0.01"  name="importe[]" value="'+mtotal+'" class="campo text-right importec" onkeydown="return false"/></div></div></td>';
		cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
		cadena += "</tr>";
	  var tr = $(cadena).appendTo("#grilla");

	  // Ajustar la altura del textarea recién agregado
	  var textarea = tr.find('textarea[name="descripcion[]"]');
  	textarea.height(textarea[0].scrollHeight - 4);
		fneliminar();
		calcularn();
		calcularCotizacion();
};

function fneliminar()
{
    $("a.elimina").click(function(){
    	$(this).parents("tr").fadeOut("normal", function(){
      $(this).remove();
			calcularCotizacion();
			toastr.error('El producto fue eliminado');
      })
   });
};

function productoBarran(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo de barra no existe');
				} else {
					var item=JSON.parse(responder);
					var producto=item.descripcion;
					if (item.nlaboratorio!='') {producto+=' ['+item.nlaboratorio+']';}
					let mtotal=decimales(item.pventa*1,2);
					let estilo=item.edicion==0 ? 'campo' : 'form-control form-control-sm';
					let bloquear=item.edicion==0 ? 'onkeydown="return false"' : '';
					cadena = '<tr>';
					cadena += '<td><input type="hidden" name="idproducto[]" value="'+item.id+'" class="productoc"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea>';
					cadena += '<div class="row"><input type="hidden" name="factor[]" id="factor[]" value="1" class="factorc"><input type="text" name="unidad[]" value="'+item.umedidav+'" class="campo"/></div>';
					cadena += '<div class="col-sm-3"><input type="number" name="cantidad[]" value="1" min="1" class="form-control form-control-sm cantidadc"/></div>';
					cadena += '<div class="col-sm-3"><input type="text" name="precio[]" value="'+item.pventa+'" class="'+estilo+' text-right precioc" '+bloquear+'/></div>';
					cadena += '<div class="col-sm-3"><input type="number" min="0.01" step="0.01" name="importe[]" value="'+mtotal+'" class="campo text-right importec" onkeydown="return false"/></div></div></td>';
					cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
					cadena += "</tr>";
				  var tr = $(cadena).appendTo("#grilla");

				  // Ajustar la altura del textarea recién agregado
				  var textarea = tr.find('textarea[name="descripcion[]"]');
			  	textarea.height(textarea[0].scrollHeight - 4);
					fneliminar();
					calcularn();
					calcularCotizacion();
				}
			});
		}else{
			let durl=url.replace('busCodigobarra','busProductos');
			productoNombren(durl,id);
		}
		$('#bproducto').val('');
		$('#bcodigo').val('');
		$('#codbarra').val('');
		$('#codbarra').focus();
	}
}

function calcularn()
{
	$('input.cantidadc').change(function(){
		factor=$(this).parents('tr').find('.factorc').val();
		cantidad = $(this).val();
		precio=$(this).parents('tr').find('.precioc').val();
		importe = cantidad*precio;
		$(this).parents('tr').find('.importec').val(decimales(importe,2));
		calcularCotizacion();
	});

	$('input.cantidadc').keyup(function(){
		factor=$(this).parents('tr').find('.factorc').val();
		cantidad = $(this).val();
		precio=$(this).parents('tr').find('.precioc').val();
		importe = cantidad*precio;
		$(this).parents('tr').find('.importec').val(decimales(importe,2));
		calcularCotizacion();
	});

	$('input.precioc').keyup(function(){
		cantidad = $(this).parents('tr').find('.cantidadc').val();
		precio = $(this).val();
		importe = cantidad*precio;
		$(this).parents('tr').find('.importec').val(decimales(importe,2));
		calcularCotizacion();
	});
}

function limpiarBuscadorn(url)
{
	$('#bproducto').val('');
	productoNombren(url,'a');
}

function borrarCotizacion(url,div)
{
	$.post(url,function(data){
		$("#"+div+"").remove();
		calcularCotizacion();
		toastr.error('El item fue eliminado');
	});
}

/*=========================================================================================================================
=                                                     funciones lotes                                                     =
=========================================================================================================================*/
function marcadol(nvalor)
{
	let numerosl = [];
	let cantidades=0;
	let numeracion = $('#orden').val();
	let cpedido=parseFloat($('#mcantidad'+numeracion+'').val());
	let cactual=parseFloat($('#lentregar').val());

	if (nvalor.checked) {
		if (cpedido>cactual) {
			$(".nlote:checked").each(function(){
				var valor=this.value.split('|');
			    numerosl.push(valor[0]);
			    cantidades+=parseFloat(valor[1]);
			});

			document.getElementById('rlote').value=numerosl;
			document.getElementById('lentregar').value=cantidades;
		}
		else {
			nvalor.checked=0;
		}
	}else{
		$(".nlote:checked").each(function(){
			var valor=this.value.split('|');
		    numerosl.push(valor[0]);
		    cantidades+=parseFloat(valor[1]);
		});

		document.getElementById('rlote').value=numerosl;
		document.getElementById('lentregar').value=cantidades;
	}
	document.getElementById('lcantidad').value=cpedido;
}

function applotes()
{
	var almacenc=$('#lcantidad').val();
	let centregar = parseFloat($('#lentregar').val());

	if (centregar>=almacenc && centregar>0) {
		let numeracion = $('#orden').val();
		let lotes = $('#rlote').val();
		document.getElementById('mlote'+numeracion+'').value=lotes;
		$('#busdatos').modal('hide');
	} else {
			$('#mensajeerrol').html('<b class="text-danger">La cantidad de lote seleccionada es mucho menor</b>');
	}
}

/*======================================================================================================================
=                                                     funciones ventas                                                 =
======================================================================================================================*/
function tcomprobante(nro,url)
{
	$.post(url,{nro},function(resp){
		document.getElementById('serie').value=resp;
	});

	if (nro=='01') {
		$('#idcliente').val('');
		$('#cliente').val('');
		$('#dni').val('');
		$('#tdocumento').val('');
	} else {
		$('#idcliente').val('1');
		$('#cliente').val('CLIENTES VARIOS');
		$('#dni').val('');
		$('#tdocumento').val(0);
		$('#puntaje').html('Puntos Acumulados : 0');
	}

	valor=$('#canjear').prop("checked");
	mostrarDescuento(valor);
}

function mostrarDetraccion(url,nro)
{
  $("#codigo").val('');
  $("#ncuenta").val('');
  $("#medio").val('');
  $("#pdetraccion").val('');
  if (nro=='1001') {
  	mostrarDato(url,nro,'ncuenta');
    document.getElementById('detraccion').style.display='block';
    $("#codigo").prop('required',true);
    $("#ncuenta").prop('required',true);
    $("#medio").prop('required',true);
    $("#pdetraccion").prop('required',true);
  }
  else {
    document.getElementById('detraccion').style.display='none';
    $("#codigo").removeAttr("required");
    $("#ncuenta").removeAttr("required");
    $("#medio").removeAttr("required");
    $("#pdetraccion").removeAttr("required");
  }
}

function mostrarRetencion(valor)
{
  $("#pretencion").val('');
  $("#mretencion").val('');
  $("#pagar").val('');
  if (valor==1) {
  	document.getElementById('pretencion').value=3;
  	porcentajes('totalg','pretencion','mretencion');
  	diferencia('totalg','mretencion','pagar');
    document.getElementById('retencion').style.display='block';
    $("#pretencion").prop('required',true);
    $("#mretencion").prop('required',true);
  }
  else {
    document.getElementById('retencion').style.display='none';
    $("#pretencion").removeAttr("required");
    $("#mretencion").removeAttr("required");
  }
}

function productoNombrev(url,id)
{
	$('#codbarra').val('');
	$('#tblproducto').html('');
	const data = new FormData();
	data.append('id', id);

	fetch(url, {
	   method: 'POST',
	   body: data
	})
	.then(function(response) {
	   if(response.ok) {
	       return response.json();
	   } else {
	       throw "Error en la llamada Ajax";
	   }
	})
	.then((datos) => {
		//console.log(datos);
		if (datos.length==0)
		{
			$('#tblproducto').html('<strong>No hay datos de la busqueda</strong>');
		}
		else
		{
			var cadena='';
      $.each(datos, function(i,item){
      	let durl=url.replace('busProductos','busInformacion');
      	let lurl=url.replace('busProductos','busProductoLotes');

      	var nproducto=item.descripcion;
        if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
	    	if (item.stock<1 && item.tipo=='B') {
	    		colores= 'red';
				} else {
					if (item.bonificacion>0) {colores= 'blueviolet';} else {colores= 'black';}
				}
        if (item.vsujeta==1) {tcolor='table-success';} else {tcolor='';}
				cadena += '<tr style="color: '+colores+';" class="'+tcolor+'">';
				cadena +='<td><a href="javascript:void(0)" onclick="mostrarModal(\''+durl+'/'+item.id+'\',\'bdatos\',\'Informacion Producto\')">'+nproducto+'</a></td>';
				cadena +='<td class="priority" align="right">'+item.bonificacion+'</td>';
				if (item.lote==1 && item.stock>0) {
				cadena +='<td align="center"><a href="javascript:void(0)" onclick="mostrarModal(\''+lurl+'/'+item.id+'\',\'bdatos\',\'Mostrar Producto y Lotes\')" class="badge badge-dark" style="font-size: 100%;" title="Seleccionar Lotes">'+(item.lstock==1 && item.stock>99 ? '+99': item.stock)+'</a></td>';
				} else {
				cadena +='<td align="center">'+(item.lstock==1 && item.stock>99 ? '+99': item.stock)+'</td>';
				}
				cadena +='<td align="center" style="font-weight: 700;">';
				if (item.stock>0 || item.tipo=='S') {
					cadena +='<a href="javascript:void(0)" class="btn btn-info btn-sm py-0 punidad" onclick="appventa(\''+item.id+'\', \`'+nproducto+'\`,\''+item.umedidav+'\',\'1\',\''+item.tafectacion+'\',\''+item.pventa+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.tipo+'\',\''+item.descuento+'\',\''+item.edicion+'\',\''+item.vsujeta+'\',\''+item.vbonificar+'\');">'+item.pventa+'</a>';
				} else {
					cadena +=''+item.pventa+'';
				}
				cadena +='</td>';
				cadena +='<td align="center" style="font-weight: 700;">';
				if (item.umedidab!='' && item.factorb>1 && item.pblister>0) {
					if (parseInt(item.stock)>=item.factorb) {
	          cadena +='<a href="javascript:void(0)" class="btn btn-primary btn-sm py-0" onclick="appventa(\''+item.id+'\', \`'+nproducto+' BLISTER X '+item.factorb+'\`,\''+item.umedidab+'\','+item.factorb+',\''+item.tafectacion+'\',\''+item.pblister+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.tipo+'\',\''+item.descuento+'\',\''+item.edicion+'\',\''+item.vsujeta+'\',\''+item.vbonificar+'\');" style="position: relative;">'+item.pblister+'<span class="badge-precio">'+item.factorb+'</span></a>';
	        }else{
	        	cadena +=''+item.pblister+'';
	        }
				}
				cadena +='</td>';
				cadena +='<td align="center" style="font-weight: 700;">';
				if (item.umedidac!='' && item.factor>1 && item.venta>0) {
					if (parseInt(item.stock)>=item.factor) {
	          cadena +='<a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="appventa(\''+item.id+'\', \`'+nproducto+' CJ X '+item.factor+'\`,\''+item.umedidac+'\','+item.factor+',\''+item.tafectacion+'\',\''+item.venta+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.tipo+'\',\''+item.descuento+'\',\''+item.edicion+'\',\''+item.vsujeta+'\',\''+item.vbonificar+'\');" style="position: relative;">'+item.venta+'<span class="badge-precio">'+item.factor+'</span></a>';
	        }else{
	        	cadena +=''+item.venta+'';
	        }
				}
				cadena +='</td>';
			  cadena +='</tr>';
	      $('#tblproducto').html(cadena);
			});
		}
    })
	.catch(function(err) {
	   console.log(err);
	});
}

function appventa(id,producto,unidad,factor,afectacion,precio,lote,stock,tipo,dscto,edicion,receta,vbonificar)
{
		if (receta==1) {toastr.info('Este producto necesita receta', '',{"positionClass" : "toast-top-center"});}

		let almacenc=factor*1;
		let mtotal=decimales(precio*1,2);
		let escondido=dscto==2 ? 'hidden' : 'number';
		let descuento=dscto==0 ? '%' : 'S/.';
		let estilo=edicion==0 ? 'campo' : 'form-control form-control-sm';
		let bloquear=edicion==0 ? 'onkeydown="return false"' : '';
		cadena = '<tr>';
		cadena += '<td>';
		cadena += '<input type="hidden" name="idproducto[]" value="'+id+'" class="producton"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea><input type="hidden" class="tipon" name="tipo[]" value="'+tipo+'"><input type="hidden" class="colegiaturan" name="colegiatura[]" value=""><input type="hidden" class="doctorn" name="doctor[]" value=""><input type="hidden" class="pacienten" name="paciente[]" value="">';
		cadena += '<div class="row"><div class="col-sm-2 col-4">';
		if (vbonificar==1) {
		cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
		}
		cadena += '</div><div class="col-sm-1 col-4"><input type="text" name="nlote[]" value="" class="campo loten"/><input type="hidden" name="lote[]" value="'+lote+'" class="campo"/></div>';
		cadena += '<div class="col-sm-1 col-4"><input type="hidden" class="factorn" name="factor[]" value="'+factor+'"><input type="text" name="unidad[]" value="'+unidad+'" class="campo"/><input type="hidden" name="tafectacion[]" class="tafectacionn" value="'+afectacion+'"/></div>';
		cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="'+stock+'" class="stockn"/><input type="number" name="cantidad[]" value="1" min="1" class="form-control form-control-sm cantidadn" onkeypress="return event.keyCode != 13;"/><input type="hidden" class="calmacenn" name="almacenc[]" value="'+almacenc+'"/></div>';
		cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="'+precio+'" class="'+estilo+' text-right precion" '+bloquear+'/></div>';
		cadena += '<div class="col-sm-2 col-4"><input type="hidden" class="dscton" name="tdscto[]" value="'+dscto+'"><input type="'+escondido+'" step="0.01" min="0.01" name="dscto[]" value="" class="form-control form-control-sm border border-danger porcentajen" placeholder="('+descuento+')Dscto"/></div>';
		cadena += '<div class="col-sm-2 col-4"><input type="number" min="0.01" step="0.01" name="importe[]" value="'+mtotal+'" class="campo text-right importen no-spinners" onkeydown="return false"/></div></div>';
		cadena += '</td>';
		cadena += '<td>';
		if (receta==1) {
			cadena += '<a href="javascript:void(0)" onclick="mostrarReceta(this)" class="btn btn-success btn-sm py-0 mb-1" title="Receta"><i class="fa fa-file"></i></a><br>';
		}
		cadena += '<a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
		cadena += '</td>';
		cadena += "</tr>";
	  var tr = $(cadena).prependTo("#grilla");

	  // Ajustar la altura del textarea recién agregado
	  var textarea = tr.find('textarea[name="descripcion[]"]');
  	textarea.height(textarea[0].scrollHeight - 4);
		fveliminar();
		calcularv();
		calcularVenta();
};

function calcularv()
{
	$('input.cantidadn').change(function(){
		cantidad = $(this).val();
		tipo = $(this).parents('tr').find('.tipon').val();
	  factor = $(this).parents('tr').find('.factorn').val();
		precio = $(this).parents('tr').find('.precion').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		descuento = $(this).parents('tr').find('.dscton').val()==0 ? importe*$(this).parents('tr').find('.porcentajen').val()/100 : $(this).parents('tr').find('.porcentajen').val();
		mtotal =importe-descuento;
		$(this).parents('tr').find('.calmacenn').val(almacenc);
		$(this).parents('tr').find('.importen').val(decimales(mtotal,2));
		stock = $(this).parents('tr').find('.stockn').val();

		if (almacenc>stock && tipo=='B') {
			toastr.error('Stock es mucho menor', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.cantidadn').val(1);
			almacenc=1*factor;
			importe = 1*precio;
			mtotal =importe-descuento;
			$(this).parents('tr').find('.calmacenn').val(almacenc);
			$(this).parents('tr').find('.importen').val(decimales(mtotal,2));
		}
		calcularVenta();
		$('#cizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	});

	$('input.cantidadn').keyup(function(){
		cantidad = $(this).val();
		tipo = $(this).parents('tr').find('.tipon').val();
	  factor = $(this).parents('tr').find('.factorn').val();
		precio = $(this).parents('tr').find('.precion').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		descuento = $(this).parents('tr').find('.dscton').val()==0 ? importe*$(this).parents('tr').find('.porcentajen').val()/100 : $(this).parents('tr').find('.porcentajen').val();
		mtotal =importe-descuento;
		$(this).parents('tr').find('.calmacenn').val(almacenc);
		$(this).parents('tr').find('.importen').val(decimales(mtotal,2));
		stock = $(this).parents('tr').find('.stockn').val();

		if (almacenc>stock && tipo=='B') {
			toastr.error('Stock es mucho menor', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.cantidadn').val(1);
			almacenc=1*factor;
			importe = 1*precio;
			mtotal =importe-descuento;
			$(this).parents('tr').find('.calmacenn').val(almacenc);
			$(this).parents('tr').find('.importen').val(decimales(mtotal,2));
		}
		calcularVenta();
		$('#cizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	});

	$('input.porcentajen').change(function(){
		factor = $(this).parents('tr').find('.factorn').val();
		cantidad = $(this).parents('tr').find('.cantidadn').val();
		precio = $(this).parents('tr').find('.precion').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		descuento = $(this).parents('tr').find('.dscton').val()==0 ? importe*$(this).val()/100 : $(this).val();
		mtotal =importe-descuento;
		$(this).parents('tr').find('.calmacenn').val(almacenc);
		$(this).parents('tr').find('.importen').val(decimales(mtotal,2));

		if ($(this).val()<0) {
			toastr.error('El descuento es negativo', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.porcentajen').val('');
			$(this).parents('tr').find('.importen').val(decimales(importe,2));
		}

		if (descuento>=importe) {
			toastr.error('El descuento es mayor o igual al importe', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.porcentajen').val('');
			$(this).parents('tr').find('.importen').val(decimales(importe,2));
		}
		calcularVenta();
		valor=$('#canjear').prop("checked");
		mostrarDescuento(valor);
		$('#cizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	});

	$('input.porcentajen').keyup(function(){
		factor = $(this).parents('tr').find('.factorn').val();
		cantidad = $(this).parents('tr').find('.cantidadn').val();
		precio = $(this).parents('tr').find('.precion').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		descuento = $(this).parents('tr').find('.dscton').val()==0 ? importe*$(this).val()/100 : $(this).val();
		mtotal =importe-descuento;
		$(this).parents('tr').find('.calmacenn').val(almacenc);
		$(this).parents('tr').find('.importen').val(decimales(mtotal,2));

		if ($(this).val()<0) {
			toastr.error('El descuento es negativo', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.porcentajen').val('');
			$(this).parents('tr').find('.importen').val(decimales(importe,2));
		}

		if (descuento>=importe) {
			toastr.error('El descuento es mayor o igual al importe', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.porcentajen').val('');
			$(this).parents('tr').find('.importen').val(decimales(importe,2));
		}
		calcularVenta();
		valor=$('#canjear').prop("checked");
		mostrarDescuento(valor);
		$('#cizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	});

	$('input.precion').keyup(function(){
		cantidad = $(this).parents('tr').find('.cantidadn').val();
		precio = $(this).val();
		descuento = $(this).parents('tr').find('.porcentajen').val();
		importe = cantidad*precio;
		mtotal =importe-descuento;
		$(this).parents('tr').find('.importen').val(decimales(mtotal,2));
		calcularVenta();
		$('#cizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	});
}

function productoBarrav(event,url,id)
{
	if(event.which === 13){
		event.preventDefault();
		if (id!='') {
			$.post(url,{id},function(responder){
				if (responder=='null') {
					toastr.error('El codigo de barra no existe');
				} else {
					var item=JSON.parse(responder);
					if (item.vsujeta==1) {toastr.info('Este producto necesita receta', '',{"positionClass" : "toast-top-center"});}
					var producto=item.descripcion;
		      if (item.nlaboratorio!='') {producto+=' ['+item.nlaboratorio+']';}
					if (item.stock>0) {
						let almacenc=1;
						let mtotal=decimales(item.pventa*1,2);
						let dscto=item.dscto;
						let escondido=dscto==2 ? 'hidden' : 'number';
						let descuento=dscto==0 ? '%' : 'S/.';
						let estilo=item.edicion==0 ? 'campo' : 'form-control form-control-sm';
						let bloquear=item.edicion==0 ? 'onkeydown="return false"' : '';
						cadena = '<tr>';
						cadena += '<td><input type="hidden" name="idproducto[]" value="'+item.id+'" class="producton"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea><input type="hidden" name="tipo[]" id="tipo[]" value="'+item.tipo+'" class="tipon"><input type="hidden" class="colegiaturan" name="colegiatura[]" value=""><input type="hidden" class="doctorn" name="doctor[]" value=""><input type="hidden" class="pacienten" name="paciente[]" value="">';
						cadena += '<div class="row"><div class="col-sm-2 col-4">';
						if (item.vbonificar==1) {
						cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
						}
						cadena += '</div><div class="col-sm-1 col-4"><input type="text" name="nlote[]" value="" class="campo loten"/><input type="hidden" name="lote[]" value="'+item.lote+'" class="campo"/></div>';
						cadena += '<div class="col-sm-1 col-4"><input type="hidden" class="calmacenn" name="almacenc[]" value="'+almacenc+'"/><input type="hidden" class="factorn" name="factor[]" id="factor[]" value="1"><input type="text" name="unidad[]" value="'+item.umedidav+'" class="campo"/><input type="hidden" name="tafectacion[]" class="tafectacionn" value="'+item.tafectacion+'"/></div>';
						cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="'+item.stock+'" class="stockn"/><input type="number" name="cantidad[]" value="1" min="1" class="form-control form-control-sm cantidadn" onkeypress="return event.keyCode != 13;"/></div>';
						cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="'+item.pventa+'" class="'+estilo+' text-right precion" '+bloquear+'/></div>';
						cadena += '<div class="col-sm-2 col-4"><input type="hidden" class="dscton" name="tdscto[]" id="tdscto[]" value="'+dscto+'"><input type="'+escondido+'" step="0.01" min="0.01" name="dscto[]" value="" class="form-control form-control-sm border border-danger porcentajen" placeholder="('+descuento+')Dscto"/></div>';
						cadena += '<div class="col-sm-2 col-4"><input  type="number" min="0.01" step="0.01" name="importe[]" value="'+mtotal+'" class="campo text-right importen no-spinners"/></div></div></td>';
						cadena += '<td>';
						if (item.vsujeta==1) {
							cadena += '<a href="javascript:void(0)" onclick="mostrarReceta(this)" class="btn btn-success btn-sm py-0 mb-1" title="Receta"><i class="fa fa-file"></i></a><br>';
						}
						cadena += '<a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
						cadena +='</td>';
						cadena +='</tr>';
					  var tr = $(cadena).prependTo("#grilla");

					  // Ajustar la altura del textarea recién agregado
					  var textarea = tr.find('textarea[name="descripcion[]"]');
				  	textarea.height(textarea[0].scrollHeight - 4);
						fveliminar();
						calcularv();
						calcularVenta();
					}else{
						toastr.error('El producto no tiene stock para la venta');
					}
				}
			});
		} else {
			let durl=url.replace('busCodigobarra','busProductos');
			productoNombrev(durl,id);
		}
		$('#bproducto').val('');
		$('#codbarra').val('');
		$('#codbarra').focus();
	}
}

function fveliminar()
{
	$("a.elimina").click(function(){
		$(this).parents("tr").fadeOut("normal", function(){
			$(this).remove();
			calcularVenta();
			$('#cizipay').val('');
			$('#mizipay').val('');
			$('#pagar').val('');
			toastr.error('El producto fue eliminado');
		})
	});
};

function limpiarBuscadorv(url)
{
	$('#bproducto').val('');
	productoNombrev(url,'a');
}

function mostrarReceta(tabla)
{
	$('#busreceta').modal('show');
	let fila = $(tabla).closest('tr').index(); // Obtenemos el índice de la fila
  let colegiatura = $(tabla).closest('tr').find('.colegiaturan').val();
  let doctor = $(tabla).closest('tr').find('.doctorn').val();
  let paciente = $(tabla).closest('tr').find('.pacienten').val();

	document.getElementById('mcolegiatura').value = colegiatura;
	document.getElementById('mdoctor').value = doctor;
	document.getElementById('mpaciente').value = paciente;
	document.getElementById('mtabla').value = fila;
}

function appreceta()
{
	var colegiatura=$('#mcolegiatura').val();
	var doctor=$('#mdoctor').val();
	var paciente=$('#mpaciente').val();
	var fila=$('#mtabla').val();

	$('#grilla tr').eq(fila).find('.colegiaturan').val(colegiatura);
	$('#grilla tr').eq(fila).find('.doctorn').val(doctor);
	$('#grilla tr').eq(fila).find('.pacienten').val(paciente);
	$('#busreceta').modal('hide');
}

function pagoCredito(valor)
{
	if (valor.checked) {
		document.getElementById('contado').style.display='none';
		var contador = document.getElementsByName("metodos").length;
		for(i=0;i<contador;i++){
			let j=i+1
			$('#metodo'+j+'').remove();
		}
		document.getElementById('cvuelto').style.display='none';

		document.getElementById('credito').style.display='block';
		$("#pcuota").prop('required',true);
		$("#cuotas").prop('required',true);
		$("#mcuota").prop('required',true);

    document.getElementById("pcuota").value = "Mensual";
    document.getElementById("cuotas").value = "1";
    document.getElementById("mcuota").value = $('#totalg').val();
	}
	else {
		document.getElementById('contado').style.display='block';
		document.getElementById('cvuelto').style.display='block';

		document.getElementById('credito').style.display='none';
		$("#pcuota").removeAttr('required',true);
		$("#cuotas").removeAttr('required',true);
		$("#mcuota").removeAttr('required',true);
		$("#pcuota").val('');
		$("#cuotas").val('');
		$("#mcuota").val('');
	}
}

function pagoIzipay(valor)
{
	if (valor.checked) {
		document.getElementById('cizipay').style.display='block';
		$('#pizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	}
	else {
		document.getElementById('cizipay').style.display='none';
		$('#pizipay').val('');
		$('#mizipay').val('');
		$('#pagar').val('');
	}
}

function apppagos(url)
{
	document.getElementsByName("monto[]")[0].removeAttribute("readonly");
	$('.metodo').html('');
	$.post(url,function(data){
		var c = JSON.parse(data);
		$.each(c, function(i,item){
			$('.metodo').append('<option value="'+item.id+'">'+item.descripcion+'</option>');
		});
	});

	var contador = document.getElementsByName("mpago[]").length;
	cadena = '<div class="form-group row mb-1" id="metodo'+contador+'" name="metodos">';
  cadena += '<label for="mpago'+contador+'" class="col-sm-2 col-6 col-form-label">Medio Pago*</label>';
  cadena += '<div class="col-sm-3 col-6">';
  cadena += '<select name="mpago[]" id="mpago'+contador+'" class="form-control form-control-sm metodo">';
  cadena += '</select>';
  cadena += '</div>';
  cadena += '<label for="monto'+contador+'" class="col-sm-1 col-6 col-form-label">Monto</label>';
  cadena += '<div class="col-sm-2 col-6">';
  cadena += '<input name="monto[]" type="text" id="monto'+contador+'" class="form-control form-control-sm" value="" placeholder="Monto"/>';
  cadena += '</div>';
  cadena += '<label for="documento'+contador+'" class="col-sm-1 col-6 col-form-label">Doc.</label>';
  cadena += '<div class="col-sm-2 col-6">';
  cadena += '<input name="documento[]" type="text" id="documento'+contador+'" class="form-control form-control-sm" value="" placeholder="Doc sustenta"/>';
  cadena += '</div>';
  cadena += '<div class="col-sm-1 col-2">';
  cadena += '<button type="button" class="btn btn-danger btn-sm" onclick="borrarp(\'metodo'+contador+'\');"><i class="fa fa-trash"></i></button>';
  cadena += '</div>';
  cadena += '</div>';
	$("#contado").append(cadena);
}

function borrarp(id)
{
	$('#'+id+'').remove();
	contador=document.getElementsByName("monto[]").length;
	if (contador==1) {
		document.getElementsByName("monto[]")[0].setAttribute("readonly", "readonly");
		document.getElementById('monto0').value=$('#totalg').val();
	}
}

function envioVenta(url)
{
	event.preventDefault();

	Swal.fire({
		title: "Desea generar la venta?",
		text: "No podras revertir esto!",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: "Si, guardar esto!",
		cancelButtonText: "No, cancelar!",
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
	}).then((result) => {
		if (result.value) {
			document.getElementById("btsubmit").value = "Enviando...";
			document.getElementById("btsubmit").disabled = true;

			$.ajax({
				url: url,
				type: 'POST',
				async:true,
				data: $('#form1').serialize(),
				success: function(responder) {
					console.log(responder);
					var dato = JSON.parse(responder);
					if (dato.impresion!='') {
						window.open(dato.impresion,'_blank');
						window.location.href=dato.url;
						toastr.success(dato.mensaje);
					} else {
						toastr.error(dato.mensaje);
						setTimeout(function () {
							document.getElementById("btsubmit").value = "Guardar";
							document.getElementById("btsubmit").disabled = false;
					    }, 800);
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
	})
}

function bonificacion(valor)
{
  let fila = $(valor).closest('tr').index(); // Obtenemos el índice de la fila
	if (valor.checked) {
		$('#grilla tr').eq(fila).find('.tafectacionn').val(15);
		$('#grilla tr').eq(fila).addClass('table-primary');
  }
  else {
		$('#grilla tr').eq(fila).find('.tafectacionn').val(10);
		$('#grilla tr').eq(fila).removeClass('table-primary');
  }
  calcularVenta();
}

function applibre(vbonificar)
{
	cadena = '<tr>';
	cadena += '<td>';
	cadena += '<textarea name="descripcion[]" class="form-control form-control-sm" rows="3"></textarea><input type="hidden" name="idproducto[]" value="0" class="producton"/><input type="hidden" class="tipon" name="tipo[]" value="S"><input type="hidden" class="colegiaturan" name="colegiatura[]" value=""><input type="hidden" class="doctorn" name="doctor[]" value=""><input type="hidden" class="pacienten" name="paciente[]" value="">';
	cadena += '<div class="row"><div class="col-sm-2 col-4">';
	if (vbonificar==1) {
	cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
	}
	cadena += '</div><div class="col-sm-1 col-4"><input type="text" name="nlote[]" value="" class="campo loten"/><input type="hidden" name="lote[]" value="0" class="campo"/></div>';
	cadena += '<div class="col-sm-1 col-4"><input type="hidden" class="factorn" name="factor[]" value="1"><input type="text" name="unidad[]" value="ZZ" class="campo"/><input type="hidden" name="tafectacion[]" class="tafectacionn" value="10"/></div>';
	cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="0" class="stockn"/><input type="number" name="cantidad[]" value="1" min="1" class="form-control form-control-sm cantidadn" onkeypress="return event.keyCode != 13;"/><input type="hidden" class="calmacenn" name="almacenc[]" value="1"/></div>';
	cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="1" class="form-control form-control-sm text-right precion"/></div>';
	cadena += '<div class="col-sm-2 col-4"><input type="hidden" class="dscton" name="tdscto[]" value="1"><input type="number" step="0.01" min="0.01" name="dscto[]" value="" class="campo porcentajen"/></div>';
	cadena += '<div class="col-sm-2 col-4"><input type="number" min="0.01" step="0.01" name="importe[]" value="1" class="campo text-right importen no-spinners" onkeydown="return false"/></div></div>';
	cadena += '</td>';
	cadena += '<td>';
	cadena += '<a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
	cadena += '</td>';
	cadena += "</tr>";
	$("#grilla").prepend(cadena);
	fveliminar();
	calcularv();
	calcularVenta();
}
/*==========================================================================================================================
=                                              venta en lotes                                                              =
==========================================================================================================================*/
function appvental()
{
	var id=$('#mcodigo').val();
	var producto=$('#mdescripcion').val();
	var medida=$('#mmedida').val().split('|');
	var afectacion=$('#mafectacion').val();
	var cantidad=$('#munidades').val();
	var precio=$('#mcosto').val();
	var total=$('#mtotal').val();
	var lote=$('#clote').val();
	var stock=parseFloat($('#mstock').val());
	var restringir=$('#mactivar').val();
	var factor=$('#mfactor').val();
	var almacenc=$('#mcantidad').val();
	var centregar=parseFloat($('#centregar').val());
	var tipo=$('#mtipo').val();
	var receta=$('#mreceta').val();
	var dscto=$('#mdscto').val();
	var escondido=dscto==2 ? 'hidden' : 'number';
	var descuento=dscto==0 ? '%' : 'S/.';
	var edicion=$('#medicion').val();
	var estilo=$('#medicion').val()==0 ? 'campo' : 'form-control form-control-sm';
	var bloquear=edicion==0 ? 'onkeydown="return false"' : '';
	var vbonificar=$('#mbonificar').val();

	if (restringir==1) {
		if(id!='' && producto!='' && cantidad!='' && precio!='' && lote!='' && almacenc<=stock && centregar>=almacenc){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+id+'" class="producton"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea><input type="hidden" name="tipo[]" id="tipo[]" class="tipon" value="'+tipo+'"><input type="hidden" class="colegiaturan" name="colegiatura[]" value=""><input type="hidden" class="doctorn" name="doctor[]" value=""><input type="hidden" class="pacienten" name="paciente[]" value="">';
			cadena += '<div class="row"><div class="col-sm-2 col-4">';
			if (vbonificar==1) {
			cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
			}
			cadena += '</div><div class="col-sm-1 col-4"><input type="text" name="nlote[]" value="'+lote+'" class="campo loten"/><input type="hidden" name="lote[]" value="'+restringir+'" class="campo"/></div>';
			cadena += '<div class="col-sm-1 col-4"><input type="hidden" class="factorn" name="factor[]" id="factor[]" value="'+factor+'"><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/><input type="hidden" name="tafectacion[]" class="tafectacionn" value="'+afectacion+'" class="campo"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="'+stock+'" class="stockn"/><input type="number" name="cantidad[]" value="'+cantidad+'" min="1" class="campo cantidadn" onkeypress="return event.keyCode != 13;"/><input type="hidden" class="calmacenn" name="almacenc[]" value="'+almacenc+'"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="'+precio+'" class="'+estilo+' text-right precion" '+bloquear+'/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="hidden" class="dscton" name="tdscto[]" id="tdscto[]" value="'+dscto+'"><input type="'+escondido+'" step="0.01" min="0.01" name="dscto[]" value="" class="form-control form-control-sm border border-danger porcentajen" placeholder="('+descuento+')Dscto"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="number" min="0.01" step="0.01" name="importe[]" value="'+decimales(total,2)+'" class="campo text-right importen no-spinners"/></div></div></td>';
			cadena += '<td>';
			if (receta==1) {
				cadena += '<a href="javascript:void(0)" onclick="mostrarReceta(this)" class="btn btn-success btn-sm py-0 mb-1" title="Receta"><i class="fa fa-file"></i></a><br>';
			}
			cadena += '<a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
			cadena += '</td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
    		$('#busdatos').modal('hide');
		}else{
			$('#mensajeerror').html('<strong class="text-danger">Falta ingresar datos del producto</strong>');
			if (cantidad=='') {document.getElementById("munidades").className += " is-invalid";}
			if (precio=='') {document.getElementById("mcosto").className += " is-invalid";}
			if (lote=='') {$('#mensajeerror').append('<strong class="text-danger"><br>Seleccione un lote</strong>');}
			if (almacenc>stock) {$('#mensajeerror').append('<strong class="text-danger"><br>EL stock actual en mucho menor al que desea vender</strong>');}
			if (centregar<almacenc) {$('#mensajeerror').append('<strong class="text-danger"><br>La cantidad de lote seleccionada es mucho menor</strong>');}
		}
	}else{
		if(id!='' && producto!='' && cantidad!='' && precio!='' && almacenc<=stock){
			cadena = '<tr>';
			cadena += '<td><input type="hidden" name="idproducto[]" value="'+id+'" class="producton"/><textarea name="descripcion[]" class="campo">'+producto+'</textarea><input type="hidden" name="tipo[]" id="tipo[]" class="tipon" value="'+tipo+'"><input type="hidden" class="colegiaturan" name="colegiatura[]" value=""><input type="hidden" class="doctorn" name="doctor[]" value=""><input type="hidden" class="pacienten" name="paciente[]" value="">';
			cadena += '<div class="row"><div class="col-sm-2 col-4">';
			if (vbonificar==1) {
			cadena += '<div class="form-check"><input type="checkbox" class="form-check-input" id="bonificacion[]" name="bonificacion[]" onclick="bonificacion(this)"><label class="form-check-label">Bonificacion</label></div>';
			}
			cadena += '</div><div class="col-sm-1 col-4"><input type="text" name="nlote[]" value="'+lote+'" class="campo loten"/><input type="hidden" name="lote[]" value="'+restringir+'" class="campo"/></div>';
			cadena += '<div class="col-sm-1 col-4"><input type="hidden" class="factorn" name="factor[]" id="factor[]" value="'+factor+'"><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/><input type="hidden" name="tafectacion[]" class="tafectacionn" value="'+afectacion+'"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="'+stock+'" class="stockn"/><input type="number" name="cantidad[]" value="'+cantidad+'" min="1" class="campo cantidadn" onkeypress="return event.keyCode != 13;"/><input type="hidden" class="calmacenn" name="almacenc[]" value="'+almacenc+'"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="'+precio+'" class="'+estilo+' text-right precion" '+bloquear+'/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="hidden" class="dscton" name="tdscto[]" id="tdscto[]" value="'+dscto+'"><input type="'+escondido+'" step="0.01" min="0.01" name="dscto[]" value="" class="form-control form-control-sm border border-danger porcentajen" placeholder="('+descuento+')Dscto"/></div>';
			cadena += '<div class="col-sm-2 col-4"><input type="number" min="0.01" step="0.01" name="importe[]" value="'+decimales(total,2)+'" class="campo text-right importen no-spinners"/></div></div></td>';
			cadena += '<td>';
			if (receta==1) {
				cadena += '<a href="javascript:void(0)" onclick="mostrarReceta(this)" class="btn btn-success btn-sm py-0 mb-1" title="Receta"><i class="fa fa-file"></i></a><br>';
			}
			cadena += '<a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a>';
			cadena += '</td>';
			cadena += "</tr>";
		  var tr = $(cadena).prependTo("#grilla");

		  // Ajustar la altura del textarea recién agregado
		  var textarea = tr.find('textarea[name="descripcion[]"]');
	  	textarea.height(textarea[0].scrollHeight - 4);
    		$('#busdatos').modal('hide');
		}else{
			if (almacenc>stock) {$('#mensajeerror').html('<strong class="text-danger">EL stock actual en mucho menor al que desea vender</strong>');}
		}
	}
	fveliminar();
	calcularv();
	calcularVenta();
};

function limpiarLote()
{
	$('.nlote').prop('checked', false);
	$('#clote').val('');
	$('#slote').val('');
	$('#centregar').val(0);
}

/*==========================================================================================================================
=                                              punto de venta                                                              =
==========================================================================================================================*/
function mcategoria(url,id)
{
	$('#bproducto').val('');
	$('#tblproducto').html('');
	$.post(url,{id},function(data){
		var datos=JSON.parse(data);
		cadena ='';
		$.each(datos, function(i,item){
			iurl=url.replace('busCategoria','busInformacion');
			purl=url.replace('busCategoria','busPrecios');
			var nproducto=item.descripcion;
      if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}

			cadena +='<div class="col-sm-3 col-6">';
			cadena +='<div class="card mb-2">';
			cadena +='<div class="card-body p-2 position-relative">';
			if (item.stock<1) {
			cadena +='<a href="javascript:void(0)" style="pointer-events: none; cursor: not-allowed;">';
			} else {
			cadena +='<a href="javascript:void(0)" onclick="appvrapido(\''+item.id+'\',\`'+nproducto+'`\,\''+item.umedidav+'\',\'1\',\''+item.tafectacion+'\',\''+item.pventa+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.tipo+'\',\''+item.edicion+'\');">';
			}
			cadena +='<div class="image-container position-relative">';
			cadena +='<img src="'+item.ruta+'" class="img-thumbail img-fluid">';
			if (item.stock<1) {
				cadena +='<img src="'+item.imagen+'" class="img-overlay position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">';
			}
			cadena +='<div class="overlay-text position-absolute w-100 text-center" style="bottom: 0; left: 0; background: rgba(0, 0, 0, 0.5); color: #fff;">';
			cadena += ''+item.descripcion+'';
			cadena +='</div>';
			cadena +='</div>';
			cadena +='</a>';
			cadena +='</div>';
			cadena +='<div class="card-footer p-2">';
			cadena +='<h5 class="text-right my-0">';
			cadena +='<a href="javascript:void(0)" onclick="mostrarModal(\''+iurl+'/'+item.id+'/pos\',\'bdatos\',\'Informacion Producto\')" class="btn btn-primary btn-sm py-0 float-left" title="Informacion Producto" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-search"> '+item.stock+'</i></a>';
			if (item.factor>1) {
				cadena +='<a href="javascript:void(0)" onclick="mostrarModal(\''+purl+'/'+item.id+'\',\'bdatos\',\'Precios Disponibles\')" class="btn btn-info btn-sm py-0 float-left ml-1" title="Precios Disponibles" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-tag"></i></a>';
			}
			cadena +=item.pventa+'</h5>';
			cadena +='</div>';
			cadena +='</div>';
			cadena +='</div>';
			$('#tblproducto').html(cadena);

			// Inicializar el tooltip después de agregar el contenido dinámico
			$('[data-toggle="tooltip"]').tooltip();
		});
	});
}

function productoNombrep(url,id)
{
	$('#tblproducto').html('');

	const data = new FormData();
	data.append('id', id);

	fetch(url, {
		method: 'POST',
		body: data
	})
	.then(function(response) {
		if(response.ok) {
			return response.json();
		} else {
			throw "Error en la llamada Ajax";
		}
	})
	.then((datos) => {
		if (datos.length==0)
		{
			$('#tblproducto').html('<strong>No hay datos de la busqueda</strong>');
		}
		else
		{
			var cadena='';
			$.each(datos, function(i,item){
				iurl=url.replace('busProductos','busInformacion');
				purl=url.replace('busProductos','busPrecios');

				var nproducto=item.descripcion;
	      if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}

				cadena +='<div class="col-sm-3 col-6">';
				cadena +='<div class="card mb-2">';
				cadena +='<div class="card-body p-2 position-relative">';
				if (item.stock<1) {
				cadena +='<a href="javascript:void(0)" style="pointer-events: none; cursor: not-allowed;">';
				} else {
				cadena +='<a href="javascript:void(0)" onclick="appvrapido(\''+item.id+'\',\`'+nproducto+'\`,\''+item.umedidav+'\',\'1\',\''+item.tafectacion+'\',\''+item.pventa+'\',\''+item.lote+'\',\''+item.stock+'\',\''+item.tipo+'\',\''+item.edicion+'\');">';
				}
				cadena +='<div class="image-container position-relative">';
				cadena +='<img src="'+item.ruta+'" class="img-thumbail img-fluid">';
				if (item.stock<1) {
					cadena +='<img src="'+item.imagen+'" class="img-overlay position-absolute" style="top: 0; left: 0; width: 100%; height: 100%; z-index: 2;">';
				}
				cadena +='<div class="overlay-text position-absolute w-100 text-center" style="bottom: 0; left: 0; background: rgba(0, 0, 0, 0.5); color: #fff;">';
				cadena += ''+item.descripcion+'';
				cadena +='</div>';
				cadena +='</div>';
				cadena +='</a>';
				cadena +='</div>';
				cadena +='<div class="card-footer p-2">';
				cadena +='<h5 class="text-right my-0">';
				cadena +='<a href="javascript:void(0)" onclick="mostrarModal(\''+iurl+'/'+item.id+'/pos\',\'bdatos\',\'Informacion Producto\')" class="btn btn-primary btn-sm py-0 float-left" title="Informacion Producto" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-search"> '+item.stock+'</i></a>';
				if (item.factor>1) {
					cadena +='<a href="javascript:void(0)" onclick="mostrarModal(\''+purl+'/'+item.id+'\',\'bdatos\',\'Precios Disponibles\')" class="btn btn-info btn-sm py-0 float-left ml-1" title="Precios Disponibles" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-tag"></i></a>';
				}
				cadena +=item.pventa+'</h5>';
				cadena +='</div>';
				cadena +='</div>';
				cadena +='</div>';
				$('#tblproducto').html(cadena);
			});
		}
	})
	.catch(function(err) {
		console.log(err);
	});
}

function appvrapido(id,producto,unidad,factor,afectacion,precio,lote,stock,tipo,edicion)
{
	let almacenc=factor*1;
	let mtotal=decimales(precio*1,2);
	let estilo=edicion==0 ? 'campo' : 'form-control form-control-sm';
	let bloquear=edicion==0 ? 'onkeydown="return false"' : '';
	cadena = '<tr>';
	cadena += '<td>';
	cadena += '<input type="hidden" name="descripcion[]" id="descripcion[]" value="'+producto+'"><input type="hidden" name="idproducto[]" value="'+id+'" class="productop"/><input type="hidden" class="tipop" name="tipo[]" id="tipo[]" value="'+tipo+'">'+producto+'';
  cadena += '<div class="row">';
    cadena += '<div class="col-sm-6"><input type="hidden" class="factorp" name="factor[]" id="factor[]" value="'+factor+'"><input type="hidden" name="tafectacion[]" class="tafectacionp" value="'+afectacion+'"/><input type="hidden" name="lote[]" value="'+lote+'" class="campo"/><input type="text" name="unidad[]" value="'+unidad+'" class="campo"/></div>';
	  cadena += '<div class="col-sm-2 col-4"><input type="hidden" name="stock[]" value="'+stock+'" class="stockp"/><input type="number" name="cantidad[]" value="1" min="0.01" step="0.01" class="form-control form-control-sm cantidadp" onkeypress="return event.keyCode != 13;"/><input type="hidden" class="calmacenp" name="almacenc[]" value="'+almacenc+'"/></div>';
  	cadena += '<div class="col-sm-2 col-4"><input type="text" name="precio[]" value="'+precio+'" class="'+estilo+' text-right preciop" '+bloquear+'/></div>';
  	cadena += '<div class="col-sm-2 col-4"><h5 class="my-0"><input type="number" min="0.01" step="0.01" name="importe[]" value="'+mtotal+'" class="campo text-right importep no-spinners"/></h5></div>';
  cadena += '</div>';
  cadena += '</td>';
  cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
	cadena += "</tr>";
	$('#grilla').prepend(cadena);
	fveliminar();
	calcularp();
	calcularVenta();
}

function calcularp()
{
	$('input.cantidadp').change(function(){
		cantidad = $(this).val();
		tipo = $(this).parents('tr').find('.tipop').val();
	  factor = $(this).parents('tr').find('.factorp').val();
		precio = $(this).parents('tr').find('.preciop').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		$(this).parents('tr').find('.calmacenp').val(almacenc);
		$(this).parents('tr').find('.importep').val(decimales(importe,2));
		stock = $(this).parents('tr').find('.stockp').val();

		if (almacenc>stock && tipo=='B') {
			toastr.error('Stock es mucho menor', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.cantidadp').val(1);
			almacenc=1*factor;
			importe = 1*precio;
			$(this).parents('tr').find('.calmacenp').val(almacenc);
			$(this).parents('tr').find('.importep').val(decimales(importe,2));
		}
		calcularVenta();
	});

	$('input.cantidadp').keyup(function(){
		cantidad = $(this).val();
		tipo = $(this).parents('tr').find('.tipop').val();
	  factor = $(this).parents('tr').find('.factorp').val();
		precio = $(this).parents('tr').find('.preciop').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		$(this).parents('tr').find('.calmacenp').val(almacenc);
		$(this).parents('tr').find('.importep').val(decimales(importe,2));
		stock = $(this).parents('tr').find('.stockp').val();

		if (almacenc>stock && tipo=='B') {
			toastr.error('Stock es mucho menor', '',{"positionClass" : "toast-top-center"});
			$(this).parents('tr').find('.cantidadp').val(1);
			almacenc=1*factor;
			importe = 1*precio;
			$(this).parents('tr').find('.calmacenp').val(almacenc);
			$(this).parents('tr').find('.importep').val(decimales(importe,2));
		}
		calcularVenta();
	});

	$('input.preciop').keyup(function(){
		cantidad = $(this).parents('tr').find('.cantidadp').val();
		precio = $(this).val();
		importe = cantidad*precio;
		$(this).parents('tr').find('.importep').val(decimales(importe,2));
		calcularVenta();
	});
}

function mostrarPagos(url)
{
    // Obtener las colecciones de elementos por nombre
    const medios = document.getElementsByName('medios[]');
    const montos = document.getElementsByName('montos[]');
    const documentos = document.getElementsByName('referencia[]');

    $('#busdatos').modal({
        backdrop: 'static',
        keyboard: false,
        show: true // Muestra el modal inmediatamente
    });
    $("#modalTitle").html('Pagos');
    $("#bdatos").html('');

    $.post(url, function(data) {
        var c = JSON.parse(data);

        // Iterar sobre los elementos (asumiendo que todos tienen el mismo número de elementos)
        for (let i = 0; i < medios.length; i++) {
            const medio = medios[i].value;
            const monto = montos[i].value;
            const documento = documentos[i].value;

            let cadena = '<div class="form-group row mb-1" id="metodo'+i+'">';
            cadena += '<label for="mpago'+i+'" class="col-sm-2 col-6 col-form-label">Medio Pago*</label>';
            cadena += '<div class="col-sm-3 col-6">';
            cadena += '<select name="mpago[]" id="mpago'+i+'" class="form-control form-control-sm">';
            cadena += '</select>';
            cadena += '</div>';
            cadena += '<label for="monto'+i+'" class="col-sm-1 col-6 col-form-label">Monto</label>';
            cadena += '<div class="col-sm-2 col-6">';
            cadena += '<input name="monto[]" type="text" id="monto'+i+'" class="form-control form-control-sm" value="'+monto+'" placeholder="Monto"/>';
            cadena += '</div>';
            cadena += '<label for="documento'+i+'" class="col-sm-1 col-6 col-form-label">Doc.</label>';
            cadena += '<div class="col-sm-2 col-6">';
            cadena += '<input name="documento[]" type="text" id="documento'+i+'" class="form-control form-control-sm" value="'+documento+'" placeholder="Doc sustenta"/>';
            cadena += '</div>';
            if (i > 0) {
                cadena += '<div class="col-sm-1 col-2">';
                cadena += '<button type="button" class="btn btn-danger btn-sm" onclick="borrarb(\'metodo'+i+'\');"><i class="fa fa-trash"></i></button>';
                cadena += '</div>';
            } else {
                cadena += '<div class="col-sm-1 col-2">';
                cadena += '<button type="button" class="btn btn-info btn-sm" onclick="agregarPagos(\''+url+'\');"><i class="fa fa-plus"></i></button>';
                cadena += '</div>';
            }
            cadena += '</div>';

            $("#bdatos").append(cadena);

            // Añadir las opciones al select correspondiente
            $.each(c, function(index, item) {
                let option = document.createElement('option');
                option.value = item.id + '/' + item.descripcion;
                option.text = item.descripcion;
                document.getElementById('mpago' + i).appendChild(option);
            });

            // Establecer el valor del select
            document.getElementById('mpago' + i).value = medio;
        }

        let cadenas = '<div id="contado"></div>';
        cadenas += '<div class="form-group row mb-0">';
        cadenas += '<div class="col-sm-12 text-right">';
        cadenas += '<button type="button" class="btn btn-outline-danger btn-sm" class="close" data-dismiss="modal" aria-label="Close" onclick="cambiarPagos();">CERRAR</button>';
        cadenas += '</div>';
        cadenas += '</div>';
        $("#bdatos").append(cadenas);
    });
}

function agregarPagos(url)
{
	$.post(url,function(data){
		var c = JSON.parse(data);

		var contador = document.getElementsByName("mpago[]").length;
		contador++; // Incrementa el contador
		cadena = '<div class="form-group row mb-1" id="metodo'+contador+'">';
	  cadena += '<label for="mpago'+contador+'" class="col-sm-2 col-6 col-form-label">Medio Pago*</label>';
	  cadena += '<div class="col-sm-3 col-6">';
	  cadena += '<select name="mpago[]" id="mpago'+contador+'" class="form-control form-control-sm">';
	  cadena += '</select>';
	  cadena += '</div>';
	  cadena += '<label for="monto'+contador+'" class="col-sm-1 col-6 col-form-label">Monto</label>';
	  cadena += '<div class="col-sm-2 col-6">';
	  cadena += '<input name="monto[]" type="text" id="monto'+contador+'" class="form-control form-control-sm" value="0" placeholder="Monto"/>';
	  cadena += '</div>';
	  cadena += '<label for="documento'+contador+'" class="col-sm-1 col-6 col-form-label">Doc.</label>';
	  cadena += '<div class="col-sm-2 col-6">';
	  cadena += '<input name="documento[]" type="text" id="documento'+contador+'" class="form-control form-control-sm" value="" placeholder="Doc sustenta"/>';
	  cadena += '</div>';
	  cadena += '<div class="col-sm-1 col-2">';
	  cadena += '<button type="button" class="btn btn-danger btn-sm" onclick="borrarb(\'metodo'+contador+'\');"><i class="fa fa-trash"></i></button>';
	  cadena += '</div>';
	  cadena += '</div>';
		$("#contado").append(cadena);

		// Añadir las opciones al select correspondiente
	  $.each(c, function(index, item) {
	      let option = document.createElement('option');
	      option.value = item.id + '/' + item.descripcion;
	      option.text = item.descripcion;
	      document.getElementById('mpago' + contador).appendChild(option);
	  });
	});
}

function cambiarPagos()
{
	$('#tblpagos').html('');
	const medios = document.getElementsByName('mpago[]');
  const montos = document.getElementsByName('monto[]');
  const documentos = document.getElementsByName('documento[]');

	var cadena='';
  for (let i = 0; i < medios.length; i++) {
		const medio = medios[i].value;
		const nmedio = medios[i].value.split('/');
		const monto = montos[i].value;
		const documento = documentos[i].value;

		agregaid= i<1 ? 'id="monto1"':'';
		cadena += '<tr>';
		cadena += '<td>'+nmedio[1]+'<input type="hidden" name="medios[]" value="'+medio+'"></td>';
		cadena += '<td><h5 class="my-0"><input name="montos[]" '+agregaid+' type="text" class="campo" value="'+monto+'"/></h5><input name="referencia[]" type="hidden" value="'+documento+'"/></td>';
		cadena += '</tr>';
		$('#tblpagos').html(cadena);
	}
}

function envioPos(url)
{
	event.preventDefault();

	Swal.fire({
		title: "Desea generar la venta?",
		text: "No podras revertir esto!",
		type: "warning",
		showCancelButton: true,
		confirmButtonText: "Si, guardar esto!",
		cancelButtonText: "No, cancelar!",
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
	}).then((result) => {
		if (result.value) {
			document.getElementById("btsubmit").value = "Enviando...";
			document.getElementById("btsubmit").disabled = true;

			$.ajax({
				url: url,
				type: 'POST',
				async:true,
				data: $('#form1').serialize(),
				success: function(responder) {
					//console.log(responder);
					var dato = JSON.parse(responder);
					if (dato.impresion!='') {
						document.getElementById("form1").reset();
						$('#grilla').html('');
						document.getElementById("btsubmit").value = "Guardar";
						document.getElementById("btsubmit").disabled = false;

						let durl=url.replace('pos/guardar','producto/busProductos');
						productoNombrep(durl,'a');

						mostrarModal(dato.impresion,'bdatos','Impresion');
					} else {
						toastr.error(dato.mensaje);
						setTimeout(function () {
							document.getElementById("btsubmit").value = "Guardar";
							document.getElementById("btsubmit").disabled = false;
					    }, 800);
					}
				},
				error: function(error) {
					console.log(error);
				}
			});
		}
	})
}

/*==========================================================================================================================
=                                            funciones ncredito                                                            =
==========================================================================================================================*/
function calculart()
{
	$('input.cantidadv').keyup(function(){
		cantidad = $(this).val();
	  factor=$(this).parents('tr').find('.factorv').val();
		precio=$(this).parents('tr').find('.preciov').val();
		almacenc=cantidad*factor;
		importe = cantidad*precio;
		$(this).parents('tr').find('.calmacenv').val(almacenc);
		$(this).parents('tr').find('.importev').val(decimales(importe,2));
		calcularNota();
	});

	$('input.preciov').keyup(function(){
		cantidad = $(this).parents('tr').find('.cantidadv').val();
	  factor=$(this).parents('tr').find('.factorv').val();
		precio = $(this).val();
		importe = cantidad*precio;
		$(this).parents('tr').find('.importev').val(decimales(importe,2));
		calcularNota();
	});
}

function fteliminar()
{
	$("a.eliminat").click(function(){
		$(this).parents("tr").fadeOut("normal", function(){
			$(this).remove();
			calcularNota();
			toastr.error('El producto fue eliminado');
		})
	});
};

/*=======================================================================================================================
=                                                    funciones despacho                                                 =
=======================================================================================================================*/
function traslado(valor)
{
	var m1l = document.getElementById("m1l").checked;
	if (valor=='01') {
		if (!m1l) {
			document.getElementById('tpublico').style.display='block';
			document.getElementById('documentot').value = 6;
			document.getElementById('ndocumentot').value = '';
			document.getElementById('nombrest').value = '';
		}

		document.getElementById('tprivado').style.display='none';
		document.getElementById('documentoc').value = 0;
		document.getElementById('ndocumentoc').value = '-';
		document.getElementById('nombresc').value = '-';
		document.getElementById('placa').value = '-';
		document.getElementById('licencia').value = '-';
	}
	else {
		document.getElementById('tpublico').style.display='none';
		document.getElementById('documentot').value = 0;
		document.getElementById('ndocumentot').value = '-';
		document.getElementById('nombrest').value = '-';

		if (!m1l) {
			document.getElementById('tprivado').style.display='block';
			document.getElementById('documentoc').value = 1;
			document.getElementById('ndocumentoc').value = '';
			document.getElementById('nombresc').value = '';
			document.getElementById('placa').value = '';
			document.getElementById('licencia').value = '';
		}
	}
}

function destinatarioNombre(url,id)
{
	$('#tblbuscador').html('');
	$.ajax({
		url: url,
		type: 'POST',
		async:true,
		data: { id : id },
		success: function(responder) {
			var datos=JSON.parse(responder);
			if (datos.length==0)
			{
				$('#grcliente').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
					cadena += '<tr>';
					cadena += '<td>'+item.nombres+'</td>';
					cadena += '<td>'+item.documento+'</td>';
					cadena += '<td>'+item.direccion+' - <i>'+item.ndistrito+'</i></td>';
					cadena += '<td><a href="javascript:void(0)" class="btn btn-success btn-sm py-0" onclick="datosDestinatario(\''+item.id+'\', \`'+item.nombres+'\`,\''+item.iddepartamento+'\',\''+item.idprovincia+'\',\''+item.iddistrito+'\',\''+item.direccion+'\',\''+url+'\');" title="Click para seleccionar"><i class="fa fa-check-square"></i></a></td>';
					cadena += '</tr>';
					$('#tblbuscador').html(cadena);
				});
			}
		},

		error: function(error) {
			console.log(error);
		}
	});
}

function datosDestinatario(cod,nom,dep,pro,dis,dir,url)
{
	document.getElementById('idcliente').value = cod;
	document.getElementById('cliente').value = nom;
	let purl=url.replace('cliente/busCliente','establecimiento/busProvincia');
	let durl=url.replace('cliente/busCliente','establecimiento/busDistrito');
	document.getElementById('departamentoe').value = dep;
	bubicaciones(purl,dep,'provinciae',pro);
	bubicaciones(durl,pro,'distritoe',dis);
	document.getElementById('direccione').value = dir;
	$('#busdatos').modal('hide');
}

function datosPrivado(doc,num,nom,lic,pla)
{
	document.getElementById('documentoc').value = doc;
	document.getElementById('ndocumentoc').value = num;
	document.getElementById('nombresc').value = nom;
	document.getElementById('licencia').value = lic;
	document.getElementById('placa').value = pla;
	$('#mdconductor').modal('hide');
}

function datosPublico(doc,num,nom)
{
	document.getElementById('documentot').value = doc;
	document.getElementById('ndocumentot').value = num;
	document.getElementById('nombrest').value = nom;
	$('#mdtransportista').modal('hide');
}

function agregarM1L(valor)
{
	transporte=$('#modot').val();
	if (valor) {
		if (transporte=='01') {
			document.getElementById('tpublico').style.display='none';
		} else {
			document.getElementById('tprivado').style.display='none';
		}

    document.getElementById('ndocumentot').value = '-';
    document.getElementById('nombrest').value = '-';

    document.getElementById('ndocumentoc').value = '-';
    document.getElementById('nombresc').value = '-';
    document.getElementById('placa').value = '-';
    document.getElementById('licencia').value = '-';
	} else {
		if (transporte=='01') {
			document.getElementById('tpublico').style.display='block';
      document.getElementById('ndocumentot').value = '';
      document.getElementById('nombrest').value = '';
		} else {
			document.getElementById('tprivado').style.display='block';
      document.getElementById('ndocumentoc').value = '';
      document.getElementById('nombresc').value = '';
      document.getElementById('placa').value = '';
      document.getElementById('licencia').value = '';
		}
	}
}

$('#busdespacho').on('shown.bs.modal', function () {
    $('#mdescripcion').focus();
})

$('#busdespacho').on('hidden.bs.modal', function (e){
	reset_despacho();
})

function productoNombred(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

        	var nproducto=item.descripcion;
        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
        	cadena +='<a href="javascript:void(0)" onclick="mdespacho(\''+item.id+'\', \`'+nproducto+'\`,\''+item.factor+'\',\''+item.lote+'\',\''+item.umedidac+'\',\''+item.umedidav+'\',\''+url+'\');" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt></a>';
        	$('#grdescripcion').html(cadena);
				});
			}
	  })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function mdespacho(id,nom,fac,lote,mec,mev,url)
{
	reset_despacho();
	document.getElementById('mcodigo').value = id;
	document.getElementById('mdescripcion').value = nom;

	if (lote==1) {
		$('#tbLotes').html('');
		document.getElementById('mdetalle').style.display = 'block';

		var lurl=url.replace('busProductos','busLotes');
		var cadena='';
		$.post(lurl,{id},function(data){
			var c = JSON.parse(data);
			$.each(c, function(i,item){
				var valores=item.nlote+'|'+item.stock+'|'+item.fvencimiento;
				cadena += '<tr>';
				cadena += '<td><div class="form-check"><label class="form-check-label"><input class="form-check-input nlote" type="checkbox" value="'+valores+'" onclick="marcadosd(this)">'+item.nlote+'</label><div></td>';
				cadena += '<td>'+item.stock+'</td>';
				cadena += '<td>'+item.fvencimiento+'</td>';
				cadena += "</tr>";
				$('#tbLotes').html(cadena);
			});
		});
	}else{
		document.getElementById('mdetalle').style.display = 'none';
	}

	$('#mmedida').html('');
	if (fac>1) {
		$("#mmedida").append('<option value="'+mev+'|1">Precio Unidad</option>');
		$("#mmedida").append('<option value="'+mec+'|'+fac+'">Precio Caja</option>');
	}else{
		$("#mmedida").append('<option value="'+mev+'|1">Precio Unidad</option>');
	}

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
	document.getElementById('mcantidad').select();
}

function marcadosd(nvalor)
{
	let numerosl = [];
	let cantidades=0;
	let vencimientol = [];
	let cpedido=parseFloat($('#mcantidad').val());
	let cactual=parseFloat($('#centregar').val());

	if (nvalor.checked) {
		if (cpedido>=cactual) {
			$(".nlote:checked").each(function(){
				var valor=this.value.split('|');
				numerosl.push(valor[0]);
				cantidades+=parseFloat(valor[1]);
				vencimientol.push(valor[2]);
			});

			document.getElementById('nlote').value=numerosl;
			document.getElementById('centregar').value=cantidades;
			document.getElementById('flote').value=vencimientol;
		}
		else {
			nvalor.checked=0;
		}
	}else{
		$(".nlote:checked").each(function(){
			var valor=this.value.split('|');
			numerosl.push(valor[0]);
			cantidades+=parseFloat(valor[1]);
			vencimientol.push(valor[2]);
		});

		document.getElementById('nlote').value=numerosl;
		document.getElementById('centregar').value=cantidades;
		document.getElementById('flote').value=vencimientol;
	}
}

function appdespacho()
{
	var codigo=$('#mcodigo').val();
	var nombres=$('#mdescripcion').val();
	var cantidad=$('#mcantidad').val();
	var medida=$('#mmedida').val().split('|');
	var nlote=$('#nlote').val();
	var flote=$('#flote').val();

	if(codigo!='' && nombres!='' && cantidad!=''){
		cadena = '<tr>';
		cadena += '<td><input type="hidden" name="idproducto[]" value="'+codigo+'"/><textarea name="descripcion[]" class="campo">'+nombres+'</textarea></td>';
		cadena += '<td><input type="hidden" name="factor[]" value="'+medida[1]+'"/><input type="text" name="unidad[]" value="'+medida[0]+'" class="campo"/></td>';
		cadena += '<td><input type="text" name="cantidad[]" value="'+cantidad+'" min="1" class="campo"/></td>';
		cadena += '<td><input type="text" name="lote[]" value="'+nlote+'" class="campo"/></td>';
		cadena += '<td><input type="text" name="fvencimiento[]" value="'+flote+'" class="campo"/></td>';
		cadena += '<td><a href="javascript:void(0)" class="btn btn-danger btn-sm py-0 elimina" title="Eliminar" data-toggle="tooltip" data-placement="bottom"><i class="fa fa-trash"></i></a></td>';
		cadena += "</tr>";
	  var tr = $(cadena).prependTo("#grilla");

	  // Ajustar la altura del textarea recién agregado
	  var textarea = tr.find('textarea[name="descripcion[]"]');
  	textarea.height(textarea[0].scrollHeight - 4);
		reset_despacho();
		feliminar();
		$('#busdespacho').modal('hide');
	}
};

function reset_despacho()
{
	$('#mensajeerror').html('');
	$('#mcodigo').val('');
	$('#mdescripcion').val('');
	$('#mcantidad').val('');
	$('#mmedida').val('');

	$('#centregar').val(0);
	$('#nlote').val('');
	$('#flote').val('');
	$('#tbLotes').html('');
	document.getElementById('mdetalle').style.display = 'none';

	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

/*=========================================================================================================================
=                                                    funciones reporte                                                    =
=========================================================================================================================*/
function productoNombre(url,id)
{
	$('#mensajeerror').html('');
	document.getElementById('tbldescripcion').style.display = 'block';
	$('#grdescripcion').html('');
	if (id.length > 2) {
		const data = new FormData();
		data.append('id', id);

		fetch(url, {
		   method: 'POST',
		   body: data
		})
		.then(function(response) {
		   if(response.ok) {
		       return response.json();
		   } else {
		       throw "Error en la llamada Ajax";
		   }
		})
		.then((datos) => {
			//console.log(datos);
			if (datos.length==0)
			{
				$('#grdescripcion').html('<strong>No hay datos de la busqueda</strong>');
			}
			else
			{
				var cadena='';
				$.each(datos, function(i,item){
		        	var ant=i-1;
					var pos=i+1;
					var anterior='input'+ant;
					var posterior='input'+pos;

		        	var nproducto=item.descripcion;
		        	if (item.nlaboratorio!='') {nproducto+=' ['+item.nlaboratorio+']';}
				    cadena +='<a href="javascript:void(0)" onclick="pproducto(\''+item.id+'\', \`'+nproducto+'\`);" title="Click para seleccionar" id="input'+i+'" onkeyup="saltar(event,\''+anterior+'\',\''+posterior+'\')"><dt class="mx-2">'+nproducto+'</dt></a>';
				    cadena +='<hr class="m-0">';
		        	$('#grdescripcion').html(cadena);
				});
			}
	    })
		.catch(function(err) {
		   console.log(err);
		});
	}
}

function pproducto(cod,nom)
{
	document.getElementById('idproducto').value = cod;
	document.getElementById('descripcion').value = nom;
	$('#grdescripcion').html('');
	document.getElementById('tbldescripcion').style.display = 'none';
}

function atributos(url,id)
{
	$('#nombres').html('');
	$.post(url,{id},function(data){
		var c = JSON.parse(data);
		$.each(c, function(i,item){
			$('#nombres').append('<option value="'+item.id+'">'+item.descripcion+'</option>');
		});
	});
}

function rventa(format) {
  var actionUrl = '';
  var target = '_self';
  if (format === 'pdf') {
    actionUrl = base_url+'reporte/pdfventa';
    target = '_blank';
  } else if (format === 'excel') {
    actionUrl = base_url+'reporte/excelventa';
  }
  document.getElementById('fventa').action = actionUrl;
  document.getElementById('fventa').target = target;
  document.getElementById('fventa').submit();
}

document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('pdfventa')) {
		document.getElementById('pdfventa').addEventListener('click', function() {
		  rventa('pdf');
		});

		document.getElementById('excelventa').addEventListener('click', function() {
		  rventa('excel');
		});
	}
});

function rcompra(format) {
  var actionUrl = '';
  var target = '_self';
  if (format === 'pdf') {
    actionUrl = base_url+'reporte/pdfcompra';
    target = '_blank';
  } else if (format === 'excel') {
    actionUrl = base_url+'reporte/excelcompra';
  }
  document.getElementById('fcompra').action = actionUrl;
  document.getElementById('fcompra').target = target;
  document.getElementById('fcompra').submit();
}

document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('pdfcompra')) {
		document.getElementById('pdfcompra').addEventListener('click', function() {
		  rcompra('pdf');
		});

		document.getElementById('excelcompra').addEventListener('click', function() {
		  rcompra('excel');
		});
	}
});

function rcompraProducto(format) {
  var actionUrl = '';
  var target = '_self';
  if (format === 'pdf') {
    actionUrl = base_url+'reporte/pdfproductoc';
    target = '_blank';
  } else if (format === 'excel') {
    actionUrl = base_url+'reporte/excelproductoc';
  }
  document.getElementById('fproductoc').action = actionUrl;
  document.getElementById('fproductoc').target = target;
  document.getElementById('fproductoc').submit();
}

document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('pdfproductoc')) {
		document.getElementById('pdfproductoc').addEventListener('click', function() {
		  rcompraProducto('pdf');
		});

		document.getElementById('excelproductoc').addEventListener('click', function() {
		  rcompraProducto('excel');
		});
	}
});

function rcompraProveedor(format) {
  var actionUrl = '';
  var target = '_self';
  if (format === 'pdf') {
    actionUrl = base_url+'reporte/pdfproveedor';
    target = '_blank';
  } else if (format === 'excel') {
    actionUrl = base_url+'reporte/excelproveedor';
  }
  document.getElementById('fproveedor').action = actionUrl;
  document.getElementById('fproveedor').target = target;
  document.getElementById('fproveedor').submit();
}

document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('pdfproveedor')) {
		document.getElementById('pdfproveedor').addEventListener('click', function() {
		  rcompraProveedor('pdf');
		});

		document.getElementById('excelproveedor').addEventListener('click', function() {
		  rcompraProveedor('excel');
		});
	}
});

/*====================================================================================================================
=                                                  funciones validador                                               =
====================================================================================================================*/
function vcomprobante(nro,url)
{
	$('#serie').html('');
	$.post(url,{nro},function(data){
		var c = JSON.parse(data);
		$.each(c, function(i,item){
			$('#serie').append('<option value="'+item.serie+'">'+item.serie+'</option>');
		});
	});
}

document.addEventListener('DOMContentLoaded', function() {
  var logos = document.getElementById('logos');
  var pushMenuButton = document.querySelector('[data-widget="pushmenu"]');
  var sidebar = document.querySelector('.main-sidebar'); // Seleccionar la barra lateral

  function updateLogos() {
    if (document.body.classList.contains('sidebar-collapse')) {
      logos.src = base_url+'public/logo/logo_sgfarmap.png'; // Imagen para menú contraído
    } else {
      logos.src = base_url+'public/logo/logo_sgfarma.png'; // Imagen para menú expandido
    }
  }

  // Cambiar la imagen cuando el estado del menú cambia
  if (pushMenuButton) {
    pushMenuButton.addEventListener('click', function() {
      // Espera un momento para que la clase 'sidebar-collapse' se actualice
      setTimeout(updateLogos, 100);
    });
  }

  // Cambiar la imagen al pasar el mouse sobre el menú
  if (sidebar) {
    sidebar.addEventListener('mouseenter', function() {
      logos.src = base_url+'public/logo/logo_sgfarma.png'; // Imagen para menú expandido
    });
    sidebar.addEventListener('mouseleave', updateLogos); // Revertir la imagen según el estado del menú
  }

  // Cambiar la imagen al cargar la página según el estado del menú
  updateLogos();
});

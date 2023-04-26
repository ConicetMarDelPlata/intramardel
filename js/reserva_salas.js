$("document").ready(function(){
	var x = document.getElementById("sala").value;
	if (x != 0){
		$('#calendar').removeClass('display-none');
		$('#salaHome-content').addClass('is-focused');
	} else {
		$('#calendar').addClass('display-none');
		$('#salaHome-content').removeClass('is-focused');
	}
});
function Block(iniTime,sDate){
	var sSala = $('select[name="sala"] option:selected').val();
	$.get("./includes/app/reserva_salas/Function.getSalasInfo.php?iTime="+iniTime+"&sDate="+sDate+"&sSala="+sSala,function(data, status){
		if (data != 'false') {
			$('.form-content').removeClass('form-content--disabled');
			$('input').removeAttr('disabled');
			$('textarea').removeAttr('disabled');
			$('select').removeAttr('disabled');
			$('#sTitulo').val('');
			$('#titulo-content').removeClass('is-focused');
			$('#sBlockDate').attr('disabled',true);
			$('#sTimeIni').attr('disabled',true);
			$('#sala-content').removeClass('is-focused');
			$('#sSala').val(0);
			$('#titular-content').removeClass('is-focused');
			$('#sTitular').val('');
			$('#motivo-content').removeClass('is-focused');
			$('#sMotivo').val('');
			$('.form-content:has("#date-content")').addClass('form-content--disabled')
			$('.form-content:has("#inicio-content")').addClass('form-content--disabled')
			getTimeRange(iniTime,data,'Fin');
			getTimeRange('07:30','17:30','Ini');
			var vTempDate = sDate.split("-");
			$("#sBlockDate").val(vTempDate[2] + "-" + vTempDate[1] + "-" + vTempDate[0]);
			$("#modalForm").css("display","table");
			$('#addButton').removeClass('display-none');
			$('#editButton').addClass('display-none');
			$('#removeButton').addClass('display-none');
			$("#sTimeIni").val(iniTime.slice(0, 5));
			if (sSala != ''){
				$("#sSala option[value="+ sSala +"]").attr("selected",true);
				$('#sala-content').addClass('is-focused');
			}
			updateEquipment(sSala);
		}
	});
	
}
function uploadCalendar(){
	var par = window.location.search.split('&');
	$('#calendar').addClass('display-none');
	var x = document.getElementById("sala").value;
	if (x!=''){
		if (par[0] != ""){
			var url = window.location.pathname+par[0]+"&s="+x;
		} else {
			var url = window.location.pathname+"?m=0&s="+x;
		}
		window.location.assign(url)
	}
}
function navigate(m){
	var s=document.getElementById("sala").value;
	var url = window.location.pathname+"?m="+m+"&s="+s;
	window.location.assign(url)
}
function onFocus(inputId){
	$('#'+inputId).addClass('is-focused');
	$('#'+inputId+' p').removeClass('display-none');
	console.log("onfocus");
}
function onFocusOut(inputId, type){
	if (type){
		val = $('#'+inputId+' '+type).val();
	} else {
		val = $('#'+inputId+' input').val();
	}
	$('#'+inputId+' p').addClass('display-none');
	if (val == "" || val == undefined) {
		$('#'+inputId).removeClass('is-focused');
	}
}
function closeModal(){
	$("#modalForm").css("display","none");
}

function getTimeRange(ini,limit,input){
	if(!limit || limit == ''){
		//var texto = "NO HAY NADA POR DELANTE";
		var tope = '18:00:00';
	}else{
		//var texto = "HASTA LAS "+data;
		var tope = limit;
	}
	tope   = tope.split(":");
	actual = ini.split(":");
	var minTope = parseInt(tope[1]);
	var hsTope = parseInt(tope[0]);
	var minActual = parseInt(actual[1]);
	var hsActual = parseInt(actual[0]);
	var optionTime = "";
	var optionHs = "";
	var optionMin = "";
	$('#sTime'+input).children().remove();
	while(hsActual != hsTope || minActual != minTope){
		minActual = minActual + 30;
		
		if(minActual == 60){
			hsActual = hsActual + 1;
			minActual = 0;
		}
		
		if(minActual < 10){
			optionMin = '0'+ minActual;
		}else{
			optionMin = minActual;
		}
		
		if(hsActual < 10){
			optionHs = '0'+ hsActual;
		}else{
			optionHs = hsActual;				
		}
		
		optionTime = optionHs+":"+optionMin;
		$('#sTime'+input).append('<option value="'+optionTime+'">'+optionTime+'</option>');
	}
}
function onSalaChange(){
	var sala = $('select[name="sSala"] option:selected').val();
	var tempDate = $("#sBlockDate").val().split("-");
	var date = tempDate[2] + "-" + tempDate[1] + "-" + tempDate[0];
	var iniTime=$("#sTimeIni").val();
	if (sala != '') {
		$.get("./includes/app/reserva_salas/Function.getSalasInfo.php?iTime="+iniTime+"&sDate="+date+"&sSala="+sala,function(data, status){
			if (data != 'false'){
				getTimeRange(iniTime,data,'Fin');
			}
			updateEquipment(sala);
		});
	}
}

function updateEquipment(sala,equiposSelected = [],editable = true){
	$('#sEquipos').children().remove();
	$('#sEquipos').addClass('display-none');
	$.get("./includes/app/reserva_salas/Function.getSalasEquipment.php?sala="+sala,function(data, status){
		eq = JSON.parse(data);
		if (eq.length >0){
			$('#sEquipos').append('<label>Equipos</label>');
			$('#sEquipos').removeClass('display-none');
			eq.forEach(element => {
				id=element['id'];
				innerCont = "cont"+id;
				$('#sEquipos').append('<div id='+innerCont+' class="form-content-checkbox-item"></div>');
				container = $('#'+innerCont);
				if (editable){
					container.append('<input type="checkbox" name="rEquipos[]" value="'+id+'"/>');
				} else {
					container.append('<input type="checkbox" name="rEquipos[]" value="'+id+'" disabled/>');
				}
				container.append('<label for="'+element['id']+'">'+ element['nombre']+'</label>');
			});
			equiposSelected.forEach(element =>
				$('#sEquipos input[value='+element+']').attr("checked", true)
			);
		}
	});
}
function validar(){
	valid = true;
	if ($('#sTitulo').val()===''){
		$('.form-content:has("#titulo-content")').addClass('form-content--error');
		$('#titulo-content p').removeClass('display-none');
		valid = false;
	} else {
		$('.form-content:has("#sTitulo")').removeClass('form-content--error');
	}
	if ($('#sBlockDate').val()===''){
		$('.form-content:has("#date-content")').addClass('form-content--error');
		$('#date-content p').removeClass('display-none');
		valid = false;
	} else {
		if (validarFecha($('#sBlockDate').val())){
			$('.form-content:has("#sBlockDate")').removeClass('form-content--error');
		} else {
			$('.form-content:has("#date-content")').addClass('form-content--error');
			$('#date-content p').removeClass('display-none');
			valid = false;
		}
	}
	hIn = $("#sTimeIni").val().split(':')[0];
	mIn = $("#sTimeIni").val().split(':')[1];
	hFi = $("#sTimeFin").val().split(':')[0];
	mFi = $("#sTimeFin").val().split(':')[1];

	if (hIn > hFi){
		$('.form-content:has("#fin-content")').addClass('form-content--error');
		$('#fin-content p').text('Fin debe ser mayor a Inicio.');
		$('#fin-content p').removeClass('display-none');
		valid = false;
	} else {
		if (hIn == hFi){
			if (mFi < mIn){
				$('.form-content:has("#fin-content")').addClass('form-content--error');
				$('#fin-content p').text('Fin debe ser mayor a Inicio.');
				$('#fin-content p').removeClass('display-none');
				valid = false;
			} else {
				$('.form-content:has("#fin-content")').removeClass('form-content--error');
			}
		} else {
			$('.form-content:has("#fin-content")').removeClass('form-content--error');
		}
	}
	if ($('#sTitular').val()===''){
		$('.form-content:has("#titular-content")').addClass('form-content--error');
		$('#titular-content p').removeClass('display-none');
		valid = false;
	} else {
		$('.form-content:has("#sTitular")').removeClass('form-content--error');
	}
	return valid;
}
function newReservation(usuario){
	var sala = $('select[name="sSala"] option:selected').val();
	var selected = [];
	$('#sEquipos input:checked').each(function() {
		selected.push($(this).attr('value'));
	});
	if (validar()){
		var data = {
			"sala": sala,
			"usuarioId": usuario,
			"sDate": $("#sBlockDate").val(),
			"sTimeIni": $("#sTimeIni").val(),
			"sTimeFin": $("#sTimeFin").val(),
			"sTitulo": $("#sTitulo").val(),
			"sTitular": $("#sTitular").val(),
			"sMotivo": $("#sMotivo").val(),
			"sEquipos": JSON.stringify(selected),
		};
		$.ajax({
			type: "POST",
			url: "./includes/app/reserva_salas/Function.abmReservaSala.php",
			data: data,
			dataType: "json",
			success: function(result){
				if(result){
					location.reload(true);
					closeModal();
					openAlertBox('success','Reserva realizada con éxito.')
				}else{
					openAlertBox('error','Algo salio mal!')
				}
			},
			error: function(e){
				openAlertBox('error','Algo salio mal! error: ' + JSON.parse(e.responseText).message)
				closeModal();
			}
		});
	} 
}

function editReservation(id){
	$.ajax({
		type: "GET",
		url: "./includes/app/reserva_salas/Function.abmReservaSala.php?id="+id,
		dataType: "json",
		success: function(data){
			var editable = data['editable'];
			var eliminable = data['eliminable'];
			if (data){
				$('#reservationId').val(id);
				$('#addButton').addClass('display-none');
				$('#removeButton').addClass('display-none');
				$('#editButton').addClass('display-none');
				if (editable){
					$('.form-content').removeClass('form-content--disabled');
					$('#editButton').removeClass('display-none');
					//$('select').removeAttr('disabled');
				} else {
					$('.form-content').addClass('form-content--disabled');
					$('intput').attr('disabled',true);
					$('textarea').attr('disabled',true);
					//$('select').attr('disabled',true);
				}
				if (eliminable){
					$('#removeButton').removeClass('display-none');
				}

				if (data.sTitulo != '') {
					$('#titulo-content').addClass('is-focused');
					$('#sTitulo').val(data.sTitulo);
				}
				if (data.sDate != ''){
					if (editable){
						$('#sBlockDate').removeAttr('disabled');
					}
					$('#sBlockDate').val(data.sDate);
				}
				if (data.sTimeIni != ''){
					getTimeRange('07:30','17:30','Ini');
					if (editable){
						$('#sTimeIni').removeAttr('disabled');
					}
					$('#sTimeIni').val(data.sTimeIni.slice(0, 5));
				}
				if (data.sTimeFin != ''){
					getTimeRange('08:00','','Fin');
					$('#sTimeFin').val(data.sTimeFin.slice(0, 5));
				}
				if (data.sala != '') {
					$('#sala-content').addClass('is-focused');
					$('#sSala').val(data.sala);
					updateEquipment(data.sala,data.Equipos,editable);
				}
				if (data.sTitular != '') {
					$('#titular-content').addClass('is-focused');
					$('#sTitular').val(data.sTitular);
				}
				if (data.sMotivo != '') {
					$('#motivo-content').addClass('is-focused');
					$('#sMotivo').val(data.sMotivo);
				}
				$("#modalForm").css("display","table");
			}else{
				openAlertBox('error','Algo salio mal!')
			}
		},
		error: function(e){
			openAlertBox('error','Algo salio mal! error: '+ JSON.parse(e.responseText).message)
			closeModal();
		}
	});

}

function saveReservation(usuario){
	var id = $('#reservationId').val();
	console.log(id);
	var sala = $('select[name="sSala"] option:selected').val();
	var selected = [];
	$('#sEquipos input:checked').each(function() {
		selected.push($(this).attr('value'));
	});
	if (validar()){
		var data = {
      		"id": id,
			"sala": sala,
			"usuarioId": usuario,
			"sDate": $("#sBlockDate").val(),
			"sTimeIni": $("#sTimeIni").val(),
			"sTimeFin": $("#sTimeFin").val(),
			"sTitulo": $("#sTitulo").val(),
			"sTitular": $("#sTitular").val(),
			"sMotivo": $("#sMotivo").val(),
			"sEquipos": JSON.stringify(selected),
		};
		$.ajax({
			type: "PUT",
			url: "./includes/app/reserva_salas/Function.abmReservaSala.php",
			data: data,
			dataType: "json",
			success: function(result){
				if(result){
					location.reload(true);
					closeModal();
					openAlertBox('success','Reserva editada con éxito.')
				}else{
					openAlertBox('error','Algo salio mal!')
				}
			},
			error: function(e){
				openAlertBox('error','Algo salio mal! error: '+ JSON.parse(e.responseText).message)
				closeModal();
			}
		});
	}
}
function removeReservation(userId){
	var id = $('#reservationId').val();
	console.log(id);
	var data = {
		"id": id,
		"usuarioId": userId,
	};
	$.ajax({
		type: "DELETE",
		url: "./includes/app/reserva_salas/Function.abmReservaSala.php",
		data: data,
		dataType: "json",
		success: function(result){
			if(result){
				location.reload(true);
				closeModal();
				openAlertBox('success','Reserva cancelada con éxito.')
			}else{
				openAlertBox('error','Algo salio mal!')
			}
		},
		error: function(e){
			openAlertBox('error','Algo salio mal! error: '+ JSON.parse(e.responseText).message)
			closeModal();
		}
	});
}

function openAlertBox(type,message){
	if (type==="error") {
		$('.m-alertbox-icon #icon').text('error');
		$('.m-alertbox-container p strong').text(message);
		$('.m-alertbox').removeClass('m-alertbox--success');
		$('.m-alertbox').addClass('m-alertbox--error');
	} else {
		$('.m-alertbox-icon #icon').text('done');
		$('.m-alertbox-container p strong').text(message);
		$('.m-alertbox').removeClass('m-alertbox--error');
		$('.m-alertbox').addClass('m-alertbox--success');
	}
	$('.m-alertbox-animation').removeClass('hidden');
}
function closeAlert(){
	$('.m-alertbox-animation').addClass('hidden');
}

//Devuelve el regex para validar una fecha.
// - dividido en variables para que se entienda y se pueda mantener/editar.
//
function regexValidarFecha() {
    let sep              = "[-]",
    
        dia1a28          = "(0?[1-9]|1\\d|2[0-8])",
        dia29            = "(29)",
        dia29o30         = "(29|30)",
        dia31            = "(31)",
        
        mes1a12          = "(0?[1-9]|1[0-2])",
        mes2             = "(0?2)",
        mesNoFeb         = "(0?[13-9]|1[0-2])",
        mes31dias        = "(0?[13578]|1[02])",
        
        diames29Feb      = dia29+sep+mes2,
        diames1a28       = dia1a28+sep+mes1a12,
        diames29o30noFeb = dia29o30+sep+mesNoFeb,
        diames31         = dia31+sep+mes31dias,
        diamesNo29Feb    = "(?:"+diames1a28+"|"+diames29o30noFeb+"|"+diames31+")",
        
        anno1a9999     = "(0{2,3}[1-9]|0{1,2}[1-9]\\d|0?[1-9]\\d{2}|[1-9]\\d{3})",
        annoMult4no100   = "\\d{1,2}(?:0[48]|[2468][048]|[13579][26])",
        annoMult400      = "(?:0?[48]|[13579][26]|[2468][048])00",
        annoBisiesto     = "("+annoMult4no100+"|"+annoMult400+")",
        
        fechaNo29Feb     = diamesNo29Feb+sep+anno1a9999,
        fecha29Feb       = diames29Feb+sep+annoBisiesto,
        
        fechaFinal       = "^(?:"+fechaNo29Feb+"|"+fecha29Feb+")$";
    
    return new RegExp(fechaFinal);
}

//Valida una fecha ingresada como "m/d/aaaa"
// - Si no es válida, devuelve false
// - Si es válida, devuelve un objeto {d:"día",m:"mes",a:"año",date:date}
// - Parámetro: UTC (opcional) si se debe devolver {date:(date)} en UTC
//
function validarFecha(texto, UTC = false) {
    let fechaValida = regexValidarFecha(),
        // fechaValida = /^(?:(?:(0?[1-9]|1\d|2[0-8])[/](0?[1-9]|1[0-2])|(29|30)[/](0?[13-9]|1[0-2])|(31)[/](0?[13578]|1[02]))[/](0{2,3}[1-9]|0{1,2}[1-9]\d|0?[1-9]\d{2}|[1-9]\d{3})|(29)[/](0?2)[/](\d{1,2}(?:0[48]|[2468][048]|[13579][26])|(?:0?[48]|[13579][26]|[2468][048])00))$/,
        grupos;
        
    if (grupos = fechaValida.exec(texto)) {
        //Unir día mes y año desde los grupos que pueden haber coincidido
        let d = [grupos[1],grupos[3],grupos[5],grupos[8]].join(''),
            m = [grupos[2],grupos[4],grupos[6],grupos[9]].join(''),
            a = [grupos[7],grupos[10]].join(''),
            date = new Date(0);

        //Obtener la fecha en formato local o UTC
        if (UTC) {
            date.setUTCHours(0);
            date.setUTCFullYear(a,parseInt(m,10) - 1,d);
        } else {
            date.setHours(0);
            date.setFullYear(a,parseInt(m,10) - 1,d);
        }
        
        //Devolver como objeto con cada número por separado
        return {
            d: d,
            m: m,
            a: a,
            date: date
        };
    }
    return false; //No es fecha válida
}
<?php
	include "includes/header.php";
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_GET['opcion'];
	$autenticado = $_SESSION["autentificado"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;	
	
	$bd = new Bd;
	$bd->AbrirBd();
	//$userData = $bd->consultar_nombre_usuario($nombre_usuario);
	//$puede_entrar = $bd->getPermisos($userData,'CAN_ACCESS_NOT_GRAL');
	$puede_entrar = $bd->checkAccess($_SESSION["id_usuario"],32,''); //32=Certificados
	if ($bd->nivel_acceso($nombre_usuario) > 1){
		if(!$puede_entrar){
			header("Location: panel_control.php");
			exit();
		}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Certificado</title>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<style type="text/css">
.tituloweb2 {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: bold;
	line-height: 10px;
}
.tituloweb2Copia {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 12px;
	color: #06C;
	font-weight: normal;
	line-height: 10px;
}

a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}
a:active {
	text-decoration: none;
	text-align: right;
}
.cerrar {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 9px;
	color: #333;
}
.pie {	font-family: Tahoma, Geneva, sans-serif;
	font-size: 9px;
	color: #FFF;
	padding-top: 5px;
	padding-right: 5px;
	padding-bottom: 5px;
	padding-left: 5px;
	text-align: center;
}
</style>
<script language="javascript" >
function enviar(inForm){
	
	if (inForm.opcion.value != 2){ //SI NO ELIJE ELIMINAR
		select = inForm.id_tipo_certificado;
		var idTipoCertificado = select.options[select.selectedIndex].value;
		switch (idTipoCertificado){
			case '1': //Obra Social
				fecha_certificado_string = inForm.fecha_certificado_OS.value;
				var fecha_certificado_array = fecha_certificado_string.split('-');
				var fecha_certificado_string = fecha_certificado_array[2] + '/' + fecha_certificado_array[1] + '/' + fecha_certificado_array[0];	
				var fecha_certificado = new Date(fecha_certificado_string);
				hoy = new Date();
				fecha_ingreso_string = inForm.fecha_ingreso_OS.value;
				var fecha_ingreso_array = fecha_ingreso_string.split('-');
				var fecha_ingreso_string = fecha_ingreso_array[2] + '/' + fecha_ingreso_array[1] + '/' + fecha_ingreso_array[0];	
				var fecha_ingreso = new Date(fecha_ingreso_string);
				
				if (inForm.apellido_OS.value.trim() == "") {
					alert("Debe indicar un apellido para completar el certificado.");
					inForm.apellido_OS.focus();
					return (false);
				} else if (inForm.nombre_OS.value.trim() == "") {
					alert("Debe indicar un nombre para completar el certificado.");
					inForm.nombre_OS.focus();
					return (false);
				} else if (inForm.DNI_OS.value.trim() == "") {
					alert("Debe indicar un DNI para completar el certificado.");
					inForm.DNI_OS.focus();
					return (false);
				} else if (!isInteger(inForm.DNI_OS.value.trim())) {
					alert("El DNI debe ser un numero entero (sin puntos).");
					inForm.DNI_OS.focus();
					return (false);
				} else if (inForm.CUIL_OS.value.trim() == "") {
					alert("Debe indicar un CUIL para completar el certificado.");
					inForm.CUIL_OS.focus();
					return (false);
				} else if (!isInteger(inForm.CUIL_OS.value.trim())) {
					alert("El CUIL debe ser un numero entero (sin guiones).");
					inForm.CUIL_OS.focus();
					return (false);
				} else if (inForm.fecha_ingreso_OS.value.trim() == "") {
					alert("Debe indicar una fecha de ingreso.");
					inForm.fecha_ingreso_OS.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_ingreso_OS.value, 'dateOnly'))) {
					alert("El formato de la fecha de ingreso es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_ingreso_OS.focus();
					return false;
				} else if (fecha_ingreso > hoy) {
					alert("La fecha de ingreso no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_ingreso_OS.focus();
					return false;
				} else if (inForm.fecha_certificado_OS.value == "") {
					alert("Debe indicar una fecha del certificado.");
					inForm.fecha_certificado_OS.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_certificado_OS.value, 'dateOnly'))) {
					alert("El formato de la fecha de certificado es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_certificado_OS.focus();
					return false;
				} else if (fecha_certificado > hoy) {
					alert("La fecha del certificado no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_certificado_OS.focus();
					return false;}
				else
					inForm.submit();

				break;
			case '2': //Unificacion de Aportes
				fecha_certificado_string = inForm.fecha_certificado_U.value;
				var fecha_certificado_array = fecha_certificado_string.split('-');
				var fecha_certificado_string = fecha_certificado_array[2] + '/' + fecha_certificado_array[1] + '/' + fecha_certificado_array[0];	
				var fecha_certificado = new Date(fecha_certificado_string);
				hoy = new Date();
				fecha_ingreso_string = inForm.fecha_ingreso_U.value;
				var fecha_ingreso_array = fecha_ingreso_string.split('-');
				var fecha_ingreso_string = fecha_ingreso_array[2] + '/' + fecha_ingreso_array[1] + '/' + fecha_ingreso_array[0];	
				var fecha_ingreso = new Date(fecha_ingreso_string);

				if (inForm.fecha_certificado_U.value == "") 
				{	alert("Debe indicar una fecha del certificado.");
					inForm.fecha_certificado_U.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_certificado_U.value, 'dateOnly'))) {
					alert("El formato de la fecha de certificado es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_certificado_U.focus();
					return false;
				} else if (fecha_certificado > hoy) {
					alert("La fecha del certificado no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_certificado_U.focus();
					return false;
				} else if (inForm.apellido_U.value.trim() == "") {
					alert("Debe indicar un apellido para completar el certificado.");
					inForm.apellido_U.focus();
					return (false);
				} else if (inForm.nombre_U.value.trim() == "") {
					alert("Debe indicar un nombre para completar el certificado.");
					inForm.nombre_U.focus();
					return (false);
				} else if (inForm.DNI_U.value.trim() == "") {
					alert("Debe indicar un DNI para completar el certificado.");
					inForm.DNI_U.focus();
					return (false);
				} else if (!isInteger(inForm.DNI_U.value.trim())) {
					alert("El DNI debe ser un numero entero (sin puntos).");
					inForm.DNI_U.focus();
					return (false);
				} else if (inForm.CUIL_U.value.trim() == "") {
					alert("Debe indicar un CUIL para completar el certificado.");
					inForm.CUIL_U.focus();
					return (false);
				} else if (!isInteger(inForm.CUIL_U.value.trim())) {
					alert("El CUIL debe ser un numero entero (sin guiones).");
					inForm.CUIL_U.focus();
					return (false);
				} else if (inForm.fecha_ingreso_U.value.trim() == "") {
					alert("Debe indicar una fecha de ingreso.");
					inForm.fecha_ingreso_U.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_ingreso_U.value, 'dateOnly'))) {
					alert("El formato de la fecha de ingreso es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_ingreso_U.focus();
					return false;
				} else if (fecha_ingreso > hoy) {
					alert("La fecha de ingreso no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_ingreso_U.focus();
					return false;
				} else
					inForm.submit();

				break; 
			case '3': //Antiguedad
				fecha_certificado_string = inForm.fecha_certificado_A.value;
				var fecha_certificado_array = fecha_certificado_string.split('-');
				var fecha_certificado_string = fecha_certificado_array[2] + '/' + fecha_certificado_array[1] + '/' + fecha_certificado_array[0];	
				var fecha_certificado = new Date(fecha_certificado_string);

				hoy = new Date();

				fecha_ingreso_string = inForm.fecha_ingreso_A.value;
				var fecha_ingreso_array = fecha_ingreso_string.split('-');
				var fecha_ingreso_string = fecha_ingreso_array[2] + '/' + fecha_ingreso_array[1] + '/' + fecha_ingreso_array[0];	
				var fecha_ingreso = new Date(fecha_ingreso_string);

				if (inForm.fecha_egreso_A.value.trim() != "") {
					fecha_egreso_string = inForm.fecha_egreso_A.value;
					var fecha_egreso_array = fecha_egreso_string.split('-');
					var fecha_egreso_string = fecha_egreso_array[2] + '/' + fecha_egreso_array[1] + '/' + fecha_egreso_array[0];	
					var fecha_egreso = new Date(fecha_egreso_string);}
				
				if (inForm.apellido_A.value.trim() == "") {
					alert("Debe indicar un apellido para completar el certificado.");
					inForm.apellido_A.focus();
					return (false);
				} else if (inForm.nombre_A.value.trim() == "") {
					alert("Debe indicar un nombre para completar el certificado.");
					inForm.nombre_A.focus();
					return (false);
				} else if (inForm.DNI_A.value.trim() == "") {
					alert("Debe indicar un DNI para completar el certificado.");
					inForm.DNI_A.focus();
					return (false);
				} else if (!isInteger(inForm.DNI_A.value.trim())) {
					alert("El DNI debe ser un numero entero (sin puntos).");
					inForm.DNI_A.focus();
					return (false);
				} else if (inForm.CUIL_A.value.trim() == "") {
					alert("Debe indicar un CUIL para completar el certificado.");
					inForm.CUIL_A.focus();
					return (false);
				} else if (!isInteger(inForm.CUIL_A.value.trim())) {
					alert("El CUIL debe ser un numero entero (sin guiones).");
					inForm.CUIL_A.focus();
					return (false);
				} else if (inForm.fecha_ingreso_A.value.trim() == "") {
					alert("Debe indicar una fecha de ingreso.");
					inForm.fecha_ingreso_A.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_ingreso_A.value, 'dateOnly'))) {
					alert("El formato de la fecha de ingreso es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_ingreso_A.focus();
					return false;
				} else if (fecha_ingreso > hoy) {
					alert("La fecha de ingreso no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_ingreso_A.focus();
					return false;
				} else if ((inForm.incluye_fecha_egreso_A.checked) && (inForm.fecha_egreso_A.value.trim() == "")) {
					alert("Debe indicar una fecha de egreso.");
					inForm.fecha_egreso_A.focus();
					return (false);
				} else if ((inForm.incluye_fecha_egreso_A.checked) && !(isDataFormatValid(inForm.fecha_egreso_A.value, 'dateOnly'))) {
					alert("El formato de la fecha de egreso es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_egreso_A.focus();
					return false;
				} else if ((inForm.incluye_fecha_egreso_A.checked) && (fecha_ingreso > fecha_egreso)) {
					alert("La fecha de egreso no puede ser anterior a la fecha de ingreso. Por favor, corrijala.");
					inForm.fecha_egreso_A.focus();
					return false;
				} else if (inForm.fecha_certificado_A.value == "") {
					alert("Debe indicar una fecha del certificado.");
					inForm.fecha_certificado_A.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_certificado_A.value, 'dateOnly'))) {
					alert("El formato de la fecha de certificado es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_certificado_A.focus();
					return false;
				} else if (fecha_certificado > hoy) {
					alert("La fecha del certificado no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_certificado_A.focus();
					return false;}
				else
					inForm.submit();

				break;
			case '4': //Beca
				fecha_certificado_string = inForm.fecha_certificado_B.value;
				var fecha_certificado_array = fecha_certificado_string.split('-');
				var fecha_certificado_string = fecha_certificado_array[2] + '/' + fecha_certificado_array[1] + '/' + fecha_certificado_array[0];	
				var fecha_certificado = new Date(fecha_certificado_string);

				hoy = new Date();

				fecha_resolucion_string = inForm.fecha_resolucion_B.value;
				var fecha_resolucion_array = fecha_resolucion_string.split('-');
				var fecha_resolucion_string = fecha_resolucion_array[2] + '/' + fecha_resolucion_array[1] + '/' + fecha_resolucion_array[0];	
				var fecha_resolucion = new Date(fecha_resolucion_string);

				fecha_ini_beca_string = inForm.fecha_ini_beca_B.value;
				var fecha_ini_beca_array = fecha_ini_beca_string.split('-');
				var fecha_ini_beca_string = fecha_ini_beca_array[2] + '/' + fecha_ini_beca_array[1] + '/' + fecha_ini_beca_array[0];	
				var fecha_ini_beca = new Date(fecha_ini_beca_string);

				if (inForm.fecha_fin_beca_B.value.trim() != "") {
					fecha_fin_beca_string = inForm.fecha_fin_beca_B.value;
					var fecha_fin_beca_array = fecha_fin_beca_string.split('-');
					var fecha_fin_beca_string = fecha_fin_beca_array[2] + '/' + fecha_fin_beca_array[1] + '/' + fecha_fin_beca_array[0];	
					var fecha_fin_beca = new Date(fecha_fin_beca_string);}
				
				if (inForm.apellido_B.value.trim() == "") {
					alert("Debe indicar un apellido para completar el certificado.");
					inForm.apellido_B.focus();
					return (false);
				} else if (inForm.nombre_B.value.trim() == "") {
					alert("Debe indicar un nombre para completar el certificado.");
					inForm.nombre_B.focus();
					return (false);
				} else if (inForm.DNI_B.value.trim() == "") {
					alert("Debe indicar un DNI para completar el certificado.");
					inForm.DNI_B.focus();
					return (false);
				} else if (!isInteger(inForm.DNI_B.value.trim())) {
					alert("El DNI debe ser un numero entero (sin puntos).");
					inForm.DNI_B.focus();
					return (false);
				} else if (inForm.resolucion_B.value.trim() == "") {
					alert("Debe indicar un numero de resolucion para completar el certificado.");
					inForm.resolucion_B.focus();
					return (false);
				} else if (inForm.fecha_resolucion_B.value.trim() == "") {
					alert("Debe indicar una fecha de resolucion.");
					inForm.fecha_resolucion_B.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_resolucion_B.value, 'dateOnly'))) {
					alert("El formato de la fecha de resolucion es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_resolucion_B.focus();
					return false;
				} else if (fecha_resolucion > hoy) {
					alert("La fecha de resolucion no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_resolucion_B.focus();
					return false;
				} else if (inForm.fecha_ini_beca_B.value.trim() == "") {
					alert("Debe indicar una fecha de inicio de beca.");
					inForm.fecha_ini_beca_B.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_ini_beca_B.value, 'dateOnly'))) {
					alert("El formato de la fecha de inicio de beca es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_ini_beca_B.focus();
					return false;
				} else if (fecha_ini_beca > hoy) {
					alert("La fecha de inicio de beca no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_ini_beca_B.focus();
					return false;
				} else if ((inForm.incluye_fecha_fin_beca_B.checked) && (inForm.fecha_fin_beca_B.value.trim() == "")) {
					alert("Debe indicar una fecha de finalizacion de beca.");
					inForm.fecha_fin_beca_B.focus();
					return (false);
				} else if ((inForm.incluye_fecha_fin_beca_B.checked) && !(isDataFormatValid(inForm.fecha_fin_beca_B.value, 'dateOnly'))) {
					alert("El formato de la fecha de finalizacion de beca es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_fin_beca_B.focus();
					return false;
				} else if ((inForm.incluye_fecha_fin_beca_B.checked) && (fecha_ini_beca > fecha_fin_beca)) {
					alert("La fecha de finalizacion de beca no puede ser anterior a la fecha de inicio. Por favor, corrijala.");
					inForm.fecha_fin_beca_B.focus();
					return false;
				} else if (inForm.tema_B.value.trim() == "") {
					alert("Debe indicar un tema para completar el certificado.");
					inForm.tema_B.focus();
					return (false);
				} else if (inForm.apellido_direccion_B.value.trim() == "") {
					alert("Debe indicar un apellido del/a director/a para completar el certificado.");
					inForm.apellido_direccion_B.focus();
					return (false);
				} else if (inForm.nombre_direccion_B.value.trim() == "") {
					alert("Debe indicar un nombre del/a director/a para completar el certificado.");
					inForm.nombre_direccion_B.focus();
					return (false);
				} else if (inForm.lugar_beca_B.value.trim() == "") {
					alert("Debe indicar un lugar para completar el certificado.");
					inForm.lugar_beca_B.focus();
					return (false);
				} else if (inForm.fecha_certificado_B.value == "") {
					alert("Debe indicar una fecha del certificado.");
					inForm.fecha_certificado_B.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_certificado_B.value, 'dateOnly'))) {
					alert("El formato de la fecha de certificado es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_certificado_B.focus();
					return false;
				} else if (fecha_certificado > hoy) {
					alert("La fecha del certificado no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_certificado_B.focus();
					return false;}
				else
					inForm.submit();

				break;
			case '5': //Horarios y Lugar de trabajo
				fecha_certificado_string = inForm.fecha_certificado_H.value;
				var fecha_certificado_array = fecha_certificado_string.split('-');
				var fecha_certificado_string = fecha_certificado_array[2] + '/' + fecha_certificado_array[1] + '/' + fecha_certificado_array[0];	
				var fecha_certificado = new Date(fecha_certificado_string);

				hoy = new Date();

				fecha_ini_string = inForm.fecha_ini_H.value;
				var fecha_ini_array = fecha_ini_string.split('-');
				var fecha_ini_string = fecha_ini_array[2] + '/' + fecha_ini_array[1] + '/' + fecha_ini_array[0];	
				var fecha_ini = new Date(fecha_ini_string);

				var select = document.getElementById('id_unidad_H');
				var idUnidadEjecutora = select.options[select.selectedIndex].value;

				var select = document.getElementById('id_escalafon_H');
				var idEscalafon = select.options[select.selectedIndex].value;

				if (inForm.apellido_H.value.trim() == "") {
					alert("Debe indicar un apellido para completar el certificado.");
					inForm.apellido_H.focus();
					return (false);
				} else if (inForm.nombre_H.value.trim() == "") {
					alert("Debe indicar un nombre para completar el certificado.");
					inForm.nombre_H.focus();
					return (false);
				} else if (inForm.DNI_H.value.trim() == "") {
					alert("Debe indicar un DNI para completar el certificado.");
					inForm.DNI_H.focus();
					return (false);
				} else if (!isInteger(inForm.DNI_H.value.trim())) {
					alert("El DNI debe ser un numero entero (sin puntos).");
					inForm.DNI_H.focus();
					return (false);
				} else if (inForm.fecha_ini_H.value.trim() == "") {
					alert("Debe indicar una fecha de inicio.");
					inForm.fecha_ini_H.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_ini_H.value, 'dateOnly'))) {
					alert("El formato de la fecha de inicio es incorrecta. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_ini_H.focus();
					return false;
				} else if (fecha_ini > hoy) {
					alert("La fecha de inicio no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_ini_H.focus();
					return false;
				} else if ((idUnidadEjecutora == 11) && (inForm.lugar_H.value.trim() == "")) {
					//El lugar solo se solicita si la unidad es CCT MdP Zona de influencia
					alert("Debido a que la unidad seleccionada es Zona de Influencia, debe indicar un lugar para completar el certificado.");
					inForm.lugar_H.focus();
					return (false);
				} else if ((idEscalafon == 2) && (inForm.tema_H.value.trim() == "")) {
					//El tema investigado solo se solicita en caso de ser investigador
					alert("Debido a que la categoria es Investigador, debe indicar un tema para completar el certificado.");
					inForm.tema_H.focus();
					return (false);
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_ini_lunes_H.value.trim() == "")) {
					alert("Debe indicar una hora de inicio de tareas del dia lunes.");
					inForm.hora_ini_lunes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_ini_lunes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de inicio de tareas del dia lunes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_ini_lunes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_fin_lunes_H.value.trim() == "")) {
					alert("Debe indicar una hora de fin de tareas del dia lunes.");
					inForm.hora_fin_lunes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_fin_lunes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de fin de tareas del dia lunes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_fin_lunes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_ini_martes_H.value.trim() == "")) {
					alert("Debe indicar una hora de inicio de tareas del dia martes.");
					inForm.hora_ini_martes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_ini_martes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de inicio de tareas del dia martes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_ini_martes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_fin_martes_H.value.trim() == "")) {
					alert("Debe indicar una hora de fin de tareas del dia martes.");
					inForm.hora_fin_martes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_fin_martes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de fin de tareas del dia martes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_fin_martes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_ini_miercoles_H.value.trim() == "")) {
					alert("Debe indicar una hora de inicio de tareas del dia miercoles.");
					inForm.hora_ini_miercoles_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_ini_miercoles_H.value.trim(), 'time')))) {
					alert("El formato de la hora de inicio de tareas del dia miercoles es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_ini_miercoles_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_fin_miercoles_H.value.trim() == "")) {
					alert("Debe indicar una hora de fin de tareas del dia miercoles.");
					inForm.hora_fin_miercoles_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_fin_miercoles_H.value.trim(), 'time')))) {
					alert("El formato de la hora de fin de tareas del dia miercoles es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_fin_miercoles_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_ini_jueves_H.value.trim() == "")) {
					alert("Debe indicar una hora de inicio de tareas del dia jueves.");
					inForm.hora_ini_jueves_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_ini_jueves_H.value.trim(), 'time')))) {
					alert("El formato de la hora de inicio de tareas del dia jueves es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_ini_jueves_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_fin_jueves_H.value.trim() == "")) {
					alert("Debe indicar una hora de fin de tareas del dia jueves.");
					inForm.hora_fin_jueves_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_fin_jueves_H.value.trim(), 'time')))) {
					alert("El formato de la hora de fin de tareas del dia jueves es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_fin_jueves_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_ini_viernes_H.value.trim() == "")) {
					alert("Debe indicar una hora de inicio de tareas del dia viernes.");
					inForm.hora_ini_viernes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_ini_viernes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de inicio de tareas del dia viernes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_ini_viernes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (inForm.hora_fin_viernes_H.value.trim() == "")) {
					alert("Debe indicar una hora de fin de tareas del dia viernes.");
					inForm.hora_fin_viernes_H.focus();
					return false;
				} else if ((inForm.incluye_horarios_H.checked) && (!(isDataFormatValid(inForm.hora_fin_viernes_H.value.trim(), 'time')))) {
					alert("El formato de la hora de fin de tareas del dia viernes es incorrecto. Por favor complete la hora con el formato: " + timeFormatPattern + ".");
					inForm.hora_fin_viernes_H.focus();
					return false;
				} else if (inForm.fecha_certificado_H.value.trim() == "") {
					alert("Debe indicar una fecha del certificado.");
					inForm.fecha_certificado_H.focus();
					return (false);
				} else if (!(isDataFormatValid(inForm.fecha_certificado_H.value.trim(), 'dateOnly'))) {
					alert("El formato de la fecha de certificado es incorrecto. Por favor complete la fecha con el formato: " + dateFormatPattern + ".");
					inForm.fecha_certificado_H.focus();
					return false;
				} else if (fecha_certificado > hoy) {
					alert("La fecha del certificado no puede ser posterior a hoy. Por favor, corrijala.");
					inForm.fecha_certificado_H.focus();
					return false;}
				else
					inForm.submit();

				break;
		}//end case
	}
	else //SI ELIJE ELIMINAR DIRECTAMENTE ENVIO EL FORM
		inForm.submit();
}
</script>
<link href="tabla.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="funciones.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>

<link href="calendario/css/calendario.css" type="text/css" rel="stylesheet">
<script src="calendario/js/calendar.js" type="text/javascript"></script>
<script src="calendario/js/calendar-es.js" type="text/javascript"></script>
<script src="calendario/js/calendar-setup.js" type="text/javascript"></script>

<script language="javascript" type="text/javascript" src="js/misc.js"></script>
<script language="javascript" type="text/javascript" src="js/validaciones.js"></script>

<style type="text/css">
.TITULO {font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color: #333;
	font-variant: normal;
	font-weight: bold;
	text-align: center;
	padding-left: 5px;
	padding-right: 30px;
	vertical-align: bottom;
	padding-bottom: 10px;
}
</style>
</head>
<body>
<p align="center"><img src="cabecera.jpg" width="900" height="101" border="0" usemap="#Map">
  <map name="Map">
    <area shape="rect" coords="12,5,154,96" href="panel_control.php" target="_top">
  </map>
</p>
<table width="898" height="346" border="0" align="center" cellpadding="0">
	<tr align="right" valign="top">
		<td colspan="10" width="552">
			<a href="lista_certificados.php" class="tituloweb2Copia" style="font-weight:bold; font-size:10px">Volver</a>
		</td>
	</tr>
  <tr>
    <td class="cerrar"><p><strong><img src="images/bullet20.gif" width="9" height="9" /> <?php echo 'Usuario: '. $nombre_usuario ?></strong></p>
      <p>&nbsp;</p></td>
    <td align="left" valign="middle"><span class="TITULO">:: 
      M&oacute;dulo Certificados ::</span><a href="form_certificado.php?opcion=1"><img src="agregar.png" width="25" height="25" border="0"></a> 
    </td>
  </tr>
  <tr>
    <td width="170" valign="top" background="images/divisor-columna.jpg" bgcolor="#FFFFFF" class="tituloweb2" style="background-repeat: no-repeat; background-position: right;">
		<?php include_once("templates/menuLateral-modulos.php");?>
	</td>
    <td width="722" valign="top"> <p> 
        <?php
		$tipo_certificado_disabled = "";
		switch ($opcion){
			case 1: // OPCION ALTA 
				$id_certificado=0;
				//Nota Vani: asigno el numero al momento de grabar
				$numero = "";
				$anio = date('Y');
				$fecha_certificado = date('d-m-Y');
				$apellido = "";
				$nombre = "";
				$DNI = "";
				$id_tipo_certificado = 3; //Default Antiguedad
				//Unificacion de aportes	
				$id_titulo_persona = 0;
				$fecha_ingreso = "";
				$CUIL = "";
				//Obra social
				//id_titulo_persona + CUIL + fecha_ingreso
				$id_escalafon_categoria = 0;
				//Antiguedad
				$fecha_egreso = "";
				$goce_licencia = 0; //false
				$incluye_fecha_egreso_checked = "";
				//Beca
				$fecha_ini_beca = "";
				$fecha_fin_beca = "";
				$incluye_fecha_fin_beca_checked = "";				
				$resolucion = "";
				$fecha_resolucion = "";
				$tema = "";
				$apellido_direccion = "";
				$nombre_direccion = "";
				$articulo_lugar_la_checked = " checked ";
				$articulo_lugar_el_checked = "";
				$lugar_beca = "";
				//Horario
				$id_escalafon = 0;
				$fecha_ini = "";
				$id_unidad_ejecutora = 0;
				$lugar = "";
				$incluye_horarios_checked = "";
				$hora_ini_lunes = "";
				$hora_fin_lunes = "";
				$hora_ini_martes = "";
				$hora_fin_martes = "";
				$hora_ini_miercoles = "";
				$hora_fin_miercoles = "";
				$hora_ini_jueves = "";
				$hora_fin_jueves = "";
				$hora_ini_viernes = "";
				$hora_fin_viernes = "";
				break;
			case 2: // OPCION BAJA 
				//break;
			case 3: // OPCION MODIFICACION 
				//Valores default para todos los cert
				//Unificacion de aportes	
				$id_titulo_persona = 0;
				$fecha_ingreso = "";
				$CUIL = "";
				//Obra social
				//id_titulo_persona + CUIL + fecha_ingreso
				$id_escalafon_categoria = 0;
				//Antiguedad
				$fecha_egreso = "";
				$goce_licencia = 0; //false
				$incluye_fecha_egreso_checked = "";
				//Beca
				$fecha_ini_beca = "";
				$fecha_fin_beca = "";
				$incluye_fecha_fin_beca_checked = "";				
				$resolucion = "";
				$fecha_resolucion = "";
				$tema = "";
				$apellido_direccion = "";
				$nombre_direccion = "";
				$articulo_lugar_la_checked = " checked ";
				$articulo_lugar_el_checked = "";
				$lugar_beca = "";
				//Horario
				$id_escalafon = 0;
				$fecha_ini = "";
				$id_unidad_ejecutora = 0;
				$lugar = "";
				$incluye_horarios_checked = "";
				$hora_ini_lunes = "";
				$hora_fin_lunes = "";
				$hora_ini_martes = "";
				$hora_fin_martes = "";
				$hora_ini_miercoles = "";
				$hora_fin_miercoles = "";
				$hora_ini_jueves = "";
				$hora_fin_jueves = "";
				$hora_ini_viernes = "";
				$hora_fin_viernes = "";

				//Que no se pueda modificar el tipo de certificado
				$tipo_certificado_disabled = " disabled ";
				$id_certificado = $_GET['id_certificado'];
				$rowTC = $bd->consultar_tipo_certificado($id_certificado);
				$id_tipo_certificado = $rowTC["id_tipo_certificado"];

				switch ($id_tipo_certificado) {
					case 1: //Obra Social
						$row = $bd->consultar_certificado_obra_social($id_certificado);
						$numero = $row['numero'];
						$anio = $row['anio'];
						$fecha_certificado = convertir_fecha($row["fecha_certificado"]);
						$apellido = $row["apellido"];				
						$nombre = $row["nombre"];
						$DNI = $row["DNI"];

						$id_titulo_persona = $row["id_titulo_persona"];
						$fecha_ingreso = convertir_fecha($row["fecha_ingreso"]);
						$CUIL = $row["CUIL"];
						$id_escalafon_categoria = $row["id_escalafon_categoria"];
						break; 
					case 2: //Unificacion de aportes
						$row = $bd->consultar_certificado_unificacion_aportes($id_certificado);	
						$numero = $row['numero'];
						$anio = $row['anio'];
						$fecha_certificado = convertir_fecha($row["fecha_certificado"]);
						$apellido = $row["apellido"];				
						$nombre = $row["nombre"];
						$DNI = $row["DNI"];

						$id_titulo_persona = $row["id_titulo_persona"];
						$fecha_ingreso = convertir_fecha($row["fecha_ingreso"]);
						$CUIL = $row["CUIL"];
						break;
					case 3: //Antiguedad
						$row = $bd->consultar_certificado_antiguedad($id_certificado);
						$numero = $row['numero'];
						$anio = $row['anio'];
						$fecha_certificado = convertir_fecha($row["fecha_certificado"]);
						$apellido = $row["apellido"];				
						$nombre = $row["nombre"];
						$DNI = $row["DNI"];

						$id_titulo_persona = $row["id_titulo_persona"];
						$fecha_ingreso = convertir_fecha($row["fecha_ingreso"]);
						if (!is_null($row["fecha_egreso"])){
							$fecha_egreso = convertir_fecha($row["fecha_egreso"]);
							$incluye_fecha_egreso_checked = " checked ";
							}
						else 
							{
							$fecha_egreso = "";
							$incluye_fecha_egreso_checked = "";						
							}
						
						$CUIL = $row["CUIL"];
						$id_escalafon_categoria = $row["id_escalafon_categoria"];
						$goce_licencia =  $row["goce_licencia"];
						break; 
					case 4: //Beca
						$row = $bd->consultar_certificado_beca($id_certificado);
						$numero = $row['numero'];
						$anio = $row['anio'];
						$fecha_certificado = convertir_fecha($row["fecha_certificado"]);
						$apellido = $row["apellido"];				
						$nombre = $row["nombre"];
						$DNI = $row["DNI"];

						$id_escalafon_categoria = $row["id_escalafon_categoria"];
						$resolucion = $row["resolucion"];
						$fecha_resolucion = convertir_fecha($row["fecha_resolucion"]);
						$fecha_ini_beca = convertir_fecha($row["fecha_ini_beca"]);
						if (!is_null($row["fecha_fin_beca"])){
							$fecha_fin_beca = convertir_fecha($row["fecha_fin_beca"]);
							$incluye_fecha_fin_beca_checked = " checked ";
							}
						else 
							{
							$fecha_fin_beca = "";
							$incluye_fecha_fin_beca_checked = "";						
							}

						$tema = $row["tema"];
						$id_titulo_persona = $row["id_titulo_persona"];						
						$apellido_direccion = $row["apellido_direccion"];				
						$nombre_direccion = $row["nombre_direccion"];
						$lugar_beca = $row["lugar_beca"];
						$articulo_lugar_la_checked = "";
						$articulo_lugar_el_checked = "";
						if ($row["articulo_lugar"] == "la") 
							$articulo_lugar_la_checked = " checked ";
						else 
							$articulo_lugar_el_checked = " checked ";
						break; 
					case 5: //Horario
						$row = $bd->consultar_certificado_horario($id_certificado);
						$numero = $row['numero'];
						$anio = $row['anio'];
						$fecha_certificado = convertir_fecha($row["fecha_certificado"]);
						$apellido = $row["apellido"];				
						$nombre = $row["nombre"];
						$DNI = $row["DNI"];

						$id_escalafon = $row["id_escalafon"];						
						$id_escalafon_categoria = $row["id_escalafon_categoria"];
						$fecha_ini = convertir_fecha($row["fecha_ini"]); //fecha ingreso
						$id_unidad_ejecutora = $row["id_unidad_ejecutora"];

						$tema = $row["tema"];
						$id_titulo_persona = $row["id_titulo_persona"];						
						$lugar = $row["lugar"];

						if (!is_null($row["hora_ini_lunes"])){
							$hora_ini_lunes = $row["hora_ini_lunes"];
							$hora_fin_lunes = $row["hora_fin_lunes"];
							$hora_ini_martes = $row["hora_ini_martes"];
							$hora_fin_martes = $row["hora_fin_martes"];
							$hora_ini_miercoles = $row["hora_ini_miercoles"];
							$hora_fin_miercoles = $row["hora_fin_miercoles"];
							$hora_ini_jueves = $row["hora_ini_jueves"];
							$hora_fin_jueves = $row["hora_fin_jueves"];
							$hora_ini_viernes = $row["hora_ini_viernes"];
							$hora_fin_viernes = $row["hora_fin_viernes"];	
							$incluye_horarios_checked = " checked ";
							}
						else 
							{
							$hora_ini_lunes = "";
							$hora_fin_lunes = "";
							$hora_ini_martes = "";
							$hora_fin_martes = "";
							$hora_ini_miercoles = "";
							$hora_fin_miercoles = "";
							$hora_ini_jueves = "";
							$hora_fin_jueves = "";
							$hora_ini_viernes = "";
							$hora_fin_viernes = "";
							$incluye_horarios_checked = "";						
							}
						$articulo_lugar_la_checked = "";
						$articulo_lugar_el_checked = "";
						if ($row["articulo_lugar"] == "la") 
							$articulo_lugar_la_checked = " checked ";
						else 
							$articulo_lugar_el_checked = " checked ";
						break; 
						break; 
				}			
				break;			
		} // FIN SWITCH
?>
      </p>
      <form action="abm_certificado.php" method="post" enctype="multipart/form-data" name="form3" id="form3">
        <table align="center" class="tabla_form">
          <tr> 
            <td width="150" class="modo1"><div align="right">Tipo certificado</div></td>
		<td class="modo2"><div align="left">
            	<?php 
			echo "<select name='id_tipo_certificado' id='id_tipo_certificado' onchange='changeTipoCertificado();' $tipo_certificado_disabled>";
				$arrayTiposCertificados = $bd->getTiposCertificados();
				while ( $row = mysqli_fetch_array($arrayTiposCertificados) ){
					if ($row['id_tipo_certificado'] == $id_tipo_certificado){
						echo '<option selected value='. $row['id_tipo_certificado'] .'>'.  $row['nombre'] .'</option>';
					}else{
						echo '<option value='. $row['id_tipo_certificado'] .'>'. $row['nombre'] .'</option>';	
					}
				}
			echo '</select>';
			echo "<input type='hidden' name='id_tipo_certificado2' id='id_tipo_certificado2' value='$id_tipo_certificado'>";
		?>
		</div></td>
          </tr>
          <tr> 
            <td class="modo1"><div align="right">N&uacute;mero certificado</div></td>
            <?php 
		echo '<td class="modo2"><div align="left"><input name="numero_certificado" type="text" id="numero_certificado" value="' . $numero .'"' . 'size="5" maxlength="25" disabled>';
   		echo '<input name="anio" type="text" id="anio" value="' . $anio .'"' . 'size="5" maxlength="5" disabled></td>';
	  ?>
          </tr>
          <tr> 
            <td class="modo1"  valign="top"><div align="right"><br/>Texto del certificado</div></td>
	    <td class="modo3" >
		<div id="obra_social" style="display:block">
			<div align="left">
				CERTIFICO por intemedio del presente que 
				<?php echo '<select name="id_titulo_persona_OS">';
							$arrayTitulosPersonas = $bd->getTitulosPersonas("1");
							while ( $row = mysqli_fetch_array($arrayTitulosPersonas) ){
								if ($row['id_titulo_persona'] == $id_titulo_persona){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_titulo_persona'] .'>'.  $row['titulo_persona'] .'</option>';
							}
				echo '</select>';?>
				<input name="apellido_OS" type="text" id="apellido_OS" placeholder="apellido" value="<?php echo $apellido;?>" size="15" maxlength="100">
				<input name="nombre_OS" type="text" id="nombre_OS" placeholder="nombre" value="<?php echo $nombre;?>" size="20" maxlength="250">, con Documento Nacional de Identidad N&ordm; 
				<input name="DNI_OS" type="text" id="DNI_OS" placeholder="DNI (sin puntos)" value="<?php echo $DNI;?>" size="7" maxlength="100">
				, Clave &Uacute;nica de Identificaci&oacute;n Laboral N&ordm;
				<input name="CUIL_OS" type="text" id="CUIL_OS" placeholder="CUIL (sin guiones)" value="<?php echo $CUIL;?>" size="12" maxlength="100">
				, reviste como miembro de la
				<?php echo '<select name="id_escalafon_categoria_OS">';
							$arrayEscalafonCategorias = $bd->getEscalafonCategorias('1,2,3,4');
							while ( $row = mysqli_fetch_array($arrayEscalafonCategorias) ){
								if ($row['id_escalafon_categoria'] == $id_escalafon_categoria){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_escalafon_categoria'] .'>'.  $row['nombre'] .'</option>';
							}
				echo '</select>';?>, de este Consejo Nacional de Investigaciones Cient&iacute;ficas y T&eacute;cnicas (CONICET). 
				De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del d&iacute;a 
				<input name="fecha_ingreso_OS" type="text" id="fecha_ingreso_OS" placeholder="fecha ingreso" value="<?php echo $fecha_ingreso;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha ingreso" id="lanzador_FI_OS">.
				<br/><br/>
				Se llevan a cabo los correspondientes aportes de Ley. En cuanto a la Obra Social, los aportes se realizan a
				la Obra Social de la Uni&oacute;n del Personal Civil de la Naci&oacute;n N&ordm; 125707-7.
				<br/><br/>
				A solicitud del interesado y al solo efecto de ser presentado ante quien corresponda, se extiende el presente
				certificado en la ciudad de Mar del Plata el <input name="fecha_certificado_OS" type="text" id="fecha_certificado_OS" placeholder="fecha certificado" value="<?php echo $fecha_certificado;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha certificado" id="lanzador_FC_OS">.
				<br/><br/>	
			</div>
		</div> 
		<div id="unificacion" style="display:none">
			<div align="right">Mar del Plata, &nbsp;
		    		<input name="fecha_certificado_U" type="text" id="fecha_certificado_U" placeholder="fecha certificado" value="<?php echo $fecha_certificado;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha certificado" id="lanzador_FC_U">
			</div>
			
			<div align="left">
				<br/>
				<br/>
				SE&Ntilde;ORES<br/>
				SUPERINTENDENCIA DE SERVICIOS DE SALUD<br/>
				PRESENTE<br/>
				<br/>
				<br/>

				CERTIFICO por intemedio del presente que 
				<?php echo '<select name="id_titulo_persona_U">';
							$arrayTitulosPersonas = $bd->getTitulosPersonas("1");
							while ( $row = mysqli_fetch_array($arrayTitulosPersonas) ){
								if ($row['id_titulo_persona'] == $id_titulo_persona){
									echo '<option selected value='. $row['id_titulo_persona'] .'>'.  $row['titulo_persona'] .'</option>';
								}else{
									echo '<option value='. $row['id_titulo_persona'] .'>'. $row['titulo_persona'] .'</option>';	
								}
							}
				echo '</select>';?>
				<input name="apellido_U" type="text" id="apellido_U" placeholder="apellido" value="<?php echo $apellido;?>" size="15" maxlength="100">
				<input name="nombre_U" type="text" id="nombre_U" placeholder="nombre" value="<?php echo $nombre;?>" size="20" maxlength="250">
				, con Documento Nacional de Identidad N&ordm; 
				<input name="DNI_U" type="text" id="DNI_U" placeholder="DNI (sin puntos)" value="<?php echo $DNI;?>" size="7" maxlength="100">
				, Clave &Uacute;nica de Identificaci&oacute;n Laboral N&ordm;
				<input name="CUIL_U" type="text" id="CUIL_U" placeholder="CUIL (sin guiones)" value="<?php echo $CUIL;?>" size="12" maxlength="100">
				, es personal del Consejo Nacional de Investigaciones Cient&iacute;ficas y T&eacute;cnicas (CONICET). 
				De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del d&iacute;a 
				<input name="fecha_ingreso_U" type="text" id="fecha_ingreso_U" placeholder="fecha ingreso" value="<?php echo $fecha_ingreso;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha ingreso" id="lanzador_FI_U">.<br/>
				Por dicha actividad le corresponde la Obra Social de la Uni&oacute;n del Personal Civil de la Naci&oacute;n CODIGO RNOS (125707 - UP - UNION PERSONAL).<br/>
				<br/>		
			</div>
		</div> 
		<div id="antiguedad" style="display:none">
			<div align="left">
				CERTIFICO por intemedio del presente que 
				<?php echo '<select name="id_titulo_persona_A">';
							$arrayTitulosPersonas = $bd->getTitulosPersonas("1");
							while ( $row = mysqli_fetch_array($arrayTitulosPersonas) ){
								if ($row['id_titulo_persona'] == $id_titulo_persona){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_titulo_persona'] .'>'.  $row['titulo_persona'] .'</option>';
							}
				echo '</select>';?>
				<input name="apellido_A" type="text" id="apellido_A" placeholder="apellido" value="<?php echo $apellido;?>" size="15" maxlength="100">
				<input name="nombre_A" type="text" id="nombre_A" placeholder="nombre" value="<?php echo $nombre;?>" size="20" maxlength="250">, con Documento Nacional de Identidad N&ordm; 
				<input name="DNI_A" type="text" id="DNI_A" placeholder="DNI (sin puntos)" value="<?php echo $DNI;?>" size="7" maxlength="100">
				, Clave &Uacute;nica de Identificaci&oacute;n Laboral N&ordm;
				<input name="CUIL_A" type="text" id="CUIL_A" placeholder="CUIL (sin guiones)" value="<?php echo $CUIL;?>" size="12" maxlength="100">
				, reviste como miembro de la
				<?php echo '<select name="id_escalafon_categoria_A">';
							$arrayEscalafonCategorias = $bd->getEscalafonCategorias('1,2,3,4');
							while ( $row = mysqli_fetch_array($arrayEscalafonCategorias) ){
								if ($row['id_escalafon_categoria'] == $id_escalafon_categoria){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_escalafon_categoria'] .'>'.  $row['nombre'] .'</option>';
							}
				echo '</select>';?>, de este Consejo Nacional de Investigaciones Cient&iacute;ficas y T&eacute;cnicas (CONICET). 
				<br/><br/>
				De acuerdo a los registros obrantes en nuestras bases de datos la fecha de ingreso al organismo data del d&iacute;a 
				<input name="fecha_ingreso_A" type="text" id="fecha_ingreso_A" placeholder="fecha ingreso" value="<?php echo $fecha_ingreso;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha ingreso" id="lanzador_FI_A">.
				<br/>
				<!--Fecha egreso optativa-->
				<input name="incluye_fecha_egreso_A" id="incluye_fecha_egreso_A" type="checkbox" value="1" <?php echo $incluye_fecha_egreso_checked; ?> onchange="changeIncluyeFechaEgresoA()"> Incluir fecha de egreso<br/>
				<div id="div_fecha_egreso_A" style="display:none">
					y la fecha de egreso corresponde al d&iacute;a <input name="fecha_egreso_A" type="text" id="fecha_egreso_A" placeholder="fecha egreso" value="<?php echo $fecha_egreso;?>" size="8" maxlength="12">
					<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha egreso" id="lanzador_FE_A">.
				</div>				
				<br/>
				<!--Goce licencia optativo-->
				<input name="incluye_goce_licencia_A" id="incluye_goce_licencia_A" type="checkbox" value="1" <?php if ($goce_licencia) echo "checked"; ?> onchange="changeGoceLicenciaA()"> Incluir goce de licencia<br/>
				<div id="div_goce_licencia_A" style="display:none">
					As&iacute; mismo, en funci&oacute;n de lo que se desprende del Sistema Integral de Gesti&oacute;n de Recursos Humanos, la persona citada no ha gozado de licencias sin goce de haberes durante el per&iacute;odo laborado.
				</div>				
				<br/><br/>
				El presente se extiende a solicitud del interesado y al solo efecto de ser presentado ante quien corresponda, 
				en la ciudad de Mar del Plata el <input name="fecha_certificado_A" type="text" id="fecha_certificado_A" placeholder="fecha certificado" value="<?php echo $fecha_certificado;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha certificado" id="lanzador_FC_A">.
				<br/><br/>	
			</div>
		</div> 
		<div id="beca" style="display:none">
			<div align="left">
				CERTIFICO que 
				<input name="apellido_B" type="text" id="apellido_B" placeholder="apellido" value="<?php echo $apellido;?>" size="15" maxlength="100">
				<input name="nombre_B" type="text" id="nombre_B" placeholder="nombre" value="<?php echo $nombre;?>" size="20" maxlength="250">(DNI N&ordm; 
				<input name="DNI_B" type="text" id="DNI_B" placeholder="DNI sin puntos" value="<?php echo $DNI;?>" size="7" maxlength="100">
				) fue/es Becario de este Consejo Nacional de Investigaciones Cient&iacute;ficas y T&eacute;cnicas (CONICET),
				en la categor&iacute;a de 
				<?php echo '<select name="id_escalafon_categoria_B">';
							$arrayEscalafonCategorias = $bd->getEscalafonCategorias('5');
							while ( $row = mysqli_fetch_array($arrayEscalafonCategorias) ){
								if ($row['id_escalafon_categoria'] == $id_escalafon_categoria){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_escalafon_categoria'] .'>'.  $row['nombre'] .'</option>';
							}
				echo '</select>';?>, otorgada por resolucion D N&ordm;
				<input name="resolucion_B" type="text" id="resolucion_B" placeholder="resoluci&oacute;n" value="<?php echo $resolucion;?>" size="5" maxlength="10">
				de fecha
				<input name="fecha_resolucion_B" type="text" id="fecha_resolucion_B" placeholder="fecha resoluci&oacute;n" value="<?php echo $fecha_resolucion;?>" size="7" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha resolucion" id="lanzador_FR_B">
				, desde el
				<input name="fecha_ini_beca_B" type="text" id="fecha_ini_beca_B" placeholder="fecha inicio" value="<?php echo $fecha_ini_beca;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha inicio beca" id="lanzador_FI_B">
				
				<!--Fecha fin optativa-->
				<br/>
				<input name="incluye_fecha_fin_beca_B" id="incluye_fecha_fin_beca_B" type="checkbox" value="1" <?php echo $incluye_fecha_fin_beca_checked; ?> onchange="changeIncluyeFechaFinBecaB()"> Incluir fecha de fin de beca<br/>
				<div id="div_fecha_fin_beca_B" style="display:none">
					hasta el <input name="fecha_fin_beca_B" type="text" id="fecha_fin_beca_B" placeholder="fecha fin" value="<?php echo $fecha_fin_beca;?>" size="8" maxlength="12">
					<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha fin beca" id="lanzador_FF_B">
				</div>
				per&iacute;odo en el cual desarroll&oacute;/desarrolla tareas de investigaci&oacute;n sobre el tema 
				<textarea name="tema_B" id="tema_B" cols="59" rows="3" maxlength="500" placeholder="Tema de investigaci&oacute;n"><?php echo $tema;?></textarea>
				<br/>bajo la direcci&oacute;n de 
				<?php echo '<select name="id_titulo_persona_B">';
							$arrayTitulosPersonas = $bd->getTitulosPersonas("2");
							while ( $row = mysqli_fetch_array($arrayTitulosPersonas) ){
								if ($row['id_titulo_persona'] == $id_titulo_persona){
									echo '<option selected value=';
								}else{
									echo '<option value=';
								}
								echo $row['id_titulo_persona'] .'>'.  $row['titulo_persona'] .'</option>';
							}
				echo '</select>';?>
				<input name="apellido_direccion_B" type="text" id="apellido_direccion_B" placeholder="apellido" value="<?php echo $apellido_direccion;?>" size="10" maxlength="100">
				<input name="nombre_direccion_B" type="text" id="nombre_direccion_B" placeholder="nombre" value="<?php echo $nombre_direccion;?>" size="20" maxlength="250">
				<br/>en 
				<input type="radio" name="articulo_lugar_B" value="la" <?php echo $articulo_lugar_la_checked;?>>la&nbsp;/
				<input type="radio" name="articulo_lugar_B" value="el" <?php echo $articulo_lugar_el_checked;?>>el
				<textarea name="lugar_beca_B" id="lugar_beca_B" cols="59" rows="3" maxlength="250" placeholder="Lugar"><?php echo $lugar_beca;?></textarea>
				<br/>
				A solicitud del interesado y al solo efecto de ser presentado ante quien corresponda, se extiende el presente certificado
				en la ciudad de Mar del Plata el <input name="fecha_certificado_B" type="text" id="fecha_certificado_B" placeholder="fecha certificado" value="<?php echo $fecha_certificado;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha certificado" id="lanzador_FC_B">.
				<br/><br/>	
			</div>
		</div> 
		<div id="horarios" style="display:none">
			<div align="left">
				CERTIFICO que 
				<?php echo '<select name="id_titulo_persona_H" id="id_titulo_persona_H">';
					$arrayTitulosPersonas = $bd->getTitulosPersonas("1,2");
					while ( $row = mysqli_fetch_array($arrayTitulosPersonas) ){
						if ($row['id_titulo_persona'] == $id_titulo_persona){
							echo '<option selected value=';
						}else{
							echo '<option value=';
						}
						echo $row['id_titulo_persona'] .'>'.  $row['titulo_persona'] .'</option>';
					}
				echo '</select>';?>
				<input name="apellido_H" type="text" id="apellido_H" placeholder="apellido" value="<?php echo $apellido;?>" size="15" maxlength="100">
				<input name="nombre_H" type="text" id="nombre_H" placeholder="nombre" value="<?php echo $nombre;?>" size="20" maxlength="250">(DNI N&ordm; 
				<input name="DNI_H" type="text" id="DNI_H" placeholder="DNI sin puntos" value="<?php echo $DNI;?>" size="7" maxlength="100">
				) es miembro de la 
				<?php echo '<select name="id_escalafon_H" id="id_escalafon_H" >';
					$arrayEscalafones = $bd->getEscalafon('1,2,3');
					while ( $row = mysqli_fetch_array($arrayEscalafones) ){
						if ($row['id_escalafon'] == $id_escalafon){
							echo '<option selected value=';
						}else{
							echo '<option value=';
						}
						echo $row['id_escalafon'] .'>'.  $row['nombre'] .'</option>';
					}
				echo '</select>';?>
				de este Consejo Nacional de Investigaciones Cient&iacute;ficas y T&eacute;cnicas, desde el
				<input name="fecha_ini_H" type="text" id="fecha_ini_H" placeholder="fecha ingreso" value="<?php echo $fecha_ini;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha inicio" id="lanzador_FI_H">
				,<br/>revistando actualmente en la categor&iacute;a de 
				<?php echo '<select name="id_escalafon_categoria_H" id="id_escalafon_categoria_H">';
					$arrayEscalafonCategorias = $bd->getEscalafonCategorias2('1,2,3');
					while ( $row = mysqli_fetch_array($arrayEscalafonCategorias) ){
						if ($row['id_escalafon_categoria'] == $id_escalafon_categoria){
							echo "<option selected value=";
						}else{
							echo "<option value=";
						}
						echo $row['id_escalafon_categoria'] .' id_escalafon='.$row['id_escalafon'].'>'.  $row['nombre'] .'</option>';
					}
				echo '</select>';?>
				,<br/> con lugar de trabajo autorizado en
				<input type="radio" name="articulo_lugar_H" value="la" <?php echo $articulo_lugar_la_checked;?>>la&nbsp;/
				<input type="radio" name="articulo_lugar_H" value="el" <?php echo $articulo_lugar_el_checked;?>>el
				<textarea name="lugar_H" id="lugar_H" cols="59" rows="3" maxlength="250" placeholder="Lugar (obligatorio solo si la unidad es Zona de Influencia)"><?php echo $lugar;?></textarea>
				<?php echo '<select name="id_unidad_H" id="id_unidad_H">';
					$arrayUnidades = $bd->getUEs($id_unidad_ejecutora);
					foreach($arrayUnidades as $row){
					//while ( $row = mysqli_fetch_array($arrayUnidades) ){
						if ($row['id_unidad_ejecutora'] != 13) { //Ocultar opcion CCT MdP
							if ($row['id_unidad_ejecutora'] == $id_unidad_ejecutora){
								echo '<option selected value=';
							}else{
								echo '<option value=';
							}
							echo $row['id_unidad_ejecutora'] .'>'.  $row['nombre'] .'</option>';
						}
					}
				echo '</select>';?>
				del CENTRO CIENTIFICO TECNOL&Oacute;GICO CONICET MAR DEL PLATA (CCT - CONICET - MDP).</br></br>
				<div id="div_tema_H" style="display:none">
					... desarrollando el tema de investigaci&oacute;n: 
					<textarea name="tema_H" id="tema_H" cols="59" rows="3" maxlength="500" placeholder="Tema de investigaci&oacute;n"><?php echo $tema;?></textarea>
				</div>
				<input name="incluye_horarios_H" id="incluye_horarios_H" type="checkbox" value="1" <?php echo $incluye_horarios_checked; ?> onchange="changeIncluyeHorariosH()"> Incluir horarios<br/>
				<br/>
				<div id="div_horarios_H" style="display:none">
					Seg&uacute;n obra en nuestros registros, desempe&ntilde;a sus tareas en los siguientes d&iacute;as y horarios:
					lunes de 
					<input name="hora_ini_lunes_H" type="text" id="hora_ini_lunes_H" placeholder="00:00:00" value="<?php echo $hora_ini_lunes;?>" size="8" maxlength="8">
					a
					<input name="hora_fin_lunes_H" type="text" id="hora_fin_lunes_H" placeholder="00:00:00" value="<?php echo $hora_fin_lunes;?>" size="8" maxlength="8">
					hs., martes de
					<input name="hora_ini_martes_H" type="text" id="hora_ini_martes_H" placeholder="00:00:00" value="<?php echo $hora_ini_martes;?>" size="8" maxlength="8">
					a
					<input name="hora_fin_martes_H" type="text" id="hora_fin_martes_H" placeholder="00:00:00" value="<?php echo $hora_fin_martes;?>" size="8" maxlength="8">
					hs., mi&eacute;rcoles de
					<input name="hora_ini_miercoles_H" type="text" id="hora_ini_miercoles_H" placeholder="00:00:00" value="<?php echo $hora_ini_miercoles;?>" size="8" maxlength="8">
					a
					<input name="hora_fin_miercoles_H" type="text" id="hora_fin_miercoles_H" placeholder="00:00:00" value="<?php echo $hora_fin_miercoles;?>" size="8" maxlength="8">
					hs., jueves de
					<input name="hora_ini_jueves_H" type="text" id="hora_ini_jueves_H" placeholder="00:00:00" value="<?php echo $hora_ini_jueves;?>" size="8" maxlength="8">
					a
					<input name="hora_fin_jueves_H" type="text" id="hora_fin_jueves_H" placeholder="00:00:00" value="<?php echo $hora_fin_jueves;?>" size="8" maxlength="8">
					hs., viernes de
					<input name="hora_ini_viernes_H" type="text" id="hora_ini_viernes_H" placeholder="00:00:00" value="<?php echo $hora_ini_viernes;?>" size="8" maxlength="8">
					a
					<input name="hora_fin_viernes_H" type="text" id="hora_fin_viernes_H" placeholder="00:00:00" value="<?php echo $hora_fin_viernes;?>" size="8" maxlength="8">
					hs.
				</div>
				<br/>
				A solicitud del interesado y al solo efecto de ser presentado ante quien corresponda, se extiende el presente certificado
				en la ciudad de Mar del Plata el <input name="fecha_certificado_H" type="text" id="fecha_certificado_H" placeholder="fecha certificado" value="<?php echo $fecha_certificado;?>" size="8" maxlength="12">
				<img src="calendario/ima/calendario.png" width="16" height="16" border="0" title="Fecha certificado" id="lanzador_FC_H">.
				<br/><br/>
			</div>
		</div> 
	    </td>
          </tr>
          <tr> 
            <td colspan="2" class="modo1" align="center"></td>
          </tr>
          <script type="text/javascript"> 
  		Calendar.setup({ 
			inputField:    "fecha_certificado_U", // id del campo de texto 
			ifFormat  :    "%d-%m-%Y",     // formato de la fecha que se escriba en el campo de texto 
			button    :    "lanzador_FC_U"       // el id del boton que lanzará el calendario 
			}); 
   		Calendar.setup({ 
			inputField:    "fecha_ingreso_U", 
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FI_U"
			});		
   		Calendar.setup({ 
			inputField:    "fecha_certificado_OS",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FC_OS"
			}); 
   		Calendar.setup({ 
			inputField:    "fecha_ingreso_OS",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FI_OS"
			});	
   		Calendar.setup({ 
			inputField:    "fecha_certificado_A",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FC_A"
			}); 
   		Calendar.setup({ 
			inputField:    "fecha_ingreso_A",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FI_A"
			});	
   		Calendar.setup({ 
			inputField:    "fecha_egreso_A",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FE_A"
			});
   		Calendar.setup({ 
			inputField:    "fecha_ini_beca_B",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FI_B"
			});	
   		Calendar.setup({ 
			inputField:    "fecha_fin_beca_B",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FF_B"
			});
   		Calendar.setup({ 
			inputField:    "fecha_resolucion_B",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FR_B"
			});
   		Calendar.setup({ 
			inputField:    "fecha_certificado_B",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FC_B"
			}); 
   		Calendar.setup({ 
			inputField:    "fecha_ini_H",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FI_H"
			});
   		Calendar.setup({ 
			inputField:    "fecha_certificado_H",
			ifFormat  :    "%d-%m-%Y",
			button    :    "lanzador_FC_H"
			}); 

	</script>
        </table>
        <?php		
		echo "<input type=\"hidden\" name=\"opcion\" id=\"opcion\" value=\"$opcion\">";
		echo '<input type="hidden" name="numero" id="numero" value="'.$numero.'">';
		echo '<input type="hidden" name="anio" id="anio" value="'.$anio.'">';
		echo '<input type="hidden" name="id_certificado" id="id_certificado" value="'.$id_certificado.'">';

		switch ($opcion){
			case 1: // ALTA  				
				echo '<p align="center"><button type="button" name="btn_enviar" id="btn_enviar" onClick="enviar(form)" alt="Grabar datos"><img src="grabar_datos.png" width="30" heigth="30" border="0"></button></p>';				
				break;
			case 2: // BAJA 							
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Eliminar"><img src="eliminar.png" width="30" heigth="30" border="0"></button></p>';
				break;
			case 3: // MODIFICACION 				
				echo '<p align="center"><button type="button" class="boton" name="Btn_enviar" id="Btn_enviar" onClick="enviar(form)" alt="Actualizar datos"><img src="actualizar_datos.png" width="30" heigth="30" border="0"></button></p>';
				break;
		}	
	$bd = NULL;
	?>
      </form>
	</td>
  </tr>
</table>
<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="3" bgcolor="#000033" class="pie">Copyright &copy; 2010 CCT Mar del Plata. Todos los derechos reservados.</td>
  </tr>
</table>
<p>&nbsp;</p>
<script>
	function changeTipoCertificado(){
		var select = document.getElementById('id_tipo_certificado');
		var idTipoCertificado = select.options[select.selectedIndex].value;
		document.getElementById('id_tipo_certificado2').value = idTipoCertificado;
		switch (idTipoCertificado){
			case '1': //Obra Social
				document.getElementById('obra_social').style.display='block';
				document.getElementById('unificacion').style.display='none';				
				document.getElementById('antiguedad').style.display='none';	
				document.getElementById('beca').style.display='none';				
				document.getElementById('horarios').style.display='none';	
				break;
			case '2': //Unificacion de Aportes
				document.getElementById('obra_social').style.display='none';
				document.getElementById('unificacion').style.display='block';
				document.getElementById('antiguedad').style.display='none';	
				document.getElementById('beca').style.display='none';				
				document.getElementById('horarios').style.display='none';	
				break; 
			case '3': //Antiguedad
				document.getElementById('obra_social').style.display='none';
				document.getElementById('unificacion').style.display='none';
				document.getElementById('antiguedad').style.display='block';
				document.getElementById('beca').style.display='none';				
				document.getElementById('horarios').style.display='none';	
				break;
			case '4': //Beca
				document.getElementById('obra_social').style.display='none';
				document.getElementById('unificacion').style.display='none';
				document.getElementById('antiguedad').style.display='none';
				document.getElementById('beca').style.display='block';				
				document.getElementById('horarios').style.display='none';	
				break; 
			case '5': //Horarios y Lugar de trabajo
				document.getElementById('obra_social').style.display='none';
				document.getElementById('unificacion').style.display='none';
				document.getElementById('antiguedad').style.display='none';
				document.getElementById('beca').style.display='none';				
				document.getElementById('horarios').style.display='block';	
				break; 
		}
	}

	changeTipoCertificado();

	function changeIncluyeFechaEgresoA() {
		if (document.getElementById('incluye_fecha_egreso_A').checked)
			document.getElementById('div_fecha_egreso_A').style.display='block';
		else
			{document.getElementById('fecha_egreso_A').value='';
			document.getElementById('div_fecha_egreso_A').style.display='none';
			}
	}

	changeIncluyeFechaEgresoA();

	function changeGoceLicenciaA() {
		if (document.getElementById('incluye_goce_licencia_A').checked)
			document.getElementById('div_goce_licencia_A').style.display='block';
		else
			document.getElementById('div_goce_licencia_A').style.display='none';
	}

	changeGoceLicenciaA();

	function changeIncluyeFechaFinBecaB() {
		if (document.getElementById('incluye_fecha_fin_beca_B').checked)
			document.getElementById('div_fecha_fin_beca_B').style.display='block';
		else
			{document.getElementById('fecha_fin_beca_B').value='';
			document.getElementById('div_fecha_fin_beca_B').style.display='none';
			}
	}

	changeIncluyeFechaFinBecaB();

	function changeIncluyeHorariosH() {
		if (document.getElementById('incluye_horarios_H').checked)
			document.getElementById('div_horarios_H').style.display='block';
		else {
			document.getElementById('div_horarios_H').style.display='none';
			document.getElementById('hora_ini_lunes_H').value='';
			document.getElementById('hora_fin_lunes_H').value='';
			document.getElementById('hora_ini_martes_H').value='';
			document.getElementById('hora_fin_martes_H').value='';
			document.getElementById('hora_ini_miercoles_H').value='';
			document.getElementById('hora_fin_miercoles_H').value='';
			document.getElementById('hora_ini_jueves_H').value='';
			document.getElementById('hora_fin_jueves_H').value='';
			document.getElementById('hora_ini_viernes_H').value='';
			document.getElementById('hora_fin_viernes_H').value='';
			}
	}

	changeIncluyeHorariosH();

	
	$("#id_escalafon_H").change(function () {

		$("#id_escalafon_categoria_H").children('option').hide();
		$("#id_escalafon_categoria_H").children("option[id_escalafon^=" + $(this).val() + "]").show();
		//que NO se ejecute esta linea al cargar el form (sobre todo cuando es modificacion)
		$("#id_escalafon_categoria_H").children("option[id_escalafon^=" + $(this).val() + "]").attr("selected","selected");

		var select = document.getElementById('id_escalafon_H');
		var id_escalafon_H = select.options[select.selectedIndex].value;
		if (id_escalafon_H == '2'){ //Es investigador, debe especificar tema
			document.getElementById('div_tema_H').style.display='block';	
		}
		else {
			document.getElementById('div_tema_H').style.display='none';	
		}
	});
	
	$("#id_escalafon_categoria_H").children('option').hide();
	$("#id_escalafon_categoria_H").children("option[id_escalafon^=" + $("#id_escalafon_H").val() + "]").show();
	if (document.form3.opcion.value == 1) //Alta
		$("#id_escalafon_categoria_H").children("option[id_escalafon^=" + $(this).val() + "]").attr("selected","selected");

	var select = document.getElementById('id_escalafon_H');
	var id_escalafon_H = select.options[select.selectedIndex].value;
	if (id_escalafon_H == '2'){ //Es investigador, debe especificar tema
		document.getElementById('div_tema_H').style.display='block';	
	}
	else {
		document.getElementById('div_tema_H').style.display='none';	
	}

	$(function() {  
	    $("textarea[maxlength]").bind('input propertychange', function() {  
		var maxLength = $(this).attr('maxlength');  
		if ($(this).val().length > maxLength) {  
		    $(this).val($(this).val().substring(0, maxLength));  
		}  
	    })  
	});

</script>
</body>
</html>

<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];

	$bd = new Bd;
	$bd->AbrirBd();

	$id_certificado = $_POST["id_certificado"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		//Datos en comun para todos los tipos		
		$anio = $_POST["anio"];	
		$numero = $_POST["numero"];
		$id_tipo_certificado = $_POST["id_tipo_certificado2"];
		//Dependiendo el tipo de certificado seran unos datos u otros
		switch ($id_tipo_certificado) {
			case 1: //Obra Social
				$fecha_certificado = convertir_fecha_sql($_POST["fecha_certificado_OS"]);
				$apellido = trim($_POST["apellido_OS"]);
				$nombre = trim($_POST["nombre_OS"]);
				$DNI = trim($_POST["DNI_OS"]);

				$id_titulo_persona = $_POST["id_titulo_persona_OS"];
				$CUIL = trim($_POST["CUIL_OS"]);
				$fecha_ingreso = convertir_fecha_sql($_POST["fecha_ingreso_OS"]);
				$id_escalafon_categoria = $_POST["id_escalafon_categoria_OS"];
				break;			
			case 2: //Unificacion Aportes
				$fecha_certificado = convertir_fecha_sql($_POST["fecha_certificado_U"]);
				$apellido = trim($_POST["apellido_U"]);
				$nombre = trim($_POST["nombre_U"]);
				$DNI = trim($_POST["DNI_U"]);	

				$id_titulo_persona = $_POST["id_titulo_persona_U"];
				$CUIL = trim($_POST["CUIL_U"]);
				$fecha_ingreso = convertir_fecha_sql($_POST["fecha_ingreso_U"]);
				break;
			case 3: //Antiguedad
				$fecha_certificado = convertir_fecha_sql($_POST["fecha_certificado_A"]);
				$apellido = trim($_POST["apellido_A"]);
				$nombre = trim($_POST["nombre_A"]);
				$DNI = trim($_POST["DNI_A"]);

				$id_titulo_persona = $_POST["id_titulo_persona_A"];
				$CUIL = trim($_POST["CUIL_A"]);
				$fecha_ingreso = convertir_fecha_sql($_POST["fecha_ingreso_A"]);
				$id_escalafon_categoria = $_POST["id_escalafon_categoria_A"];
				if ($_POST["fecha_egreso_A"] != "")
					$fecha_egreso = convertir_fecha_sql($_POST["fecha_egreso_A"]);
				else
					$fecha_egreso = "";
				if ($_POST["incluye_goce_licencia_A"] == '1')
					$goce_licencia = 1;
				else $goce_licencia = 0;
				break;			
			case 4: //Beca
				$fecha_certificado = convertir_fecha_sql($_POST["fecha_certificado_B"]);
				$apellido = $_POST["apellido_B"];
				$nombre = $_POST["nombre_B"];	
				$DNI = $_POST["DNI_B"];	

				$id_escalafon_categoria = $_POST["id_escalafon_categoria_B"];
				$resolucion = $_POST["resolucion_B"];
				$fecha_resolucion = convertir_fecha_sql($_POST["fecha_resolucion_B"]);
				$fecha_ini_beca = convertir_fecha_sql($_POST["fecha_ini_beca_B"]);
				if ($_POST["fecha_fin_beca_B"] != "")
					$fecha_fin_beca = convertir_fecha_sql($_POST["fecha_fin_beca_B"]);
				else
					$fecha_fin_beca = "";
				$tema = $_POST["tema_B"];
				$id_titulo_persona = $_POST["id_titulo_persona_B"];
				$apellido_direccion = $_POST["apellido_direccion_B"];
				$nombre_direccion = $_POST["nombre_direccion_B"];
				$articulo_lugar = $_POST["articulo_lugar_B"];
				$lugar_beca = $_POST["lugar_beca_B"];	
				break;			
			case 5: //Horarios de trabajo
				$fecha_certificado = convertir_fecha_sql($_POST["fecha_certificado_H"]);
				$apellido = $_POST["apellido_H"];
				$nombre = $_POST["nombre_H"];	
				$DNI = $_POST["DNI_H"];	

				$id_escalafon_categoria = $_POST["id_escalafon_categoria_H"];
				$fecha_ini = convertir_fecha_sql($_POST["fecha_ini_H"]);

				$tema = $_POST["tema_H"];
				$id_titulo_persona = $_POST["id_titulo_persona_H"];
				$articulo_lugar = $_POST["articulo_lugar_H"];
				$lugar = $_POST["lugar_H"];	
				$id_unidad_ejecutora = $_POST["id_unidad_H"];

				$hora_ini_lunes =  $_POST["hora_ini_lunes_H"];				
				$hora_fin_lunes =  $_POST["hora_fin_lunes_H"];
				$hora_ini_martes =  $_POST["hora_ini_martes_H"];				
				$hora_fin_martes =  $_POST["hora_fin_martes_H"];
				$hora_ini_miercoles =  $_POST["hora_ini_miercoles_H"];				
				$hora_fin_miercoles =  $_POST["hora_fin_miercoles_H"];
				$hora_ini_jueves =  $_POST["hora_ini_jueves_H"];				
				$hora_fin_jueves =  $_POST["hora_fin_jueves_H"];
				$hora_ini_viernes =  $_POST["hora_ini_viernes_H"];				
				$hora_fin_viernes =  $_POST["hora_fin_viernes_H"];
				break;			
		}

	}

	$result = false;
	$error = "";

	switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],32,'alta')){
				switch ($id_tipo_certificado) {
					case 1: //Obra Social
						$result = $bd->agregar_certificado_obra_social($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $error);
						break;
					case 2: //Unificacion Aportes
						$result = $bd->agregar_certificado_unificacion_aportes($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $error);
						break;
					case 3: //Antiguedad
						$result = $bd->agregar_certificado_antiguedad($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $fecha_egreso, $goce_licencia, $error);
						break;
					case 4: //Beca
						$result = $bd->agregar_certificado_beca($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
							$resolucion, $fecha_resolucion, $fecha_ini_beca, $fecha_fin_beca, $tema, 
							$id_titulo_persona, $apellido_direccion, $nombre_direccion, 
							$articulo_lugar, $lugar_beca, $error);
						break;
					case 5: //Horario
						$result = $bd->agregar_certificado_horario($anio, $id_tipo_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
							$fecha_ini, $tema, $id_titulo_persona, $articulo_lugar, $lugar, $id_unidad_ejecutora,
							$hora_ini_lunes, $hora_fin_lunes, $hora_ini_martes, $hora_fin_martes,
							$hora_ini_miercoles, $hora_fin_miercoles, $hora_ini_jueves, $hora_fin_jueves,
							$hora_ini_viernes, $hora_fin_viernes, $error);
						break;

				}
			}
			break;
		case 2: // BORRAR 
			// Para todos los tipos de certificado es igual
			if($bd->checkPerm($_SESSION["id_usuario"],32,'baja')){
				$result = $bd->borrar_certificado($id_certificado, $error);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],32,'modificacion')){
				switch ($id_tipo_certificado) {
					case 1: //Unificacion Aportes
						$result = $bd->modificar_certificado_obra_social($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $error);
						break;
					case 2: //Unificacion Aportes
						$result = $bd->modificar_certificado_unificacion_aportes($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $error);
						break;
					case 3: //Antiguedad
						$result = $bd->modificar_certificado_antiguedad($id_certificado,  
							$fecha_certificado, $apellido, $nombre, $DNI, $id_titulo_persona, $CUIL,
							$fecha_ingreso, $id_escalafon_categoria, $fecha_egreso, $goce_licencia, $error);
						break;
					case 4: //Beca
						$result = $bd->modificar_certificado_beca($id_certificado, 
							$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
							$resolucion, $fecha_resolucion, $fecha_ini_beca, $fecha_fin_beca, $tema, 
							$id_titulo_persona, $apellido_direccion, $nombre_direccion, 
							$articulo_lugar, $lugar_beca, $error);
						break;
					case 5: //Horario
						$result = $bd->modificar_certificado_horario($id_certificado,
							$fecha_certificado, $apellido, $nombre, $DNI, $id_escalafon_categoria,
							$fecha_ini, $tema, $id_titulo_persona, $articulo_lugar, $lugar, $id_unidad_ejecutora,
							$hora_ini_lunes, $hora_fin_lunes, $hora_ini_martes, $hora_fin_martes,
							$hora_ini_miercoles, $hora_fin_miercoles, $hora_ini_jueves, $hora_fin_jueves,
							$hora_ini_viernes, $hora_fin_viernes, $error);
						break;
				}
			}
			break;		
	}
	$bd = NULL;
	if (!$result){
		$_SESSION["message"]='No se grabaron los cambios, contacte con el administrador del sistema. Detalle del error:'.$error;
	}
	//echo $_SESSION["message"];
	header("Location: lista_certificados.php");
?>

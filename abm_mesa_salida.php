<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];
	$nombre_usuario = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];
	$sesion = NULL;
	/*echo "Datos de session: " . '<br>';
	echo $nombre_usuario . '<br>';
	echo $contrasenia . '<br>';
	echo $nivel_acceso . '<br>';*/

	$bd = new Bd;
	$bd->AbrirBd();

	$numero_remito = $_POST["numero_remito"];	

	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$numero_orden = $_POST["numero_orden"]??0;	
		$numero_tramite = $_POST["numero_tramite"]??0;	
		$remitente = $_POST["remitente"]??'';
		$documento = $_POST["documento"]??'';
		$destinatario = $_POST["destinatario"]??0;
		$copias = $_POST["copias"]??0;
		$cantidad_hojas = $_POST["cantidad_hojas"]??0;
		$firmante = $_POST["firmante"]??0;	
	} 

	$fecha = $_POST["fecha"];
	$vFecha = explode("-",convertir_fecha($fecha));
	$anio = (int)$vFecha[2];
	
	/*echo $numero_remito . '<br>';
	echo $fecha . '<br>';
	echo $numero_orden . '<br>';
	echo $numero_tramite . '<br>';
	echo $remitente . '<br>';
	echo $documento . '<br>';
	echo $destinatario . '<br>';
	echo $copias . '<br>';
	echo $cantidad_hojas . '<br>';		
	//break;	*/
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	switch ($opcion){
		case 1: // NUEVO REMITO
		CASE 4: // NUEVO MOVIMIENTO DE UN REMITO EXISTENTE
			//if($row_usuario['ms_alta'] == 1){
			
			if($bd->checkPerm($_SESSION["id_usuario"],2,'alta')){
				//$fecha = convertir_fecha($fecha);
				$fecha = convertir_fecha_sql($fecha);	
				$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];	
				if($opcion == 1){
					$numero_remito = $bd->getConfig('last_ms_id');
				}
				$bd->agregar_mesa_salida($numero_remito, $fecha, $numero_orden, $numero_tramite, $remitente, $documento, $destinatario, $copias, $cantidad_hojas, $confecciono, $firmante);
				if($opcion == 1){
					$bd->setConfig('last_ms_id');
				}
			}
			header("Location: form_mesa_salida.php?opcion=4&numero_remito=$numero_remito&anio=$anio");	
			break;
		case 2: // BORRAR 
			//if($row_usuario['ms_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],2,'baja')){
				$bd->borrar_mesa_salida($numero_remito, $anio);
			}
			header("Location: lista_mesa_salida.php");			
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['ms_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],2,'modificacion')){
				//$fecha = convertir_fecha($fecha);		
				$fecha = convertir_fecha_sql($fecha);	
				$bd->modificar_mesa_salida($numero_remito, $fecha, $numero_orden, $numero_tramite, $remitente, $documento, $destinatario, $copias, $cantidad_hojas, $firmante);
			}
			header("Location: form_mesa_salida.php?opcion=4&numero_remito=$numero_remito&anio=$anio");
			break;
		case 6:
			header("Location: mesa_salida_pdf.php?numero_remito=$numero_remito&fecha=$fecha&anio=$anio");
			break;
	}
	$bd = NULL;
	
?>

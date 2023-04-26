<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion2 = $_POST["opcion2"];	
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
	$fecha = $_POST["fecha2"];
	$firmante = $_POST["firmante2"];		
	$anio = $_POST["anio"];		
	
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
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];
	switch ($opcion2){
		case 1: // NUEVO REMITO
			//if($row_usuario['ms_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],2,'alta')){
				$fecha = convertir_fecha($fecha);
				$numero_remito = $bd->getConfig('last_ms_id');
				$bd->agregar_mesa_salida_encabezado($numero_remito, $fecha, $confecciono, $firmante);
			}
			header("Location: form_mesa_salida.php?opcion=4&numero_remito=$numero_remito");	
			break;
		case 3: // ACTUALIZAR 
			//if($row_usuario['ms_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],2,'modificacion')){
				$fecha = convertir_fecha($fecha);		
				$bd->modificar_mesa_salida_encabezado($numero_remito, $fecha, $confecciono, $firmante, $anio);
			}
			header("Location: form_mesa_salida.php?opcion=4&numero_remito=$numero_remito&anio=$anio");
			break;
	}
	$bd = NULL;
?>

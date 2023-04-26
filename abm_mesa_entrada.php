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
	*/

	$bd = new Bd;
	$bd->AbrirBd();

	$numero_orden = $_POST["numero_orden"]??0;	
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$numero_tramite = $_POST["numero_tramite"];	
		$anio_numero_tramite = $_POST["anio_numero_tramite"];			
		$fecha = $_POST["fecha"];
		$remitente = $_POST["remitente"];
		$documento = $_POST["documento"];
		$destinatario = $_POST["destinatario"];
		$cantidad = $_POST["cantidad"];
		$observaciones = $_POST["observaciones"];
		$firmante = $_POST["firmante"];			
	}
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	switch ($opcion){
		case 1: // NUEVO
			//if($row_usuario['me_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],1,'alta')){
				$row = $bd->consultar_mesa_entrada($numero_orden);
				if ($row["numero_orden"] == ""){
					//$fecha = convertir_fecha($fecha);
					$fecha = convertir_fecha_sql($fecha);	
					$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];	
					$numero_tramite = $bd->getConfig('last_me_id');
					$bd->agregar_mesa_entrada($numero_tramite, $anio_numero_tramite, $fecha, $remitente, $documento, $destinatario, $cantidad, $observaciones, $confecciono, $firmante);
					$bd->setConfig('last_me_id');
					break;
				}else
				{
					$error_mesa_entrada = 1;
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['me_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],1,'baja')){
				$bd->borrar_mesa_entrada($numero_orden);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['me_modificacion'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],1,'modificacion')){
				//$fecha = convertir_fecha($fecha);
				$fecha = convertir_fecha_sql($fecha);			
				$bd->modificar_mesa_entrada($numero_orden, $numero_tramite, $anio_numero_tramite, $fecha, $remitente, $documento, $destinatario, $cantidad, $observaciones, $firmante);
			}
			break;
	}
	$bd = NULL;
	if ($error_mesa_entrada == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El Número de trámite ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_mesa_entrada.php");
	}
?>

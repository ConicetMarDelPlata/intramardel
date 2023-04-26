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

	$id_unidad_ejecutora = $_POST["id_unidad_ejecutora"];
	if ($opcion == 1 or $opcion==3){ 
		$cuit = $_POST["cuit"];	
		$iibb = $_POST["iibb"];			
		$nombre = $_POST["nombre"];
		$nombre_completo = $_POST["nombre_completo"];
		$domicilio = $_POST["domicilio"];
		$telefono = $_POST["telefono"];		
		$referente = $_POST["referente"];
		$mail_referente = $_POST["mail_referente"];
		$director = $_POST["director"];
		$mail_director = $_POST["mail_director"];
		if (!$_POST["agente_retencion"]) {
			$agente_retencion = 0;
		} else {
			$agente_retencion = $_POST["agente_retencion"];}
		
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['ue_alta'] == 1){	
			if($bd->checkPerm($_SESSION["id_usuario"],15,'alta')){	
				$row = $bd->consultar_unidad_ejecutora($cuit);
				if ($row["cuit"] == ""){
					$bd->agregar_unidad_ejecutora($nombre, $nombre_completo, $cuit, $domicilio, $telefono, $referente, $mail_referente, $director, $mail_director, $agente_retencion, $iibb);
					header("Location: lista_unidades_ejecutoras.php");
					break;
				}else
				{
					echo "El CUIT de la Unidad Ejecutora elegido ya existe, por favor elija otro y carguelo nuevamente.";
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['ue_baja'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],15,'baja')){
				$bd->borrar_unidad_ejecutora($id_unidad_ejecutora);
			}
			header("Location: lista_unidades_ejecutoras.php");
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['ue_modificacion'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],15,'modificacion')){
				$bd->modificar_unidad_ejecutora($id_unidad_ejecutora, $nombre, $nombre_completo, $cuit, $domicilio, $telefono, $referente, $mail_referente, $director, $mail_director, $agente_retencion, $iibb);
			}
			header("Location: lista_unidades_ejecutoras.php");
			break;
		case 5: //NUEVA CUENTA
			if($bd->checkPerm($_SESSION["id_usuario"],15,'alta')){	
				$nroCuenta = $_POST["nroCuenta"];	
				if (!$bd->agregar_cta_unidad_ejecutora($id_unidad_ejecutora, $nroCuenta)) {
					$_SESSION["message"]='No se grabaron los cambios.';
				}
			}
			header("Location: form_unidad_ejecutora.php?id_unidad_ejecutora=$id_unidad_ejecutora&opcion=3");
			break;
		case 6: //ELIMINAR CUENTA
			if($bd->checkPerm($_SESSION["id_usuario"],15,'baja')){	
				$idCuenta = $_POST["idCuenta"];	
				if (!$bd->eliminar_cta_unidad_ejecutora($idCuenta)) {
					$_SESSION["message"]='No se grabaron los cambios.';
				}
			}
			header("Location: form_unidad_ejecutora.php?id_unidad_ejecutora=$id_unidad_ejecutora&opcion=3");
			break;
		case 7: //MODIFICAR CUENTA
			if($bd->checkPerm($_SESSION["id_usuario"],15,'modificacion')){	
				$idCuenta = $_POST["idCuenta"];	
				$nroCuenta = $_POST["nroCuenta"];
				if (!$bd->modificar_cta_unidad_ejecutora($idCuenta, $nroCuenta)) {
					$_SESSION["message"]='No se grabaron los cambios.';
				}
			}
			header("Location: form_unidad_ejecutora.php?id_unidad_ejecutora=$id_unidad_ejecutora&opcion=3");
			break;

	}
?>

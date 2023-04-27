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

	$id_tramite = $_POST["id_tramite"];
	$id_estado = $_POST["id_estado"];
	$enviar_email = false;
	$comprobantes = array();
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$enviar_email = $_POST["enviar_email"];
		$enviar_email = $enviar_email === 'true'? true: false;
		if ($id_estado == 1) {
			//Datos en comun para todos los tipos		
			$anio = $_POST["anio"];	
			$numero = $_POST["numero"];
			$fecha_inicio = convertir_fecha_sql($_POST["fecha_inicio"]);
			$rendicion = $_POST["rendicion"];
			$id_titular_proyecto = $_POST["id_titular_proyecto"];	
			$id_titular_adm_proyecto = $_POST["id_titular_adm_proyecto"];
			$id_titular_adm_proyecto = $id_titular_adm_proyecto === '-1'? "NULL": $id_titular_adm_proyecto;
			$motivo_tramite = $_POST["motivo_tramite"]; 
			$rendicion_codigo = $_POST["rendicion_codigo"]; //solo se completa si motivo tramite es 2
			$observaciones = $_POST["observaciones"]; 
			if (isset($_POST["firma_realizada"])) 
				$firma_realizada = $_POST["firma_realizada"]; //solo se completa si motivo tramite es 2
			else 
				$firma_realizada = 0;
			if ($motivo_tramite == "1") {
				//Comprobantes asociados
				//Orden es la cantidad maxima de comprobantes, que pueden estar o no por haberse eliminado
				$orden = $_POST["orden"];
				for ($i = 1; $i <= $orden; $i++) {
					if (isset($_POST["comprobante".$i])) {
						unset($reclamos);
						$reclamos = array();
						//Mandó datos de este comprobante
						$comprobante['comprobante'] = $_POST["comprobante".$i];
						$comprobante['monedacomprobante'] = $_POST["monedacomprobante".$i];
						$comprobante['montocomprobante'] = $_POST["montocomprobante".$i];
						$comprobante['fechacomprobante'] = $_POST["fechacomprobante".$i];
						$comprobante['proveedorid'] = $_POST["proveedor".$i];
						$comprobante['proveedorcomprobante'] = $_POST["proveedorcomprobante".$i];
						$comprobante['destino']  = $_POST["destino".$i];
						$comprobante['monto']  = $_POST["monto".$i];
						$comprobante['motivo']  = $_POST["motivo".$i];
						//Recorro los tipos de reclamo chequeados
						foreach($_POST["tipo_reclamo".$i] as $id_tramite_reclamo_tipo) {
							$reclamos[] = $id_tramite_reclamo_tipo; 
						}
						$comprobante['reclamos'] = $reclamos;
						$comprobantes[] = $comprobante;
					}
				}
			} //if ($motivo_tramite == "1") 
		} else {
			//Si ya fue enviado el email, solo puede tildar si fue presentado o no un reclamo
			//Y solo se actualizan esos datos sin tocarse los otros
			//motivo 2
			$rendicion_codigo = $_POST["rendicion_codigo"]??null; //solo se completa si motivo tramite es 2
			$observaciones = $_POST["observaciones"]; 
			if (isset($_POST["firma_realizada"])) 
				$firma_realizada = $_POST["firma_realizada"]??null; //solo se completa si motivo tramite es 2
			else 
				$firma_realizada = 0;
			//motivo 1
			$orden = $_POST["orden"];
			$comprobantes[] = array();
			//print_r($comprobantes);
			for ($i = 1; $i <= $orden; $i++) {
				if (isset($_POST["id_tramite_comprobante".$i])) {
					unset($reclamos);
					$reclamos = array();
					//Mando datos de este comprobante
					$comprobante['id_tramite_comprobante'] = $_POST["id_tramite_comprobante".$i];
					//Recorro los tipos de reclamo chequeados (puede ser que no sea ninguno)
					if (isset($_POST["presentado".$i])) {
						foreach($_POST["presentado".$i] as $id_tramite_reclamo_tipo) {
							$reclamos[] = $id_tramite_reclamo_tipo; 
						}
						$comprobante['reclamos'] = $reclamos;
						//Si no chequeo reclamos no envio el comprobante
						$comprobantes[] = $comprobante;
					}
				}
			} 
		}
	}

	$result = false;
	$error = "";
	$error2 = "";

	switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],33,'alta')){
				$result = $bd->agregar_tramite($anio, $fecha_inicio, $rendicion, $id_titular_proyecto,
						$id_titular_adm_proyecto, $motivo_tramite, $rendicion_codigo, $observaciones,
						$comprobantes, $_SESSION["id_usuario"], $id_tramite, $error);

				if ($result and $enviar_email) {
					$result2 = $bd->enviar_email_tramite($id_tramite, $_SESSION["id_usuario"], $error2);
					if ($result2) {
						$_SESSION["message"] = "El mensaje se envió correctamente.";
					}
				}
			}
			break;
		case 2: // BORRAR 
			if($bd->checkPerm($_SESSION["id_usuario"],33,'baja')){
				$result = $bd->borrar_tramite($id_tramite, $error);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],33,'modificacion')){
				if ($id_estado == 1) {
					$result = $bd->modificar_tramite($id_tramite, $rendicion, $id_titular_proyecto,
							$id_titular_adm_proyecto, $rendicion_codigo, $observaciones,
							$comprobantes, $_SESSION["id_usuario"], $error);
				} else {
					//aca solo se puede modificar si presento un comprobante o si firmo la rendicion
					$result = $bd->modificar_tramite2($id_tramite, $comprobantes, $firma_realizada, $_SESSION["id_usuario"], $error);
				}
				if ($result and $enviar_email) {
					$result2 = $bd->enviar_email_tramite($id_tramite, $_SESSION["id_usuario"], $error2);
					if ($result2) {
						$_SESSION["message"] = "El mensaje se envió correctamente.";
					}
				}
			}
			break;		
	}
	$bd = NULL;
	if (!$result){
		$_SESSION["message"]='No se grabaron los cambios, contacte con el administrador del sistema. Detalle del error:'.$error;
	}
	if ($enviar_email and !$result2){
		$_SESSION["message"]='No se pudo enviar el email de aviso. Detalle del error:'.$error2;
	} 
	//echo $_SESSION["message"];
	header("Location: lista_tramites.php");
?>

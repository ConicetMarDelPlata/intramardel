<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}
	
	// echo "<pre>";
	// var_dump($_POST);exit;
	
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

	$numero_orden_pago = $_POST["numero_orden_pago"];	
	$anio_numero_orden_pago = $_POST["anio_numero_orden_pago"];			
	$confeccionador = 0;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$fecha 					= $_POST["fecha"];
		$proveedor 				= $_POST["proveedor"];
		$usa_banco 				= $_POST["usa_banco"];
		$factura 				= $_POST["factura"];
		$objeto 				= $_POST["objeto"];		
		$asignacion_rendicion 	= $_POST["asignacion_rendicion"];
		$id_moneda 				= $_POST["signo_moneda"];
		$importe 				= $_POST["importe"];
		$aclaraciones 			= $_POST["aclaraciones"];
		$firmante 				= $_POST["firmante"];
		$firmante2 				= $_POST["firmante2"];
		$cm 					= $_POST["cm"];
		$iva 					= ($_POST["id_iva"])??0;
		$alicuota 				= ($_POST["alicuota"])??0;
		$id_unidad_ejecutora 			= $_POST["emisor"]??0;//viene un id unidad ejecutora
		$cuenta 				= $_POST["cuenta"];
		$cuenta 				= $_POST["cuenta"];
		$aviso_pago_adicional			= $_POST["aviso_pago_adicional"];
		if ($aviso_pago_adicional == 0){
			$no_enviar_aviso_pago_adicional = 1;
			$id_titular_aviso_pago = "";
		} else if ($aviso_pago_adicional == 2) {
			$no_enviar_aviso_pago_adicional = 0;
			$id_titular_aviso_pago = $_POST["id_titular_aviso_pago"];
		}
		$forma_pago 				= $_POST["forma_pago"];
		$condicion_venta_ce			= $_POST["condicion_venta_ce"];
	}

	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	switch ($opcion){
		case 1: // NUEVO
			//if($row_usuario['op_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],4,'alta')){
				$fecha = convertir_fecha_sql($fecha);
				$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];	
				$HayQueRetenerle =($bd->isRetentionAgent($id_unidad_ejecutora) && $bd->availRetention($proveedor) && $importe >= 2000);
				if($HayQueRetenerle){
					$cert_ret = $bd->getNextCRID($id_unidad_ejecutora);
				}else{
					$cert_ret = 0;
				}
				//Nota Vanina: cambio, obtengo el numero de orden de pago antes de grabar y no al comienzo del alta
				//Aqui es necesaria una transaccion pero el motor en el estado actual no lo permite, ver de implementar luego
				$numero_orden_pago = $bd->get_next_numero_orden_pago($anio_numero_orden_pago);
				if($bd->agregar_orden_pago($numero_orden_pago, $fecha, $anio_numero_orden_pago, $confeccionador, $proveedor, $usa_banco, 
							   $factura, $objeto, $asignacion_rendicion, $id_moneda, $importe, $aclaraciones, $confecciono, 
							   $firmante, $cm, $iva, $alicuota, $id_unidad_ejecutora, $cert_ret, $cuenta, $firmante2,
							   $forma_pago, $no_enviar_aviso_pago_adicional, $id_titular_aviso_pago, $condicion_venta_ce)){
					if($HayQueRetenerle){
						if(!$bd->updateCRID($id_unidad_ejecutora)){
							echo "ERROR AL GRABAR Numero de Cert. De Retencion";
						}
					}
				}
				break;
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['op_baja'] == 1){				
			if($bd->checkPerm($_SESSION["id_usuario"],4,'baja')){
				$bd->borrar_orden_pago($numero_orden_pago, $anio_numero_orden_pago);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['op_modificacion'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],4,'modificacion')){
				$fecha = convertir_fecha_sql($fecha);		
				//En la modificacion no viene el id_unidad_ejecutora (emisor) porque esta disabled
				$orden_pago = $bd->consultar_orden_pago($numero_orden_pago,$anio_numero_orden_pago);
				$id_unidad_ejecutora = $orden_pago['id_unidad_ejecutora'];
				$HayQueRetenerle =($bd->isRetentionAgent($id_unidad_ejecutora) && $bd->availRetention($proveedor) && $importe >= 2000 && $bd->getCertRet($numero_orden_pago,$anio_numero_orden_pago) === 0);
				
				if($HayQueRetenerle){
					$cert_ret = $bd->getNextCRID($id_unidad_ejecutora);
				}else{
					$cert_ret = $bd->getCertRet($numero_orden_pago,$anio_numero_orden_pago);
				}
				$bd->modificar_orden_pago($numero_orden_pago, $fecha, $anio_numero_orden_pago, $confeccionador, 
							  $proveedor, $usa_banco, $factura, $objeto, $asignacion_rendicion, 
							  $id_moneda, $importe, $aclaraciones, $firmante, $cm, $iva, $alicuota, 
							  $id_unidad_ejecutora,$cert_ret, $cuenta, $firmante2, $forma_pago, 
							  $no_enviar_aviso_pago_adicional, $id_titular_aviso_pago, $condicion_venta_ce);
				
				if($HayQueRetenerle){
					if(!$bd->updateCRID($id_unidad_ejecutora)){
						echo "ERROR AL GRABAR Numero de Cert. De Retencion";
					}
				}
			}
			break;
	}
	$bd = NULL;
	header("Location: lista_ordenes_pago.php");
?>

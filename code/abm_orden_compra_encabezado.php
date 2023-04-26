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
//echo "<pre>";
//var_dump($_POST);exit;
	$numero_orden_compra = $_POST["numero_orden_compra"];	
	$anio_numero_orden_compra = $_POST["anio_numero_orden_compra2"];
	$fecha = $_POST["fecha2"];
	$contacto = $_POST["usuario2"];	
	$proveedor = $_POST["proveedor2"];	
	$id_unidad= $_POST["id_unidad"];
	$procedimiento_seleccion = $_POST["procedimiento_seleccion2"];
	$objeto = $_POST["objeto2"];
	$referencia = $_POST["referencia2"];
	$firmante = $_POST["firmante2"];
	$firma_digital = $_POST["firma_digital2"];	
	/*echo "op " . $opcion2 . '<br>';
	echo $numero_orden_compra . '<br>';
	echo $anio_numero_orden_compra . '<br>';
	echo $fecha . '<br>';
	echo $contacto . '<br>';
	echo $proveedor . '<br>';
	echo $procedimiento_seleccion . '<br>';
	echo $objeto . '<br>';
	echo $referencia . '<br>';
	echo $firmante . '<br>';*/
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];	
	switch ($opcion2){
		case 1: // NUEVA ORDEN DE COMPRA SOLO ENCABEZADO
			// Vanina: Obtengo el numero de la proxima orden de compra
			$numero_orden_compra = $bd->get_next_numero_orden_compra($anio_numero_orden_compra);		
			//if($row_usuario['oc_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],3,'alta')){
				$fecha = convertir_fecha_sql($fecha);
				$bd->agregar_orden_compra_encabezado($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $confecciono, $firmante, $firma_digital,$id_unidad);
			}
			//header("Location: form_orden_compra.php?opcion=4&numero_orden_compra=$numero_orden_compra");
			header("Location: lista_ordenes_compra.php");			
			break;
		case 3: // ACTAULIZAR ENCABEZADO ORDEN DE COMPRA
			//if($row_usuario['oc_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],3,'modificacion')){
				$fecha = convertir_fecha_sql($fecha);
				$bd->modificar_orden_compra_encabezado($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $confecciono, $firmante, $firma_digital,$id_unidad);
			}
			//header("Location: form_orden_compra.php?opcion=4&numero_orden_compra=$numero_orden_compra");		
			header("Location: lista_ordenes_compra.php");			
			break;
	}
	$bd = NULL;
?>

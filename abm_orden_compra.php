<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];
	//$opcion2 = $_POST["opcion2"];	
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
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$anio_numero_orden_compra = $_POST["anio_numero_orden_compra"];
		$fecha = $_POST["fecha"];
		$contacto = $_POST["usuario"];	
		$proveedor = $_POST["proveedor"];	
		$procedimiento_seleccion = $_POST["procedimiento"];
		$objeto = $_POST["objeto"];
		$referencia = $_POST["referencia"];
		$numero_item = $_POST["numero_item"];
		$descripcion_componente = $_POST["descripcion_componente"];
		$cantidad = $_POST["cantidad"];
		$unidad = $_POST["unidad"];
		$id_unidad= $_POST["id_unidad"];
		$precio_unitario = $_POST["precio_unitario"];
		$signo_moneda = $_POST["signo_moneda"];
		$subtotal = $_POST["subtotal"];
		$firmante = $_POST["firmante"];
		$firma_digital = $_POST["firma_digital"];
	}
	
	/*echo "op " . $opcion . '<br>';
	echo $numero_orden_compra . '<br>';
	echo $anio_numero_orden_compra . '<br>';
	echo $fecha . '<br>';
	echo $contacto . '<br>';
	echo $proveedor . '<br>';
	echo $procedimiento_seleccion . '<br>';
	echo $objeto . '<br>';
	echo $referencia . '<br>';
	echo $numero_item . '<br>';
	echo $descripcion_componente . '<br>';
	echo $cantidad . '<br>';	
	echo $unidad . '<br>';	
	echo $precio_unitario . '<br>';	
	echo $signo_moneda . '<br>';	
	echo $subtotal . '<br>';	
	echo $firmante . '<br>';	
	exit();*/
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);
	$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];	
	switch ($opcion){
		case 1: // NUEVA ORDEN DE COMPRA
			//echo "uno";
			if($bd->checkPerm($_SESSION["id_usuario"],3,'alta')){
				//echo "dos";
				$fecha = convertir_fecha_sql($fecha);
				$numero_orden_compra = $bd->agregar_orden_compra($anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital,$id_unidad);
			}
			//echo "tres";
			//exit;
			header("Location: form_orden_compra.php?opcion=4&numero_orden_compra=$numero_orden_compra&anio=$anio_numero_orden_compra");
			break;
		case 4: // NUEVO MOVIMIENTO DE UNA ORDEN DE COMPRA (NUEVA O EXISTENTE)
			//echo "cuatro";
			//if($row_usuario['oc_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],3,'alta')){
				//echo "cinco";
				$fecha = convertir_fecha_sql($fecha);
				$bd->agregar_orden_compra_item($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital,$id_unidad);
			}
			//echo "seis";
			//exit;
			header("Location: form_orden_compra.php?opcion=4&numero_orden_compra=$numero_orden_compra&anio=$anio_numero_orden_compra");
			break;
		case 2: // BORRAR UNA ORDEN DE COMPRA
			//if($row_usuario['oc_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],3,'baja')){
				$bd->borrar_orden_compra($numero_orden_compra);
			}
			header("Location: lista_ordenes_compra.php");
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['oc_modificacion'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],3,'modificacion')){
				$fecha = convertir_fecha_sql($fecha);
				$bd->modificar_orden_compra($numero_orden_compra, $anio_numero_orden_compra, $fecha, $contacto, $proveedor, $procedimiento_seleccion, $objeto, $referencia, $numero_item, $descripcion_componente, $cantidad, $unidad, $precio_unitario, $signo_moneda, $subtotal, $confecciono, $firmante, $firma_digital,$id_unidad);
			}
			header("Location: form_orden_compra.php?opcion=4&numero_orden_compra=$numero_orden_compra&anio=$anio_numero_orden_compra");
			break;
		case 6:
			header("Location: orden_compra_pdf.php?numero_orden_compra=$numero_orden_compra&fecha=$fecha&anio=$anio_numero_orden_compra");
			break;
	}
	$bd = NULL;
?>
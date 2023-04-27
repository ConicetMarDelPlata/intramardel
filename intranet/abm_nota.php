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

	if ($opcion != 1) $id_nota = $_POST["id_nota"];
	$anio_numero_nota = $_POST["anio_numero_nota"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$numero_nota = $_POST["numero_nota"];
		$fecha = $_POST["fecha"];
		$destinatario = $_POST["destinatario"];				
		$lugar_trabajo = $_POST["lugar_trabajo"];
		$texto = $_POST["texto"];
		$referencia = $_POST["referencia"];
		$firmante = $_POST["firmante"];	
		$firmante1 = $_POST["firmante1"];	
		$CC = $_POST["sCC"];	
		$firma_digital = $_POST["firma_digital"];		
	}
	
	$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['mn_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],6,'alta')){
				$fecha = convertir_fecha_sql($fecha);				
				$confecciono = $row_usuario['apellido'] .', ' . $row_usuario['nombre'];
				$bd->agregar_nota($anio_numero_nota, $fecha, $destinatario, $lugar_trabajo, $texto, $referencia, $firmante, $firmante1, $CC, $confecciono, $firma_digital);
				break;
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['mn_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],6,'baja')){
				$row = $bd->consultar_nota($id_nota, $anio_numero_nota);
				$bd->borrar_nota($id_nota, $anio_numero_nota);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['mn_modificacion'] == 1){			
			if($bd->checkPerm($_SESSION["id_usuario"],6,'modificacion')){
				$fecha = convertir_fecha_sql($fecha);		
				$bd->modificar_nota($numero_nota, $anio_numero_nota, $fecha, $destinatario, $lugar_trabajo, $texto, $referencia, $firmante, $firmante1, $CC, $firma_digital, $id_nota);
			}
			break;
	}
	$bd = NULL;

	header("Location: lista_notas.php");
?>

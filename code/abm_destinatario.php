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

	$id_destinatario = $_POST["id_destinatario"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$mesa = $_POST["mesa"];
		//echo $mesa;
		/*if ($mesa == "Salida"){
			interna_externa == "";
		}else
			$interna_externa = $_POST["interna_externa"];*/
		$descripcion = $_POST["descripcion"];
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['de_alta'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],17,'alta')){
				$bd->agregar_destinatario($mesa, $descripcion);
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['de_baja'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],17,'baja')){
				$bd->borrar_destinatario($id_destinatario);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['de_modificacion'] == 1){	
			if($bd->checkPerm($_SESSION["id_usuario"],17,'modificacion')){
				$bd->modificar_destinatario($id_destinatario, $mesa, $descripcion);
			}
			break;
	}
	$bd = NULL;

	header("Location: lista_destinatarios.php");
	
?>

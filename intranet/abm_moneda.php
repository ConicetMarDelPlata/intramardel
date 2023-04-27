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

	$id_moneda = $_POST["id_moneda"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$signo = $_POST["signo"];
		$descripcion = $_POST["descripcion"];	
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	$error = false;
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['mo_alta'] == 1){	
			if($bd->checkPerm($_SESSION["id_usuario"],14,'alta')){
				//Modif Vani: no se porque consultaba si existia un autoincremental	
				//$row = $bd->consultar_moneda($id_moneda);
				//if ($row["id_moneda"] == ""){
					$bd->agregar_moneda($signo, $descripcion);
					//break;
				//}else
				//{
				//	$error_moneda = 1;
				//	break;
				//}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['mo_baja'] == 1){				
			if($bd->checkPerm($_SESSION["id_usuario"],14,'baja')){
				$mensaje = $bd->check_uso_moneda($id_moneda);
				if ($mensaje == ""){
					$bd->borrar_moneda($id_moneda);
				}
				else {
					$mensaje = "No se puede eliminar la moneda debido a que se encuentra utilizada en ".$mensaje.".";
					$error = true;
				}
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['mo_modificacion'] == 1){	
			if($bd->checkPerm($_SESSION["id_usuario"],14,'modificacion')){
				$mensaje = $bd->check_uso_moneda($id_moneda);
				if ($mensaje == ""){	
					$bd->modificar_moneda($id_moneda, $signo, $descripcion);
				}
				else {
					$mensaje = "No se puede modificar la moneda debido a que se encuentra utilizada en ".$mensaje.".";
					$error = true;
				}				
			}
			break;
	}
	$bd = NULL;
	if ($error){
		$_SESSION["message"] = $mensaje;
	}
	header("Location: lista_monedas.php");
	
?>

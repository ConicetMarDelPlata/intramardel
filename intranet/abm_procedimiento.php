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

	$id_procedimiento = $_POST["id_procedimiento"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$descripcion = $_POST["descripcion"];
	}
	//$row_usuario = $bd->consultar_nombre_usuario($nombre_usuario);	
	switch ($opcion){
		case 1: //NUEVO
			//if($row_usuario['proc_alta'] == 1){
			if($bd->checkPerm($_SESSION["id_usuario"],18,'alta')){
				$row = $bd->consultar_procedimiento_seleccion($id_descripcion);
				if ($row["id_procedimiento"] == ""){
					$bd->agregar_procedimiento($descripcion);
					break;
				}else
				{
					$error_procedimiento = 1;
					break;
				}
			}
			break;
		case 2: // BORRAR 
			//if($row_usuario['proc_baja'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],18,'baja')){
				$bd->borrar_procedimiento($id_procedimiento);
			}
			break;	
		case 3: // ACTUALIZAR 
			//if($row_usuario['proc_modificacion'] == 1){		
			if($bd->checkPerm($_SESSION["id_usuario"],18,'modificacion')){
				$bd->modificar_procedimiento($id_procedimiento, $descripcion);
			}
			break;
	}
	$bd = NULL;
	if ($error_procedimiento == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Procedimiento elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: form_procedimiento.php?opcion=1");
	}
?>

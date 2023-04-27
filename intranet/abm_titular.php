<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if ( !$sesion->chequear_sesion() ){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];
	$sesion = NULL;

	$bd = new Bd;
	$bd->AbrirBd();

	$id_titular = $_POST["id_titular"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$apellido = $_POST["apellido"];
		$nombre = $_POST["nombre"];	
		$dni = $_POST["dni"];
		$email = $_POST["email"];
	}

	$error = 0;

	switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],31,'alta')){
				$bd->agregar_titular($apellido, $nombre, $dni, $email);
			}
			break;
		case 2: // BORRAR 
			if($bd->checkPerm($_SESSION["id_usuario"],31,'baja')){
				$bd->borrar_titular($id_titular);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],31,'modificacion')){
				$noExiste = $bd->check_dni_titular($dni, $id_titular);
				if ($noExiste){
					$bd->modificar_titular($id_titular, $apellido, $nombre, $dni, $email);				
				}else
				{
					$error = 1;
				}	
			}
			break;		
	}
	$bd = NULL;
	if ($error == 1){
		$_SESSION["message"]='No se grabaron los cambios. El dni de titular ya existe, por favor elija otro y carguelo nuevamente.';
	}
	header("Location: lista_titulares.php");
?>

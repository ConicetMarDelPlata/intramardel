<?php
	include "seguridad_bd.php";
	include_once("./includes/class.Equipos.php");

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
	$equipo = new Equipos($bd);

	$id_equipo = $_POST["id_equipo"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$nombre = $_POST["nombre"];
    }

	$error = 0;

    switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],36,'alta')){
				$noExiste = $equipo->check_nombre_equipo($nombre, '');
				if ($noExiste){
					$equipo->agregar_equipo($nombre);
				} else {
					$error = 1;
				}
			}
			break;
		case 2: // BORRAR 
			if($bd->checkPerm($_SESSION["id_usuario"],36,'baja')){
				$equipo->borrar_equipo($id_equipo);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],36,'modificacion')){
				$noExiste = $equipo->check_nombre_equipo($nombre, $id_equipo);
				if ($noExiste){
					$equipo->modificar_equipo($id_equipo, $nombre);				
				} else
				{
					$error = 1;
				}	
			}
			break;		
	}
	$bd = NULL;
	if ($error == 1){
		$_SESSION["message"]='No se grabaron los cambios. Ese equipo ya fue ingresado, por favor elija otro nombre y carguelo nuevamente.';
	} 

	header("Location: lista_equipo_salas.php");
?>

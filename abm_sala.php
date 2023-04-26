<?php
	include "seguridad_bd.php";
	include_once("./includes/class.Salas.php");

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

	$sala = new Salas($bd);

	$id_sala = $_POST["id_sala"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$nombre = $_POST["nombre"];
		$equipos = $_POST["equipos"] ?? '';
	}
	$error = 0;

    switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],35,'alta')){
				$noExiste = $sala->check_nombre_sala($nombre, '');
				if ($noExiste){
					$sala->agregar_sala($nombre);
				} else {
					$error = 1;
				}
			}
			break;
		case 2: // BORRAR 
			if($bd->checkPerm($_SESSION["id_usuario"],35,'baja')){
				$sala->borrar_sala($id_sala);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],35,'modificacion')){
				$noExiste = $sala->check_nombre_sala($nombre, $id_sala);
				if ($noExiste){
					$sala->modificar_sala($id_sala, $nombre, $equipos);			
				} else
				{
					$error = 1;
				}	
			}
			break;		
	}
	$bd = NULL;
	if ($error == 1){
		$_SESSION["message"]='No se grabaron los cambios. Esa sala ya fue ingresada, por favor elija otro nombre y carguelo nuevamente.';
	} 

	header("Location: lista_salas.php");
?>

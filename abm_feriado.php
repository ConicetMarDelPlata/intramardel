<?php
    include "seguridad_bd.php";
    include_once("./includes/class.Feriados.php");

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
    
    $feriados	= new Feriados($bd);

	$id_feriado = $_POST["id_feriado"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$fecha = $_POST["fecha"];
		$descripcion = $_POST["descripcion"];	
    }

	$error = 0;

	switch ($opcion){
		case 1: //NUEVO
			if($bd->checkPerm($_SESSION["id_usuario"],34,'alta')){
				$noExiste = $feriados->check_fecha_feriado($fecha, '');
				if ($noExiste){
					$feriados->agregar_feriado($fecha, $descripcion);
				} else {
					$error = 1;
				}
			}
			break;
		case 2: // BORRAR 
			if($bd->checkPerm($_SESSION["id_usuario"],34,'baja')){
				$feriados->borrar_feriado($id_feriado);
			}
			break;	
		case 3: // ACTUALIZAR 			
			if($bd->checkPerm($_SESSION["id_usuario"],34,'modificacion')){
				$noExiste = $feriados->check_fecha_feriado($fecha, $id_feriado);
				if ($noExiste){
					$feriados->modificar_feriado($id_feriado, $fecha, $descripcion);				
				} else
				{
					$error = 1;
				}	
			}
			break;		
	}
	$bd = NULL;
	if ($error == 1){
		$_SESSION["message"]='No se grabaron los cambios. La fecha ya fue ingresada, por favor elija otra fecha y carguela nuevamente.';
	} 

	header("Location: lista_feriados.php");
?>

<?php
	//Actualizado a PHP > 7 
	//Victoria Ganuza
	//Fecha: 04/11/2022

	include 'seguridad_bd.php'; 

	$nombre_usuario = $_POST['nombre_usuario'];
	$contrasenia = $_POST['contrasenia'];
	$nueva = $_POST['nueva'];

	$hash = password_hash($nueva, PASSWORD_ARGON2I);

	$bd = new Bd;
	$bd->AbrirBd();

	if($_POST['wp-submit'] == "Acceder"){
		if ( $bd->usuario_registrado($nombre_usuario, $contrasenia) ){
			if ($nueva) {
				if ($bd->update_user($nombre_usuario,$hash)){
					$sesion = new Sesion; //Objeto para iniciar una nueva session
					$sesion->nueva_sesion($nombre_usuario, $hash);
					$sesion = NULL; 
					header("Location: panel_control.php");
					$bd = NULL;
				} 
			} else
			 {
				header("Location: cambiar_pwd.php?m=2");
			 }
		}
		else
		{
			header("Location: cambiar_pwd.php?m=1");
		}
	}else{
			header("Location: index.php");
	} 
?>

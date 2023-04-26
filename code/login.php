<?php
	//Actualizado a PHP > 7 
	//Victoria Ganuza
	//Fecha: 20/09/2022

	include 'seguridad_bd.php'; 
	//include 'seg_bd.php'; 

	$nombre_usuario = $_POST['nombre_usuario'];
	$contrasenia = $_POST['contrasenia'];

	$bd = new Bd;
	$bd->AbrirBd();

	if($_POST['wp-submit'] == "Acceder"){
		if ( $bd->usuario_registrado($nombre_usuario, $contrasenia) ){
			if($nombre_usuario && $contrasenia){
				$sesion = new Sesion; //Objeto para iniciar una nueva session
				$sesion->nueva_sesion($nombre_usuario, password_hash($contrasenia, PASSWORD_ARGON2I));
				$sesion = NULL; 
				if ($bd->old_password($nombre_usuario)){
					header("Location: cambiar_contraseña.php");
				} else {
					header("Location: panel_control.php");
					$bd = NULL;
				}
			} else{
				header("Location: index.php");
			}
		}
		else
		{
			header("Location: index.php?m=1");
		}
	}else{
			//header("Location: registro.php");
			header("Location: index.php");
	}
?>

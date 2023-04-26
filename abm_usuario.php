<?php
	include "seguridad_bd.php";
	$sesion = new Sesion;	
	if (!$sesion->chequear_sesion()){	
		$sesion = NULL;
		header("Location: index.php");
		exit();
	}

	$opcion = $_POST["opcion"];
	$nombre_usuario_session = $_SESSION["usuario"];
	$contrasenia_session = $_SESSION["contrasenia"];

	$sesion = NULL;
	/*echo "Datos de session: " . '<br>';
	echo $nombre_usuario . '<br>';
	echo $contrasenia . '<br>';
	*/

	$bd = new Bd;
	$bd->AbrirBd();
	$error_usuario = 0;

	$id_usuario = $_POST["id_usuario"];
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$nombre_usuario = $_POST["nombre_usuario"];
		$contrasenia = $_POST["contrasenia"];
		$nombre = $_POST["nombre"];
		$apellido = $_POST["apellido"];
		$email = $_POST["email"];
		$titulo = $_POST["titulo"];
		
		$qPermisos = 'SELECT * FROM permiso ORDER BY id_permiso';
		$rPermisos = $bd->excecuteQuery($qPermisos);
		while ( $arrayPermisos = mysqli_fetch_array($rPermisos) ){
			//Si no esta chequeado el control de check no llega
			$alta = "alta".$arrayPermisos['id_permiso'];
			$opciones_alta[$arrayPermisos['id_permiso']] = (int)isset($_POST[$alta]);
			
			$baja = "baja".$arrayPermisos['id_permiso'];
			$opciones_baja[$arrayPermisos['id_permiso']] = (int)isset($_POST[$baja]);
			
			$modificacion = "modificacion".$arrayPermisos['id_permiso'];
			$opciones_modificacion[$arrayPermisos['id_permiso']] = (int)isset($_POST[$modificacion]);
			
			$consulta = "consulta".$arrayPermisos['id_permiso'];
			$opciones_consulta[$arrayPermisos['id_permiso']] = (int)isset($_POST[$consulta]);
			
			$especial = "especial".$arrayPermisos['id_permiso'];
			$opciones_especial[$arrayPermisos['id_permiso']] = (int)isset($_POST[$especial]);
		}// end while
			
	}
	switch ($opcion){
		case 1: //USUARIO NUEVO	
			if($bd->checkPerm($_SESSION["id_usuario"],19,'alta')){
				$noExiste = $bd->check_nombre_usuario($nombre_usuario);
				if ($noExiste){
					$bd->agregar_usuario2($nombre_usuario, $contrasenia, $nombre, $apellido, $email, $titulo, $opciones_alta, $opciones_baja, $opciones_modificacion, $opciones_consulta, $opciones_especial);
				}else
				{
					$error_usuario = 1;
				}
			}
			break;
		case 2: // BORRAR USUARIO	
			if($bd->checkPerm($_SESSION["id_usuario"],19,'baja')){
				$bd->borrar_usuario($id_usuario);
			}
			break;	
		case 3: // ACTUALIZAR USUARIO	
			if($bd->checkPerm($_SESSION["id_usuario"],19,'modificacion')){
				$noExiste = $bd->check_nombre_usuario($nombre_usuario, $id_usuario);
				if ($noExiste){
					$bd->modificar_usuario2($id_usuario, $nombre_usuario, $contrasenia, $nombre, $apellido, $email, $titulo, $opciones_alta, $opciones_baja, $opciones_modificacion, $opciones_consulta, $opciones_especial);				
				}else
				{
					$error_usuario = 1;
				}
			}
			break;
	}
	$bd = NULL;
	if ($error_usuario == 1){
		$_SESSION["message"]='No se grabaron los cambios. El nombre de usuario elegido ya existe o existio anteriormente, por favor elija otro y carguelo nuevamente.';
	}
	header("Location: lista_usuarios.php");
	
?>

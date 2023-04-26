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

	$bd = new Bd;
	$bd->AbrirBd();
	$error_proyecto_c_i = 0;
	$id = $_POST["id"]??null;
	if ($opcion != 2){ //SI NO ES BORRAR TOMAR TODOS LOS DATOS
		$descripcion = $_POST["descripcion"];
	}
	$archivo = $_FILES['archivo1']['tmp_name'];
	if ($archivo) {
		$nombre = $_FILES['archivo1']['name'];
	}

	switch ($opcion){
		case 1: //NUEVO
			$iLastId = $bd->agregar_sh_file((int)$_POST['UE'],$descripcion, $nombre);
			break;
		case 2: // BORRAR 
			if($bd->borrar_sh_file($id)){
				$arch = $bd->consultar_sh_file($id);
			}
			break;	
		case 3: // ACTUALIZAR 
			$iLastId = $bd->actualizar_sh_file((int)$id,(int)$_POST['UE'],$descripcion, $nombre);
			break;
		case 4: // ELIMINAR SOLO ARCHIVO 
			$bd->quitar_sh_file((int)$id,(int)$_POST['UE'], $nombre);
			break;
	}
	if($archivo){
		$ruta = $bd->get_path_sh_file((int)$_POST['UE']);
		move_uploaded_file($_FILES['archivo1']['tmp_name'], '../PDF/'.$ruta.$iLastId.$nombre);
	}
	
	$bd = NULL;
	if ($error_proyecto_c_i == 1){
		// HACER PAGINA DE ERROR DE USUARIO REGISTRADO
		//header("error_usuario.php");
		echo "El ID del Proyecto CI elegido ya existe, por favor elija otro y carguelo nuevamente.";
	}else{
		header("Location: lista_sh_files.php");
	}
?>
